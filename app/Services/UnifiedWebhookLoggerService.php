<?php

namespace App\Services;

use App\Models\DataLog;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class UnifiedWebhookLoggerService
{
    /**
     * Log incoming webhook data to both DataLog and WebhookLog tables
     */
    public function logIncomingWebhook(Request $request, string $endpoint, string $webhookType = 'payment_callback', array $additionalData = []): array
    {
        $startTime = microtime(true);
        $method = $request->method();
        $transactionId = $this->extractTransactionId($request, $additionalData);
        
        // Log to DataLog table (for general request logging)
        $dataLog = DataLog::logRequest($endpoint, $method, $request, null, $transactionId);
        
        // Enhanced DataLog with additional webhook data
        if ($dataLog && $dataLog->id) {
            $dataLog->update([
                'request_data' => array_merge($request->all(), $additionalData),
                'headers' => $request->headers->all(),
                'raw_input' => $request->getContent(),
                'status' => 'processing'
            ]);
        }
        
        // Log to WebhookLog table (for webhook-specific logging)
        $webhookLog = $this->createWebhookLog($request, $endpoint, $webhookType, $transactionId, $additionalData);
        
        return [
            'data_log' => $dataLog,
            'webhook_log' => $webhookLog,
            'start_time' => $startTime
        ];
    }
    
    /**
     * Update webhook processing status
     */
    public function updateWebhookStatus(array $logs, string $status, string $response = null, array $additionalData = []): void
    {
        $executionTime = microtime(true) - $logs['start_time'];
        
        // Update DataLog
        if ($logs['data_log']) {
            $logs['data_log']->updateStatus($status, $response);
        }
        
        // Update WebhookLog
        if ($logs['webhook_log']) {
            $logs['webhook_log']->update([
                'status' => $status,
                'response_body' => $response,
                'execution_time' => $executionTime,
                'error_message' => $status === 'failed' ? $response : null,
            ]);
        }
        
        // Enhanced logging based on status
        $this->logStatusUpdate($logs, $status, $response, $executionTime, $additionalData);
    }
    
    /**
     * Extract transaction ID from request or additional data
     */
    private function extractTransactionId(Request $request, array $additionalData = []): ?string
    {
        // Common transaction ID field names across different gateways
        $transactionFields = [
            'TransactionID', 'transaction_id', 'txn_id', 'txnId', 
            'payment_id', 'paymentId', 'order_id', 'orderId',
            'reference', 'ref', 'trx', 'trx_id'
        ];
        
        foreach ($transactionFields as $field) {
            if ($request->has($field)) {
                return $request->get($field);
            }
            if (isset($additionalData[$field])) {
                return $additionalData[$field];
            }
        }
        
        return null;
    }
    
    /**
     * Create WebhookLog entry
     */
    private function createWebhookLog(Request $request, string $endpoint, string $webhookType, ?string $transactionId, array $additionalData = []): ?WebhookLog
    {
        try {
            return WebhookLog::create([
                'webhook_type' => $webhookType,
                'url' => $endpoint,
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'payload' => array_merge($request->all(), $additionalData),
                'response_status' => null,
                'response_body' => null,
                'response_headers' => null,
                'execution_time' => null,
                'status' => 'processing',
                'error_message' => null,
                'retry_count' => 0,
                'user_id' => $additionalData['user_id'] ?? null,
                'campaign_id' => $additionalData['campaign_id'] ?? null,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to create WebhookLog', [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint,
                'webhook_type' => $webhookType,
            ]);
            return null;
        }
    }
    
    /**
     * Log status update with enhanced details
     */
    private function logStatusUpdate(array $logs, string $status, ?string $response, float $executionTime, array $additionalData = []): void
    {
        $logData = [
            'data_log_id' => $logs['data_log']->id ?? null,
            'webhook_log_id' => $logs['webhook_log']->id ?? null,
            'status' => $status,
            'execution_time' => $executionTime,
        ];
        
        // Add additional data to log
        $logData = array_merge($logData, $additionalData);
        
        switch ($status) {
            case 'success':
                Log::info('Webhook processed successfully', $logData);
                break;
            case 'failed':
                Log::warning('Webhook processing failed', $logData);
                break;
            case 'error':
                Log::error('Webhook processing error', $logData);
                break;
            default:
                Log::info('Webhook status updated', $logData);
        }
    }
    
    /**
     * Get webhook statistics for admin dashboard
     */
    public function getWebhookStatistics(): array
    {
        $dataLogStats = [
            'total_requests' => DataLog::count(),
            'successful_requests' => DataLog::where('status', 'success')->count(),
            'failed_requests' => DataLog::where('status', 'failed')->count(),
            'error_requests' => DataLog::where('status', 'error')->count(),
        ];
        
        $webhookLogStats = [
            'total_webhooks' => WebhookLog::count(),
            'successful_webhooks' => WebhookLog::successful()->count(),
            'failed_webhooks' => WebhookLog::failed()->count(),
            'pending_webhooks' => WebhookLog::pending()->count(),
        ];
        
        return [
            'data_logs' => $dataLogStats,
            'webhook_logs' => $webhookLogStats,
            'combined' => [
                'total' => $dataLogStats['total_requests'] + $webhookLogStats['total_webhooks'],
                'success_rate' => $this->calculateSuccessRate($dataLogStats, $webhookLogStats),
            ]
        ];
    }
    
    /**
     * Calculate overall success rate
     */
    private function calculateSuccessRate(array $dataLogStats, array $webhookLogStats): float
    {
        $total = $dataLogStats['total_requests'] + $webhookLogStats['total_webhooks'];
        $successful = $dataLogStats['successful_requests'] + $webhookLogStats['successful_webhooks'];
        
        return $total > 0 ? round(($successful / $total) * 100, 2) : 0;
    }
    
    /**
     * Get recent webhook logs with pagination
     */
    public function getRecentWebhookLogs(int $limit = 50, string $type = null): array
    {
        $dataLogs = DataLog::latest()->limit($limit)->get();
        $webhookLogs = WebhookLog::latest()->limit($limit)->get();
        
        if ($type) {
            $dataLogs = DataLog::where('endpoint', 'like', "%{$type}%")->latest()->limit($limit)->get();
            $webhookLogs = WebhookLog::byType($type)->latest()->limit($limit)->get();
        }
        
        return [
            'data_logs' => $dataLogs,
            'webhook_logs' => $webhookLogs,
        ];
    }
    
    /**
     * Clean up old webhook logs
     */
    public function cleanupOldLogs(int $days = 30): array
    {
        $dataLogsDeleted = DataLog::where('created_at', '<', now()->subDays($days))->delete();
        $webhookLogsDeleted = WebhookLog::where('created_at', '<', now()->subDays($days))->delete();
        
        return [
            'data_logs_deleted' => $dataLogsDeleted,
            'webhook_logs_deleted' => $webhookLogsDeleted,
            'total_deleted' => $dataLogsDeleted + $webhookLogsDeleted,
        ];
    }
}
