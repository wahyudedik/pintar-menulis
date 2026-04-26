<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\MonitorAIHealthJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================
// AI HEALTH MONITORING SCHEDULE
// ============================================

// Monitor AI health every minute
Schedule::job(new MonitorAIHealthJob())
    ->everyMinute()
    ->name('ai-health-monitor')
    ->withoutOverlapping()
    ->onOneServer();

// Clean up old health data daily at 2 AM
Schedule::call(function () {
    $notifications = \Illuminate\Support\Facades\Cache::get('ai_health_notifications', []);
    $cutoff = now()->subDays(7);
    $notifications = array_filter($notifications, function ($notif) use ($cutoff) {
        return \Carbon\Carbon::parse($notif['timestamp'])->isAfter($cutoff);
    });
    \Illuminate\Support\Facades\Cache::put('ai_health_notifications', array_values($notifications), now()->addDays(7));
    \Illuminate\Support\Facades\Cache::forget('ai_success_count_today');
    \Illuminate\Support\Facades\Cache::forget('ai_failure_count_today');
    \Illuminate\Support\Facades\Log::info('AI health data cleanup completed');
})->dailyAt('02:00')->name('ai-health-cleanup');

// ============================================
// ML TRAINING SCHEDULE
// ============================================

// Run ML training daily at 3 AM
Schedule::command('ml:train-daily')
    ->dailyAt('03:00')
    ->name('ml-daily-training')
    ->withoutOverlapping()
    ->onOneServer();

// ML data cleanup weekly on Sunday at 3 AM
Schedule::command('ml:manage cleanup')
    ->weeklyOn(0, '03:00')
    ->name('ml-data-cleanup')
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/ml-cleanup.log'));

// ML optimization daily at 4 AM
Schedule::command('ml:manage optimize')
    ->dailyAt('04:00')
    ->name('ml-data-optimize')
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/ml-optimize.log'));

// ============================================
// QUEUE MONITORING
// ============================================

// Queue monitoring every 5 minutes
Schedule::command('queue:health-monitor')
    ->everyFiveMinutes()
    ->name('queue-monitoring')
    ->runInBackground()
    ->onOneServer();

// Failed job retry every 30 minutes
Schedule::command('queue:retry all')
    ->everyThirtyMinutes()
    ->name('queue-retry-failed')
    ->runInBackground()
    ->onOneServer();
