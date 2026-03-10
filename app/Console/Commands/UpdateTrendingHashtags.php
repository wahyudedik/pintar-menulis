<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrendingHashtag;
use App\Services\HashtagModerationService;
use Carbon\Carbon;

class UpdateTrendingHashtags extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'hashtags:update
                            {--platform= : Specific platform to update (instagram, tiktok, facebook, youtube, twitter, linkedin)}
                            {--force : Force update even if recently updated}';

    /**
     * The console command description.
     */
    protected $description = 'Update trending hashtags with fresh data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $platform = $this->option('platform');
        $force = $this->option('force');
        
        $this->info('🔄 Starting hashtag update...');
        
        if ($platform) {
            $this->updatePlatform($platform, $force);
        } else {
            // Update all platforms
            $platforms = ['instagram', 'tiktok', 'facebook', 'youtube', 'twitter', 'linkedin'];
            foreach ($platforms as $plat) {
                $this->updatePlatform($plat, $force);
            }
        }
        
        $this->info('✅ Hashtag update completed!');
        
        // Show statistics
        $this->showStatistics();
        
        return Command::SUCCESS;
    }
    
    /**
     * Update hashtags for specific platform
     */
    private function updatePlatform(string $platform, bool $force): void
    {
        $this->line("Updating {$platform} hashtags...");
        
        // Check if recently updated (within 6 hours)
        if (!$force) {
            $recentUpdate = TrendingHashtag::where('platform', $platform)
                ->where('last_updated', '>=', now()->subHours(6))
                ->exists();
            
            if ($recentUpdate) {
                $this->warn("  ⏭️  {$platform} was recently updated. Use --force to override.");
                return;
            }
        }
        
        // Simulate fetching fresh data (in production, this would call real APIs)
        $freshData = $this->fetchFreshHashtags($platform);
        
        // 🔒 SECURITY: Filter hashtags through moderation
        $moderation = app(HashtagModerationService::class);
        
        $updated = 0;
        $created = 0;
        $blocked = 0;
        
        foreach ($freshData as $data) {
            // Check if hashtag is safe
            if (!$moderation->isSafe($data['hashtag'])) {
                $blocked++;
                continue;
            }
            
            // Check quality metrics
            if (!$moderation->validateQuality($data['hashtag'], $data)) {
                $this->warn("  ⚠️  Low quality: {$data['hashtag']}");
                continue;
            }
            
            $hashtag = TrendingHashtag::updateOrCreate(
                [
                    'hashtag' => $data['hashtag'],
                    'platform' => $platform,
                ],
                [
                    'trend_score' => $data['trend_score'],
                    'usage_count' => $data['usage_count'],
                    'engagement_rate' => $data['engagement_rate'],
                    'category' => $data['category'],
                    'country' => 'ID',
                    'last_updated' => now(),
                ]
            );
            
            if ($hashtag->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }
        
        $message = "  ✅ {$platform}: {$updated} updated, {$created} created";
        if ($blocked > 0) {
            $message .= ", {$blocked} blocked";
        }
        $this->info($message);
    }
    
    /**
     * Fetch fresh hashtags (simulated - in production use real APIs)
     */
    private function fetchFreshHashtags(string $platform): array
    {
        // In production, this would:
        // 1. Call platform APIs (Instagram Graph API, TikTok API, etc.)
        // 2. Scrape trending pages (with proper rate limiting)
        // 3. Use third-party services (RapidAPI, etc.)
        
        // For now, we simulate by slightly randomizing existing data
        $existing = TrendingHashtag::where('platform', $platform)
            ->where('country', 'ID')
            ->get();
        
        if ($existing->isEmpty()) {
            $this->warn("  ⚠️  No existing data for {$platform}. Run seeder first.");
            return [];
        }
        
        return $existing->map(function ($hashtag) {
            // Simulate trend changes (±10%)
            $trendChange = rand(-10, 10);
            $usageChange = rand(-5000, 5000);
            $engagementChange = rand(-5, 5) / 10;
            
            return [
                'hashtag' => $hashtag->hashtag,
                'trend_score' => max(50, min(100, $hashtag->trend_score + $trendChange)),
                'usage_count' => max(10000, $hashtag->usage_count + $usageChange),
                'engagement_rate' => max(1.0, min(10.0, $hashtag->engagement_rate + $engagementChange)),
                'category' => $hashtag->category,
            ];
        })->toArray();
    }
    
    /**
     * Show hashtag statistics
     */
    private function showStatistics(): void
    {
        $this->newLine();
        $this->info('📊 Hashtag Statistics:');
        $this->table(
            ['Platform', 'Total', 'Last Updated'],
            $this->getStatistics()
        );
    }
    
    /**
     * Get statistics data
     */
    private function getStatistics(): array
    {
        $platforms = ['instagram', 'tiktok', 'facebook', 'youtube', 'twitter', 'linkedin'];
        $stats = [];
        
        foreach ($platforms as $platform) {
            $count = TrendingHashtag::where('platform', $platform)->count();
            $lastUpdate = TrendingHashtag::where('platform', $platform)
                ->max('last_updated');
            
            $stats[] = [
                ucfirst($platform),
                $count,
                $lastUpdate ? Carbon::parse($lastUpdate)->diffForHumans() : 'Never'
            ];
        }
        
        return $stats;
    }
}
