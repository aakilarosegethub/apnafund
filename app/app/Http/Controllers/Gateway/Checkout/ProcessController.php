<?php

namespace App\Http\Controllers\Gateway\Checkout;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Lib\CurlRequest;
use App\Models\Deposit;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $alias = $deposit->gateway->alias;
        $send['track'] = $deposit->trx;
        $send['view'] = 'user.payment.'.$alias;
        $send['method'] = 'post';
        $send['url'] = route('ipn.'.$alias);

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $track = $request->track;
        $deposit = Deposit::where('trx', $track)->first();

        if ($deposit->status == ManageStatus::PAYMENT_SUCCESS) {
            $toast[] = ['error', 'Invalid request.'];

            return redirect()->to(gatewayRedirectUrlFull(false))->withToasts($toast);
        }

        $this->validate($request, [
            'cardNumber' => 'required',
            'cardExpiry' => 'required',
            'cardCVC' => 'required',
        ]);

        $checkoutAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $processingChannelId = $checkoutAcc->processing_channel_id;
        $publicKey = $checkoutAcc->public_key;
        $secretKey = $checkoutAcc->secret_key;
        $cardExpire = explode('/', $request->cardExpiry);
        $data = [
            'type' => 'card',
            'number' => str_replace(' ', '', $request->cardNumber),
            'expiry_month' => $cardExpire[0],
            'expiry_year' => $cardExpire[1],
            'cvv' => $request->cardCVC,
        ];

        $tokenUrl = 'https://api.checkout.com/tokens';
        $paymentUrl = 'https://api.checkout.com/payments';

        $response = CurlRequest::curlPostContent($tokenUrl, json_encode($data), [
            'Content-Type: application/json',
            'Authorization: '.$publicKey,
        ]);

        $response = json_decode($response);

        if (@$response->token) {
            $cardToken = $response->token;
        } else {
            $toast = [];

            foreach ($response->error_codes ?? [] as $error) {
                $toast[] = ['error', $error];
            }

            if (empty($toast)) {
                $toast[] = ['error', 'Something went wrong'];
            }

            return redirect()->to(gatewayRedirectUrlFull(false))->withToasts($toast);
        }

        $data = [
            'source' => [
                'type' => 'token',
                'token' => $cardToken,
            ],
            'amount' => (int) (round($deposit->final_amount, 2) * 100),
            'currency' => $deposit->method_currency,
            'processing_channel_id' => $processingChannelId,
            'reference' => $deposit->trx,
            'capture' => true,
            'customer' => [
                'email' => $deposit->user_id ? $deposit->user->email : $deposit->email,
                'name' => $deposit->user_id ? $deposit->user->fullname : $deposit->full_name,
                'phone' => [
                    'number' => $deposit->user_id ? $deposit->user->mobile : $deposit->phone,
                ],
            ],
        ];

        $data = json_encode($data);
        $result = CurlRequest::curlPostContent($paymentUrl, $data, [
            "Authorization: Bearer $secretKey",
            'Content-Type: application/json',
            'Content-Length: '.strlen($data),
        ]
        );

        $result = json_decode($result);

        if (@$result->status == 'Authorized' || @$result->status == 'Captured') {
            PaymentController::campaignDataUpdate($deposit);
            $toast[] = ['success', 'Payment completed successfully'];

            return redirect()->to(gatewayRedirectUrlFull(true))->withToasts($toast);
        }

        $toast[] = ['error', 'Payment failed'];

        return redirect()->to(gatewayRedirectUrlFull(false))->withToasts($toast);
    }
}
