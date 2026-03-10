<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\MonitorAIHealthJob;
use App\Jobs\TestAIConnectivityJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================
// AI HEALTH MONITORING SCHEDULE
// ============================================

// 🔍 Monitor AI health every minute (proactive monitoring)
Schedule::job(new MonitorAIHealthJob())
    ->everyMinute()
    ->name('ai-health-monitor')
    ->withoutOverlapping()
    ->onOneServer();

// 🧪 Test AI connectivity every 5 minutes (ensure system is working)
Schedule::job(new TestAIConnectivityJob())
    ->everyFiveMinutes()
    ->name('ai-connectivity-test')
    ->withoutOverlapping()
    ->onOneServer();

// 🧹 Clean up old health data daily at 2 AM
Schedule::call(function () {
    // Clean up old notifications (keep last 7 days)
    $notifications = \Illuminate\Support\Facades\Cache::get('ai_health_notifications', []);
    $cutoff = now()->subDays(7);
    $notifications = array_filter($notifications, function($notif) use ($cutoff) {
        return \Carbon\Carbon::parse($notif['timestamp'])->isAfter($cutoff);
    });
    \Illuminate\Support\Facades\Cache::put('ai_health_notifications', array_values($notifications), now()->addDays(7));
    
    // Reset daily counters
    \Illuminate\Support\Facades\Cache::forget('ai_success_count_today');
    \Illuminate\Support\Facades\Cache::forget('ai_failure_count_today');
    
    \Illuminate\Support\Facades\Log::info('🧹 AI health data cleanup completed');
})->dailyAt('02:00')->name('ai-health-cleanup');

// 📊 Generate daily health report at 8 AM
Schedule::call(function () {
    $successCount = \Illuminate\Support\Facades\Cache::get('ai_success_count_today', 0);
    $failureCount = \Illuminate\Support\Facades\Cache::get('ai_failure_count_today', 0);
    $total = $successCount + $failureCount;
    $successRate = $total > 0 ? round(($successCount / $total) * 100, 2) : 0;
    
    \Illuminate\Support\Facades\Log::info('📊 Daily AI Health Report', [
        'date' => now()->toDateString(),
        'success_count' => $successCount,
        'failure_count' => $failureCount,
        'success_rate' => $successRate . '%',
        'status' => $successRate >= 99 ? 'Excellent' : ($successRate >= 95 ? 'Good' : 'Needs Attention')
    ]);
    
    // TODO: Send daily report email to admin
})->dailyAt('08:00')->name('ai-daily-report');

// 🔄 Reset model usage stats at midnight (fresh start each day)
Schedule::call(function () {
    // Note: RPM counters reset automatically (1 minute TTL)
    // Note: RPD counters reset automatically (1 day TTL)
    // This is just a safety cleanup
    
    \Illuminate\Support\Facades\Log::info('🔄 Daily model usage reset (automatic via TTL)');
})->dailyAt('00:00')->name('ai-usage-reset');

// ============================================
// ML SUGGESTIONS SCHEDULE
// ============================================

// 🤖 Update ML suggestions every day at 6 AM (fresh trending data)
Schedule::command('ml:update-suggestions')
    ->dailyAt('06:00')
    ->name('ml-suggestions-daily')
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/ml-suggestions.log'));

// 🔄 Force update ML suggestions every Sunday at 3 AM (weekly refresh)
Schedule::command('ml:update-suggestions --force')
    ->weeklyOn(0, '03:00')
    ->name('ml-suggestions-weekly')
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/ml-suggestions-weekly.log'));

// ============================================
// ML TRAINING SCHEDULE
// ============================================

// 🤖 Run ML training daily at 3 AM (learn from yesterday's analytics)
Schedule::command('ml:train-daily')
    ->dailyAt('03:00')
    ->name('ml-daily-training')
    ->withoutOverlapping()
    ->onOneServer();

// ============================================
// ARTICLE GENERATION SCHEDULE
// ============================================

// 📰 Generate 1 article daily at midnight (00:00)
// Rotation pattern: Day 1 = Industry, Day 2 = Tips, Day 3 = Quote (repeats every 3 days)
Schedule::call(function () {
    $service = app(\App\Services\ArticleGeneratorService::class);
    $result = $service->generateDailyArticles();
    
    if ($result['success'] > 0) {
        \Illuminate\Support\Facades\Log::info('✅ Daily article generated successfully', $result);
    } else {
        \Illuminate\Support\Facades\Log::error('❌ Failed to generate daily article', $result);
    }
})->dailyAt('00:00')->name('articles-generate-daily')->withoutOverlapping()->onOneServer();
