<?php

namespace App\Services;

use App\Models\MLOptimizedData;
use App\Models\CaptionAnalytics;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * ML Data Service
 * 
 * Manages optimized ML data (free tier)
 * Auto-updates from real analytics data
 */
class MLDataService
{
    /**
     * Get trending hashtags from ML data
     */
    public function getTrendingHashtags(string $industry, string $platform = 'instagram', int $limit = 30): array
    {
        $cacheKey = "ml_hashtags_{$industry}_{$platform}";
        
        return Cache::remember($cacheKey, 3600, function () use ($industry, $platform, $limit) {
            $data = MLOptimizedData::where('type', 'hashtag')
                ->where('industry', $industry)
                ->where('platform', $platform)
                ->where('is_active', true)
                ->orderBy('performance_score', 'desc')
                ->limit($limit)
                ->get();
            
            if ($data->isEmpty()) {
                return $this->getDefaultHashtags($industry);
            }
            
            return $data->pluck('data')->toArray();
        });
    }
    
    /**
     * Get keyword suggestions from ML data
     */
    public function getKeywordSuggestions(string $query, string $industry, int $limit = 10): array
    {
        $queryHash = md5($query);
        $cacheKey = "ml_keywords_{$queryHash}_{$industry}";
        
        return Cache::remember($cacheKey, 3600, function () use ($query, $industry, $limit) {
            $data = MLOptimizedData::where('type', 'keyword')
                ->where('industry', $industry)
                ->where('data', 'like', "%{$query}%")
                ->where('is_active', true)
                ->orderBy('performance_score', 'desc')
                ->limit($limit)
                ->get();
            
            if ($data->isEmpty()) {
                return $this->getDefaultKeywords($query, $industry);
            }
            
            return $data->map(function ($item) {
                return [
                    'keyword' => $item->data,
                    'score' => $item->performance_score,
                    'search_volume' => $item->metadata['search_volume'] ?? 0,
                ];
            })->toArray();
        });
    }
    
    /**
     * Get trending topics from ML data
     */
    public function getTrendingTopics(string $industry, int $limit = 10): array
    {
        $cacheKey = "ml_topics_{$industry}";
        
        return Cache::remember($cacheKey, 3600, function () use ($industry, $limit) {
            $data = MLOptimizedData::where('type', 'topic')
                ->where('industry', $industry)
                ->where('is_active', true)
                ->orderBy('performance_score', 'desc')
                ->limit($limit)
                ->get();
            
            return $data->map(function ($item) {
                return [
                    'topic' => $item->data,
                    'score' => $item->performance_score,
                    'engagement_rate' => $item->metadata['engagement_rate'] ?? 0,
                ];
            })->toArray();
        });
    }
    
    /**
     * Get best performing hooks from ML data
     */
    public function getBestHooks(string $industry, string $tone, int $limit = 5): array
    {
        $cacheKey = "ml_hooks_{$industry}_{$tone}";
        
        return Cache::remember($cacheKey, 3600, function () use ($industry, $tone, $limit) {
            $data = MLOptimizedData::where('type', 'hook')
                ->where('industry', $industry)
                ->where('metadata->tone', $tone)
                ->where('is_active', true)
                ->orderBy('performance_score', 'desc')
                ->limit($limit)
                ->get();
            
            return $data->pluck('data')->toArray();
        });
    }
    
    /**
     * Get best performing CTAs from ML data
     */
    public function getBestCTAs(string $industry, string $goal, int $limit = 5): array
    {
        $cacheKey = "ml_ctas_{$industry}_{$goal}";
        
        return Cache::remember($cacheKey, 3600, function () use ($industry, $goal, $limit) {
            $data = MLOptimizedData::where('type', 'cta')
                ->where('industry', $industry)
                ->where('metadata->goal', $goal)
                ->where('is_active', true)
                ->orderBy('performance_score', 'desc')
                ->limit($limit)
                ->get();
            
            return $data->pluck('data')->toArray();
        });
    }
    
    /**
     * Check if user should upgrade to Google API
     */
    public function shouldSuggestUpgrade(int $userId): bool
    {
        // Check user's caption performance
        $avgEngagement = CaptionAnalytics::where('user_id', $userId)
            ->whereNotNull('engagement_rate')
            ->avg('engagement_rate');
        
        // If engagement rate < 2%, suggest upgrade
        return $avgEngagement !== null && $avgEngagement < 2.0;
    }
    
    /**
     * Get upgrade suggestion message
     */
    public function getUpgradeSuggestion(): array
    {
        return [
            'title' => '💡 Tingkatkan Performa Caption Anda',
            'message' => 'Engagement rate Anda masih di bawah rata-rata. Gunakan data real dari Google untuk hasil lebih akurat!',
            'benefits' => [
                'Data trending real-time dari Google',
                'Keyword research lebih akurat',
                'Hashtag berdasarkan lokasi',
                'Analisis kompetitor',
            ],
            'cta' => 'Upgrade ke Google API',
        ];
    }
    
    /**
     * Default hashtags (fallback)
     */
    private function getDefaultHashtags(string $industry): array
    {
        $defaults = [
            'fashion' => ['#fashion', '#ootd', '#style', '#fashionista', '#fashionblogger'],
            'food' => ['#food', '#foodie', '#foodporn', '#instafood', '#foodstagram'],
            'beauty' => ['#beauty', '#makeup', '#skincare', '#beautyblogger', '#makeuptutorial'],
            'printing' => ['#printing', '#printingservice', '#customprint', '#printshop', '#digitalprinting'],
            'photography' => ['#photography', '#photographer', '#photooftheday', '#photoshoot', '#portrait'],
        ];
        
        return $defaults[$industry] ?? ['#umkm', '#umkmindonesia', '#bisnislokal'];
    }
    
    /**
     * Default keywords (fallback)
     */
    private function getDefaultKeywords(string $query, string $industry): array
    {
        return [
            [
                'keyword' => $query,
                'score' => 50,
                'search_volume' => 1000,
            ]
        ];
    }
}
