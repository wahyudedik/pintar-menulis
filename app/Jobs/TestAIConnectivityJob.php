<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\GeminiService;

class TestAIConnectivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('🧪 AI Connectivity Test Started');
        
        try {
            $geminiService = app(GeminiService::class);
            
            // Simple test request
            $startTime = microtime(true);
            $result = $geminiService->generateCopywriting([
                'brief' => 'Test connectivity',
                'category' => 'quick_templates',
                'subcategory' => 'caption_instagram',
                'platform' => 'instagram',
                'tone' => 'casual',
                'mode' => 'simple',
                'user_id' => null, // System test
                '_health_check' => true, // Mark as health check
            ]);
            $endTime = microtime(true);
            
            $responseTime = round(($endTime - $startTime) * 1000, 2); // ms
            
            if (!empty($result)) {
                Log::info('✅ AI Connectivity Test: SUCCESS', [
                    'response_time' => $responseTime . 'ms',
                    'model' => $geminiService->getCurrentModel(),
                    'output_length' => strlen($result)
                ]);
                
                Cache::put('ai_connectivity_status', 'online', now()->addMinutes(5));
                Cache::put('ai_last_success', now()->toDateTimeString(), now()->addDay());
                Cache::put('ai_response_time', $responseTime, now()->addMinutes(5));
                
                // Track success rate
                $this->trackSuccess();
                
            } else {
                throw new \Exception('Empty response from AI');
            }
            
        } catch (\Exception $e) {
            Log::error('❌ AI Connectivity Test: FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Cache::put('ai_connectivity_status', 'offline', now()->addMinutes(5));
            Cache::put('ai_last_error', [
                'message' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], now()->addDay());
            
            // Track failure
            $this->trackFailure();
            
            // Alert if multiple consecutive failures
            $this->checkConsecutiveFailures();
        }
    }
    
    /**
     * Track successful test
     */
    protected function trackSuccess(): void
    {
        Cache::put('ai_consecutive_failures', 0, now()->addDay());
        
        $successCount = Cache::get('ai_success_count_today', 0);
        Cache::put('ai_success_count_today', $successCount + 1, now()->endOfDay());
    }
    
    /**
     * Track failed test
     */
    protected function trackFailure(): void
    {
        $failures = Cache::get('ai_consecutive_failures', 0);
        Cache::put('ai_consecutive_failures', $failures + 1, now()->addDay());
        
        $failureCount = Cache::get('ai_failure_count_today', 0);
        Cache::put('ai_failure_count_today', $failureCount + 1, now()->endOfDay());
    }
    
    /**
     * Check for consecutive failures and alert
     */
    protected function checkConsecutiveFailures(): void
    {
        $failures = Cache::get('ai_consecutive_failures', 0);
        
        if ($failures >= 3) {
            Log::critical('🚨 AI SYSTEM DOWN: 3+ consecutive failures detected!');
            
            // Store critical alert
            $notifications = Cache::get('ai_health_notifications', []);
            $notifications[] = [
                'title' => '🚨 AI System Down',
                'message' => "AI connectivity test failed {$failures} times in a row. System may be down. Immediate action required!",
                'level' => 'emergency',
                'timestamp' => now()->toDateTimeString(),
            ];
            Cache::put('ai_health_notifications', $notifications, now()->addDays(7));
            
            // TODO: Send emergency notification
            // - Email to admin
            // - SMS to on-call engineer
            // - Slack alert
            // - PagerDuty incident
        } elseif ($failures >= 2) {
            Log::warning('⚠️ AI connectivity unstable: 2 consecutive failures');
        }
    }
}
