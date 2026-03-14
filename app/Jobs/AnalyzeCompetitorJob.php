<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Competitor;
use App\Services\CompetitorAnalysisService;
use Illuminate\Support\Facades\Log;

class AnalyzeCompetitorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // Reduced to 2 minutes
    public $tries = 2; // Reduced tries
    public $maxExceptions = 1;

    protected $competitor;

    /**
     * Create a new job instance.
     */
    public function __construct(Competitor $competitor)
    {
        $this->competitor = $competitor;
        
        // Set high priority for competitor analysis
        $this->onQueue('high');
    }

    /**
     * Execute the job.
     */
    public function handle(CompetitorAnalysisService $analysisService): void
    {
        $startTime = microtime(true);
        
        try {
            Log::info('🚀 Starting FAST competitor analysis', [
                'competitor_id' => $this->competitor->id,
                'username' => $this->competitor->username,
                'queue' => 'high'
            ]);

            // Run optimized analysis
            $result = $analysisService->analyzeCompetitor($this->competitor);

            $executionTime = microtime(true) - $startTime;

            if ($result['success']) {
                Log::info('✅ FAST competitor analysis completed', [
                    'competitor_id' => $this->competitor->id,
                    'execution_time' => round($executionTime, 2) . 's',
                    'posts_count' => $result['posts_count'] ?? 0,
                    'content_gaps' => count($result['content_gaps'] ?? []),
                    'summary_generated' => $result['summary_generated'] ?? false
                ]);
            } else {
                Log::warning('⚠️ FAST competitor analysis failed', [
                    'competitor_id' => $this->competitor->id,
                    'execution_time' => round($executionTime, 2) . 's',
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            $executionTime = microtime(true) - $startTime;
            
            Log::error('❌ FAST competitor analysis job failed', [
                'competitor_id' => $this->competitor->id,
                'execution_time' => round($executionTime, 2) . 's',
                'error' => $e->getMessage(),
                'attempts' => $this->attempts()
            ]);

            // Only retry once
            if ($this->attempts() < 2) {
                throw $e;
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('💥 Competitor analysis job failed permanently', [
            'competitor_id' => $this->competitor->id,
            'username' => $this->competitor->username,
            'error' => $exception->getMessage(),
            'final_attempt' => true
        ]);
        
        // Create failure alert for user
        try {
            \App\Models\CompetitorAlert::create([
                'user_id' => $this->competitor->user_id,
                'competitor_id' => $this->competitor->id,
                'alert_type' => 'analysis_failed',
                'alert_title' => '⚠️ Analisis Gagal',
                'alert_message' => "Analisis untuk {$this->competitor->username} gagal. Silakan coba refresh manual.",
                'alert_data' => [
                    'error' => $exception->getMessage(),
                    'failed_at' => now()->toISOString()
                ],
                'triggered_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create failure alert', ['error' => $e->getMessage()]);
        }
    }
}