<?php

namespace App\Http\Controllers;

use App\Services\MLSuggestionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MLSuggestionsController extends Controller
{
    protected $mlSuggestionsService;

    public function __construct(MLSuggestionsService $mlSuggestionsService)
    {
        $this->mlSuggestionsService = $mlSuggestionsService;
    }

    /**
     * Get ML preview data (trending suggestions)
     */
    public function getPreview(Request $request)
    {
        try {
            $industry = $request->get('industry', 'fashion');
            $platform = $request->get('platform', 'instagram');
            
            // Validate industry and platform
            $validIndustries = ['fashion', 'food', 'beauty', 'printing', 'photography', 'catering', 'tiktok_shop', 'shopee_affiliate', 'home_decor', 'handmade', 'digital_service', 'automotive', 'general'];
            $validPlatforms = ['instagram', 'tiktok', 'facebook', 'twitter', 'linkedin'];
            
            if (!in_array($industry, $validIndustries)) {
                $industry = 'fashion';
            }
            
            if (!in_array($platform, $validPlatforms)) {
                $platform = 'instagram';
            }

            // Get personalized suggestions if user is logged in
            if (auth()->check()) {
                $suggestions = $this->mlSuggestionsService->getPersonalizedSuggestions(
                    auth()->id(),
                    $industry,
                    $platform
                );
            } else {
                $suggestions = $this->mlSuggestionsService->getTrendingSuggestions($industry, $platform);
            }

            if (!$suggestions['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to generate suggestions',
                ], 500);
            }

            // Format response for frontend
            $data = $suggestions['data'];
            
            return response()->json([
                'success' => true,
                'trending_hashtags' => $data['trending_hashtags'] ?? $data['personalized_hashtags'] ?? [],
                'best_hooks' => $data['best_hooks'] ?? $data['style_matched_hooks'] ?? [],
                'best_ctas' => $data['best_ctas'] ?? $data['brand_voice_ctas'] ?? [],
                'trending_topics' => $data['trending_topics'] ?? $data['trending_for_your_niche'] ?? [],
                'engagement_tips' => $data['engagement_tips'] ?? $data['content_improvement_tips'] ?? [],
                'optimal_posting_times' => $data['optimal_posting_times'] ?? $data['optimal_times_for_audience'] ?? [],
                'current_trends' => $data['current_trends'] ?? [],
                'content_ideas' => $data['content_ideas'] ?? $data['next_content_ideas'] ?? [],
                'generated_at' => $suggestions['generated_at'],
                'personalized' => $suggestions['personalized'] ?? false,
                'industry' => $industry,
                'platform' => $platform,
            ]);

        } catch (\Throwable $e) {
            Log::error("ML Preview error: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load ML suggestions',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get ML status
     */
    public function getStatus()
    {
        try {
            $user = auth()->user();
            
            // Check if user has generated content recently
            $recentGenerations = 0;
            if ($user) {
                // Try to get recent generations, fallback to 0 if model doesn't exist
                try {
                    if (class_exists('\App\Models\Caption')) {
                        $recentGenerations = \App\Models\Caption::where('user_id', $user->id)
                            ->where('created_at', '>=', now()->subDays(7))
                            ->count();
                    }
                } catch (\Exception $e) {
                    // Model doesn't exist, use default
                    $recentGenerations = 0;
                }
            }

            // Determine if should show upgrade suggestion
            $shouldUpgrade = $recentGenerations > 5 && !$this->hasGoogleAPIEnabled();
            
            $status = [
                'success' => true,
                'ml_enabled' => true,
                'google_api_enabled' => $this->hasGoogleAPIEnabled(),
                'recent_generations' => $recentGenerations,
                'should_upgrade' => $shouldUpgrade,
                'features' => [
                    'trending_suggestions' => true,
                    'personalized_recommendations' => auth()->check(),
                    'weekly_trend_analysis' => true,
                    'real_time_hashtags' => $this->hasGoogleAPIEnabled(),
                    'competitor_analysis' => $this->hasGoogleAPIEnabled(),
                ],
            ];

            // Add upgrade suggestion if needed
            if ($shouldUpgrade) {
                $status['upgrade_suggestion'] = [
                    'title' => '🚀 Tingkatkan Performa dengan Google API',
                    'message' => 'Dapatkan trending hashtags real-time dan analisis kompetitor yang lebih akurat!',
                    'benefits' => [
                        'Hashtags trending real-time dari Google Trends',
                        'Analisis kompetitor otomatis',
                        'Keyword research yang lebih mendalam',
                        'Prediksi viral content dengan akurasi tinggi',
                        'Update suggestions setiap jam',
                    ],
                    'cta' => 'Aktifkan Google API',
                ];
            }

            return response()->json($status);

        } catch (\Throwable $e) {
            Log::error("ML Status error: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get ML status',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get weekly trend analysis
     */
    public function getWeeklyTrends(Request $request)
    {
        try {
            $industry = $request->get('industry', 'fashion');
            
            $analysis = $this->mlSuggestionsService->getWeeklyTrendAnalysis($industry);
            
            if (!$analysis['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to generate weekly analysis',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $analysis['data'],
                'generated_at' => $analysis['generated_at'],
                'industry' => $industry,
            ]);

        } catch (\Exception $e) {
            Log::error("Weekly trends error: {$e->getMessage()}");
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load weekly trends',
            ], 500);
        }
    }

    /**
     * Force refresh ML suggestions (admin only)
     */
    public function refreshSuggestions(Request $request)
    {
        try {
            // Check if user is admin or has permission
            if (!auth()->check() || !auth()->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized',
                ], 403);
            }

            $this->mlSuggestionsService->clearCache();
            
            return response()->json([
                'success' => true,
                'message' => 'ML suggestions cache cleared successfully',
            ]);

        } catch (\Exception $e) {
            Log::error("Refresh suggestions error: {$e->getMessage()}");
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to refresh suggestions',
            ], 500);
        }
    }

    /**
     * Get cache statistics (admin only)
     */
    public function getCacheStats()
    {
        try {
            if (!auth()->check() || !auth()->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized',
                ], 403);
            }

            $stats = $this->mlSuggestionsService->getCacheStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            Log::error("Cache stats error: {$e->getMessage()}");
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get cache stats',
            ], 500);
        }
    }

    /**
     * Check if Google API is enabled
     */
    protected function hasGoogleAPIEnabled()
    {
        // Check if Google API keys are configured
        return !empty(config('services.google.api_key')) && 
               !empty(config('services.google.search_engine_id'));
    }
}