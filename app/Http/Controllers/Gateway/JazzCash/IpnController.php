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
use App\Services\JazzCashApiLoggerService;

class IpnController extends Controller
{
    /**
     * Handle JazzCash IPN callback with comprehensive logging
     */
    public function handle(Request $request)
    {
        $endpoint = 'jazzcash/ipn';
        $webhookLogger = new UnifiedWebhookLoggerService();
        $jazzLogger = app(JazzCashApiLoggerService::class);
        $transactionId = $request->pp_TxnRefNo ?? $request->TransactionID ?? null;
        $deposit = $transactionId ? Deposit::where('trx', $transactionId)->first() : null;

        $jazzLogger->appendRootLog(
            date('Y-m-d H:i:s') . ' | main_ipn | inbound | trx: ' . ($transactionId ?: 'n/a'),
            [
                'CURL' => $jazzLogger->buildIncomingCurl($request, $endpoint),
                'REQUEST' => $request->all(),
                'RAW_INPUT' => $request->getContent() !== '' ? $request->getContent() : null,
            ]
        );
        
        // Log incoming webhook data to both DataLog and WebhookLog tables
        $logs = $webhookLogger->logIncomingWebhook(
            $request, 
            $endpoint, 
            'jazzcash_payment',
            [
                'transaction_id' => $request->pp_TxnRefNo ?? $request->TransactionID ?? null,
                'amount' => $request->Amount ?? $request->pp_Amount ?? null,
                'status' => $request->Status ?? $request->status ?? $request->pp_ResponseMessage ?? null,
                'currency' => $request->Currency ?? $request->pp_TxnCurrency ?? null,
                'gateway' => 'jazzcash',
                'flow' => 'main_ipn',
                'direction' => 'inbound',
                'curl_command' => $jazzLogger->buildIncomingCurl($request, $endpoint),
            ]
        );

        try {
            // Check if TransactionID exists
            $transactionId = $request->pp_TxnRefNo;

            // Find the deposit
            $deposit = Deposit::where('trx', $transactionId)->first();

            if (!$deposit) {
                $response = 'Transaction not found';
                $jazzLogger->appendRootLog(
                    date('Y-m-d H:i:s') . ' | main_ipn | inbound | RESPONSE | trx: ' . ($transactionId ?: 'n/a') . ' | status: failed',
                    ['HTTP_STATUS' => 404, 'RESPONSE' => $response]
                );
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
                $jazzLogger->appendRootLog(
                    date('Y-m-d H:i:s') . ' | main_ipn | inbound | RESPONSE | trx: ' . $transactionId . ' | status: success',
                    ['HTTP_STATUS' => 200, 'RESPONSE' => $response]
                );
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
                $jazzLogger->appendRootLog(
                    date('Y-m-d H:i:s') . ' | main_ipn | inbound | RESPONSE | trx: ' . $transactionId . ' | status: failed',
                    ['HTTP_STATUS' => 400, 'RESPONSE' => $response]
                );
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
                
                // Send email to admin(s): Basic settings → site_email, else all admin accounts
                try {
                    $adminTemplate = \App\Models\NotificationTemplate::where('act', 'ADMIN_PAYMENT_SUCCESS')
                        ->where('email_status', ManageStatus::ACTIVE)
                        ->first();

                    if (!$adminTemplate) {
                        \Log::warning('ADMIN_PAYMENT_SUCCESS template not found or inactive. Email not sent to admin (JazzCash).');
                    } else {
                        $adminRecipients = adminMailNotifyRecipients();
                        if ($adminRecipients === []) {
                            \Log::warning('No admin email recipients (set Site email in Basic settings or add admin user emails). JazzCash.');
                        } else {
                            $shortCodes = [
                                'full_name' => $deposit->full_name,
                                'email' => $deposit->email,
                                'campaign_name' => $campaign->name,
                                'amount' => showAmount($deposit->amount),
                                'method_name' => $deposit->gatewayCurrency()->name,
                                'trx' => $deposit->trx,
                                'date' => showDateTime($deposit->updated_at, 'd M, Y h:i A'),
                                'campaign_url' => route('campaign.show', $campaign->slug),
                                'admin_url' => urlPath('admin.donations.done'),
                            ];
                            foreach ($adminRecipients as $adminUser) {
                                notify($adminUser, 'ADMIN_PAYMENT_SUCCESS', $shortCodes, ['email']);
                            }
                            $toList = implode(', ', array_map(static fn ($r) => $r->email, $adminRecipients));
                            \Log::info('Payment success email sent to admin(s) (JazzCash): ' . $toList);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send payment success email to admin (JazzCash): ' . $e->getMessage(), [
                        'deposit_id' => $deposit->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                $response = 'Payment processed successfully';
                $jazzLogger->appendRootLog(
                    date('Y-m-d H:i:s') . ' | main_ipn | inbound | RESPONSE | trx: ' . $transactionId . ' | status: success',
                    ['HTTP_STATUS' => 200, 'RESPONSE' => $response]
                );
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
                $jazzLogger->appendRootLog(
                    date('Y-m-d H:i:s') . ' | main_ipn | inbound | RESPONSE | trx: ' . $transactionId . ' | status: failed',
                    ['HTTP_STATUS' => 400, 'RESPONSE' => $response]
                );
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
            $jazzLogger->appendRootLog(
                date('Y-m-d H:i:s') . ' | main_ipn | inbound | RESPONSE | status: error',
                ['HTTP_STATUS' => 500, 'RESPONSE' => $response]
            );
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
