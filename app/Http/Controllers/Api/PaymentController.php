<?php

namespace App\Http\Controllers\Api;

use App\Models\Campaign;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\AdminNotification;
use App\Models\GatewayCurrency;
use App\Constants\ManageStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends BaseApiController
{
    /**
     * Get Gateways List (with currencies, min/max amount)
     * GET /api/gateways
     * Query: ?country=Pakistan (optional)
     */
    public function gateways(Request $request): JsonResponse
    {
        $country = $request->query('country', '');

        $query = Gateway::active()
            ->with(['currencies' => function ($q) {
                $q->where('status', 1)->select('id', 'method_code', 'currency', 'symbol', 'min_amount', 'max_amount');
            }]);

        if (!empty($country)) {
            $query->forCountry($country);
        }

        $gateways = $query->orderBy('id')->get()->map(function ($g) {
            $currencies = $g->currencies->map(function ($c) {
                return [
                    'currency'   => $c->currency,
                    'symbol'     => $c->symbol ?? $c->currency,
                    'min_amount' => (float) $c->min_amount,
                    'max_amount' => (float) $c->max_amount,
                ];
            })->values()->all();
            return [
                'id'         => $g->id,
                'code'       => $g->code,
                'name'       => $g->name,
                'alias'      => $g->alias ?? $g->name,
                'currencies' => $currencies,
            ];
        });

        return response()->json([
            'ResponseCode' => '200',
            'Result'       => 'true',
            'ResponseMsg'  => 'Gateways list.',
            'gateways'     => $gateways,
        ]);
    }

    /**
     * Get Payment Gateway List
     */
    public function paymentGatewayList(Request $request): JsonResponse
    {
        // Get active payment gateways (using gateways table)
        // Status is boolean: 1 = active, 0 = inactive
        $sel = $this->h->queryfire("select id, code, name, alias, status from gateways where status = 1 order by id asc");
        
        if (!$sel) {
            return response()->json([
                "paymentdata" => [],
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Payment Gateway List Founded!"
            ]);
        }
        
        $myarray = array();
        if ($sel->num_rows > 0) {
        while ($row = $sel->fetch_assoc()) {
                if (!$row) break;
            $myarray[] = $row;
            }
        }

        return response()->json([
            "paymentdata" => $myarray,
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Payment Gateway List Founded!"
        ]);
    }

    /**
     * Get Payment Webview URL for mobile apps
     * POST /api/payment/webview-url
     * Body: gateway_id (int) OR gateway (string code), amount, campaign_id (int), full_name, email, country, currency, phone (optional)
     */
    public function webviewUrl(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        // Normalize: campaign_id can be string "118", int, or decimal (298.8) – round to int
        if (isset($data['campaign_id'])) {
            $data['campaign_id'] = (int) round((float) $data['campaign_id']);
        }

        $gatewayCode = null;
        $gatewayId = $data['gateway_id'] ?? $data['gateway'] ?? null;
        // Support: gateway_id (int), gateway as id (numeric), or gateway as code (string)
        if (!empty($gatewayId) && is_numeric($gatewayId)) {
            $gateway = Gateway::active()->find((int) $gatewayId);
            if (!$gateway) {
                // Try by code (e.g. "114" is Stripe's code, not id)
                $gateway = Gateway::active()->where('code', (string) $gatewayId)->first();
            }
            if (!$gateway) {
                return response()->json([
                    'Result' => 'false',
                    'ResponseCode' => '400',
                    'ResponseMsg' => 'Gateway not found',
                ], 400);
            }
            $gatewayCode = $gateway->code;
        } elseif (!empty($data['gateway'])) {
            $gatewayCode = (string) $data['gateway'];
        } else {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'gateway_id or gateway is required',
            ], 400);
        }

        $validator = \Validator::make($data, [
            'amount'       => 'required|numeric|gt:0',
            'full_name'    => 'required|string|max:255',
            'email'        => 'required|email|max:40',
            'currency'     => 'nullable|string|max:10',
            'campaign_id'  => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => $validator->errors()->first(),
            ], 400);
        }

        $campaign = Campaign::where('id', $data['campaign_id'])->approve()->first();
        if (!$campaign) {
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

        $userCurrency = !empty($data['currency']) ? strtoupper(trim($data['currency'])) : null;

        // If currency not provided, use gateway's first/default currency (fallback: PKR for Pakistan)
        if (!$userCurrency) {
            $defaultCurrency = GatewayCurrency::whereHas('method', function ($q) {
                $q->active();
            })->where('method_code', $gatewayCode)
              ->where('status', 1)
              ->orderBy('id')
              ->value('currency');
            $userCurrency = $defaultCurrency ? strtoupper($defaultCurrency) : 'PKR';
        }

        if (!$userCurrency) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'Currency is required. Gateway has no default currency.',
            ], 400);
        }

        // 1. Try direct currency match (PKR gateway for PKR, USD gateway for USD)
        $currencyMatch = [$userCurrency];
        if ($userCurrency === 'PKR') {
            $currencyMatch[] = '0'; // JazzCash etc use "0" for PKR
        }

        $gatewayData = GatewayCurrency::whereHas('method', function ($q) {
            $q->active();
        })->where('method_code', $gatewayCode)
          ->whereIn('currency', $currencyMatch)
          ->where('status', 1)
          ->first();

        // 2. If no direct match, try gateway with input_currency_rates (e.g. Stripe USD accepts PKR via conversion)
        $convertedFromUserCurrency = false;
        if (!$gatewayData) {
            $gatewayData = GatewayCurrency::whereHas('method', function ($q) {
                $q->active();
            })->where('method_code', $gatewayCode)
              ->where('status', 1)
              ->get()
              ->first(function ($gc) use ($userCurrency) {
                  $rates = $gc->input_currency_rates ?? [];
                  return !empty($rates) && isset($rates[$userCurrency]) && (float) ($rates[$userCurrency] ?? 0) > 0;
              });
            $convertedFromUserCurrency = (bool) $gatewayData;
        }

        if (!$gatewayData) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'Invalid gateway or gateway not available for this currency',
            ], 400);
        }

        $amountUser = (float) $data['amount'];

        if ($convertedFromUserCurrency) {
            // Convert user amount to gateway currency
            $conversionRate = (float) ($gatewayData->input_currency_rates[$userCurrency] ?? 0);
            $amount = $amountUser * $conversionRate;
            if ($amount <= 0) {
                return response()->json([
                    'Result' => 'false',
                    'ResponseCode' => '400',
                    'ResponseMsg' => 'Invalid conversion rate for ' . $userCurrency,
                ], 400);
            }
        } else {
            $amount = $amountUser;
        }

        if ($gatewayData->min_amount > $amount || $gatewayData->max_amount < $amount) {
            return response()->json([
                'Result' => 'false',
                'ResponseCode' => '400',
                'ResponseMsg' => 'Amount must be between ' . $gatewayData->min_amount . ' and ' . $gatewayData->max_amount . ' ' . $gatewayData->currency,
            ], 400);
        }

        $charge = $gatewayData->fixed_charge + (($amount * $gatewayData->percent_charge) / 100);
        $payable = $amount + $charge;
        // Amount is in gateway currency (direct match or converted); final_amount = payable
        $finalAmount = $payable;

        $deposit = new Deposit();
        $deposit->campaign_id = $campaign->id;
        $deposit->user_id = 0;
        $deposit->donor_type = ManageStatus::KNOWN_DONOR;
        $deposit->full_name = $data['full_name'];
        $deposit->email = $data['email'];
        $country = !empty($data['country']) ? trim($data['country']) : (getUserCountryByIP() ?? 'Pakistan');
        $deposit->phone = formatPhoneForStorage($data['phone'] ?? '', $country);
        $deposit->country = $country;
        $deposit->receiver_id = $campaign->user->id;
        $deposit->reward_id = null;
        $deposit->method_code = $gatewayData->method_code;
        $deposit->amount = $amount;
        $deposit->method_currency = strtoupper($gatewayData->currency);
        $deposit->charge = $charge;
        $deposit->rate = $convertedFromUserCurrency
            ? (float) ($gatewayData->input_currency_rates[$userCurrency] ?? 1)
            : $gatewayData->rate;
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
                'gateway_type'   => 'manual',
                'gateway_name'   => $gateway->name ?? $gatewayData->name,
                'guideline'      => $gateway->guideline ?? '',
                'amount'         => (float) $deposit->amount,
                'final_amount'   => (float) $deposit->final_amount,
                'currency'       => $deposit->method_currency,
                'charge'         => (float) $deposit->charge,
                'trx'            => $deposit->trx,
                'form_fields'    => [],
            ];

            if ($gateway->form && $gateway->form->form_data) {
                $formData = is_array($gateway->form->form_data) ? $gateway->form->form_data : (array) $gateway->form->form_data;
                $paymentGuide['form_fields'] = array_values(array_map(function ($field) {
                    $f = is_object($field) ? (array) $field : $field;
                    return [
                        'name'        => $f['name'] ?? $f['label'] ?? '',
                        'label'       => $f['label'] ?? $f['name'] ?? '',
                        'type'        => $f['type'] ?? 'text',
                        'is_required' => ($f['is_required'] ?? 'optional') === 'required',
                        'options'     => $f['options'] ?? [],
                        'extensions'  => $f['extensions'] ?? '',
                    ];
                }, $formData));
            }

            return response()->json([
                'Result'        => 'true',
                'ResponseCode'  => '200',
                'ResponseMsg'   => 'Payment guide generated',
                'payment_url'   => null,
                'trx'           => $deposit->trx,
                'payment_guide' => $paymentGuide,
            ]);
        }

        // Automated gateway: return payment URL for webview
        $paymentUrl = url('/user/deposit/confirm?trx=' . $deposit->trx);

        return response()->json([
            'Result'       => 'true',
            'ResponseCode' => '200',
            'ResponseMsg'  => 'Payment URL generated',
            'payment_url'  => $paymentUrl,
            'trx'          => $deposit->trx,
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
            'trx'           => 'required|string|max:191',
            'payment_proof' => 'required|file|mimes:jpeg,jpg,png,pdf,webp|max:5120',
            'note'          => 'nullable|string|max:1000',
        ]);

        $deposit = Deposit::with(['gateway', 'campaign', 'user'])
            ->where('trx', $request->trx)
            ->whereIn('status', [ManageStatus::PAYMENT_INITIATE, ManageStatus::PAYMENT_PENDING])
            ->first();

        if (!$deposit) {
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

        $directory = date('Y') . '/' . date('m') . '/' . date('d');
        $path = getFilePath('verify') . '/' . $directory;
        $value = $directory . '/' . fileUploader($request->file('payment_proof'), $path);

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
                'value' => (string) $request->note,
            ];
        }

        $deposit->details = $details;
        $deposit->status = ManageStatus::PAYMENT_PENDING;
        $deposit->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $deposit->user->id ?? 0;
        $adminNotification->title = 'Payment proof submitted — ' . ($deposit->full_name ?? $deposit->email ?? 'Guest') . ' — ' . ($deposit->campaign->name ?? 'Campaign');
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
}

