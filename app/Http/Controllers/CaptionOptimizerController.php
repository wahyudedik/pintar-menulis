<?php

namespace App\Http\Controllers;

use App\Services\GrammarCheckerService;
use App\Services\CaptionLengthOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CaptionOptimizerController extends Controller
{
    private $grammarService;
    private $lengthService;

    public function __construct(
        GrammarCheckerService $grammarService,
        CaptionLengthOptimizerService $lengthService
    ) {
        $this->grammarService = $grammarService;
        $this->lengthService = $lengthService;
    }

    /**
     * 📝 Check grammar and spelling
     */
    public function checkGrammar(Request $request)
    {
        $request->validate([
            'text' => 'required|string|min:5|max:2000',
            'language' => 'nullable|in:id,en,mix'
        ]);

        try {
            $result = $this->grammarService->checkGrammar(
                $request->text,
                $request->language ?? 'id'
            );

            return response()->json($result);

        } catch (Exception $e) {
            Log::error('Grammar check API error', [
                'error' => $e->getMessage(),
                'text_length' => strlen($request->text)
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Grammar check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔧 Quick grammar fix
     */
    public function quickGrammarFix(Request $request)
    {
        $request->validate([
            'text' => 'required|string|min:5|max:2000',
            'language' => 'nullable|in:id,en,mix'
        ]);

        try {
            $result = $this->grammarService->quickFix(
                $request->text,
                $request->language ?? 'id'
            );

            return response()->json($result);

        } catch (Exception $e) {
            Log::error('Quick grammar fix API error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Quick fix failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📊 Get detailed grammar analysis
     */
    public function getDetailedGrammarAnalysis(Request $request)
    {
        $request->validate([
            'text' => 'required|string|min:5|max:2000',
            'language' => 'nullable|in:id,en,mix'
        ]);

        try {
            $result = $this->grammarService->getDetailedAnalysis(
                $request->text,
                $request->language ?? 'id'
            );

            return response()->json($result);

        } catch (Exception $e) {
            Log::error('Detailed grammar analysis API error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Detailed analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✂️ Shorten caption
     */
    public function shortenCaption(Request $request)
    {
        $request->validate([
            'caption' => 'required|string|min:10|max:3000',
            'target_length' => 'required|integer|min:50|max:500',
            'platform' => 'nullable|string|in:instagram,tiktok,facebook,twitter,linkedin,youtube',
            'preserve_hashtags' => 'nullable|boolean',
            'preserve_emojis' => 'nullable|boolean',
            'preserve_cta' => 'nullable|boolean'
        ]);

        try {
            $options = [
                'platform' => $request->platform ?? 'general',
                'preserve_hashtags' => $request->preserve_hashtags ?? true,
                'preserve_emojis' => $request->preserve_emojis ?? true,
                'preserve_cta' => $request->preserve_cta ?? true
            ];

            $result = $this->lengthService->shortenCaption(
                $request->caption,
                $request->target_length,
                $options
            );

            return response()->json($result);

        } catch (Exception $e) {
            Log::error('Caption shortening API error', [
                'error' => $e->getMessage(),
                'original_length' => strlen($request->caption),
                'target_length' => $request->target_length
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Caption shortening failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📈 Expand caption
     */
    public function expandCaption(Request $request)
    {
        $request->validate([
            'caption' => 'required|string|min:10|max:1000',
            'target_length' => 'required|integer|min:100|max:2000',
            'platform' => 'nullable|string|in:instagram,tiktok,facebook,twitter,linkedin,youtube',
            'expansion_type' => 'nullable|string|in:detailed,storytelling,educational,promotional,engaging',
            'industry' => 'nullable|string|max:100',
            'add_hashtags' => 'nullable|boolean',
            'add_emojis' => 'nullable|boolean'
        ]);

        try {
            $options = [
                'platform' => $request->platform ?? 'general',
                'expansion_type' => $request->expansion_type ?? 'detailed',
                'industry' => $request->industry ?? 'general',
                'add_hashtags' => $request->add_hashtags ?? true,
                'add_emojis' => $request->add_emojis ?? true
            ];

            $result = $this->lengthService->expandCaption(
                $request->caption,
                $request->target_length,
                $options
            );

            return response()->json($result);

        } catch (Exception $e) {
            Log::error('Caption expansion API error', [
                'error' => $e->getMessage(),
                'original_length' => strlen($request->caption),
                'target_length' => $request->target_length
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Caption expansion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🎯 Smart length adjustment
     */
    public function smartAdjustLength(Request $request)
    {
        $request->validate([
            'caption' => 'required|string|min:10|max:3000',
            'platform' => 'required|string|in:instagram,tiktok,facebook,twitter,linkedin,youtube',
            'preserve_hashtags' => 'nullable|boolean',
            'preserve_emojis' => 'nullable|boolean',
            'expansion_type' => 'nullable|string|in:detailed,storytelling,educational'
        ]);

        try {
            $preferences = [
                'preserve_hashtags' => $request->preserve_hashtags ?? true,
                'preserve_emojis' => $request->preserve_emojis ?? true,
                'expansion_type' => $request->expansion_type ?? 'detailed'
            ];

            $result = $this->lengthService->smartAdjustLength(
                $request->caption,
                $request->platform,
                $preferences
            );

            return response()->json($result);

        } catch (Exception $e) {
            Log::error('Smart length adjustment API error', [
                'error' => $e->getMessage(),
                'platform' => $request->platform
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Smart adjustment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📏 Get optimal length guide
     */
    public function getOptimalLength(Request $request)
    {
        $request->validate([
            'platform' => 'required|string|in:instagram,tiktok,facebook,twitter,linkedin,youtube',
            'content_type' => 'nullable|string|in:caption,story,reels,bio,ad,tweet,thread,post,article,description,shorts'
        ]);

        try {
            $result = $this->lengthService->getOptimalLength(
                $request->platform,
                $request->content_type ?? 'caption'
            );

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (Exception $e) {
            Log::error('Optimal length guide API error', [
                'error' => $e->getMessage(),
                'platform' => $request->platform
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get optimal length guide: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📊 Analyze length impact
     */
    public function analyzeLengthImpact(Request $request)
    {
        $request->validate([
            'caption' => 'required|string|min:5|max:3000',
            'platform' => 'required|string|in:instagram,tiktok,facebook,twitter,linkedin,youtube'
        ]);

        try {
            $result = $this->lengthService->analyzeLengthImpact(
                $request->caption,
                $request->platform
            );

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (Exception $e) {
            Log::error('Length impact analysis API error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Length impact analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📈 Get optimizer statistics
     */
    public function getOptimizerStats()
    {
        try {
            $grammarStats = $this->grammarService->getGrammarStats();
            
            $stats = [
                'grammar_checker' => $grammarStats,
                'length_optimizer' => [
                    'service_status' => 'active',
                    'supported_platforms' => ['instagram', 'tiktok', 'facebook', 'twitter', 'linkedin', 'youtube'],
                    'features' => [
                        'smart_shortening' => true,
                        'smart_expansion' => true,
                        'length_analysis' => true,
                        'platform_optimization' => true,
                        'preservation_options' => true
                    ],
                    'expansion_types' => ['detailed', 'storytelling', 'educational', 'promotional', 'engaging']
                ],
                'integration_status' => [
                    'ai_generator' => 'integrated',
                    'analysis_dashboard' => 'integrated',
                    'real_time_optimization' => 'available'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (Exception $e) {
            Log::error('Optimizer stats API error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get optimizer stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔄 Batch optimize multiple captions
     */
    public function batchOptimize(Request $request)
    {
        $request->validate([
            'captions' => 'required|array|min:1|max:10',
            'captions.*' => 'required|string|min:10|max:2000',
            'platform' => 'required|string|in:instagram,tiktok,facebook,twitter,linkedin,youtube',
            'optimization_type' => 'required|string|in:grammar,length,both',
            'target_length' => 'nullable|integer|min:50|max:2000'
        ]);

        try {
            $results = [];
            $optimizationType = $request->optimization_type;
            $platform = $request->platform;
            $targetLength = $request->target_length;

            foreach ($request->captions as $index => $caption) {
                $captionResult = [
                    'index' => $index,
                    'original' => $caption,
                    'optimizations' => []
                ];

                // Grammar optimization
                if ($optimizationType === 'grammar' || $optimizationType === 'both') {
                    try {
                        $grammarResult = $this->grammarService->quickFix($caption);
                        $captionResult['optimizations']['grammar'] = $grammarResult;
                    } catch (Exception $e) {
                        $captionResult['optimizations']['grammar'] = [
                            'success' => false,
                            'error' => $e->getMessage()
                        ];
                    }
                }

                // Length optimization
                if ($optimizationType === 'length' || $optimizationType === 'both') {
                    try {
                        if ($targetLength) {
                            $lengthResult = $this->lengthService->smartAdjustLength($caption, $platform);
                        } else {
                            $lengthResult = $this->lengthService->analyzeLengthImpact($caption, $platform);
                        }
                        $captionResult['optimizations']['length'] = $lengthResult;
                    } catch (Exception $e) {
                        $captionResult['optimizations']['length'] = [
                            'success' => false,
                            'error' => $e->getMessage()
                        ];
                    }
                }

                $results[] = $captionResult;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_processed' => count($results),
                    'optimization_type' => $optimizationType,
                    'platform' => $platform,
                    'results' => $results
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Batch optimization API error', [
                'error' => $e->getMessage(),
                'captions_count' => count($request->captions ?? [])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Batch optimization failed: ' . $e->getMessage()
            ], 500);
        }
    }
}