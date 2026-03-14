<?php

use Illuminate\Support\Facades\Route;
use App\Services\CaptionPerformancePredictorService;
use App\Services\GeminiService;
use App\Services\MLDataService;

Route::get('/test-performance-predictor', function () {
    try {
        $testCaption = "Hai Bun! 👋 Tau gak sih rahasia kulit glowing tanpa ribet? ✨ 

Cuma butuh 3 langkah simpel:
1. Cleansing yang bersih 🧼
2. Toner untuk pH balance 💧  
3. Moisturizer yang tepat 🌸

Produk skincare lokal ini udah terbukti bikin kulit cerah dalam 2 minggu! 

Mau tau produk apa? Comment \"GLOWING\" ya! 💕

#skincare #glowing #kulitsehat #skincarelokal";

        $geminiService = app(GeminiService::class);
        $mlService = app(MLDataService::class);
        
        $predictor = new CaptionPerformancePredictorService($geminiService, $mlService);
        
        $context = [
            'platform' => 'instagram',
            'industry' => 'beauty',
            'user_id' => 1
        ];
        
        // Test full prediction (without AI calls for A/B variants)
        $reflection = new ReflectionClass($predictor);
        
        // Test analysis
        $analyzeMethod = $reflection->getMethod('analyzeCaptionComponents');
        $analyzeMethod->setAccessible(true);
        $analysis = $analyzeMethod->invoke($predictor, $testCaption, 'instagram');
        
        // Test engagement prediction
        $predictMethod = $reflection->getMethod('calculateEngagementPrediction');
        $predictMethod->setAccessible(true);
        $historicalData = ['avg_engagement' => 4.5, 'sample_size' => 100, 'source' => 'test'];
        $prediction = $predictMethod->invoke($predictor, $analysis, $historicalData, $context);
        
        // Test quality score
        $qualityMethod = $reflection->getMethod('calculateQualityScore');
        $qualityMethod->setAccessible(true);
        $qualityScore = $qualityMethod->invoke($predictor, $analysis, $prediction);
        
        // Test improvement suggestions
        $improvementMethod = $reflection->getMethod('generateImprovementSuggestions');
        $improvementMethod->setAccessible(true);
        $improvements = $improvementMethod->invoke($predictor, $analysis, $testCaption, $context);
        
        // Test best posting time
        $timeMethod = $reflection->getMethod('predictBestPostingTime');
        $timeMethod->setAccessible(true);
        $bestTime = $timeMethod->invoke($predictor, 'instagram', 'beauty', 1);
        
        return response()->json([
            'success' => true,
            'message' => 'Full Performance Predictor Test Passed!',
            'results' => [
                'analysis' => $analysis,
                'prediction' => $prediction,
                'quality_score' => $qualityScore,
                'improvements' => $improvements,
                'best_posting_time' => $bestTime,
            ],
            'summary' => [
                'caption_length' => $analysis['length'],
                'quality_score' => $qualityScore['total_score'],
                'grade' => $qualityScore['grade'],
                'engagement_rate' => $prediction['engagement_rate'] . '%',
                'confidence' => $prediction['confidence'],
                'improvement_count' => count($improvements),
                'best_times_today' => $bestTime['best_times_today'],
            ]
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Demo endpoints (without CSRF protection)
Route::withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    
    // Test endpoint tanpa authentication untuk demo
    Route::post('/demo-predict-performance', function (Illuminate\Http\Request $request) {
        try {
            $validated = $request->validate([
                'caption' => 'required|string|min:10',
                'platform' => 'nullable|string',
                'industry' => 'nullable|string',
            ]);

            $geminiService = app(\App\Services\GeminiService::class);
            $mlService = app(\App\Services\MLDataService::class);
            
            $predictorService = new \App\Services\CaptionPerformancePredictorService(
                $geminiService,
                $mlService
            );

            $context = [
                'platform' => $validated['platform'] ?? 'instagram',
                'industry' => $validated['industry'] ?? 'general',
                'user_id' => 1, // Demo user
            ];

            // Get prediction results (without AI calls for A/B variants to avoid API costs)
            $reflection = new ReflectionClass($predictorService);
            
            // Analysis
            $analyzeMethod = $reflection->getMethod('analyzeCaptionComponents');
            $analyzeMethod->setAccessible(true);
            $analysis = $analyzeMethod->invoke($predictorService, $validated['caption'], $context['platform']);
            
            // Prediction
            $predictMethod = $reflection->getMethod('calculateEngagementPrediction');
            $predictMethod->setAccessible(true);
            $historicalData = ['avg_engagement' => 4.5, 'sample_size' => 100, 'source' => 'demo'];
            $prediction = $predictMethod->invoke($predictorService, $analysis, $historicalData, $context);
            
            // Quality Score
            $qualityMethod = $reflection->getMethod('calculateQualityScore');
            $qualityMethod->setAccessible(true);
            $qualityScore = $qualityMethod->invoke($predictorService, $analysis, $prediction);
            
            // Improvements
            $improvementMethod = $reflection->getMethod('generateImprovementSuggestions');
            $improvementMethod->setAccessible(true);
            $improvements = $improvementMethod->invoke($predictorService, $analysis, $validated['caption'], $context);
            
            // Best Time
            $timeMethod = $reflection->getMethod('predictBestPostingTime');
            $timeMethod->setAccessible(true);
            $bestTime = $timeMethod->invoke($predictorService, $context['platform'], $context['industry'], 1);
            
            // Simple A/B variant (without AI)
            $abVariant = [
                'variant_caption' => 'Demo A/B variant: ' . substr($validated['caption'], 0, 100) . '... (improved version)',
                'test_focus' => 'Hook & CTA Optimization',
                'success_metrics' => ['engagement_rate', 'click_through_rate'],
                'recommended_duration' => '3-7 hari',
                'sample_size_needed' => 'Minimal 1000 impressions per variant'
            ];

            return response()->json([
                'success' => true,
                'prediction' => $prediction,
                'quality_score' => $qualityScore,
                'improvements' => $improvements,
                'ab_variant' => $abVariant,
                'best_posting_time' => $bestTime,
                'analysis_details' => $analysis,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Demo error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });

    // Demo A/B Variants endpoint
    Route::post('/demo-generate-ab-variants', function (Illuminate\Http\Request $request) {
        try {
            $validated = $request->validate([
                'caption' => 'required|string|min:10',
                'platform' => 'nullable|string',
                'industry' => 'nullable|string',
                'variant_count' => 'nullable|integer|in:2,3,4,5',
                'focus_area' => 'nullable|string|in:hook,cta,length,emoji,hashtag,overall',
            ]);

            $context = [
                'platform' => $validated['platform'] ?? 'instagram',
                'industry' => $validated['industry'] ?? 'general',
                'user_id' => 1, // Demo user
            ];

            $variantCount = $validated['variant_count'] ?? 2;
            $focusArea = $validated['focus_area'] ?? 'overall';

            // Generate demo variants (without AI to save costs)
            $variants = [];
            for ($i = 1; $i <= $variantCount; $i++) {
                $originalCaption = $validated['caption'];
                
                // Simple variant generation based on focus area
                $variantCaption = $originalCaption;
                
                switch ($focusArea) {
                    case 'hook':
                        $variantCaption = "🔥 Pssst... " . $originalCaption;
                        break;
                    case 'cta':
                        $variantCaption = $originalCaption . "\n\n💬 DM sekarang untuk info lebih lanjut!";
                        break;
                    case 'emoji':
                        $variantCaption = "✨ " . str_replace("!", " 🎉!", $originalCaption) . " ✨";
                        break;
                    case 'length':
                        if ($i % 2 === 0) {
                            // Shorter version
                            $variantCaption = substr($originalCaption, 0, 150) . "... Singkat tapi padat! 💯";
                        } else {
                            // Longer version
                            $variantCaption = $originalCaption . "\n\nTambahan info: Produk ini sudah terbukti dan dipercaya ribuan customer! 🌟";
                        }
                        break;
                    case 'hashtag':
                        $variantCaption = $originalCaption . "\n\n#trending #viral #mustbuy #recommended";
                        break;
                    default: // overall
                        $variantCaption = "🌟 SPECIAL EDITION 🌟\n\n" . $originalCaption . "\n\n⚡ Limited time offer!";
                }
                
                $variants[] = [
                    'variant_id' => $i,
                    'caption' => trim($variantCaption),
                    'focus' => $focusArea,
                    'test_hypothesis' => getDemoHypothesis($focusArea, $i),
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
            return response()->json([
                'success' => false,
                'message' => 'Demo A/B variants error: ' . $e->getMessage()
            ], 500);
        }
    });
    
});

// Simple GET demo endpoints (no CSRF issues)
Route::get('/demo-predict/{caption}', function ($caption) {
    try {
        $caption = urldecode($caption);
        
        $geminiService = app(\App\Services\GeminiService::class);
        $mlService = app(\App\Services\MLDataService::class);
        
        $predictorService = new \App\Services\CaptionPerformancePredictorService(
            $geminiService,
            $mlService
        );

        $context = [
            'platform' => 'instagram',
            'industry' => 'beauty',
            'user_id' => 1,
        ];

        // Get prediction results with REAL AI analysis
        $results = $predictorService->predictPerformance($caption, $context);

        return response()->json([
            'success' => true,
            'prediction' => $results['prediction'],
            'quality_score' => $results['quality_score'],
            'improvements' => $results['improvements'],
            'ab_variant' => $results['ab_variant'],
            'best_posting_time' => $results['best_posting_time'],
            'analysis_details' => $results['analysis'],
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Demo error: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/demo-variants/{caption}', function ($caption) {
    try {
        $caption = urldecode($caption);
        
        // Generate 3 demo variants
        $variants = [
            [
                'variant_id' => 1,
                'caption' => "🔥 Pssst... " . $caption,
                'focus' => 'hook',
                'test_hypothesis' => 'Hook yang lebih menarik akan meningkatkan engagement rate',
            ],
            [
                'variant_id' => 2,
                'caption' => $caption . "\n\n💬 DM sekarang untuk info lebih lanjut!",
                'focus' => 'cta',
                'test_hypothesis' => 'CTA yang lebih direct akan meningkatkan conversion rate',
            ],
            [
                'variant_id' => 3,
                'caption' => "🌟 SPECIAL EDITION 🌟\n\n" . $caption . "\n\n⚡ Limited time offer!",
                'focus' => 'overall',
                'test_hypothesis' => 'Variant ini akan meningkatkan overall performance',
            ]
        ];

        return response()->json([
            'success' => true,
            'original_caption' => $caption,
            'variants' => $variants,
            'test_setup' => [
                'recommended_duration' => '5-7 hari',
                'minimum_sample_size' => '1000 impressions per variant',
                'success_metrics' => ['engagement_rate', 'click_through_rate', 'conversion_rate'],
                'statistical_significance' => '95% confidence level'
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Demo variants error: ' . $e->getMessage()
        ], 500);
    }
});