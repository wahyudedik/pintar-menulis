<?php

namespace App\Jobs;

use App\Models\MLDataCache;
use App\Services\MLDataManagerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BatchCompetitorAnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutes timeout
    public $tries = 2;

    private array $competitors;
    private string $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $competitors, string $batchId = null)
    {
        $this->competitors = $competitors;
        $this->batchId = $batchId ?? uniqid('batch_');
        $this->onQueue('competitor-batch');
    }

    /**
     * Execute the job.
     */
    public function handle(MLDataManagerService $mlManager): void
    {
        Log::info('Starting batch competitor analysis', [
            'batch_id' => $this->batchId,
            'competitors_count' => count($this->competitors)
        ]);

        $processed = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($this->competitors as $competitor) {
            try {
                $username = $competitor['username'];
                $platform = $competitor['platform'];

                // Check if we need to process this competitor
                if ($this->shouldSkipCompetitor($username, $platform)) {
                    $skipped++;
                    continue;
                }

                // Process competitor with rate limiting
                $result = $mlManager->getCompetitorData($username, $platform);

                if ($result['success']) {
                    $processed++;
                    Log::debug('Batch processed competitor', [
                        'batch_id' => $this->batchId,
                        'username' => $username,
                        'platform' => $platform
                    ]);
                } else {
                    $failed++;
                    Log::warning('Batch failed to process competitor', [
                        'batch_id' => $this->batchId,
                        'username' => $username,
                        'platform' => $platform,
                        'error' => $result['error'] ?? 'Unknown error'
                    ]);
                }

                // Rate limiting: Wait between requests to avoid API limits
                if ($processed % 5 === 0) {
                    sleep(2); // 2 second pause every 5 requests
                }

            } catch (\Exception $e) {
                $failed++;
                Log::error('Batch processing error', [
                    'batch_id' => $this->batchId,
                    'competitor' => $competitor,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Update batch status
        $this->updateBatchStatus($processed, $failed, $skipped);

        Log::info('Batch competitor analysis completed', [
            'batch_id' => $this->batchId,
            'processed' => $processed,
            'failed' => $failed,
            'skipped' => $skipped
        ]);
    }

    /**
     * Check if competitor should be skipped
     */
    private function shouldSkipCompetitor(string $username, string $platform): bool
    {
        $mlData = MLDataCache::where('username', $username)
            ->where('platform', $platform)
            ->first();

        if (!$mlData) {
            return false; // New competitor, don't skip
        }

        // Skip if data is fresh (less than 6 hours old for batch processing)
        return $mlData->updated_at->diffInHours(now()) < 6;
    }

    /**
     * Update batch status
     */
    private function updateBatchStatus(int $processed, int $failed, int $skipped): void
    {
        \Cache::put("batch_status_{$this->batchId}", [
            'batch_id' => $this->batchId,
            'status' => 'completed',
            'total_competitors' => count($this->competitors),
            'processed' => $processed,
            'failed' => $failed,
            'skipped' => $skipped,
            'completed_at' => now()->toISOString()
        ], now()->addDays(7));
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Batch competitor analysis job failed', [
            'batch_id' => $this->batchId,
            'error' => $exception->getMessage()
        ]);

        \Cache::put("batch_status_{$this->batchId}", [
            'batch_id' => $this->batchId,
            'status' => 'failed',
            'error' => $exception->getMessage(),
            'failed_at' => now()->toISOString()
        ], now()->addDays(7));
    }
}