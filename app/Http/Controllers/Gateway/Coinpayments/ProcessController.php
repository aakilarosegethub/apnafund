<?php

namespace App\Http\Controllers\Gateway\Coinpayments;

use Exception;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Gateway\Coinpayments\CoinPaymentHosted;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $coinPayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        if ($deposit->btc_amount == 0 || $deposit->btc_wallet == "") {
            try {
                $cps = new CoinPaymentHosted();
            } catch (Exception $e) {
                $send['error']   = true;
                $send['message'] = $e->getMessage();

                return json_encode($send);
            }

            $cps->Setup($coinPayAcc->private_key, $coinPayAcc->public_key);
            $callbackUrl = route('ipn.' . $deposit->gateway->alias);

            $req = array(
                'amount'      => $deposit->final_amount,
                'currency1'   => 'USD',
                'currency2'   => $deposit->method_currency,
                'custom'      => $deposit->trx,
                'buyer_email' => $deposit->user_id ? $deposit->user->email : $deposit->email,
                'ipn_url'     => $callbackUrl,
            );

            $result = $cps->CreateTransaction($req);

            if ($result['error'] == 'ok') {
                $bCoin                 = sprintf('%.08f', $result['result']['amount']);
                $sendAdd               = $result['result']['address'];
                $deposit['btc_amount'] = $bCoin;
                $deposit['btc_wallet'] = $sendAdd;
                $deposit->update();
            } else {
                $send['error']   = true;
                $send['message'] = $result['error'];
            }
        }

        $send['amount']   = $deposit->btc_amount;
        $send['sendTo']   = $deposit->btc_wallet;
        $send['img']      = cryptoQR($deposit->btc_wallet);
        $send['currency'] = "$deposit->method_currency";
        $send['view']     = 'user.payment.crypto';

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $track   = $request->custom;
        $status  = $request->status;
        $amount2 = floatval($request->amount2);
        $deposit = Deposit::where('trx', $track)->first();

        if ($status >= 100 || $status == 2) {
            $coinPayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

            if (
                $deposit->method_currency == $request->currency2 && 
                $deposit->btc_amount <= $amount2 && 
                $coinPayAcc->merchant_id == $request->merchant && 
                $deposit->status == ManageStatus::PAYMENT_INITIATE
            ) {
                PaymentController::campaignDataUpdate($deposit);
            }
        }
    }
}
