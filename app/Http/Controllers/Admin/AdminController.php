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
    function dashboard() {
        $pageTitle     = 'Dashboard';
        $latestUsers   = User::active()->latest()->limit(6)->get();
        $admin         = Admin::first();
        $passwordAlert = false;

        if (Hash::check('admin', $admin->password) || $admin->username == 'admin') $passwordAlert = true;

        // User Info
        $widget['totalUsers']             = User::count();
        $widget['activeUsers']            = User::active()->count();
        $widget['emailUnconfirmedUsers']  = User::emailUnconfirmed()->count();
        $widget['mobileUnconfirmedUsers'] = User::mobileUnconfirmed()->count();

        // Campaign Info
        $widget['pendingCampaignCount']  = Campaign::pending()->count();
        $widget['runningCampaignCount']  = Campaign::running()->count();
        $widget['upcomingCampaignCount'] = Campaign::upcoming()->count();
        $widget['expiredCampaignCount']  = Campaign::expired()->count();

        // Deposit Info
        $widget['depositDone']      = Deposit::done()->sum('amount');
        $widget['depositPending']   = Deposit::pending()->count();
        $widget['depositCancelled'] = Deposit::cancelled()->count();
        $widget['depositCharge']    = Deposit::done()->sum('charge');

        // Withdraw Info
        $widget['withdrawDone']      = Withdrawal::done()->sum('amount');
        $widget['withdrawPending']   = Withdrawal::pending()->count();
        $widget['withdrawCancelled'] = Withdrawal::cancelled()->count();
        $widget['withdrawCharge']    = Withdrawal::done()->sum('charge');

        // Monthly Deposit & Withdraw Report Graph
        $report['months']                = collect([]);
        $report['deposit_month_amount']  = collect([]);
        $report['withdraw_month_amount'] = collect([]);

        $depositsMonth = Deposit::where('created_at', '>=', Carbon::now()->subYear())
            ->where('status', ManageStatus::PAYMENT_SUCCESS)
            ->selectRaw("SUM(CASE WHEN status = " . ManageStatus::PAYMENT_SUCCESS . " THEN amount END) as depositAmount")
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
            ->selectRaw("SUM(CASE WHEN status = " . ManageStatus::PAYMENT_SUCCESS . " THEN amount END) as withdrawAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')
            ->get();

        $withdrawalMonth->map(function ($withdrawData) use ($report) {
            if (!in_array($withdrawData->months, $report['months']->toArray())) {
                $report['months']->push($withdrawData->months);
            }

            $report['withdraw_month_amount']->push(getAmount($withdrawData->withdrawAmount));
        });

        $months = $report['months'];

        for ($i = 0; $i < $months->count(); ++$i) {
            $monthVal = Carbon::parse($months[$i]);

            if (isset($months[$i + 1])) {
                $monthValNext = Carbon::parse($months[$i + 1]);

                if ($monthValNext < $monthVal) {
                    $temp           = $months[$i];
                    $months[$i]     = Carbon::parse($months[$i + 1])->format('F-Y');
                    $months[$i + 1] = Carbon::parse($temp)->format('F-Y');
                } else {
                    $months[$i] = Carbon::parse($months[$i])->format('F-Y');
                }
            }
        }

        return view('admin.page.dashboard', compact('pageTitle', 'widget', 'latestUsers', 'depositsMonth', 'withdrawalMonth', 'months', 'passwordAlert'));
    }

    function profile() {
        $pageTitle = 'Profile';
        $admin     = auth('admin')->user();
        return view('admin.page.profile', compact('pageTitle', 'admin'));
    }

    function profileUpdate() {
        $this->validate(request(), [
            'name'     => 'required|max:40',
            'email'    => 'required|email|max:40',
            'username' => 'required|max:40',
            'contact'  => 'required|max:40',
            'address'  => 'required|max:255',
            'image'    => [File::types(['png', 'jpg', 'jpeg'])],
        ]);

        $admin = auth('admin')->user();

        if (request()->hasFile('image')) {
            try {
                $old          = $admin->image;
                $admin->image = fileUploader(request('image'), getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                $toast[] = ['error', 'Image upload failed'];
                return back()->withToasts($toast);
            }
        }

        $admin->name     = request('name');
        $admin->email    = request('email');
        $admin->username = request('username');
        $admin->contact  = request('contact');
        $admin->address  = request('address');
        $admin->save();

        $toast[] = ['success', 'Profile update success'];
        return back()->withToasts($toast);
    }

    function passwordChange() {
        $passwordValidation = Password::min(6);

        if (bs('strong_pass')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate(request(), [
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', $passwordValidation],
        ]);

        $admin = auth('admin')->user();

        if (!Hash::check(request('current_password'), $admin->password)) {
            $toast[] = ['error', 'Current password mismatched !!'];
            return back()->withToasts($toast);
        }

        $admin->password = Hash::make(request('password'));
        $admin->save();

        $toast[] = ['success', 'Password change success'];
        return back()->withToasts($toast);
    }

    function notificationAll() {
        $notifications = AdminNotification::with('user')->orderBy('is_read')->paginate(getPaginate());
        $pageTitle     = 'Notifications';

        return view('admin.page.notification',compact('pageTitle','notifications'));
    }

    function notificationRead($id) {
        $notification = AdminNotification::findOrFail($id);
        $notification->is_read = ManageStatus::YES;
        $notification->save();

        $url = $notification->click_url;

        if ($url == '#') {
            $url = url()->previous();
        }

        return redirect($url);
    }

    function notificationReadAll() {
        AdminNotification::where('is_read', ManageStatus::NO)->update([
            'is_read'=>ManageStatus::YES
        ]);

        $toast[] = ['success', 'All notification marked as read success'];
        return back()->withToasts($toast);
    }

    function notificationRemove($id) {
        $notification = AdminNotification::findOrFail($id);
        $notification->delete();

        $toast[] = ['success', 'Notification removal success'];
        return back()->withToasts($toast);
    }

    function notificationRemoveAll(){
        AdminNotification::truncate();

        $toast[] = ['success', 'All notification remove success'];
        return back()->withToasts($toast);
    }

    function transaction() {
        $pageTitle    = 'Transactions';
        $remarks      = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::with('user')
            ->searchable(['trx', 'user:username'])
            ->filter(['remark'])
            ->dateFilter()
            ->latest()
            ->paginate(getPaginate());

        $transactions->map(function ($transaction) {
            if (is_null($transaction->user)) {
                $deposit                   = Deposit::where('trx', $transaction->trx)->select('full_name', 'email')->first();
                $transaction->sender_name  = $deposit->full_name;
                $transaction->sender_email = $deposit->email;
            }
        });

        return view('admin.page.transaction', compact('pageTitle', 'transactions', 'remarks'));
    }

    function fileDownload() {
        $path = request('filePath');
        $file = fileManager()->$path()->path.'/'.request('fileName');

        return response()->download($file);
    }
}
