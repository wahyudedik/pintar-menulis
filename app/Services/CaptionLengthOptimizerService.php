<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class CaptionLengthOptimizerService
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * ✂️ Shorten caption while preserving meaning
     */
    public function shortenCaption(string $caption, int $targetLength, array $options = []): array
    {
        try {
            $preserveHashtags = $options['preserve_hashtags'] ?? true;
            $preserveEmojis = $options['preserve_emojis'] ?? true;
            $preserveCTA = $options['preserve_cta'] ?? true;
            $platform = $options['platform'] ?? 'general';

            $platformContext = $this->getPlatformContext($platform);
            $preservationRules = $this->getPreservationRules($preserveHashtags, $preserveEmojis, $preserveCTA);

            $prompt = "Perpendek caption berikut menjadi maksimal {$targetLength} karakter sambil mempertahankan makna utama dan daya tarik.

Caption asli: \"{$caption}\"
Target length: {$targetLength} karakter
Platform: {$platform}
{$platformContext}

Aturan preservasi:
{$preservationRules}

Berikan response dalam format JSON:
{
  \"original_length\": " . strlen($caption) . ",
  \"target_length\": {$targetLength},
  \"shortened_versions\": [
    {
      \"version\": \"versi pendek 1\",
      \"length\": 120,
      \"strategy\": \"remove redundancy\",
      \"preserved_elements\": [\"hashtags\", \"cta\"],
      \"readability_score\": 8
    },
    {
      \"version\": \"versi pendek 2\",
      \"length\": 115,
      \"strategy\": \"simplify language\",
      \"preserved_elements\": [\"emojis\", \"key_message\"],
      \"readability_score\": 9
    },
    {
      \"version\": \"versi pendek 3\",
      \"length\": 110,
      \"strategy\": \"focus on core message\",
      \"preserved_elements\": [\"main_point\"],
      \"readability_score\": 8
    }
  ],
  \"removed_elements\": [
    \"elemen yang dihapus untuk memperpendek\"
  ],
  \"optimization_tips\": [
    \"tips untuk optimasi lebih lanjut\"
  ],
  \"impact_analysis\": {
    \"message_clarity\": \"maintained|slightly_reduced|significantly_reduced\",
    \"engagement_potential\": \"maintained|improved|reduced\",
    \"cta_effectiveness\": \"maintained|improved|reduced\"
  }
}";

            $response = $this->geminiService->generateText($prompt, 1000, 0.4);
            return $this->parseShortenResponse($response, $caption, $targetLength);

        } catch (Exception $e) {
            Log::error('Caption shortening failed', [
                'error' => $e->getMessage(),
                'original_length' => strlen($caption),
                'target_length' => $targetLength
            ]);

            return $this->getFallbackShortenResponse($caption, $targetLength);
        }
    }

    /**
     * 📈 Expand caption with additional content
     */
    public function expandCaption(string $caption, int $targetLength, array $options = []): array
    {
        try {
            $expansionType = $options['expansion_type'] ?? 'detailed'; // detailed, storytelling, educational
            $addHashtags = $options['add_hashtags'] ?? true;
            $addEmojis = $options['add_emojis'] ?? true;
            $platform = $options['platform'] ?? 'general';
            $industry = $options['industry'] ?? 'general';

            $platformContext = $this->getPlatformContext($platform);
            $expansionStrategy = $this->getExpansionStrategy($expansionType);

            $prompt = "Perluas caption berikut menjadi sekitar {$targetLength} karakter dengan menambahkan konten yang relevan dan menarik.

Caption asli: \"{$caption}\"
Target length: {$targetLength} karakter
Platform: {$platform}
Industry: {$industry}
Expansion type: {$expansionType}

{$platformContext}
{$expansionStrategy}

Berikan response dalam format JSON:
{
  \"original_length\": " . strlen($caption) . ",
  \"target_length\": {$targetLength},
  \"expanded_versions\": [
    {
      \"version\": \"versi expanded 1\",
      \"length\": 280,
      \"expansion_method\": \"add_details\",
      \"added_elements\": [\"background_info\", \"benefits\"],
      \"engagement_score\": 8
    },
    {
      \"version\": \"versi expanded 2\",
      \"length\": 290,
      \"expansion_method\": \"storytelling\",
      \"added_elements\": [\"personal_story\", \"emotional_connection\"],
      \"engagement_score\": 9
    },
    {
      \"version\": \"versi expanded 3\",
      \"length\": 275,
      \"expansion_method\": \"educational\",
      \"added_elements\": [\"tips\", \"how_to\"],
      \"engagement_score\": 8
    }
  ],
  \"added_elements\": [
    \"elemen yang ditambahkan untuk memperpanjang\"
  ],
  \"expansion_suggestions\": [
    \"saran untuk ekspansi lebih lanjut\"
  ],
  \"enhancement_analysis\": {
    \"value_added\": \"high|medium|low\",
    \"engagement_improvement\": \"significant|moderate|minimal\",
    \"information_density\": \"optimal|good|too_dense\"
  }
}";

            $response = $this->geminiService->generateText($prompt, 1200, 0.5);
            return $this->parseExpandResponse($response, $caption, $targetLength);

        } catch (Exception $e) {
            Log::error('Caption expansion failed', [
                'error' => $e->getMessage(),
                'original_length' => strlen($caption),
                'target_length' => $targetLength
            ]);

            return $this->getFallbackExpandResponse($caption, $targetLength);
        }
    }

    /**
     * 📏 Get optimal length suggestions for platform
     */
    public function getOptimalLength(string $platform, string $contentType = 'caption'): array
    {
        $lengthGuides = [
            'instagram' => [
                'caption' => ['min' => 125, 'optimal' => 150, 'max' => 300],
                'story' => ['min' => 50, 'optimal' => 80, 'max' => 120],
                'reels' => ['min' => 100, 'optimal' => 125, 'max' => 200]
            ],
            'tiktok' => [
                'caption' => ['min' => 80, 'optimal' => 100, 'max' => 150],
                'bio' => ['min' => 50, 'optimal' => 80, 'max' => 80]
            ],
            'facebook' => [
                'caption' => ['min' => 200, 'optimal' => 250, 'max' => 400],
                'ad' => ['min' => 90, 'optimal' => 125, 'max' => 125]
            ],
            'twitter' => [
                'tweet' => ['min' => 100, 'optimal' => 200, 'max' => 280],
                'thread' => ['min' => 200, 'optimal' => 250, 'max' => 280]
            ],
            'linkedin' => [
                'post' => ['min' => 300, 'optimal' => 500, 'max' => 700],
                'article' => ['min' => 1000, 'optimal' => 1500, 'max' => 3000]
            ],
            'youtube' => [
                'description' => ['min' => 200, 'optimal' => 300, 'max' => 500],
                'shorts' => ['min' => 100, 'optimal' => 150, 'max' => 200]
            ]
        ];

        $guide = $lengthGuides[$platform][$contentType] ?? $lengthGuides['instagram']['caption'];

        return [
            'platform' => $platform,
            'content_type' => $contentType,
            'length_guide' => $guide,
            'recommendations' => [
                'minimum' => "Minimal {$guide['min']} karakter untuk engagement yang baik",
                'optimal' => "Optimal di {$guide['optimal']} karakter untuk performa terbaik",
                'maximum' => "Maksimal {$guide['max']} karakter sebelum engagement menurun"
            ],
            'tips' => $this->getLengthTips($platform, $contentType)
        ];
    }

    /**
     * 🎯 Smart length adjustment with multiple options
     */
    public function smartAdjustLength(string $caption, string $platform, array $preferences = []): array
    {
        try {
            $optimalGuide = $this->getOptimalLength($platform, 'caption');
            $currentLength = strlen($caption);
            $optimalLength = $optimalGuide['length_guide']['optimal'];

            $adjustmentNeeded = $this->determineAdjustmentType($currentLength, $optimalLength);

            if ($adjustmentNeeded === 'shorten') {
                return $this->shortenCaption($caption, $optimalLength, array_merge($preferences, ['platform' => $platform]));
            } elseif ($adjustmentNeeded === 'expand') {
                return $this->expandCaption($caption, $optimalLength, array_merge($preferences, ['platform' => $platform]));
            } else {
                return [
                    'success' => true,
                    'data' => [
                        'adjustment_needed' => 'none',
                        'current_length' => $currentLength,
                        'optimal_length' => $optimalLength,
                        'message' => 'Caption sudah dalam panjang yang optimal',
                        'suggestions' => [
                            'Caption Anda sudah memiliki panjang yang ideal untuk ' . $platform,
                            'Fokus pada kualitas konten dan engagement'
                        ]
                    ]
                ];
            }

        } catch (Exception $e) {
            Log::error('Smart length adjustment failed', [
                'error' => $e->getMessage(),
                'platform' => $platform
            ]);

            return [
                'success' => false,
                'message' => 'Smart adjustment gagal: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 📊 Analyze caption length impact
     */
    public function analyzeLengthImpact(string $caption, string $platform): array
    {
        $currentLength = strlen($caption);
        $optimalGuide = $this->getOptimalLength($platform, 'caption');
        $guide = $optimalGuide['length_guide'];

        $analysis = [
            'current_length' => $currentLength,
            'optimal_range' => $guide,
            'status' => $this->getLengthStatus($currentLength, $guide),
            'engagement_prediction' => $this->predictEngagementByLength($currentLength, $guide),
            'readability_score' => $this->calculateReadabilityScore($caption),
            'recommendations' => []
        ];

        // Generate recommendations based on analysis
        if ($currentLength < $guide['min']) {
            $analysis['recommendations'][] = "Caption terlalu pendek. Tambahkan " . ($guide['min'] - $currentLength) . " karakter untuk engagement yang lebih baik.";
        } elseif ($currentLength > $guide['max']) {
            $analysis['recommendations'][] = "Caption terlalu panjang. Kurangi " . ($currentLength - $guide['max']) . " karakter untuk menghindari penurunan engagement.";
        } else {
            $analysis['recommendations'][] = "Panjang caption sudah optimal untuk platform " . $platform . ".";
        }

        return $analysis;
    }

    /**
     * 🎯 Get platform-specific context
     */
    private function getPlatformContext(string $platform): string
    {
        $contexts = [
            'instagram' => 'Instagram users prefer engaging, visual-focused captions with emojis and hashtags.',
            'tiktok' => 'TikTok users like short, punchy, trend-focused captions that encourage interaction.',
            'facebook' => 'Facebook allows longer captions and users engage with storytelling content.',
            'twitter' => 'Twitter requires concise, impactful messages due to character limits.',
            'linkedin' => 'LinkedIn users prefer professional, value-driven content with industry insights.',
            'youtube' => 'YouTube descriptions should be informative and include relevant keywords.'
        ];

        return $contexts[$platform] ?? $contexts['instagram'];
    }

    /**
     * 🛡️ Get preservation rules for shortening
     */
    private function getPreservationRules(bool $hashtags, bool $emojis, bool $cta): string
    {
        $rules = [];
        
        if ($hashtags) $rules[] = "- Pertahankan hashtags penting";
        if ($emojis) $rules[] = "- Pertahankan emoji yang relevan";
        if ($cta) $rules[] = "- Pertahankan call-to-action";
        
        $rules[] = "- Pertahankan pesan utama";
        $rules[] = "- Jaga tone dan gaya bahasa";

        return implode("\n", $rules);
    }

    /**
     * 📈 Get expansion strategy
     */
    private function getExpansionStrategy(string $type): string
    {
        $strategies = [
            'detailed' => 'Tambahkan detail, spesifikasi, dan informasi pendukung yang relevan.',
            'storytelling' => 'Kembangkan dengan cerita personal, emosi, dan pengalaman yang relatable.',
            'educational' => 'Tambahkan tips, tutorial, atau informasi edukatif yang bermanfaat.',
            'promotional' => 'Tambahkan benefit, testimonial, dan call-to-action yang kuat.',
            'engaging' => 'Tambahkan pertanyaan, ajakan interaksi, dan elemen yang mendorong engagement.'
        ];

        return $strategies[$type] ?? $strategies['detailed'];
    }

    /**
     * 🔍 Determine adjustment type needed
     */
    private function determineAdjustmentType(int $currentLength, int $optimalLength): string
    {
        $tolerance = 20; // 20 character tolerance

        if ($currentLength < ($optimalLength - $tolerance)) {
            return 'expand';
        } elseif ($currentLength > ($optimalLength + $tolerance)) {
            return 'shorten';
        } else {
            return 'optimal';
        }
    }

    /**
     * 📊 Get length status
     */
    private function getLengthStatus(int $length, array $guide): string
    {
        if ($length < $guide['min']) return 'too_short';
        if ($length > $guide['max']) return 'too_long';
        if ($length >= $guide['optimal'] - 20 && $length <= $guide['optimal'] + 20) return 'optimal';
        return 'acceptable';
    }

    /**
     * 📈 Predict engagement by length
     */
    private function predictEngagementByLength(int $length, array $guide): array
    {
        $optimalLength = $guide['optimal'];
        $distance = abs($length - $optimalLength);
        
        $engagementScore = max(1, 10 - ($distance / 20));
        
        return [
            'score' => round($engagementScore, 1),
            'level' => $engagementScore >= 8 ? 'high' : ($engagementScore >= 6 ? 'medium' : 'low'),
            'explanation' => $this->getEngagementExplanation($engagementScore)
        ];
    }

    /**
     * 📖 Calculate readability score
     */
    private function calculateReadabilityScore(string $text): float
    {
        $sentences = preg_split('/[.!?]+/', $text);
        $words = str_word_count($text);
        $avgWordsPerSentence = $words / max(1, count($sentences) - 1);
        
        // Simple readability calculation
        if ($avgWordsPerSentence <= 15) return 9.0;
        if ($avgWordsPerSentence <= 20) return 7.5;
        if ($avgWordsPerSentence <= 25) return 6.0;
        return 4.5;
    }

    /**
     * 💡 Get length tips for platform
     */
    private function getLengthTips(string $platform, string $contentType): array
    {
        $tips = [
            'instagram' => [
                'Gunakan line breaks untuk readability',
                'Hashtags bisa diletakkan di komentar pertama',
                'Emoji membantu visual appeal tanpa menambah banyak karakter'
            ],
            'tiktok' => [
                'Fokus pada hook di 5 kata pertama',
                'Gunakan trending keywords',
                'Ajukan pertanyaan untuk mendorong komentar'
            ],
            'facebook' => [
                'Cerita personal mendapat engagement tinggi',
                'Gunakan paragraf pendek untuk readability',
                'Tambahkan call-to-action yang jelas'
            ]
        ];

        return $tips[$platform] ?? $tips['instagram'];
    }

    /**
     * 📊 Get engagement explanation
     */
    private function getEngagementExplanation(float $score): string
    {
        if ($score >= 8) return 'Panjang caption optimal untuk engagement maksimal';
        if ($score >= 6) return 'Panjang caption cukup baik, masih bisa dioptimalkan';
        return 'Panjang caption perlu disesuaikan untuk engagement yang lebih baik';
    }

    /**
     * 🔄 Parse shorten response
     */
    private function parseShortenResponse(string $response, string $originalCaption, int $targetLength): array
    {
        try {
            $cleanResponse = $this->cleanJsonResponse($response);
            $data = json_decode($cleanResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response');
            }

            return [
                'success' => true,
                'data' => $data
            ];

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 📈 Parse expand response
     */
    private function parseExpandResponse(string $response, string $originalCaption, int $targetLength): array
    {
        try {
            $cleanResponse = $this->cleanJsonResponse($response);
            $data = json_decode($cleanResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response');
            }

            return [
                'success' => true,
                'data' => $data
            ];

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 🧹 Clean JSON response
     */
    private function cleanJsonResponse(string $response): string
    {
        // Strip all markdown code fences (```json ... ``` or ``` ... ```)
        $response = preg_replace('/```(?:json)?\s*/i', '', $response);
        $response = preg_replace('/```/', '', $response);
        $response = trim($response);

        // Extract JSON object or array
        $start = strpos($response, '{');
        $end = strrpos($response, '}');

        if ($start !== false && $end !== false && $end > $start) {
            return substr($response, $start, $end - $start + 1);
        }

        return $response;
    }

    /**
     * 🔄 Get fallback shorten response
     */
    private function getFallbackShortenResponse(string $caption, int $targetLength): array
    {
        $currentLength = strlen($caption);
        $shortenedCaption = substr($caption, 0, $targetLength - 3) . '...';

        return [
            'success' => true,
            'data' => [
                'original_length' => $currentLength,
                'target_length' => $targetLength,
                'shortened_versions' => [
                    [
                        'version' => $shortenedCaption,
                        'length' => strlen($shortenedCaption),
                        'strategy' => 'simple_truncation',
                        'preserved_elements' => ['main_message'],
                        'readability_score' => 6
                    ]
                ],
                'removed_elements' => ['excess_text'],
                'optimization_tips' => ['Pertimbangkan untuk mempertahankan pesan utama'],
                'impact_analysis' => [
                    'message_clarity' => 'slightly_reduced',
                    'engagement_potential' => 'maintained',
                    'cta_effectiveness' => 'maintained'
                ]
            ]
        ];
    }

    /**
     * 📈 Get fallback expand response
     */
    private function getFallbackExpandResponse(string $caption, int $targetLength): array
    {
        $currentLength = strlen($caption);
        $expandedCaption = $caption . "\n\nApa pendapat Anda tentang ini? Share di komentar! 💬✨";

        return [
            'success' => true,
            'data' => [
                'original_length' => $currentLength,
                'target_length' => $targetLength,
                'expanded_versions' => [
                    [
                        'version' => $expandedCaption,
                        'length' => strlen($expandedCaption),
                        'expansion_method' => 'add_engagement',
                        'added_elements' => ['call_to_action', 'emojis'],
                        'engagement_score' => 7
                    ]
                ],
                'added_elements' => ['engagement_question'],
                'expansion_suggestions' => ['Tambahkan cerita personal atau tips'],
                'enhancement_analysis' => [
                    'value_added' => 'medium',
                    'engagement_improvement' => 'moderate',
                    'information_density' => 'good'
                ]
            ]
        ];
    }
}