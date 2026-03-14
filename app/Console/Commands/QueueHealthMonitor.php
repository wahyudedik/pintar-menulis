<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;

class QueueHealthMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:health-monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor queue health with concise output';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Check queue connection
            $connection = Queue::connection();
            
            // Get queue sizes
            $queues = ['competitor-high', 'competitor-normal', 'competitor-low', 'competitor-batch'];
            $totalJobs = 0;
            $failedJobs = 0;
            
            foreach ($queues as $queue) {
                try {
                    $size = $connection->size($queue);
                    $totalJobs += $size;
                } catch (\Exception $e) {
                    // Queue might not exist yet, that's ok
                }
            }
            
            // Check failed jobs
            try {
                $failedJobs = \DB::table('failed_jobs')->count();
            } catch (\Exception $e) {
                // Failed jobs table might not exist
            }
            
            // Output status
            if ($totalJobs > 0) {
                $this->info("Queue: {$totalJobs} pending jobs");
            } else {
                $this->info("Queue: All clear");
            }
            
            if ($failedJobs > 0) {
                $this->warn("Failed: {$failedJobs} jobs need attention");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Queue monitoring failed: " . $e->getMessage());
            return 1;
        }
    }
}
