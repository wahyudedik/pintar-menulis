<?php

namespace App\Services;

use App\Models\CaptionAnalytics;
use App\Models\CaptionHistory;
use Illuminate\Support\Facades\Log;

class CaptionPerformancePredictor
{
    protected $geminiService;
    protected $mlService;

    public function __construct(GeminiService $geminiService, MLDataService $mlService)
    {
        $this->geminiService = $geminiService;
        $this->mlService = $mlService;
    }

    /**
     * 📈 Predict caption performance with detailed metrics
     */
    public function predictPerformance(string $caption, array $context = []): array
    {
        $platform = $context['platform'] ?? 'instagram';
        $industry = $context['industry'] ?? 'general';
        $userId = $context['user_id'] ?? null;

        // Get historical data for better prediction
        $historicalData = $this->getHistoricalData($userId, $platform);
        
        // Analyze caption components
        $analysis = $this->analyzeCaption($caption, $platform);
        
        // Predict engagement rate using AI + historical data
        $engagementPrediction = $this->predictEngagementRate($caption, $platform, $industry, $historicalData, $analysis);
        
        // Calculate quality score (1-100)
        $qualityScore = $this->calculateQualityScore($analysis, $engagementPrediction);
        
        // Generate improvement suggestions
        $improvements = $this->generateImprovements($caption, $analysis, $qualityScore, $platform);
        
        // Predict best posting time
        $bestTime = $this->predictBestPostingTime($platform, $industry, $historicalData);

        return [
            'engagement_prediction' => $engagementPrediction,
            'quality_score' => $qualityScore,
            'analysis' => $analysis,
            'improvements' => $improvements,
            'best_posting_time' => $bestTime,
            'confidence_level' => $this->calculateConfidence($historicalData),
        ];
    }

    /**
     * 🎯 Generate A/B Testing Variants
     */
    public function generateABTestVariants(string $originalCaption, array $context = []): array
    {
        $platform = $context['platform'] ?? 'instagram';
        $industry = $context['industry'] ?? 'general';
        
        // Analyze original caption
        $originalAnalysis = $this->analyzeCaption($originalCaption, $platform);
        
        // Generate variant A (Hook-focused)
        $variantA = $this->generateVariant($originalCaption, 'hook', $platform, $industry);
        
        // Generate variant B (CTA-focused)
        $variantB = $this->generateVariant($originalCaption, 'cta', $platform, $industry);
        
        // Predict performance for each
        $originalPrediction = $this->predictPerformance($originalCaption, $context);
        $variantAPrediction = $this->predictPerformance($variantA, $context);
        $variantBPrediction = $this->predictPerformance($variantB, $context);

        return [
            'original' => [
                'caption' => $originalCaption,
                'prediction' => $originalPrediction,
                'focus' => 'Balanced',
            ],
            'variant_a' => [
                'caption' => $variantA,
                'prediction' => $variantAPrediction,
                'focus' => 'Hook-Optimized (Attention)',
            ],
            'variant_b' => [
                'caption' => $variantB,
                'prediction' => $variantBPrediction,
                'focus' => 'CTA-Optimized (Conversion)',
            ],
            'recommendation' => $this->recommendBestVariant($originalPrediction, $variantAPrediction, $variantBPrediction),
        ];
    }

    /**
     * Analyze caption components and return quality score
     */
    protected function analyzeCaption(string $caption, string $platform = 'instagram', array $context = []): array
    {
        $scores = [];
        $totalScore = 0;

        // 1. Hook Strength (20 points)
        $hookScore = $this->scoreHook($caption);
        $scores['hook_strength'] = $hookScore;
        $totalScore += $hookScore;

        // 2. Emotional Appeal (15 points)
        $emotionalScore = $this->scoreEmotionalAppeal($caption);
        $scores['emotional_appeal'] = $emotionalScore;
        $totalScore += $emotionalScore;

        // 3. CTA Strength (15 points)
        $ctaScore = $this->scoreCTA($caption);
        $scores['cta_strength'] = $ctaScore;
        $totalScore += $ctaScore;

        // 4. Readability (15 points)
        $readabilityScore = $this->scoreReadability($caption);
        $scores['readability'] = $readabilityScore;
        $totalScore += $readabilityScore;

        // 5. Hashtag Quality (10 points)
        $hashtagScore = $this->scoreHashtags($caption, $context);
        $scores['hashtag_quality'] = $hashtagScore;
        $totalScore += $hashtagScore;

        // 6. Length Optimization (10 points)
        $lengthScore = $this->scoreLength($caption, $platform);
        $scores['length_optimization'] = $lengthScore;
        $totalScore += $lengthScore;

        // 7. Emoji Usage (10 points)
        $emojiScore = $this->scoreEmojis($caption);
        $scores['emoji_usage'] = $emojiScore;
        $totalScore += $emojiScore;

        // 8. Urgency/FOMO (5 points)
        $urgencyScore = $this->scoreUrgency($caption);
        $scores['urgency_fomo'] = $urgencyScore;
        $totalScore += $urgencyScore;

        return [
            'total_score' => $totalScore,
            'breakdown' => $scores,
            'max_score' => 100,
        ];
    }

    /**
     * 📈 Predict Engagement Rate (like, comment, share)
     */
    protected function predictEngagementRate(string $caption, array $context, array $qualityScore): array
    {
        $platform = $context['platform'] ?? 'instagram';
        $userId = $context['user_id'] ?? null;

        // Base engagement rate by platform
        $baseRates = [
            'instagram' => ['like' => 3.5, 'comment' => 0.5, 'share' => 0.3],
            'facebook' => ['like' => 2.8, 'comment' => 0.4, 'share' => 0.6],
            'tiktok' => ['like' => 8.0, 'comment' => 1.2, 'share' => 2.5],
            'linkedin' => ['like' => 2.0, 'comment' => 0.3, 'share' => 0.2],
            'twitter' => ['like' => 1.5, 'comment' => 0.2, 'share' => 0.8],
        ];

        $base = $baseRates[$platform] ?? $baseRates['instagram'];

        // Adjust based on quality score (0.5x to 2.5x multiplier)
        $multiplier = 0.5 + ($qualityScore['total_score'] / 100) * 2.0;

        // Get user's historical performance
        $historicalMultiplier = 1.0;
        if ($userId) {
            $avgEngagement = CaptionAnalytics::where('user_id', $userId)
                ->where('platform', $platform)
                ->avg('engagement_rate');
            
            if ($avgEngagement && $avgEngagement > 0) {
                $historicalMultiplier = min(2.0, max(0.5, $avgEngagement / 3.0));
            }
        }

        $finalMultiplier = ($multiplier + $historicalMultiplier) / 2;

        return [
            'like_rate' => round($base['like'] * $finalMultiplier, 2),
            'comment_rate' => round($base['comment'] * $finalMultiplier, 2),
            'share_rate' => round($base['share'] * $finalMultiplier, 2),
            'total_engagement_rate' => round(
                ($base['like'] + $base['comment'] + $base['share']) * $finalMultiplier,
                2
            ),
            'confidence' => $this->calculateConfidence($userId, $platform),
            'benchmark' => $base,
        ];
    }

    /**
     * 💡 Generate Improvement Suggestions using AI
     */
    protected function generateImprovements(string $caption, array $context, array $qualityScore): array
    {
        $weakPoints = [];
        
        // Identify weak points
        foreach ($qualityScore['breakdown'] as $aspect => $score) {
            $maxScores = [
                'hook_strength' => 20,
                'emotional_appeal' => 15,
                'cta_strength' => 15,
                'readability' => 15,
                'hashtag_quality' => 10,
                'length_optimization' => 10,
                'emoji_usage' => 10,
                'urgency_fomo' => 5,
            ];
            
            $maxScore = $maxScores[$aspect] ?? 10;
            $percentage = ($score / $maxScore) * 100;
            
            if ($percentage < 60) {
                $weakPoints[] = $aspect;
            }
        }

        // Generate AI-powered suggestions
        $suggestions = [];
        
        if (in_array('hook_strength', $weakPoints)) {
            $suggestions[] = [
                'aspect' => 'Hook',
                'issue' => 'Opening kurang menarik perhatian',
                'suggestion' => 'Mulai dengan pertanyaan provokatif, statistik mengejutkan, atau statement bold',
                'example' => '❌ "Kami jual baju" → ✅ "Tau gak? 8 dari 10 orang salah pilih ukuran baju online!"',
            ];
        }

        if (in_array('cta_strength', $weakPoints)) {
            $suggestions[] = [
                'aspect' => 'Call-to-Action',
                'issue' => 'CTA kurang jelas atau tidak ada',
                'suggestion' => 'Tambahkan CTA yang spesifik dan urgent',
                'example' => '❌ "Silakan order" → ✅ "Order sekarang! Stok tinggal 5 pcs, besok harga naik!"',
            ];
        }

        if (in_array('emotional_appeal', $weakPoints)) {
            $suggestions[] = [
                'aspect' => 'Emotional Appeal',
                'issue' => 'Caption terlalu datar, kurang emosi',
                'suggestion' => 'Tambahkan storytelling atau pain point yang relatable',
                'example' => 'Ceritakan masalah yang dialami customer dan bagaimana produk jadi solusinya',
            ];
        }

        if (in_array('hashtag_quality', $weakPoints)) {
            $suggestions[] = [
                'aspect' => 'Hashtags',
                'issue' => 'Hashtag kurang optimal atau tidak ada',
                'suggestion' => 'Gunakan mix hashtag: 3 trending + 3 niche + 3 branded',
                'example' => $this->getSuggestedHashtags($context),
            ];
        }

        return $suggestions;
    }

    /**
     * ⏰ Predict Best Posting Time
     */
    protected function predictBestPostingTime(string $platform, string $industry): array
    {
        // Data based on industry research + Indonesia timezone
        $bestTimes = [
            'instagram' => [
                'fashion' => ['11:00', '13:00', '19:00', '21:00'],
                'food' => ['11:30', '12:00', '18:00', '19:30'],
                'beauty' => ['10:00', '13:00', '20:00', '21:00'],
                'tech' => ['09:00', '12:00', '15:00', '20:00'],
                'general' => ['11:00', '13:00', '19:00', '21:00'],
            ],
            'facebook' => [
                'general' => ['12:00', '13:00', '18:00', '19:00'],
            ],
            'tiktok' => [
                'general' => ['07:00', '12:00', '19:00', '22:00'],
            ],
            'linkedin' => [
                'general' => ['08:00', '12:00', '17:00'],
            ],
        ];

        $times = $bestTimes[$platform][$industry] ?? $bestTimes[$platform]['general'] ?? ['12:00', '19:00'];

        return [
            'recommended_times' => $times,
            'timezone' => 'Asia/Jakarta (WIB)',
            'best_days' => $this->getBestDays($platform),
            'avoid_times' => ['01:00-05:00', '14:00-16:00'],
        ];
    }

    /**
     * 🎲 Generate A/B Testing Variants
     */
    public function generateABVariants(string $caption, array $context): array
    {
        $platform = $context['platform'] ?? 'instagram';
        $goal = $context['goal'] ?? 'engagement';

        $prompt = "Kamu adalah expert A/B testing untuk social media.\n\n";
        $prompt .= "Caption Original:\n{$caption}\n\n";
        $prompt .= "Platform: {$platform}\n";
        $prompt .= "Goal: {$goal}\n\n";
        $prompt .= "Buatkan 2 VARIASI caption untuk A/B testing:\n\n";
        $prompt .= "VARIANT A: Fokus pada emotional appeal & storytelling\n";
        $prompt .= "VARIANT B: Fokus pada urgency & FOMO (scarcity, limited time)\n\n";
        $prompt .= "Format:\n";
        $prompt .= "=== VARIANT A ===\n";
        $prompt .= "[caption variant A]\n\n";
        $prompt .= "=== VARIANT B ===\n";
        $prompt .= "[caption variant B]\n\n";
        $prompt .= "=== TESTING HYPOTHESIS ===\n";
        $prompt .= "Variant A akan perform lebih baik jika: [kondisi]\n";
        $prompt .= "Variant B akan perform lebih baik jika: [kondisi]\n\n";
        $prompt .= "Gunakan bahasa Indonesia yang natural dan sesuai platform.";

        try {
            $result = $this->geminiService->generateText($prompt, 1500, 0.8);
            
            return [
                'success' => true,
                'variants' => $this->parseABVariants($result),
                'original' => $caption,
                'testing_duration' => '3-7 hari',
                'sample_size_needed' => 'Min. 100 impressions per variant',
            ];
        } catch (\Exception $e) {
            Log::error('A/B Variant Generation Failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal generate A/B variants',
            ];
        }
    }

    // ========== SCORING METHODS ==========

    protected function scoreHook(string $caption): float
    {
        $score = 0;
        $firstLine = explode("\n", $caption)[0];
        
        // Question hook
        if (preg_match('/\?/', $firstLine)) $score += 5;
        
        // Numbers/stats
        if (preg_match('/\d+/', $firstLine)) $score += 4;
        
        // Power words
        $powerWords = ['gratis', 'diskon', 'rahasia', 'terbukti', 'wow', 'gila', 'viral'];
        foreach ($powerWords as $word) {
            if (stripos($firstLine, $word) !== false) {
                $score += 3;
                break;
            }
        }
        
        // Emoji in first line
        if (preg_match('/[\x{1F300}-\x{1F9FF}]/u', $firstLine)) $score += 3;
        
        // Length check (not too long)
        if (strlen($firstLine) <= 100) $score += 5;
        
        return min(20, $score);
    }

    protected function scoreEmotionalAppeal(string $caption): float
    {
        $score = 0;
        
        // Emotional words
        $emotionalWords = [
            'bahagia', 'sedih', 'takut', 'khawatir', 'senang', 'bangga',
            'percaya', 'yakin', 'ragu', 'kecewa', 'puas', 'frustasi'
        ];
        
        foreach ($emotionalWords as $word) {
            if (stripos($caption, $word) !== false) {
                $score += 3;
            }
        }
        
        // Storytelling indicators
        if (preg_match('/(dulu|awalnya|cerita|pengalaman|kisah)/i', $caption)) {
            $score += 5;
        }
        
        // Personal pronouns
        if (preg_match('/(aku|saya|kita|kamu|kalian)/i', $caption)) {
            $score += 4;
        }
        
        return min(15, $score);
    }

    protected function scoreCTA(string $caption): float
    {
        $score = 0;
        
        // CTA words
        $ctaWords = [
            'order', 'beli', 'pesan', 'klik', 'chat', 'dm', 'wa', 'hubungi',
            'daftar', 'follow', 'like', 'comment', 'share', 'tag', 'save'
        ];
        
        $ctaCount = 0;
        foreach ($ctaWords as $word) {
            if (stripos($caption, $word) !== false) {
                $ctaCount++;
            }
        }
        
        if ($ctaCount > 0) $score += 8;
        if ($ctaCount > 2) $score += 4; // Multiple CTAs
        
        // Urgency in CTA
        if (preg_match('/(sekarang|hari ini|segera|cepat|buruan)/i', $caption)) {
            $score += 3;
        }
        
        return min(15, $score);
    }

    protected function scoreReadability(string $caption): float
    {
        $score = 15; // Start with full score
        
        // Penalty for too long sentences
        $sentences = preg_split('/[.!?]+/', $caption);
        foreach ($sentences as $sentence) {
            if (strlen(trim($sentence)) > 200) {
                $score -= 3;
            }
        }
        
        // Penalty for no paragraph breaks
        if (strlen($caption) > 300 && substr_count($caption, "\n") < 2) {
            $score -= 4;
        }
        
        // Bonus for good structure
        if (substr_count($caption, "\n") >= 2) {
            $score += 2;
        }
        
        return max(0, min(15, $score));
    }

    protected function scoreHashtags(string $caption, array $context): float
    {
        preg_match_all('/#\w+/', $caption, $matches);
        $hashtagCount = count($matches[0]);
        
        if ($hashtagCount === 0) return 0;
        if ($hashtagCount >= 5 && $hashtagCount <= 15) return 10;
        if ($hashtagCount >= 3 && $hashtagCount <= 20) return 7;
        if ($hashtagCount > 20) return 3; // Too many
        
        return 5;
    }

    protected function scoreLength(string $caption, string $platform): float
    {
        $length = strlen($caption);
        
        $optimal = [
            'instagram' => ['min' => 150, 'max' => 500],
            'facebook' => ['min' => 100, 'max' => 400],
            'tiktok' => ['min' => 50, 'max' => 300],
            'linkedin' => ['min' => 200, 'max' => 600],
            'twitter' => ['min' => 50, 'max' => 280],
        ];
        
        $range = $optimal[$platform] ?? $optimal['instagram'];
        
        if ($length >= $range['min'] && $length <= $range['max']) return 10;
        if ($length >= $range['min'] * 0.8 && $length <= $range['max'] * 1.2) return 7;
        
        return 4;
    }

    protected function scoreEmojis(string $caption): float
    {
        preg_match_all('/[\x{1F300}-\x{1F9FF}]/u', $caption, $matches);
        $emojiCount = count($matches[0]);
        
        if ($emojiCount >= 3 && $emojiCount <= 10) return 10;
        if ($emojiCount >= 1 && $emojiCount <= 15) return 7;
        if ($emojiCount > 15) return 3; // Too many
        
        return 0;
    }

    protected function scoreUrgency(string $caption): float
    {
        $urgencyWords = [
            'terbatas', 'limited', 'segera', 'cepat', 'hari ini', 'sekarang',
            'stok terbatas', 'promo', 'diskon', 'flash sale', 'besok', 'terakhir'
        ];
        
        $score = 0;
        foreach ($urgencyWords as $word) {
            if (stripos($caption, $word) !== false) {
                $score += 2;
            }
        }
        
        return min(5, $score);
    }

    // ========== HELPER METHODS ==========

    protected function getGrade(float $score): string
    {
        if ($score >= 90) return 'A+ (Excellent)';
        if ($score >= 80) return 'A (Very Good)';
        if ($score >= 70) return 'B (Good)';
        if ($score >= 60) return 'C (Average)';
        if ($score >= 50) return 'D (Below Average)';
        return 'F (Poor)';
    }

    protected function calculateConfidence(?int $userId, string $platform): string
    {
        if (!$userId) return 'Low (No historical data)';
        
        $dataCount = CaptionAnalytics::where('user_id', $userId)
            ->where('platform', $platform)
            ->count();
        
        if ($dataCount >= 20) return 'High';
        if ($dataCount >= 10) return 'Medium';
        if ($dataCount >= 5) return 'Low-Medium';
        
        return 'Low';
    }

    protected function getBestDays(string $platform): array
    {
        $days = [
            'instagram' => ['Senin', 'Rabu', 'Jumat', 'Sabtu'],
            'facebook' => ['Rabu', 'Kamis', 'Jumat'],
            'tiktok' => ['Selasa', 'Kamis', 'Sabtu', 'Minggu'],
            'linkedin' => ['Selasa', 'Rabu', 'Kamis'],
        ];
        
        return $days[$platform] ?? ['Senin', 'Rabu', 'Jumat'];
    }

    protected function getSuggestedHashtags(array $context): string
    {
        $industry = $context['industry'] ?? 'general';
        $platform = $context['platform'] ?? 'instagram';
        
        $hashtags = $this->mlService->getTrendingHashtags($industry, $platform, 9);
        
        return implode(' ', array_map(fn($h) => "#{$h}", $hashtags));
    }

    protected function parseABVariants(string $result): array
    {
        $variants = [
            'variant_a' => '',
            'variant_b' => '',
            'hypothesis' => '',
        ];
        
        // Parse Variant A
        if (preg_match('/=== VARIANT A ===(.*?)=== VARIANT B ===/s', $result, $matches)) {
            $variants['variant_a'] = trim($matches[1]);
        }
        
        // Parse Variant B
        if (preg_match('/=== VARIANT B ===(.*?)=== TESTING HYPOTHESIS ===/s', $result, $matches)) {
            $variants['variant_b'] = trim($matches[1]);
        }
        
        // Parse Hypothesis
        if (preg_match('/=== TESTING HYPOTHESIS ===(.*?)$/s', $result, $matches)) {
            $variants['hypothesis'] = trim($matches[1]);
        }
        
        return $variants;
    }
}
