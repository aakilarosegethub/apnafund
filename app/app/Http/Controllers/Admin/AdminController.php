<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Campaign;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function dashboard()
    {
        $pageTitle = 'Dashboard';
        $latestUsers = User::active()->latest()->limit(6)->get();
        $admin = Admin::first();
        $passwordAlert = false;

        if (Hash::check('admin', $admin->password) || $admin->username == 'admin') {
            $passwordAlert = true;
        }

        // User Info
        $widget['totalUsers'] = User::count();
        $widget['activeUsers'] = User::active()->count();
        $widget['emailUnconfirmedUsers'] = User::emailUnconfirmed()->count();
        $widget['mobileUnconfirmedUsers'] = User::mobileUnconfirmed()->count();

        // Campaign Info
        $widget['pendingCampaignCount'] = Campaign::pending()->count();
        $widget['runningCampaignCount'] = Campaign::running()->count();
        $widget['upcomingCampaignCount'] = Campaign::upcoming()->count();
        $widget['expiredCampaignCount'] = Campaign::expired()->count();

        // Deposit Info
        $widget['depositDone'] = Deposit::done()->sum('amount');
        $widget['depositPending'] = Deposit::pending()->count();
        $widget['depositCancelled'] = Deposit::cancelled()->count();
        $widget['depositCharge'] = Deposit::done()->sum('charge');

        // Withdraw Info
        $widget['withdrawDone'] = Withdrawal::done()->sum('amount');
        $widget['withdrawPending'] = Withdrawal::pending()->count();
        $widget['withdrawCancelled'] = Withdrawal::cancelled()->count();
        $widget['withdrawCharge'] = Withdrawal::done()->sum('charge');

        // Monthly Deposit & Withdraw Report Graph
        $report['months'] = collect([]);
        $report['deposit_month_amount'] = collect([]);
        $report['withdraw_month_amount'] = collect([]);

        $depositsMonth = Deposit::where('created_at', '>=', Carbon::now()->subYear())
            ->where('status', ManageStatus::PAYMENT_SUCCESS)
            ->selectRaw('SUM(CASE WHEN status = '.ManageStatus::PAYMENT_SUCCESS.' THEN amount END) as depositAmount')
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')
            ->get();

        $depositsMonth->map(function ($depositData) use ($report) {
            $report['months']->push($depositData->months);
            $report['deposit_month_amount']->push(getAmount($depositData->depositAmount));
        });

        $withdrawalMonth = Withdrawal::where('created_at', '>=', Carbon::now()->subYear())
            ->where('status', ManageStatus::PAYMENT_SUCCESS)
            ->selectRaw('SUM(CASE WHEN status = '.ManageStatus::PAYMENT_SUCCESS.' THEN amount END) as withdrawAmount')
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')
            ->get();

        $withdrawalMonth->map(function ($withdrawData) use ($report) {
            if (! in_array($withdrawData->months, $report['months']->toArray())) {
                $report['months']->push($withdrawData->months);
            }

            $report['withdraw_month_amount']->push(getAmount($withdrawData->withdrawAmount));
        });

        $months = $report['months'];

        for ($i = 0; $i < $months->count(); $i++) {
            $monthVal = Carbon::parse($months[$i]);

            if (isset($months[$i + 1])) {
                $monthValNext = Carbon::parse($months[$i + 1]);

                if ($monthValNext < $monthVal) {
                    $temp = $months[$i];
                    $months[$i] = Carbon::parse($months[$i + 1])->format('F-Y');
                    $months[$i + 1] = Carbon::parse($temp)->format('F-Y');
                } else {
                    $months[$i] = Carbon::parse($months[$i])->format('F-Y');
                }
            }
        }

        // Check if trending campaign has expired (admin notice)
        $expiredTrendingCampaign = null;
        $trendingCampaignContent = \App\Models\SiteData::where('data_key', 'home.trending_campaign')->first();
        if ($trendingCampaignContent?->data_info) {
            $dataInfo = is_array($trendingCampaignContent->data_info) ? $trendingCampaignContent->data_info : (array) $trendingCampaignContent->data_info;
            if (($dataInfo['show_trending'] ?? 0) == 1 && ! empty($dataInfo['trending_campaign_id'])) {
                $trendingCampaign = Campaign::find($dataInfo['trending_campaign_id']);
                if ($trendingCampaign && $trendingCampaign->isExpired()) {
                    $expiredTrendingCampaign = $trendingCampaign;
                }
            }
        }

        return view('admin.page.dashboard', compact('pageTitle', 'widget', 'latestUsers', 'depositsMonth', 'withdrawalMonth', 'months', 'passwordAlert', 'expiredTrendingCampaign'));
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $admin = auth('admin')->user();

        return view('admin.page.profile', compact('pageTitle', 'admin'));
    }

    public function profileUpdate()
    {
        $this->validate(request(), [
            'name' => 'required|max:40',
            'email' => 'required|email|max:40',
            'username' => 'required|max:40',
            'contact' => 'required|max:40',
            'address' => 'required|max:255',
            'image' => [File::types(['png', 'jpg', 'jpeg'])],
        ]);

        $admin = auth('admin')->user();

        if (request()->hasFile('image')) {
            try {
                $old = $admin->image;
                $admin->image = fileUploader(request('image'), getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                $toast[] = ['error', 'Image upload failed'];

                return back()->withToasts($toast);
            }
        }

        $admin->name = request('name');
        $admin->email = request('email');
        $admin->username = request('username');
        $admin->contact = request('contact');
        $admin->address = request('address');
        $admin->save();

        $toast[] = ['success', 'Profile update success'];

        return back()->withToasts($toast);
    }

    public function passwordChange()
    {
        $passwordValidation = Password::min(6);

        if (bs('strong_pass')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate(request(), [
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $passwordValidation],
        ]);

        $admin = auth('admin')->user();

        if (! Hash::check(request('current_password'), $admin->password)) {
            $toast[] = ['error', 'Current password mismatched !!'];

            return back()->withToasts($toast);
        }

        $admin->password = Hash::make(request('password'));
        $admin->save();

        $toast[] = ['success', 'Password change success'];

        return back()->withToasts($toast);
    }

    public function notificationAll()
    {
        $notifications = AdminNotification::with('user')->orderBy('is_read')->paginate(getPaginate());
        $pageTitle = 'Notifications';

        return view('admin.page.notification', compact('pageTitle', 'notifications'));
    }

    public function notificationRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->is_read = ManageStatus::YES;
        $notification->save();

        $url = $notification->click_url;

        if ($url == '#') {
            $url = url()->previous();
        }

        return redirect($url);
    }

    public function notificationReadAll()
    {
        AdminNotification::where('is_read', ManageStatus::NO)->update([
            'is_read' => ManageStatus::YES,
        ]);

        $toast[] = ['success', 'All notification marked as read success'];

        return back()->withToasts($toast);
    }

    public function notificationRemove($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->delete();

        $toast[] = ['success', 'Notification removal success'];

        return back()->withToasts($toast);
    }

    public function notificationRemoveAll()
    {
        AdminNotification::truncate();

        $toast[] = ['success', 'All notification remove success'];

        return back()->withToasts($toast);
    }

    public function transaction()
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::with('user')
            ->searchable(['trx', 'user:username'])
            ->filter(['remark'])
            ->dateFilter()
            ->latest()
            ->paginate(getPaginate());

        $transactions->map(function ($transaction) {
            if (is_null($transaction->user)) {
                $deposit = Deposit::where('trx', $transaction->trx)->select('full_name', 'email')->first();
                $transaction->sender_name = $deposit->full_name;
                $transaction->sender_email = $deposit->email;
            }
        });

        return view('admin.page.transaction', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function fileDownload()
    {
        $path = request('filePath');
        $file = getFilePath($path).'/'.request('fileName');

        return response()->download($file);
    }

    public function uploadFile()
    {
        if (request()->hasFile('upload')) {
            $file = request()->file('upload');

            // Validate file
            $this->validate(request(), [
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            // Generate unique filename
            $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

            // Create directory if not exists
            $uploadPath = public_path('assets/images/editor');
            if (! file_exists($uploadPath)) {
                @mkdir($uploadPath, 0777, true);
                @chmod($uploadPath, 0777);
            }

            // Move file to public directory
            $file->move($uploadPath, $fileName);
            @chmod($uploadPath.'/'.$fileName, 0777);

            // Return CKEditor response format
            return response()->json([
                'uploaded' => 1,
                'fileName' => $fileName,
                'url' => asset('assets/images/editor/'.$fileName),
            ]);
        }

        return response()->json([
            'uploaded' => 0,
            'error' => [
                'message' => 'No file uploaded or invalid file type.',
            ],
        ]);
    }

    // Handle external image URLs - download and upload to server
    public function uploadExternalImage()
    {
        try {
            $externalUrl = request()->input('external_url');

            if (! $externalUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'No external URL provided',
                ], 400);
            }

            // Validate URL
            if (! filter_var($externalUrl, FILTER_VALIDATE_URL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid URL provided',
                ], 400);
            }

            // Check if URL is from current domain - if yes, return as is
            $currentUrl = url('/');
            if (strpos($externalUrl, $currentUrl) === 0) {
                return response()->json([
                    'success' => true,
                    'url' => $externalUrl,
                    'message' => 'Image is already on this server',
                ]);
            }

            // Download image from external URL
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'follow_location' => true,
                    'max_redirects' => 5,
                ],
            ]);

            $imageData = @file_get_contents($externalUrl, false, $context);

            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to download image from external URL',
                ], 400);
            }

            // Get image info
            $imageInfo = @getimagesizefromstring($imageData);
            if ($imageInfo === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image file',
                ], 400);
            }

            // Determine file extension from MIME type
            $mimeType = $imageInfo['mime'];
            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];

            $extension = $extensions[$mimeType] ?? 'jpg';

            // Generate unique filename
            $filename = time().'_'.uniqid().'.'.$extension;

            // Create upload directory
            $uploadPath = public_path('assets/images/editor');
            if (! file_exists($uploadPath)) {
                @mkdir($uploadPath, 0777, true);
                @chmod($uploadPath, 0777);
            }

            // Save image
            $fullPath = $uploadPath.'/'.$filename;
            file_put_contents($fullPath, $imageData);
            @chmod($fullPath, 0777);

            // Return success response with image URL
            $imageUrl = asset('assets/images/editor/'.$filename);

            return response()->json([
                'success' => true,
                'url' => $imageUrl,
                'fileName' => $filename,
                'message' => 'Image uploaded successfully',
            ]);

        } catch (Exception $e) {
            \Log::error('External image upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => request()->input('external_url'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading external image: '.$e->getMessage(),
            ], 500);
        }
    }
}
