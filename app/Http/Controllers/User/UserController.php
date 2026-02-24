<?php

namespace App\Http\Controllers\User;

use Exception;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\Deposit;
use App\Lib\FormProcessor;
use App\Models\Transaction;
use App\Constants\ManageStatus;
use App\Lib\GoogleAuthenticator;
use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    function home() {
        $pageTitle  = 'Dashboard';
        $kycContent = getSiteData('kyc.content', true);
        $user       = auth()->user();

        $user->loadCount(['campaigns', 
            'campaigns as pending_campaigns'  => fn ($query) => $query->pending(), 
            'campaigns as approved_campaigns' => fn ($query) => $query->approve(), 
            'campaigns as rejected_campaigns' => fn ($query) => $query->reject()
        ]);

        $widgetData['campaignCount']         = $user->campaigns_count;
        $widgetData['pendingCampaignCount']  = $user->pending_campaigns;
        $widgetData['approvedCampaignCount'] = $user->approved_campaigns;
        $widgetData['rejectedCampaignCount'] = $user->rejected_campaigns;
        $campaigns                           = $user->campaigns()->pluck('id');
        $widgetData['receivedDonation']      = Deposit::whereIn('campaign_id', $campaigns)->done()->sum('amount');
        $widgetData['sendDonation']          = Deposit::where('user_id', $user->id)->done()->sum('amount');
        $widgetData['withdrawalAmount']      = $user->withdrawals()->done()->sum('amount');

        // Monthly Deposit & Withdraw Report Graph
        $report['donationAmount'] = collect([]);
        $report['withdrawAmount'] = collect([]);

        $monthWiseDonation = Deposit::where('receiver_id', $user->id)
                            ->where('status', ManageStatus::PAYMENT_SUCCESS)
                            ->whereYear('created_at', now()->format('Y'))
                            ->selectRaw('date_format(created_at, "%M") as month')
                            ->selectRaw('sum(amount) as donation_amount')
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get();

        $monthWiseWithdraw = Withdrawal::where('user_id', $user->id)
                            ->where('status', ManageStatus::PAYMENT_SUCCESS)
                            ->whereYear('created_at', now()->format('Y'))
                            ->selectRaw('date_format(created_at, "%M") as month')
                            ->selectRaw('sum(amount) as withdraw_amount')
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get();

        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create()->month($i)->format('F');
            $donation  = $monthWiseDonation->firstWhere('month', $monthName);

            if ($donation) $report['donationAmount']->push(intval($donation->donation_amount));
            else $report['donationAmount']->push(0);

            $withdraw = $monthWiseWithdraw->firstWhere('month', $monthName);

            if ($withdraw) $report['withdrawAmount']->push(intval($withdraw->withdraw_amount));
            else $report['withdrawAmount']->push(0);
        }

        $donations   = $report['donationAmount']->toArray();
        $withdrawals = $report['withdrawAmount']->toArray();

        $showContactWarning = ($user->campaigns_count > 0) && !$user->mobile && !$user->whatsapp;

        return view($this->activeTheme . 'user.page.dashboard', compact('pageTitle', 'kycContent', 'user', 'widgetData', 'donations', 'withdrawals', 'showContactWarning'));
    }

    function kycForm() {
        if (auth()->user()->kc == ManageStatus::PENDING) {
            $toast[] = ['warning', 'Your identity verification is being processed'];

            return back()->withToasts($toast);
        }

        if (auth()->user()->kc == ManageStatus::VERIFIED) {
            $toast[] = ['success', 'Your identity verification is being succeed'];

            return back()->withToasts($toast);
        }

        $pageTitle = 'Identification Form';
        $form      = Form::where('act', 'kyc')->first();

        return view($this->activeTheme . 'user.kyc.form', compact('pageTitle', 'form'));
    }

    function kycSubmit() {
        $form           = Form::where('act', 'kyc')->first();
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);

        request()->validate($validationRule);

        $userData       = $formProcessor->processFormData(request(), $formData);
        $user           = auth()->user();
        $user->kyc_data = $userData;
        $user->kc       = ManageStatus::PENDING;
        $user->save();

        $toast[] = ['success', 'Your identity verification information has been received'];

        return to_route('user.home')->withToasts($toast);
    }

    function kycData() {
        $pageTitle = 'Identification Information';
        $user      = auth()->user();

        return view($this->activeTheme . 'user.kyc.info', compact('pageTitle', 'user'));
    }

    function profile() {
        $pageTitle = 'Profile Settings';
        $user      = auth()->user();

        return view($this->activeTheme . 'user.page.profile', compact('pageTitle', 'user'));
    }

    function profileUpdate() {
        $this->validate(request(), [
            'firstname' => 'required|string',
            'lastname'  => 'required|string',
            'mobile'    => 'nullable|string|max:30',
            'whatsapp'  => 'nullable|string|max:30',
            'image'     => ['nullable', File::types(['png', 'jpg', 'jpeg'])],
        ], [
            'firstname.required' => 'First name field is required',
            'lastname.required'  => 'Last name field is required'
        ]);

        $user = auth()->user();

        if (request()->hasFile('image')) {
            try {
                $user->image = fileUploader(request('image'), getFilePath('userProfile'), getFileSize('userProfile'), $user->image);
            } catch (Exception) {
                $toast[] = ['error', 'Image uploading process has failed'];

                return back()->withToasts($toast);
            }
        }

        $user->firstname = request('firstname');
        $user->lastname  = request('lastname');
        $user->mobile    = request('mobile');
        $user->whatsapp  = request('whatsapp');

        $user->address = [
            'state'   => request('state'),
            'zip'     => request('zip'),
            'city'    => request('city'),
            'address' => request('address'),
        ];

        $user->save();

        $toast[] = ['success', 'Your profile has updated'];

        return back()->withToasts($toast);
    }

    function password() {
        $pageTitle = 'Change Password';
        $user      = auth()->user();

        return view($this->activeTheme . 'user.page.password', compact('pageTitle', 'user'));
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

        $user = auth()->user();

        if (!Hash::check(request('current_password'), $user->password)) {
            $toast[] = ['error', 'Current password mismatched!'];

            return back()->withToasts($toast);
        }

        $user->password = Hash::make(request('password'));
        $user->save();

        $toast[] = ['success', 'Your password has changed'];

        return back()->withToasts($toast);
    }

    function show2faForm() {
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . bs('site_name'), $secret);
        $pageTitle = '2FA Settings';

        return view($this->activeTheme . 'user.page.twoFactor', compact('pageTitle', 'secret', 'qrCodeUrl', 'user'));
    }

    function enable2fa() {
        $user = auth()->user();

        $this->validate(request(), [
            'key'    => 'required',
            'code'   => 'required|array|min:6',
            'code.*' => 'required|integer',
        ]);

        $verCode  = (int)(implode("", request('code')));
        $response = verifyG2fa($user, $verCode, request('key'));

        if ($response) {
            $user->tsc = request('key');
            $user->ts  = ManageStatus::YES;
            $user->save();

            $toast[] = ['success', 'Two factor authenticator successfully activated'];

            return back()->withToasts($toast);
        } else {
            $toast[] = ['error', 'Wrong verification code'];

            return back()->withToasts($toast);
        }
    }

    function disable2fa() {
        $this->validate(request(), [
            'code'   => 'required|array|min:6',
            'code.*' => 'required|integer',
        ]);

        $verCode  = (int)(implode("", request('code')));
        $user     = auth()->user();
        $response = verifyG2fa($user, $verCode);

        if ($response) {
            $user->tsc = null;
            $user->ts  = ManageStatus::NO;
            $user->save();

            $toast[] = ['success', 'Two factor authenticator successfully deactivated'];
        } else {
            $toast[] = ['error', 'Wrong verification code'];
        }

        return back()->withToasts($toast);
    }

    function donationHistory() {
        $pageTitle = 'My Donations';
        $deposits  = auth()->user()->deposits()
                    ->with(['gateway', 'campaign', 'reward'])
                    ->index()
                    ->latest();
        
        // Filter by reward if requested
        if (request('filter') == 'reward') {
            $deposits = $deposits->whereNotNull('reward_id');
        }
        
        $deposits = $deposits->searchable(['trx'])
                    ->paginate(getPaginate());

        $emptyMessage = 'No donations found';
        return view($this->activeTheme . 'user.donation.sendLog', compact('pageTitle', 'deposits', 'emptyMessage'));
    }

    function donationReceived() {
        $pageTitle = 'Received Donations';
        $campaigns = auth()->user()->campaigns()->pluck('id');
        $donations = Deposit::whereIn('campaign_id', $campaigns)
                            ->with(['user', 'gateway', 'campaign', 'reward'])
                            ->done()
                            ->latest()
                            ->searchable(['trx'])
                            ->paginate(getPaginate());

        return view($this->activeTheme . 'user.donation.receivedLog', compact('pageTitle', 'donations'));
    }

    function transactions() {
        $pageTitle    = 'Transactions';
        $remarks      = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id())
                                    ->with(['deposit.reward', 'deposit.campaign', 'reward'])
                                    ->searchable(['trx'])
                                    ->filter(['remark'])
                                    ->orderBy('id', 'desc')
                                    ->paginate(getPaginate());

        $emptyMessage = 'No transactions found';
        return view($this->activeTheme . 'user.page.transactions', compact('pageTitle', 'transactions', 'remarks', 'emptyMessage'));
    }

    function rewardsTracking() {
        $pageTitle = 'Rewards Tracking';
        $filter = request('filter', 'received'); // received or paid
        
        // Check if reward_id column exists in transactions table
        $hasRewardIdColumn = Schema::hasColumn('transactions', 'reward_id');
        
        if ($filter == 'received') {
            // Rewards received by creator (donation_received transactions)
            $query = Transaction::where('user_id', auth()->id())
                ->where('remark', 'donation_received');
            
            // Only filter by reward_id if the column exists
            if ($hasRewardIdColumn) {
                $query->whereNotNull('reward_id');
            }
            
            // Get unique transaction IDs first to avoid duplicates
            $transactionIds = $query->pluck('id')
                ->unique()
                ->toArray();
            
            // Now get the transactions with relationships
            if (empty($transactionIds)) {
                $transactions = Transaction::where('id', 0)->paginate(getPaginate()); // Empty result
            } else {
                $transactions = Transaction::whereIn('id', $transactionIds)
                    ->with(['reward', 'deposit.campaign', 'deposit.user', 'deposit.reward'])
                    ->latest()
                    ->paginate(getPaginate());
            }
            
        } else {
            // Rewards paid by contributor (donation_given transactions)
            $query = Transaction::where('user_id', auth()->id())
                ->where('remark', 'donation_given');
            
            // Only filter by reward_id if the column exists
            if ($hasRewardIdColumn) {
                $query->whereNotNull('reward_id');
            }
            
            // Get unique transaction IDs first to avoid duplicates
            $transactionIds = $query->pluck('id')
                ->unique()
                ->toArray();
            
            // Now get the transactions with relationships
            if (empty($transactionIds)) {
                $transactions = Transaction::where('id', 0)->paginate(getPaginate()); // Empty result
            } else {
                $transactions = Transaction::whereIn('id', $transactionIds)
                    ->with(['reward', 'deposit.campaign', 'deposit.reward'])
                    ->latest()
                    ->paginate(getPaginate());
            }
            
        }

        $emptyMessage = 'No rewards found';
        
        return view($this->activeTheme . 'user.rewards.index', compact('pageTitle', 'transactions', 'filter', 'emptyMessage'));
    }

    function fulfillReward() {
        $this->validate(request(), [
            'trx' => 'required|string',
            'note' => 'nullable|string|max:500',
        ]);

        $trx = request('trx');
        $transaction = Transaction::where('trx', $trx)
            ->where('remark', 'donation_received')
            ->where('user_id', auth()->id())
            ->whereHas('deposit', function($q) {
                $q->whereNotNull('reward_id');
            })
            ->where(function($query) {
                // Check if reward_fulfilled column exists, if not skip that condition
                if (Schema::hasColumn('transactions', 'reward_fulfilled')) {
                    $query->where('reward_fulfilled', false);
                }
            })
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found or already fulfilled'
            ], 404);
        }

        // Update reward fulfillment fields if columns exist
        if (Schema::hasColumn('transactions', 'reward_fulfilled')) {
            $transaction->reward_fulfilled = true;
        }
        if (Schema::hasColumn('transactions', 'reward_fulfilled_at')) {
            $transaction->reward_fulfilled_at = now();
        }
        if (Schema::hasColumn('transactions', 'reward_fulfillment_note')) {
            $transaction->reward_fulfillment_note = request('note');
        }
        $transaction->save();

        return response()->json([
            'success' => true,
            'message' => 'Reward marked as fulfilled successfully'
        ]);
    }

    function fileDownload() {
        $path = request('filePath');
        $file = getFilePath($path) . '/' . request('fileName');

        return response()->download($file);
    }
}
