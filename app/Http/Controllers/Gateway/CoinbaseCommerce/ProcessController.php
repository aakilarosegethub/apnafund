<?php

namespace App\Http\Controllers\Gateway\CoinbaseCommerce;

use App\Models\Deposit;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $coinbaseAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $url         = 'https://api.commerce.coinbase.com/charges';
        $array       = [
            'name'         => $deposit->user_id ? $deposit->user->fullname : $deposit->full_name,
            'description'  => "Pay to " . bs('site_name'),
            'local_price'  => [
                'amount'   => $deposit->final_amount,
                'currency' => $deposit->method_currency,
            ],
            'metadata'     => [
                'trx' => $deposit->trx,
            ],
            'pricing_type' => "fixed_price",
            'redirect_url' => route(gatewayRedirectUrl(true)),
            'cancel_url'   => route(gatewayRedirectUrl()),
        ];

        $jsonData = json_encode($array);
        $ch       = curl_init();
        $apiKey   = $coinbaseAcc->api_key;
        $header   = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'X-CC-Api-Key: ' . "$apiKey";
        $header[] = 'X-CC-Version: 2018-03-22';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        if (@$result->error == '') {
            $send['redirect']     = true;
            $send['redirect_url'] = $result->data->hosted_url;
        } else {
            $send['error']   = true;
            $send['message'] = 'Some problem ocurred with api.';
        }

        $send['view'] = '';

        return json_encode($send);
    }

    public function ipn()
    {
        $postData    = file_get_contents("php://input");
        $res         = json_decode($postData);
        $deposit     = Deposit::where('trx', $res->event->data->metadata->trx)->first();
        $coinbaseAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $headers     = apache_request_headers();
        $headers     = json_decode(json_encode($headers), true);
        $sentSign    = $headers['X-Cc-Webhook-Signature'];
        $sig         = hash_hmac('sha256', $postData, $coinbaseAcc->secret);

        if ($sentSign == $sig) {
            if ($res->event->type == 'charge:confirmed' && $deposit->status == ManageStatus::PAYMENT_INITIATE) {
                PaymentController::campaignDataUpdate($deposit);
            }
        }
    }
}
