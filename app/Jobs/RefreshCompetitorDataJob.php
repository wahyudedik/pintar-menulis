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

class RefreshCompetitorDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes timeout
    public $tries = 3;
    public $backoff = [30, 60, 120]; // Retry delays in seconds

    private string $username;
    private string $platform;
    private string $priority;
    private ?int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $username, string $platform, string $priority = 'normal', ?int $userId = null)
    {
        $this->username = $username;
        $this->platform = $platform;
        $this->priority = $priority;
        $this->userId = $userId;
        
        // Set queue based on priority
        $this->onQueue($this->getQueueName($priority));
    }

    /**
     * Execute the job.
     */
    public function handle(MLDataManagerService $mlManager): void
    {
        try {
            Log::info('Starting background competitor data refresh', [
                'username' => $this->username,
                'platform' => $this->platform,
                'priority' => $this->priority,
                'user_id' => $this->userId
            ]);

            // Force fresh data fetch
            $result = $mlManager->getCompetitorData($this->username, $this->platform);

            if ($result['success']) {
                Log::info('Background refresh completed successfully', [
                    'username' => $this->username,
                    'platform' => $this->platform,
                    'source' => $result['source'] ?? 'unknown'
                ]);

                // Update job completion status
                $this->updateJobStatus('completed', $result);
            } else {
                throw new \Exception($result['error'] ?? 'Unknown error during refresh');
            }

        } catch (\Exception $e) {
            Log::error('Background refresh failed', [
                'username' => $this->username,
                'platform' => $this->platform,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // Update job status
            $this->updateJobStatus('failed', ['error' => $e->getMessage()]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Competitor data refresh job failed permanently', [
            'username' => $this->username,
            'platform' => $this->platform,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        $this->updateJobStatus('failed_permanently', [
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * Get queue name based on priority
     */
    private function getQueueName(string $priority): string
    {
        return match($priority) {
            'high' => 'competitor-high',
            'low' => 'competitor-low',
            default => 'competitor-normal'
        };
    }

    /**
     * Update job status in cache
     */
    private function updateJobStatus(string $status, array $data = []): void
    {
        $jobKey = "competitor_job_{$this->username}_{$this->platform}";
        
        \Cache::put($jobKey, [
            'status' => $status,
            'username' => $this->username,
            'platform' => $this->platform,
            'priority' => $this->priority,
            'user_id' => $this->userId,
            'updated_at' => now()->toISOString(),
            'data' => $data
        ], now()->addHours(24));
    }
}