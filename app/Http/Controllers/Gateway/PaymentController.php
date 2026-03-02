<?php

namespace App\Http\Controllers\Gateway;

use App\Models\User;
use App\Models\Deposit;
use App\Models\Campaign;
use App\Lib\FormProcessor;
use App\Models\Transaction;
use App\Constants\ManageStatus;
use App\Models\GatewayCurrency;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    function depositInserts($slug) {
        
        $countryData = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        
        // $mobileCodes = implode(',', array_column($countryData, 'dial_code'));
        $countries   = implode(',', array_column($countryData, 'country'));

        $this->validate(request(), [
            'amount'      => 'required|numeric|gt:0',
            'full_name'   => 'required|string|max:255',
            'email'       => 'required|email|max:40',
            // 'phone'       => 'required|max:40',
            'country'     => 'required|max:40|in:' . $countries,
            // 'mobile_code' => 'required|in:' . $mobileCodes,
            'gateway'     => 'required|exists:gateways,code',
            'currency'    => 'required',
        ]);

        // Phone validation disabled - accept any phone format
        $phone = request('phone');
        $country = request('country');
        

        $campaign = Campaign::where('slug', $slug)->approve()->firstOrFail();

        if (!$campaign) {
            $toast[] = ['error', 'Campaign not found'];
            return back()->withToasts($toast);
        }

        if ($campaign->isExpired()) {
            $toast[] = ['error', 'This campaign has expired'];
            return back()->withToasts($toast);
        }

        $userCountry = auth()->check() ? auth()->user()->country_name : request('country');
        
        $gatewayData = GatewayCurrency::whereHas('method', function ($gateway) use ($userCountry) {
                        $gateway->active();
                        if ($userCountry) {
                            $gateway->forCountry($userCountry);
                        }
                    })->where('method_code', request('gateway'))
                    ->where('currency', request('currency'))
                    ->first();

        if (!$gatewayData) {
            $toast[] = ['error', 'Invalid gateway or gateway not available in your country'];
            return back()->withToasts($toast);
        }

        $amount = request('amount');

        if ($gatewayData->min_amount > $amount || $gatewayData->max_amount < $amount) {
            $toast[] = ['error', 'Please follow donation limit'];
            return back()->withToasts($toast);
        }

        // Validate reward if provided
        $rewardId = request('reward_id') ?: request('reward');
        $reward = null;
        if ($rewardId) {
            $reward = $campaign->rewards()->where('id', $rewardId)->where('is_active', true)->first();
            if (!$reward) {
                $toast[] = ['error', 'Selected reward is not available'];
                return back()->withToasts($toast);
            }
            
            // Check if reward is available (not sold out)
            if (!$reward->isAvailable()) {
                $toast[] = ['error', 'This reward is no longer available'];
                return back()->withToasts($toast);
            }
            
            // Check if donation amount meets minimum requirement
            if ($amount < $reward->minimum_amount) {
                $setting = gs();
                $toast[] = ['error', 'Donation amount must be at least ' . $setting->cur_sym . showAmount($reward->minimum_amount) . ' for this reward'];
                return back()->withToasts($toast);
            }
        }

        $charge       = $gatewayData->fixed_charge + (($amount * $gatewayData->percent_charge) / 100);
        $payable      = $amount + $charge;
        $final_amount = $payable * $gatewayData->rate;

        if (auth()->check()) {
            $userFullName = auth()->user()->fullname;
            $userEmail    = auth()->user()->email;
            $userPhone    = auth()->user()->mobile;
            $userCountry  = auth()->user()->country_name;
        } else {
            $userFullName = request('full_name');
            $userEmail    = request('email');
            $userPhone    = formatPhoneForStorage(request('phone'), request('country'));
            $userCountry  = request('country');
        }

        // Save data in deposit table
        $deposit                  = new Deposit();
        $deposit->campaign_id     = $campaign->id;
        $deposit->user_id         = auth()->check() ? auth()->id() : 0;
        $deposit->donor_type      = request('anonymousDonation') == 'on' ? ManageStatus::ANONYMOUS_DONOR : ManageStatus::KNOWN_DONOR;
        $deposit->full_name       = $userFullName;
        $deposit->email           = $userEmail;
        $deposit->phone           = $userPhone;
        $deposit->country         = $userCountry;
        $deposit->receiver_id     = $campaign->user->id;
        $deposit->reward_id       = $rewardId ? $rewardId : null;
        $deposit->method_code     = $gatewayData->method_code;
        $deposit->amount          = $amount;
        $deposit->method_currency = strtoupper($gatewayData->currency);
        $deposit->charge          = $charge;
        $deposit->rate            = $gatewayData->rate;
        $deposit->final_amount    = $final_amount;
        $deposit->btc_amount      = 0;
        $deposit->btc_wallet      = "";
        $deposit->trx             = getTrx();
        $deposit->save();

        session()->put('Track', $deposit->trx);

        return to_route('user.deposit.confirm');
    }

    function depositConfirm() {
        $track = session()->get('Track') ?? request()->query('trx');
        if (!$track) {
            abort(404, 'Invalid or missing payment session');
        }
        $deposit = Deposit::with('gateway')->where('trx', $track)->initiate()->firstOrFail();

        // Set session for webview/mobile flow (trx from query param)
        session()->put('Track', $deposit->trx);

        if ($deposit->method_code >= 1000) return to_route('user.deposit.manual.confirm');

        $dirName = $deposit->gateway->alias;
        $new     = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        \Log::channel('payments')->info('Payment process started', [
            'gateway'      => $dirName,
            'method_code'  => $deposit->method_code,
            'trx'          => $deposit->trx,
            'amount'       => $deposit->final_amount,
            'currency'     => $deposit->method_currency,
        ]);

        $data    = $new::process($deposit);
        $data    = json_decode($data);
        

        if (isset($data->error)) {
            \Log::channel('payments')->error('Payment process returned error to user', [
                'gateway' => $dirName,
                'trx'     => $deposit->trx,
                'message' => $data->message ?? 'unknown',
            ]);
            $toast[] = ['error', $data->message];

            return to_route(gatewayRedirectUrl())->withToasts($toast);
        }
        

        if (isset($data->redirect)) return redirect($data->redirect_url);

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Donation Confirmation';

        return view($this->activeTheme . $data->view, compact('data', 'pageTitle', 'deposit'));
    }

    static function campaignDataUpdate($deposit, $isManual = null) {
        if ($deposit->status == ManageStatus::PAYMENT_INITIATE || $deposit->status == ManageStatus::PAYMENT_PENDING) {
            $deposit->status = ManageStatus::PAYMENT_SUCCESS;
            $deposit->save();

            $depositType = $deposit->deposit_type ?? 'donation';

            if ($depositType === 'registration_fee') {
                // Registration fee: platform keeps it, no campaign/creator credit
                if (!$isManual) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->user_id = $deposit->user_id;
                    $adminNotification->title = 'Registration fee paid via ' . $deposit->gatewayCurrency()->name . ' for campaign: ' . ($deposit->campaign->name ?? '');
                    $adminNotification->click_url = urlPath('admin.donations.done');
                    $adminNotification->save();
                }
                $user = $deposit->user;
                if ($user) {
                    notify($user, 'DONATION_COMPLETE', [
                        'method_name'     => $deposit->gatewayCurrency()->name,
                        'method_currency' => $deposit->method_currency,
                        'method_amount'   => showAmount($deposit->final_amount),
                        'amount'          => showAmount($deposit->amount),
                        'charge'          => showAmount($deposit->charge),
                        'rate'            => showAmount($deposit->rate),
                        'trx'             => $deposit->trx,
                        'campaign_name'   => $deposit->campaign->name ?? '',
                    ]);
                }
                return;
            }

            $user = User::find($deposit->user_id);

            if (!$user) {
                $user = [
                    'fullname' => $deposit->full_name,
                    'username' => $deposit->email,
                    'email'    => $deposit->email,
                    'mobile'   => $deposit->phone,
                ];
            }

            $campaign                 = $deposit->campaign;
            $campaign->raised_amount += $deposit->amount;
            $campaign->save();

            // Update reward claimed count if reward was selected
            if ($deposit->reward_id) {
                $reward = \App\Models\Reward::find($deposit->reward_id);
                if ($reward && $reward->quantity !== null) {
                    $reward->claimed_count = ($reward->claimed_count ?? 0) + 1;
                    $reward->save();
                }
            }

            $campaignAuthor           = $campaign->user;
            $campaignAuthor->balance += $deposit->amount;
            $campaignAuthor->save();

            // donor transaction
            $transaction               = new Transaction();
            $transaction->user_id      = $deposit->user_id;
            $transaction->amount       = $deposit->amount;
            $transaction->charge       = $deposit->charge;
            $transaction->post_balance = $user->balance ?? 0;
            $transaction->trx_type     = '-';
            $transaction->trx          = $deposit->trx;
            $transaction->details      = 'Contribution Via ' . $deposit->gatewayCurrency()->name;
            $transaction->remark       = 'donation_given';
            $transaction->save();

            // receiver transaction
            $transaction               = new Transaction();
            $transaction->user_id      = $campaignAuthor->id;
            $transaction->amount       = $deposit->amount;
            $transaction->charge       = 0;
            $transaction->post_balance = $campaignAuthor->balance ?? 0;
            $transaction->trx_type     = '+';
            $transaction->trx          = $deposit->trx;
            $transaction->details      = 'Contribution received for a campaign';
            $transaction->remark       = 'donation_received';
            $transaction->reward_id   = $deposit->reward_id; // Add reward_id for tracking
            $transaction->reward_fulfilled = false; // Default to not fulfilled
            $transaction->save();

            if (!$isManual) {
                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $deposit->user_id;
                $adminNotification->title     = 'Deposit successful via ' . $deposit->gatewayCurrency()->name . ' for a campaign';
                $adminNotification->click_url = urlPath('admin.donations.done');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DONATION_APPROVE' : 'DONATION_COMPLETE', [
                'method_name'     => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount'   => showAmount($deposit->final_amount),
                'amount'          => showAmount($deposit->amount),
                'charge'          => showAmount($deposit->charge),
                'rate'            => showAmount($deposit->rate),
                'trx'             => $deposit->trx,
                'campaign_name'   => $campaign->name,
            ]);
        }
    }

    function manualDepositConfirm() {
        $track   = session()->get('Track');
        $deposit = Deposit::with('gateway')->where('trx', $track)->initiate()->first();

        if (!$deposit) return to_route(gatewayRedirectUrl());

        if ($deposit->method_code > 999) {
            $pageTitle       = 'Donation Confirmation';
            $gatewayCurrency = $deposit->gatewayCurrency();
            $gateway         = $gatewayCurrency->method;
            
            // Check if gateway and form exist
            if (!$gateway || !$gateway->form) {
                $toast[] = ['error', 'Invalid gateway configuration'];
                return to_route(gatewayRedirectUrl())->withToasts($toast);
            }

            return view($this->activeTheme . 'user.payment.manual', compact('deposit', 'pageTitle', 'gateway'));
        }

        abort(404);
    }

    function manualDepositUpdate() {
        $track   = session()->get('Track');
        $deposit = Deposit::with('gateway')->where('trx', $track)->initiate()->first();

        if (!$deposit) return to_route(gatewayRedirectUrl());

        $gatewayCurrency = $deposit->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        
        // Check if gateway and form exist
        if (!$gateway || !$gateway->form) {
            $toast[] = ['error', 'Invalid gateway configuration'];
            return to_route(gatewayRedirectUrl())->withToasts($toast);
        }
        
        $formData = $gateway->form->form_data;

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);

        request()->validate($validationRule);
        $userData = $formProcessor->processFormData(request(), $formData);

        $deposit->details = $userData;
        $deposit->status  = ManageStatus::PAYMENT_PENDING;
        $deposit->save();

        $adminNotification          = new AdminNotification();
        $adminNotification->user_id = $deposit->user->id ?? 0;

        if ($deposit->donor_type) {
            if ($deposit->user_id) $donor = $deposit->user->fullname;
            else $donor = $deposit->full_name;
        } else {
            $donor = 'an anonymous user';
        }

        $adminNotification->title     = "Donation request from $donor for a campaign";
        $adminNotification->click_url = urlPath('admin.donations.pending');
        $adminNotification->save();

        if (!$deposit->user) {
            $user = [
                'fullname' => $deposit->full_name,
                'username' => $deposit->email,
                'email'    => $deposit->email,
                'mobile'   => $deposit->phone,
            ];
        } else {
            $user = $deposit->user;
        }

        notify($user, 'DONATION_REQUEST', [
            'method_name'     => $deposit->gatewayCurrency()->name,
            'method_currency' => $deposit->method_currency,
            'method_amount'   => showAmount($deposit->final_amount),
            'amount'          => showAmount($deposit->amount),
            'charge'          => showAmount($deposit->charge),
            'rate'            => showAmount($deposit->rate),
            'trx'             => $deposit->trx,
            'campaign_name'   => $deposit->campaign->name,
        ]);

        $toast[]   = ['success', 'Your donation request has been taken. Please wait for admin response'];
        $routeName = auth()->check() ? 'user.donation.history' : 'campaign';

        return to_route($routeName)->withToasts($toast);
    }

    function success() {
        $track   = session()->get('Track');
        $deposit = Deposit::with('gateway')->where('trx', $track)->done()->first();

        if (!$deposit) return to_route(gatewayRedirectUrl());

        $toast[] = ['success', 'Payment completed successfully'];

        // Registration fee: redirect to campaign edit
        if (($deposit->deposit_type ?? '') === 'registration_fee' && $deposit->campaign_id) {
            $slug = session()->pull('registration_fee_campaign_slug') ?? $deposit->campaign->slug ?? null;
            if ($slug && auth()->check()) {
                return redirect()->route('user.campaign.edit.basics', $slug)->withToasts($toast);
            }
        }
        
        if (auth()->check()) {
            return to_route('user.donation.history')->withToasts($toast);
        } else {
            return to_route('campaign')->withToasts($toast);
        }
    }
}
