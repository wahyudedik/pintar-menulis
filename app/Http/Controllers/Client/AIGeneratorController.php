<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Services\MLDataService;
use App\Services\GooglePlacesService;
use Illuminate\Http\Request;

class AIGeneratorController extends Controller
{
    protected $aiService;
    protected $mlService;
    protected $googleService;

    public function __construct(
        AIService $aiService,
        MLDataService $mlService,
        GooglePlacesService $googleService
    ) {
        $this->aiService = $aiService;
        $this->mlService = $mlService;
        $this->googleService = $googleService;
    }

    public function index()
    {
        return view('client.ai-generator');
    }

    /**
     * Check if user is first-time (has no caption history)
     */
    public function checkFirstTime()
    {
        $userId = auth()->id();
        $historyCount = \App\Models\CaptionHistory::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'is_first_time' => ($historyCount === 0)
        ]);
    }

    /**
     * 🤖 Get ML status and suggestions
     */
    public function getMLStatus()
    {
        $userId = auth()->id();
        
        return response()->json([
            'success' => true,
            'should_upgrade' => $this->mlService->shouldSuggestUpgrade($userId),
            'upgrade_suggestion' => $this->mlService->getUpgradeSuggestion(),
        ]);
    }

    /**
     * 🤖 Get ML data preview (for testing)
     */
    public function getMLPreview(Request $request)
    {
        $industry = $request->input('industry', 'fashion');
        $platform = $request->input('platform', 'instagram');
        
        return response()->json([
            'success' => true,
            'hashtags' => $this->mlService->getTrendingHashtags($industry, $platform, 10),
            'topics' => $this->mlService->getTrendingTopics($industry, 5),
            'hooks' => $this->mlService->getBestHooks($industry, 'casual', 3),
            'ctas' => $this->mlService->getBestCTAs($industry, 'closing', 3),
        ]);
    }


    public function generate(Request $request)
    {
        // Set longer execution time for this specific request
        set_time_limit(300); // 5 minutes
        ini_set('max_execution_time', 300);
        
        try {
            $validated = $request->validate([
                'category' => 'required|string',
                'subcategory' => 'required|string',
                'platform' => 'nullable|string',
                'brief' => 'required|string|min:10',
                'tone' => 'required|string',
                'keywords' => 'nullable|string',
                'generate_variations' => 'nullable|boolean',
                'variation_count' => 'nullable|integer|in:5,10,15,20',
                'auto_hashtag' => 'nullable|boolean',
                'local_language' => 'nullable|string',
                'mode' => 'nullable|string|in:simple,advanced',
                'industry' => 'nullable|string', // 🤖 ML: industry for ML data
                'goal' => 'nullable|string', // 🤖 ML: goal for CTAs
            ]);

            $params = [
                'category' => $validated['category'],
                'subcategory' => $validated['subcategory'],
                'brief' => $validated['brief'],
                'tone' => $validated['tone'],
                'platform' => $validated['platform'] ?? 'instagram',
                'keywords' => $validated['keywords'] ?? '',
                'generate_variations' => $validated['generate_variations'] ?? false,
                'variation_count' => $validated['variation_count'] ?? 5,
                'auto_hashtag' => $validated['auto_hashtag'] ?? true,
                'local_language' => $validated['local_language'] ?? '',
                'mode' => $validated['mode'] ?? 'simple',
                'user_id' => auth()->id(),
                'industry' => $validated['industry'] ?? 'general',
                'goal' => $validated['goal'] ?? 'closing',
            ];

            // 🤖 ML DATA INTEGRATION
            $mlData = $this->getMLData($params, false); // Always use ML (no Google API)
            
            // Add ML suggestions to params
            $params['ml_hashtags'] = $mlData['hashtags'];
            $params['ml_keywords'] = $mlData['keywords'];
            $params['ml_hooks'] = $mlData['hooks'];
            $params['ml_ctas'] = $mlData['ctas'];
            $params['ml_topics'] = $mlData['topics'];

            // 🔍 KEYWORD RESEARCH INTEGRATION
            $googleAds = app(\App\Services\GoogleAdsService::class);
            
            // Extract keywords from brief
            $extractedKeywords = $googleAds->extractKeywordsFromCaption($validated['brief']);
            
            // Get keyword data for top 3 keywords
            $keywordInsights = [];
            foreach (array_slice($extractedKeywords, 0, 3) as $keyword) {
                $keywordData = $googleAds->getKeywordIdeas($keyword);
                
                // Sanitize all string values for JSON encoding
                $keywordData = $this->sanitizeForJson($keywordData);
                
                $keywordInsights[] = $keywordData;
                
                // Save to database for analytics
                $googleAds->saveKeywordResearch(auth()->id(), $keywordData);
            }

            $result = $this->aiService->generateCopywriting($params);

            // Record caption history for each generated caption
            $lastCaptionId = null;
            if (isset($result) && is_string($result)) {
                // Parse result to extract individual captions
                $captions = $this->extractCaptions($result);
                
                foreach ($captions as $caption) {
                    $history = \App\Models\CaptionHistory::recordCaption(
                        auth()->id(),
                        $caption,
                        $params
                    );
                    
                    // Link keywords to caption
                    foreach ($keywordInsights as $kwData) {
                        \App\Models\CaptionKeyword::create([
                            'caption_history_id' => $history->id,
                            'keyword' => $kwData['keyword'],
                            'search_volume' => $kwData['search_volume'],
                            'competition' => $kwData['competition'],
                            'cpc_low' => $kwData['cpc_low'],
                            'cpc_high' => $kwData['cpc_high'],
                            'relevance_score' => 0.8, // Default relevance
                        ]);
                    }
                    
                    // Store the last caption ID for rating
                    $lastCaptionId = $history->id;
                }
            }

            // 🤖 ML: Check if should suggest upgrade (removed - fokus ke AI optimization)
            // Semua user langsung pakai ML data yang sudah optimal

            return response()->json([
                'success' => true,
                'result' => $result,
                'caption_id' => $lastCaptionId,
                'keyword_insights' => $keywordInsights,
                'ml_data' => $mlData, // 🤖 ML: Return ML suggestions
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                $errors[] = implode(', ', $messages);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode('; ', $errors)
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('AI Generate Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Handle specific timeout errors
            if (strpos($e->getMessage(), 'Maximum execution time') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => '⏱️ Proses generate membutuhkan waktu lebih lama dari biasanya. Silakan coba dengan brief yang lebih singkat atau coba lagi dalam beberapa saat.'
                ], 500);
            }
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🤖 Get ML data (free only - no Google API)
     */
    private function getMLData(array $params, bool $useGoogleAPI = false): array
    {
        $industry = $params['industry'];
        $platform = $params['platform'];
        $tone = $params['tone'];
        $goal = $params['goal'];
        
        $mlData = [
            'hashtags' => [],
            'keywords' => [],
            'hooks' => [],
            'ctas' => [],
            'topics' => [],
            'source' => 'ml', // Always ML (no Google API)
        ];
        
        // Always use ML data (free)
        $mlData['hashtags'] = $this->mlService->getTrendingHashtags($industry, $platform, 30);
        $mlData['keywords'] = $this->mlService->getKeywordSuggestions(
            $params['brief'],
            $industry,
            10
        );
        
        // Always get hooks, CTAs, topics from ML
        $mlData['hooks'] = $this->mlService->getBestHooks($industry, $tone, 5);
        $mlData['ctas'] = $this->mlService->getBestCTAs($industry, $goal, 5);
        $mlData['topics'] = $this->mlService->getTrendingTopics($industry, 5);
        
        return $mlData;
    }

    /**
     * Extract individual captions from AI result
     */
    private function extractCaptions(string $result): array
    {
        $captions = [];
        
        // Try to split by numbered list (1., 2., 3., etc)
        if (preg_match_all('/\d+\.\s*(.+?)(?=\d+\.|$)/s', $result, $matches)) {
            $captions = array_map('trim', $matches[1]);
        } else {
            // If no numbered list, treat whole result as one caption
            $captions = [$result];
        }
        
        return array_filter($captions); // Remove empty entries
    }

    /**
     * Sanitize data for JSON encoding (remove non-UTF8 characters)
     */
    private function sanitizeForJson($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeForJson'], $data);
        }
        
        if (is_string($data)) {
            // Convert to UTF-8 and remove invalid characters
            $clean = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            // Remove any control characters except newlines and tabs
            $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $clean);
            return $clean;
        }
        
        return $data;
    }

    public function generateImageCaption(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'business_type' => 'nullable|string',
            'product_name' => 'nullable|string',
        ]);

        try {
            // Upload image
            $image = $request->file('image');
            $path = $image->store('image-captions', 'public');
            $fullPath = storage_path('app/public/' . $path);

            // Convert image to base64
            $imageData = base64_encode(file_get_contents($fullPath));
            $mimeType = $image->getMimeType();

            // Generate caption using AIService (with all optimizations!)
            $result = $this->aiService->generateImageCaption([
                'user_id' => auth()->id(),
                'image_data' => $imageData,
                'mime_type' => $mimeType,
                'business_type' => $request->business_type ?? 'UMKM',
                'product_name' => $request->product_name ?? '',
            ]);

            // Save to caption_history for ML training using recordCaption method
            $captionHistory = \App\Models\CaptionHistory::recordCaption(
                auth()->id(),
                $result['caption_single'],
                [
                    'brief' => "Image Caption: " . ($request->product_name ?? 'Produk'),
                    'category' => $request->business_type ?? 'umkm',
                    'platform' => 'instagram',
                    'tone' => 'engaging',
                ]
            );

            // Delete temporary image file
            \Storage::disk('public')->delete($path);

            return response()->json([
                'success' => true,
                'detected_objects' => $result['detected_objects'],
                'dominant_colors' => $result['dominant_colors'],
                'caption_single' => $result['caption_single'],
                'caption_carousel' => $result['caption_carousel'],
                'editing_tips' => $result['editing_tips'],
                'caption_id' => $captionHistory->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Image Caption Generation Failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate caption: ' . $e->getMessage()
            ], 500);
        }
    }
}
