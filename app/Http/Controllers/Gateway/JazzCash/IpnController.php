<?php

namespace App\Http\Controllers\Gateway\JazzCash;

use App\Models\DataLog;
use App\Models\Deposit;
use App\Models\User;
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
                
                // Reload deposit to get updated data
                $deposit->refresh();
                $campaign = $deposit->campaign;
                
                // Get user data for email
                $user = User::find($deposit->user_id);
                if (!$user) {
                    $user = (object) [
                        'fullname' => $deposit->full_name,
                        'username' => $deposit->email,
                        'email' => $deposit->email,
                        'mobile' => $deposit->phone,
                    ];
                }
                
                // Send email to donor (user)
                try {
                    // Check if template exists
                    $userTemplate = \App\Models\NotificationTemplate::where('act', 'USER_PAYMENT_SUCCESS')
                        ->where('email_status', ManageStatus::ACTIVE)
                        ->first();
                    
                    if ($userTemplate) {
                        notify($user, 'USER_PAYMENT_SUCCESS', [
                            'full_name' => $deposit->full_name,
                            'email' => $deposit->email,
                            'campaign_name' => $campaign->name,
                            'amount' => showAmount($deposit->amount),
                            'method_name' => $deposit->gatewayCurrency()->name,
                            'trx' => $deposit->trx,
                            'date' => showDateTime($deposit->updated_at, 'd M, Y h:i A'),
                            'campaign_url' => route('campaign.show', $campaign->slug),
                            'admin_url' => urlPath('admin.donations.done'),
                        ], ['email']);
                        \Log::info('Payment success email sent to user: ' . $deposit->email);
                    } else {
                        \Log::warning('USER_PAYMENT_SUCCESS template not found or inactive. Email not sent to: ' . $deposit->email);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send payment success email to user (JazzCash): ' . $e->getMessage(), [
                        'user_email' => $deposit->email,
                        'deposit_id' => $deposit->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                // Send email to admin
                try {
                    $adminEmail = bs('site_email');
                    if ($adminEmail) {
                        // Check if template exists
                        $adminTemplate = \App\Models\NotificationTemplate::where('act', 'ADMIN_PAYMENT_SUCCESS')
                            ->where('email_status', ManageStatus::ACTIVE)
                            ->first();
                        
                        if ($adminTemplate) {
                            $adminUser = (object) [
                                'fullname' => 'Admin',
                                'username' => 'admin',
                                'email' => $adminEmail,
                            ];
                            
                            notify($adminUser, 'ADMIN_PAYMENT_SUCCESS', [
                                'full_name' => $deposit->full_name,
                                'email' => $deposit->email,
                                'campaign_name' => $campaign->name,
                                'amount' => showAmount($deposit->amount),
                                'method_name' => $deposit->gatewayCurrency()->name,
                                'trx' => $deposit->trx,
                                'date' => showDateTime($deposit->updated_at, 'd M, Y h:i A'),
                                'campaign_url' => route('campaign.show', $campaign->slug),
                                'admin_url' => urlPath('admin.donations.done'),
                            ], ['email']);
                            \Log::info('Payment success email sent to admin: ' . $adminEmail);
                        } else {
                            \Log::warning('ADMIN_PAYMENT_SUCCESS template not found or inactive. Email not sent to admin.');
                        }
                    } else {
                        \Log::warning('Admin email (site_email) not configured. Admin email not sent.');
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send payment success email to admin (JazzCash): ' . $e->getMessage(), [
                        'admin_email' => $adminEmail ?? 'not set',
                        'deposit_id' => $deposit->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                $response = 'Payment processed successfully';
                $webhookLogger->updateWebhookStatus($logs, 'success', $response, [
                    'gateway' => 'jazzcash',
                    'transaction_id' => $transactionId,
                    'amount' => $request->Amount,
                    'status' => $status,
                    'deposit_id' => $deposit->id,
                    'payment_processed' => true,
                    'emails_sent' => true
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
