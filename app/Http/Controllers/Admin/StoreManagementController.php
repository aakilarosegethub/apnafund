<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StoreManagementController extends Controller
{
    public function index()
    {
        $pageTitle = 'Store Dashboard';
        
        // Get sync statistics
        $syncStats = $this->getSyncStatistics();
        
        return view('admin.store.dashboard', compact('pageTitle', 'syncStats'));
    }

    public function runCron(Request $request)
    {
        try {
            // Simulate cron job execution
            $response = $this->executeCronJob();
            
            if ($response['success']) {
                $toast[] = ['success', 'Cron job executed successfully!'];
                return response()->json([
                    'success' => true,
                    'message' => 'Cron job executed successfully!',
                    'data' => $response['data']
                ]);
            } else {
                $toast[] = ['error', 'Cron job failed to execute!'];
                return response()->json([
                    'success' => false,
                    'message' => 'Cron job failed to execute!',
                    'error' => $response['error']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Cron job execution failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while executing cron job!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getSyncStatistics()
    {
        // This would typically fetch from your database or external API
        // For now, returning mock data
        return [
            'total_products' => 1250,
            'synced_products' => 1180,
            'pending_products' => 70,
            'failed_products' => 0,
            'last_sync' => now()->subMinutes(15)->format('Y-m-d H:i:s'),
            'sync_percentage' => 94.4,
            'sync_status' => 'active'
        ];
    }

    private function executeCronJob()
    {
        try {
            // Simulate API call or database operations
            // In real implementation, this would call your actual sync logic
            
            // Simulate processing time
            sleep(2);
            
            // Mock response
            $newlySynced = rand(5, 15);
            $failed = rand(0, 2);
            
            return [
                'success' => true,
                'data' => [
                    'newly_synced' => $newlySynced,
                    'failed' => $failed,
                    'execution_time' => '2.3 seconds',
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getSyncStatus()
    {
        $syncStats = $this->getSyncStatistics();
        
        return response()->json([
            'success' => true,
            'data' => $syncStats
        ]);
    }
}
