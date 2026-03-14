<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MLDataManagerService;
use Illuminate\Support\Facades\Cache;

class MonitorApiUsageCommand extends Command
{
    protected $signature = 'api:monitor {--watch : Watch mode - refresh every 30 seconds}';
    protected $description = 'Monitor API usage and ML cache efficiency';

    public function handle(MLDataManagerService $mlManager): int
    {
        $watchMode = $this->option('watch');

        if ($watchMode) {
            $this->info('📊 API Usage Monitor (Watch Mode - Press Ctrl+C to exit)');
            $this->line('Refreshing every 30 seconds...');
            $this->line('');

            while (true) {
                $this->displayUsageStats($mlManager);
                sleep(30);
                $this->line("\033[2J\033[H"); // Clear screen
            }
        } else {
            $this->info('📊 API Usage Monitor');
            $this->line('');
            $this->displayUsageStats($mlManager);
        }

        return 0;
    }

    private function displayUsageStats(MLDataManagerService $mlManager): void
    {
        $stats = $mlManager->getMLStatistics();
        
        // API Usage Today
        $this->info('🔥 Today\'s API Usage:');
        $todayUsage = $this->getTodayApiUsage();
        
        $this->table(
            ['Platform', 'Requests', 'Limit', 'Usage %', 'Status'],
            [
                ['YouTube', $todayUsage['youtube'] ?? 0, '10,000/day', $this->calculateUsagePercent($todayUsage['youtube'] ?? 0, 10000), $this->getUsageStatus($todayUsage['youtube'] ?? 0, 10000)],
                ['X (Twitter)', $todayUsage['x'] ?? 0, '50/day', $this->calculateUsagePercent($todayUsage['x'] ?? 0, 50), $this->getUsageStatus($todayUsage['x'] ?? 0, 50)],
                ['Instagram', $todayUsage['instagram'] ?? 0, '200/hour', $this->calculateUsagePercent($todayUsage['instagram'] ?? 0, 200), $this->getUsageStatus($todayUsage['instagram'] ?? 0, 200)],
                ['Facebook', $todayUsage['facebook'] ?? 0, '200/hour', $this->calculateUsagePercent($todayUsage['facebook'] ?? 0, 200), $this->getUsageStatus($todayUsage['facebook'] ?? 0, 200)],
            ]
        );

        // ML Cache Efficiency
        $this->line('');
        $this->info('🧠 ML Cache Performance:');
        $this->table(
            ['Metric', 'Value', 'Target', 'Status'],
            [
                ['Cache Hit Rate', $stats['cache_efficiency'] . '%', '80%', $stats['cache_efficiency'] >= 80 ? '✅ Good' : '⚠️ Needs Improvement'],
                ['Profiles Cached', number_format($stats['total_profiles_cached']), '1000+', $stats['total_profiles_cached'] >= 1000 ? '✅ Good' : '📈 Growing'],
                ['API Calls Saved', number_format($stats['api_calls_saved']), '500+', $stats['api_calls_saved'] >= 500 ? '✅ Excellent' : '📈 Building'],
                ['Cost Savings', '$' . number_format($stats['api_calls_saved'] * 0.01, 2), '$50+', $stats['api_calls_saved'] >= 5000 ? '💰 Excellent' : '💡 Potential'],
            ]
        );

        // Queue Status
        $this->line('');
        $this->info('⚡ Queue Status:');
        $queueStats = $this->getQueueStats();
        
        $this->table(
            ['Queue', 'Pending', 'Processing', 'Failed', 'Status'],
            [
                ['High Priority', $queueStats['competitor-high']['pending'] ?? 0, $queueStats['competitor-high']['processing'] ?? 0, $queueStats['competitor-high']['failed'] ?? 0, '🔥 Active'],
                ['Normal Priority', $queueStats['competitor-normal']['pending'] ?? 0, $queueStats['competitor-normal']['processing'] ?? 0, $queueStats['competitor-normal']['failed'] ?? 0, '⚡ Active'],
                ['Low Priority', $queueStats['competitor-low']['pending'] ?? 0, $queueStats['competitor-low']['processing'] ?? 0, $queueStats['competitor-low']['failed'] ?? 0, '🐌 Active'],
                ['Batch Processing', $queueStats['competitor-batch']['pending'] ?? 0, $queueStats['competitor-batch']['processing'] ?? 0, $queueStats['competitor-batch']['failed'] ?? 0, '📦 Scheduled'],
            ]
        );

        $this->line('');
        $this->info('Last updated: ' . now()->format('Y-m-d H:i:s'));
    }

    private function getTodayApiUsage(): array
    {
        // Get today's API usage from cache
        $today = now()->format('Y-m-d');
        
        return [
            'youtube' => Cache::get("api_usage_youtube_{$today}", 0),
            'x' => Cache::get("api_usage_x_{$today}", 0),
            'instagram' => Cache::get("api_usage_instagram_{$today}", 0),
            'facebook' => Cache::get("api_usage_facebook_{$today}", 0),
        ];
    }

    private function calculateUsagePercent(int $used, int $limit): string
    {
        if ($limit == 0) return '0%';
        return round(($used / $limit) * 100, 1) . '%';
    }

    private function getUsageStatus(int $used, int $limit): string
    {
        $percent = $limit > 0 ? ($used / $limit) * 100 : 0;
        
        if ($percent >= 90) return '🔴 Critical';
        if ($percent >= 70) return '🟡 Warning';
        if ($percent >= 50) return '🟠 Moderate';
        return '🟢 Good';
    }

    private function getQueueStats(): array
    {
        // Simplified queue stats - in real implementation, 
        // you'd query the jobs table or use Laravel Horizon
        return [
            'competitor-high' => ['pending' => 0, 'processing' => 0, 'failed' => 0],
            'competitor-normal' => ['pending' => 0, 'processing' => 0, 'failed' => 0],
            'competitor-low' => ['pending' => 0, 'processing' => 0, 'failed' => 0],
            'competitor-batch' => ['pending' => 0, 'processing' => 0, 'failed' => 0],
        ];
    }
}