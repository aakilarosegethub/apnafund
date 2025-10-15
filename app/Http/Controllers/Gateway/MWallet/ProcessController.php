<?php

namespace App\Http\Controllers\Gateway\MWallet;

use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Lib\CurlRequest;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $setting = bs();
        
        // Get configuration parameters
        $merchantId = $gatewayAcc->merchant_id;
        $apiKey = $gatewayAcc->api_key;
        $secretKey = $gatewayAcc->secret_key;
        $sandbox = $gatewayAcc->sandbox ?? false;
        
        // Determine API endpoints based on sandbox mode
        $baseUrl = $sandbox ? 'https://sandbox.mwallet.com.pk/api/v2' : 'https://api.mwallet.com.pk/api/v2';
        
        // Prepare payment data
        $paymentData = [
            'merchant_id' => $merchantId,
            'amount' => number_format($deposit->final_amount, 2, '.', ''),
            'currency' => $deposit->method_currency,
            'order_id' => $deposit->trx,
            'description' => "Donation to " . $setting->site_name,
            'customer_name' => $deposit->user_id && $deposit->user ? $deposit->user->fullname : $deposit->full_name,
            'customer_email' => $deposit->user_id && $deposit->user ? $deposit->user->email : $deposit->email,
            'customer_phone' => $deposit->user_id && $deposit->user ? $deposit->user->mobile : $deposit->phone,
            'return_url' => route('user.deposit.index'),
            'cancel_url' => route('user.deposit.index'),
            'webhook_url' => route('ipn.MWallet'),
            'timestamp' => time()
        ];
        
        // Generate signature for security
        $signatureString = $merchantId . $paymentData['amount'] . $paymentData['currency'] . $paymentData['order_id'] . $paymentData['timestamp'] . $secretKey;
        $paymentData['signature'] = hash('sha256', $signatureString);
        
        // Add API key to headers
        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        
        // Make API call to create payment
        $curl = new CurlRequest();
        $response = $curl->curlPostContent($baseUrl . '/payment/create', $paymentData, $headers);
        $responseData = json_decode($response, true);
        
        if (isset($responseData['status']) && $responseData['status'] === 'success') {
            $send['val'] = [
                'payment_url' => $responseData['data']['payment_url'],
                'order_id' => $deposit->trx,
                'amount' => $deposit->final_amount,
                'currency' => $deposit->method_currency
            ];
            $send['view'] = 'user.payment.redirect';
            $send['method'] = 'get';
            $send['url'] = $responseData['data']['payment_url'];
        } else {
            $send['error'] = true;
            $send['message'] = $responseData['message'] ?? 'Payment initialization failed';
        }

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $deposit = Deposit::where('trx', $request->order_id)->first();
        
        if (!$deposit) {
            return response('Transaction not found', 404);
        }
        
        if ($deposit->status == ManageStatus::PAYMENT_SUCCESS) {
            return response('Already processed', 200);
        }
        
        // Get gateway configuration
        $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $merchantId = $gatewayAcc->merchant_id;
        $secretKey = $gatewayAcc->secret_key;
        $sandbox = $gatewayAcc->sandbox ?? false;
        
        // Verify the signature
        $expectedSignature = hash('sha256', $merchantId . $request->amount . $request->currency . $request->order_id . $request->timestamp . $secretKey);
        
        if ($expectedSignature !== $request->signature) {
            return response('Invalid signature', 400);
        }
        
        // Verify payment status
        if ($request->status === 'success' || $request->status === 'completed') {
            PaymentController::campaignDataUpdate($deposit);
            return response('Payment processed successfully', 200);
        }
        
        return response('Payment failed', 400);
    }
}


