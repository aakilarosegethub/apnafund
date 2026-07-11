<?php

namespace App\Http\Controllers\Api;

use App\Constants\ManageStatus;
use App\Models\AdminNotification;
use App\Models\Campaign;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\GatewayCurrency;
use App\Models\Transaction;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends BaseApiController
{
    /**
     * Get Gateways List (min/max & charges on each gateway root; primary currency row)
     * GET /api/gateways
     * Query: ?country_id=1 (1-based index in getAdminDefaultAllCountryNames(), same ids as mobile list) or ?country=Pakistan (legacy).
     * If neither is set, local_currency_code / exchange_rate follow visitor IP (getLocalCurrencyCode).
     */
    public function gateways(Request $request): JsonResponse
    {
        $country = '';
        $fromId = resolveCountryNameFromAdminCountryId($request->query('country_id'));
        if ($fromId !== null) {
            $country = $fromId;
        } else {
            $country = (string) $request->query('country', '');
        }

        $query = Gateway::active()
            ->with(['currencies' => function ($q) {
                $q->where('status', 1)
                    ->orderBy('id')
                    ->select(
                        'id',
                        'method_code',
                        'min_amount',
                        'max_amount',
                        'fixed_charge',
                        'percent_charge'
                    );
            }]);

        if (! empty($country)) {
            $query->forCountry($country);
        }

        $gateways = $query->orderBy('id')->get()->map(function ($g) {
            $c = $g->currencies->first();

            return [
                'id' => $g->id,
                'code' => $g->code,
                'name' => $g->name,
                'alias' => $g->alias ?? $g->name,
                'min_amount' => $c ? (float) $c->min_amount : 0.0,
                'max_amount' => $c ? (float) $c->max_amount : 0.0,
                'fixed_charge' => $c ? (float) $c->fixed_charge : 0.0,
                'percent_charge' => $c ? (float) $c->percent_charge : 0.0,
            ];
        });

        $localCode = ! empty(trim((string) $country))
            ? strtoupper(getCurrencyCodeForCountryName(trim((string) $country)))
            : strtoupper(getLocalCurrencyCode());

        $platformCurrency = strtoupper(getPlatformCurrency());
        $currencyService = app(CurrencyService::class);
        $exchangeRate = 1.0;
        try {
            if ($localCode === $platformCurrency) {
                $exchangeRate = 1.0;
            } else {
                $exchangeRate = (float) $currencyService->convertFromPlatform(1.0, $localCode);
            }
        } catch (\Throwable $e) {
            $exchangeRate = $localCode === $platformCurrency ? 1.0 : null;
        }

        return response()->json([
            'ResponseCode' => '200',
            'Result' => 'true',
            'ResponseMsg' => 'Gateways list.',
            'platform_currency' => $platformCurrency,
            'local_currency_code' => $localCode,
            'local_currency_symbol' => CurrencyService::getSymbolForCode($localCode),
            'exchange_rate' => $exchangeRate === null ? null : round($exchangeRate, 8),
            'gateways' => $gateways,
        ]);
    }

    /**
     * Get Payment Gateway List
     */
    public function paymentGatewayList(Request $request): JsonResponse
    {
        // Get active payment gateways (using gateways table)
        // Status is boolean: 1 = active, 0 = inactive
        $sel = $this->h->queryfire('select id, code, name, alias, status from gateways where status = 1 order by id asc');

        if (! $sel) {
            return response()->json([
                'paymentdata' => [],
                'ResponseCode' => '200',
                'Result' => 'true',
                'ResponseMsg' => 'Payment Gateway List Founded!',
            ]);
        }

        $myarray = [];
        if ($sel->num_rows > 0) {
            while ($row = $sel->fetch_assoc()) {
                if (! $row) {
                    break;
                }
                $myarray[] = $row;
            }
        }

        return response()->json([
            'paymentdata' => $myarray,
            'ResponseCode' => '200',
            'Result' => 'true',
            'ResponseMsg' => 'Payment Gateway List Founded!',
        ]);
    }

    /**
     * Get Payment Webview URL for mobile apps
     * POST /api/payment/webview-url
     * Body: gateway_id (int) OR gateway (string code), amount, campaign_id (int), full_name, email, phone (optional).
     * Location: country_id (allowed list, same as /api/gateways) preferred; else country name; else IP for currency.
     */
    public function webviewUrl(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);
        // dd();

        // Normalize: campaign_id can be string "118", int, or decimal (298.8) – round to int
        if (isset($data['campaign_id'])) {
            $data['campaign_id'] = (int) round((float) $data['campaign_id']);
        }

        $gatewayCode = null;
        $gatewayId = $data['gateway_id'] ?? $data['gateway'] ?? null;
        // Support: gateway_id (int), gateway as id (numeric), or gateway as code (string)
        if (! empty($gatewayId) && is_numeric($gatewayId)) {
            $gateway = Gateway::active()->find((int) $gatewayId);
            if (! $gateway) {
                // Try by code (e.g. "114" is Stripe's code, not id)
                $gateway = Gateway::active()->where('code', (string) $gatewayId)->first();
            }
            if (! $gateway) {
                return response()->json([
                    'Result' => 'false',
                    'ResponseCode' => '400',
                    'ResponseMsg' => 'Gateway not found',
                ], 400);
            }
            $gatewayCode = $gateway->code;
        } elseif (! empty($data['gateway'])) {
            $gatewayCode = (string) $data['gateway'];
        } else {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'gateway_id or gateway is required',
            ], 400);
        }

        $validator = \Validator::make($data, [
            'amount' => 'required|numeric|gt:0',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:40',
            'campaign_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => $validator->errors()->first(),
            ], 400);
        }

        $campaign = Campaign::where('id', $data['campaign_id'])->approve()->first();
        if (! $campaign) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '404',
                'ResponseMsg' => 'Campaign not found',
            ], 404);
        }

        if ($campaign->isExpired()) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'This campaign has expired',
            ], 400);
        }

        // country_id (admin list index) > explicit country name > IP — deposit.country must match app selection.
        $countryFromId = resolveCountryNameFromAdminCountryId($data['country_id'] ?? null);
        if ($countryFromId !== null) {
            $country = $countryFromId;
        } elseif (! empty($data['country']) && is_string($data['country']) && trim($data['country']) !== '') {
            $country = trim((string) $data['country']);
        } else {
            $country = getUserCountryByIP() ?? 'Pakistan';
        }
        $data['country'] = $country;

        // Currency from resolved country; client-sent currency is ignored (exchange UI uses country/location).
        $userCurrency = strtoupper((string) getCurrencyCodeForCountryName($country));
        // Fallback: strict country map miss — align with visitor local currency, then gateway default.
        if ($userCurrency === '' || resolveStrictCurrencyCodeForCountryName($country) === null) {
            $local = strtoupper(getLocalCurrencyCode());
            $userCurrency = $local !== '' ? $local : $userCurrency;
        }
        if ($userCurrency === '') {
            $defaultCurrency = GatewayCurrency::whereHas('method', function ($q) {
                $q->active();
            })->where('method_code', $gatewayCode)
                ->where('status', 1)
                ->orderBy('id')
                ->value('currency');
            $userCurrency = $defaultCurrency ? strtoupper((string) $defaultCurrency) : 'PKR';
        }

        if (! $userCurrency) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'Currency is required. Gateway has no default currency.',
            ], 400);
        }

        // Prefer gateway currency matching local currency, then mapping via input_currency_rates, then first active.
        $currencyMatch = [$userCurrency];
        // if ($userCurrency === 'PKR') {
        //     $currencyMatch[] = '0'; // Some gateways store PKR as "0".
        // }

        $baseGatewayQuery = GatewayCurrency::whereHas('method', function ($q) {
            $q->active();
        })->where('method_code', $gatewayCode)
            ->where('status', 1);

        $gatewayData = (clone $baseGatewayQuery)
            ->whereIn('currency', $currencyMatch)
            ->first();

        if (! $gatewayData) {
            $gatewayData = (clone $baseGatewayQuery)
                ->get()
                ->first(function ($gc) use ($userCurrency) {
                    $rates = $gc->input_currency_rates ?? [];

                    return ! empty($rates) && isset($rates[$userCurrency]) && (float) ($rates[$userCurrency] ?? 0) > 0;
                });
        }

        if (! $gatewayData) {
            $gatewayData = (clone $baseGatewayQuery)->orderBy('id')->first();
        }

        if (! $gatewayData) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'Invalid gateway or gateway not available for this currency',
            ], 400);
        }

        // DB amount must be in platform/system currency (USD by default).
        $amountUser = (float) $data['amount'];

        $currencyService = app(\App\Services\CurrencyService::class);
        $amount = round((float) $currencyService->convertToPlatform($amountUser, $userCurrency), 8);
        if ($amount <= 0) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'Invalid amount for currency conversion',
            ], 400);
        }

        // Validate limits in gateway/method currency (not platform currency).
        $gatewayCurrencyCode = strtoupper((string) $gatewayData->currency);
        $isDirectGatewayCurrency = in_array($gatewayCurrencyCode, $currencyMatch, true)
            || ($gatewayCurrencyCode === '0' && $userCurrency === 'PKR');
        $amountForGatewayLimit = $isDirectGatewayCurrency
            ? $amountUser
            : ($amount * (float) $gatewayData->rate);

        if ((float) $gatewayData->min_amount > $amountForGatewayLimit || (float) $gatewayData->max_amount < $amountForGatewayLimit) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'Amount must be between '.$gatewayData->min_amount.' and '.$gatewayData->max_amount.' '.$gatewayData->currency,
            ], 400);
        }

        $charge = $gatewayData->fixed_charge + (($amount * $gatewayData->percent_charge) / 100);
        $payable = $amount + $charge;
        // final_amount is payable in gateway/method currency.
        // dd($payable,$gatewayData->rate);
        $finalAmount = $payable;

        $deposit = new Deposit;
        $deposit->campaign_id = $campaign->id;
        $deposit->user_id = $data['user_id'];
        $deposit->donor_type = ManageStatus::KNOWN_DONOR;
        $deposit->full_name = $data['full_name'];
        $deposit->email = $data['email'];
        $deposit->phone = formatPhoneForStorage($data['phone'] ?? '', $country);
        $deposit->country = $country;
        $deposit->receiver_id = $campaign->user->id;
        $deposit->reward_id = null;
        $deposit->method_code = $gatewayData->method_code;
        $deposit->amount = $amount;
        $deposit->method_currency = $userCurrency;
        $deposit->charge = $charge;
        $deposit->rate = $gatewayData->rate;
        $deposit->final_amount = $finalAmount;
        $deposit->btc_amount = 0;
        $deposit->btc_wallet = '';
        $deposit->trx = getTrx();
        $deposit->save();

        $gatewayData->load(['method', 'method.form']);
        $gateway = $gatewayData->method;
        $isManual = $gateway && (int) $gateway->code >= 1000;

        if ($isManual) {
            // Manual gateway: return payment guide/instructions, NO payment_url
            $paymentGuide = [
                'gateway_type' => 'manual',
                'gateway_name' => $gateway->name ?? $gatewayData->name,
                'guideline' => $gateway->guideline ?? '',
                'amount' => (float) $deposit->final_amount,
                'final_amount' => (float) $deposit->final_amount,
                'currency' => $deposit->method_currency,
                'charge' => (float) $deposit->charge,
                'platform_amount' => (float) $deposit->amount,
                'platform_currency' => strtoupper((string) getPlatformCurrency()),
                'trx' => $deposit->trx,
                'form_fields' => [],
            ];

            if ($gateway->form && $gateway->form->form_data) {
                $formData = is_array($gateway->form->form_data) ? $gateway->form->form_data : (array) $gateway->form->form_data;
                $paymentGuide['form_fields'] = array_values(array_map(function ($field) {
                    $f = is_object($field) ? (array) $field : $field;

                    return [
                        'name' => $f['name'] ?? $f['label'] ?? '',
                        'label' => $f['label'] ?? $f['name'] ?? '',
                        'type' => $f['type'] ?? 'text',
                        'is_required' => ($f['is_required'] ?? 'optional') === 'required',
                        'options' => $f['options'] ?? [],
                        'extensions' => $f['extensions'] ?? '',
                    ];
                }, $formData));
            }

            return response()->json([
                'Result' => 'true',
                'ResponseCode' => '200',
                'ResponseMsg' => 'Payment guide generated',
                'payment_url' => null,
                'trx' => $deposit->trx,
                'final_amount' => $deposit->final_amount,
                'payment_guide' => $paymentGuide,
            ]);
        }

        // Automated gateway: return payment URL for webview
        $paymentUrl = url('/user/deposit/confirm?trx='.$deposit->trx);

        return response()->json([
            'Result' => 'true',
            'ResponseCode' => '200',
            'ResponseMsg' => 'Payment URL generated',
            'payment_url' => $paymentUrl,
            'final_amount' => $deposit->final_amount,
            'trx' => $deposit->trx,
            'gateway_type' => 'automated',
        ]);
    }

    /**
     * Submit manual gateway payment proof from mobile app.
     * POST /api/payment/manual-proof
     */
    public function manualProof(Request $request): JsonResponse
    {
        $request->validate([
            'trx' => 'required|string|max:191',
            'payment_proof' => 'required|file|mimes:jpeg,jpg,png,pdf,webp|max:5120',
            'note' => 'nullable|string|max:1000',
        ]);

        $deposit = Deposit::with(['gateway', 'campaign', 'user'])
            ->where('trx', $request->trx)
            ->whereIn('status', [ManageStatus::PAYMENT_INITIATE, ManageStatus::PAYMENT_PENDING])
            ->first();

        if (! $deposit) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '404',
                'ResponseMsg' => 'Transaction not found',
            ], 404);
        }

        if ((int) $deposit->method_code < 1000) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'This transaction is not a manual gateway payment',
            ], 400);
        }

        $directory = date('Y').'/'.date('m').'/'.date('d');
        $path = getFilePath('verify').'/'.$directory;
        $value = $directory.'/'.fileUploader($request->file('payment_proof'), $path);

        $details = [
            [
                'name' => __('Payment proof'),
                'type' => 'file',
                'value' => $value,
            ],
        ];

        if ($request->filled('note')) {
            $details[] = [
                'name' => __('Note'),
                'type' => 'textarea',
                'value' => (string) $request->note,
            ];
        }

        $deposit->details = $details;
        $deposit->status = ManageStatus::PAYMENT_PENDING;
        $deposit->save();

        $adminNotification = new AdminNotification;
        $adminNotification->user_id = $deposit->user->id ?? 0;
        $adminNotification->title = 'Payment proof submitted — '.($deposit->full_name ?? $deposit->email ?? 'Guest').' — '.($deposit->campaign->name ?? 'Campaign');
        $adminNotification->click_url = urlPath('admin.donations.pending');
        $adminNotification->save();

        return response()->json([
            'Result' => 'true',
            'ResponseCode' => '200',
            'ResponseMsg' => 'Payment proof submitted successfully',
            'trx' => $deposit->trx,
            'status' => 'pending_approval',
        ]);
    }

    /**
     * List authenticated user's transactions for mobile app.
     * GET /api/user_payment_list.php
     */
    public function userPaymentList(Request $request): JsonResponse
    {
        $uid = $this->getUserId($request);
        if (empty($uid)) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '401',
                'ResponseMsg' => 'Unauthenticated! Please provide a valid token.',
            ], 401);
        }

        $limit = max(1, min((int) $request->input('limit', 20), 100));

        $transactions = Transaction::where('user_id', $uid)
            ->with(['deposit'])
            ->orderByDesc('id')
            ->paginate($limit);

        $items = $transactions->getCollection()->map(function (Transaction $transaction) {
            $deposit = $transaction->deposit;
            $isManual = $deposit && (int) $deposit->method_code >= 1000;
            $needsProof = $isManual && (int) $deposit->status === ManageStatus::PAYMENT_INITIATE && ! $this->hasPaymentProof($deposit);

            return [
                'id' => $transaction->id,
                'trx' => $transaction->trx,
                'amount' => (float) $transaction->amount,
                'trx_type' => (string) $transaction->trx_type,
                'post_balance' => (float) $transaction->post_balance,
                'remark' => (string) ($transaction->remark ?? ''),
                'details' => (string) ($transaction->details ?? ''),
                'created_at' => optional($transaction->created_at)->toDateTimeString(),
                'deposit' => $deposit ? [
                    'id' => $deposit->id,
                    'method_code' => (int) $deposit->method_code,
                    'status' => (int) $deposit->status,
                ] : null,
                'can_upload_proof' => $needsProof,
                'proof_submit_endpoint' => $needsProof ? url('/api/user_payment_proof_submit.php') : null,
            ];
        })->values();

        return response()->json([
            'Result' => 'true',
            'ResponseCode' => '200',
            'ResponseMsg' => 'User payment list fetched successfully',
            'payments' => $items,
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    /**
     * List authenticated user's donation/deposit records.
     * GET/POST /api/donation_list.php
     */
    public function donationList(Request $request): JsonResponse
    {
        $uid = $this->getUserId($request);
        if (empty($uid)) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '401',
                'ResponseMsg' => 'Unauthenticated! Please provide a valid token.',
            ], 401);
        }

        $limit = max(1, min((int) $request->input('limit', 20), 100));

        $donations = Deposit::query()
            ->where('user_id', $uid)
            ->with(['campaign:id,name,slug,image', 'gateway:id,code,name'])
            ->orderByDesc('id')
            ->paginate($limit);

        $items = $donations->getCollection()->map(function (Deposit $deposit) {
            $isManualGateway = (int) $deposit->method_code >= 1000;
            $hasProof = $this->hasPaymentProof($deposit);
            $manualProofMissing = $isManualGateway && ! $hasProof;

            return [
                'id' => (int) $deposit->id,
                'trx' => (string) ($deposit->trx ?? ''),
                'amount' => (float) $deposit->amount,
                'charge' => (float) $deposit->charge,
                'final_amount' => (float) $deposit->final_amount,
                'method_code' => (int) $deposit->method_code,
                'method_currency' => (string) ($deposit->method_currency ?? ''),
                'status' => (int) $deposit->status,
                'gateway_name' => $deposit->gateway->name ?? null,
                'gateway_type' => $isManualGateway ? 'manual' : 'automated',
                'is_manual_without_proof' => $manualProofMissing,
                'can_upload_proof' => $manualProofMissing,
                'proof_submit_endpoint' => $manualProofMissing ? url('/api/user_payment_proof_submit.php') : null,
                'campaign' => $deposit->campaign ? [
                    'id' => (int) $deposit->campaign->id,
                    'name' => (string) $deposit->campaign->name,
                    'slug' => (string) $deposit->campaign->slug,
                    'image' => (string) ($deposit->campaign->image ?? ''),
                ] : null,
                'created_at' => optional($deposit->created_at)->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'Result' => 'true',
            'ResponseCode' => '200',
            'ResponseMsg' => 'Donation list fetched successfully',
            'donations' => $items,
            'pagination' => [
                'current_page' => $donations->currentPage(),
                'last_page' => $donations->lastPage(),
                'per_page' => $donations->perPage(),
                'total' => $donations->total(),
            ],
        ]);
    }

    /**
     * List current user's contributions (deposits) with proof flag for manual gateways.
     * GET/POST /api/user_contributions_proof_list.php (Bearer)
     */
    public function contributionsProofList(Request $request): JsonResponse
    {
        $uid = $this->getUserId($request);
        if (empty($uid)) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '401',
                'ResponseMsg' => 'Unauthenticated! Please provide a valid token.',
            ], 401);
        }

        $limit = max(1, min((int) $request->input('limit', 20), 100));

        $user = $request->user();
        $emailNorm = strtolower(trim((string) ($user->email ?? '')));

        $deposits = Deposit::query()
            ->where(function ($q) use ($uid, $emailNorm) {
                $q->where('user_id', $uid);
                if ($emailNorm !== '') {
                    $q->orWhere(function ($q2) use ($emailNorm) {
                        $q2->where('user_id', 0)
                            ->whereRaw('LOWER(TRIM(COALESCE(email, \'\'))) = ?', [$emailNorm]);
                    });
                }
            })
            ->with(['campaign:id,name,slug', 'gateway:id,code,name'])
            ->orderByDesc('id')
            ->paginate($limit);

        $items = $deposits->getCollection()->map(function (Deposit $d) {
            $isManual = (int) $d->method_code >= 1000;

            return [
                'id' => (int) $d->id,
                'trx' => (string) ($d->trx ?? ''),
                'campaign_name' => $d->campaign->name ?? null,
                'campaign_slug' => $d->campaign->slug ?? null,
                'gateway_type' => $isManual ? 'manual' : 'automated',
                'gateway_name' => $d->gateway->name ?? null,
                'amount' => (float) $d->amount,
                'platform_currency' => strtoupper((string) getPlatformCurrency()),
                'status' => (int) $d->status,
                'proof' => $d->proofSubmittedFlag(),
                'can_upload_proof' => $d->needsProofUpload(),
            ];
        })->values();

        return response()->json([
            'Result' => 'true',
            'ResponseCode' => '200',
            'ResponseMsg' => 'Contributions list',
            'contributions' => $items,
            'pagination' => [
                'current_page' => $deposits->currentPage(),
                'last_page' => $deposits->lastPage(),
                'per_page' => $deposits->perPage(),
                'total' => $deposits->total(),
            ],
        ]);
    }

    /**
     * Submit manual payment proof for authenticated user's own transaction.
     * POST /api/user_payment_proof_submit.php
     */
    public function userPaymentProofSubmit(Request $request): JsonResponse
    {
        $uid = $this->getUserId($request);
        if (empty($uid)) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '401',
                'ResponseMsg' => 'Unauthenticated! Please provide a valid token.',
            ], 401);
        }

        $request->validate([
            'trx' => 'required|string|max:191',
            'payment_proof' => 'required|file|mimes:jpeg,jpg,png,pdf,webp|max:5120',
            'note' => 'nullable|string|max:1000',
        ]);

        $userRow = $request->user();
        $emailNorm = strtolower(trim((string) ($userRow->email ?? '')));

        $deposit = Deposit::with(['gateway', 'campaign', 'user'])
            ->where('trx', $request->trx)
            ->where('status', ManageStatus::PAYMENT_INITIATE)
            ->where(function ($q) use ($uid, $emailNorm) {
                $q->where('user_id', $uid);
                if ($emailNorm !== '') {
                    $q->orWhere(function ($q2) use ($emailNorm) {
                        $q2->where('user_id', 0)
                            ->whereRaw('LOWER(TRIM(COALESCE(email, \'\'))) = ?', [$emailNorm]);
                    });
                }
            })
            ->first();

        if (! $deposit) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '404',
                'ResponseMsg' => 'Payment transaction not found or proof already submitted.',
            ], 404);
        }

        if ((int) $deposit->method_code < 1000) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'This transaction is not a manual gateway payment.',
            ], 400);
        }

        $directory = date('Y').'/'.date('m').'/'.date('d');
        $path = getFilePath('verify').'/'.$directory;
        $value = $directory.'/'.fileUploader($request->file('payment_proof'), $path);

        $details = [
            [
                'name' => __('Payment proof'),
                'type' => 'file',
                'value' => $value,
            ],
        ];

        if ($request->filled('note')) {
            $details[] = [
                'name' => __('Note'),
                'type' => 'textarea',
                'value' => (string) $request->note,
            ];
        }

        $deposit->details = $details;
        $deposit->status = ManageStatus::PAYMENT_PENDING;
        if ((int) $deposit->user_id === 0 && $uid > 0) {
            $deposit->user_id = $uid;
        }
        $deposit->save();

        $adminNotification = new AdminNotification;
        $adminNotification->user_id = $deposit->user->id ?? 0;
        $adminNotification->title = 'Payment proof submitted — '.($deposit->full_name ?? $deposit->email ?? 'Guest').' — '.($deposit->campaign->name ?? 'Campaign');
        $adminNotification->click_url = urlPath('admin.donations.pending');
        $adminNotification->save();

        return response()->json([
            'Result' => 'true',
            'ResponseCode' => '200',
            'ResponseMsg' => 'Payment proof submitted successfully',
            'trx' => $deposit->trx,
            'status' => 'pending_approval',
        ]);
    }

    protected function hasPaymentProof(Deposit $deposit): bool
    {
        $details = $deposit->details;
        if (is_string($details)) {
            $decoded = json_decode($details, true);
            $details = is_array($decoded) ? $decoded : [];
        } elseif (is_object($details)) {
            $details = (array) $details;
        }

        if (! is_array($details)) {
            return false;
        }

        foreach ($details as $entry) {
            $row = is_object($entry) ? (array) $entry : (array) $entry;
            $name = strtolower(trim((string) ($row['name'] ?? '')));
            $type = strtolower(trim((string) ($row['type'] ?? '')));
            $value = trim((string) ($row['value'] ?? ''));
            if (($name === 'payment proof' || $type === 'file') && $value !== '') {
                return true;
            }
        }

        return false;
    }
}
