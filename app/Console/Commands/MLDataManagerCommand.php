<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MLDataManagerService;

class MLDataManagerCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ml:manage {action : Action to perform (stats|cleanup|optimize)}';

    /**
     * The console command description.
     */
    protected $description = 'Manage ML data cache and optimization';

    /**
     * Execute the console command.
     */
    public function handle(MLDataManagerService $mlManager): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'stats':
                $this->showStatistics($mlManager);
                break;
                
            case 'cleanup':
                $this->performCleanup($mlManager);
                break;
                
            case 'optimize':
                $this->optimizeCache($mlManager);
                break;
                
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: stats, cleanup, optimize");
                return 1;
        }

        return 0;
    }

    /**
     * Show ML statistics
     */
    private function showStatistics(MLDataManagerService $mlManager): void
    {
        $this->info('📊 ML Data Manager Statistics');
        $this->line('');

        $stats = $mlManager->getMLStatistics();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Profiles Cached', number_format($stats['total_profiles_cached'])],
                ['Recent Updates (7 days)', number_format($stats['recent_updates'])],
                ['API Calls Saved', number_format($stats['api_calls_saved'])],
                ['Cache Efficiency', $stats['cache_efficiency'] . '%']
            ]
        );

        if (!empty($stats['platform_distribution'])) {
            $this->line('');
            $this->info('📱 Platform Distribution:');
            
            $platformData = [];
            foreach ($stats['platform_distribution'] as $platform) {
                $platformData[] = [
                    ucfirst($platform->platform),
                    number_format($platform->count),
                    round($platform->avg_quality, 1) . '%'
                ];
            }
            
            $this->table(
                ['Platform', 'Profiles', 'Avg Quality'],
                $platformData
            );
        }
    }

    /**
     * Perform cleanup
     */
    private function performCleanup(MLDataManagerService $mlManager): void
    {
        $this->info('🧹 Starting ML data cleanup...');
        
        $deleted = $mlManager->cleanOldMLData();
        
        $this->info("✅ Cleanup completed. Removed {$deleted} old records.");
    }

    /**
     * Optimize cache
     */
    private function optimizeCache(MLDataManagerService $mlManager): void
    {
        $this->info('⚡ Optimizing ML cache...');
        
        // Show current stats
        $stats = $mlManager->getMLStatistics();
        $this->line("Current cache efficiency: {$stats['cache_efficiency']}%");
        
        // Perform cleanup
        $deleted = $mlManager->cleanOldMLData();
        
        // Show new stats
        $newStats = $mlManager->getMLStatistics();
        $this->line("New cache efficiency: {$newStats['cache_efficiency']}%");
        
        $this->info("✅ Optimization completed. Removed {$deleted} records.");
        $this->info("💰 Estimated API cost savings: $" . number_format($newStats['api_calls_saved'] * 0.01, 2));
    }
}