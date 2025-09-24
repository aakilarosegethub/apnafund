<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function index() {
        $pageTitle = 'All Users';
        $users     = $this->userData();

        return view('admin.user.index', compact('pageTitle', 'users'));
    }

    function active() {
        $pageTitle = 'Active Users';
        $users     = $this->userData('active');

        return view('admin.user.index', compact('pageTitle', 'users'));
    }

    function banned() {
        $pageTitle = 'Banned Users';
        $users     = $this->userData('banned');

        return view('admin.user.index', compact('pageTitle', 'users'));
    }

    function kycPending() {
        $pageTitle = 'KYC Pending Users';
        $users     = $this->userData('kycPending');

        return view('admin.user.index', compact('pageTitle', 'users'));
    }

    function kycUnConfirmed() {
        $pageTitle = 'KYC Unconfirmed Users';
        $users     = $this->userData('kycUnconfirmed');

        return view('admin.user.index', compact('pageTitle', 'users'));
    }

    function emailUnConfirmed() {
        $pageTitle = 'Email Unconfirmed Users';
        $users     = $this->userData('emailUnconfirmed');

        return view('admin.user.index', compact('pageTitle', 'users'));
    }

    function mobileUnConfirmed() {
        $pageTitle = 'Mobile Unconfirmed Users';
        $users     = $this->userData('mobileUnconfirmed');

        return view('admin.user.index', compact('pageTitle', 'users'));
    }

    function details($id) {
        $user = User::withCount([
            'campaigns as pending_campaigns'  => fn ($query) => $query->pending(),
            'campaigns as approved_campaigns' => fn ($query) => $query->approve(),
            'campaigns as rejected_campaigns' => fn ($query) => $query->reject(),
        ])->findOrFail($id);

        $pageTitle              = 'Details - ' . $user->username;
        $campaigns              = $user->campaigns->pluck('id');
        $totalReceivedDonation  = Deposit::whereIn('campaign_id', $campaigns)->done()->sum('amount');
        $totalWithdrawal        = $user->withdrawals()->done()->sum('amount');
        $totalGivenDonation     = $user->deposits()->done()->sum('amount');
        $totalTransactions      = $user->transactions->count();
        $totalPendingCampaigns  = $user->pending_campaigns;
        $totalApprovedCampaigns = $user->approved_campaigns;
        $totalRejectedCampaigns = $user->rejected_campaigns;
        $countries              = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('admin.user.details', compact('pageTitle', 'user', 'totalReceivedDonation', 'totalWithdrawal', 'totalGivenDonation', 'totalTransactions', 'totalPendingCampaigns', 'totalApprovedCampaigns', 'totalRejectedCampaigns', 'countries'));
    }

    function update($id) {
        $user         = User::findOrFail($id);
        $countryData  = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryArray = (array)$countryData;
        $countries    = implode(',', array_keys($countryArray));
        $countryCode  = request('country');
        $country      = $countryData->$countryCode->country;
        $dialCode     = $countryData->$countryCode->dial_code;

        $this->validate(request(), [
            'firstname' => 'required|string|max:40',
            'lastname'  => 'required|string|max:40',
            'email'     => 'required|email|string|max:40|unique:users,email,' . $user->id,
            'mobile'    => 'required|string|max:40|unique:users,mobile,' . $user->id,
            'country'   => 'required|in:' . $countries,
        ]);

        $user->mobile       = $dialCode . request('mobile');
        $user->country_name = $country;
        $user->country_code = $countryCode;
        $user->firstname    = request('firstname');
        $user->lastname     = request('lastname');
        $user->email        = request('email');
        $user->ec           = request('ec') ? ManageStatus::VERIFIED : ManageStatus::UNVERIFIED;
        $user->sc           = request('sc') ? ManageStatus::VERIFIED : ManageStatus::UNVERIFIED;
        $user->ts           = request('ts') ? ManageStatus::ACTIVE   : ManageStatus::INACTIVE;
        $user->address      = [
            'city'    => request('city'),
            'state'   => request('state'),
            'zip'     => request('zip'),
            'country' => @$country,
        ];

        // Update business fields if provided
        if (request()->has('business_type')) {
            $user->business_type = request('business_type');
        }
        if (request()->has('business_name')) {
            $user->business_name = request('business_name');
        }
        if (request()->has('business_description')) {
            $user->business_description = request('business_description');
        }
        if (request()->has('industry')) {
            $user->industry = request('industry');
        }
        if (request()->has('funding_amount')) {
            $user->funding_amount = request('funding_amount');
        }
        if (request()->has('fund_usage')) {
            $user->fund_usage = request('fund_usage');
        }
        if (request()->has('campaign_duration')) {
            $user->campaign_duration = request('campaign_duration');
        }

        if (!request('kc')) {
            $user->kc = ManageStatus::UNVERIFIED;

            if ($user->kyc_data) {
                foreach ($user->kyc_data as $kycData) {
                    if ($kycData->type == 'file') fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
                }
            }

            $user->kyc_data = null;
        } else {
            $user->kc = ManageStatus::VERIFIED;
        }

        $user->save();

        $toast[] = ['success', 'User details updated successfully'];

        return back()->withToasts($toast);
    }

    function login($id) {
        Auth::loginUsingId($id);

        return to_route('user.home');
    }

    function balanceUpdate($id) {
        $this->validate(request(), [
            'amount' => 'required|numeric|gt:0',
            'act'    => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $user   = User::findOrFail($id);
        $amount = request('amount');
        $trx    = getTrx();

        $transaction = new Transaction();

        if (request('act') == 'add') {
            $user->balance        += $amount;
            $transaction->trx_type = '+';
            $transaction->remark   = 'balance_add';
            $notifyTemplate        = 'BAL_ADD';

            $toast[] = ['success', 'The amount of ' . bs('cur_sym') . $amount . ' has been added successfully'];
        } else {
            if ($amount > $user->balance) {
                $toast[] = ['error', $user->username . ' doesn\'t have sufficient balance'];

                return back()->withToasts($toast);
            }

            $user->balance        -= $amount;
            $transaction->trx_type = '-';
            $transaction->remark   = 'balance_subtract';
            $notifyTemplate        = 'BAL_SUB';

            $toast[] = ['success', 'The amount of ' . bs('cur_sym') . $amount . ' has been subtracted successfully'];
        }

        $user->save();

        $transaction->user_id      = $user->id;
        $transaction->amount       = $amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = 0;
        $transaction->trx          =  $trx;
        $transaction->details      = request('remark');
        $transaction->save();

        notify($user, $notifyTemplate, [
            'trx'          => $trx,
            'amount'       => showAmount($amount),
            'remark'       => request('remark'),
            'post_balance' => showAmount($user->balance),
        ]);

        return back()->withToasts($toast);
    }

    function status($id) {
        $user = User::findOrFail($id);

        if ($user->status == ManageStatus::ACTIVE) {
            $this->validate(request(), [
                'ban_reason' => 'required|string|max:255',
            ]);

            $user->status     = ManageStatus::INACTIVE;
            $user->ban_reason = request('ban_reason');
            $toast[]          = ['success', 'User banned successfully'];
        } else {
            $user->status     = ManageStatus::ACTIVE;
            $user->ban_reason = null;
            $toast[]          = ['success', 'User unbanned successfully'];
        }

        $user->save();

        return back()->withToasts($toast);
    }

    function kycApprove($id) {
        $user     = User::findOrFail($id);
        $user->kc = ManageStatus::VERIFIED;
        $user->save();

        notify($user, 'KYC_APPROVE', []);

        $toast[] = ['success', 'KYC approval success'];

        return back()->withToasts($toast);
    }

    function kycCancel($id) {
        $user = User::findOrFail($id);

        foreach ($user->kyc_data as $kycData) {
            if ($kycData->type == 'file') fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
        }

        $user->kc       = ManageStatus::UNVERIFIED;
        $user->kyc_data = null;
        $user->save();

        notify($user, 'KYC_REJECT', []);

        $toast[] = ['success', 'KYC cancellation success'];

        return back()->withToasts($toast);
    }

    function sendEmail($id) {
        $user = User::findOrFail($id);
        $pageTitle = 'Send Email to ' . $user->username;
        
        return view('admin.user.send-email', compact('pageTitle', 'user'));
    }

    function sendEmailToUser($id) {
        $this->validate(request(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $user = User::findOrFail($id);
        
        // Send email using the notify function
        notify($user, 'DEFAULT', [
            'subject' => request('subject'),
            'message' => request('message'),
        ], ['email']);

        $toast[] = ['success', 'Email sent to ' . $user->email . ' successfully'];
        return back()->withToasts($toast);
    }

    function sendBulkEmail() {
        $pageTitle = 'Send Bulk Email to Users';
        $users = User::where('status', ManageStatus::ACTIVE)->get(['id', 'username', 'email', 'firstname', 'lastname']);
        
        return view('admin.user.send-bulk-email', compact('pageTitle', 'users'));
    }

    function sendBulkEmailToUsers() {
        $this->validate(request(), [
            'user_ids' => 'required|array|min:1',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $userIds = request('user_ids');
        $users = User::whereIn('id', $userIds)->get();
        
        $sentCount = 0;
        foreach ($users as $user) {
            try {
                notify($user, 'DEFAULT', [
                    'subject' => request('subject'),
                    'message' => request('message'),
                ], ['email']);
                $sentCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to send email to user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        $toast[] = ['success', 'Email sent to ' . $sentCount . ' users successfully'];
        return back()->withToasts($toast);
    }

    function deleteAllUsers() {
        $pageTitle = 'Delete All Users';
        $userCount = User::count();
        
        return view('admin.user.delete-all-users', compact('pageTitle', 'userCount'));
    }

    function confirmDeleteAllUsers() {
        try {
            // Get count before deletion for logging
            $userCount = User::count();
            $admin = auth()->guard('admin')->user();
            
            // Delete all users and related data
            User::query()->delete();
            
            // Log the action
            \Log::info('All users deleted by admin: ' . $admin->username . ' at ' . now());
            
            $toast[] = ['success', 'All ' . $userCount . ' users have been deleted successfully'];
            return redirect()->route('admin.user.index')->withToasts($toast);
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete all users: ' . $e->getMessage());
            $toast[] = ['error', 'Failed to delete users: ' . $e->getMessage()];
            return back()->withToasts($toast);
        }
    }

    function deleteSelectedUsers() {
        $this->validate(request(), [
            'user_ids' => 'required|array|min:1',
            'confirmation_text' => 'required|in:DELETE SELECTED USERS',
        ]);

        try {
            $userIds = request('user_ids');
            $userCount = User::whereIn('id', $userIds)->count();
            $admin = auth()->guard('admin')->user();
            
            // Delete selected users
            User::whereIn('id', $userIds)->delete();
            
            // Log the action
            \Log::info('Selected users deleted by admin: ' . $admin->username . ' at ' . now() . '. Deleted count: ' . $userCount);
            
            $toast[] = ['success', $userCount . ' users have been deleted successfully'];
            return redirect()->route('admin.user.index')->withToasts($toast);
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete selected users: ' . $e->getMessage());
            $toast[] = ['error', 'Failed to delete users: ' . $e->getMessage()];
            return back()->withToasts($toast);
        }
    }

    function testWelcomeEmail($id) {
        $user = User::findOrFail($id);
        dd($user);
        
        try {
            // Send welcome email using the same method as registration
            $user->sendEmailVerificationNotification();
            
            $toast[] = ['success', 'Welcome email sent successfully to ' . $user->email];
            return back()->withToasts($toast);
            
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email to user ' . $user->id . ': ' . $e->getMessage());
            $toast[] = ['error', 'Failed to send welcome email: ' . $e->getMessage()];
            return back()->withToasts($toast);
        }
    }

    function testEmailToLastUser() {
        $lastUser = User::latest()->first();
        
        if (!$lastUser) {
            $toast[] = ['error', 'No users found in database'];
            return back()->withToasts($toast);
        }
        
        try {
            // Send welcome email to the last user
            $lastUser->sendEmailVerificationNotification();
            
            $toast[] = ['success', 'Welcome email sent successfully to last user: ' . $lastUser->email . ' (' . $lastUser->firstname . ' ' . $lastUser->lastname . ')'];
            return back()->withToasts($toast);
            
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email to last user ' . $lastUser->id . ': ' . $e->getMessage());
            $toast[] = ['error', 'Failed to send welcome email: ' . $e->getMessage()];
            return back()->withToasts($toast);
        }
    }

    function sendWelcomeToRecentUsers() {
        $pageTitle = 'Send Welcome Emails to Recent Users';
        
        // Get users created in the last 7 days
        $recentUsers = User::where('created_at', '>=', now()->subDays(7))
            ->where('status', ManageStatus::ACTIVE)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'username', 'email', 'firstname', 'lastname', 'created_at']);
        
        $totalUsers = $recentUsers->count();
        
        return view('admin.user.send-welcome-recent', compact('pageTitle', 'recentUsers', 'totalUsers'));
    }

    function sendWelcomeToRecentUsersPost() {
        $this->validate(request(), [
            'user_ids' => 'required|array|min:1',
        ]);

        $userIds = request('user_ids');
        $users = User::whereIn('id', $userIds)->get();
        
        $sentCount = 0;
        $failedCount = 0;
        
        foreach ($users as $user) {
            try {
                // Send welcome email using the same method as registration
                $user->sendEmailVerificationNotification();
                $sentCount++;
                
                \Log::info('Welcome email sent successfully to: ' . $user->email);
                
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error('Failed to send welcome email to user ' . $user->id . ' (' . $user->email . '): ' . $e->getMessage());
            }
        }

        $toast = [];
        if ($sentCount > 0) {
            $toast[] = ['success', 'Welcome emails sent successfully to ' . $sentCount . ' users'];
        }
        if ($failedCount > 0) {
            $toast[] = ['error', 'Failed to send welcome emails to ' . $failedCount . ' users'];
        }
        
        return back()->withToasts($toast);
    }

    function welcomeTemplateEditor() {
        $pageTitle = 'Welcome Email Template Editor';
        return view('admin.notification.welcome-template-editor', compact('pageTitle'));
    }

    function welcomeTemplateUpdate() {
        $this->validate(request(), [
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // For now, we'll just show a success message
        // In a real implementation, you'd save this to a database or config file
        $toast[] = ['success', 'Welcome email template updated successfully!'];
        $toast[] = ['info', 'Note: This is a demo. In production, you would save the template to database or config file.'];
        
        return back()->withToasts($toast);
    }

    protected function userData($scope = null) {
        if ($scope) $users = User::$scope();
        else $users = User::query();

        return $users->searchable(['username', 'email'])->dateFilter()->latest()->paginate(getPaginate());
    }
}
