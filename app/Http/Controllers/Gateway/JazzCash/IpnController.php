<?php

namespace App\Http\Controllers\Gateway\JazzCash;

use App\Models\DataLog;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Services\UnifiedWebhookLoggerService;

class IpnController extends Controller
{
    /**
     * Handle JazzCash IPN callback with comprehensive logging
     */
    public function handle(Request $request)
    {
        $endpoint = 'jazzcash/ipn';
        $webhookLogger = new UnifiedWebhookLoggerService();
        dd($request->Status);
        
        // Log incoming webhook data to both DataLog and WebhookLog tables
        $logs = $webhookLogger->logIncomingWebhook(
            $request, 
            $endpoint, 
            'jazzcash_payment',
            [
                'transaction_id' => $request->TransactionID ?? null,
                'amount' => $request->Amount ?? null,
                'status' => $request->Status ?? $request->status ?? null,
                'currency' => $request->Currency ?? null,
            ]
        );

        try {
            // Check if TransactionID exists

            $transactionId = $request->pp_TxnRefNo;

            // Find the deposit
            $deposit = Deposit::where('trx', $transactionId)->first();

            if (!$deposit) {
                $response = 'Transaction not found';
                $webhookLogger->updateWebhookStatus($logs, 'failed', $response, [
                    'error_type' => 'transaction_not_found',
                    'gateway' => 'jazzcash',
                    'transaction_id' => $transactionId
                ]);
                return response($response, 404);
            }

            // Check if already processed
            if ($deposit->status == ManageStatus::PAYMENT_SUCCESS) {
                $response = 'Already processed';
                $webhookLogger->updateWebhookStatus($logs, 'success', $response, [
                    'gateway' => 'jazzcash',
                    'deposit_id' => $deposit->id,
                    'already_processed' => true
                ]);
                return response($response, 200);
            }

            // Get JazzCash gateway configuration
            $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
            $merchantId = $gatewayAcc->merchant_id;
            $hashKey = $gatewayAcc->hash_key;

            // Verify the hash for JazzCash
            $expectedHash = hash('sha256', $merchantId . $transactionId . $request->Amount . $request->Currency . $hashKey);

            if ($expectedHash !== $request->Hash) {
                $response = 'Invalid hash';
                $webhookLogger->updateWebhookStatus($logs, 'failed', $response, [
                    'error_type' => 'invalid_hash',
                    'gateway' => 'jazzcash',
                    'transaction_id' => $transactionId,
                    'deposit_id' => $deposit->id
                ]);
                return response($response, 400);
            }

            // Verify JazzCash payment status
            $status = $request->Status ?? $request->status ?? '';
            
            if (in_array($status, ['Success', 'Completed', 'APPROVED', 'success', 'completed', 'approved'])) {
                // Process the payment
                PaymentController::campaignDataUpdate($deposit);
                $response = 'Payment processed successfully';
                $webhookLogger->updateWebhookStatus($logs, 'success', $response, [
                    'gateway' => 'jazzcash',
                    'transaction_id' => $transactionId,
                    'amount' => $request->Amount,
                    'status' => $status,
                    'deposit_id' => $deposit->id,
                    'payment_processed' => true
                ]);
                
                return response($response, 200);
            } else {
                $response = 'Payment failed - Status: ' . $status;
                $webhookLogger->updateWebhookStatus($logs, 'failed', $response, [
                    'error_type' => 'payment_failed',
                    'gateway' => 'jazzcash',
                    'transaction_id' => $transactionId,
                    'amount' => $request->Amount,
                    'status' => $status,
                    'deposit_id' => $deposit->id
                ]);
                
                return response($response, 400);
            }

        } catch (\Exception $e) {
            $response = 'Error processing IPN: ' . $e->getMessage();
            $webhookLogger->updateWebhookStatus($logs, 'error', $response, [
                'error_type' => 'exception',
                'gateway' => 'jazzcash',
                'transaction_id' => $request->TransactionID ?? null,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            
            return response($response, 500);
        }
        <?php

namespace App\Http\Controllers\Gateway\JazzCash;

use App\Models\DataLog;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Services\UnifiedWebhookLoggerService;

class IpnController extends Controller
{
    /**
     * Handle JazzCash IPN callback with comprehensive logging
     */
    public function handle(Request $request)
    {
        $endpoint = 'jazzcash/ipn';
        $webhookLogger = new UnifiedWebhookLoggerService();
        dd($request->Status);
        
        // Log incoming webhook data to both DataLog and WebhookLog tables
        $logs = $webhookLogger->logIncomingWebhook(
            $request, 
            $endpoint, 
            'jazzcash_payment',
            [
                'transaction_id' => $request->TransactionID ?? null,
                'amount' => $request->Amount ?? null,
                'status' => $request->Status ?? $request->status ?? null,
                'currency' => $request->Currency ?? null,
            ]
        );

        try {
            // Check if TransactionID exists

            $transactionId = $request->pp_TxnRefNo;

            // Find the deposit
            $deposit = Deposit::where('trx', $transactionId)->first();

            if (!$deposit) {
                $response = 'Transaction not found';
                $webhookLogger->updateWebhookStatus($logs, 'failed', $response, [
                    'error_type' => 'transaction_not_found',
                    'gateway' => 'jazzcash',
                    'transaction_id' => $transactionId
                ]);
                return response($response, 404);
            }

            // Check if already processed
            if ($deposit->status == ManageStatus::PAYMENT_SUCCESS) {
                $response = 'Already processed';
                $webhookLogger->updateWebhookStatus($logs, 'success', $response, [
                    'gateway' => 'jazzcash',
                    'deposit_id' => $deposit->id,
                    'already_processed' => true
                ]);
                return response($response, 200);
            }

            // Get JazzCash gateway configuration
            $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
            $merchantId = $gatewayAcc->merchant_id;
            $hashKey = $gatewayAcc->hash_key;

            // Verify the hash for JazzCash
            $expectedHash = hash('sha256', $merchantId . $transactionId . $request->Amount . $request->Currency . $hashKey);

            if ($expectedHash !== $request->Hash) {
                $response = 'Invalid hash';
                $webhookLogger->updateWebhookStatus($logs, 'failed', $response, [
                    'error_type' => 'invalid_hash',
                    'gateway' => 'jazzcash',
                    'transaction_id' => $transactionId,
                    'deposit_id' => $deposit->id
                ]);
                return response($response, 400);
            }

            // Verify JazzCash payment status
            $status = $request->Status ?? $request->status ?? '';
            
            if (in_array($status, ['Success', 'Completed', 'APPROVED', 'success', 'completed', 'approved'])) {
                // Process the payment
                PaymentController::campaignDataUpdate($deposit);
                $response = 'Payment processed successfully';
                $webhookLogger->updateWebhookStatus($logs, 'success', $response, [
                    'gateway' => 'jazzcash',
                    'transaction_id' => $transactionId,
                    'amount' => $request->Amount,
                    'status' => $status,
                    'deposit_id' => $deposit->id,
                    'payment_processed' => true
                ]);
                
                return response($response, 200);
            } else {
                $response = 'Payment failed - Status: ' . $status;
                $webhookLogger->updateWebhookStatus($logs, 'failed', $response, [
                    'error_type' => 'payment_failed',
                    'gateway' => 'jazzcash',
                    'transaction_id' => $transactionId,
                    'amount' => $request->Amount,
                    'status' => $status,
                    'deposit_id' => $deposit->id
                ]);
                
                return response($response, 400);
            }

        } catch (\Exception $e) {
            $response = 'Error processing IPN: ' . $e->getMessage();
            $webhookLogger->updateWebhookStatus($logs, 'error', $response, [
                'error_type' => 'exception',
                'gateway' => 'jazzcash',
                'transaction_id' => $request->TransactionID ?? null,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            
            return response($response, 500);
        }
        $input = json_decode($rawInput, true);

// Validate essential parameter
if (!isset($input['pp_TxnRefNo'])) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}
        
        // Extract transaction data
        $TxnRefNumber = $input['pp_TxnRefNo'];
        $ResponseCode = $input['pp_ResponseCode'];
        $ResponseMessage = $input['pp_ResponseMessage'];
        $TxnDateTime = $input['pp_TxnDateTime'];
        $TxnType = $input['pp_TxnType'];
        $Version = $input['pp_Version'];
        $BankID = $input['pp_BankID'];
        $ProductID = $input['pp_ProductID'];
        $AuthCode = $input['pp_AuthCode'];
        $RetreivalReferenceNo = $input['pp_RetreivalReferenceNo'];
        $SettlementExpiry = $input['pp_SettlementExpiry'];
        //$ResponseCode = "000";  // Success code
        //$ResponseMessage = "Transaction confirmed by Merchant.";
        //$TxnDateTime = date('YmdHis');  // Current timestamp
        //$TxnType = "MWALLET";  // Mobile wallet type
        //$Version = '1.1';
        // Optional parameters (can be left empty)
        //$BankID = ""; 
        //$ProductID = ""; 
        //$AuthCode = "";
        //$RetreivalReferenceNo = "";
        //$SettlementExpiry = "";
        
        // Prepare data for secure hash calculation
        $SortedHashArray = [
            $AuthCode,
            $BankID, 
            $Password,
            $ProductID,
            $ResponseCode,
            $ResponseMessage,
            $RetreivalReferenceNo,
            $SettlementExpiry,
            $TxnDateTime,
            $TxnRefNumber,
            $TxnType,
            $Version
        ];
        
        // Filter out empty values and concatenate them with the Integrity Salt
        $SortedArray = $IntegritySalt . '&' . implode('&', array_filter($SortedHashArray));
        
        // Debug: Log the sorted string for hash verification
        file_put_contents('sorted_array_debug.txt', "Sorted Array: " . $SortedArray . PHP_EOL, FILE_APPEND);
        
        // Generate the secure hash
        $SecureHash = hash_hmac('sha256', $SortedArray, $IntegritySalt);
        
        // Debug: Log the generated secure hash
        file_put_contents('secure_hash_debug.txt', "SecureHash: " . $SecureHash . PHP_EOL, FILE_APPEND);
        //file_put_contents('ipn_log.txt', date('Y-m-d H:i:s') . " - Request received" . PHP_EOL, FILE_APPEND);
        file_put_contents('ipn_log.txt', date('Y-m-d H:i:s') . " - Raw Input: " . $rawInput . PHP_EOL, FILE_APPEND);
        // Prepare the data to send back to JazzCash
        $postData = [
            'pp_Version' => $Version,
            'pp_TxnType' => $TxnType,
            'pp_BankID' => $BankID,
            'pp_ProductID' => $ProductID,
            'pp_Password' => $Password,
            'pp_TxnRefNo' => $TxnRefNumber,
            'pp_TxnDateTime' => $TxnDateTime,
            'pp_ResponseCode' => $ResponseCode,
            'pp_ResponseMessage' => $ResponseMessage,
            'pp_AuthCode' => $AuthCode,
            'pp_SettlementExpiry' => $SettlementExpiry,
            'pp_RetreivalReferenceNo' => $RetreivalReferenceNo,
            'pp_SecureHash' => $SecureHash
        ];
        
        // Initialize and execute the POST request
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($postData),
            ]
        ];
        
        $context = stream_context_create($options);
        $response = @file_get_contents($PaymentURL, false, $context);
        
        // Handle connection failure
        if ($response === FALSE) {
            echo json_encode(['error' => 'Failed to connect to JazzCash']);
            exit;
        }
        
        // Decode and process the response
        $responseData = json_decode($response, true);
        
        // Check if the response is valid
        if (empty($responseData)) {
            echo json_encode(['error' => 'Invalid response from JazzCash']);
            exit;
        }
        
        // Extract necessary response data
        $responseFields = [
            $responseData['pp_ResponseCode'] ?? '',
            $responseData['pp_ResponseMessage'] ?? '',
            $responseData['pp_TxnRefNo'] ?? '',
            $responseData['pp_TxnDateTime'] ?? '',
            $responseData['pp_TxnType'] ?? '',
            $responseData['pp_AuthCode'] ?? '',
            $responseData['pp_RetreivalReferenceNo'] ?? '',
            $responseData['pp_SettlementExpiry'] ?? '',
            $responseData['pp_BankID'] ?? '',
            $responseData['pp_ProductID'] ?? '',
            $responseData['pp_Version'] ?? ''
        ];
        
        // Prepend IntegritySalt and concatenate non-empty values with "&"
        //$IntegritySalt = "9d48fssdhg";  // Replace with your actual IntegritySalt
        $hashString = $IntegritySalt . '&' . implode('&', array_filter($responseFields));
        
        // Debug: Log the hash string for verification
        file_put_contents('hash_string_debug.txt', "Hash String: " . $hashString . PHP_EOL, FILE_APPEND);
        
        // Generate the pp_SecureHash using HMAC SHA-256
        $pp_SecureHash = hash_hmac('sha256', $hashString, $IntegritySalt);
        
        // Debug: Log the generated pp_SecureHash
        file_put_contents('secure_hash_debug.txt', "Generated pp_SecureHash: " . $pp_SecureHash . PHP_EOL, FILE_APPEND);
        
        // Prepare the output response with the newly generated pp_SecureHash
        $output = [
            'pp_ResponseCode' => "000",
            'pp_ResponseMessage' => "Success",
            'pp_SecureHash' => $pp_SecureHash
        ];
        
        // Return the response as JSON
        echo json_encode($output);
        
    }
    
    /**
     * Handle any method (GET, POST, PUT, etc.) for JazzCash IPN
     */
    public function anyMethod(Request $request)
    {
        return $this->handle($request);
    }
}

        
        // Extract transaction data
        $TxnRefNumber = $input['pp_TxnRefNo'];
        $ResponseCode = $input['pp_ResponseCode'];
        $ResponseMessage = $input['pp_ResponseMessage'];
        $TxnDateTime = $input['pp_TxnDateTime'];
        $TxnType = $input['pp_TxnType'];
        $Version = $input['pp_Version'];
        $BankID = $input['pp_BankID'];
        $ProductID = $input['pp_ProductID'];
        $AuthCode = $input['pp_AuthCode'];
        $RetreivalReferenceNo = $input['pp_RetreivalReferenceNo'];
        $SettlementExpiry = $input['pp_SettlementExpiry'];
        //$ResponseCode = "000";  // Success code
        //$ResponseMessage = "Transaction confirmed by Merchant.";
        //$TxnDateTime = date('YmdHis');  // Current timestamp
        //$TxnType = "MWALLET";  // Mobile wallet type
        //$Version = '1.1';
        // Optional parameters (can be left empty)
        //$BankID = ""; 
        //$ProductID = ""; 
        //$AuthCode = "";
        //$RetreivalReferenceNo = "";
        //$SettlementExpiry = "";
        
        // Prepare data for secure hash calculation
        $SortedHashArray = [
            $AuthCode,
            $BankID, 
            $Password,
            $ProductID,
            $ResponseCode,
            $ResponseMessage,
            $RetreivalReferenceNo,
            $SettlementExpiry,
            $TxnDateTime,
            $TxnRefNumber,
            $TxnType,
            $Version
        ];
        
        // Filter out empty values and concatenate them with the Integrity Salt
        $SortedArray = $IntegritySalt . '&' . implode('&', array_filter($SortedHashArray));
        
        // Debug: Log the sorted string for hash verification
        file_put_contents('sorted_array_debug.txt', "Sorted Array: " . $SortedArray . PHP_EOL, FILE_APPEND);
        
        // Generate the secure hash
        $SecureHash = hash_hmac('sha256', $SortedArray, $IntegritySalt);
        
        // Debug: Log the generated secure hash
        file_put_contents('secure_hash_debug.txt', "SecureHash: " . $SecureHash . PHP_EOL, FILE_APPEND);
        //file_put_contents('ipn_log.txt', date('Y-m-d H:i:s') . " - Request received" . PHP_EOL, FILE_APPEND);
        file_put_contents('ipn_log.txt', date('Y-m-d H:i:s') . " - Raw Input: " . $rawInput . PHP_EOL, FILE_APPEND);
        // Prepare the data to send back to JazzCash
        $postData = [
            'pp_Version' => $Version,
            'pp_TxnType' => $TxnType,
            'pp_BankID' => $BankID,
            'pp_ProductID' => $ProductID,
            'pp_Password' => $Password,
            'pp_TxnRefNo' => $TxnRefNumber,
            'pp_TxnDateTime' => $TxnDateTime,
            'pp_ResponseCode' => $ResponseCode,
            'pp_ResponseMessage' => $ResponseMessage,
            'pp_AuthCode' => $AuthCode,
            'pp_SettlementExpiry' => $SettlementExpiry,
            'pp_RetreivalReferenceNo' => $RetreivalReferenceNo,
            'pp_SecureHash' => $SecureHash
        ];
        
        // Initialize and execute the POST request
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($postData),
            ]
        ];
        
        $context = stream_context_create($options);
        $response = @file_get_contents($PaymentURL, false, $context);
        
        // Handle connection failure
        if ($response === FALSE) {
            echo json_encode(['error' => 'Failed to connect to JazzCash']);
            exit;
        }
        
        // Decode and process the response
        $responseData = json_decode($response, true);
        
        // Check if the response is valid
        if (empty($responseData)) {
            echo json_encode(['error' => 'Invalid response from JazzCash']);
            exit;
        }
        
        // Extract necessary response data
        $responseFields = [
            $responseData['pp_ResponseCode'] ?? '',
            $responseData['pp_ResponseMessage'] ?? '',
            $responseData['pp_TxnRefNo'] ?? '',
            $responseData['pp_TxnDateTime'] ?? '',
            $responseData['pp_TxnType'] ?? '',
            $responseData['pp_AuthCode'] ?? '',
            $responseData['pp_RetreivalReferenceNo'] ?? '',
            $responseData['pp_SettlementExpiry'] ?? '',
            $responseData['pp_BankID'] ?? '',
            $responseData['pp_ProductID'] ?? '',
            $responseData['pp_Version'] ?? ''
        ];
        
        // Prepend IntegritySalt and concatenate non-empty values with "&"
        //$IntegritySalt = "9d48fssdhg";  // Replace with your actual IntegritySalt
        $hashString = $IntegritySalt . '&' . implode('&', array_filter($responseFields));
        
        // Debug: Log the hash string for verification
        file_put_contents('hash_string_debug.txt', "Hash String: " . $hashString . PHP_EOL, FILE_APPEND);
        
        // Generate the pp_SecureHash using HMAC SHA-256
        $pp_SecureHash = hash_hmac('sha256', $hashString, $IntegritySalt);
        
        // Debug: Log the generated pp_SecureHash
        file_put_contents('secure_hash_debug.txt', "Generated pp_SecureHash: " . $pp_SecureHash . PHP_EOL, FILE_APPEND);
        
        // Prepare the output response with the newly generated pp_SecureHash
        $output = [
            'pp_ResponseCode' => "000",
            'pp_ResponseMessage' => "Success",
            'pp_SecureHash' => $pp_SecureHash
        ];
        
        // Return the response as JSON
        echo json_encode($output);
        
    }
    
    /**
     * Handle any method (GET, POST, PUT, etc.) for JazzCash IPN
     */
    public function anyMethod(Request $request)
    {
        return $this->handle($request);
    }
}
