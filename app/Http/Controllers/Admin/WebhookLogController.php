<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataLog;
use App\Models\WebhookLog;
use App\Services\UnifiedWebhookLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookLogController extends Controller
{
    protected $webhookLogger;

    public function __construct(UnifiedWebhookLoggerService $webhookLogger)
    {
        $this->webhookLogger = $webhookLogger;
    }

    /**
     * Display webhook logs dashboard
     */
    public function index(Request $request)
    {
        $pageTitle = 'Webhook Logs Dashboard';
        
        // Get statistics
        $statistics = $this->webhookLogger->getWebhookStatistics();
        
        // Get recent logs
        $recentLogs = $this->webhookLogger->getRecentWebhookLogs(50);
        
        // Filter parameters
        $type = $request->get('type');
        $status = $request->get('status');
        $gateway = $request->get('gateway');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Build query for DataLogs
        $dataLogsQuery = DataLog::query();
        if ($type) {
            $dataLogsQuery->where('endpoint', 'like', "%{$type}%");
        }
        if ($status) {
            $dataLogsQuery->where('status', $status);
        }
        if ($dateFrom) {
            $dataLogsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $dataLogsQuery->whereDate('created_at', '<=', $dateTo);
        }
        
        // Build query for WebhookLogs
        $webhookLogsQuery = WebhookLog::query();
        if ($type) {
            $webhookLogsQuery->where('webhook_type', 'like', "%{$type}%");
        }
        if ($status) {
            $webhookLogsQuery->where('status', $status);
        }
        if ($gateway) {
            $webhookLogsQuery->where('payload->gateway', $gateway);
        }
        if ($dateFrom) {
            $webhookLogsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $webhookLogsQuery->whereDate('created_at', '<=', $dateTo);
        }
        
        $dataLogs = $dataLogsQuery->latest()->paginate(20, ['*'], 'data_logs_page');
        $webhookLogs = $webhookLogsQuery->latest()->paginate(20, ['*'], 'webhook_logs_page');
        
        return view('admin.webhook_logs.index', compact(
            'pageTitle',
            'statistics',
            'recentLogs',
            'dataLogs',
            'webhookLogs',
            'type',
            'status',
            'gateway',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Show detailed webhook log
     */
    public function show($id, $type = 'data_log')
    {
        $pageTitle = 'Webhook Log Details';
        
        if ($type === 'webhook_log') {
            $log = WebhookLog::with(['user', 'campaign'])->findOrFail($id);
        } else {
            $log = DataLog::findOrFail($id);
        }
        
        return view('admin.webhook_logs.show', compact('pageTitle', 'log', 'type'));
    }

    /**
     * Get webhook statistics via AJAX
     */
    public function statistics()
    {
        try {
            $statistics = $this->webhookLogger->getWebhookStatistics();
            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get webhook statistics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics'
            ], 500);
        }
    }

    /**
     * Retry failed webhook
     */
    public function retry($id)
    {
        try {
            $webhookLog = WebhookLog::findOrFail($id);
            
            if ($webhookLog->status === 'success') {
                return response()->json([
                    'success' => false,
                    'message' => 'Webhook already successful'
                ], 400);
            }
            
            // Use the existing WebhookLoggerService to retry
            $webhookLoggerService = new \App\Services\WebhookLoggerService();
            $retryResult = $webhookLoggerService->retryWebhook($webhookLog);
            
            return response()->json([
                'success' => true,
                'message' => 'Webhook retry initiated',
                'data' => [
                    'new_webhook_id' => $retryResult->id,
                    'status' => $retryResult->status
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retry webhook', [
                'webhook_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retry webhook'
            ], 500);
        }
    }

    /**
     * Clean up old webhook logs
     */
    public function cleanup(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $result = $this->webhookLogger->cleanupOldLogs($days);
            
            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$result['total_deleted']} old webhook logs",
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to cleanup webhook logs', [
                'days' => $request->get('days', 30),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup webhook logs'
            ], 500);
        }
    }

    /**
     * Export webhook logs
     */
    public function export(Request $request)
    {
        try {
            $type = $request->get('type', 'both'); // data_log, webhook_log, or both
            $format = $request->get('format', 'csv'); // csv or json
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            
            $data = [];
            
            if ($type === 'data_log' || $type === 'both') {
                $dataLogsQuery = DataLog::query();
                if ($dateFrom) {
                    $dataLogsQuery->whereDate('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $dataLogsQuery->whereDate('created_at', '<=', $dateTo);
                }
                
                $dataLogs = $dataLogsQuery->latest()->get();
                $data['data_logs'] = $dataLogs->toArray();
            }
            
            if ($type === 'webhook_log' || $type === 'both') {
                $webhookLogsQuery = WebhookLog::query();
                if ($dateFrom) {
                    $webhookLogsQuery->whereDate('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $webhookLogsQuery->whereDate('created_at', '<=', $dateTo);
                }
                
                $webhookLogs = $webhookLogsQuery->latest()->get();
                $data['webhook_logs'] = $webhookLogs->toArray();
            }
            
            if ($format === 'json') {
                return response()->json($data);
            } else {
                // CSV export logic would go here
                return response()->json([
                    'success' => true,
                    'message' => 'CSV export feature coming soon',
                    'data' => $data
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to export webhook logs', [
                'type' => $request->get('type'),
                'format' => $request->get('format'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to export webhook logs'
            ], 500);
        }
    }

    /**
     * Get webhook logs by gateway
     */
    public function byGateway($gateway)
    {
        try {
            $webhookLogs = WebhookLog::where('payload->gateway', $gateway)
                ->with(['user', 'campaign'])
                ->latest()
                ->paginate(20);
            
            return response()->json([
                'success' => true,
                'data' => $webhookLogs
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get webhook logs by gateway', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get webhook logs'
            ], 500);
        }
    }

    /**
     * Get webhook logs by status
     */
    public function byStatus($status)
    {
        try {
            $dataLogs = DataLog::where('status', $status)->latest()->paginate(20);
            $webhookLogs = WebhookLog::where('status', $status)
                ->with(['user', 'campaign'])
                ->latest()
                ->paginate(20);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'data_logs' => $dataLogs,
                    'webhook_logs' => $webhookLogs
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get webhook logs by status', [
                'status' => $status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get webhook logs'
            ], 500);
        }
    }
}
