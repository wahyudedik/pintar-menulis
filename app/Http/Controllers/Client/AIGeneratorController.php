<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Services\MLDataService;
use Illuminate\Http\Request;

class AIGeneratorController extends Controller
{
    protected $aiService;
    protected $mlService;
    protected $geminiService;

    public function __construct(
        AIService $aiService,
        MLDataService $mlService,
        \App\Services\GeminiService $geminiService
    ) {
        $this->aiService = $aiService;
        $this->mlService = $mlService;
        $this->geminiService = $geminiService;
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

        // ── Subscription & daily limit check ─────────────────────────────────
        $user = auth()->user();
        $sub  = $user->currentSubscription();

        if (!$sub || !$sub->isValid()) {
            return response()->json([
                'success'     => false,
                'quota_error' => true,
                'message'     => '⚡ Kamu belum memiliki langganan aktif. <a href="' . route('pricing') . '" class="underline font-semibold">Mulai trial gratis 30 hari</a> untuk menggunakan fitur AI.',
            ], 403);
        }

        if ($sub->remaining_quota <= 0) {
            return response()->json([
                'success'     => false,
                'quota_error' => true,
                'message'     => '🚫 Kuota AI kamu sudah habis bulan ini. <a href="' . route('pricing') . '" class="underline font-semibold">Upgrade paket</a> untuk kuota lebih banyak.',
            ], 403);
        }

        // Free plan: max 5 generate per day
        if ($sub->package && $sub->package->price == 0) {
            $todayCount = \App\Models\CaptionHistory::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count();
            if ($todayCount >= 5) {
                return response()->json([
                    'success'     => false,
                    'quota_error' => true,
                    'message'     => '⏳ Batas harian paket Gratis adalah 5 generate/hari. Reset besok pukul 00:00. <a href="' . route('pricing') . '" class="underline font-semibold">Upgrade sekarang</a> untuk generate tanpa batas harian.',
                ], 403);
            }
        }
        // ─────────────────────────────────────────────────────────────────────

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

            // 🔍 KEYWORD RESEARCH INTEGRATION (Using ML Data Service)
            // Extract keywords from brief using AI
            $extractedKeywords = $this->extractKeywordsFromBrief($validated['brief']);
            
            // Get keyword insights from ML data
            $keywordInsights = [];
            foreach (array_slice($extractedKeywords, 0, 3) as $keyword) {
                $keywordData = $this->getKeywordInsights($keyword);
                
                // Sanitize all string values for JSON encoding
                $keywordData = $this->sanitizeForJson($keywordData);
                
                $keywordInsights[] = $keywordData;
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
                    
                    // Store the last caption ID for rating
                    $lastCaptionId = $history->id;
                }
            }

            // Consume one quota unit after successful generation
            $sub->consumeQuota(1);

            return response()->json([
                'success' => true,
                'result' => $result,
                'caption_id' => $lastCaptionId,
                'keyword_insights' => $keywordInsights,
                'ml_data' => $mlData, // 🤖 ML: Return ML suggestions
                'quota_remaining' => $sub->fresh()->remaining_quota,
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

    /**
     * 🔍 Analyze image with Gemini Vision
     */
    public function analyzeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB max
            'options' => 'required|json',
            'context' => 'nullable|string|max:1000',
        ]);

        try {
            $options = json_decode($request->options, true);
            
            if (empty($options)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mohon pilih minimal satu jenis analisis'
                ], 400);
            }

            // Upload image
            $image = $request->file('image');
            $path = $image->store('image-analysis', 'public');
            $fullPath = storage_path('app/public/' . $path);

            // Convert image to base64
            $imageData = base64_encode(file_get_contents($fullPath));
            $mimeType = $image->getMimeType();

            // Generate analysis using Gemini Vision
            $result = $this->geminiService->analyzeImageWithVision([
                'user_id' => auth()->id(),
                'image_data' => $imageData,
                'mime_type' => $mimeType,
                'analysis_options' => $options,
                'context' => $request->context ?? '',
            ]);

            // Save to caption_history for tracking
            $captionHistory = \App\Models\CaptionHistory::recordCaption(
                auth()->id(),
                $result['analysis'],
                [
                    'brief' => "Image Analysis: " . implode(', ', $options),
                    'category' => 'image-analysis',
                    'platform' => 'analysis',
                    'tone' => 'analytical',
                ]
            );

            // Delete temporary image file
            \Storage::disk('public')->delete($path);

            return response()->json([
                'success' => true,
                'analysis' => $result['analysis'],
                'caption_id' => $captionHistory->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Image Analysis Failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'options' => $request->options ?? 'none'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menganalisis gambar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🎬 Generate video content (script, storyboard, hooks, ideas)
     */
    public function generateVideoContent(Request $request)
    {
        $request->validate([
            'content_type' => 'required|in:script,storyboard,hook,ideas',
            'platform' => 'required|string',
            'duration' => 'required|integer|min:15|max:90',
            'product' => 'required|string|max:500',
            'target_audience' => 'nullable|string|max:200',
            'goal' => 'required|string',
            'styles' => 'nullable|array',
            'context' => 'nullable|string|max:1000',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        try {
            $imageAnalysis = null;
            
            // Process product image if uploaded
            if ($request->hasFile('product_image')) {
                $image = $request->file('product_image');
                $path = $image->store('video-products', 'public');
                $fullPath = storage_path('app/public/' . $path);

                // Convert image to base64 for Gemini Vision
                $imageData = base64_encode(file_get_contents($fullPath));
                $mimeType = $image->getMimeType();

                // Analyze image with Gemini Vision for video context
                $imageAnalysis = $this->geminiService->analyzeProductImageForVideo([
                    'image_data' => $imageData,
                    'mime_type' => $mimeType,
                    'product_name' => $request->product,
                    'video_platform' => $request->platform,
                    'video_goal' => $request->goal,
                ]);

                // Delete temporary image file
                \Storage::disk('public')->delete($path);
            }

            // Generate video content using Gemini
            $result = $this->geminiService->generateVideoContent([
                'user_id' => auth()->id(),
                'content_type' => $request->content_type,
                'platform' => $request->platform,
                'duration' => $request->duration,
                'product' => $request->product,
                'target_audience' => $request->target_audience ?? '',
                'goal' => $request->goal,
                'styles' => $request->styles ?? [],
                'context' => $request->context ?? '',
                'image_analysis' => $imageAnalysis, // Include image analysis
            ]);

            // Save to caption_history for tracking
            $captionHistory = \App\Models\CaptionHistory::recordCaption(
                auth()->id(),
                $result['content'],
                [
                    'brief' => "Video {$request->content_type}: {$request->product}" . ($imageAnalysis ? ' (with image)' : ''),
                    'category' => 'video-content',
                    'platform' => $request->platform,
                    'tone' => 'engaging',
                ]
            );

            return response()->json([
                'success' => true,
                'content' => $result['content'],
                'caption_id' => $captionHistory->id,
                'has_image_analysis' => $imageAnalysis !== null,
            ]);

        } catch (\Exception $e) {
            \Log::error('Video Content Generation Failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'content_type' => $request->content_type ?? 'none',
                'has_image' => $request->hasFile('product_image')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate konten video: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract keywords from brief using simple text analysis
     */
    private function extractKeywordsFromBrief(string $brief): array
    {
        // Simple keyword extraction - remove common words and get meaningful terms
        $commonWords = ['dan', 'atau', 'yang', 'untuk', 'dengan', 'dari', 'ke', 'di', 'pada', 'adalah', 'akan', 'dapat', 'bisa', 'juga', 'ini', 'itu', 'the', 'and', 'or', 'for', 'with', 'from', 'to', 'in', 'on', 'is', 'will', 'can', 'also', 'this', 'that'];
        
        $words = preg_split('/[\s,.\-!?]+/', strtolower($brief));
        $keywords = array_filter($words, function($word) use ($commonWords) {
            return strlen($word) > 3 && !in_array($word, $commonWords);
        });
        
        return array_values(array_unique($keywords));
    }

    /**
     * Get keyword insights using ML data instead of Google Ads
     */
    private function getKeywordInsights(string $keyword): array
    {
        try {
            // Use AI to generate keyword insights
            $prompt = "Analisis keyword '{$keyword}' untuk social media marketing Indonesia:

Berikan data dalam format JSON:
{
    \"keyword\": \"{$keyword}\",
    \"search_volume\": \"estimasi volume pencarian bulanan\",
    \"competition\": \"low/medium/high\",
    \"trend\": \"growth factor (0.8-1.5)\",
    \"related_keywords\": [\"keyword terkait 1\", \"keyword terkait 2\", \"keyword terkait 3\"]
}

Berikan estimasi realistis berdasarkan tren Indonesia.";

            $geminiService = app(\App\Services\GeminiService::class);
            $aiResponse = $geminiService->generateText($prompt, 300, 0.7);
            
            // Try to parse AI response
            if (preg_match('/\{.*\}/s', $aiResponse, $matches)) {
                $insights = json_decode($matches[0], true);
                if ($insights && is_array($insights)) {
                    return [
                        'keyword' => $insights['keyword'] ?? $keyword,
                        'search_volume' => is_numeric($insights['search_volume']) ? (int)$insights['search_volume'] : rand(1000, 50000),
                        'competition' => in_array($insights['competition'], ['low', 'medium', 'high']) ? $insights['competition'] : 'medium',
                        'trend' => is_numeric($insights['trend']) ? (float)$insights['trend'] : 1.0,
                        'related_keywords' => is_array($insights['related_keywords']) ? $insights['related_keywords'] : [
                            $keyword . ' terbaik',
                            $keyword . ' murah',
                            'tips ' . $keyword
                        ]
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::warning('AI keyword insights failed, using fallback: ' . $e->getMessage());
        }

        // Fallback with more realistic data
        return [
            'keyword' => $keyword,
            'search_volume' => rand(1000, 50000),
            'competition' => ['low', 'medium', 'high'][rand(0, 2)],
            'trend' => rand(80, 120) / 100,
            'related_keywords' => [
                $keyword . ' terbaik',
                $keyword . ' murah',
                'tips ' . $keyword
            ]
        ];
    }
    /**
     * 📈 CAPTION PERFORMANCE PREDICTOR - Main Feature
     */
    public function predictPerformance(Request $request)
    {
        try {
            $validated = $request->validate([
                'caption' => 'required|string|min:10',
                'platform' => 'nullable|string|in:instagram,facebook,tiktok,youtube,linkedin',
                'industry' => 'nullable|string',
                'target_audience' => 'nullable|string',
            ]);

            // Initialize services safely
            $geminiService = $this->geminiService ?? app(\App\Services\GeminiService::class);
            $mlService = $this->mlService ?? app(\App\Services\MLDataService::class);

            // Initialize Performance Predictor Service
            $predictorService = new \App\Services\CaptionPerformancePredictorService(
                $geminiService,
                $mlService
            );

            $context = [
                'platform' => $validated['platform'] ?? 'instagram',
                'industry' => $validated['industry'] ?? 'general',
                'target_audience' => $validated['target_audience'] ?? 'general',
                'user_id' => auth()->id(),
            ];

            // Get prediction results
            $results = $predictorService->predictPerformance($validated['caption'], $context);

            return response()->json([
                'success' => true,
                'prediction' => $results['prediction'],
                'quality_score' => $results['quality_score'],
                'improvements' => $results['improvements'],
                'ab_variant' => $results['ab_variant'],
                'best_posting_time' => $results['best_posting_time'],
                'analysis_details' => $results['analysis'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Performance Prediction Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memprediksi performa caption: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🧪 Generate A/B Testing Variants
     */
    public function generateABVariants(Request $request)
    {
        try {
            $validated = $request->validate([
                'caption' => 'required|string|min:10',
                'platform' => 'nullable|string|in:instagram,facebook,tiktok,youtube,linkedin',
                'industry' => 'nullable|string',
                'variant_count' => 'nullable|integer|in:2,3,4,5',
                'focus_area' => 'nullable|string',
            ]);

            $context = [
                'platform' => $validated['platform'] ?? 'instagram',
                'industry' => $validated['industry'] ?? 'general',
                'user_id' => auth()->id(),
            ];

            $variantCount = $validated['variant_count'] ?? 2;
            $focusArea = $validated['focus_area'] ?? 'overall';

            // Initialize services safely
            $geminiService = $this->geminiService ?? app(\App\Services\GeminiService::class);

            // Generate multiple variants
            $variants = [];
            for ($i = 1; $i <= $variantCount; $i++) {
                $prompt = $this->buildABVariantPrompt($validated['caption'], $context, $focusArea, $i);
                
                $variant = $geminiService->generateText($prompt, 800, 0.8);
                
                $variants[] = [
                    'variant_id' => $i,
                    'caption' => trim($variant),
                    'focus' => $focusArea,
                    'test_hypothesis' => $this->generateTestHypothesis($focusArea, $i),
                ];
            }

            return response()->json([
                'success' => true,
                'original_caption' => $validated['caption'],
                'variants' => $variants,
                'test_setup' => [
                    'recommended_duration' => '5-7 hari',
                    'minimum_sample_size' => '1000 impressions per variant',
                    'success_metrics' => ['engagement_rate', 'click_through_rate', 'conversion_rate'],
                    'statistical_significance' => '95% confidence level'
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('A/B Variants Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate A/B variants: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build A/B variant prompt
     */
    private function buildABVariantPrompt(string $originalCaption, array $context, string $focusArea, int $variantNumber): string
    {
        $prompt = "Kamu adalah A/B testing expert untuk social media marketing.\n\n";
        $prompt .= "ORIGINAL CAPTION:\n{$originalCaption}\n\n";
        $prompt .= "PLATFORM: " . $context['platform'] . "\n";
        $prompt .= "FOCUS AREA: {$focusArea}\n";
        $prompt .= "VARIANT NUMBER: {$variantNumber}\n\n";

        switch ($focusArea) {
            case 'hook':
                $prompt .= "Fokus: Buat variant dengan HOOK yang berbeda di pembukaan.\n";
                $prompt .= "Ganti kalimat pembuka dengan style yang berbeda tapi tetap menarik.\n";
                break;
            case 'cta':
                $prompt .= "Fokus: Buat variant dengan CALL-TO-ACTION yang berbeda.\n";
                $prompt .= "Ganti atau perkuat ajakan bertindak di akhir caption.\n";
                break;
            case 'length':
                $prompt .= "Fokus: Buat variant dengan PANJANG yang berbeda.\n";
                if ($variantNumber % 2 === 0) {
                    $prompt .= "Buat versi yang lebih SINGKAT dan to-the-point.\n";
                } else {
                    $prompt .= "Buat versi yang lebih DETAIL dengan penjelasan tambahan.\n";
                }
                break;
            case 'emoji':
                $prompt .= "Fokus: Buat variant dengan penggunaan EMOJI yang berbeda.\n";
                $prompt .= "Ubah jenis, jumlah, atau posisi emoji untuk testing.\n";
                break;
            case 'hashtag':
                $prompt .= "Fokus: Buat variant dengan HASHTAG strategy yang berbeda.\n";
                $prompt .= "Gunakan hashtag yang berbeda atau jumlah yang berbeda.\n";
                break;
            default:
                $prompt .= "Fokus: Buat variant OVERALL yang berbeda dengan tetap mempertahankan pesan inti.\n";
        }

        $prompt .= "\nPedoman:\n";
        $prompt .= "1. Tetap pertahankan pesan dan tujuan utama\n";
        $prompt .= "2. Gunakan bahasa yang natural dan engaging\n";
        $prompt .= "3. Sesuaikan dengan platform " . $context['platform'] . "\n";
        $prompt .= "4. Buat perbedaan yang cukup signifikan untuk testing\n\n";
        $prompt .= "Output: Langsung caption variant tanpa penjelasan tambahan.";

        return $prompt;
    }

    /**
     * Generate test hypothesis for A/B testing
     */
    private function generateTestHypothesis(string $focusArea, int $variantNumber): string
    {
        $hypotheses = [
            'hook' => [
                1 => 'Hook dengan pertanyaan akan meningkatkan engagement rate',
                2 => 'Hook dengan statement bold akan meningkatkan click-through rate',
                3 => 'Hook dengan storytelling akan meningkatkan time spent',
            ],
            'cta' => [
                1 => 'CTA yang lebih direct akan meningkatkan conversion rate',
                2 => 'CTA dengan urgency akan meningkatkan immediate action',
                3 => 'CTA dengan benefit akan meningkatkan click-through rate',
            ],
            'length' => [
                1 => 'Caption yang lebih singkat akan meningkatkan completion rate',
                2 => 'Caption yang lebih panjang akan meningkatkan engagement depth',
            ],
            'emoji' => [
                1 => 'Lebih banyak emoji akan meningkatkan engagement rate',
                2 => 'Emoji yang lebih relevan akan meningkatkan relatability',
            ],
            'hashtag' => [
                1 => 'Hashtag niche akan meningkatkan qualified traffic',
                2 => 'Hashtag trending akan meningkatkan reach',
            ],
            'overall' => [
                1 => 'Variant ini akan meningkatkan overall performance',
                2 => 'Pendekatan berbeda akan menarik audience segment yang berbeda',
            ]
        ];

        return $hypotheses[$focusArea][$variantNumber] ?? 'Variant ini akan memberikan insight tentang preferensi audience';
    }

    /**
     * 📚 Get all templates for Template Library
     */
    public function getAllTemplates()
    {
        try {
            // Get system templates from TemplatePrompts service
            $templateService = new \App\Services\TemplatePrompts();
            $systemTemplates = $templateService->getAllTemplatesForAPI();
            
            // Get community templates (public & approved)
            $communityTemplates = \App\Models\UserTemplate::with('user')
                ->public()
                ->approved()
                ->get()
                ->map(function($template) {
                    return [
                        'id' => 'user_' . $template->id,
                        'key' => 'user_template_' . $template->id,
                        'title' => $template->title,
                        'description' => $template->description,
                        'category' => $template->category,
                        'category_label' => $this->getCategoryLabel($template->category),
                        'platform' => $template->platform,
                        'tone' => $template->tone,
                        'format' => $template->template_content,
                        'usage_count' => $template->usage_count,
                        'is_favorite' => false,
                        'is_community' => true,
                        'author' => $template->user->name,
                        'rating_average' => $template->rating_average,
                        'total_ratings' => $template->total_ratings,
                        'is_premium' => $template->is_premium,
                        'price' => $template->price,
                    ];
                })
                ->toArray();
            
            // Merge system and community templates
            $allTemplates = array_merge($systemTemplates, $communityTemplates);
            
            return response()->json([
                'success' => true,
                'templates' => $allTemplates,
                'total_count' => count($allTemplates),
                'system_count' => count($systemTemplates),
                'community_count' => count($communityTemplates)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Get Templates Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat templates: ' . $e->getMessage(),
                'templates' => []
            ], 500);
        }
    }
    
    private function getCategoryLabel($category)
    {
        $labels = [
            'viral_clickbait' => '🔥 Viral & Clickbait',
            'trend_fresh_ideas' => '✨ Trend & Fresh Ideas',
            'event_promo' => '🎉 Event & Promo',
            'hr_recruitment' => '💼 HR & Recruitment',
            'branding_tagline' => '🎯 Branding & Tagline',
            'education' => '🎓 Education',
            'monetization' => '💰 Monetization',
            'video_monetization' => '📹 Video Content',
            'freelance' => '💻 Freelance',
            'digital_products' => '📱 Digital Products',
        ];
        
        return $labels[$category] ?? ucfirst(str_replace('_', ' ', $category));
    }

    /**
     * 🎯 Generate Multi-Platform Optimized Content
     */
    public function generateMultiPlatform(Request $request)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string|min:10',
                'business_type' => 'nullable|string',
                'target_audience' => 'nullable|string',
                'goal' => 'nullable|string',
                'platforms' => 'required|array|min:2',
                'platforms.*' => 'string|in:instagram,tiktok,facebook,twitter,whatsapp,marketplace'
            ]);

            $results = [];
            
            foreach ($validated['platforms'] as $platform) {
                $platformResult = $this->optimizeForPlatform(
                    $validated['content'],
                    $platform,
                    $validated['business_type'] ?? 'general',
                    $validated['target_audience'] ?? 'general',
                    $validated['goal'] ?? 'engagement'
                );
                
                $results[$platform] = $platformResult;
            }

            return response()->json([
                'success' => true,
                'results' => $results,
                'total_platforms' => count($results)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Multi-Platform Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate multi-platform content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimize content for specific platform
     */
    private function optimizeForPlatform($content, $platform, $businessType, $targetAudience, $goal)
    {
        $platformSpecs = $this->getPlatformSpecifications($platform);
        
        // Build optimization prompt
        $prompt = $this->buildMultiPlatformPrompt($content, $platform, $platformSpecs, $businessType, $targetAudience, $goal);
        
        // Generate optimized content using AI
        $geminiService = $this->geminiService ?? app(\App\Services\GeminiService::class);
        $optimizedContent = $geminiService->generateText($prompt, 1000, 0.7);
        
        // Extract content and metadata
        $result = $this->parseMultiPlatformResult($optimizedContent, $platform, $platformSpecs);
        
        return $result;
    }

    /**
     * Get platform specifications
     */
    private function getPlatformSpecifications($platform)
    {
        $specs = [
            'instagram' => [
                'max_chars' => 2200,
                'hashtag_limit' => 30,
                'tone' => 'visual-focused, engaging',
                'format' => 'caption with hashtags',
                'features' => ['hashtags', 'emojis', 'line_breaks']
            ],
            'tiktok' => [
                'max_chars' => 150,
                'hashtag_limit' => 10,
                'tone' => 'casual, trendy, short',
                'format' => 'short and catchy',
                'features' => ['hashtags', 'trending_sounds', 'challenges']
            ],
            'facebook' => [
                'max_chars' => 8000,
                'hashtag_limit' => 5,
                'tone' => 'storytelling, community-focused',
                'format' => 'longer narrative',
                'features' => ['storytelling', 'questions', 'community_engagement']
            ],
            'twitter' => [
                'max_chars' => 280,
                'hashtag_limit' => 3,
                'tone' => 'concise, news-style',
                'format' => 'tweet or thread',
                'features' => ['hashtags', 'mentions', 'thread_potential']
            ],
            'whatsapp' => [
                'max_chars' => 100,
                'hashtag_limit' => 0,
                'tone' => 'personal, direct',
                'format' => 'status message',
                'features' => ['emojis', 'personal_tone', 'call_to_action']
            ],
            'marketplace' => [
                'max_chars' => 1000,
                'hashtag_limit' => 10,
                'tone' => 'SEO-optimized, conversion-focused',
                'format' => 'product description',
                'features' => ['keywords', 'benefits', 'specifications', 'cta']
            ]
        ];

        return $specs[$platform] ?? $specs['instagram'];
    }

    /**
     * Build optimization prompt for specific platform
     */
    private function buildMultiPlatformPrompt($content, $platform, $specs, $businessType, $targetAudience, $goal)
    {
        $prompt = "Kamu adalah expert social media copywriter yang spesialis optimasi multi-platform.\n\n";
        
        $prompt .= "TUGAS: Optimize konten berikut untuk platform {$platform}\n\n";
        $prompt .= "KONTEN ASLI:\n{$content}\n\n";
        
        $prompt .= "PLATFORM SPECIFICATIONS:\n";
        $prompt .= "- Platform: " . ucfirst($platform) . "\n";
        $prompt .= "- Max Characters: {$specs['max_chars']}\n";
        $prompt .= "- Tone: {$specs['tone']}\n";
        $prompt .= "- Format: {$specs['format']}\n";
        
        if ($specs['hashtag_limit'] > 0) {
            $prompt .= "- Hashtag Limit: {$specs['hashtag_limit']}\n";
        }
        
        $prompt .= "\nCONTEXT:\n";
        $prompt .= "- Business Type: {$businessType}\n";
        $prompt .= "- Target Audience: {$targetAudience}\n";
        $prompt .= "- Goal: {$goal}\n\n";
        
        $prompt .= "OPTIMIZATION REQUIREMENTS:\n";
        
        switch ($platform) {
            case 'instagram':
                $prompt .= "- Buat caption engaging dengan line breaks\n";
                $prompt .= "- Tambahkan 15-30 hashtag yang relevan\n";
                $prompt .= "- Gunakan emoji yang sesuai\n";
                $prompt .= "- Include call-to-action\n";
                $prompt .= "- Format: Caption + hashtags terpisah\n";
                break;
                
            case 'tiktok':
                $prompt .= "- Buat caption super singkat dan catchy\n";
                $prompt .= "- Fokus pada hook di awal\n";
                $prompt .= "- Gunakan trending hashtags\n";
                $prompt .= "- Sebutkan trending sounds jika relevan\n";
                $prompt .= "- Max 150 karakter total\n";
                break;
                
            case 'facebook':
                $prompt .= "- Buat storytelling yang engaging\n";
                $prompt .= "- Gunakan format yang mudah dibaca\n";
                $prompt .= "- Tambahkan pertanyaan untuk engagement\n";
                $prompt .= "- Minimal hashtag (max 5)\n";
                $prompt .= "- Fokus pada community building\n";
                break;
                
            case 'twitter':
                $prompt .= "- Buat tweet yang concise dan impactful\n";
                $prompt .= "- Max 280 karakter\n";
                $prompt .= "- Jika perlu, buat thread (tandai dengan 1/n)\n";
                $prompt .= "- Gunakan 1-3 hashtag relevan\n";
                $prompt .= "- News-style writing\n";
                break;
                
            case 'whatsapp':
                $prompt .= "- Buat status yang personal dan direct\n";
                $prompt .= "- Sangat singkat (max 100 karakter)\n";
                $prompt .= "- Tanpa hashtag\n";
                $prompt .= "- Gunakan emoji yang tepat\n";
                $prompt .= "- Tone seperti chat personal\n";
                break;
                
            case 'marketplace':
                $prompt .= "- Buat deskripsi produk yang SEO-friendly\n";
                $prompt .= "- Fokus pada benefit dan spesifikasi\n";
                $prompt .= "- Include keywords untuk search\n";
                $prompt .= "- Strong call-to-action\n";
                $prompt .= "- Format: Title + Description + Keywords\n";
                break;
        }
        
        $prompt .= "\nOUTPUT FORMAT:\n";
        $prompt .= "CONTENT:\n[optimized content here]\n\n";
        
        if ($specs['hashtag_limit'] > 0) {
            $prompt .= "HASHTAGS:\n[hashtag1] [hashtag2] [hashtag3]\n\n";
        }
        
        $prompt .= "OPTIMIZATION_NOTES:\n";
        $prompt .= "- [note 1]\n";
        $prompt .= "- [note 2]\n";
        $prompt .= "- [note 3]\n\n";
        
        $prompt .= "Pastikan hasil optimasi sesuai dengan character limit dan best practices platform {$platform}.";
        
        return $prompt;
    }

    /**
     * Parse multi-platform AI result
     */
    private function parseMultiPlatformResult($aiResult, $platform, $specs)
    {
        // Extract content
        $content = '';
        if (preg_match('/CONTENT:\s*(.*?)(?=HASHTAGS:|OPTIMIZATION_NOTES:|$)/s', $aiResult, $matches)) {
            $content = trim($matches[1]);
        } else {
            // Fallback: use first part of result
            $lines = explode("\n", $aiResult);
            $content = trim($lines[0] ?? $aiResult);
        }
        
        // Extract hashtags
        $hashtags = [];
        if (preg_match('/HASHTAGS:\s*(.*?)(?=OPTIMIZATION_NOTES:|$)/s', $aiResult, $matches)) {
            $hashtagText = trim($matches[1]);
            $hashtags = array_filter(array_map('trim', preg_split('/[\s\n]+/', $hashtagText)));
        }
        
        // Extract optimization notes
        $optimizationNotes = [];
        if (preg_match('/OPTIMIZATION_NOTES:\s*(.*?)$/s', $aiResult, $matches)) {
            $notesText = trim($matches[1]);
            $lines = explode("\n", $notesText);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line && $line !== '-') {
                    $optimizationNotes[] = ltrim($line, '- ');
                }
            }
        }
        
        // Ensure character limit compliance
        if (strlen($content) > $specs['max_chars']) {
            $content = substr($content, 0, $specs['max_chars'] - 3) . '...';
        }
        
        // Limit hashtags
        if (count($hashtags) > $specs['hashtag_limit']) {
            $hashtags = array_slice($hashtags, 0, $specs['hashtag_limit']);
        }
        
        return [
            'content' => $content,
            'char_count' => strlen($content),
            'hashtags' => $hashtags,
            'optimization_notes' => $optimizationNotes,
            'platform_specs' => $specs
        ];
    }

    /**
     * ♻️ Content Repurposing - Transform 1 content into multiple formats
     */
    public function repurposeContent(Request $request)
    {
        try {
            $validated = $request->validate([
                'original_content' => 'required|string|min:10',
                'original_type' => 'nullable|string',
                'industry' => 'nullable|string',
                'selected_formats' => 'required|array|min:1',
                'selected_formats.*' => 'string',
                'include_hashtags' => 'boolean',
                'include_cta' => 'boolean',
                'optimize_length' => 'boolean',
                'generate_variations' => 'boolean'
            ]);

            $results = [];
            
            foreach ($validated['selected_formats'] as $format) {
                $repurposedContent = $this->repurposeToFormat(
                    $validated['original_content'],
                    $format,
                    $validated['original_type'] ?? 'auto-detect',
                    $validated['industry'] ?? 'general',
                    $validated
                );
                
                $results[] = $repurposedContent;
            }

            return response()->json([
                'success' => true,
                'results' => $results,
                'total_formats' => count($results)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Content Repurposing Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal repurpose content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Repurpose content to specific format
     */
    private function repurposeToFormat($originalContent, $format, $originalType, $industry, $options)
    {
        $formatSpecs = $this->getRepurposeFormatSpecs($format);
        
        // Build repurposing prompt
        $prompt = $this->buildRepurposePrompt($originalContent, $format, $formatSpecs, $originalType, $industry, $options);
        
        try {
            $aiResult = $this->aiService->generateText($prompt);
            $result = $this->parseRepurposeResult($aiResult, $format, $formatSpecs, $options);
            
            return $result;
        } catch (\Exception $e) {
            \Log::warning("Repurpose failed for format {$format}, using fallback: " . $e->getMessage());
            return $this->getFallbackRepurpose($originalContent, $format, $formatSpecs);
        }
    }

    /**
     * Get format specifications for repurposing
     */
    private function getRepurposeFormatSpecs($format)
    {
        $specs = [
            'instagram_story' => [
                'max_chars' => 150,
                'style' => 'Visual, engaging, short',
                'structure' => 'Hook + Key Point + CTA',
                'tone' => 'Casual, visual-first'
            ],
            'tiktok_script' => [
                'max_chars' => 200,
                'style' => 'Hook-heavy, trendy',
                'structure' => '3-second hook + content + trending element',
                'tone' => 'Energetic, Gen Z'
            ],
            'blog_outline' => [
                'max_chars' => 500,
                'style' => 'Structured, SEO-friendly',
                'structure' => 'Title + H2 sections + conclusion',
                'tone' => 'Educational, authoritative'
            ],
            'email_copy' => [
                'max_chars' => 300,
                'style' => 'Personal, direct',
                'structure' => 'Subject + greeting + value + CTA',
                'tone' => 'Conversational, persuasive'
            ],
            'product_description' => [
                'max_chars' => 250,
                'style' => 'Benefit-focused, conversion-oriented',
                'structure' => 'Problem + solution + benefits + urgency',
                'tone' => 'Persuasive, clear'
            ],
            'linkedin_post' => [
                'max_chars' => 400,
                'style' => 'Professional, thought-leadership',
                'structure' => 'Insight + story/example + takeaway',
                'tone' => 'Professional, insightful'
            ],
            'facebook_post' => [
                'max_chars' => 300,
                'style' => 'Community-focused, engaging',
                'structure' => 'Story + question/discussion starter',
                'tone' => 'Friendly, community-building'
            ],
            'youtube_description' => [
                'max_chars' => 400,
                'style' => 'SEO-optimized, detailed',
                'structure' => 'Summary + timestamps + links + keywords',
                'tone' => 'Descriptive, searchable'
            ],
            'twitter_thread' => [
                'max_chars' => 280,
                'style' => 'Bite-sized, thread-worthy',
                'structure' => 'Hook tweet + supporting tweets + conclusion',
                'tone' => 'Concise, engaging'
            ],
            'whatsapp_broadcast' => [
                'max_chars' => 100,
                'style' => 'Personal, direct',
                'structure' => 'Greeting + key message + action',
                'tone' => 'Personal, urgent'
            ],
            'carousel_slides' => [
                'max_chars' => 150,
                'style' => 'Slide-by-slide breakdown',
                'structure' => 'Title slide + content slides + CTA slide',
                'tone' => 'Educational, visual'
            ],
            'podcast_script' => [
                'max_chars' => 500,
                'style' => 'Conversational, audio-friendly',
                'structure' => 'Intro + main points + conclusion',
                'tone' => 'Conversational, storytelling'
            ]
        ];

        return $specs[$format] ?? $specs['instagram_story'];
    }

    /**
     * Build repurposing prompt
     */
    private function buildRepurposePrompt($originalContent, $format, $specs, $originalType, $industry, $options)
    {
        $prompt = "CONTENT REPURPOSING TASK\n\n";
        $prompt .= "ORIGINAL CONTENT:\n{$originalContent}\n\n";
        $prompt .= "ORIGINAL TYPE: {$originalType}\n";
        $prompt .= "INDUSTRY: {$industry}\n\n";
        
        $prompt .= "REPURPOSE TO: " . strtoupper(str_replace('_', ' ', $format)) . "\n";
        $prompt .= "TARGET SPECS:\n";
        $prompt .= "- Max Characters: {$specs['max_chars']}\n";
        $prompt .= "- Style: {$specs['style']}\n";
        $prompt .= "- Structure: {$specs['structure']}\n";
        $prompt .= "- Tone: {$specs['tone']}\n\n";
        
        $prompt .= "REQUIREMENTS:\n";
        $prompt .= "- Maintain core message and value proposition\n";
        $prompt .= "- Adapt to target format's best practices\n";
        $prompt .= "- Optimize for platform-specific engagement\n";
        
        if ($options['optimize_length']) {
            $prompt .= "- Strictly follow character limits\n";
        }
        
        if ($options['include_hashtags']) {
            $prompt .= "- Include relevant hashtags (3-5 max)\n";
        }
        
        if ($options['include_cta']) {
            $prompt .= "- Include clear call-to-action\n";
        }
        
        $prompt .= "\nOUTPUT FORMAT:\n";
        $prompt .= "CONTENT:\n[repurposed content here]\n\n";
        
        if ($options['generate_variations']) {
            $prompt .= "VARIATIONS:\n";
            $prompt .= "1. [variation 1]\n";
            $prompt .= "2. [variation 2]\n";
            $prompt .= "3. [variation 3]\n\n";
        }
        
        $prompt .= "Berikan hasil yang natural, engaging, dan sesuai dengan karakteristik format target.";
        
        return $prompt;
    }

    /**
     * Parse repurpose result
     */
    private function parseRepurposeResult($aiResult, $format, $specs, $options)
    {
        // Extract main content
        $content = $aiResult;
        if (preg_match('/CONTENT:\s*(.*?)(?=\n\n|VARIATIONS:|$)/s', $aiResult, $matches)) {
            $content = trim($matches[1]);
        }
        
        // Extract variations if requested
        $variations = [];
        if ($options['generate_variations'] && preg_match('/VARIATIONS:\s*(.*?)$/s', $aiResult, $matches)) {
            $variationsText = trim($matches[1]);
            $lines = explode("\n", $variationsText);
            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/^\d+\.\s*(.+)$/', $line, $varMatches)) {
                    $variations[] = trim($varMatches[1]);
                }
            }
        }
        
        // Ensure character limit compliance
        if ($options['optimize_length'] && strlen($content) > $specs['max_chars']) {
            $content = substr($content, 0, $specs['max_chars'] - 3) . '...';
        }
        
        return [
            'format' => $format,
            'content' => $content,
            'char_count' => strlen($content),
            'variations' => $variations,
            'format_specs' => $specs
        ];
    }

    /**
     * Fallback repurpose when AI fails
     */
    private function getFallbackRepurpose($originalContent, $format, $specs)
    {
        $templates = [
            'instagram_story' => "📱 {content}\n\n#tips #inspiration",
            'tiktok_script' => "🎵 {content}\n\nWhat do you think? 🤔",
            'blog_outline' => "# {content}\n\n## Introduction\n## Main Points\n## Conclusion",
            'email_copy' => "Hi there! 👋\n\n{content}\n\nBest regards!",
            'product_description' => "✨ {content}\n\n🛒 Get yours today!",
            'linkedin_post' => "💼 {content}\n\nWhat's your experience with this?",
            'facebook_post' => "👥 {content}\n\nShare your thoughts below! 👇",
            'youtube_description' => "📺 {content}\n\n🔔 Subscribe for more!",
            'twitter_thread' => "🧵 {content}\n\nThread 👇",
            'whatsapp_broadcast' => "📢 {content}",
            'carousel_slides' => "📊 Slide 1: {content}",
            'podcast_script' => "🎙️ Today we're talking about: {content}"
        ];
        
        $template = $templates[$format] ?? $templates['instagram_story'];
        $content = str_replace('{content}', substr($originalContent, 0, 100), $template);
        
        return [
            'format' => $format,
            'content' => $content,
            'char_count' => strlen($content),
            'variations' => [],
            'format_specs' => $specs
        ];
    }

    /**
     * 🔔 Generate content based on trending topics
     */
    public function generateTrendContent(Request $request)
    {
        try {
            $validated = $request->validate([
                'trend' => 'required|array',
                'trend.title' => 'required|string',
                'trend.description' => 'required|string',
                'trend.hashtags' => 'nullable|array',
                'content_types' => 'required|array|min:1',
                'content_types.*' => 'string',
                'business_context' => 'required|string|min:3'
            ]);

            $results = [];
            
            foreach ($validated['content_types'] as $contentType) {
                $trendContent = $this->generateTrendContentByType(
                    $validated['trend'],
                    $contentType,
                    $validated['business_context']
                );
                
                $results[] = $trendContent;
            }

            return response()->json([
                'success' => true,
                'results' => $results,
                'total_formats' => count($results)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Trend Content Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate trend content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate content for specific type based on trend
     */
    private function generateTrendContentByType($trend, $contentType, $businessContext)
    {
        $contentSpecs = $this->getTrendContentSpecs($contentType);
        
        // Build trend-based prompt
        $prompt = $this->buildTrendPrompt($trend, $contentType, $contentSpecs, $businessContext);
        
        try {
            $aiResult = $this->geminiService->generateText($prompt, 400, 0.8);
            $result = $this->parseTrendResult($aiResult, $contentType, $contentSpecs, $trend);
            
            return $result;
        } catch (\Exception $e) {
            \Log::warning("Trend content generation failed for type {$contentType}, using fallback: " . $e->getMessage());
            return $this->getFallbackTrendContent($trend, $contentType, $contentSpecs, $businessContext);
        }
    }

    /**
     * Get content specifications for trend-based content
     */
    private function getTrendContentSpecs($contentType)
    {
        $specs = [
            'caption' => [
                'icon' => '📱',
                'title' => 'Instagram/Facebook Caption',
                'max_chars' => 300,
                'style' => 'Engaging, trend-aware, social',
                'structure' => 'Hook + Trend Connection + Business Angle + CTA',
                'tone' => 'Trendy, relatable, authentic'
            ],
            'story' => [
                'icon' => '📸',
                'title' => 'Instagram Story',
                'max_chars' => 150,
                'style' => 'Visual-first, quick consumption',
                'structure' => 'Slide sequence with trend integration',
                'tone' => 'Casual, immediate, engaging'
            ],
            'tiktok' => [
                'icon' => '🎵',
                'title' => 'TikTok Script',
                'max_chars' => 250,
                'style' => 'Hook-heavy, trend-riding',
                'structure' => '3-sec hook + trend explanation + business twist + CTA',
                'tone' => 'Energetic, Gen Z, viral-ready'
            ],
            'thread' => [
                'icon' => '🧵',
                'title' => 'Twitter Thread',
                'max_chars' => 280,
                'style' => 'Informative, thread-worthy',
                'structure' => 'Hook tweet + trend analysis + business insight + conclusion',
                'tone' => 'Insightful, conversational, shareable'
            ],
            'blog' => [
                'icon' => '📝',
                'title' => 'Blog Post',
                'max_chars' => 500,
                'style' => 'SEO-friendly, comprehensive',
                'structure' => 'Title + intro + trend analysis + business application + conclusion',
                'tone' => 'Educational, authoritative, trend-aware'
            ],
            'email' => [
                'icon' => '📧',
                'title' => 'Email Marketing',
                'max_chars' => 300,
                'style' => 'Personal, direct, trend-leveraging',
                'structure' => 'Subject + trend hook + business connection + CTA',
                'tone' => 'Personal, urgent, trend-conscious'
            ],
            'ads' => [
                'icon' => '💰',
                'title' => 'Facebook/Instagram Ads',
                'max_chars' => 200,
                'style' => 'Conversion-focused, trend-leveraging',
                'structure' => 'Trend hook + problem/solution + benefits + strong CTA',
                'tone' => 'Persuasive, urgent, trend-aware'
            ],
            'whatsapp' => [
                'icon' => '💬',
                'title' => 'WhatsApp Blast',
                'max_chars' => 150,
                'style' => 'Personal, direct, trend-aware',
                'structure' => 'Trend mention + personal connection + business offer + action',
                'tone' => 'Personal, friendly, immediate'
            ]
        ];

        return $specs[$contentType] ?? $specs['caption'];
    }

    /**
     * Build trend-based content prompt
     */
    private function buildTrendPrompt($trend, $contentType, $specs, $businessContext)
    {
        $prompt = "TREND-BASED CONTENT GENERATION\n\n";
        $prompt .= "TRENDING TOPIC:\n";
        $prompt .= "Title: {$trend['title']}\n";
        $prompt .= "Description: {$trend['description']}\n";
        
        if (!empty($trend['hashtags'])) {
            $prompt .= "Trending Hashtags: " . implode(', ', $trend['hashtags']) . "\n";
        }
        
        $prompt .= "\nBUSINESS CONTEXT: {$businessContext}\n\n";
        
        $prompt .= "CONTENT TYPE: " . strtoupper(str_replace('_', ' ', $contentType)) . "\n";
        $prompt .= "TARGET SPECS:\n";
        $prompt .= "- Max Characters: {$specs['max_chars']}\n";
        $prompt .= "- Style: {$specs['style']}\n";
        $prompt .= "- Structure: {$specs['structure']}\n";
        $prompt .= "- Tone: {$specs['tone']}\n\n";
        
        $prompt .= "REQUIREMENTS:\n";
        $prompt .= "- Naturally integrate the trending topic\n";
        $prompt .= "- Connect trend to business context authentically\n";
        $prompt .= "- Maintain trend relevance without forced connection\n";
        $prompt .= "- Use appropriate trending hashtags\n";
        $prompt .= "- Create engaging, shareable content\n";
        $prompt .= "- Include clear call-to-action\n";
        $prompt .= "- Write in Indonesian (Bahasa Indonesia)\n";
        $prompt .= "- Make it suitable for UMKM/small business audience\n\n";
        
        $prompt .= "OUTPUT FORMAT:\n";
        $prompt .= "CONTENT:\n[generated content here]\n\n";
        $prompt .= "HASHTAGS:\n[relevant hashtags including trending ones]\n\n";
        
        $prompt .= "Buat konten yang natural, engaging, dan memanfaatkan momentum trend untuk bisnis {$businessContext}.";
        
        return $prompt;
    }

    /**
     * Parse trend content result
     */
    private function parseTrendResult($aiResult, $contentType, $specs, $trend)
    {
        // Extract main content
        $content = $aiResult;
        if (preg_match('/CONTENT:\s*(.*?)(?=\n\nHASHTAGS:|$)/s', $aiResult, $matches)) {
            $content = trim($matches[1]);
        }
        
        // Extract hashtags
        $hashtags = $trend['hashtags'] ?? [];
        if (preg_match('/HASHTAGS:\s*(.*?)$/s', $aiResult, $matches)) {
            $hashtagsText = trim($matches[1]);
            $extractedHashtags = array_map('trim', explode(' ', $hashtagsText));
            $hashtags = array_merge($hashtags, $extractedHashtags);
            $hashtags = array_unique(array_filter($hashtags));
        }
        
        // Ensure character limit compliance
        if (strlen($content) > $specs['max_chars']) {
            $content = substr($content, 0, $specs['max_chars'] - 3) . '...';
        }
        
        return [
            'type' => $contentType,
            'icon' => $specs['icon'],
            'title' => $specs['title'],
            'content' => $content,
            'hashtags' => array_slice($hashtags, 0, 8), // Limit to 8 hashtags
            'char_count' => strlen($content),
            'trend_title' => $trend['title']
        ];
    }

    /**
     * 📊 Generate optimal content based on analytics data
     */
    public function generateOptimalContent(Request $request)
    {
        try {
            $validated = $request->validate([
                'use_analytics' => 'boolean',
                'content_type' => 'nullable|string',
                'business_context' => 'nullable|string'
            ]);

            $user = auth()->user();
            
            // Get user's analytics data for optimization
            $analyticsData = $this->getUserAnalyticsForOptimization($user->id);
            
            // Generate optimal content based on analytics
            $optimalContent = $this->generateAnalyticsBasedContent($analyticsData, $validated);

            return response()->json([
                'success' => true,
                'content' => $optimalContent,
                'analytics_used' => $analyticsData,
                'message' => 'Optimal content generated based on your performance data'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Optimal Content Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate optimal content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user analytics data for content optimization
     */
    private function getUserAnalyticsForOptimization($userId)
    {
        // Get best performing patterns
        $bestCaption = \App\Models\CaptionAnalytics::where('user_id', $userId)
            ->orderByDesc('engagement_rate')
            ->first();

        $bestPlatform = \App\Models\CaptionAnalytics::where('user_id', $userId)
            ->select('platform', \Illuminate\Support\Facades\DB::raw('AVG(engagement_rate) as avg_engagement'))
            ->groupBy('platform')
            ->orderByDesc('avg_engagement')
            ->first();

        $optimalTime = \App\Models\CaptionAnalytics::where('user_id', $userId)
            ->whereNotNull('posted_at')
            ->select(
                \Illuminate\Support\Facades\DB::raw('HOUR(posted_at) as hour'),
                \Illuminate\Support\Facades\DB::raw('DAYNAME(posted_at) as day_name'),
                \Illuminate\Support\Facades\DB::raw('AVG(engagement_rate) as avg_engagement')
            )
            ->groupBy('hour', 'day_name')
            ->orderByDesc('avg_engagement')
            ->first();

        // Extract success keywords from top performing captions
        $successKeywords = $this->extractSuccessKeywords($userId);

        return [
            'best_engagement_rate' => $bestCaption ? $bestCaption->engagement_rate : 5.0,
            'best_platform' => $bestPlatform ? $bestPlatform->platform : 'Instagram',
            'optimal_hour' => $optimalTime ? $optimalTime->hour : 19,
            'optimal_day' => $optimalTime ? $optimalTime->day_name : 'Tuesday',
            'success_keywords' => $successKeywords,
            'best_caption_sample' => $bestCaption ? \Illuminate\Support\Str::limit($bestCaption->caption_text, 100) : null,
            'has_data' => (bool) $bestCaption
        ];
    }

    /**
     * Extract success keywords from user's top performing captions
     */
    private function extractSuccessKeywords($userId)
    {
        $topCaptions = \App\Models\CaptionAnalytics::where('user_id', $userId)
            ->where('engagement_rate', '>', 3) // Above average
            ->orderByDesc('engagement_rate')
            ->take(5)
            ->pluck('caption_text');

        $keywords = [];
        $commonSuccessWords = [
            'promo', 'diskon', 'sale', 'flash', 'terbatas', 'limited', 'eksklusif', 'exclusive',
            'gratis', 'free', 'bonus', 'cashback', 'hemat', 'murah', 'best', 'terbaik',
            'new', 'baru', 'launch', 'trending', 'viral', 'hot', 'populer'
        ];

        foreach ($topCaptions as $caption) {
            $words = str_word_count(strtolower($caption), 1);
            foreach ($commonSuccessWords as $word) {
                if (in_array($word, $words)) {
                    $keywords[] = $word;
                }
            }
        }

        $keywordCounts = array_count_values($keywords);
        arsort($keywordCounts);

        return array_slice(array_keys($keywordCounts), 0, 5) ?: ['promo', 'terbatas', 'eksklusif'];
    }

    /**
     * Generate content based on analytics insights
     */
    private function generateAnalyticsBasedContent($analyticsData, $options)
    {
        $businessContext = $options['business_context'] ?? 'UMKM';
        
        // Build analytics-optimized prompt
        $prompt = $this->buildAnalyticsOptimizedPrompt($analyticsData, $businessContext);
        
        try {
            $aiResult = $this->geminiService->generateText($prompt, 500, 0.8);
            $captions = $this->parseOptimalContentResult($aiResult, $analyticsData);
            
            return $captions;
        } catch (\Exception $e) {
            \Log::warning("Analytics-based generation failed, using fallback: " . $e->getMessage());
            return $this->getFallbackOptimalContent($analyticsData, $businessContext);
        }
    }

    /**
     * Build prompt optimized based on user analytics
     */
    private function buildAnalyticsOptimizedPrompt($analyticsData, $businessContext)
    {
        $prompt = "ANALYTICS-OPTIMIZED CONTENT GENERATION\n\n";
        
        $prompt .= "USER PERFORMANCE DATA:\n";
        $prompt .= "- Best Platform: {$analyticsData['best_platform']}\n";
        $prompt .= "- Best Engagement Rate: {$analyticsData['best_engagement_rate']}%\n";
        $prompt .= "- Optimal Posting Time: {$analyticsData['optimal_day']} at {$analyticsData['optimal_hour']}:00\n";
        $prompt .= "- Success Keywords: " . implode(', ', $analyticsData['success_keywords']) . "\n";
        
        if ($analyticsData['best_caption_sample']) {
            $prompt .= "- Best Caption Sample: {$analyticsData['best_caption_sample']}\n";
        }
        
        $prompt .= "\nBUSINESS CONTEXT: {$businessContext}\n\n";
        
        $prompt .= "TASK: Generate 3 optimized captions that leverage the user's proven success patterns.\n\n";
        
        $prompt .= "OPTIMIZATION REQUIREMENTS:\n";
        $prompt .= "- Use proven success keywords from user's data\n";
        $prompt .= "- Match the style/tone of their best performing content\n";
        $prompt .= "- Optimize for {$analyticsData['best_platform']} platform\n";
        $prompt .= "- Include timing recommendation for {$analyticsData['optimal_day']} {$analyticsData['optimal_hour']}:00\n";
        $prompt .= "- Target engagement rate above {$analyticsData['best_engagement_rate']}%\n";
        $prompt .= "- Include relevant hashtags for {$analyticsData['best_platform']}\n";
        $prompt .= "- Write in Indonesian (Bahasa Indonesia)\n";
        $prompt .= "- Focus on UMKM/small business audience\n\n";
        
        $prompt .= "OUTPUT FORMAT:\n";
        $prompt .= "CAPTION 1:\n";
        $prompt .= "Platform: {$analyticsData['best_platform']}\n";
        $prompt .= "Content: [caption here]\n";
        $prompt .= "Expected Engagement: [percentage]%\n";
        $prompt .= "Hashtags: [hashtags]\n\n";
        
        $prompt .= "CAPTION 2:\n";
        $prompt .= "Platform: {$analyticsData['best_platform']}\n";
        $prompt .= "Content: [caption here]\n";
        $prompt .= "Expected Engagement: [percentage]%\n";
        $prompt .= "Hashtags: [hashtags]\n\n";
        
        $prompt .= "CAPTION 3:\n";
        $prompt .= "Platform: {$analyticsData['best_platform']}\n";
        $prompt .= "Content: [caption here]\n";
        $prompt .= "Expected Engagement: [percentage]%\n";
        $prompt .= "Hashtags: [hashtags]\n\n";
        
        $prompt .= "Generate captions yang memanfaatkan pola sukses user untuk memaksimalkan engagement.";
        
        return $prompt;
    }

    /**
     * Parse optimal content result
     */
    private function parseOptimalContentResult($aiResult, $analyticsData)
    {
        $captions = [];
        
        // Extract captions using regex
        preg_match_all('/CAPTION (\d+):\s*Platform: ([^\n]+)\s*Content: (.*?)Expected Engagement: ([^\n]+)\s*Hashtags: ([^\n]+)/s', $aiResult, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $captions[] = [
                'platform' => trim($match[2]),
                'text' => trim($match[3]),
                'expected_engagement' => trim($match[4]),
                'hashtags' => trim($match[5])
            ];
        }
        
        // Fallback parsing if regex fails
        if (empty($captions)) {
            $lines = explode("\n", $aiResult);
            $currentCaption = null;
            
            foreach ($lines as $line) {
                $line = trim($line);
                
                if (preg_match('/CAPTION (\d+):/', $line)) {
                    if ($currentCaption) {
                        $captions[] = $currentCaption;
                    }
                    $currentCaption = [
                        'platform' => $analyticsData['best_platform'],
                        'text' => '',
                        'expected_engagement' => $analyticsData['best_engagement_rate'] + 1 . '%',
                        'hashtags' => ''
                    ];
                } elseif ($currentCaption && strpos($line, 'Content:') === 0) {
                    $currentCaption['text'] = trim(substr($line, 8));
                } elseif ($currentCaption && strpos($line, 'Expected Engagement:') === 0) {
                    $currentCaption['expected_engagement'] = trim(substr($line, 20));
                } elseif ($currentCaption && strpos($line, 'Hashtags:') === 0) {
                    $currentCaption['hashtags'] = trim(substr($line, 9));
                }
            }
            
            if ($currentCaption) {
                $captions[] = $currentCaption;
            }
        }
        
        return $captions ?: $this->getFallbackOptimalContent($analyticsData, 'UMKM')['captions'];
    }

    /**
     * Fallback optimal content when AI fails
     */
    private function getFallbackOptimalContent($analyticsData, $businessContext)
    {
        $successKeywords = implode(' ', array_slice($analyticsData['success_keywords'], 0, 3));
        $platform = $analyticsData['best_platform'];
        $expectedEngagement = $analyticsData['best_engagement_rate'] + 1;
        
        return [
            'captions' => [
                [
                    'platform' => $platform,
                    'text' => "🔥 {$successKeywords} spesial untuk Anda! Sebagai {$businessContext} terpercaya, kami memberikan penawaran terbaik. Jangan sampai terlewat ya! 💫",
                    'expected_engagement' => $expectedEngagement . '%',
                    'hashtags' => '#' . str_replace(' ', '', $businessContext) . ' #' . $successKeywords . ' #TerpercayaIndonesia'
                ],
                [
                    'platform' => $platform,
                    'text' => "✨ Kabar gembira! {$successKeywords} hadir lagi nih. Khusus untuk followers setia {$businessContext} kami. Buruan sebelum kehabisan! 🚀",
                    'expected_engagement' => ($expectedEngagement + 0.5) . '%',
                    'hashtags' => '#' . str_replace(' ', '', $businessContext) . ' #' . $successKeywords . ' #LimitedOffer'
                ],
                [
                    'platform' => $platform,
                    'text' => "🎯 Sudah tau belum? {$successKeywords} terbaik ada di sini! {$businessContext} pilihan tepat untuk kebutuhan Anda. Yuk order sekarang! 💪",
                    'expected_engagement' => ($expectedEngagement + 1) . '%',
                    'hashtags' => '#' . str_replace(' ', '', $businessContext) . ' #' . $successKeywords . ' #OrderSekarang'
                ]
            ]
        ];
    }

    /**
     * Fallback trend content when AI fails
     */
    private function getFallbackTrendContent($trend, $contentType, $specs, $businessContext)
    {
        $templates = [
            'caption' => "🔥 Lagi viral nih {$trend['title']}!\n\n{$trend['description']}\n\nSebagai {$businessContext}, kita juga ikutan dong! Gimana menurut kalian?\n\n💬 Comment pendapat kalian ya!",
            
            'story' => "📸 STORY ALERT!\n\nTrend: {$trend['title']}\n\nVersi {$businessContext} gimana ya?\n\nSwipe up untuk tau lebih lanjut! 👆",
            
            'tiktok' => "🎵 Eh tau gak trend {$trend['title']} yang lagi viral?\n\n{$trend['description']}\n\nNah sebagai {$businessContext}, kita juga bisa loh ikutan dengan cara unik!\n\nComment 'TREND' kalau mau tau caranya! 👇",
            
            'thread' => "🧵 THREAD: {$trend['title']}\n\n1/3 Lagi viral banget nih!\n\n2/3 {$trend['description']}\n\n3/3 Sebagai {$businessContext}, kita lihat peluang buat ikutan dengan cara yang kreatif!",
            
            'blog' => "# Memanfaatkan Trend {$trend['title']} untuk {$businessContext}\n\n## Apa itu {$trend['title']}?\n{$trend['description']}\n\n## Cara Memanfaatkan untuk Bisnis\n- Buat konten yang relevan\n- Gunakan hashtag yang tepat\n- Timing yang pas\n\n## Kesimpulan\nTrend ini bisa jadi peluang besar untuk {$businessContext}!",
            
            'email' => "Subject: 🔥 Jangan Ketinggalan Trend {$trend['title']}!\n\nHalo!\n\nPasti udah tau dong trend {$trend['title']} yang lagi viral?\n\n{$trend['description']}\n\nSebagai {$businessContext}, kita gak mau ketinggalan!\n\n[CTA: Lihat Penawaran Spesial]",
            
            'ads' => "🔥 TRENDING: {$trend['title']}\n\n{$trend['description']}\n\nSebagai {$businessContext} terdepan, kita ikutan trend ini!\n\n✅ Kualitas terjamin\n✅ Harga bersaing\n✅ Pelayanan terbaik\n\n[PESAN SEKARANG]",
            
            'whatsapp' => "🔥 Hai! Tau gak trend {$trend['title']} yang lagi viral?\n\n{$trend['description']}\n\nSebagai {$businessContext}, kita juga ikutan nih!\n\nYuk chat balik kalau tertarik! 😊"
        ];
        
        $template = $templates[$contentType] ?? $templates['caption'];
        
        return [
            'type' => $contentType,
            'icon' => $specs['icon'],
            'title' => $specs['title'],
            'content' => $template,
            'hashtags' => $trend['hashtags'] ?? ['#TrendAlert', '#' . str_replace(' ', '', $businessContext)],
            'char_count' => strlen($template),
            'trend_title' => $trend['title']
        ];
    }

    /**
     * 🎯 Google Ads Campaign Generator
     */
    public function generateGoogleAds(Request $request)
    {
        set_time_limit(300);

        $user = auth()->user();
        $sub  = $user->currentSubscription();

        if (!$sub || !$sub->isValid()) {
            return response()->json([
                'success'     => false,
                'quota_error' => true,
                'message'     => '⚡ Kamu belum memiliki langganan aktif.',
            ], 403);
        }

        if ($sub->remaining_quota <= 0) {
            return response()->json([
                'success'     => false,
                'quota_error' => true,
                'message'     => '🚫 Kuota AI kamu sudah habis bulan ini.',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'business_name'    => 'required|string|max:100',
                'product_service'  => 'required|string|max:500',
                'target_audience'  => 'required|string|max:300',
                'location'         => 'required|string|max:100',
                'daily_budget'     => 'required|numeric|min:10000',
                'campaign_goal'    => 'required|in:sales,leads,traffic,brand_awareness,app_installs',
                'campaign_type'    => 'required|in:search,display,shopping,video,performance_max',
                'landing_page_url' => 'nullable|url|max:500',
                'keywords_hint'    => 'nullable|string|max:500',
                'usp'              => 'nullable|string|max:300',
                'language'         => 'nullable|in:id,en',
            ]);

            $result = $this->geminiService->generateGoogleAdsCampaign($validated);

            $sub->consumeQuota(1);

            return response()->json([
                'success'         => true,
                'campaign'        => $result,
                'quota_remaining' => $sub->fresh()->remaining_quota,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->implode('; '),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Google Ads Generator Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 💬 AI Product Explainer for WhatsApp
     */
    public function generateProductExplainer(Request $request)
    {
        set_time_limit(120);

        $user = auth()->user();
        $sub  = $user->currentSubscription();

        if (!$sub || !$sub->isValid()) {
            return response()->json([
                'success'     => false,
                'quota_error' => true,
                'message'     => '⚡ Kamu belum memiliki langganan aktif.',
            ], 403);
        }

        if ($sub->remaining_quota <= 0) {
            return response()->json([
                'success'     => false,
                'quota_error' => true,
                'message'     => '🚫 Kuota AI kamu sudah habis bulan ini.',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'product_name'  => 'required|string|max:100',
                'product_desc'  => 'required|string|max:500',
                'price'         => 'nullable|string|max:100',
                'features'      => 'nullable|string|max:500',
                'target_buyer'  => 'nullable|string|max:200',
                'seller_name'   => 'nullable|string|max:100',
                'wa_number'     => 'nullable|string|max:20',
            ]);

            $result = $this->geminiService->generateProductExplainer($validated);

            $sub->consumeQuota(1);

            return response()->json([
                'success'         => true,
                'data'            => $result,
                'quota_remaining' => $sub->fresh()->remaining_quota,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->implode('; '),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Product Explainer Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔗 Magic Promo Link Generator
     */
    public function generateMagicPromoLink(Request $request)
    {
        set_time_limit(120);

        $user = auth()->user();
        $sub  = $user->currentSubscription();

        if (!$sub || !$sub->isValid()) {
            return response()->json([
                'success'     => false,
                'quota_error' => true,
                'message'     => '⚡ Kamu belum memiliki langganan aktif.',
            ], 403);
        }

        if ($sub->remaining_quota <= 0) {
            return response()->json([
                'success'     => false,
                'quota_error' => true,
                'message'     => '🚫 Kuota AI kamu sudah habis bulan ini.',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'product_name'    => 'required|string|max:100',
                'product_desc'    => 'required|string|max:500',
                'price'           => 'nullable|string|max:100',
                'target_audience' => 'nullable|string|max:200',
                'promo_detail'    => 'nullable|string|max:300',
                'wa_number'       => 'nullable|string|max:20',
                'language'        => 'nullable|in:id,en',
            ]);

            $result = $this->geminiService->generateMagicPromoLink($validated);

            $sub->consumeQuota(1);

            return response()->json([
                'success'         => true,
                'data'            => $result,
                'quota_remaining' => $sub->fresh()->remaining_quota,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->implode('; '),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Magic Promo Link Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ── Shared quota guard ──────────────────────────────────────────
    private function _checkQuota(): array|null
    {
        $user = auth()->user();
        $sub  = $user->currentSubscription();
        if (!$sub || !$sub->isValid()) {
            return ['success' => false, 'quota_error' => true, 'message' => '⚡ Kamu belum memiliki langganan aktif.'];
        }
        if ($sub->remaining_quota <= 0) {
            return ['success' => false, 'quota_error' => true, 'message' => '🚫 Kuota AI kamu sudah habis bulan ini.'];
        }
        return null;
    }

    private function _quotaResponse(array|null $err, int $status = 403)
    {
        return response()->json($err, $status);
    }

    private function _consume(): int
    {
        $sub = auth()->user()->currentSubscription();
        $sub->consumeQuota(1);
        return $sub->fresh()->remaining_quota;
    }

    /** 3. SEO Metadata */
    public function generateSeoMetadata(Request $request)
    {
        set_time_limit(60);
        if ($err = $this->_checkQuota()) return $this->_quotaResponse($err);
        try {
            $v = $request->validate([
                'product_name'   => 'required|string|max:150',
                'product_desc'   => 'required|string|max:500',
                'category'       => 'nullable|string|max:100',
                'keywords'       => 'nullable|string|max:200',
                'url'            => 'nullable|string|max:200',
            ]);
            $result = $this->geminiService->generateSeoMetadata($v);
            $quota  = $this->_consume();
            return response()->json(['success' => true, 'data' => $result, 'quota_remaining' => $quota]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->implode('; ')], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 4. Smart Comparison */
    public function generateComparison(Request $request)
    {
        set_time_limit(90);
        if ($err = $this->_checkQuota()) return $this->_quotaResponse($err);
        try {
            $v = $request->validate([
                'product_a_name'  => 'required|string|max:150',
                'product_a_desc'  => 'required|string|max:400',
                'product_a_price' => 'nullable|string|max:100',
                'product_b_name'  => 'required|string|max:150',
                'product_b_desc'  => 'required|string|max:400',
                'product_b_price' => 'nullable|string|max:100',
                'buyer_persona'   => 'nullable|string|max:300',
            ]);
            $result = $this->geminiService->generateComparison($v);
            $quota  = $this->_consume();
            return response()->json(['success' => true, 'data' => $result, 'quota_remaining' => $quota]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->implode('; ')], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 5. FAQ Generator */
    public function generateFaq(Request $request)
    {
        set_time_limit(60);
        if ($err = $this->_checkQuota()) return $this->_quotaResponse($err);
        try {
            $v = $request->validate([
                'product_name' => 'required|string|max:150',
                'product_desc' => 'required|string|max:500',
                'price'        => 'nullable|string|max:100',
                'category'     => 'nullable|string|max:100',
            ]);
            $result = $this->geminiService->generateFaq($v);
            $quota  = $this->_consume();
            return response()->json(['success' => true, 'data' => $result, 'quota_remaining' => $quota]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->implode('; ')], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 6. Reels/TikTok Hook */
    public function generateReelsHook(Request $request)
    {
        set_time_limit(90);
        if ($err = $this->_checkQuota()) return $this->_quotaResponse($err);
        try {
            $v = $request->validate([
                'product_name'    => 'required|string|max:150',
                'product_desc'    => 'required|string|max:500',
                'target_audience' => 'nullable|string|max:200',
                'platform'        => 'nullable|string|max:50',
                'tone'            => 'nullable|string|max:50',
                'video_goal'      => 'nullable|string|max:200',
            ]);
            $result = $this->geminiService->generateReelsHook($v);
            $quota  = $this->_consume();
            return response()->json(['success' => true, 'data' => $result, 'quota_remaining' => $quota]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->implode('; ')], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 7. Quality Badge Scanner */
    public function generateQualityBadge(Request $request)
    {
        set_time_limit(90);
        if ($err = $this->_checkQuota()) return $this->_quotaResponse($err);
        try {
            $v = $request->validate([
                'product_name' => 'required|string|max:150',
                'product_desc' => 'required|string|max:500',
                'asset_type'   => 'nullable|string|max:100',
                'code_or_doc'  => 'nullable|string|max:3000',
            ]);
            $result = $this->geminiService->generateQualityBadge($v);
            $quota  = $this->_consume();
            return response()->json(['success' => true, 'data' => $result, 'quota_remaining' => $quota]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->implode('; ')], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 8. Discount Campaign Copywriter */
    public function generateDiscountCampaign(Request $request)
    {
        set_time_limit(90);
        if ($err = $this->_checkQuota()) return $this->_quotaResponse($err);
        try {
            $v = $request->validate([
                'promo_name'     => 'required|string|max:100',
                'product_name'   => 'required|string|max:150',
                'product_desc'   => 'required|string|max:400',
                'original_price' => 'nullable|string|max:100',
                'discount_price' => 'nullable|string|max:100',
                'discount_pct'   => 'nullable|string|max:10',
                'duration'       => 'nullable|string|max:100',
                'platform'       => 'nullable|string|max:100',
                'wa_number'      => 'nullable|string|max:20',
            ]);
            $result = $this->geminiService->generateDiscountCampaign($v);
            $quota  = $this->_consume();
            return response()->json(['success' => true, 'data' => $result, 'quota_remaining' => $quota]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->implode('; ')], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 9. Trend-Based Product Tagging */
    public function generateTrendTags(Request $request)
    {
        set_time_limit(60);
        if ($err = $this->_checkQuota()) return $this->_quotaResponse($err);
        try {
            $v = $request->validate([
                'product_name' => 'required|string|max:150',
                'product_desc' => 'required|string|max:500',
                'category'     => 'nullable|string|max:100',
                'current_tags' => 'nullable|string|max:300',
            ]);
            $result = $this->geminiService->generateTrendTags($v);
            $quota  = $this->_consume();
            return response()->json(['success' => true, 'data' => $result, 'quota_remaining' => $quota]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->implode('; ')], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 10. Lead Magnet Creator */
    public function generateLeadMagnet(Request $request)
    {
        set_time_limit(90);
        if ($err = $this->_checkQuota()) return $this->_quotaResponse($err);
        try {
            $v = $request->validate([
                'product_name'    => 'required|string|max:150',
                'product_desc'    => 'required|string|max:500',
                'product_type'    => 'nullable|string|max:100',
                'price'           => 'nullable|string|max:100',
                'target_audience' => 'nullable|string|max:200',
                'goal'            => 'nullable|string|max:200',
                'wa_number'       => 'nullable|string|max:20',
            ]);
            $result = $this->geminiService->generateLeadMagnet($v);
            $quota  = $this->_consume();
            return response()->json(['success' => true, 'data' => $result, 'quota_remaining' => $quota]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->implode('; ')], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
