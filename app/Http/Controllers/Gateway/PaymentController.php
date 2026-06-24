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

/**
 * Web payment flow: validate donor input, create {@see Deposit} rows, redirect to gateway manual instructions,
 * and handle manual proof upload for offline/bank gateways.
 *
 * Entry routes are defined in `routes/web.php` (e.g. deposit insert per campaign slug). Responses use
 * session toasts, redirects, or themed Blade views — not the legacy JSON API shape.
 */
class PaymentController extends Controller
{
    /** Laravel `max` rule for files is kilobytes — 5120 KB = 5 MB. */
    private const MANUAL_PAYMENT_PROOF_MAX_KB = 5120;

    /**
     * Logged-in contributor can open proof upload without re-visiting manual instructions (e.g. closed webview early).
     */
    protected function contributorMaySkipManualGuide(Deposit $deposit): bool
    {
        if (! auth()->check()) {
            return false;
        }
        if ((int) $deposit->user_id === (int) auth()->id()) {
            return true;
        }
        if ((int) $deposit->user_id !== 0) {
            return false;
        }
        $authEmail = strtolower(trim((string) (auth()->user()->email ?? '')));
        $depEmail = strtolower(trim((string) ($deposit->email ?? '')));

        return $authEmail !== '' && $authEmail === $depEmail;
    }

    /**
     * Green / apnafund themes POST anonymous_donation=1; classic checkbox name anonymousDonation with value on.
     */
    private static function depositRequestIsAnonymousDonation(): bool
    {
        if (request()->has('anonymous_donation')) {
            $v = request('anonymous_donation');

            return $v !== '0' && $v !== '' && $v !== false && strtolower((string) $v) !== 'false';
        }

        return request('anonymousDonation') === 'on' || request('anonymousDonation') === '1';
    }

    function depositInserts($slug) {
        if (!auth()->check()) {
            $redirect = route('user.login.form', ['redirect' => url()->current()]);
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to make a contribution.',
                    'redirect' => $redirect,
                ], 401);
            }
            $toast[] = ['error', 'Please log in to make a contribution.'];
            return redirect($redirect)->withToasts($toast);
        }

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

        if (auth()->check() && (int) auth()->id() === (int) $campaign->user_id) {
            $toast[] = ['error', 'You cannot contribute to your own campaign'];
            return back()->withToasts($toast);
        }

        $gatewayFilterCountry = resolveCountryForGatewayFiltering();

        $gatewayData = GatewayCurrency::whereHas('method', function ($gateway) use ($gatewayFilterCountry) {
                        $gateway->active();
                        if ($gatewayFilterCountry) {
                            $gateway->forCountry($gatewayFilterCountry);
                        }
                    })->where('method_code', request('gateway'))
                    ->where('currency', request('currency'))
                    ->first();

        if (!$gatewayData) {
            $toast[] = ['error', 'Invalid gateway or gateway not available in your country'];
            return back()->withToasts($toast);
        }

        // User enters amount in local/display currency; convert and store in platform currency (USD/site_cur).
        $enteredAmount = round((float) request('amount'), 8);
        $inputCurrency = request('input_currency') ?: (session('user_detected_currency') ?: getPlatformCurrency());
        $currencyService = app(\App\Services\CurrencyService::class);
        $amount = round((float) $currencyService->convertToPlatform($enteredAmount, $inputCurrency), 8);

        $gatewayCurrencyCode = strtoupper((string) $gatewayData->currency);
        $platformCurrency = strtoupper((string) getPlatformCurrency());
        $amountForGatewayLimit = ($gatewayCurrencyCode === $platformCurrency || $gatewayCurrencyCode === strtoupper($inputCurrency))
            ? $enteredAmount
            : round($enteredAmount * (float) $gatewayData->rate, 2);

        if ((float) $gatewayData->min_amount > $amountForGatewayLimit || (float) $gatewayData->max_amount < $amountForGatewayLimit) {
            $toast[] = ['error', 'Amount must be between ' . showAmount($gatewayData->min_amount) . ' and ' . showAmount($gatewayData->max_amount) . ' ' . $gatewayData->currency];
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
        $final_amount = round($amountForGatewayLimit + (($amountForGatewayLimit * $gatewayData->percent_charge) / 100) + (float) $gatewayData->fixed_charge, 2);

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
        $deposit->donor_type      = self::depositRequestIsAnonymousDonation() ? ManageStatus::ANONYMOUS_DONOR : ManageStatus::KNOWN_DONOR;
        $deposit->full_name       = $userFullName;
        $deposit->email           = $userEmail;
        $deposit->phone           = $userPhone;
        $deposit->country         = $userCountry;
        $deposit->receiver_id     = $campaign->user->id;
        $deposit->reward_id       = $rewardId ? $rewardId : null;
        $deposit->method_code     = $gatewayData->method_code;
        $deposit->amount          = round($amount, 2);
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

    /**
     * Donation amount credited to campaign/receiver — always in platform currency (deposits.amount).
     */
    private static function platformDonationAmount(Deposit $deposit): float
    {
        return round((float) $deposit->amount, 2);
    }

    function depositConfirm() {
        
        $track = session()->get('Track') ?? request()->query('trx');
        if(!$track || !empty($_GET['trx'])){
            $track = $_GET['trx'];
        }
        
        if (!$track) {

            abort(404, 'Invalid or missing payment session');
        }
        $deposit = Deposit::with('gateway')->where('trx', $track)->initiate()->firstOrFail();
        // Set session for webview/mobile flow (trx from query param)
        session()->put('Track', $deposit->trx);
        syncVisitorCurrencySessionFromCountryName($deposit->country ? (string) $deposit->country : null);

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.instructions');
        }

        $dirName = $deposit->gateway->alias;
        
        $country = (string) $deposit->country;
        $currencyFromCountry = strtoupper((string) getCurrencyCodeForCountryName($country));
        $currencyFromCountry = $currencyFromCountry ?: $deposit->method_currency;
        $new     = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';
        $currencyService = app(\App\Services\CurrencyService::class);
        $hasCountryGatewayCurrency = GatewayCurrency::where('method_code', $deposit->method_code)
            ->where('status', ManageStatus::ACTIVE)
            ->whereRaw('UPPER(TRIM(currency)) = ?', [strtoupper(trim($currencyFromCountry))])
            ->exists();
        try {
            if ($hasCountryGatewayCurrency) {
                // Only switch currency when gateway has configuration for that currency.
                $convertedFinalAmount = $currencyService->convertFromPlatform((float) $deposit->amount, $currencyFromCountry);
                $deposit->final_amount = round((float) $convertedFinalAmount, 2);
                $deposit->method_currency = $currencyFromCountry;
            } else {
                $deposit->final_amount = round((float) $deposit->final_amount, 2);
            }
        } catch (\Throwable $e) {
            // Fallback to stored final amount if conversion mapping is unavailable.
            $deposit->final_amount = round((float) $deposit->final_amount, 2);
        }
        \Log::channel('payments')->info('Payment process started', [
            'gateway'      => $dirName,
            'method_code'  => $deposit->method_code,
            'trx'          => $deposit->trx,
            'amount'       => $deposit->final_amount,
            'currency'     => $deposit->method_currency,
            'country'      => $country,
            'country_currency' => $currencyFromCountry,
        ]);

        $data    = $new::process($deposit);
        $data    = json_decode($data);
        // dd($data);
        if (isset($data->error)) {
            $errorMsg = isset($data->message) ? $data->message : 'Payment failed';
            \Log::channel('payments')->error('Payment process returned error to user', [
                'gateway' => $dirName,
                'trx'     => $deposit->trx,
                'message' => $errorMsg,
            ]);
            $toast[] = ['error', $errorMsg];

            return redirect()->to(gatewayRedirectUrlFull(false, $errorMsg))->withToasts($toast);
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
        if ($deposit->status == ManageStatus::PAYMENT_INITIATE || $deposit->status == ManageStatus::PAYMENT_PENDING ) {
            $deposit->status = ManageStatus::PAYMENT_SUCCESS;
            if(isset($deposit->jazzcash))
            {
                unset($deposit->jazzcash);
                $deposit->status = ManageStatus::PAYMENT_PENDING;
            }
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

            $campaign      = $deposit->campaign;
            $platformAmount = self::platformDonationAmount($deposit);

            // Keep existing campaign dates intact while updating raised amount.
            $oldStartDate = $campaign->start_date;
            $oldEndDate   = $campaign->getRawOriginal('end_date') ?: optional($campaign->end_date)->format('Y-m-d');

            $campaign->raised_amount += $platformAmount;
            $campaign->start_date = $oldStartDate;
            $campaign->end_date   = $oldEndDate;
            $campaign->save();

            try {
                app(\App\Services\CampaignGoalReachedNotificationService::class)->handleAfterCampaignUpdate($campaign);
            } catch (\Throwable $e) {
                \Log::warning('Campaign goal reached notification failed', ['error' => $e->getMessage(), 'campaign_id' => $campaign->id]);
            }

            // Update reward claimed count if reward was selected
            if ($deposit->reward_id) {
                $reward = \App\Models\Reward::find($deposit->reward_id);
                if ($reward && $reward->quantity !== null) {
                    $reward->claimed_count = ($reward->claimed_count ?? 0) + 1;
                    $reward->save();
                }
            }

            $campaignAuthor           = $campaign->user;
            $campaignAuthor->balance += $platformAmount;
            $campaignAuthor->save();

            // donor transaction
            $transaction               = new Transaction();
            $transaction->user_id      = $deposit->user_id;
            $transaction->amount       = $platformAmount;
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
            $transaction->amount       = $platformAmount;
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

            try {
                if ($campaignAuthor && $campaignAuthor->id) {
                    \App\Models\UserNotification::notifyCreatorNewDonation((int) $campaignAuthor->id, $deposit, $campaign);
                }
            } catch (\Throwable $e) {
                \Log::warning('Creator UserNotification failed', ['error' => $e->getMessage()]);
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

    /**
     * Step 1: Show admin guideline + notify admin (pending approval). Next: manual.confirm for proof form.
     */
    function manualDepositInstructions() {
        $track = session()->get('Track') ?? request()->query('trx');
        if (!$track) {
            return redirect()->to(gatewayRedirectUrlFull(false));
        }
        session()->put('Track', $track);

        $deposit = Deposit::with(['gateway', 'campaign', 'user'])->where('trx', $track)->initiate()->first();
        if (!$deposit) {
            return redirect()->to(gatewayRedirectUrlFull(false));
        }

        if ($deposit->method_code < 1000) {
            abort(404);
        }

        $gatewayCurrency = $deposit->gatewayCurrency();
        $gateway = $gatewayCurrency?->method;
        if (!$gateway) {
            $toast[] = ['error', 'Invalid gateway configuration'];
            return redirect()->to(gatewayRedirectUrlFull(false))->withToasts($toast);
        }

        $notifSessionKey = 'manual_instr_admin_notif_' . $deposit->trx;
        if (!session()->get($notifSessionKey)) {
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $deposit->user_id ?: 0;
            $campaignName = $deposit->campaign->name ?? 'Campaign';
            $donor = ($deposit->user_id && $deposit->user)
                ? $deposit->user->fullname
                : ($deposit->full_name ?? 'Guest');
            $adminNotification->title = 'Manual payment pending approval: ' . $donor . ' — ' . $campaignName;
            $adminNotification->click_url = urlPath('admin.donations.pending');
            $adminNotification->save();
            session()->put($notifSessionKey, true);
        }

        session()->put('manual_guide_seen', $deposit->trx);

        $pageTitle = __('Payment instructions');

        return view($this->activeTheme . 'user.payment.manual-instructions', compact('deposit', 'pageTitle', 'gateway', 'gatewayCurrency'));
    }

    /**
     * Upload payment proof (screenshot) when gateway has no Phinix form — avoids manual.confirm failing on missing form.
     */
    function manualDepositProof() {
        $track = session()->get('Track') ?? request()->query('trx');
        if (!$track) {
            return redirect()->to(gatewayRedirectUrlFull(false));
        }
        session()->put('Track', $track);

        $deposit = Deposit::with(['gateway', 'campaign', 'user'])->where('trx', $track)->initiate()->first();
        if (!$deposit) {
            return redirect()->to(gatewayRedirectUrlFull(false));
        }

        if ($deposit->method_code < 1000) {
            abort(404);
        }

        if (session('manual_guide_seen') !== $deposit->trx && ! $this->contributorMaySkipManualGuide($deposit)) {
            return redirect()->route('user.deposit.manual.instructions');
        }

        $gatewayCurrency = $deposit->gatewayCurrency();
        $gateway         = $gatewayCurrency?->method;
        if (!$gateway) {
            $toast[] = ['error', 'Invalid gateway configuration'];
            return redirect()->to(gatewayRedirectUrlFull(false))->withToasts($toast);
        }

        $pageTitle = __('Submit payment proof');

        return view($this->activeTheme . 'user.payment.manual-proof', compact('deposit', 'pageTitle', 'gateway', 'gatewayCurrency'));
    }

    function manualDepositProofSubmit() {
        $request = request();

        $request->validate([
            'trx'           => 'required|string',
            'payment_proof' => 'required|file|mimes:jpeg,jpg,png,pdf,webp|max:' . self::MANUAL_PAYMENT_PROOF_MAX_KB,
            'note'          => 'nullable|string|max:1000',
        ], [
            'payment_proof.max' => __('The payment proof file must not be larger than 5 MB.'),
        ]);

        $track = $request->string('trx')->toString();
        if (session()->get('Track') && session()->get('Track') !== $track) {
            $toast[] = ['error', 'Invalid session. Please open the payment link again.'];
            return redirect()->to(gatewayRedirectUrlFull(false))->withToasts($toast);
        }
        session()->put('Track', $track);

        $deposit = Deposit::with(['gateway', 'campaign', 'user'])->where('trx', $track)->initiate()->first();
        if (!$deposit) {
            return redirect()->to(gatewayRedirectUrlFull(false));
        }

        if ($deposit->method_code < 1000) {
            abort(404);
        }

        if (session('manual_guide_seen') !== $deposit->trx && ! $this->contributorMaySkipManualGuide($deposit)) {
            return redirect()->route('user.deposit.manual.instructions');
        }

        $gatewayCurrency = $deposit->gatewayCurrency();
        $gateway         = $gatewayCurrency?->method;
        if (!$gateway) {
            $toast[] = ['error', 'Invalid gateway configuration'];
            return redirect()->to(gatewayRedirectUrlFull(false))->withToasts($toast);
        }

        $directory = date('Y') . '/' . date('m') . '/' . date('d');
        $path      = getFilePath('verify') . '/' . $directory;
        $value     = $directory . '/' . fileUploader($request->file('payment_proof'), $path);

        $details = [
            [
                'name'  => __('Payment proof'),
                'type'  => 'file',
                'value' => $value,
            ],
        ];
        if ($request->filled('note')) {
            $details[] = [
                'name'  => __('Note'),
                'type'  => 'textarea',
                'value' => $request->string('note')->toString(),
            ];
        }

        $deposit->details = $details;
        $deposit->status  = ManageStatus::PAYMENT_PENDING;
        if ((int) $deposit->user_id === 0 && auth()->check()) {
            $ae = strtolower(trim((string) (auth()->user()->email ?? '')));
            $de = strtolower(trim((string) ($deposit->email ?? '')));
            if ($ae !== '' && $ae === $de) {
                $deposit->user_id = (int) auth()->id();
            }
        }
        $deposit->save();

        $adminNotification          = new AdminNotification();
        $adminNotification->user_id = $deposit->user->id ?? 0;

        if ($deposit->donor_type) {
            if ($deposit->user_id) {
                $donor = $deposit->user->fullname;
            } else {
                $donor = $deposit->full_name;
            }
        } else {
            $donor = 'an anonymous user';
        }

        $adminNotification->title     = 'Payment proof submitted — ' . $donor . ' — ' . ($deposit->campaign->name ?? 'Campaign');
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

        $toast[] = ['success', 'Your donation request has been taken. Please wait for admin response'];

        if (auth()->check()) {
            return back()->withToasts($toast);
        }

        return to_route('campaign')->withToasts($toast);
    }

    function manualDepositConfirm() {
        $track = session()->get('Track') ?? request()->query('trx');
        if (!$track) {
            return redirect()->to(gatewayRedirectUrlFull(false));
        }
        session()->put('Track', $track);

        $deposit = Deposit::with('gateway')->where('trx', $track)->initiate()->first();

        if (!$deposit) {
            return redirect()->to(gatewayRedirectUrlFull(false));
        }

        if ($deposit->method_code > 999) {
            if (session('manual_guide_seen') !== $deposit->trx && ! $this->contributorMaySkipManualGuide($deposit)) {
                return redirect()->route('user.deposit.manual.instructions');
            }

            $pageTitle       = 'Donation Confirmation';
            $gatewayCurrency = $deposit->gatewayCurrency();
            $gateway         = $gatewayCurrency->method;

            // Check if gateway and form exist
            if (!$gateway || !$gateway->form) {
                $toast[] = ['error', 'Invalid gateway configuration'];
                return redirect()->to(gatewayRedirectUrlFull(false))->withToasts($toast);
            }

            return view($this->activeTheme . 'user.payment.manual', compact('deposit', 'pageTitle', 'gateway'));
        }

        abort(404);
    }

    function manualDepositUpdate() {
        $track   = session()->get('Track');
        $deposit = Deposit::with('gateway')->where('trx', $track)->initiate()->first();

        if (!$deposit) return redirect()->to(gatewayRedirectUrlFull(false));

        $gatewayCurrency = $deposit->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        
        // Check if gateway and form exist
        if (!$gateway || !$gateway->form) {
            $toast[] = ['error', 'Invalid gateway configuration'];
            return redirect()->to(gatewayRedirectUrlFull(false))->withToasts($toast);
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

        $adminNotification->title     = 'Payment proof submitted — ' . $donor . ' — ' . ($deposit->campaign->name ?? 'Campaign');
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

        $toast[] = ['success', 'Your donation request has been taken. Please wait for admin response'];

        if (auth()->check()) {
            return back()->withToasts($toast);
        }

        return to_route('campaign')->withToasts($toast);
    }

    function success() {
        $track   = session()->get('Track');
        $deposit = Deposit::with('gateway')->where('trx', $track)->done()->first();
        $toast[] = ['success', 'Payment completed successfully'];

        if (!$deposit) return redirect()->to(gatewayRedirectUrlFull(true))->withToasts($toast);

        

        // Registration fee: redirect to campaign edit
        if (($deposit->deposit_type ?? '') === 'registration_fee' && $deposit->campaign_id) {
            $slug = session()->pull('registration_fee_campaign_slug') ?? $deposit->campaign->slug ?? null;
            if ($slug && auth()->check()) {
                return redirect()->route('user.campaign.edit.basics', $slug)->withToasts($toast);
            }
        }

        // Redirect to thank you page with order details
        return redirect()->route('order.success', ['id' => $deposit->id, 'payment_status' => 'success'])->withToasts($toast);
    }

    /**
     * Payment error page – mobile/webview friendly.
     * Shown when payment fails or is cancelled. Flutter app detects payment_status=error in URL.
     */
    function paymentError() {
        $message = request('message');
        if (!$message && session()->has('toasts')) {
            $toasts = session('toasts');
            $message = $toasts[0][1] ?? null;
        }
        $message = $message ?: __('Payment could not be completed. Please try again.');

        return view($this->activeTheme . 'user.payment.error', [
            // 'message' => $message,
            'pageTitle' => __('Payment Failed'),
        ]);
    }
}
