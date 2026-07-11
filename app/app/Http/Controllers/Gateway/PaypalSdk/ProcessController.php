<?php

namespace App\Http\Controllers\Gateway\PaypalSdk;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Gateway\PaypalSdk\Core\PayPalHttpClient;
use App\Http\Controllers\Gateway\PaypalSdk\Core\ProductionEnvironment;
use App\Http\Controllers\Gateway\PaypalSdk\Core\SandboxEnvironment;
use App\Http\Controllers\Gateway\PaypalSdk\Orders\OrdersCaptureRequest;
use App\Http\Controllers\Gateway\PaypalSdk\Orders\OrdersCreateRequest;
use App\Http\Controllers\Gateway\PaypalSdk\PayPalHttp\HttpException;
use App\Models\Deposit;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $amount = round($deposit->final_amount, 2);
        if ($amount <= 0) {
            return json_encode([
                'error' => true,
                'message' => __('Invalid amount. Payment amount must be greater than zero.'),
            ]);
        }

        $paypalAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        // Creating an environment (Sandbox for testing, Production for live)
        $clientId = $paypalAcc->clientId;
        $clientSecret = $paypalAcc->clientSecret;
        $useSandbox = ! empty($paypalAcc->sandbox);
        $environment = $useSandbox
            ? new SandboxEnvironment($clientId, $clientSecret)
            : new ProductionEnvironment($clientId, $clientSecret);
        $client = new PayPalHttpClient($environment);
        $request = new OrdersCreateRequest;
        $request->prefer('return=representation');

        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $deposit->trx,
                    'amount' => [
                        'value' => round($deposit->final_amount, 2),
                        'currency_code' => $deposit->method_currency,
                    ],
                ],
            ],
            'application_context' => [
                'cancel_url' => gatewayRedirectUrlFull(false),
                'return_url' => route('ipn.'.$deposit->gateway->alias),
            ],
        ];

        try {
            $response = $client->execute($request);
            $deposit->btc_wallet = $response->result->id;
            $deposit->save();

            $send['redirect'] = true;
            $send['redirect_url'] = $response->result->links[1]->href;
        } catch (HttpException $ex) {
            $send['error'] = true;

            $responseBody = $ex->getMessage();
            $statusCode = $ex->statusCode ?? 0;

            // Parse PayPal error JSON for readable message
            $paypalError = null;
            if ($responseBody) {
                $decoded = @json_decode($responseBody, true);
                if (isset($decoded['details'][0])) {
                    $d = $decoded['details'][0];
                    $paypalError = ($d['issue'] ?? '').' – '.($d['description'] ?? '');
                } elseif (isset($decoded['message'])) {
                    $paypalError = $decoded['message'];
                } elseif (isset($decoded['error_description'])) {
                    $paypalError = $decoded['error_description'];
                }
            }
            $send['message'] = $paypalError ?? 'Payment failed';

            $logContext = [
                'api_hit' => 'PayPal Orders API – Create Order (v2/checkout/orders)',
                'http_status' => $statusCode,
                'trx' => $deposit->trx ?? null,
                'amount' => $deposit->final_amount ?? null,
                'currency' => $deposit->method_currency ?? null,
                'paypal_message' => $paypalError,
                'raw_response' => $responseBody,
            ];

            \Log::channel('paypal')->error('PayPal API failed', $logContext);
            \Log::warning('PayPal API error on process', $logContext);
        }

        return json_encode($send);
    }

    public function ipn()
    {
        $request = new OrdersCaptureRequest($_GET['token']);
        $request->prefer('return=representation');

        try {
            $deposit = Deposit::where('btc_wallet', $_GET['token'])->initiate()->first();
            $paypalAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
            $clientId = $paypalAcc->clientId;
            $clientSecret = $paypalAcc->clientSecret;
            $useSandbox = ! empty($paypalAcc->sandbox);
            $environment = $useSandbox
                ? new SandboxEnvironment($clientId, $clientSecret)
                : new ProductionEnvironment($clientId, $clientSecret);
            $client = new PayPalHttpClient($environment);
            $response = $client->execute($request);

            if (@$response->result->status == 'COMPLETED') {
                $deposit->details = json_decode(json_encode($response->result->payer));
                $deposit->save();

                PaymentController::campaignDataUpdate($deposit);
                $toast[] = ['success', 'Payment completed successfully'];

                return redirect()->to(gatewayRedirectUrlFull(true))->withToasts($toast);
            } else {
                $toast[] = ['error', 'Payment captured failed'];

                return redirect()->to(gatewayRedirectUrlFull(false))->withToasts($toast);
            }
        } catch (HttpException $ex) {
            $toast[] = ['error', __('Payment failed or was cancelled. Please try again.')];

            return redirect()->to(gatewayRedirectUrlFull(false))->withToasts($toast);
        }
    }
}
