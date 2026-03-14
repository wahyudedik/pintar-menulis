<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Competitor;
use App\Services\CompetitorAnalysisService;
use App\Models\CompetitorAlert;
use Illuminate\Support\Facades\Log;

class AnalyzeCompetitors extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'competitors:analyze {--force : Force analysis even if recently analyzed}';

    /**
     * The console command description.
     */
    protected $description = 'Analyze all active competitors and send notifications';

    protected $analysisService;

    public function __construct(CompetitorAnalysisService $analysisService)
    {
        parent::__construct();
        $this->analysisService = $analysisService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting competitor analysis...');
        
        // Get all active competitors that need analysis
        $competitors = Competitor::where('is_active', true)
            ->when(!$this->option('force'), function($query) {
                // Only analyze if not analyzed in last 6 hours
                return $query->where(function($q) {
                    $q->whereNull('last_analyzed_at')
                      ->orWhere('last_analyzed_at', '<', now()->subHours(6));
                });
            })
            ->with('user')
            ->get();

        if ($competitors->isEmpty()) {
            $this->info('✅ No competitors need analysis at this time.');
            return;
        }

        $this->info("📊 Found {$competitors->count()} competitors to analyze");

        $successCount = 0;
        $errorCount = 0;

        foreach ($competitors as $competitor) {
            try {
                $this->line("Analyzing: {$competitor->username} (@{$competitor->platform})");
                
                // Run AI analysis
                $result = $this->analysisService->analyzeCompetitor($competitor);
                
                if ($result['success']) {
                    $successCount++;
                    $this->info("✅ {$competitor->username} - Analysis complete");
                    
                    // Send notification to user
                    $this->sendAnalysisNotification($competitor, $result);
                    
                } else {
                    $errorCount++;
                    $this->error("❌ {$competitor->username} - Analysis failed: " . ($result['error'] ?? 'Unknown error'));
                }
                
                // Small delay to avoid overwhelming the system
                sleep(2);
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("❌ {$competitor->username} - Exception: " . $e->getMessage());
                
                Log::error('Competitor analysis command failed', [
                    'competitor_id' => $competitor->id,
                    'username' => $competitor->username,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("\n📈 Analysis Summary:");
        $this->info("✅ Successful: {$successCount}");
        $this->info("❌ Failed: {$errorCount}");
        $this->info("🎯 Total processed: " . ($successCount + $errorCount));
        
        // Clean up old alerts (older than 30 days)
        $this->cleanupOldAlerts();
        
        $this->info('🏁 Competitor analysis completed!');
    }

    /**
     * Send analysis notification to user
     */
    private function sendAnalysisNotification(Competitor $competitor, array $result): void
    {
        try {
            // Create summary alert
            CompetitorAlert::create([
                'user_id' => $competitor->user_id,
                'competitor_id' => $competitor->id,
                'alert_type' => 'pattern_change',
                'alert_title' => '📊 Analysis Update: ' . $competitor->username,
                'alert_message' => $this->generateAnalysisSummary($competitor, $result),
                'alert_data' => [
                    'analysis_type' => 'scheduled',
                    'timestamp' => now()->toISOString(),
                    'ai_insights_count' => count($result['ai_analysis']['content_recommendations']['content_ideas'] ?? []),
                    'content_gaps_count' => count($result['content_gaps'] ?? [])
                ],
                'triggered_at' => now()
            ]);

            // Check for high-priority opportunities
            $highPriorityGaps = collect($result['content_gaps'] ?? [])
                ->where('priority', '>=', 8);

            if ($highPriorityGaps->isNotEmpty()) {
                CompetitorAlert::create([
                    'user_id' => $competitor->user_id,
                    'competitor_id' => $competitor->id,
                    'alert_type' => 'engagement_spike',
                    'alert_title' => '🚀 High-Priority Opportunities Found!',
                    'alert_message' => "AI menemukan {$highPriorityGaps->count()} peluang konten dengan potensi viral tinggi untuk mengalahkan {$competitor->username}",
                    'alert_data' => [
                        'opportunities' => $highPriorityGaps->pluck('gap_title')->toArray(),
                        'priority_level' => 'high',
                        'action_required' => true
                    ],
                    'triggered_at' => now()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send analysis notification', [
                'competitor_id' => $competitor->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate analysis summary message
     */
    private function generateAnalysisSummary(Competitor $competitor, array $result): string
    {
        $messages = [];
        
        $messages[] = "Analisis terbaru untuk kompetitor {$competitor->username} telah selesai.";
        
        if (isset($result['ai_analysis']['competitive_advantages']['opportunities'])) {
            $opportunities = count($result['ai_analysis']['competitive_advantages']['opportunities']);
            $messages[] = "🎯 Ditemukan {$opportunities} peluang strategis.";
        }
        
        if (isset($result['content_gaps'])) {
            $gaps = count($result['content_gaps']);
            $messages[] = "💡 Teridentifikasi {$gaps} content gap yang bisa dimanfaatkan.";
        }
        
        if (isset($result['ai_analysis']['content_recommendations']['top_opportunity'])) {
            $messages[] = "🚀 Peluang terbesar: " . $result['ai_analysis']['content_recommendations']['top_opportunity'];
        }
        
        $messages[] = "Lihat detail analisis di dashboard untuk strategi lengkap.";
        
        return implode(' ', $messages);
    }

    /**
     * Clean up old alerts
     */
    private function cleanupOldAlerts(): void
    {
        $deletedCount = CompetitorAlert::where('triggered_at', '<', now()->subDays(30))->delete();
        
        if ($deletedCount > 0) {
            $this->info("🧹 Cleaned up {$deletedCount} old alerts");
        }
    }
}