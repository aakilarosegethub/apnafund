<?php

namespace App\Http\Controllers\Gateway\Razorpay;

use Exception;
use Razorpay\Api\Api;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $razorAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        //  API request and response for creating an order
        $api_key    = $razorAcc->key_id;
        $api_secret = $razorAcc->key_secret;

        try {
            $api   = new Api($api_key, $api_secret);
            $order = $api->order->create(
                array(
                    'receipt'         => $deposit->trx,
                    'amount'          => round($deposit->final_amount * 100),
                    'currency'        => $deposit->method_currency,
                    'payment_capture' => '0'
                )
            );
        } catch (Exception $e) {
            $send['error']   = true;
            $send['message'] = $e->getMessage();

            return json_encode($send);
        }

        $deposit->btc_wallet = $order->id;
        $deposit->save();

        $val['key']             = $razorAcc->key_id;
        $val['amount']          = round($deposit->final_amount * 100);
        $val['currency']        = $deposit->method_currency;
        $val['order_id']        = $order['id'];
        $val['buttontext']      = "Pay with Razorpay";
        $val['name']            = $deposit->user_id ? $deposit->user->fullname : $deposit->full_name;
        $val['description']     = "Payment By Razorpay";
        $val['image']           = getImage(getFilePath('logoFavicon') . '/logo_dark.png');
        $val['prefill.name']    = $deposit->user_id ? $deposit->user->fullname : $deposit->full_name;
        $val['prefill.email']   = $deposit->user_id ? $deposit->user->email : $deposit->email;
        $val['prefill.contact'] = $deposit->user_id ? $deposit->user->mobile : $deposit->phone;
        $val['theme.color']     = "#47d195";
        $send['val']            = $val;
        $send['method']         = 'POST';

        $alias = $deposit->gateway->alias;

        $send['url']         = route('ipn.' . $alias);
        $send['custom']      = $deposit->trx;
        $send['checkout_js'] = "https://checkout.razorpay.com/v1/checkout.js";
        $send['view']        = 'user.payment.' . $alias;

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $deposit  = Deposit::where('btc_wallet', $request->razorpay_order_id)->first();
        $razorAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        if (!$deposit) $toast[] = ['error', 'Invalid request'];

        $sig              = hash_hmac('sha256', $request->razorpay_order_id . "|" . $request->razorpay_payment_id, $razorAcc->key_secret);
        $deposit->details = $request->all();
        $deposit->save();

        if ($sig == $request->razorpay_signature && $deposit->status == ManageStatus::PAYMENT_INITIATE) {
            PaymentController::campaignDataUpdate($deposit);
            $toast[] = ['success', 'Payment completed successfully'];

            return to_route(gatewayRedirectUrl(true))->withToasts($toast);
        } else {
            $toast[] = ['error', "Invalid Request"];

            return to_route(gatewayRedirectUrl())->withToasts($toast);
        }
    }
}
