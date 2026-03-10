<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MLSuggestionsService
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Get trending suggestions for industry and platform
     */
    public function getTrendingSuggestions($industry = 'fashion', $platform = 'instagram')
    {
        $cacheKey = "ml_suggestions_{$industry}_{$platform}_" . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, 3600, function () use ($industry, $platform) {
            return $this->generateTrendingSuggestions($industry, $platform);
        });
    }

    /**
     * Generate fresh trending suggestions using AI
     */
    protected function generateTrendingSuggestions($industry, $platform)
    {
        try {
            $currentDate = now()->format('d F Y');
            $dayOfWeek = now()->format('l');
            
            $prompt = "Generate trending social media suggestions for {$industry} industry on {$platform} platform for today ({$currentDate}, {$dayOfWeek}).

Respond in JSON format:
{
  \"trending_hashtags\": [\"#hashtag1\", \"#hashtag2\", \"#hashtag3\", \"#hashtag4\", \"#hashtag5\"],
  \"best_hooks\": [\"hook1\", \"hook2\", \"hook3\"],
  \"best_ctas\": [\"cta1\", \"cta2\", \"cta3\"],
  \"trending_topics\": [\"topic1\", \"topic2\", \"topic3\"],
  \"optimal_posting_times\": [\"time1\", \"time2\", \"time3\"],
  \"engagement_tips\": [\"tip1\", \"tip2\", \"tip3\"],
  \"current_trends\": [\"trend1\", \"trend2\", \"trend3\"],
  \"seasonal_keywords\": [\"keyword1\", \"keyword2\", \"keyword3\"],
  \"competitor_insights\": [\"insight1\", \"insight2\", \"insight3\"],
  \"content_ideas\": [\"idea1\", \"idea2\", \"idea3\"]
}

Requirements:
- Use Indonesian language for hooks, CTAs, and tips
- Make hashtags relevant to current trends and {$industry}
- Consider {$dayOfWeek} timing for posting suggestions
- Include seasonal/monthly trends for {$currentDate}
- Make suggestions specific to {$platform} algorithm preferences
- Focus on high-engagement content types for {$industry}";

            $response = $this->geminiService->generateText($prompt, 800, 0.7);
            $parsed = $this->parseJsonResponse($response);

            if ($parsed['success']) {
                return [
                    'success' => true,
                    'data' => $parsed['data'],
                    'generated_at' => now()->toISOString(),
                    'industry' => $industry,
                    'platform' => $platform,
                ];
            }

            return $this->getFallbackSuggestions($industry, $platform);

        } catch (\Exception $e) {
            Log::error("ML Suggestions generation error: {$e->getMessage()}");
            return $this->getFallbackSuggestions($industry, $platform);
        }
    }

    /**
     * Get personalized suggestions based on user's content history
     */
    public function getPersonalizedSuggestions($userId, $industry, $platform)
    {
        $cacheKey = "ml_personalized_{$userId}_{$industry}_{$platform}_" . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, 1800, function () use ($userId, $industry, $platform) {
            return $this->generatePersonalizedSuggestions($userId, $industry, $platform);
        });
    }

    /**
     * Generate personalized suggestions based on user history
     */
    protected function generatePersonalizedSuggestions($userId, $industry, $platform)
    {
        try {
            // Get user's recent captions for analysis
            $recentCaptions = [];
            
            // Try to get captions if model exists
            try {
                if (class_exists('\App\Models\Caption')) {
                    $recentCaptions = \App\Models\Caption::where('user_id', $userId)
                        ->where('created_at', '>=', now()->subDays(30))
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->pluck('content')
                        ->toArray();
                }
            } catch (\Exception $e) {
                // Model doesn't exist or error, continue with empty array
                Log::warning("Could not fetch user captions: {$e->getMessage()}");
            }

            if (empty($recentCaptions)) {
                return $this->getTrendingSuggestions($industry, $platform);
            }

            $captionSample = implode("\n---\n", array_slice($recentCaptions, 0, 5));
            $currentDate = now()->format('d F Y');

            $prompt = "Analyze these recent captions from a {$industry} business and provide personalized trending suggestions for {$platform} (today: {$currentDate}):

Recent Captions:
{$captionSample}

Based on their writing style and {$industry} focus, provide personalized suggestions in JSON:
{
  \"personalized_hashtags\": [\"#hashtag1\", \"#hashtag2\", \"#hashtag3\", \"#hashtag4\", \"#hashtag5\"],
  \"style_matched_hooks\": [\"hook1\", \"hook2\", \"hook3\"],
  \"brand_voice_ctas\": [\"cta1\", \"cta2\", \"cta3\"],
  \"content_improvement_tips\": [\"tip1\", \"tip2\", \"tip3\"],
  \"trending_for_your_niche\": [\"trend1\", \"trend2\", \"trend3\"],
  \"engagement_boosters\": [\"booster1\", \"booster2\", \"booster3\"],
  \"next_content_ideas\": [\"idea1\", \"idea2\", \"idea3\"],
  \"optimal_times_for_audience\": [\"time1\", \"time2\", \"time3\"]
}

Make suggestions that:
- Match their existing tone and style
- Improve upon their current approach
- Are trending specifically for {$industry} on {$platform}
- Consider today's date ({$currentDate}) for seasonal relevance";

            $response = $this->geminiService->generateText($prompt, 800, 0.6);
            $parsed = $this->parseJsonResponse($response);

            if ($parsed['success']) {
                return [
                    'success' => true,
                    'data' => $parsed['data'],
                    'generated_at' => now()->toISOString(),
                    'personalized' => true,
                    'user_id' => $userId,
                    'industry' => $industry,
                    'platform' => $platform,
                ];
            }

            return $this->getTrendingSuggestions($industry, $platform);

        } catch (\Exception $e) {
            Log::error("Personalized ML Suggestions error: {$e->getMessage()}");
            return $this->getTrendingSuggestions($industry, $platform);
        }
    }

    /**
     * Get weekly trend analysis
     */
    public function getWeeklyTrendAnalysis($industry)
    {
        $cacheKey = "ml_weekly_trends_{$industry}_" . now()->startOfWeek()->format('Y-m-d');
        
        return Cache::remember($cacheKey, 7200, function () use ($industry) {
            return $this->generateWeeklyTrendAnalysis($industry);
        });
    }

    /**
     * Generate weekly trend analysis
     */
    protected function generateWeeklyTrendAnalysis($industry)
    {
        try {
            $currentWeek = now()->format('W');
            $currentMonth = now()->format('F Y');
            $currentDate = now()->format('d F Y');

            $prompt = "Provide weekly trend analysis for {$industry} industry (Week {$currentWeek} of {$currentMonth}, today: {$currentDate}).

Analyze current market trends and provide insights in JSON:
{
  \"week_highlights\": [\"highlight1\", \"highlight2\", \"highlight3\"],
  \"rising_trends\": [\"trend1\", \"trend2\", \"trend3\"],
  \"declining_trends\": [\"trend1\", \"trend2\", \"trend3\"],
  \"opportunity_keywords\": [\"keyword1\", \"keyword2\", \"keyword3\"],
  \"competitor_moves\": [\"move1\", \"move2\", \"move3\"],
  \"content_gaps\": [\"gap1\", \"gap2\", \"gap3\"],
  \"viral_potential_topics\": [\"topic1\", \"topic2\", \"topic3\"],
  \"week_strategy\": [\"strategy1\", \"strategy2\", \"strategy3\"],
  \"next_week_predictions\": [\"prediction1\", \"prediction2\", \"prediction3\"]
}

Focus on:
- Current events affecting {$industry}
- Seasonal trends for this time of year
- Social media algorithm changes
- Consumer behavior shifts
- Emerging hashtags and topics";

            $response = $this->geminiService->generateText($prompt, 1000, 0.5);
            $parsed = $this->parseJsonResponse($response);

            if ($parsed['success']) {
                return [
                    'success' => true,
                    'data' => $parsed['data'],
                    'generated_at' => now()->toISOString(),
                    'week' => $currentWeek,
                    'industry' => $industry,
                ];
            }

            return $this->getFallbackWeeklyAnalysis($industry);

        } catch (\Exception $e) {
            Log::error("Weekly trend analysis error: {$e->getMessage()}");
            return $this->getFallbackWeeklyAnalysis($industry);
        }
    }

    /**
     * Parse JSON response from Gemini
     */
    protected function parseJsonResponse($response)
    {
        try {
            // Clean response
            $response = preg_replace('/```json\n?|\n?```/i', '', $response);
            $response = trim($response);
            
            if (preg_match('/\{.*\}/s', $response, $matches)) {
                $response = $matches[0];
            }
            
            $decoded = json_decode($response, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return [
                    'success' => true,
                    'data' => $decoded,
                ];
            }
            
            return ['success' => false, 'error' => 'Invalid JSON'];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get fallback suggestions when AI fails
     */
    protected function getFallbackSuggestions($industry, $platform)
    {
        $industryData = $this->getIndustryFallbackData($industry);
        $platformData = $this->getPlatformFallbackData($platform);
        
        return [
            'success' => true,
            'data' => [
                'trending_hashtags' => array_merge($industryData['hashtags'], $platformData['hashtags']),
                'best_hooks' => $industryData['hooks'],
                'best_ctas' => $industryData['ctas'],
                'trending_topics' => $industryData['topics'],
                'optimal_posting_times' => $platformData['posting_times'],
                'engagement_tips' => $industryData['tips'],
                'current_trends' => ['Konten autentik', 'Video pendek', 'User-generated content'],
                'seasonal_keywords' => $this->getSeasonalKeywords(),
                'competitor_insights' => ['Fokus pada storytelling', 'Interaksi tinggi di comments', 'Konsistensi posting'],
                'content_ideas' => $industryData['content_ideas'],
            ],
            'generated_at' => now()->toISOString(),
            'industry' => $industry,
            'platform' => $platform,
            'fallback' => true,
        ];
    }

    /**
     * Get industry-specific fallback data
     */
    protected function getIndustryFallbackData($industry)
    {
        $data = [
            'fashion' => [
                'hashtags' => ['#fashion', '#ootd', '#style', '#fashionista', '#trendy'],
                'hooks' => ['Outfit hari ini bikin percaya diri!', 'Style yang lagi trending nih!', 'Fashion hack yang wajib kamu tahu!'],
                'ctas' => ['Shop sekarang!', 'DM untuk order!', 'Link di bio ya!'],
                'topics' => ['Trend fashion terbaru', 'Mix and match outfit', 'Fashion tips'],
                'tips' => ['Posting di jam prime time', 'Gunakan lighting yang bagus', 'Konsisten dengan brand aesthetic'],
                'content_ideas' => ['Outfit of the day', 'Fashion haul', 'Styling tips'],
            ],
            'food' => [
                'hashtags' => ['#food', '#foodie', '#kuliner', '#makanan', '#enak'],
                'hooks' => ['Makanan yang bikin nagih!', 'Resep rahasia yang wajib dicoba!', 'Kuliner hits yang lagi viral!'],
                'ctas' => ['Order sekarang!', 'Pesan via WhatsApp!', 'Delivery available!'],
                'topics' => ['Kuliner viral', 'Resep mudah', 'Food review'],
                'tips' => ['Foto makanan dengan pencahayaan natural', 'Ceritakan proses pembuatan', 'Tampilkan testimoni customer'],
                'content_ideas' => ['Behind the scenes cooking', 'Customer reactions', 'Recipe tutorials'],
            ],
            'beauty' => [
                'hashtags' => ['#beauty', '#skincare', '#makeup', '#glowing', '#cantik'],
                'hooks' => ['Skincare routine yang bikin glowing!', 'Makeup look yang stunning!', 'Rahasia kulit sehat!'],
                'ctas' => ['Coba sekarang!', 'Get yours now!', 'Limited stock!'],
                'topics' => ['Skincare tips', 'Makeup tutorial', 'Beauty trends'],
                'tips' => ['Before-after photos work well', 'Share personal experience', 'Use good lighting for skin'],
                'content_ideas' => ['Skincare routine', 'Makeup transformation', 'Product reviews'],
            ],
        ];

        return $data[$industry] ?? $data['fashion'];
    }

    /**
     * Get platform-specific fallback data
     */
    protected function getPlatformFallbackData($platform)
    {
        $data = [
            'instagram' => [
                'hashtags' => ['#instagram', '#instagood', '#photooftheday'],
                'posting_times' => ['08:00-09:00', '12:00-13:00', '19:00-21:00'],
            ],
            'tiktok' => [
                'hashtags' => ['#tiktok', '#fyp', '#viral'],
                'posting_times' => ['06:00-10:00', '19:00-23:00'],
            ],
            'facebook' => [
                'hashtags' => ['#facebook', '#social', '#community'],
                'posting_times' => ['09:00-10:00', '15:00-16:00', '20:00-21:00'],
            ],
        ];

        return $data[$platform] ?? $data['instagram'];
    }

    /**
     * Get seasonal keywords based on current date
     */
    protected function getSeasonalKeywords()
    {
        $month = now()->month;
        
        $seasonal = [
            1 => ['tahunbaru', 'resolusi', 'fresh'],
            2 => ['valentine', 'love', 'romantic'],
            3 => ['spring', 'fresh', 'renewal'],
            4 => ['easter', 'spring', 'bloom'],
            5 => ['mother', 'family', 'love'],
            6 => ['summer', 'vacation', 'fun'],
            7 => ['summer', 'holiday', 'freedom'],
            8 => ['independence', 'merdeka', 'patriotic'],
            9 => ['back2school', 'autumn', 'new'],
            10 => ['halloween', 'spooky', 'autumn'],
            11 => ['thanksgiving', 'grateful', 'family'],
            12 => ['christmas', 'holiday', 'celebration'],
        ];

        return $seasonal[$month] ?? ['trending', 'popular', 'viral'];
    }

    /**
     * Get fallback weekly analysis
     */
    protected function getFallbackWeeklyAnalysis($industry)
    {
        return [
            'success' => true,
            'data' => [
                'week_highlights' => ['Engagement rate meningkat', 'Video content trending', 'User interaction tinggi'],
                'rising_trends' => ['Short-form video', 'Behind the scenes', 'User testimonials'],
                'declining_trends' => ['Static images', 'Long captions', 'Generic content'],
                'opportunity_keywords' => ['authentic', 'trending', 'viral'],
                'competitor_moves' => ['Lebih fokus video', 'Interaksi dengan audience', 'Kolaborasi influencer'],
                'content_gaps' => ['Educational content', 'Behind the scenes', 'Customer stories'],
                'viral_potential_topics' => ['Tips and tricks', 'Transformation', 'Challenges'],
                'week_strategy' => ['Post consistently', 'Engage with comments', 'Use trending hashtags'],
                'next_week_predictions' => ['Video akan dominan', 'Interaksi real-time penting', 'Authenticity key'],
            ],
            'generated_at' => now()->toISOString(),
            'industry' => $industry,
            'fallback' => true,
        ];
    }

    /**
     * Clear all ML suggestions cache
     */
    public function clearCache()
    {
        $patterns = [
            'ml_suggestions_*',
            'ml_personalized_*',
            'ml_weekly_trends_*',
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats()
    {
        // This would need Redis or another cache driver that supports pattern matching
        return [
            'cache_enabled' => true,
            'last_updated' => now()->toISOString(),
            'cache_ttl' => '1 hour for suggestions, 2 hours for weekly trends',
        ];
    }
}