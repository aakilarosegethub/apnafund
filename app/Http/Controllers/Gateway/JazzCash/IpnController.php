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
            if (!$request->has('TransactionID')) {
                $response = 'Transaction ID missing';
                $webhookLogger->updateWebhookStatus($logs, 'failed', $response, [
                    'error_type' => 'missing_transaction_id',
                    'gateway' => 'jazzcash'
                ]);
                return response($response, 400);
            }

            $transactionId = $request->TransactionID;

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
    }
    
    /**
     * Handle any method (GET, POST, PUT, etc.) for JazzCash IPN
     */
    public function anyMethod(Request $request)
    {
        return $this->handle($request);
    }
}
