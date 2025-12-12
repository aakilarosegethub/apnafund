<?php

namespace App\Services;

use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WebhookLoggerService
{
    /**
     * Log a webhook request and response
     */
    public function logWebhook(array $data): WebhookLog
    {
        $startTime = microtime(true);
        
        try {
            // Make the HTTP request
            $response = $this->makeHttpRequest($data);
            
            $executionTime = microtime(true) - $startTime;
            
            // Create webhook log entry
            $webhookLog = WebhookLog::create([
                'webhook_type' => $data['webhook_type'] ?? 'product_sync',
                'url' => $data['url'],
                'method' => $data['method'] ?? 'POST',
                'headers' => $data['headers'] ?? [],
                'payload' => $data['payload'] ?? [],
                'response_status' => $response['status'],
                'response_body' => $response['body'],
                'response_headers' => $response['headers'],
                'execution_time' => $executionTime,
                'status' => $response['status'] >= 200 && $response['status'] < 300 ? 'success' : 'failed',
                'error_message' => $response['status'] >= 400 ? $response['body'] : null,
                'retry_count' => 0,
                'user_id' => $data['user_id'] ?? null,
                'campaign_id' => $data['campaign_id'] ?? null,
            ]);

            // Log to Laravel log as well
            Log::info('Webhook executed', [
                'webhook_id' => $webhookLog->id,
                'url' => $data['url'],
                'status' => $response['status'],
                'execution_time' => $executionTime,
            ]);

            return $webhookLog;

        } catch (Exception $e) {
            $executionTime = microtime(true) - $startTime;
            
            // Log failed webhook
            $webhookLog = WebhookLog::create([
                'webhook_type' => $data['webhook_type'] ?? 'product_sync',
                'url' => $data['url'],
                'method' => $data['method'] ?? 'POST',
                'headers' => $data['headers'] ?? [],
                'payload' => $data['payload'] ?? [],
                'response_status' => null,
                'response_body' => null,
                'response_headers' => null,
                'execution_time' => $executionTime,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'retry_count' => 0,
                'user_id' => $data['user_id'] ?? null,
                'campaign_id' => $data['campaign_id'] ?? null,
            ]);

            Log::error('Webhook failed', [
                'webhook_id' => $webhookLog->id,
                'url' => $data['url'],
                'error' => $e->getMessage(),
                'execution_time' => $executionTime,
            ]);

            return $webhookLog;
        }
    }

    /**
     * Make HTTP request and return response data
     */
    private function makeHttpRequest(array $data): array
    {
        $method = strtoupper($data['method'] ?? 'POST');
        $url = $data['url'];
        $headers = $data['headers'] ?? [];
        $payload = $data['payload'] ?? [];

        // Add default headers if not provided
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/json';
        }

        if (!isset($headers['User-Agent'])) {
            $headers['User-Agent'] = 'ApnaCrowdfunding-Webhook/1.0';
        }

        // Make the request
        $response = Http::withHeaders($headers)
            ->timeout(30)
            ->send($method, $url, $payload);

        return [
            'status' => $response->status(),
            'body' => $response->body(),
            'headers' => $response->headers(),
        ];
    }

    /**
     * Retry a failed webhook
     */
    public function retryWebhook(WebhookLog $webhookLog): WebhookLog
    {
        $data = [
            'webhook_type' => $webhookLog->webhook_type,
            'url' => $webhookLog->url,
            'method' => $webhookLog->method,
            'headers' => $webhookLog->headers,
            'payload' => $webhookLog->payload,
            'user_id' => $webhookLog->user_id,
            'campaign_id' => $webhookLog->campaign_id,
        ];

        // Update retry count
        $webhookLog->update([
            'retry_count' => $webhookLog->retry_count + 1,
            'status' => 'retrying',
        ]);

        // Execute the retry
        return $this->logWebhook($data);
    }

    /**
     * Get webhook statistics
     */
    public function getStatistics(): array
    {
        $total = WebhookLog::count();
        $successful = WebhookLog::successful()->count();
        $failed = WebhookLog::failed()->count();
        $pending = WebhookLog::pending()->count();

        return [
            'total' => $total,
            'successful' => $successful,
            'failed' => $failed,
            'pending' => $pending,
            'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0,
            'failure_rate' => $total > 0 ? round(($failed / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get recent webhook logs
     */
    public function getRecentLogs(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return WebhookLog::with(['user', 'campaign'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Clean up old webhook logs
     */
    public function cleanupOldLogs(int $days = 30): int
    {
        return WebhookLog::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Get webhook logs by type
     */
    public function getLogsByType(string $type, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return WebhookLog::byType($type)
            ->with(['user', 'campaign'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get detailed webhook log
     */
    public function getDetailedLog(int $id): ?WebhookLog
    {
        return WebhookLog::with(['user', 'campaign'])->find($id);
    }
}

