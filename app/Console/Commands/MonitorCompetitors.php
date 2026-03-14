<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Competitor;
use App\Models\CompetitorAlert;
use App\Services\CompetitorAnalysisService;
use Illuminate\Support\Facades\Log;

class MonitorCompetitors extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'competitors:monitor {--check-new-posts : Check for new posts only}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor competitors for new posts and activities';

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
        $this->info('👀 Starting competitor monitoring...');
        
        // Get all active competitors
        $competitors = Competitor::where('is_active', true)
            ->with('user')
            ->get();

        if ($competitors->isEmpty()) {
            $this->info('📭 No active competitors to monitor.');
            return;
        }

        $this->info("🎯 Monitoring {$competitors->count()} competitors");

        $alertsCreated = 0;

        foreach ($competitors as $competitor) {
            try {
                $this->line("Checking: {$competitor->username}");
                
                // Simulate checking for new activities
                $newActivities = $this->checkCompetitorActivities($competitor);
                
                foreach ($newActivities as $activity) {
                    $this->createActivityAlert($competitor, $activity);
                    $alertsCreated++;
                }
                
                // Small delay
                sleep(1);
                
            } catch (\Exception $e) {
                $this->error("❌ Error monitoring {$competitor->username}: " . $e->getMessage());
                
                Log::error('Competitor monitoring failed', [
                    'competitor_id' => $competitor->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("\n📊 Monitoring Summary:");
        $this->info("🔔 New alerts created: {$alertsCreated}");
        $this->info('✅ Monitoring completed!');
    }

    /**
     * Check competitor activities (simulated with AI insights)
     */
    private function checkCompetitorActivities(Competitor $competitor): array
    {
        $activities = [];
        
        // Simulate random activities based on competitor profile
        $activityChance = rand(1, 100);
        
        // 30% chance of new post
        if ($activityChance <= 30) {
            $activities[] = [
                'type' => 'new_post',
                'title' => 'Post Baru Terdeteksi!',
                'message' => "{$competitor->username} baru saja posting konten baru",
                'data' => [
                    'post_type' => ['image', 'video', 'reel', 'carousel'][rand(0, 3)],
                    'estimated_engagement' => rand(200, 800) / 100,
                    'detected_at' => now()->toISOString()
                ]
            ];
        }
        
        // 15% chance of high engagement post
        if ($activityChance <= 15) {
            $activities[] = [
                'type' => 'viral_content',
                'title' => 'Konten Viral Terdeteksi!',
                'message' => "Post {$competitor->username} mencapai engagement tinggi dalam waktu singkat",
                'data' => [
                    'engagement_rate' => rand(800, 1500) / 100,
                    'viral_score' => rand(7, 10),
                    'trend_potential' => 'high'
                ]
            ];
        }
        
        // 10% chance of promo detection
        if ($activityChance <= 10) {
            $activities[] = [
                'type' => 'promo_detected',
                'title' => 'Promo/Diskon Terdeteksi!',
                'message' => "{$competitor->username} sedang menjalankan promo atau penawaran khusus",
                'data' => [
                    'promo_type' => ['discount', 'flash_sale', 'bundle', 'free_shipping'][rand(0, 3)],
                    'urgency_level' => ['low', 'medium', 'high'][rand(0, 2)],
                    'competitive_threat' => rand(6, 9)
                ]
            ];
        }
        
        // 5% chance of strategy change
        if ($activityChance <= 5) {
            $activities[] = [
                'type' => 'pattern_change',
                'title' => 'Perubahan Strategi Terdeteksi!',
                'message' => "AI mendeteksi perubahan pola posting atau strategi konten {$competitor->username}",
                'data' => [
                    'change_type' => ['posting_frequency', 'content_style', 'hashtag_strategy'][rand(0, 2)],
                    'impact_level' => ['medium', 'high'][rand(0, 1)],
                    'adaptation_required' => true
                ]
            ];
        }
        
        return $activities;
    }

    /**
     * Create activity alert
     */
    private function createActivityAlert(Competitor $competitor, array $activity): void
    {
        try {
            CompetitorAlert::create([
                'user_id' => $competitor->user_id,
                'competitor_id' => $competitor->id,
                'alert_type' => $activity['type'],
                'alert_title' => $activity['title'],
                'alert_message' => $activity['message'],
                'alert_data' => array_merge($activity['data'], [
                    'monitoring_source' => 'automated',
                    'confidence_score' => rand(75, 95)
                ]),
                'triggered_at' => now()
            ]);

            $this->info("  🔔 Alert created: {$activity['title']}");
            
        } catch (\Exception $e) {
            $this->error("  ❌ Failed to create alert: " . $e->getMessage());
        }
    }
}