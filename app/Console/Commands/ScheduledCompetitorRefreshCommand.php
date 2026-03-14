<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MLDataCache;
use App\Jobs\RefreshCompetitorDataJob;
use App\Jobs\BatchCompetitorAnalysisJob;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScheduledCompetitorRefreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'competitor:refresh-scheduled 
                           {--type=smart : Type of refresh (smart|all|high-priority)}
                           {--batch-size=50 : Number of competitors per batch}
                           {--dry-run : Show what would be refreshed without actually doing it}';

    /**
     * The console command description.
     */
    protected $description = 'Refresh competitor data based on AI-driven scheduling';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->option('type');
        $batchSize = (int) $this->option('batch-size');
        $dryRun = $this->option('dry-run');

        $this->info("🤖 AI-Powered Competitor Refresh Scheduler");
        $this->info("Type: {$type} | Batch Size: {$batchSize}" . ($dryRun ? ' | DRY RUN' : ''));
        $this->line('');

        switch ($type) {
            case 'smart':
                return $this->smartRefresh($batchSize, $dryRun);
            case 'all':
                return $this->refreshAll($batchSize, $dryRun);
            case 'high-priority':
                return $this->refreshHighPriority($batchSize, $dryRun);
            default:
                $this->error("Unknown refresh type: {$type}");
                return 1;
        }
    }

    /**
     * Smart refresh based on AI analysis
     */
    private function smartRefresh(int $batchSize, bool $dryRun): int
    {
        $this->info('🧠 Analyzing competitors for smart refresh...');

        // Get competitors that need refresh based on AI criteria
        $competitorsToRefresh = $this->getCompetitorsForSmartRefresh();

        if ($competitorsToRefresh->isEmpty()) {
            $this->info('✅ No competitors need refresh at this time.');
            return 0;
        }

        $this->info("📊 Found {$competitorsToRefresh->count()} competitors that need refresh:");

        // Group by priority
        $highPriority = $competitorsToRefresh->where('priority', 'high');
        $normalPriority = $competitorsToRefresh->where('priority', 'normal');
        $lowPriority = $competitorsToRefresh->where('priority', 'low');

        $this->table(
            ['Priority', 'Count', 'Reason'],
            [
                ['High', $highPriority->count(), 'Large accounts, high engagement'],
                ['Normal', $normalPriority->count(), 'Regular scheduled refresh'],
                ['Low', $lowPriority->count(), 'Small accounts, low activity']
            ]
        );

        if ($dryRun) {
            $this->warn('DRY RUN: No jobs will be dispatched.');
            return 0;
        }

        // Dispatch jobs by priority
        $this->dispatchPriorityJobs($highPriority, $normalPriority, $lowPriority, $batchSize);

        $this->info('✅ Smart refresh jobs dispatched successfully!');
        return 0;
    }

    /**
     * Refresh all competitors
     */
    private function refreshAll(int $batchSize, bool $dryRun): int
    {
        $this->info('🔄 Refreshing all competitors...');

        $allCompetitors = MLDataCache::select('username', 'platform')
            ->get()
            ->map(function($item) {
                return [
                    'username' => $item->username,
                    'platform' => $item->platform
                ];
            })
            ->toArray();

        $this->info("📊 Total competitors: " . count($allCompetitors));

        if ($dryRun) {
            $this->warn('DRY RUN: No jobs will be dispatched.');
            return 0;
        }

        // Split into batches
        $batches = array_chunk($allCompetitors, $batchSize);
        
        foreach ($batches as $index => $batch) {
            $batchId = 'refresh_all_' . date('Y-m-d_H-i') . '_' . ($index + 1);
            BatchCompetitorAnalysisJob::dispatch($batch, $batchId)
                ->delay(now()->addMinutes($index * 2)); // Stagger batches
        }

        $this->info("✅ Dispatched " . count($batches) . " batch jobs for all competitors!");
        return 0;
    }

    /**
     * Refresh high priority competitors only
     */
    private function refreshHighPriority(int $batchSize, bool $dryRun): int
    {
        $this->info('⚡ Refreshing high priority competitors...');

        $highPriorityCompetitors = $this->getHighPriorityCompetitors();

        $this->info("📊 High priority competitors: {$highPriorityCompetitors->count()}");

        if ($dryRun) {
            $this->warn('DRY RUN: No jobs will be dispatched.');
            return 0;
        }

        foreach ($highPriorityCompetitors as $competitor) {
            RefreshCompetitorDataJob::dispatch(
                $competitor->username,
                $competitor->platform,
                'high'
            )->onQueue('competitor-high');
        }

        $this->info('✅ High priority refresh jobs dispatched!');
        return 0;
    }

    /**
     * Get competitors for smart refresh using AI criteria
     */
    private function getCompetitorsForSmartRefresh()
    {
        return MLDataCache::select('username', 'platform', 'profile_data', 'updated_at')
            ->get()
            ->map(function($mlData) {
                $priority = $this->calculateRefreshPriority($mlData);
                $needsRefresh = $this->needsRefresh($mlData, $priority);

                return [
                    'username' => $mlData->username,
                    'platform' => $mlData->platform,
                    'priority' => $priority,
                    'needs_refresh' => $needsRefresh,
                    'data_age_hours' => $mlData->updated_at->diffInHours(now()),
                    'followers' => $mlData->profile_data['followers_count'] ?? 0
                ];
            })
            ->filter(function($item) {
                return $item['needs_refresh'];
            })
            ->collect();
    }

    /**
     * Calculate refresh priority based on AI criteria
     */
    private function calculateRefreshPriority(MLDataCache $mlData): string
    {
        $followers = $mlData->profile_data['followers_count'] ?? 0;
        $engagement = $mlData->profile_data['engagement_rate'] ?? 0;
        $isVerified = $mlData->profile_data['is_verified'] ?? false;

        // High priority criteria
        if ($followers > 100000 || $engagement > 5 || $isVerified) {
            return 'high';
        }

        // Low priority criteria
        if ($followers < 1000 && $engagement < 2) {
            return 'low';
        }

        return 'normal';
    }

    /**
     * Check if competitor needs refresh based on priority and age
     */
    private function needsRefresh(MLDataCache $mlData, string $priority): bool
    {
        $hoursOld = $mlData->updated_at->diffInHours(now());

        return match($priority) {
            'high' => $hoursOld >= 6,      // High priority: 6 hours
            'normal' => $hoursOld >= 12,   // Normal: 12 hours
            'low' => $hoursOld >= 24,      // Low priority: 24 hours
            default => $hoursOld >= 12
        };
    }

    /**
     * Get high priority competitors
     */
    private function getHighPriorityCompetitors()
    {
        return MLDataCache::whereRaw('
            JSON_EXTRACT(profile_data, "$.followers_count") > 100000 
            OR JSON_EXTRACT(profile_data, "$.engagement_rate") > 5 
            OR JSON_EXTRACT(profile_data, "$.is_verified") = true
        ')->get();
    }

    /**
     * Dispatch jobs by priority
     */
    private function dispatchPriorityJobs($high, $normal, $low, int $batchSize): void
    {
        // High priority - individual jobs, immediate
        foreach ($high as $competitor) {
            RefreshCompetitorDataJob::dispatch(
                $competitor['username'],
                $competitor['platform'],
                'high'
            )->onQueue('competitor-high');
        }

        // Normal priority - batch jobs, slight delay
        if ($normal->isNotEmpty()) {
            $normalBatches = $normal->chunk($batchSize);
            foreach ($normalBatches as $index => $batch) {
                $batchId = 'smart_normal_' . date('Y-m-d_H-i') . '_' . ($index + 1);
                BatchCompetitorAnalysisJob::dispatch($batch->toArray(), $batchId)
                    ->delay(now()->addMinutes(5 + ($index * 3)));
            }
        }

        // Low priority - batch jobs, longer delay
        if ($low->isNotEmpty()) {
            $lowBatches = $low->chunk($batchSize * 2); // Larger batches for low priority
            foreach ($lowBatches as $index => $batch) {
                $batchId = 'smart_low_' . date('Y-m-d_H-i') . '_' . ($index + 1);
                BatchCompetitorAnalysisJob::dispatch($batch->toArray(), $batchId)
                    ->delay(now()->addMinutes(15 + ($index * 5)));
            }
        }
    }
}