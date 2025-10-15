<?php

namespace App\Http\Controllers\Gateway\JazzCashWallet;

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
        
        // Get JazzCash Wallet configuration parameters
        $merchantId = $gatewayAcc->merchant_id;
        $password = $gatewayAcc->password;
        $integritySalt = $gatewayAcc->integrity_salt;
        $sandbox = $gatewayAcc->sandbox ?? false;
        
        // Determine API endpoints based on sandbox mode
        $baseUrl = $sandbox ? 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction' : 'https://jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';
        
        // Generate transaction datetime and expiry (Pakistan Time)
        date_default_timezone_set("Asia/Karachi");
        $pp_TxnDateTime = date('YmdHis');
        $pp_TxnExpiryDateTime = date('YmdHis', strtotime('+1 day'));
        $pp_TxnRefNo = "T" . time() . rand(1000, 9999); // Unique reference number
        
        // Prepare JazzCash Wallet payment data
        $paymentData = [
            "pp_Amount"            => number_format($deposit->final_amount * 100, 0, '', ''), // Convert to paisa
            "pp_BillReference"     => $deposit->trx, // Use transaction ID directly as bill reference
            "pp_CNIC"              => "", // Will be filled by user
            "pp_Description"       => "Donation to " . $setting->site_name,
            "pp_Language"          => "EN",
            "pp_MerchantID"        => $merchantId,
            "pp_MobileNumber"      => "", // Will be filled by user
            "pp_Password"          => $password,
            "pp_TxnCurrency"       => "PKR",
            "pp_TxnDateTime"       => $pp_TxnDateTime,
            "pp_TxnExpiryDateTime" => $pp_TxnExpiryDateTime,
            "pp_TxnRefNo"          => $pp_TxnRefNo,
            "ppmpf_1"              => "",
            "ppmpf_2"              => "",
            "ppmpf_3"              => "",
            "ppmpf_4"              => "",
            "ppmpf_5"              => ""
        ];
        
        // Store transaction reference for later use
        $deposit->update(['trx' => $pp_TxnRefNo]);
        
        $send['val'] = [
            'merchant_id' => $merchantId,
            'password' => $password,
            'integrity_salt' => $integritySalt,
            'amount' => $deposit->final_amount,
            'currency' => $deposit->method_currency,
            'transaction_id' => $pp_TxnRefNo,
            'description' => $paymentData['pp_Description'],
            'customer_name' => $deposit->user_id ? $deposit->user->fullname : $deposit->full_name,
            'customer_email' => $deposit->user_id ? $deposit->user->email : $deposit->email,
            'sandbox' => $sandbox,
            'api_url' => $baseUrl
        ];
        $send['view'] = 'user.payment.jazzcash_wallet';
        $send['method'] = 'get';
        $send['url'] = '';

        return json_encode($send);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'phone_number' => 'required|string|regex:/^03[0-9]{9}$/',
            'cnic_last_6' => 'required|string|regex:/^[0-9]{6}$/'
        ]);

        $deposit = Deposit::where('trx', $request->transaction_id)->first();
        // dd($deposit->status);
        if (!$deposit) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        if ($deposit->status == ManageStatus::PAYMENT_SUCCESS) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already processed'
            ], 400);
        }

        $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $merchantId = $gatewayAcc->merchant_id;
        $password = $gatewayAcc->password;
        $integritySalt = $gatewayAcc->integrity_salt;
        $sandbox = $gatewayAcc->sandbox ?? false;
        
        // Generate transaction datetime and expiry (Pakistan Time)
        date_default_timezone_set("Asia/Karachi");
        $pp_TxnDateTime = date('YmdHis');
        $pp_TxnExpiryDateTime = date('YmdHis', strtotime('+1 day'));
        
        // Prepare JazzCash Wallet payment data
        $data = [
            "pp_Amount"            => number_format($deposit->final_amount * 100, 0, '', ''), // Convert to paisa
            "pp_BillReference"     => $deposit->trx, // Use transaction ID directly as bill reference
            "pp_CNIC"              => $request->cnic_last_6,
            "pp_Description"       => "Donation to " . bs()->site_name,
            "pp_Language"          => "EN",
            "pp_MerchantID"        => $merchantId,
            "pp_MobileNumber"      => $request->phone_number,
            "pp_Password"          => $password,
            "pp_TxnCurrency"       => "PKR",
            "pp_TxnDateTime"       => $pp_TxnDateTime,
            "pp_TxnExpiryDateTime" => $pp_TxnExpiryDateTime,
            "pp_TxnRefNo"          => $deposit->trx,
            "ppmpf_1"              => "",
            "ppmpf_2"              => "",
            "ppmpf_3"              => "",
            "ppmpf_4"              => "",
            "ppmpf_5"              => ""
        ];

        // Generate Secure Hash
        $data['pp_SecureHash'] = $this->generateSecureHash($data, $integritySalt);

        // Determine API URL based on sandbox mode
        $url = $sandbox ? 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction' : 'https://jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';

        // Initialize cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Execute and get response
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            return response()->json([
                'success' => false,
                'message' => 'Connection error: ' . curl_error($ch)
            ], 500);
        }
        
        curl_close($ch);
        
        $responseData = json_decode($response, true);
        
        if ($httpCode == 200 && isset($responseData['pp_ResponseCode']) && $responseData['pp_ResponseCode'] == '000') {
            // Payment successful
            $deposit->status = ManageStatus::PAYMENT_PENDING;
            PaymentController::campaignDataUpdate($deposit);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'transaction_id' => $deposit->trx,
                'amount' => $deposit->final_amount
            ]);
        } else {
            $errorMessage = $responseData['pp_ResponseMessage'] ?? 'Payment failed';
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'response_code' => $responseData['pp_ResponseCode'] ?? 'UNKNOWN'
            ], 400);
        }
    }

    private function generateSecureHash($data, $integritySalt)
    {
        ksort($data); // Sort array alphabetically by keys
        $string = '';
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $string .= '&' . $value;
            }
        }
        $string = $integritySalt . $string;
        return strtoupper(hash_hmac('sha256', $string, $integritySalt));
    }

    public function ipn(Request $request)
    {
        $deposit = Deposit::where('trx', $request->pp_TxnRefNo)->first();
        
        if (!$deposit) {
            return response('Transaction not found', 404);
        }
        
        if ($deposit->status == ManageStatus::PAYMENT_SUCCESS) {
            return response('Already processed', 200);
        }
        
        // Get JazzCash gateway configuration
        $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $merchantId = $gatewayAcc->merchant_id;
        $integritySalt = $gatewayAcc->integrity_salt;
        
        // Verify the hash for JazzCash
        $expectedHash = $this->generateSecureHash($request->all(), $integritySalt);
        
        if ($expectedHash !== $request->pp_SecureHash) {
            return response('Invalid hash', 400);
        }
        
        // Verify JazzCash payment status
        if ($request->pp_ResponseCode === '000' && $request->pp_ResponseMessage === 'Success') {
            PaymentController::campaignDataUpdate($deposit);
            return response('Payment processed successfully', 200);
        }
        
        return response('Payment failed', 400);
    }
}
