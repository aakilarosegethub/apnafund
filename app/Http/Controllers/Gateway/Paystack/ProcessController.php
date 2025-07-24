<?php

namespace App\Http\Controllers\Gateway\Paystack;

use App\Constants\ManageStatus;
use App\Models\Deposit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $paystackAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $alias       = $deposit->gateway->alias;

        $send['key']      = $paystackAcc->public_key;
        $send['email']    = $deposit->user_id ? $deposit->user->email : $deposit->email;
        $send['amount']   = $deposit->final_amount * 100;
        $send['currency'] = $deposit->method_currency;
        $send['ref']      = $deposit->trx;
        $send['view']     = 'user.payment.' . $alias;

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $request->validate([
            'reference'       => 'required',
            'paystack-trxref' => 'required',
        ]);

        $track       = $request->reference;
        $deposit     = Deposit::where('trx', $track)->first();
        $paystackAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $secret_key  = $paystackAcc->secret_key;
        $result      = array();

        $url      = 'https://api.paystack.co/transaction/verify/' . $track;
        $ch       = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $secret_key]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);

            if ($result) {
                if ($result['data']) {
                    $deposit->details = $result['data'];
                    $deposit->save();

                    if ($result['data']['status'] == 'success') {
                        $am  = $result['data']['amount'] / 100;
                        $sam = round($deposit->final_amount, 2);

                        if ($am == $sam && $result['data']['currency'] == $deposit->method_currency && $deposit->status == ManageStatus::PAYMENT_INITIATE) {
                            PaymentController::campaignDataUpdate($deposit);
                            $toast[] = ['success', 'Payment completed successfully'];

                            return to_route(gatewayRedirectUrl(true))->withToasts($toast);
                        } else {
                            $toast[] = ['error', 'Less amount paid. Please contact with admin.'];
                        }
                    } else {
                        $toast[] = ['error', $result['data']['gateway_response']];
                    }
                } else {
                    $toast[] = ['error', $result['message']];
                }
            } else {
                $toast[] = ['error', 'Something went wrong while executing'];
            }
        } else {
            $toast[] = ['error', 'Something went wrong while executing'];
        }

        return to_route(gatewayRedirectUrl())->withToasts($toast);
    }
}
