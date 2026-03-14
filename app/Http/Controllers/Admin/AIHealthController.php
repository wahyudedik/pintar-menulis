<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Services\ModelFallbackManager;
use App\Services\GeminiService;

class AIHealthController extends Controller
{
    /**
     * Show AI health dashboard
     */
    public function index()
    {
        $healthStatus = Cache::get('ai_health_status', 'unknown');
        $connectivityStatus = Cache::get('ai_connectivity_status', 'unknown');
        $lastSuccess = Cache::get('ai_last_success', 'Never');
        $lastError = Cache::get('ai_last_error', null);
        $responseTime = Cache::get('ai_response_time', 0);
        $consecutiveFailures = Cache::get('ai_consecutive_failures', 0);
        
        // Get today's stats
        $successCount = Cache::get('ai_success_count_today', 0);
        $failureCount = Cache::get('ai_failure_count_today', 0);
        $total = $successCount + $failureCount;
        $successRate = $total > 0 ? round(($successCount / $total) * 100, 2) : 0;
        
        // Get model stats
        $fallbackManager = app(ModelFallbackManager::class);
        $modelStats = $fallbackManager->getUsageStats();
        $tierInfo = $modelStats['_meta'] ?? [];
        unset($modelStats['_meta']);
        
        // Get health history
        $healthHistory = Cache::get('ai_health_history', []);
        
        // Get notifications
        $notifications = Cache::get('ai_health_notifications', []);
        $notifications = array_reverse($notifications); // Latest first
        
        return view('admin.ai-health.index', compact(
            'healthStatus',
            'connectivityStatus',
            'lastSuccess',
            'lastError',
            'responseTime',
            'consecutiveFailures',
            'successCount',
            'failureCount',
            'successRate',
            'modelStats',
            'tierInfo',
            'healthHistory',
            'notifications'
        ));
    }
    
    /**
     * Get health status (AJAX)
     */
    public function status()
    {
        $healthStatus = Cache::get('ai_health_status', 'unknown');
        $connectivityStatus = Cache::get('ai_connectivity_status', 'unknown');
        $responseTime = Cache::get('ai_response_time', 0);
        $consecutiveFailures = Cache::get('ai_consecutive_failures', 0);
        
        $successCount = Cache::get('ai_success_count_today', 0);
        $failureCount = Cache::get('ai_failure_count_today', 0);
        $total = $successCount + $failureCount;
        $successRate = $total > 0 ? round(($successCount / $total) * 100, 2) : 0;
        
        $fallbackManager = app(ModelFallbackManager::class);
        $modelStats = $fallbackManager->getUsageStats();
        
        return response()->json([
            'success' => true,
            'health_status' => $healthStatus,
            'connectivity_status' => $connectivityStatus,
            'response_time' => $responseTime,
            'consecutive_failures' => $consecutiveFailures,
            'success_rate' => $successRate,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'model_stats' => $modelStats,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
    
    /**
     * Force health check (manual trigger)
     */
    public function forceCheck()
    {
        // Dispatch jobs immediately
        \App\Jobs\MonitorAIHealthJob::dispatch();
        
        return response()->json([
            'success' => true,
            'message' => 'Health check jobs dispatched. Results will be available in ~30 seconds.'
        ]);
    }
    
    /**
     * Clear health data (for testing)
     */
    public function clearData()
    {
        Cache::forget('ai_health_status');
        Cache::forget('ai_connectivity_status');
        Cache::forget('ai_last_success');
        Cache::forget('ai_last_error');
        Cache::forget('ai_response_time');
        Cache::forget('ai_consecutive_failures');
        Cache::forget('ai_success_count_today');
        Cache::forget('ai_failure_count_today');
        Cache::forget('ai_health_history');
        Cache::forget('ai_health_notifications');
        
        // Also reset model stats
        $geminiService = app(GeminiService::class);
        $geminiService->resetModelStats();
        
        return response()->json([
            'success' => true,
            'message' => 'All health data cleared'
        ]);
    }
    
    /**
     * Get health history chart data
     */
    public function chartData()
    {
        $history = Cache::get('ai_health_history', []);
        
        // Format for chart
        $labels = [];
        $rpmUsage = [];
        $rpdUsage = [];
        $modelsAvailable = [];
        
        foreach ($history as $entry) {
            $labels[] = \Carbon\Carbon::parse($entry['timestamp'])->format('H:i');
            
            $rpmPercent = $entry['total_rpm_limit'] > 0 
                ? round(($entry['total_rpm_used'] / $entry['total_rpm_limit']) * 100, 1)
                : 0;
            $rpmUsage[] = $rpmPercent;
            
            $rpdPercent = $entry['total_rpd_limit'] > 0 
                ? round(($entry['total_rpd_used'] / $entry['total_rpd_limit']) * 100, 1)
                : 0;
            $rpdUsage[] = $rpdPercent;
            
            $modelsAvailable[] = $entry['models_available'];
        }
        
        return response()->json([
            'success' => true,
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'RPM Usage (%)',
                    'data' => $rpmUsage,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'tension' => 0.1
                ],
                [
                    'label' => 'RPD Usage (%)',
                    'data' => $rpdUsage,
                    'borderColor' => 'rgb(255, 99, 132)',
                    'tension' => 0.1
                ],
                [
                    'label' => 'Models Available',
                    'data' => $modelsAvailable,
                    'borderColor' => 'rgb(54, 162, 235)',
                    'tension' => 0.1
                ]
            ]
        ]);
    }
}
