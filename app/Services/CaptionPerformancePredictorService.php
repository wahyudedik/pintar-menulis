<?php

namespace App\Services;

use App\Models\CaptionHistory;
use App\Models\CaptionAnalytics;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CaptionPerformancePredictorService
{
    protected $geminiService;
    protected $mlService;

    public function __construct(GeminiService $geminiService, MLDataService $mlService)
    {
        $this->geminiService = $geminiService;
        $this->mlService = $mlService;
    }

    /**
     * 🎯 MAIN FEATURE: Predict caption performance
     */
    public function predictPerformance(string $caption, array $context = []): array
    {
        $platform = $context['platform'] ?? 'instagram';
        $industry = $context['industry'] ?? 'general';
        $userId = $context['user_id'] ?? null;

        // Get historical data for better prediction
        $historicalData = $this->getHistoricalPerformanceData($platform, $industry, $userId);
        
        // Analyze caption components
        $analysis = $this->analyzeCaptionComponents($caption, $platform);
        
        // Predict engagement metrics
        $prediction = $this->calculateEngagementPrediction($analysis, $historicalData, $context);
        
        // Generate quality score (1-100)
        $qualityScore = $this->calculateQualityScore($analysis, $prediction);
        
        // Generate improvement suggestions
        $improvements = $this->generateImprovementSuggestions($analysis, $caption, $context);
        
        // Generate A/B testing variant
        $abVariant = $this->generateABTestingVariant($caption, $context, $improvements);
        
        // Predict best posting time
        $bestTime = $this->predictBestPostingTime($platform, $industry, $userId);

        return [
            'prediction' => $prediction,
            'quality_score' => $qualityScore,
            'improvements' => $improvements,
            'ab_variant' => $abVariant,
            'best_posting_time' => $bestTime,
            'analysis' => $analysis,
        ];
    }
    /**
     * 📊 Analyze caption components for prediction
     */
    protected function analyzeCaptionComponents(string $caption, string $platform): array
    {
        $analysis = [
            'length' => strlen($caption),
            'word_count' => str_word_count($caption),
            'sentence_count' => substr_count($caption, '.') + substr_count($caption, '!') + substr_count($caption, '?'),
            'emoji_count' => preg_match_all('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', $caption),
            'hashtag_count' => preg_match_all('/#\w+/', $caption),
            'question_count' => substr_count($caption, '?'),
            'exclamation_count' => substr_count($caption, '!'),
            'has_cta' => $this->detectCallToAction($caption),
            'has_hook' => $this->detectHook($caption),
            'sentiment' => $this->analyzeSentiment($caption),
            'readability' => $this->calculateReadability($caption),
            'urgency_words' => $this->countUrgencyWords($caption),
            'power_words' => $this->countPowerWords($caption),
        ];

        // Platform-specific analysis
        if ($platform === 'instagram') {
            $analysis['optimal_length'] = $analysis['length'] >= 125 && $analysis['length'] <= 2200;
            $analysis['hashtag_optimal'] = $analysis['hashtag_count'] >= 5 && $analysis['hashtag_count'] <= 30;
        } elseif ($platform === 'tiktok') {
            $analysis['optimal_length'] = $analysis['length'] >= 50 && $analysis['length'] <= 300;
            $analysis['hashtag_optimal'] = $analysis['hashtag_count'] >= 3 && $analysis['hashtag_count'] <= 10;
        } elseif ($platform === 'facebook') {
            $analysis['optimal_length'] = $analysis['length'] >= 40 && $analysis['length'] <= 500;
            $analysis['hashtag_optimal'] = $analysis['hashtag_count'] >= 1 && $analysis['hashtag_count'] <= 5;
        }

        return $analysis;
    }

    /**
     * 🎯 Calculate engagement prediction
     */
    protected function calculateEngagementPrediction(array $analysis, array $historicalData, array $context): array
    {
        $baseEngagement = $historicalData['avg_engagement'] ?? 3.5; // Default 3.5%
        
        $multiplier = 1.0;
        
        // Length optimization
        if ($analysis['optimal_length']) $multiplier += 0.15;
        
        // Emoji impact
        if ($analysis['emoji_count'] >= 3 && $analysis['emoji_count'] <= 8) $multiplier += 0.10;
        
        // Hashtag optimization
        if ($analysis['hashtag_optimal']) $multiplier += 0.12;
        
        // CTA presence
        if ($analysis['has_cta']) $multiplier += 0.20;
        
        // Hook presence
        if ($analysis['has_hook']) $multiplier += 0.25;
        
        // Question engagement
        if ($analysis['question_count'] >= 1) $multiplier += 0.15;
        
        // Sentiment impact
        if ($analysis['sentiment'] === 'positive') $multiplier += 0.10;
        elseif ($analysis['sentiment'] === 'negative') $multiplier -= 0.05;
        
        // Readability
        if ($analysis['readability'] >= 7) $multiplier += 0.08;
        
        // Power words
        $multiplier += min($analysis['power_words'] * 0.03, 0.15);
        
        // Urgency words
        $multiplier += min($analysis['urgency_words'] * 0.05, 0.10);

        $predictedEngagement = $baseEngagement * $multiplier;
        
        // Calculate individual metrics
        $likes = $predictedEngagement * 0.70; // 70% of engagement is likes
        $comments = $predictedEngagement * 0.20; // 20% comments
        $shares = $predictedEngagement * 0.10; // 10% shares

        return [
            'engagement_rate' => round($predictedEngagement, 2),
            'likes_rate' => round($likes, 2),
            'comments_rate' => round($comments, 2),
            'shares_rate' => round($shares, 2),
            'confidence' => $this->calculateConfidence($analysis, $historicalData),
        ];
    }

    /**
     * 💯 Calculate quality score (1-100)
     */
    protected function calculateQualityScore(array $analysis, array $prediction): array
    {
        $score = 0;
        $breakdown = [];

        // Content Structure (25 points)
        $structureScore = 0;
        if ($analysis['has_hook']) $structureScore += 8;
        if ($analysis['has_cta']) $structureScore += 8;
        if ($analysis['optimal_length']) $structureScore += 5;
        if ($analysis['sentence_count'] >= 2 && $analysis['sentence_count'] <= 8) $structureScore += 4;
        $breakdown['structure'] = min($structureScore, 25);

        // Engagement Elements (25 points)
        $engagementScore = 0;
        if ($analysis['question_count'] >= 1) $engagementScore += 8;
        if ($analysis['emoji_count'] >= 3 && $analysis['emoji_count'] <= 8) $engagementScore += 7;
        if ($analysis['hashtag_optimal']) $engagementScore += 6;
        if ($analysis['exclamation_count'] >= 1) $engagementScore += 4;
        $breakdown['engagement'] = min($engagementScore, 25);

        // Content Quality (25 points)
        $qualityScore = 0;
        $qualityScore += min($analysis['power_words'] * 3, 10);
        $qualityScore += min($analysis['readability'], 8);
        if ($analysis['sentiment'] === 'positive') $qualityScore += 7;
        $breakdown['quality'] = min($qualityScore, 25);

        // Performance Potential (25 points)
        $performanceScore = min($prediction['engagement_rate'] * 5, 25);
        $breakdown['performance'] = round($performanceScore);

        $totalScore = array_sum($breakdown);
        
        // Determine grade
        $grade = 'F';
        if ($totalScore >= 90) $grade = 'A+';
        elseif ($totalScore >= 85) $grade = 'A';
        elseif ($totalScore >= 80) $grade = 'B+';
        elseif ($totalScore >= 75) $grade = 'B';
        elseif ($totalScore >= 70) $grade = 'C+';
        elseif ($totalScore >= 65) $grade = 'C';
        elseif ($totalScore >= 60) $grade = 'D';

        return [
            'total_score' => $totalScore,
            'grade' => $grade,
            'breakdown' => $breakdown,
        ];
    }
    /**
     * 💡 Generate improvement suggestions
     */
    protected function generateImprovementSuggestions(array $analysis, string $caption, array $context): array
    {
        $suggestions = [];
        $priority = [];

        // Hook improvement
        if (!$analysis['has_hook']) {
            $suggestions[] = [
                'type' => 'hook',
                'title' => 'Tambahkan Hook Menarik',
                'description' => 'Caption butuh pembuka yang menarik perhatian dalam 3 detik pertama',
                'examples' => ['Tau gak sih...', 'Pernah nggak kamu...', 'Siapa yang setuju kalau...'],
                'impact' => '+25% engagement'
            ];
            $priority[] = 'high';
        }

        // CTA improvement
        if (!$analysis['has_cta']) {
            $suggestions[] = [
                'type' => 'cta',
                'title' => 'Tambahkan Call-to-Action',
                'description' => 'Ajak audience untuk melakukan aksi spesifik',
                'examples' => ['DM untuk order!', 'Comment "MINAT" ya!', 'Save post ini!'],
                'impact' => '+20% conversion'
            ];
            $priority[] = 'high';
        }

        // Length optimization
        if (!$analysis['optimal_length']) {
            $platform = $context['platform'] ?? 'instagram';
            if ($platform === 'instagram') {
                $optimal = '125-2200 karakter';
            } elseif ($platform === 'tiktok') {
                $optimal = '50-300 karakter';
            } else {
                $optimal = '40-500 karakter';
            }
            
            $suggestions[] = [
                'type' => 'length',
                'title' => 'Optimasi Panjang Caption',
                'description' => "Panjang optimal untuk {$platform}: {$optimal}",
                'current' => $analysis['length'] . ' karakter',
                'impact' => '+15% reach'
            ];
            $priority[] = 'medium';
        }

        // Hashtag optimization
        if (!$analysis['hashtag_optimal']) {
            $suggestions[] = [
                'type' => 'hashtag',
                'title' => 'Optimasi Hashtag',
                'description' => 'Jumlah hashtag belum optimal untuk platform ini',
                'examples' => $this->mlService->getTrendingHashtags($context['industry'] ?? 'general', $context['platform'] ?? 'instagram', 10),
                'impact' => '+12% discoverability'
            ];
            $priority[] = 'medium';
        }

        // Emoji enhancement
        if ($analysis['emoji_count'] < 3) {
            $suggestions[] = [
                'type' => 'emoji',
                'title' => 'Tambahkan Emoji',
                'description' => 'Emoji membuat caption lebih menarik dan mudah dibaca',
                'examples' => ['🔥', '✨', '💯', '👆', '📍'],
                'impact' => '+10% engagement'
            ];
            $priority[] = 'low';
        }

        // Question engagement
        if ($analysis['question_count'] === 0) {
            $suggestions[] = [
                'type' => 'question',
                'title' => 'Tambahkan Pertanyaan',
                'description' => 'Pertanyaan meningkatkan interaksi di komentar',
                'examples' => ['Setuju nggak?', 'Kamu pilih yang mana?', 'Pernah ngalamin juga?'],
                'impact' => '+15% comments'
            ];
            $priority[] = 'medium';
        }

        // Combine suggestions with priority
        $prioritizedSuggestions = [];
        foreach ($suggestions as $index => $suggestion) {
            $suggestion['priority'] = $priority[$index];
            $prioritizedSuggestions[] = $suggestion;
        }

        // Sort by priority
        usort($prioritizedSuggestions, function($a, $b) {
            $priorityOrder = ['high' => 3, 'medium' => 2, 'low' => 1];
            return $priorityOrder[$b['priority']] - $priorityOrder[$a['priority']];
        });

        return $prioritizedSuggestions;
    }

    /**
     * 🧪 Generate A/B testing variant (AI-powered)
     */
    protected function generateABTestingVariant(string $originalCaption, array $context, array $improvements): array
    {
        try {
            // Build AI prompt for A/B variant with specific improvements
            $prompt = "Kamu adalah A/B testing expert untuk social media marketing.\n\n";
            $prompt .= "ORIGINAL CAPTION:\n{$originalCaption}\n\n";
            $prompt .= "PLATFORM: " . $context['platform'] . "\n";
            $prompt .= "INDUSTRY: " . $context['industry'] . "\n\n";
            
            if (!empty($improvements)) {
                $prompt .= "IMPROVEMENT AREAS (prioritas tinggi):\n";
                foreach (array_slice($improvements, 0, 3) as $improvement) {
                    if ($improvement['priority'] === 'high') {
                        $prompt .= "- {$improvement['title']}: {$improvement['description']}\n";
                    }
                }
                $prompt .= "\n";
            }
            
            $prompt .= "Tugas: Buat 1 VARIANT CAPTION yang:\n";
            $prompt .= "1. Tetap mempertahankan pesan inti yang sama\n";
            $prompt .= "2. Menerapkan improvement suggestions di atas\n";
            $prompt .= "3. Menggunakan struktur dan kata-kata yang BERBEDA\n";
            $prompt .= "4. Lebih engaging dan conversion-oriented\n";
            $prompt .= "5. Sesuai untuk platform " . $context['platform'] . "\n";
            $prompt .= "6. Cocok untuk audience Indonesia\n\n";
            $prompt .= "Format: Langsung caption variant tanpa penjelasan tambahan.";

            $variant = $this->geminiService->generateText($prompt, 1000, 0.8);

            return [
                'variant_caption' => trim($variant),
                'test_focus' => $this->determineTestFocus($improvements),
                'success_metrics' => ['engagement_rate', 'click_through_rate', 'conversion_rate'],
                'recommended_duration' => '3-7 hari',
                'sample_size_needed' => 'Minimal 1000 impressions per variant',
                'source' => 'ai_generated'
            ];

        } catch (\Exception $e) {
            Log::error('AI A/B Variant Generation Failed: ' . $e->getMessage());
            
            // Fallback to simple variant
            return [
                'variant_caption' => $this->generateSimpleVariant($originalCaption),
                'test_focus' => $this->determineTestFocus($improvements),
                'success_metrics' => ['engagement_rate'],
                'recommended_duration' => '3-7 hari',
                'sample_size_needed' => 'Minimal 1000 impressions per variant',
                'source' => 'template_based'
            ];
        }
    }

    /**
     * ⏰ Predict best posting time (AI-powered)
     */
    protected function predictBestPostingTime(string $platform, string $industry, ?int $userId): array
    {
        try {
            // Use AI to analyze optimal posting times based on content and industry
            $prompt = "Sebagai social media expert, analisis waktu posting terbaik untuk:\n\n";
            $prompt .= "Platform: {$platform}\n";
            $prompt .= "Industry: {$industry}\n";
            $prompt .= "Timezone: Asia/Jakarta (WIB)\n\n";
            $prompt .= "Berikan rekomendasi waktu posting yang optimal berdasarkan:\n";
            $prompt .= "1. Perilaku audience Indonesia\n";
            $prompt .= "2. Peak hours untuk industry ini\n";
            $prompt .= "3. Algorithm platform\n\n";
            $prompt .= "Format response dalam JSON:\n";
            $prompt .= "{\n";
            $prompt .= '  "best_times_today": ["HH:MM", "HH:MM", "HH:MM"],';
            $prompt .= '  "reasoning": "Penjelasan singkat mengapa waktu ini optimal"';
            $prompt .= "\n}";

            $aiResponse = $this->geminiService->generateText($prompt, 300, 0.7);
            
            // Try to parse AI response
            $aiData = null;
            if (preg_match('/\{.*\}/s', $aiResponse, $matches)) {
                try {
                    $aiData = json_decode($matches[0], true);
                } catch (\Exception $e) {
                    Log::warning('Failed to parse AI posting time response: ' . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::warning('AI posting time prediction failed: ' . $e->getMessage());
            $aiData = null;
        }

        // Get user's historical best times if available
        $userBestTimes = [];
        if ($userId) {
            $userBestTimes = $this->getUserBestPostingTimes($userId, $platform);
        }

        // Platform-specific optimal times (fallback)
        $platformOptimalTimes = [
            'instagram' => [
                'weekdays' => ['11:00', '13:00', '17:00', '19:00'],
                'weekends' => ['10:00', '12:00', '15:00', '20:00']
            ],
            'tiktok' => [
                'weekdays' => ['07:00', '12:00', '19:00', '21:00'],
                'weekends' => ['09:00', '11:00', '16:00', '20:00']
            ],
            'facebook' => [
                'weekdays' => ['09:00', '13:00', '15:00'],
                'weekends' => ['12:00', '14:00', '16:00']
            ]
        ];

        // Industry-specific adjustments
        $industryAdjustments = [
            'food' => ['meal_times' => ['11:00-13:00', '17:00-20:00']],
            'fashion' => ['shopping_hours' => ['10:00-12:00', '15:00-18:00']],
            'fitness' => ['workout_times' => ['06:00-08:00', '17:00-19:00']],
            'education' => ['study_hours' => ['08:00-10:00', '14:00-16:00']],
            'beauty' => ['self_care_hours' => ['19:00-21:00', '10:00-12:00']]
        ];

        $recommendations = [];
        $optimalTimes = $platformOptimalTimes[$platform] ?? $platformOptimalTimes['instagram'];

        // Use AI recommendations if available, otherwise combine user data with platform defaults
        if ($aiData && isset($aiData['best_times_today'])) {
            $recommendations = $aiData['best_times_today'];
            $aiReasoning = $aiData['reasoning'] ?? 'AI-generated optimal times';
        } elseif (!empty($userBestTimes)) {
            $recommendations = array_merge($userBestTimes, $optimalTimes['weekdays']);
        } else {
            $recommendations = $optimalTimes['weekdays'];
        }

        // Ensure we have exactly 3 recommendations
        $recommendations = array_slice(array_unique($recommendations), 0, 3);

        return [
            'best_times_today' => $recommendations,
            'best_days' => ['Selasa', 'Rabu', 'Kamis'], // Historically best for engagement
            'avoid_times' => ['01:00-06:00', '22:00-24:00'],
            'weekend_times' => $optimalTimes['weekends'],
            'industry_specific' => $industryAdjustments[$industry] ?? null,
            'timezone' => 'Asia/Jakarta',
            'confidence' => !empty($userBestTimes) ? 'high' : (isset($aiReasoning) ? 'high' : 'medium'),
            'ai_reasoning' => $aiReasoning ?? null,
            'source' => isset($aiReasoning) ? 'ai_analysis' : (!empty($userBestTimes) ? 'user_data' : 'platform_default')
        ];
    }
    // ===== HELPER METHODS =====

    protected function getHistoricalPerformanceData(string $platform, string $industry, ?int $userId): array
    {
        $query = CaptionAnalytics::where('platform', $platform);
        
        if ($userId) {
            // Get user's historical data first
            $userQuery = clone $query;
            $userData = $userQuery->where('user_id', $userId)
                ->selectRaw('AVG(engagement_rate) as avg_engagement, COUNT(*) as total_posts')
                ->first();
            
            if ($userData && $userData->total_posts >= 5) {
                return [
                    'avg_engagement' => $userData->avg_engagement,
                    'sample_size' => $userData->total_posts,
                    'source' => 'user_data'
                ];
            }
        }
        
        // Fallback to platform averages
        $platformData = $query->selectRaw('AVG(engagement_rate) as avg_engagement, COUNT(*) as total_posts')
            ->first();
        
        return [
            'avg_engagement' => $platformData->avg_engagement ?? 3.5,
            'sample_size' => $platformData->total_posts ?? 0,
            'source' => 'platform_average'
        ];
    }

    protected function detectCallToAction(string $caption): bool
    {
        $ctaPatterns = [
            '/\b(dm|direct message|hubungi|contact|wa|whatsapp)\b/i',
            '/\b(order|pesan|beli|checkout|add to cart)\b/i',
            '/\b(comment|komen|tulis|jawab)\b/i',
            '/\b(like|love|heart|tap)\b/i',
            '/\b(share|bagikan|repost)\b/i',
            '/\b(follow|ikuti|subscribe)\b/i',
            '/\b(save|simpan|bookmark)\b/i',
            '/\b(click|klik|tap|tekan)\b/i',
            '/\b(daftar|register|sign up|join)\b/i'
        ];
        
        foreach ($ctaPatterns as $pattern) {
            if (preg_match($pattern, $caption)) {
                return true;
            }
        }
        
        return false;
    }

    protected function detectHook(string $caption): bool
    {
        $hookPatterns = [
            '/^(tau gak|tahukah|pernah gak|siapa yang|gimana kalau|coba bayangin)/i',
            '/^(jangan sampai|hati-hati|awas|warning|perhatian)/i',
            '/^(rahasia|secret|tips|trick|cara)/i',
            '/^(wow|amazing|incredible|luar biasa|keren banget)/i',
            '/^(stop|berhenti|jangan|hindari)/i',
            '/\?$/' // Ends with question
        ];
        
        $firstSentence = explode('.', $caption)[0];
        
        foreach ($hookPatterns as $pattern) {
            if (preg_match($pattern, $firstSentence)) {
                return true;
            }
        }
        
        return false;
    }

    protected function analyzeSentiment(string $caption): string
    {
        $positiveWords = ['bagus', 'keren', 'amazing', 'luar biasa', 'mantap', 'top', 'best', 'terbaik', 'suka', 'love', 'senang', 'bahagia', 'sukses', 'berhasil'];
        $negativeWords = ['buruk', 'jelek', 'gagal', 'susah', 'sulit', 'mahal', 'rugi', 'kecewa', 'sedih', 'stress', 'capek'];
        
        $caption_lower = strtolower($caption);
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($caption_lower, $word);
        }
        
        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($caption_lower, $word);
        }
        
        if ($positiveCount > $negativeCount) return 'positive';
        if ($negativeCount > $positiveCount) return 'negative';
        return 'neutral';
    }

    protected function calculateReadability(string $caption): float
    {
        $words = str_word_count($caption);
        $sentences = max(1, substr_count($caption, '.') + substr_count($caption, '!') + substr_count($caption, '?'));
        $avgWordsPerSentence = $words / $sentences;
        
        // Simple readability score (lower is better, scale 1-10)
        if ($avgWordsPerSentence <= 10) return 10;
        if ($avgWordsPerSentence <= 15) return 8;
        if ($avgWordsPerSentence <= 20) return 6;
        if ($avgWordsPerSentence <= 25) return 4;
        return 2;
    }

    protected function countUrgencyWords(string $caption): int
    {
        $urgencyWords = ['sekarang', 'hari ini', 'segera', 'cepat', 'terbatas', 'limited', 'flash sale', 'promo', 'diskon', 'sale', 'urgent', 'penting'];
        $count = 0;
        $caption_lower = strtolower($caption);
        
        foreach ($urgencyWords as $word) {
            $count += substr_count($caption_lower, $word);
        }
        
        return $count;
    }

    protected function countPowerWords(string $caption): int
    {
        $powerWords = ['gratis', 'free', 'eksklusif', 'rahasia', 'terbukti', 'guaranteed', 'instant', 'mudah', 'simple', 'powerful', 'amazing', 'incredible'];
        $count = 0;
        $caption_lower = strtolower($caption);
        
        foreach ($powerWords as $word) {
            $count += substr_count($caption_lower, $word);
        }
        
        return $count;
    }

    protected function calculateConfidence(array $analysis, array $historicalData): string
    {
        $confidence = 'medium';
        
        if ($historicalData['sample_size'] >= 20 && $historicalData['source'] === 'user_data') {
            $confidence = 'high';
        } elseif ($historicalData['sample_size'] >= 100) {
            $confidence = 'high';
        } elseif ($historicalData['sample_size'] < 10) {
            $confidence = 'low';
        }
        
        return $confidence;
    }

    protected function determineTestFocus(array $improvements): string
    {
        if (empty($improvements)) return 'Overall Performance';
        
        $highPriorityTypes = array_filter($improvements, function($imp) {
            return $imp['priority'] === 'high';
        });
        
        if (!empty($highPriorityTypes)) {
            $types = array_column($highPriorityTypes, 'type');
            return ucfirst(implode(' & ', array_slice($types, 0, 2)));
        }
        
        return 'Engagement Optimization';
    }

    protected function generateSimpleVariant(string $originalCaption): string
    {
        // Simple fallback variant generation
        $variants = [
            'Tambahkan emoji di awal: 🔥 ' . $originalCaption,
            'Tambahkan pertanyaan: ' . $originalCaption . ' Setuju nggak?',
            'Tambahkan CTA: ' . $originalCaption . ' DM untuk info lebih lanjut!'
        ];
        
        return $variants[array_rand($variants)];
    }

    protected function getUserBestPostingTimes(int $userId, string $platform): array
    {
        // Get user's best performing posts and extract posting times
        $bestPosts = CaptionAnalytics::where('user_id', $userId)
            ->where('platform', $platform)
            ->where('engagement_rate', '>', 5.0) // Above average
            ->orderBy('engagement_rate', 'desc')
            ->limit(10)
            ->get();
        
        $times = [];
        foreach ($bestPosts as $post) {
            $hour = $post->created_at->format('H:00');
            $times[] = $hour;
        }
        
        // Return most frequent times
        $timeCounts = array_count_values($times);
        arsort($timeCounts);
        
        return array_keys(array_slice($timeCounts, 0, 3, true));
    }
}