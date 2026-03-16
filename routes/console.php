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

// 🔍 Monitor AI health every minute (proactive monitoring)
Schedule::job(new MonitorAIHealthJob())
    ->everyMinute()
    ->name('ai-health-monitor')
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
Schedule::command('articles:generate-daily')
    ->dailyAt('00:00')
    ->name('articles-generate-daily')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/articles-generate.log'));

// ============================================
// TRENDING HASHTAGS SCHEDULE
// ============================================

// 🏷️ Update trending hashtags every Sunday at 4 AM (weekly refresh)
Schedule::command('hashtags:update')
    ->weeklyOn(0, '04:00')
    ->name('hashtags-weekly-update')
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/hashtags-update.log'));

// 🔄 Force update trending hashtags on 1st of every month at 5 AM (monthly refresh)
Schedule::command('hashtags:update --force')
    ->monthlyOn(1, '05:00')
    ->name('hashtags-monthly-update')
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/hashtags-monthly.log'));

// ============================================
// ML-OPTIMIZED COMPETITOR ANALYSIS SCHEDULE
// ============================================

// 🤖 Smart competitor refresh - AI decides who needs updating (every hour)
Schedule::command('competitor:refresh-scheduled --type=smart')
    ->hourly()
    ->name('competitor-smart-refresh')
    ->withoutOverlapping(60) // Prevent overlapping runs
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/competitor-smart-refresh.log'));

// ⚡ High priority competitors - every 6 hours
Schedule::command('competitor:refresh-scheduled --type=high-priority')
    ->everySixHours()
    ->name('competitor-high-priority')
    ->withoutOverlapping(30)
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/competitor-high-priority.log'));

// 🔄 Full refresh - once daily at 2 AM (off-peak hours)
Schedule::command('competitor:refresh-scheduled --type=all --batch-size=100')
    ->dailyAt('02:00')
    ->name('competitor-full-refresh')
    ->withoutOverlapping(180) // 3 hour max runtime
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/competitor-full-refresh.log'));

// 🧹 ML data cleanup - weekly on Sunday at 3 AM
Schedule::command('ml:manage cleanup')
    ->weeklyOn(0, '03:00')
    ->name('ml-data-cleanup')
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/ml-cleanup.log'));

// ⚡ ML optimization - daily at 4 AM
Schedule::command('ml:manage optimize')
    ->dailyAt('04:00')
    ->name('ml-data-optimize')
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/ml-optimize.log'));

// 📊 Queue monitoring - every 5 minutes
Schedule::command('queue:health-monitor')
    ->everyFiveMinutes()
    ->name('queue-monitoring')
    ->runInBackground()
    ->onOneServer();

// 🔄 Failed job retry - every 30 minutes
Schedule::command('queue:retry all')
    ->everyThirtyMinutes()
    ->name('queue-retry-failed')
    ->runInBackground()
    ->onOneServer();

// 📈 ML statistics report - daily at 8 AM
Schedule::call(function () {
    $mlManager = app(\App\Services\MLDataManagerService::class);
    $stats = $mlManager->getMLStatistics();
    
    \Illuminate\Support\Facades\Log::info('📈 Daily ML Statistics Report', [
        'date' => now()->toDateString(),
        'total_profiles_cached' => $stats['total_profiles_cached'],
        'api_calls_saved' => $stats['api_calls_saved'],
        'cache_efficiency' => $stats['cache_efficiency'] . '%',
        'estimated_cost_savings' => '$' . number_format($stats['api_calls_saved'] * 0.01, 2)
    ]);
})->dailyAt('08:00')->name('ml-daily-report');

// ============================================
// LEGACY COMPETITOR ANALYSIS SCHEDULE (REPLACED BY ML-OPTIMIZED)
// ============================================

// 🔍 Analyze competitors every 6 hours (4 times daily)
Schedule::command('competitors:analyze')
    ->everySixHours()
    ->name('competitors-analyze-regular')
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/competitors-analysis.log'));

// 👀 Monitor competitors for new activities every 30 minutes
Schedule::command('competitors:monitor')
    ->everyThirtyMinutes()
    ->name('competitors-monitor-activities')
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/competitors-monitoring.log'));

// 🚀 Deep analysis for high-priority competitors daily at 7 AM
Schedule::command('competitors:analyze --force')
    ->dailyAt('07:00')
    ->name('competitors-deep-analysis')
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/competitors-deep-analysis.log'));

// 🧹 Clean up old competitor alerts weekly on Sunday at 1 AM
Schedule::call(function () {
    $deletedCount = \App\Models\CompetitorAlert::where('triggered_at', '<', now()->subDays(30))->delete();
    \Illuminate\Support\Facades\Log::info("🧹 Cleaned up {$deletedCount} old competitor alerts");
})->weeklyOn(0, '01:00')->name('competitors-cleanup');

// 📊 Generate weekly competitor insights report every Monday at 9 AM
Schedule::call(function () {
    $activeCompetitors = \App\Models\Competitor::where('is_active', true)->count();
    $weeklyAlerts = \App\Models\CompetitorAlert::where('triggered_at', '>=', now()->subWeek())->count();
    $highPriorityGaps = \App\Models\CompetitorContentGap::where('priority', '>=', 8)
        ->where('is_implemented', false)
        ->count();
    
    \Illuminate\Support\Facades\Log::info('📊 Weekly Competitor Insights Report', [
        'week_ending' => now()->toDateString(),
        'active_competitors' => $activeCompetitors,
        'alerts_generated' => $weeklyAlerts,
        'high_priority_opportunities' => $highPriorityGaps,
        'analysis_frequency' => '6 hours',
        'monitoring_frequency' => '30 minutes'
    ]);
})->weeklyOn(1, '09:00')->name('competitors-weekly-report');

// ============================================
// WHATSAPP INTEGRATION SCHEDULE
// ============================================

// 🌅 Send daily content ideas via WhatsApp at 8 AM
Schedule::call(function () {
    $whatsappService = app(\App\Services\WhatsAppService::class);
    $userIntegrationService = app(\App\Services\WhatsAppUserIntegrationService::class);
    
    // Check if WhatsApp service is enabled
    if (!config('services.whatsapp.enabled')) {
        \Illuminate\Support\Facades\Log::warning('WhatsApp service is disabled');
        return;
    }

    // Check device status
    $deviceStatus = $whatsappService->getDeviceStatus();
    if (!$deviceStatus['success'] || !($deviceStatus['connected'] ?? false)) {
        \Illuminate\Support\Facades\Log::error('WhatsApp device not connected for daily broadcast', $deviceStatus);
        return;
    }

    // Get subscribers for daily content
    $subscribers = \App\Models\WhatsAppSubscription::getDailyContentSubscribers();

    if ($subscribers->isEmpty()) {
        \Illuminate\Support\Facades\Log::info('No subscribers for daily WhatsApp content');
        return;
    }

    $successCount = 0;
    $failCount = 0;

    foreach ($subscribers as $subscription) {
        try {
            // Send personalized daily content
            $result = $userIntegrationService->sendPersonalizedMessage(
                $subscription->phone_number,
                'daily_content',
                ['subscription' => $subscription]
            );

            if ($result['success']) {
                $successCount++;
                $subscription->updateLastInteraction();

                // Record outgoing message
                \App\Models\WhatsAppMessage::recordOutgoing([
                    'phone_number' => $subscription->phone_number,
                    'message_type' => 'text',
                    'message_content' => 'Daily content ideas broadcast',
                    'user_id' => $subscription->user_id,
                    'metadata' => [
                        'broadcast_type' => 'daily_content',
                        'sent_at' => now()->toISOString(),
                        'scheduled_task' => true,
                        'personalized' => true
                    ],
                    'status' => 'sent'
                ]);
            } else {
                $failCount++;
            }

            // Add delay between messages to avoid rate limiting
            sleep(3); // 3 seconds delay

        } catch (\Exception $e) {
            $failCount++;
            \Illuminate\Support\Facades\Log::error('Daily WhatsApp broadcast failed for subscription', [
                'subscription_id' => $subscription->id,
                'phone' => $subscription->phone_number,
                'error' => $e->getMessage()
            ]);
        }
    }

    \Illuminate\Support\Facades\Log::info('Daily WhatsApp content broadcast completed', [
        'success_count' => $successCount,
        'fail_count' => $failCount,
        'total_subscribers' => $subscribers->count()
    ]);

})->dailyAt('08:00')->name('whatsapp-daily-content');

// 📱 Send weekly content planning reminder every Monday at 9 AM
Schedule::call(function () {
    $whatsappService = app(\App\Services\WhatsAppService::class);
    
    // Get active WhatsApp users (interacted in last 7 days)
    $activeNumbers = \App\Models\WhatsAppMessage::select('phone_number')
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('phone_number')
        ->pluck('phone_number')
        ->toArray();
    
    if (empty($activeNumbers)) {
        \Illuminate\Support\Facades\Log::info('📱 No active WhatsApp users for weekly reminder');
        return;
    }
    
    $message = "📅 *Weekly Content Planning Reminder*\n\n";
    $message .= "Halo! Sudah siap konten untuk minggu ini?\n\n";
    $message .= "💡 *Tips Minggu Ini:*\n";
    $message .= "• Buat 7 konten sekaligus di awal minggu\n";
    $message .= "• Mix antara edukasi, hiburan, dan promosi\n";
    $message .= "• Gunakan trending hashtag terbaru\n\n";
    $message .= "🤖 Ketik `daily` untuk ide konten hari ini!\n\n";
    $message .= "_Powered by Noteds AI_ ✨";
    
    $successCount = 0;
    foreach ($activeNumbers as $number) {
        try {
            $result = $whatsappService->sendMessage($number, $message);
            if ($result['success']) {
                $successCount++;
            }
            sleep(2); // 2 second delay between messages
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Weekly WhatsApp reminder failed', [
                'phone' => $number,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    \Illuminate\Support\Facades\Log::info('📱 Weekly WhatsApp reminder sent', [
        'total_sent' => $successCount,
        'total_numbers' => count($activeNumbers)
    ]);
    
})->weeklyOn(1, '09:00')->name('whatsapp-weekly-reminder');

// 🎯 Send trending topics notification every Tuesday and Friday at 10 AM
Schedule::call(function () {
    $whatsappService = app(\App\Services\WhatsAppService::class);
    
    // Get subscribers for trending notifications
    $subscribedNumbers = \App\Models\WhatsAppMessage::select('phone_number')
        ->where('created_at', '>=', now()->subDays(14))
        ->whereJsonContains('metadata->preferences->trending_notifications', true)
        ->orWhere('message_content', 'like', '%trending%')
        ->groupBy('phone_number')
        ->pluck('phone_number')
        ->toArray();
    
    if (empty($subscribedNumbers)) {
        // Fallback to active users
        $subscribedNumbers = \App\Models\WhatsAppMessage::select('phone_number')
            ->where('created_at', '>=', now()->subDays(3))
            ->groupBy('phone_number')
            ->limit(50) // Limit to prevent spam
            ->pluck('phone_number')
            ->toArray();
    }
    
    if (empty($subscribedNumbers)) {
        \Illuminate\Support\Facades\Log::info('🎯 No subscribers for trending topics notification');
        return;
    }
    
    $dayName = now()->format('l');
    $message = "🔥 *Trending Topics - {$dayName}*\n\n";
    $message .= "Topik yang lagi viral hari ini:\n\n";
    $message .= "1️⃣ Tips hemat listrik di musim kemarau\n";
    $message .= "2️⃣ Resep minuman segar untuk berbuka\n";
    $message .= "3️⃣ UMKM go digital dengan AI\n";
    $message .= "4️⃣ Tren fashion sustainable 2026\n\n";
    $message .= "💬 Balas dengan nomor untuk generate caption!\n";
    $message .= "Contoh: ketik \"1\" untuk topik pertama\n\n";
    $message .= "_Powered by Noteds AI_ ✨";
    
    $successCount = 0;
    foreach ($subscribedNumbers as $number) {
        try {
            $result = $whatsappService->sendMessage($number, $message);
            if ($result['success']) {
                $successCount++;
            }
            sleep(3); // 3 second delay between messages
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Trending topics WhatsApp notification failed', [
                'phone' => $number,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    \Illuminate\Support\Facades\Log::info('🎯 Trending topics notification sent', [
        'total_sent' => $successCount,
        'total_numbers' => count($subscribedNumbers)
    ]);
    
})->weeklyOn(2, '10:00')->name('whatsapp-trending-topics-tuesday');

// 🎯 Send trending topics notification Friday at 10 AM  
Schedule::call(function () {
    $whatsappService = app(\App\Services\WhatsAppService::class);
    
    // Get subscribers for trending notifications
    $subscribedNumbers = \App\Models\WhatsAppMessage::select('phone_number')
        ->where('created_at', '>=', now()->subDays(14))
        ->whereJsonContains('metadata->preferences->trending_notifications', true)
        ->orWhere('message_content', 'like', '%trending%')
        ->groupBy('phone_number')
        ->pluck('phone_number')
        ->toArray();
    
    if (empty($subscribedNumbers)) {
        // Fallback to active users
        $subscribedNumbers = \App\Models\WhatsAppMessage::select('phone_number')
            ->where('created_at', '>=', now()->subDays(3))
            ->groupBy('phone_number')
            ->limit(50) // Limit to prevent spam
            ->pluck('phone_number')
            ->toArray();
    }
    
    if (empty($subscribedNumbers)) {
        \Illuminate\Support\Facades\Log::info('🎯 No subscribers for trending topics notification');
        return;
    }
    
    $dayName = now()->format('l');
    $message = "🔥 *Trending Topics - {$dayName}*\n\n";
    $message .= "Topik yang lagi viral hari ini:\n\n";
    $message .= "1️⃣ Tips hemat listrik di musim kemarau\n";
    $message .= "2️⃣ Resep minuman segar untuk berbuka\n";
    $message .= "3️⃣ UMKM go digital dengan AI\n";
    $message .= "4️⃣ Tren fashion sustainable 2026\n\n";
    $message .= "💬 Balas dengan nomor untuk generate caption!\n";
    $message .= "Contoh: ketik \"1\" untuk topik pertama\n\n";
    $message .= "_Powered by Noteds AI_ ✨";
    
    $successCount = 0;
    foreach ($subscribedNumbers as $number) {
        try {
            $result = $whatsappService->sendMessage($number, $message);
            if ($result['success']) {
                $successCount++;
            }
            sleep(3); // 3 second delay between messages
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Trending topics WhatsApp notification failed', [
                'phone' => $number,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    \Illuminate\Support\Facades\Log::info('🎯 Trending topics notification sent', [
        'total_sent' => $successCount,
        'total_numbers' => count($subscribedNumbers)
    ]);
    
})->weeklyOn(5, '10:00')->name('whatsapp-trending-topics-friday');

// 🧹 Clean up old WhatsApp messages monthly (keep last 3 months)
Schedule::call(function () {
    $cutoffDate = now()->subMonths(3);
    $deletedCount = \App\Models\WhatsAppMessage::where('created_at', '<', $cutoffDate)->delete();
    
    \Illuminate\Support\Facades\Log::info('🧹 WhatsApp messages cleanup completed', [
        'deleted_count' => $deletedCount,
        'cutoff_date' => $cutoffDate->toDateString()
    ]);
    
})->monthlyOn(1, '01:00')->name('whatsapp-cleanup');

// 📊 WhatsApp usage statistics report daily at 11 PM
Schedule::call(function () {
    $today = now()->toDateString();
    
    $stats = [
        'date' => $today,
        'total_messages_today' => \App\Models\WhatsAppMessage::whereDate('created_at', $today)->count(),
        'incoming_messages' => \App\Models\WhatsAppMessage::whereDate('created_at', $today)->incoming()->count(),
        'outgoing_messages' => \App\Models\WhatsAppMessage::whereDate('created_at', $today)->outgoing()->count(),
        'unique_users' => \App\Models\WhatsAppMessage::whereDate('created_at', $today)->distinct('phone_number')->count(),
        'image_messages' => \App\Models\WhatsAppMessage::whereDate('created_at', $today)->where('message_type', 'image')->count(),
        'voice_messages' => \App\Models\WhatsAppMessage::whereDate('created_at', $today)->whereIn('message_type', ['audio', 'voice'])->count(),
        'processed_messages' => \App\Models\WhatsAppMessage::whereDate('created_at', $today)->processed()->count()
    ];
    
    \Illuminate\Support\Facades\Log::info('📊 Daily WhatsApp Usage Report', $stats);
    
})->dailyAt('23:00')->name('whatsapp-daily-stats');

// 🧹 Clean up old voice files weekly
Schedule::call(function () {
    $speechService = app(\App\Services\SpeechToTextService::class);
    $deletedCount = $speechService->cleanupOldVoiceFiles(7); // Delete files older than 7 days
    
    \Illuminate\Support\Facades\Log::info('Voice files cleanup completed', [
        'deleted_count' => $deletedCount
    ]);
    
})->weeklyOn(0, '02:00')->name('whatsapp-voice-cleanup');

// 🔄 Sync user data with WhatsApp interactions daily
Schedule::call(function () {
    $userIntegrationService = app(\App\Services\WhatsAppUserIntegrationService::class);
    $result = $userIntegrationService->bulkSyncUsers();
    
    \Illuminate\Support\Facades\Log::info('WhatsApp user sync completed', $result);
    
})->dailyAt('03:00')->name('whatsapp-user-sync');

// 🧹 Clean up inactive subscriptions monthly
Schedule::call(function () {
    $subscriptionService = app(\App\Services\WhatsAppSubscriptionService::class);
    $deactivatedCount = $subscriptionService->cleanupInactiveSubscriptions(90); // 90 days inactive
    
    \Illuminate\Support\Facades\Log::info('Inactive WhatsApp subscriptions cleaned up', [
        'deactivated_count' => $deactivatedCount
    ]);
    
})->monthlyOn(1, '04:00')->name('whatsapp-subscription-cleanup');
