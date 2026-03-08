<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Services\ModelFallbackManager;
use App\Services\GeminiService;

class MonitorAIHealthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('🔍 AI Health Check Started');
        
        $fallbackManager = app(ModelFallbackManager::class);
        $stats = $fallbackManager->getUsageStats();
        $tierInfo = $stats['_meta'] ?? [];
        unset($stats['_meta']);
        
        $issues = [];
        $warnings = [];
        $criticalIssues = [];
        
        // Check each model
        foreach ($stats as $modelName => $stat) {
            // Critical: Model at 90%+ capacity
            if ($stat['rpm']['percentage'] >= 90) {
                $criticalIssues[] = "{$stat['display_name']}: RPM at {$stat['rpm']['percentage']}% (CRITICAL)";
            }
            
            if ($stat['rpd']['percentage'] >= 90) {
                $criticalIssues[] = "{$stat['display_name']}: RPD at {$stat['rpd']['percentage']}% (CRITICAL)";
            }
            
            // Warning: Model at 70%+ capacity
            if ($stat['rpm']['percentage'] >= 70 && $stat['rpm']['percentage'] < 90) {
                $warnings[] = "{$stat['display_name']}: RPM at {$stat['rpm']['percentage']}% (WARNING)";
            }
            
            if ($stat['rpd']['percentage'] >= 70 && $stat['rpd']['percentage'] < 90) {
                $warnings[] = "{$stat['display_name']}: RPD at {$stat['rpd']['percentage']}% (WARNING)";
            }
            
            // Issue: Model unavailable
            if (!$stat['available']) {
                $issues[] = "{$stat['display_name']}: UNAVAILABLE (Rate Limited)";
            }
        }
        
        // Check if all models are unavailable (DISASTER!)
        $allUnavailable = true;
        foreach ($stats as $stat) {
            if ($stat['available']) {
                $allUnavailable = false;
                break;
            }
        }
        
        if ($allUnavailable) {
            $this->handleDisaster($tierInfo);
            return;
        }
        
        // Handle critical issues
        if (!empty($criticalIssues)) {
            $this->handleCriticalIssues($criticalIssues, $tierInfo);
        }
        
        // Handle warnings
        if (!empty($warnings)) {
            $this->handleWarnings($warnings, $tierInfo);
        }
        
        // Handle issues
        if (!empty($issues)) {
            $this->handleIssues($issues, $tierInfo);
        }
        
        // All good!
        if (empty($criticalIssues) && empty($warnings) && empty($issues)) {
            Log::info('✅ AI Health Check: All systems operational');
            Cache::put('ai_health_status', 'healthy', now()->addMinutes(5));
        }
        
        // Store health metrics
        $this->storeHealthMetrics($stats, $tierInfo);
    }
    
    /**
     * Handle disaster scenario (all models unavailable)
     */
    protected function handleDisaster(array $tierInfo): void
    {
        Log::critical('🚨 DISASTER: ALL AI MODELS UNAVAILABLE!');
        
        Cache::put('ai_health_status', 'disaster', now()->addMinutes(5));
        
        // Try to upgrade tier immediately
        $fallbackManager = app(ModelFallbackManager::class);
        $currentTier = $tierInfo['current_tier'] ?? 'free';
        
        if ($currentTier === 'free') {
            Log::info('🔄 Attempting emergency tier upgrade...');
            $fallbackManager->markHighVolumeSuccess();
            
            // Notify admin
            $this->notifyAdmin(
                '🚨 CRITICAL: All AI models unavailable',
                'All free tier models are exhausted. System attempted automatic upgrade to paid tier. Please check immediately!',
                'critical'
            );
        } else {
            // Already on paid tier and still failing - SERIOUS PROBLEM
            $this->notifyAdmin(
                '🚨 EMERGENCY: Paid tier exhausted',
                'Even paid tier models are unavailable. This is a serious capacity issue. Immediate action required!',
                'emergency'
            );
        }
    }
    
    /**
     * Handle critical issues (90%+ capacity)
     */
    protected function handleCriticalIssues(array $issues, array $tierInfo): void
    {
        Log::warning('⚠️ CRITICAL: AI models approaching limit', ['issues' => $issues]);
        
        Cache::put('ai_health_status', 'critical', now()->addMinutes(5));
        
        $currentTier = $tierInfo['current_tier'] ?? 'free';
        
        // If on free tier, prepare for upgrade
        if ($currentTier === 'free') {
            Log::info('📊 Free tier nearing limit. Paid tier will activate soon.');
            
            // Notify admin (non-urgent)
            $this->notifyAdmin(
                '⚠️ AI Usage High',
                'Free tier models are at 90%+ capacity. Paid tier will activate automatically if needed. No action required.',
                'warning'
            );
        } else {
            // On paid tier and still critical - monitor closely
            Log::warning('📊 Paid tier at high capacity. Monitor closely.');
            
            $this->notifyAdmin(
                '⚠️ High AI Usage (Paid Tier)',
                'Paid tier models are at 90%+ capacity. System is handling load but costs may be high.',
                'info'
            );
        }
    }
    
    /**
     * Handle warnings (70%+ capacity)
     */
    protected function handleWarnings(array $warnings, array $tierInfo): void
    {
        Log::info('ℹ️ AI models at moderate usage', ['warnings' => $warnings]);
        
        Cache::put('ai_health_status', 'warning', now()->addMinutes(5));
        
        // Just log, no notification needed
    }
    
    /**
     * Handle issues (models unavailable)
     */
    protected function handleIssues(array $issues, array $tierInfo): void
    {
        Log::info('ℹ️ Some AI models unavailable (fallback active)', ['issues' => $issues]);
        
        Cache::put('ai_health_status', 'degraded', now()->addMinutes(5));
        
        // Fallback is working, no immediate action needed
    }
    
    /**
     * Store health metrics for analytics
     */
    protected function storeHealthMetrics(array $stats, array $tierInfo): void
    {
        $metrics = [
            'timestamp' => now()->toDateTimeString(),
            'tier' => $tierInfo['current_tier'] ?? 'unknown',
            'models_available' => 0,
            'models_total' => count($stats),
            'total_rpm_used' => 0,
            'total_rpm_limit' => 0,
            'total_rpd_used' => 0,
            'total_rpd_limit' => 0,
        ];
        
        foreach ($stats as $stat) {
            if ($stat['available']) {
                $metrics['models_available']++;
            }
            $metrics['total_rpm_used'] += $stat['rpm']['current'];
            $metrics['total_rpm_limit'] += $stat['rpm']['limit'];
            $metrics['total_rpd_used'] += $stat['rpd']['current'];
            if ($stat['rpd']['limit'] < 999999) {
                $metrics['total_rpd_limit'] += $stat['rpd']['limit'];
            }
        }
        
        // Store in cache (last 60 data points = 1 hour if running every minute)
        $history = Cache::get('ai_health_history', []);
        $history[] = $metrics;
        
        // Keep only last 60 entries
        if (count($history) > 60) {
            $history = array_slice($history, -60);
        }
        
        Cache::put('ai_health_history', $history, now()->addHours(2));
        
        Log::info('📊 Health metrics stored', [
            'available' => $metrics['models_available'] . '/' . $metrics['models_total'],
            'rpm_usage' => round(($metrics['total_rpm_used'] / $metrics['total_rpm_limit']) * 100, 1) . '%',
            'tier' => $metrics['tier']
        ]);
    }
    
    /**
     * Notify admin about issues
     */
    protected function notifyAdmin(string $title, string $message, string $level): void
    {
        // Store notification in cache for admin dashboard
        $notifications = Cache::get('ai_health_notifications', []);
        $notifications[] = [
            'title' => $title,
            'message' => $message,
            'level' => $level,
            'timestamp' => now()->toDateTimeString(),
        ];
        
        // Keep only last 50 notifications
        if (count($notifications) > 50) {
            $notifications = array_slice($notifications, -50);
        }
        
        Cache::put('ai_health_notifications', $notifications, now()->addDays(7));
        
        Log::info('📧 Admin notification created', [
            'title' => $title,
            'level' => $level
        ]);
        
        // TODO: Send email/SMS/Slack notification to admin
        // Notification::route('mail', config('app.admin_email'))
        //     ->notify(new AIHealthAlert($title, $message, $level));
    }
}
