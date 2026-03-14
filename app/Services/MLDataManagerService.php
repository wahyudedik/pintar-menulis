<?php

namespace App\Services;

use App\Models\Competitor;
use App\Models\CompetitorPost;
use App\Models\CompetitorAnalysisSummary;
use App\Models\MLDataCache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class MLDataManagerService
{
    private SocialMediaApiService $apiService;
    private GeminiService $aiService;

    public function __construct(SocialMediaApiService $apiService, GeminiService $aiService)
    {
        $this->apiService = $apiService;
        $this->aiService = $aiService;
    }

    /**
     * Smart data fetching with ML-based decision making (Queue-optimized)
     */
    public function getCompetitorData(string $username, string $platform, bool $forceRefresh = false): array
    {
        try {
            // 1. Check if we have recent ML data
            $mlData = $this->getMLData($username, $platform);
            
            // 2. If force refresh or no data, fetch immediately
            if ($forceRefresh || !$mlData) {
                return $this->fetchFreshDataSync($username, $platform);
            }
            
            // 3. AI decides if we need fresh data
            $needsFreshData = $this->aiDecisionEngine($mlData, $username, $platform);
            
            if ($needsFreshData) {
                // Check if there's already a job running for this competitor
                $jobKey = "competitor_job_{$username}_{$platform}";
                $existingJob = Cache::get($jobKey);
                
                if ($existingJob && $existingJob['status'] === 'running') {
                    Log::info('Job already running, returning cached data', [
                        'username' => $username,
                        'platform' => $platform
                    ]);
                    
                    // Return cached data while job is running
                    return [
                        'success' => true,
                        'data' => $mlData->profile_data,
                        'source' => 'ml_cache_while_refreshing',
                        'last_updated' => $mlData->updated_at,
                        'job_status' => 'refreshing_in_background'
                    ];
                }
                
                // Dispatch background job for fresh data
                $priority = $this->calculateJobPriority($mlData);
                \App\Jobs\RefreshCompetitorDataJob::dispatch($username, $platform, $priority)
                    ->onQueue($this->getQueueName($priority));
                
                Log::info('Background refresh job dispatched', [
                    'username' => $username,
                    'platform' => $platform,
                    'priority' => $priority,
                    'reason' => $needsFreshData['reason']
                ]);
                
                // Mark job as running
                Cache::put($jobKey, [
                    'status' => 'running',
                    'started_at' => now()->toISOString()
                ], now()->addHours(2));
                
                // Return cached data immediately
                $mlData->recordCacheHit();
                return [
                    'success' => true,
                    'data' => $mlData->profile_data,
                    'source' => 'ml_cache_refreshing',
                    'last_updated' => $mlData->updated_at,
                    'job_status' => 'refresh_dispatched'
                ];
            }
            
            // 4. Use ML data - no refresh needed
            if ($mlData) {
                $mlData->recordCacheHit();
                
                Log::info('Using ML cached data', [
                    'username' => $username,
                    'platform' => $platform,
                    'cache_age' => $mlData->updated_at->diffInHours(now()) . ' hours'
                ]);
                
                return [
                    'success' => true,
                    'data' => $mlData->profile_data,
                    'source' => 'ml_cache',
                    'last_updated' => $mlData->updated_at
                ];
            }
            
            // 5. Fallback: Fetch fresh data synchronously for new profiles
            return $this->fetchFreshDataSync($username, $platform);
            
        } catch (\Exception $e) {
            Log::error('ML Data Manager failed', [
                'username' => $username,
                'platform' => $platform,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Fetch fresh data synchronously (for immediate needs)
     */
    public function fetchFreshDataSync(string $username, string $platform): array
    {
        Log::info('Fetching fresh data synchronously', [
            'username' => $username,
            'platform' => $platform
        ]);
        
        $apiData = $this->apiService->fetchRealProfileData($username, $platform);
        
        if ($apiData['success']) {
            // Enhance data with AI analysis
            $enhancedData = $this->enhanceDataWithAI($apiData['data'], $platform);
            
            // Update ML cache
            $this->updateMLCache($username, $platform, $enhancedData);
            
            // Train ML model
            $this->trainMLModel($username, $platform, $enhancedData);
            
            return [
                'success' => true,
                'data' => $enhancedData,
                'source' => 'fresh_api_sync'
            ];
        }
        
        return $apiData;
    }

    /**
     * Calculate job priority based on competitor importance
     */
    private function calculateJobPriority(MLDataCache $mlData): string
    {
        $followers = $mlData->profile_data['followers_count'] ?? 0;
        $engagement = $mlData->profile_data['engagement_rate'] ?? 0;
        $isVerified = $mlData->profile_data['is_verified'] ?? false;
        
        // High priority: Large accounts, high engagement, verified
        if ($followers > 100000 || $engagement > 5 || $isVerified) {
            return 'high';
        }
        
        // Low priority: Small accounts, low engagement
        if ($followers < 1000 && $engagement < 2) {
            return 'low';
        }
        
        return 'normal';
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
     * AI-powered decision engine to determine if fresh data is needed
     */
    private function aiDecisionEngine(?MLDataCache $mlData, string $username, string $platform): array|false
    {
        // If no ML data exists, definitely need fresh data
        if (!$mlData) {
            return [
                'decision' => true,
                'reason' => 'No existing ML data found'
            ];
        }

        // Calculate data age
        $dataAge = $mlData->updated_at->diffInHours(now());
        $lastApiCall = $mlData->last_api_call ? $mlData->last_api_call->diffInHours(now()) : 999;

        // AI prompt for decision making
        $prompt = "Analisis apakah perlu fetch data baru dari API untuk kompetitor analysis.

DATA EXISTING:
- Username: {$username}
- Platform: {$platform}
- Data age: {$dataAge} jam
- Last API call: {$lastApiCall} jam yang lalu
- Followers: " . ($mlData->profile_data['followers_count'] ?? 0) . "
- Posts count: " . ($mlData->profile_data['posts_count'] ?? 0) . "
- Engagement rate: " . ($mlData->profile_data['engagement_rate'] ?? 0) . "%
- Account type: " . ($mlData->profile_data['category'] ?? 'unknown') . "

DECISION RULES:
1. Akun besar (>100k followers): Update setiap 6 jam
2. Akun medium (10k-100k): Update setiap 12 jam  
3. Akun kecil (<10k): Update setiap 24 jam
4. Akun bisnis/verified: Update lebih sering
5. Jika engagement tinggi (>5%): Update lebih sering
6. Jika data >48 jam: Pasti update

Berikan keputusan dalam JSON:
{
    \"need_fresh_data\": true/false,
    \"reason\": \"alasan keputusan\",
    \"priority\": \"high/medium/low\",
    \"suggested_interval\": \"jam untuk update berikutnya\"
}";

        try {
            $aiResponse = $this->aiService->generateText($prompt, 300, 0.3);
            
            // Parse AI response
            if (preg_match('/\{.*\}/s', $aiResponse, $matches)) {
                $decision = json_decode($matches[0], true);
                
                if ($decision && isset($decision['need_fresh_data'])) {
                    return $decision['need_fresh_data'] ? $decision : false;
                }
            }
        } catch (\Exception $e) {
            Log::warning('AI decision engine failed, using fallback logic', [
                'error' => $e->getMessage()
            ]);
        }

        // Fallback logic if AI fails
        $followers = $mlData->profile_data['followers_count'] ?? 0;
        $engagementRate = $mlData->profile_data['engagement_rate'] ?? 0;
        
        // High priority accounts need frequent updates
        if ($followers > 100000 || $engagementRate > 5) {
            return $dataAge > 6 ? ['reason' => 'High priority account needs update'] : false;
        }
        
        // Medium accounts
        if ($followers > 10000) {
            return $dataAge > 12 ? ['reason' => 'Medium account scheduled update'] : false;
        }
        
        // Small accounts
        return $dataAge > 24 ? ['reason' => 'Regular scheduled update'] : false;
    }

    /**
     * Get ML cached data
     */
    private function getMLData(string $username, string $platform): ?MLDataCache
    {
        return MLDataCache::where('username', $username)
            ->where('platform', $platform)
            ->first();
    }

    /**
     * Fetch fresh data from API
     */
    private function fetchFreshData(string $username, string $platform): array
    {
        $apiData = $this->apiService->fetchRealProfileData($username, $platform);
        
        if ($apiData['success']) {
            // Enhance data with AI analysis
            $enhancedData = $this->enhanceDataWithAI($apiData['data'], $platform);
            
            return [
                'success' => true,
                'data' => $enhancedData,
                'source' => 'fresh_api'
            ];
        }
        
        return $apiData;
    }

    /**
     * Enhance API data with AI analysis
     */
    private function enhanceDataWithAI(array $profileData, string $platform): array
    {
        $prompt = "Analisis profil kompetitor dan berikan insights tambahan.

PROFILE DATA:
" . json_encode($profileData, JSON_PRETTY_PRINT) . "

Berikan analisis dalam JSON:
{
    \"account_tier\": \"micro/small/medium/large/mega\",
    \"engagement_quality\": \"low/medium/high/excellent\",
    \"content_strategy\": \"deskripsi strategi konten\",
    \"growth_potential\": \"low/medium/high\",
    \"competitive_threat\": \"low/medium/high\",
    \"recommended_action\": \"rekomendasi untuk menghadapi kompetitor ini\"
}";

        try {
            $aiResponse = $this->aiService->generateText($prompt, 400, 0.5);
            
            if (preg_match('/\{.*\}/s', $aiResponse, $matches)) {
                $aiInsights = json_decode($matches[0], true);
                
                if ($aiInsights) {
                    $profileData['ai_insights'] = $aiInsights;
                    $profileData['analysis_timestamp'] = now()->toISOString();
                }
            }
        } catch (\Exception $e) {
            Log::warning('AI enhancement failed', ['error' => $e->getMessage()]);
        }

        return $profileData;
    }

    /**
     * Update ML cache with new data
     */
    private function updateMLCache(string $username, string $platform, array $data): void
    {
        MLDataCache::updateOrCreate(
            [
                'username' => $username,
                'platform' => $platform
            ],
            [
                'profile_data' => $data,
                'last_api_call' => now(),
                'api_calls_count' => \DB::raw('api_calls_count + 1'),
                'data_quality_score' => $this->calculateDataQuality($data),
                'updated_at' => now()
            ]
        );
    }

    /**
     * Calculate data quality score
     */
    private function calculateDataQuality(array $data): float
    {
        $score = 0;
        $maxScore = 100;
        
        // Profile completeness
        if (!empty($data['profile_name'])) $score += 15;
        if (!empty($data['bio'])) $score += 10;
        if (!empty($data['profile_picture'])) $score += 10;
        
        // Metrics availability
        if (isset($data['followers_count']) && $data['followers_count'] > 0) $score += 20;
        if (isset($data['posts_count']) && $data['posts_count'] > 0) $score += 15;
        if (isset($data['engagement_rate']) && $data['engagement_rate'] > 0) $score += 15;
        
        // Data freshness and source
        if ($data['api_source'] ?? false) $score += 10;
        if (isset($data['ai_insights'])) $score += 5;
        
        return min($score, $maxScore);
    }

    /**
     * Train ML model with new patterns
     */
    private function trainMLModel(string $username, string $platform, array $data): void
    {
        try {
            // Extract patterns for ML training
            $patterns = [
                'platform' => $platform,
                'followers_tier' => $this->getFollowersTier($data['followers_count'] ?? 0),
                'engagement_tier' => $this->getEngagementTier($data['engagement_rate'] ?? 0),
                'content_frequency' => $this->estimateContentFrequency($data),
                'account_type' => $data['category'] ?? 'unknown',
                'verification_status' => $data['is_verified'] ?? false,
                'data_timestamp' => now()->toISOString()
            ];

            // Store pattern for future ML training
            Cache::put(
                "ml_pattern_{$platform}_{$username}_" . now()->format('Y-m-d'),
                $patterns,
                now()->addDays(30)
            );

            Log::info('ML pattern stored for training', [
                'username' => $username,
                'platform' => $platform,
                'patterns' => $patterns
            ]);

        } catch (\Exception $e) {
            Log::error('ML training failed', [
                'username' => $username,
                'platform' => $platform,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get followers tier classification
     */
    private function getFollowersTier(int $followers): string
    {
        if ($followers >= 1000000) return 'mega';
        if ($followers >= 100000) return 'large';
        if ($followers >= 10000) return 'medium';
        if ($followers >= 1000) return 'small';
        return 'micro';
    }

    /**
     * Get engagement tier classification
     */
    private function getEngagementTier(float $engagement): string
    {
        if ($engagement >= 10) return 'excellent';
        if ($engagement >= 5) return 'high';
        if ($engagement >= 2) return 'medium';
        return 'low';
    }

    /**
     * Estimate content frequency
     */
    private function estimateContentFrequency(array $data): string
    {
        $posts = $data['posts_count'] ?? 0;
        
        if ($posts > 1000) return 'very_high';
        if ($posts > 500) return 'high';
        if ($posts > 100) return 'medium';
        if ($posts > 10) return 'low';
        return 'very_low';
    }

    /**
     * Get ML statistics and insights
     */
    public function getMLStatistics(): array
    {
        $totalCached = MLDataCache::count();
        $recentUpdates = MLDataCache::where('updated_at', '>', now()->subDays(7))->count();
        $apiCallsSaved = MLDataCache::sum('api_calls_saved') ?? 0;
        
        $platformStats = MLDataCache::selectRaw('platform, COUNT(*) as count, AVG(data_quality_score) as avg_quality')
            ->groupBy('platform')
            ->get();

        return [
            'total_profiles_cached' => $totalCached,
            'recent_updates' => $recentUpdates,
            'api_calls_saved' => $apiCallsSaved,
            'platform_distribution' => $platformStats,
            'cache_efficiency' => $totalCached > 0 ? round(($apiCallsSaved / $totalCached) * 100, 2) : 0
        ];
    }

    /**
     * Clean old ML data
     */
    public function cleanOldMLData(): int
    {
        // Remove data older than 90 days with low quality scores
        $deleted = MLDataCache::where('updated_at', '<', now()->subDays(90))
            ->where('data_quality_score', '<', 50)
            ->delete();

        Log::info('ML data cleanup completed', ['deleted_records' => $deleted]);
        
        return $deleted;
    }
}