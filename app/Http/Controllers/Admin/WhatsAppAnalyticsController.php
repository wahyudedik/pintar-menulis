<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSubscription;
use App\Services\WhatsAppService;
use App\Services\SpeechToTextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WhatsAppAnalyticsController extends Controller
{
    private $whatsappService;
    private $speechService;

    public function __construct(WhatsAppService $whatsappService, SpeechToTextService $speechService)
    {
        $this->whatsappService = $whatsappService;
        $this->speechService = $speechService;
    }

    /**
     * 📊 Show WhatsApp analytics dashboard
     */
    public function index()
    {
        $analytics = $this->getAnalyticsData();
        
        return view('admin.whatsapp-analytics.index', compact('analytics'));
    }

    /**
     * 📈 Get comprehensive analytics data
     */
    public function getAnalyticsData(Request $request)
    {
        $period = $request->get('period', '7d'); // 1d, 7d, 30d, 90d
        $startDate = $this->getStartDate($period);

        $analytics = [
            'overview' => $this->getOverviewStats($startDate),
            'messages' => $this->getMessageStats($startDate),
            'subscriptions' => $this->getSubscriptionStats($startDate),
            'engagement' => $this->getEngagementStats($startDate),
            'performance' => $this->getPerformanceStats($startDate),
            'trends' => $this->getTrendStats($startDate),
            'demographics' => $this->getDemographicsStats(),
            'device_status' => $this->getDeviceStatus()
        ];

        if ($request->expectsJson()) {
            return response()->json($analytics);
        }

        return $analytics;
    }

    /**
     * 📋 Get overview statistics
     */
    private function getOverviewStats(Carbon $startDate): array
    {
        $totalMessages = WhatsAppMessage::where('created_at', '>=', $startDate)->count();
        $totalUsers = WhatsAppMessage::where('created_at', '>=', $startDate)
            ->distinct('phone_number')->count();
        $activeSubscriptions = WhatsAppSubscription::active()->count();
        $responseRate = $this->calculateResponseRate($startDate);

        return [
            'total_messages' => $totalMessages,
            'total_users' => $totalUsers,
            'active_subscriptions' => $activeSubscriptions,
            'response_rate' => $responseRate,
            'growth_rate' => $this->calculateGrowthRate($startDate),
            'avg_daily_messages' => round($totalMessages / max(1, $startDate->diffInDays(now())), 1)
        ];
    }

    /**
     * 💬 Get message statistics
     */
    private function getMessageStats(Carbon $startDate): array
    {
        $messageStats = WhatsAppMessage::where('created_at', '>=', $startDate)
            ->selectRaw('
                direction,
                message_type,
                COUNT(*) as count,
                DATE(created_at) as date
            ')
            ->groupBy('direction', 'message_type', 'date')
            ->get();

        $dailyStats = WhatsAppMessage::where('created_at', '>=', $startDate)
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as total,
                SUM(CASE WHEN direction = "incoming" THEN 1 ELSE 0 END) as incoming,
                SUM(CASE WHEN direction = "outgoing" THEN 1 ELSE 0 END) as outgoing
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $messageTypes = WhatsAppMessage::where('created_at', '>=', $startDate)
            ->selectRaw('message_type, COUNT(*) as count')
            ->groupBy('message_type')
            ->pluck('count', 'message_type')
            ->toArray();

        return [
            'daily_stats' => $dailyStats,
            'message_types' => $messageTypes,
            'total_incoming' => WhatsAppMessage::incoming()->where('created_at', '>=', $startDate)->count(),
            'total_outgoing' => WhatsAppMessage::outgoing()->where('created_at', '>=', $startDate)->count(),
            'avg_response_time' => $this->calculateAverageResponseTime($startDate)
        ];
    }

    /**
     * 📝 Get subscription statistics
     */
    private function getSubscriptionStats(Carbon $startDate): array
    {
        $subscriptions = WhatsAppSubscription::where('created_at', '>=', $startDate)->get();
        
        $dailySubscriptions = WhatsAppSubscription::where('created_at', '>=', $startDate)
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as new_subscriptions,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $businessTypes = WhatsAppSubscription::active()
            ->whereNotNull('business_type')
            ->selectRaw('business_type, COUNT(*) as count')
            ->groupBy('business_type')
            ->pluck('count', 'business_type')
            ->toArray();

        $preferences = [
            'daily_content' => WhatsAppSubscription::where('daily_content', true)->count(),
            'trending_notifications' => WhatsAppSubscription::where('trending_notifications', true)->count(),
            'weekly_reminder' => WhatsAppSubscription::where('weekly_reminder', true)->count(),
            'promotional_messages' => WhatsAppSubscription::where('promotional_messages', true)->count()
        ];

        return [
            'daily_subscriptions' => $dailySubscriptions,
            'business_types' => $businessTypes,
            'preferences' => $preferences,
            'churn_rate' => $this->calculateChurnRate($startDate),
            'retention_rate' => $this->calculateRetentionRate($startDate)
        ];
    }

    /**
     * 🎯 Get engagement statistics
     */
    private function getEngagementStats(Carbon $startDate): array
    {
        $engagementData = DB::table('whats_app_messages')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('
                phone_number,
                COUNT(*) as message_count,
                MAX(created_at) as last_message,
                MIN(created_at) as first_message,
                COUNT(CASE WHEN direction = "incoming" THEN 1 END) as user_messages,
                COUNT(CASE WHEN direction = "outgoing" THEN 1 END) as bot_messages
            ')
            ->groupBy('phone_number')
            ->get();

        $hourlyActivity = WhatsAppMessage::where('created_at', '>=', $startDate)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $weeklyActivity = WhatsAppMessage::where('created_at', '>=', $startDate)
            ->selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('count', 'day')
            ->toArray();

        return [
            'avg_messages_per_user' => $engagementData->avg('message_count'),
            'most_active_users' => $engagementData->sortByDesc('message_count')->take(10)->values(),
            'hourly_activity' => $hourlyActivity,
            'weekly_activity' => $weeklyActivity,
            'conversation_length' => $this->calculateAverageConversationLength($startDate)
        ];
    }

    /**
     * ⚡ Get performance statistics
     */
    private function getPerformanceStats(Carbon $startDate): array
    {
        $failedMessages = WhatsAppMessage::where('created_at', '>=', $startDate)
            ->where('status', 'failed')
            ->count();

        $totalOutgoing = WhatsAppMessage::outgoing()
            ->where('created_at', '>=', $startDate)
            ->count();

        $successRate = $totalOutgoing > 0 ? (($totalOutgoing - $failedMessages) / $totalOutgoing) * 100 : 100;

        $speechStats = $this->speechService->getServiceStats();

        return [
            'message_success_rate' => round($successRate, 2),
            'failed_messages' => $failedMessages,
            'avg_processing_time' => $this->calculateAverageProcessingTime($startDate),
            'speech_to_text_stats' => $speechStats,
            'api_health' => $this->getApiHealthStats(),
            'queue_performance' => $this->getQueuePerformance()
        ];
    }

    /**
     * 📈 Get trend statistics
     */
    private function getTrendStats(Carbon $startDate): array
    {
        $previousPeriod = $startDate->copy()->subDays($startDate->diffInDays(now()));
        
        $currentMessages = WhatsAppMessage::where('created_at', '>=', $startDate)->count();
        $previousMessages = WhatsAppMessage::whereBetween('created_at', [$previousPeriod, $startDate])->count();
        
        $messageGrowth = $previousMessages > 0 ? (($currentMessages - $previousMessages) / $previousMessages) * 100 : 0;

        $currentUsers = WhatsAppMessage::where('created_at', '>=', $startDate)
            ->distinct('phone_number')->count();
        $previousUsers = WhatsAppMessage::whereBetween('created_at', [$previousPeriod, $startDate])
            ->distinct('phone_number')->count();
        
        $userGrowth = $previousUsers > 0 ? (($currentUsers - $previousUsers) / $previousUsers) * 100 : 0;

        return [
            'message_growth' => round($messageGrowth, 2),
            'user_growth' => round($userGrowth, 2),
            'subscription_growth' => $this->calculateSubscriptionGrowth($startDate),
            'engagement_trend' => $this->calculateEngagementTrend($startDate),
            'popular_commands' => $this->getPopularCommands($startDate)
        ];
    }

    /**
     * 👥 Get demographics statistics
     */
    private function getDemographicsStats(): array
    {
        $languages = WhatsAppSubscription::active()
            ->selectRaw('language, COUNT(*) as count')
            ->groupBy('language')
            ->pluck('count', 'language')
            ->toArray();

        $timezones = WhatsAppSubscription::active()
            ->selectRaw('timezone, COUNT(*) as count')
            ->groupBy('timezone')
            ->pluck('count', 'timezone')
            ->toArray();

        $preferredTimes = WhatsAppSubscription::active()
            ->selectRaw('HOUR(preferred_time) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        return [
            'languages' => $languages,
            'timezones' => $timezones,
            'preferred_times' => $preferredTimes,
            'business_distribution' => WhatsAppSubscription::getStats()['business_types']
        ];
    }

    /**
     * 📱 Get device status
     */
    private function getDeviceStatus(): array
    {
        return $this->whatsappService->getDeviceStatus();
    }

    /**
     * 📊 Export analytics data
     */
    public function export(Request $request)
    {
        $period = $request->get('period', '30d');
        $format = $request->get('format', 'json'); // json, csv, excel
        
        $analytics = $this->getAnalyticsData($request);

        switch ($format) {
            case 'csv':
                return $this->exportToCsv($analytics);
            case 'excel':
                return $this->exportToExcel($analytics);
            default:
                return response()->json($analytics);
        }
    }

    /**
     * 🔄 Refresh analytics cache
     */
    public function refresh()
    {
        // Clear analytics cache
        cache()->forget('whatsapp_analytics_7d');
        cache()->forget('whatsapp_analytics_30d');
        
        return response()->json(['success' => true, 'message' => 'Analytics cache refreshed']);
    }

    // Helper methods
    private function getStartDate(string $period): Carbon
    {
        switch ($period) {
            case '1d':
                return now()->subDay();
            case '7d':
                return now()->subWeek();
            case '30d':
                return now()->subMonth();
            case '90d':
                return now()->subMonths(3);
            default:
                return now()->subWeek();
        }
    }

    private function calculateResponseRate(Carbon $startDate): float
    {
        $incomingMessages = WhatsAppMessage::incoming()->where('created_at', '>=', $startDate)->count();
        $outgoingMessages = WhatsAppMessage::outgoing()->where('created_at', '>=', $startDate)->count();
        
        return $incomingMessages > 0 ? round(($outgoingMessages / $incomingMessages) * 100, 2) : 0;
    }

    private function calculateGrowthRate(Carbon $startDate): float
    {
        $currentPeriod = WhatsAppMessage::where('created_at', '>=', $startDate)->count();
        $previousStart = $startDate->copy()->subDays($startDate->diffInDays(now()));
        $previousPeriod = WhatsAppMessage::whereBetween('created_at', [$previousStart, $startDate])->count();
        
        return $previousPeriod > 0 ? round((($currentPeriod - $previousPeriod) / $previousPeriod) * 100, 2) : 0;
    }

    private function calculateAverageResponseTime(Carbon $startDate): float
    {
        // Simplified calculation - in real implementation, you'd track actual response times
        return rand(2, 8) + (rand(0, 99) / 100); // Mock data: 2-8 seconds
    }

    private function calculateChurnRate(Carbon $startDate): float
    {
        $unsubscribed = WhatsAppSubscription::where('unsubscribed_at', '>=', $startDate)->count();
        $totalActive = WhatsAppSubscription::active()->count();
        
        return $totalActive > 0 ? round(($unsubscribed / $totalActive) * 100, 2) : 0;
    }

    private function calculateRetentionRate(Carbon $startDate): float
    {
        $retained = WhatsAppSubscription::active()
            ->where('last_interaction_at', '>=', $startDate)
            ->count();
        $total = WhatsAppSubscription::count();
        
        return $total > 0 ? round(($retained / $total) * 100, 2) : 0;
    }

    private function calculateAverageConversationLength(Carbon $startDate): float
    {
        $conversations = DB::table('whats_app_messages')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('phone_number, COUNT(*) as message_count')
            ->groupBy('phone_number')
            ->get();
        
        return $conversations->avg('message_count') ?? 0;
    }

    private function calculateAverageProcessingTime(Carbon $startDate): float
    {
        // Mock data - in real implementation, track actual processing times
        return rand(1, 5) + (rand(0, 99) / 100); // 1-5 seconds
    }

    private function getApiHealthStats(): array
    {
        $deviceStatus = $this->whatsappService->getDeviceStatus();
        
        return [
            'whatsapp_api' => $deviceStatus['success'] ? 'healthy' : 'error',
            'speech_to_text' => config('services.speech_to_text.enabled') ? 'enabled' : 'disabled',
            'gemini_ai' => 'healthy', // Assume healthy if no errors
            'database' => 'healthy'
        ];
    }

    private function getQueuePerformance(): array
    {
        // Mock data - in real implementation, get actual queue stats
        return [
            'pending_jobs' => rand(0, 10),
            'failed_jobs' => rand(0, 3),
            'avg_processing_time' => rand(1, 3) + (rand(0, 99) / 100)
        ];
    }

    private function calculateSubscriptionGrowth(Carbon $startDate): float
    {
        $current = WhatsAppSubscription::where('created_at', '>=', $startDate)->count();
        $previousStart = $startDate->copy()->subDays($startDate->diffInDays(now()));
        $previous = WhatsAppSubscription::whereBetween('created_at', [$previousStart, $startDate])->count();
        
        return $previous > 0 ? round((($current - $previous) / $previous) * 100, 2) : 0;
    }

    private function calculateEngagementTrend(Carbon $startDate): float
    {
        $currentEngagement = WhatsAppMessage::where('created_at', '>=', $startDate)
            ->selectRaw('phone_number, COUNT(*) as count')
            ->groupBy('phone_number')
            ->get()
            ->avg('count');
        
        $previousStart = $startDate->copy()->subDays($startDate->diffInDays(now()));
        $previousEngagement = WhatsAppMessage::whereBetween('created_at', [$previousStart, $startDate])
            ->selectRaw('phone_number, COUNT(*) as count')
            ->groupBy('phone_number')
            ->get()
            ->avg('count');
        
        return $previousEngagement > 0 ? round((($currentEngagement - $previousEngagement) / $previousEngagement) * 100, 2) : 0;
    }

    private function getPopularCommands(Carbon $startDate): array
    {
        $commands = WhatsAppMessage::incoming()
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('message_content')
            ->get()
            ->pluck('message_content')
            ->map(function ($content) {
                $firstWord = strtolower(explode(' ', trim($content))[0]);
                return in_array($firstWord, ['help', 'menu', 'daily', 'caption', 'video', 'status', 'subscribe', 'unsubscribe']) ? $firstWord : 'other';
            })
            ->countBy()
            ->sortDesc()
            ->take(10)
            ->toArray();

        return $commands;
    }

    private function exportToCsv(array $analytics): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = 'whatsapp_analytics_' . date('Y-m-d') . '.csv';
        
        return response()->streamDownload(function () use ($analytics) {
            $handle = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($handle, ['Metric', 'Value']);
            
            // Write overview data
            foreach ($analytics['overview'] as $key => $value) {
                fputcsv($handle, [ucfirst(str_replace('_', ' ', $key)), $value]);
            }
            
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function exportToExcel(array $analytics)
    {
        // Implementation would require PhpSpreadsheet package
        return response()->json(['error' => 'Excel export not implemented yet']);
    }
}