<?php

namespace App\Http\Controllers\Gateway\CustomGateway;

use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $setting = bs();
        
        // Get configuration parameters
        $merchantId = $gatewayAcc->merchant_id;
        $password = $gatewayAcc->password;
        $hashKey = $gatewayAcc->hash_key;
        $returnUrl = $gatewayAcc->return_url;
        $sandbox = $gatewayAcc->sandbox ?? false;
        
        // Prepare payment data
        $paymentData = [
            'MerchantID' => $merchantId,
            'Password' => $password,
            'HashKey' => $hashKey,
            'ReturnURL' => $returnUrl,
            'Amount' => number_format($deposit->final_amount, 2, '.', ''),
            'Currency' => $deposit->method_currency,
            'TransactionID' => $deposit->trx,
            'Description' => "Donation to " . $setting->site_name,
            'CustomerName' => $deposit->user_id ? $deposit->user->fullname : $deposit->full_name,
            'CustomerEmail' => $deposit->user_id ? $deposit->user->email : $deposit->email,
            'Sandbox' => $sandbox ? '1' : '0'
        ];
        
        // Generate hash for security
        $hashString = $merchantId . $deposit->trx . $paymentData['Amount'] . $deposit->method_currency . $hashKey;
        $paymentData['Hash'] = hash('sha256', $hashString);
        
        // Determine gateway URL based on sandbox mode
        $gatewayUrl = $sandbox ? 'https://sandbox.yourgateway.com/payment' : 'https://api.yourgateway.com/payment';
        
        $send['val'] = $paymentData;
        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
        $send['url'] = $gatewayUrl;

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $deposit = Deposit::where('trx', $request->TransactionID)->first();
        
        if (!$deposit) {
            return response('Transaction not found', 404);
        }
        
        if ($deposit->status == ManageStatus::PAYMENT_SUCCESS) {
            return response('Already processed', 200);
        }
        
        // Get gateway configuration
        $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $merchantId = $gatewayAcc->merchant_id;
        $hashKey = $gatewayAcc->hash_key;
        $sandbox = $gatewayAcc->sandbox ?? false;
        
        // Verify the hash
        $expectedHash = hash('sha256', $merchantId . $request->TransactionID . $request->Amount . $request->Currency . $hashKey);
        
        if ($expectedHash !== $request->Hash) {
            return response('Invalid hash', 400);
        }
        
        // Verify payment status
        if ($request->Status === 'Success' || $request->Status === 'Completed') {
            PaymentController::campaignDataUpdate($deposit);
            return response('Payment processed successfully', 200);
        }
        
        return response('Payment failed', 400);
    }
}
