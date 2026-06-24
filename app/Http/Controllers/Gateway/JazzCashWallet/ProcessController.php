<?php

namespace App\Http\Controllers\Gateway\JazzCashWallet;

use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Services\JazzCashApiLoggerService;

class ProcessController extends Controller
{
    private function regenerateTransactionId(Deposit $deposit): string
    {
        do {
            $newTrx = 'T' . time() . random_int(1000, 9999);
        } while (Deposit::where('trx', $newTrx)->exists());

        $deposit->trx = $newTrx;
        $deposit->save();
        session()->put('Track', $newTrx);

        return $newTrx;
    }

    public static function process($deposit)
    {
        $gwCurrency = $deposit->gatewayCurrency();
        if (!$gwCurrency) {
            return json_encode([
                'error' => true,
                'message' => 'Gateway currency configuration not found for this payment.',
            ]);
        }

        $gatewayAcc = json_decode($gwCurrency->gateway_parameter ?? '{}');
        if (!$gatewayAcc || !isset($gatewayAcc->merchant_id, $gatewayAcc->password, $gatewayAcc->integrity_salt)) {
            return json_encode([
                'error' => true,
                'message' => 'Gateway is not configured correctly. Please contact support.',
            ]);
        }
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
        $country = (string) ($deposit->country ?? '');
        $localCurrency = strtoupper((string) getCurrencyCodeForCountryName($country));
        $localCurrency = $localCurrency ?: strtoupper((string) ($deposit->method_currency ?? 'PKR'));
        $currencyService = app(\App\Services\CurrencyService::class);
        $localAmount = (float) $deposit->final_amount;
        try {
            // DB amount is stored in platform currency; convert to depositor local currency for gateway request.
            $localAmount = (float) $currencyService->convertFromPlatform((float) $deposit->amount, $localCurrency);
        } catch (\Throwable $e) {
            $localAmount = (float) $deposit->final_amount;
        }
        $pp_TxnRefNo = "T" . time() . rand(1000, 9999); // Unique reference number
        
        // Prepare JazzCash Wallet payment data
        // die($localAmount);
        $paymentData = [
            "pp_Amount"            => number_format($localAmount * 100, 0, '', ''), // Convert to paisa
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
        session()->put('Track', $pp_TxnRefNo);
        
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

        app(JazzCashApiLoggerService::class)->logWalletInit($send['val'], $deposit);

        return json_encode($send);
    }

    public function processPayment(Request $request)
    {
        $logger = app(JazzCashApiLoggerService::class);
        $deposit = Deposit::where('trx', $request->input('transaction_id'))->first();
        $internalLog = $logger->logInternalRequest(
            $request,
            'wallet_process',
            'ipn/jazzcash-wallet/process',
            $deposit
        );

        try {
            $request->validate([
                'transaction_id' => 'required|string',
                'phone_number' => 'required|string',
                'cnic_last_6' => 'required|string|regex:/^[0-9]{6}$/'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $response = [
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
            ];
            $logger->finalizeLog($internalLog, 'failed', $response, 422);
            return response()->json($response, 422);
        }

        $normalizedPhone = preg_replace('/\D+/', '', (string) $request->phone_number);
        if (str_starts_with($normalizedPhone, '92') && strlen($normalizedPhone) === 12) {
            $normalizedPhone = '0' . substr($normalizedPhone, 2);
        }
        if (!preg_match('/^03[0-9]{9}$/', $normalizedPhone)) {
            $response = [
                'success' => false,
                'message' => 'Please enter a valid JazzCash mobile number (03XXXXXXXXX).',
            ];
            $logger->finalizeLog($internalLog, 'failed', $response, 422);
            return response()->json($response, 422);
        }
        $request->merge(['phone_number' => $normalizedPhone]);

        $deposit = Deposit::where('trx', $request->transaction_id)->first();
        // dd($deposit->status);
        if (!$deposit) {
            $response = [
                'success' => false,
                'message' => 'Transaction not found'
            ];
            $logger->finalizeLog($internalLog, 'failed', $response, 404);
            return response()->json($response, 404);
        }

        if ($deposit->status == ManageStatus::PAYMENT_SUCCESS) {
            $response = [
                'success' => false,
                'message' => 'Payment already processed'
            ];
            $logger->finalizeLog($internalLog, 'failed', $response, 400);
            return response()->json($response, 400);
        }

        $gwCurrency = $deposit->gatewayCurrency();
        if (!$gwCurrency) {
            $response = [
                'success' => false,
                'message' => 'Gateway currency configuration not found for this payment.',
            ];
            $logger->finalizeLog($internalLog, 'failed', $response, 422);
            return response()->json($response, 422);
        }

        $gatewayAcc = json_decode($gwCurrency->gateway_parameter ?? '{}');
        if (!$gatewayAcc || !isset($gatewayAcc->merchant_id, $gatewayAcc->password, $gatewayAcc->integrity_salt)) {
            $response = [
                'success' => false,
                'message' => 'Gateway is not configured correctly. Please contact support.',
            ];
            $logger->finalizeLog($internalLog, 'failed', $response, 500);
            return response()->json($response, 500);
        }

        $merchantId = $gatewayAcc->merchant_id;
        $password = $gatewayAcc->password;
        $integritySalt = $gatewayAcc->integrity_salt;
        $sandbox = $gatewayAcc->sandbox ?? false;
        
        // Generate transaction datetime and expiry (Pakistan Time)
        date_default_timezone_set("Asia/Karachi");
        $pp_TxnDateTime = date('YmdHis');
        $pp_TxnExpiryDateTime = date('YmdHis', strtotime('+1 day'));
        $country = (string) ($deposit->country ?? '');
        $localCurrency = strtoupper((string) getCurrencyCodeForCountryName($country));
        $localCurrency = $localCurrency ?: strtoupper((string) ($deposit->method_currency ?? 'PKR'));
        $currencyService = app(\App\Services\CurrencyService::class);
        $localAmount = (float) $deposit->final_amount;
        try {
            $localAmount = (float) $currencyService->convertFromPlatform((float) $deposit->final_amount, $localCurrency);
        } catch (\Throwable $e) {
            $localAmount = (float) $deposit->final_amount;
        }
        
        // Prepare JazzCash Wallet payment data
        $data = [
            "pp_Amount"            => number_format($localAmount * 100, 0, '', ''), // Convert to paisa
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

        $requestHeaders = ['Content-Type' => 'application/json'];
        $startTime = microtime(true);

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
        $executionTime = microtime(true) - $startTime;
        
        if (curl_errno($ch)) {
            $curlError = curl_error($ch);
            $logger->logOutboundRequest(
                'wallet_api',
                $url,
                'POST',
                $data,
                $requestHeaders,
                null,
                $httpCode ?: null,
                $executionTime,
                $deposit,
                $curlError
            );
            $newTransactionId = $this->regenerateTransactionId($deposit);
            curl_close($ch);
            $response = [
                'success' => false,
                'message' => 'Connection error: ' . $curlError,
                'transaction_id' => $newTransactionId,
            ];
            $logger->finalizeLog($internalLog, 'failed', $response, 500);
            return response()->json($response, 500);
        }
        
        curl_close($ch);

        $logger->logOutboundRequest(
            'wallet_api',
            $url,
            'POST',
            $data,
            $requestHeaders,
            is_string($response) ? $response : null,
            $httpCode,
            $executionTime,
            $deposit
        );
        
        $responseData = json_decode($response, true);
        
        if ($httpCode == 200 && isset($responseData['pp_ResponseCode']) && $responseData['pp_ResponseCode'] == '000') {
            // Payment successful
            $deposit->status = ManageStatus::PAYMENT_PENDING;
            PaymentController::campaignDataUpdate($deposit);
            session()->put('Track', $deposit->trx);
            
            $response = [
                'success' => true,
                'message' => 'Payment processed successfully',
                'transaction_id' => $deposit->trx,
                'amount' => round($localAmount, 2),
            ];
            $logger->finalizeLog($internalLog, 'success', $response, 200);
            return response()->json($response);
        }

        $errorMessage = $responseData['pp_ResponseMessage'] ?? 'Payment failed';
        $newTransactionId = $this->regenerateTransactionId($deposit);

        $response = [
            'success' => false,
            'message' => $errorMessage,
            'response_code' => $responseData['pp_ResponseCode'] ?? 'UNKNOWN',
            'transaction_id' => $newTransactionId,
        ];
        $logger->finalizeLog($internalLog, 'failed', $response, 400);
        return response()->json($response, 400);
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
        $logger = app(JazzCashApiLoggerService::class);
        $deposit = Deposit::where('trx', $request->pp_TxnRefNo)->first();
        $logContext = $logger->logIncoming($request, 'ipn/jazzcash-wallet', 'wallet_ipn', $deposit, 'jazzcash_wallet');
        
        if (!$deposit) {
            $response = 'Transaction not found';
            $logger->finalizeInbound($logContext, 'failed', $response, 404);
            return response($response, 404);
        }
        
        if ($deposit->status == ManageStatus::PAYMENT_SUCCESS) {
            $response = 'Already processed';
            $logger->finalizeInbound($logContext, 'success', $response, 200);
            return response($response, 200);
        }

        $gwCurrency = $deposit->gatewayCurrency();
        if (!$gwCurrency) {
            $response = 'Gateway configuration not found';
            $logger->finalizeInbound($logContext, 'failed', $response, 500);
            return response($response, 500);
        }

        $gatewayAcc = json_decode($gwCurrency->gateway_parameter ?? '{}');
        if (!$gatewayAcc || !isset($gatewayAcc->merchant_id, $gatewayAcc->integrity_salt)) {
            $response = 'Gateway not configured';
            $logger->finalizeInbound($logContext, 'failed', $response, 500);
            return response($response, 500);
        }

        $merchantId = $gatewayAcc->merchant_id;
        $integritySalt = $gatewayAcc->integrity_salt;
        
        // Verify the hash for JazzCash
        $expectedHash = $this->generateSecureHash($request->all(), $integritySalt);
        
        if ($expectedHash !== $request->pp_SecureHash) {
            $response = 'Invalid hash';
            $logger->finalizeInbound($logContext, 'failed', $response, 400);
            return response($response, 400);
        }
        
        // Verify JazzCash payment status
        if ($request->pp_ResponseCode === '000' && $request->pp_ResponseMessage === 'Success') {
            PaymentController::campaignDataUpdate($deposit);
            $response = 'Payment processed successfully';
            $logger->finalizeInbound($logContext, 'success', $response, 200);
            return response($response, 200);
        }
        
        $response = 'Payment failed';
        $logger->finalizeInbound($logContext, 'failed', $response, 400);
        return response($response, 400);
    }
}
