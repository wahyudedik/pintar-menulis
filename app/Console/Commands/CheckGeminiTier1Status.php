<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GeminiService;
use App\Services\ModelFallbackManager;

class CheckGeminiTier1Status extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'gemini:tier1-status {--reset : Reset usage stats}';

    /**
     * The console command description.
     */
    protected $description = 'Check Gemini Tier 1 status and performance metrics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $geminiService = app(GeminiService::class);
        $fallbackManager = app(ModelFallbackManager::class);

        if ($this->option('reset')) {
            $fallbackManager->resetUsageStats();
            $this->info('✅ Usage stats reset successfully');
            return;
        }

        $this->info('🔍 Checking Gemini Tier 1 Status...');
        $this->newLine();

        // Get tier 1 performance stats
        $stats = $geminiService->getTier1PerformanceStats();

        // Display tier info
        $this->displayTierInfo($stats['tier_info']);
        $this->newLine();

        // Display usage stats
        $this->displayUsageStats($stats['usage_stats']);
        $this->newLine();

        // Display performance metrics
        $this->displayPerformanceMetrics($stats['performance_metrics']);
        $this->newLine();

        // Display recommendations
        $this->displayRecommendations($stats['recommendations']);
    }

    protected function displayTierInfo(array $tierInfo)
    {
        $this->info('📊 TIER INFORMATION');
        $this->line('─────────────────────');
        
        $status = $tierInfo['is_tier1'] ? '<fg=green>✅ ACTIVE</>' : '<fg=red>❌ INACTIVE</>';
        $this->line("Tier 1 Status: {$status}");
        $this->line("Tier Name: {$tierInfo['tier_name']}");
        $this->line("Billing Status: {$tierInfo['billing_status']}");
        $this->line("Primary Model: {$tierInfo['primary_model']}");
        $this->line("Total RPM Capacity: {$tierInfo['total_rpm_limit']}");
        $this->line("Models Available: {$tierInfo['models_available']}");
        
        if ($tierInfo['is_tier1']) {
            $this->line('<fg=green>🎉 Tier 1 benefits are active!</>');
        } else {
            $this->line('<fg=yellow>⚠️  Consider upgrading to Tier 1 for better performance</>');
        }
    }

    protected function displayUsageStats(array $usageStats)
    {
        $this->info('📈 USAGE STATISTICS');
        $this->line('─────────────────────');

        $meta = $usageStats['_meta'] ?? [];
        unset($usageStats['_meta']);

        foreach ($usageStats as $modelName => $stats) {
            $available = $stats['available'] ? '<fg=green>✅</>' : '<fg=red>❌</>';
            $this->line("Model: {$stats['display_name']} {$available}");
            
            // RPM Stats
            $rpmColor = $stats['rpm']['percentage'] > 80 ? 'red' : ($stats['rpm']['percentage'] > 50 ? 'yellow' : 'green');
            $this->line("  RPM: {$stats['rpm']['current']}/{$stats['rpm']['limit']} (<fg={$rpmColor}>{$stats['rpm']['percentage']}%</>)");
            
            // RPD Stats (if not unlimited)
            if ($stats['rpd']['limit'] < 999999) {
                $rpdColor = $stats['rpd']['percentage'] > 80 ? 'red' : ($stats['rpd']['percentage'] > 50 ? 'yellow' : 'green');
                $this->line("  RPD: {$stats['rpd']['current']}/{$stats['rpd']['limit']} (<fg={$rpdColor}>{$stats['rpd']['percentage']}%</>)");
            } else {
                $this->line("  RPD: {$stats['rpd']['current']}/Unlimited");
            }
            
            $this->line("  Quality: {$stats['quality']} | Priority: {$stats['priority']} | Tier: {$stats['tier']}");
            $this->newLine();
        }
    }

    protected function displayPerformanceMetrics(array $metrics)
    {
        $this->info('⚡ PERFORMANCE METRICS');
        $this->line('─────────────────────');
        
        $this->line("Requests Today: {$metrics['requests_today']}");
        $this->line("Average Response Time: {$metrics['average_response_time']}s");
        
        $successColor = $metrics['success_rate'] > 95 ? 'green' : ($metrics['success_rate'] > 90 ? 'yellow' : 'red');
        $this->line("Success Rate: <fg={$successColor}>{$metrics['success_rate']}%</>");
        
        $tier1Status = $metrics['tier1_benefits_utilized'] ? '<fg=green>Yes</>' : '<fg=red>No</>';
        $this->line("Tier 1 Benefits: {$tier1Status}");
    }

    protected function displayRecommendations(array $recommendations)
    {
        $this->info('💡 RECOMMENDATIONS');
        $this->line('─────────────────────');
        
        foreach ($recommendations as $recommendation) {
            $this->line("• {$recommendation}");
        }
    }
}