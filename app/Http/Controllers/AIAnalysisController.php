<?php

namespace App\Http\Controllers;

use App\Services\AIAnalysisService;
use App\Models\CaptionQualityScore;
use App\Models\SentimentAnalysis;
use App\Models\ImageAnalysis;
use App\Models\CampaignAnalytic;
use App\Models\ArticleQualityAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIAnalysisController extends Controller
{
    protected $analysisService;

    public function __construct(AIAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    /**
     * Analyze caption sentiment
     */
    public function analyzeSentiment(Request $request)
    {
        $request->validate([
            'caption' => 'required|string|min:5|max:2000',
        ]);

        try {
            $result = $this->analysisService->analyzeSentiment($request->caption);

            // Try to store in database, but don't fail if it doesn't work
            if ($result['success']) {
                try {
                    SentimentAnalysis::create([
                        'user_id' => auth()->id(),
                        'caption' => substr($request->caption, 0, 2000),
                        'sentiment' => $result['data']['sentiment'],
                        'sentiment_score' => $result['data']['score'],
                        'keywords' => $result['data']['keywords'] ?? [],
                        'explanation' => substr($result['data']['explanation'] ?? '', 0, 1000),
                        'full_analysis' => json_encode($result['data']),
                    ]);
                } catch (\Throwable $dbError) {
                    Log::warning("Failed to store sentiment analysis: {$dbError->getMessage()}");
                    // Continue anyway
                }
            }

            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error("Sentiment analysis error: {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze sentiment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze image and suggest captions
     */
    public function analyzeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
        ]);

        try {
            $imagePath = $request->file('image')->store('analysis', 'public');
            $result = $this->analysisService->analyzeImage($imagePath);

            if ($result['success']) {
                // Store in database
                ImageAnalysis::create([
                    'user_id' => auth()->id(),
                    'image_path' => $imagePath,
                    'image_description' => $result['data']['image_description'] ?? '',
                    'suggested_captions' => $result['data']['suggested_captions'] ?? [],
                    'hashtags' => $result['data']['hashtags'] ?? [],
                    'best_time_to_post' => $result['data']['best_time_to_post'] ?? '',
                    'content_type' => $result['data']['content_type'] ?? '',
                    'full_analysis' => json_encode($result['data']),
                ]);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("Image analysis error: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze image',
            ], 500);
        }
    }

    /**
     * Score caption quality
     */
    public function scoreCaption(Request $request)
    {
        $request->validate([
            'caption' => 'required|string|min:5|max:2000',
            'platform' => 'required|in:instagram,tiktok,facebook,twitter,linkedin',
            'industry' => 'nullable|string|max:100',
        ]);

        try {
            $result = $this->analysisService->scoreCaption(
                $request->caption,
                $request->platform,
                $request->industry
            );

            // Try to store in database, but don't fail if it doesn't work
            if ($result['success']) {
                try {
                    CaptionQualityScore::create([
                        'user_id' => auth()->id(),
                        'caption' => substr($request->caption, 0, 2000),
                        'platform' => $request->platform,
                        'industry' => $request->industry,
                        'overall_score' => $result['data']['overall_score'] ?? 5,
                        'engagement_score' => $result['data']['engagement_score'] ?? 5,
                        'clarity_score' => $result['data']['clarity_score'] ?? 5,
                        'cta_score' => $result['data']['call_to_action_score'] ?? 5,
                        'emoji_score' => $result['data']['emoji_usage_score'] ?? 5,
                        'analysis_data' => json_encode($result['data']),
                    ]);
                } catch (\Throwable $dbError) {
                    Log::warning("Failed to store caption quality score: {$dbError->getMessage()}");
                    // Continue anyway - the analysis was successful
                }
            }

            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error("Caption scoring error: {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to score caption: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get smart recommendations
     */
    public function getRecommendations(Request $request)
    {
        $request->validate([
            'caption' => 'required|string|min:5|max:2000',
            'platform' => 'required|in:instagram,tiktok,facebook,twitter,linkedin',
            'target_audience' => 'nullable|string|max:200',
        ]);

        try {
            $result = $this->analysisService->getSmartRecommendations(
                $request->caption,
                $request->platform,
                $request->target_audience
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("Recommendations error: {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate recommendations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze campaign performance
     */
    public function analyzeCampaign(Request $request)
    {
        $request->validate([
            'captions' => 'required|array|min:3',
            'captions.*' => 'string|min:5|max:2000',
            'ratings' => 'required|array|min:3',
            'ratings.*' => 'numeric|min:1|max:10',
        ]);

        try {
            $result = $this->analysisService->analyzeCampaignPerformance(
                $request->captions,
                $request->ratings,
                $request->engagement_data ?? []
            );

            if ($result['success']) {
                // Store in database
                CampaignAnalytic::create([
                    'user_id' => auth()->id(),
                    'campaign_name' => $request->campaign_name ?? 'Campaign ' . now()->format('Y-m-d'),
                    'total_captions' => count($request->captions),
                    'average_rating' => array_sum($request->ratings) / count($request->ratings),
                    'top_patterns' => $result['data']['top_performing_patterns'] ?? [],
                    'weak_areas' => $result['data']['weak_areas'] ?? [],
                    'recommendations' => $result['data']['recommendations'] ?? [],
                    'trending_elements' => $result['data']['trending_elements'] ?? [],
                    'full_analysis' => json_encode($result['data']),
                ]);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("Campaign analysis error: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze campaign',
            ], 500);
        }
    }

    /**
     * Analyze article quality
     */
    public function analyzeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:5|max:200',
            'content' => 'required|string|min:50|max:10000',
            'keywords' => 'nullable|string|max:500',
        ]);

        try {
            $result = $this->analysisService->analyzeArticleQuality(
                $request->title,
                $request->content,
                $request->keywords
            );

            if ($result['success']) {
                // Store in database
                ArticleQualityAnalysis::create([
                    'article_id' => $request->article_id ?? null,
                    'seo_score' => $result['data']['seo_score'] ?? 50,
                    'readability_score' => $result['data']['readability_score'] ?? 50,
                    'engagement_score' => $result['data']['engagement_score'] ?? 50,
                    'keyword_optimization' => $result['data']['keyword_optimization'] ?? 'fair',
                    'strengths' => $result['data']['strengths'] ?? [],
                    'improvements' => $result['data']['improvements'] ?? [],
                    'suggested_title' => $result['data']['suggested_title'] ?? '',
                    'meta_description' => $result['data']['meta_description'] ?? '',
                    'internal_links' => $result['data']['internal_links'] ?? [],
                    'external_links' => $result['data']['external_links'] ?? [],
                    'overall_quality' => $result['data']['overall_quality'] ?? 'fair',
                    'full_analysis' => json_encode($result['data']),
                ]);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("Article analysis error: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze article',
            ], 500);
        }
    }

    /**
     * Get user's analysis history
     */
    public function getHistory(Request $request)
    {
        $type = $request->query('type'); // sentiment, image, quality, campaign, article

        try {
            $query = match ($type) {
                'sentiment' => SentimentAnalysis::where('user_id', auth()->id()),
                'image' => ImageAnalysis::where('user_id', auth()->id()),
                'quality' => CaptionQualityScore::where('user_id', auth()->id()),
                'campaign' => CampaignAnalytic::where('user_id', auth()->id()),
                'article' => ArticleQualityAnalysis::whereHas('article', function ($q) {
                    $q->where('user_id', auth()->id());
                }),
                default => null,
            };

            if (!$query) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid analysis type',
                ], 400);
            }

            $results = $query->latest()->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            Log::error("History retrieval error: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve history',
            ], 500);
        }
    }

    /**
     * Get analytics dashboard data
     */
    public function getDashboard()
    {
        try {
            $userId = auth()->id();

            $data = [
                'total_analyses' => SentimentAnalysis::where('user_id', $userId)->count() +
                                   ImageAnalysis::where('user_id', $userId)->count() +
                                   CaptionQualityScore::where('user_id', $userId)->count(),
                'average_caption_score' => CaptionQualityScore::where('user_id', $userId)
                    ->avg('overall_score') ?? 0,
                'sentiment_breakdown' => SentimentAnalysis::where('user_id', $userId)
                    ->selectRaw('sentiment, count(*) as count')
                    ->groupBy('sentiment')
                    ->get(),
                'top_platforms' => CaptionQualityScore::where('user_id', $userId)
                    ->selectRaw('platform, avg(overall_score) as avg_score, count(*) as count')
                    ->groupBy('platform')
                    ->orderByDesc('avg_score')
                    ->limit(5)
                    ->get(),
                'recent_analyses' => [
                    'sentiments' => SentimentAnalysis::where('user_id', $userId)->latest()->limit(5)->get(),
                    'images' => ImageAnalysis::where('user_id', $userId)->latest()->limit(5)->get(),
                    'quality_scores' => CaptionQualityScore::where('user_id', $userId)->latest()->limit(5)->get(),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error("Dashboard error: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'error' => 'Failed to load dashboard',
            ], 500);
        }
    }
}
