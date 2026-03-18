<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Traits\DynamicDateAware;
use App\Models\MLTrainingData;

class GeminiService
{
    use DynamicDateAware;
    protected $apiKey;
    protected $apiUrl;
    protected $validator;
    protected $qualityScorer;
    protected $fallbackManager;
    protected $currentModel;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->validator = new OutputValidator();
        $this->qualityScorer = new QualityScorer();
        $this->fallbackManager = new ModelFallbackManager();
        
        // Get best available model dynamically (now optimized for Tier 1)
        $selectedModel = $this->fallbackManager->getBestAvailableModel();
        $this->currentModel = $selectedModel['name'];
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->currentModel}:generateContent";
        
        // Log tier 1 status
        $tier1Status = $this->fallbackManager->getTier1Status();
        
        Log::info('GeminiService initialized with Tier 1 optimization', [
            'model' => $this->currentModel,
            'tier' => $tier1Status['tier_name'],
            'billing_status' => $tier1Status['billing_status'],
            'rpm_limit' => $selectedModel['rpm'] ?? 'unknown',
            'url' => $this->apiUrl
        ]);
        
        // Log tier 1 benefits if active
        if ($tier1Status['is_tier1']) {
            Log::info('🎉 Tier 1 Benefits Active!', [
                'primary_model_rpm' => $selectedModel['rpm'],
                'total_rpm_capacity' => $tier1Status['total_rpm_limit'],
                'models_available' => $tier1Status['models_available']
            ]);
        }
    }

    /**
     * Generate copywriting content using Gemini AI with quality validation and caching
     */
    public function generateCopywriting(array $params)
    {
        // Validate API key
        if (empty($this->apiKey)) {
            Log::error('Gemini API Key not configured');
            throw new \Exception('API Key tidak dikonfigurasi. Hubungi administrator.');
        }

        // Check cache for similar requests (only for returning users)
        $cacheKey = $this->generateCacheKey($params);
        $isFirstTime = $this->isFirstTimeUser($params['user_id'] ?? null);
        
        if (!$isFirstTime && !($params['skip_cache'] ?? false)) {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                Log::info('Cache hit for copywriting request', ['cache_key' => $cacheKey]);
                return $cached;
            }
        }

                $prompt = DynamicDateService::replaceDatePlaceholders($this->buildPrompt($params));
        
        // Adjust max tokens based on variations request
        // 5 variasi (default): 4096 tokens
        // 20 variasi (premium): 8192 tokens
        $maxTokens = 4096; // default for 5 variasi
        if (isset($params['generate_variations']) && $params['generate_variations']) {
            $maxTokens = 8192; // double for 20 variations
        }

        // Adjust temperature based on user history (more creative for frequent users)
        $temperature = 0.7; // default
        if (isset($params['user_id'])) {
            $recentCount = \App\Models\CaptionHistory::where('user_id', $params['user_id'])
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
            
            // Increase temperature for users who generate frequently
            if ($recentCount > 20) {
                $temperature = 0.9; // More creative
            } elseif ($recentCount > 10) {
                $temperature = 0.8; // Moderately creative
            }
        }

        try {
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => $maxTokens,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
                ]
            ];

            Log::info('Gemini API Request', ['url' => $this->apiUrl, 'prompt_length' => strlen($prompt)]);

            $response = Http::timeout(120) // Increased timeout for better reliability
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $this->apiKey
                ])
                ->post($this->apiUrl, $requestData);

            $statusCode = $response->status();
            Log::info('Gemini API Response Status: ' . $statusCode);

            if ($response->successful()) {
                $data = $response->json();
                
                // Track successful usage
                $this->fallbackManager->trackUsage($this->currentModel);
                
                // Log response structure for debugging
                Log::info('Gemini API Response Structure', [
                    'has_candidates' => isset($data['candidates']),
                    'candidates_count' => isset($data['candidates']) ? count($data['candidates']) : 0,
                    'full_response' => $data
                ]);
                
                // Check if response has candidates
                if (!isset($data['candidates']) || !is_array($data['candidates']) || count($data['candidates']) === 0) {
                    Log::error('Gemini API: No candidates in response', ['response' => $data]);
                    throw new \Exception('AI tidak menghasilkan konten. Silakan coba dengan brief yang berbeda.');
                }
                
                $candidate = $data['candidates'][0];
                
                // Check finish reason
                if (isset($candidate['finishReason'])) {
                    $finishReason = $candidate['finishReason'];
                    Log::info('Gemini Finish Reason: ' . $finishReason);
                    
                    if ($finishReason === 'SAFETY') {
                        throw new \Exception('Konten diblokir oleh filter keamanan. Silakan gunakan brief yang lebih profesional.');
                    } elseif ($finishReason === 'RECITATION') {
                        throw new \Exception('Konten terlalu mirip dengan konten yang ada. Silakan coba brief yang lebih unik.');
                    } elseif ($finishReason !== 'STOP' && $finishReason !== 'MAX_TOKENS') {
                        Log::warning('Unexpected finish reason: ' . $finishReason);
                    }
                }
                
                // Extract text content
                if (isset($candidate['content']['parts']) && is_array($candidate['content']['parts'])) {
                    foreach ($candidate['content']['parts'] as $part) {
                        if (isset($part['text']) && !empty($part['text'])) {
                            $output = trim($part['text']);
                            
                            // Validate output quality
                            $validation = $this->validator->validate($output, $params);
                            
                            // Log validation results
                            Log::info('Output validation', [
                                'score' => $validation['score'],
                                'valid' => $validation['valid'],
                                'warnings' => $validation['warnings'],
                                'errors' => $validation['errors']
                            ]);
                            
                            // Retry if quality too low (max 2 retries)
                            $retryCount = $params['_retry_count'] ?? 0;
                            if ($this->validator->shouldRetry($validation, $retryCount) && $retryCount < 2) {
                                Log::info('Retrying due to low quality', [
                                    'score' => $validation['score'],
                                    'retry' => $retryCount + 1
                                ]);
                                
                                // Add retry count to params
                                $params['_retry_count'] = $retryCount + 1;
                                $params['skip_cache'] = true; // Don't use cache on retry
                                
                                // Recursive retry
                                return $this->generateCopywriting($params);
                            }
                            
                            // Score quality for analytics
                            $qualityScore = $this->qualityScorer->score($output, $params);
                            Log::info('Quality score', [
                                'total_score' => $qualityScore['total_score'],
                                'grade' => $qualityScore['grade'],
                                'breakdown' => $qualityScore['breakdown']
                            ]);
                            
                            // Cache successful result (24 hours)
                            if ($validation['score'] >= 6.0) {
                                Cache::put($cacheKey, $output, now()->addHours(24));
                                Log::info('Cached successful result', ['cache_key' => $cacheKey]);
                            }
                            
                            return $output;
                        }
                    }
                }
                
                // If we reach here, structure is unexpected
                Log::error('Gemini API: Cannot extract text from response', ['candidate' => $candidate]);
                throw new \Exception('Format response tidak valid. Silakan coba lagi.');
            }

            // Handle error responses
            $errorBody = $response->body();
            Log::error('Gemini API Error Response', [
                'status' => $statusCode,
                'body' => $errorBody
            ]);
            
            // Try to parse error message
            try {
                $errorData = $response->json();
                if (isset($errorData['error']['message'])) {
                    $errorMessage = $errorData['error']['message'];
                    
                    // Handle rate limit errors with fallback
                    if (strpos($errorMessage, 'rate limit') !== false || strpos($errorMessage, 'quota') !== false || strpos($errorMessage, 'high demand') !== false || $statusCode === 429) {
                        Log::warning('Rate limit detected, attempting fallback', [
                            'current_model' => $this->currentModel,
                            'error' => $errorMessage
                        ]);
                        
                        // Get fallback model
                        $fallbackModel = $this->fallbackManager->handleRateLimitError($this->currentModel);
                        
                        if ($fallbackModel && $fallbackModel['name'] !== $this->currentModel) {
                            // Retry with fallback model
                            $this->currentModel = $fallbackModel['name'];
                            $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->currentModel}:generateContent";
                            
                            Log::info('Retrying with fallback model', [
                                'model' => $this->currentModel,
                                'url' => $this->apiUrl
                            ]);
                            
                            // Recursive retry with new model
                            $params['_fallback_retry'] = true;
                            return $this->generateCopywriting($params);
                        }
                        
                        throw new \Exception('API sedang mengalami beban tinggi. Silakan tunggu beberapa saat dan coba lagi.');
                    }
                    
                    // Handle specific error cases
                    if (strpos($errorMessage, 'API key') !== false) {
                        throw new \Exception('API Key tidak valid. Silakan generate API key baru di https://aistudio.google.com/app/apikey');
                    } elseif (strpos($errorMessage, 'not found') !== false || strpos($errorMessage, 'not supported') !== false) {
                        throw new \Exception('Model AI tidak tersedia. API key mungkin tidak valid atau expired. Silakan generate API key baru di https://aistudio.google.com/app/apikey');
                    } elseif (strpos($errorMessage, 'quota') !== false || strpos($errorMessage, 'exceeded your current quota') !== false) {
                        // Check if free tier limit
                        if (strpos($errorMessage, 'free_tier') !== false) {
                            throw new \Exception('⚠️ Kuota Free Tier habis (20 requests/hari). Solusi: 1) Setup billing di Google AI Studio untuk upgrade ke Paid Tier (300 RPM), atau 2) Generate API key baru. Kunjungi: https://aistudio.google.com/app/apikey');
                        }
                        throw new \Exception('Kuota API habis. Setup billing di Google AI Studio: https://aistudio.google.com/app/apikey');
                    } elseif (strpos($errorMessage, 'rate limit') !== false) {
                        throw new \Exception('Terlalu banyak request. Silakan tunggu beberapa saat.');
                    }
                    
                    throw new \Exception('API Error: ' . $errorMessage);
                }
            } catch (\JsonException $e) {
                // Response is not JSON
                Log::error('Non-JSON error response: ' . $errorBody);
            }
            
            throw new \Exception('Gagal terhubung ke AI service (Status: ' . $statusCode . '). Silakan cek API key di https://aistudio.google.com/app/apikey');

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini Connection Exception', ['message' => $e->getMessage()]);
            throw new \Exception('Gagal terhubung ke AI service. Periksa koneksi internet Anda.');
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Gemini Request Exception', ['message' => $e->getMessage()]);
            throw new \Exception('Request gagal: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Re-throw our custom exceptions
            if (strpos($e->getMessage(), 'API') !== false || 
                strpos($e->getMessage(), 'konten') !== false ||
                strpos($e->getMessage(), 'Gagal') !== false) {
                throw $e;
            }
            
            // Log unexpected exceptions
            Log::error('Gemini Unexpected Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Terjadi kesalahan tidak terduga. Silakan coba lagi.');
        }
    }
    /**
     * Generate simple text response (for AI Assistant)
     */
    public function generateText(string $prompt, int $maxTokens = 500, float $temperature = 0.7): string
    {
        // Validate API key
        if (empty($this->apiKey)) {
            Log::error('Gemini API Key not configured');
            throw new \Exception('API Key tidak dikonfigurasi. Hubungi administrator.');
        }

        // Process prompt with dynamic dates
        $processedPrompt = DynamicDateService::replaceDatePlaceholders($prompt);

        try {
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $processedPrompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => $maxTokens,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
                ]
            ];

            $response = Http::timeout(90)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $this->apiKey
                ])
                ->post($this->apiUrl, $requestData);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['candidates']) && is_array($data['candidates']) && count($data['candidates']) > 0) {
                    $candidate = $data['candidates'][0];

                    if (isset($candidate['content']['parts']) && is_array($candidate['content']['parts'])) {
                        foreach ($candidate['content']['parts'] as $part) {
                            if (isset($part['text']) && !empty($part['text'])) {
                                return trim($part['text']);
                            }
                        }
                    }
                }

                throw new \Exception('Tidak ada response dari AI');
            }

            // Handle error responses
            $errorBody = $response->body();
            Log::error('Gemini API Error', ['status' => $response->status(), 'body' => $errorBody]);

            throw new \Exception('Gagal terhubung ke AI service');

        } catch (\Exception $e) {
            Log::error('Gemini generateText Error: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Generate text with Google Search Grounding — for features that need real-time web data.
     * Adds "tools": [{"google_search": {}}] to the request so Gemini searches before answering.
     * Returns [text, sources] where sources is an array of cited web URLs.
     */
    public function generateTextWithSearch(string $prompt, int $maxTokens = 2000, float $temperature = 1.0): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('API Key tidak dikonfigurasi.');
        }

        $processedPrompt = DynamicDateService::replaceDatePlaceholders($prompt);

        $requestData = [
            'contents' => [
                ['parts' => [['text' => $processedPrompt]]]
            ],
            'tools' => [
                ['google_search' => (object)[]]
            ],
            'generationConfig' => [
                'temperature'     => $temperature,
                'topK'            => 40,
                'topP'            => 0.95,
                'maxOutputTokens' => $maxTokens,
            ],
        ];

        try {
            $response = Http::timeout(90)
                ->withHeaders([
                    'Content-Type'   => 'application/json',
                    'x-goog-api-key' => $this->apiKey,
                ])
                ->post($this->apiUrl, $requestData);

            if (!$response->successful()) {
                Log::warning('Gemini Search Grounding failed, falling back to plain generateText', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                // Graceful fallback — still return useful result without search
                return ['text' => $this->generateText($prompt, $maxTokens, $temperature), 'sources' => []];
            }

            $data      = $response->json();
            $candidate = $data['candidates'][0] ?? null;

            $text = '';
            foreach (($candidate['content']['parts'] ?? []) as $part) {
                if (!empty($part['text'])) {
                    $text .= $part['text'];
                }
            }

            // Extract cited sources from groundingMetadata
            $sources = [];
            $chunks  = $data['candidates'][0]['groundingMetadata']['groundingChunks'] ?? [];
            foreach ($chunks as $chunk) {
                if (!empty($chunk['web']['uri']) && !empty($chunk['web']['title'])) {
                    $sources[] = [
                        'title' => $chunk['web']['title'],
                        'url'   => $chunk['web']['uri'],
                    ];
                }
            }

            return ['text' => trim($text), 'sources' => $sources];

        } catch (\Exception $e) {
            Log::error('generateTextWithSearch error: ' . $e->getMessage());
            // Fallback to plain text generation
            return ['text' => $this->generateText($prompt, $maxTokens, $temperature), 'sources' => []];
        }
    }

    /**
     * Get few-shot examples from guru-reviewed training data to improve AI output quality.
     * Queries excellent-rated MLTrainingData, filtered by category then platform, cached 1 hour.
     */
    protected function getFewShotExamples(string $category, string $platform, string $subcategory = ''): string
    {
        $cacheKey = 'fewshot_' . md5("{$category}_{$platform}_{$subcategory}");

        return Cache::remember($cacheKey, now()->addHour(), function () use ($category, $platform) {
            $base = MLTrainingData::where('quality_rating', 'excellent')
                ->whereNotNull('corrected_output');

            // Try category + platform match first (caption-sourced training data has platform in metadata)
            $examples = (clone $base)
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.category')) = ?", [$category])
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.platform')) = ?", [$platform])
                ->latest()
                ->limit(3)
                ->get();

            // Fallback 1: category match only
            if ($examples->isEmpty()) {
                $examples = (clone $base)
                    ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.category')) = ?", [$category])
                    ->latest()
                    ->limit(3)
                    ->get();
            }

            // Fallback 2: any excellent examples
            if ($examples->isEmpty()) {
                $examples = $base->latest()->limit(3)->get();
            }

            if ($examples->isEmpty()) {
                return '';
            }

            $block = "📚 CONTOH KONTEN BERKUALITAS TINGGI (dari guru copywriting kami):\n";
            $block .= "Pelajari GAYA dan STRUKTUR contoh berikut, lalu buat konten yang LEBIH BAIK:\n\n";

            foreach ($examples as $i => $example) {
                $num = $i + 1;
                // input_prompt from trainFromCaption() is a metadata string, not a real brief
                $isMetaPrompt = str_starts_with($example->input_prompt, 'Category:');
                $briefLabel = $isMetaPrompt ? 'Konteks' : 'Brief';
                $block .= "--- Contoh {$num} ---\n";
                $block .= "{$briefLabel}: " . substr($example->input_prompt, 0, 150) . "\n";
                $block .= "Konten terbaik:\n" . substr($example->corrected_output, 0, 300) . "\n\n";
            }

            $block .= "---\n";
            $block .= "Gunakan contoh di atas sebagai REFERENSI KUALITAS. Buat konten yang setara atau lebih baik!\n\n";

            return $block;
        });
    }

    /**
     * Build prompt for Gemini based on parameters
     */
    protected function buildPrompt(array $params)
    {
        $category = $params['category'] ?? 'social_media';
        $subcategory = $params['subcategory'] ?? 'caption';
        $brief = $params['brief'] ?? '';
        $tone = $params['tone'] ?? 'casual';
        $platform = $params['platform'] ?? 'instagram';
        $keywords = $params['keywords'] ?? '';
        $generateVariations = $params['generate_variations'] ?? false;
        $autoHashtag = $params['auto_hashtag'] ?? true;
        $localLanguage = $params['local_language'] ?? '';
        $userId = $params['user_id'] ?? null;
        $mode = $params['mode'] ?? 'simple'; // simple or advanced
        
        // SMART VARIATION LOGIC v2.0:
        // 
        // SIMPLE MODE (Pemula/UMKM):
        // - First time: 5 caption GRATIS (wow factor!)
        // - Returning: 1 caption (hemat & simple)
        // 
        // ADVANCED MODE (Pro/Agency):
        // - User pilih: 5, 10, 15, atau 20 caption
        // - Bayar sesuai pilihan
        // - Default (no checkbox): 1 caption
        
        $isFirstTime = false;
        if ($userId) {
            $historyCount = \App\Models\CaptionHistory::where('user_id', $userId)->count();
            $isFirstTime = ($historyCount === 0);
        }
        
        // Determine variation count
        if ($mode === 'simple') {
            // Simple Mode: First time 5, then 1
            $variationCount = $isFirstTime ? 5 : 1;
        } else {
            // Advanced Mode: User choice (5, 10, 15, 20) or default 1
            if ($generateVariations && isset($params['variation_count'])) {
                $variationCount = (int) $params['variation_count']; // 5, 10, 15, or 20
            } else {
                $variationCount = 1; // Default: 1 caption
            }
        }

        // Get user's recent captions to avoid repetition
        $recentCaptions = [];
        $successfulCaptions = [];
        if ($userId) {
            // Get recent captions (last 10)
            $recentHistory = \App\Models\CaptionHistory::getRecentCaptions($userId, 10, $category, $platform);
            $recentCaptions = $recentHistory->pluck('caption_text')->toArray();
            
            // Get successful captions from analytics (for learning style, not copying)
            $successfulAnalytics = \App\Models\CaptionAnalytics::where('user_id', $userId)
                ->where('platform', $platform)
                ->successful()
                ->orderBy('engagement_rate', 'desc')
                ->limit(5)
                ->get();
            $successfulCaptions = $successfulAnalytics->pluck('caption_text')->toArray();
        }

        // Quick Templates - Specialized prompts
        if ($category === 'quick_templates') {
            return $this->buildQuickTemplatePrompt($subcategory, $brief, $tone, $keywords, $platform, $variationCount, $autoHashtag, $localLanguage, $recentCaptions, $successfulCaptions);
        }
        
        // Industry Presets - UMKM Specific
        if ($category === 'industry_presets') {
            return $this->buildIndustryPresetPrompt($subcategory, $brief, $tone, $platform, $keywords, $variationCount, $autoHashtag, $localLanguage, $recentCaptions, $successfulCaptions);
        }

        // Short Drama & Story - Creative writing mode
        if ($category === 'short_drama') {
            return $this->buildShortDramaPrompt($subcategory, $brief, $tone, $keywords, $variationCount);
        }

        // AI Context Awareness: Analyze brief to understand target audience
        $audienceContext = $this->analyzeAudience($brief);
        
        // Auto-adjust tone based on platform if not explicitly set
        $adjustedTone = $this->adjustToneForPlatform($tone, $platform);
        
        // Platform-specific guidelines
        $platformGuidelines = $this->getPlatformGuidelines($platform);

        // General prompt with context awareness
        $prompt = "Kamu adalah copywriter profesional yang ahli dalam membuat konten promosi untuk UMKM Indonesia.\n\n";
        
        // Inject few-shot examples from guru training data
        $prompt .= $this->getFewShotExamples($category, $platform, $subcategory);
        
        // Add CRITICAL instruction to avoid repetition
        if (!empty($recentCaptions)) {
            $prompt .= "⚠️ PENTING - AVOID REPETITION:\n";
            $prompt .= "User ini sudah pernah generate caption sebelumnya. JANGAN BUAT CAPTION YANG MIRIP dengan yang sudah pernah dibuat!\n";
            $prompt .= "Caption yang HARUS DIHINDARI (jangan copy pattern, struktur, atau kata-kata yang sama):\n";
            foreach (array_slice($recentCaptions, 0, 5) as $index => $oldCaption) {
                $prompt .= ($index + 1) . ". " . substr($oldCaption, 0, 100) . "...\n";
            }
            $prompt .= "\nBuat caption yang BENAR-BENAR BERBEDA dari contoh di atas!\n";
            $prompt .= "Gunakan:\n";
            $prompt .= "- Hook yang berbeda (jangan mulai dengan kata/kalimat yang sama)\n";
            $prompt .= "- Struktur kalimat yang berbeda\n";
            $prompt .= "- Angle/sudut pandang yang berbeda\n";
            $prompt .= "- Call-to-action yang berbeda\n";
            $prompt .= "- Emoji yang berbeda (jangan pattern emoji yang sama)\n\n";
        }
        
        // Add successful captions as style reference (not to copy!)
        if (!empty($successfulCaptions)) {
            $prompt .= "📊 REFERENCE - Caption yang Sukses (JANGAN COPY, tapi pelajari style-nya):\n";
            $prompt .= "Caption-caption ini punya engagement tinggi. Pelajari STYLE dan TONE-nya, tapi JANGAN copy struktur atau kata-katanya:\n";
            foreach (array_slice($successfulCaptions, 0, 3) as $index => $successCaption) {
                $prompt .= ($index + 1) . ". " . substr($successCaption, 0, 100) . "...\n";
            }
            $prompt .= "\n";
        }
        
        // Add audience context
        $prompt .= "CONTEXT AWARENESS:\n";
        $prompt .= "Target Audience: {$audienceContext['audience']}\n";
        $prompt .= "Pain Points: {$audienceContext['pain_points']}\n";
        $prompt .= "Desired Action: {$audienceContext['desired_action']}\n\n";
        
        $prompt .= "Tugas: Buatkan {$subcategory} untuk {$platform} dengan tone {$adjustedTone}.\n\n";
        $prompt .= "Brief dari client:\n{$brief}\n\n";
        
        if ($keywords) {
            $prompt .= "Keywords yang harus dimasukkan: {$keywords}\n\n";
        }
        
        // Add variations instruction - SMART LOGIC
        if ($variationCount > 1) {
            // Multiple variations (5 or 20)
            $prompt .= "PENTING: Generate {$variationCount} variasi caption yang berbeda-beda!\n";
            $prompt .= "Format: Nomor urut, lalu caption. Contoh:\n";
            $prompt .= "1. [caption pertama]\n";
            $prompt .= "2. [caption kedua]\n";
            $prompt .= "... dst sampai {$variationCount} variasi\n\n";
        } else {
            // Single best caption (returning user)
            $prompt .= "PENTING: Generate 1 caption TERBAIK yang paling efektif!\n";
            $prompt .= "Fokus pada kualitas, bukan kuantitas. Buat caption yang:\n";
            $prompt .= "- Hook paling menarik\n";
            $prompt .= "- Paling sesuai dengan target audience\n";
            $prompt .= "- Paling likely untuk convert\n";
            $prompt .= "Format: Langsung caption tanpa nomor.\n\n";
        }
        
        // Add hashtag instruction
        if ($autoHashtag) {
            $prompt .= "HASHTAG: Sertakan hashtag trending Indonesia yang relevan di akhir setiap caption.\n\n";
        }
        
        // Add local language instruction with specific examples
        if ($localLanguage) {
            $localLanguageGuide = $this->getLocalLanguageGuide($localLanguage);
            $prompt .= "BAHASA DAERAH ({$localLanguage}):\n";
            $prompt .= $localLanguageGuide . "\n\n";
        }
        
        // Add UMKM-friendly language instruction
        $prompt .= "SAPAAN: Pilih SATU sapaan yang paling sesuai target audience (jangan gabungkan): 'Kak' (umum/netral), 'Bun' (ibu-ibu/parenting), 'Gaes' (anak muda/gen-z), 'Sob' (casual pria), atau tidak pakai sapaan jika formal. Gunakan konsisten di seluruh caption.\n\n";

        $prompt .= "Panduan Umum:\n";
        $prompt .= "1. Gunakan bahasa Indonesia yang menarik dan mudah dipahami\n";
        $prompt .= "2. Buat opening yang menarik perhatian dalam 3 detik pertama\n";
        $prompt .= "3. Fokus pada manfaat produk/jasa untuk customer\n";
        $prompt .= "4. Sertakan call-to-action yang jelas\n";
        $prompt .= "5. Sesuaikan dengan karakteristik target audience\n\n";
        
        // Add platform-specific guidelines
        $prompt .= "Panduan Platform {$platform}:\n";
        $prompt .= $platformGuidelines . "\n\n";

        $prompt .= "Sekarang buatkan copywriting yang menarik dan sesuai dengan target audience:";

        return $prompt;
    }
    
    /**
     * Analyze brief to understand target audience
     */
    protected function analyzeAudience($brief)
    {
        $brief_lower = strtolower($brief);
        
        // Detect audience type
        $audience = 'General audience';
        if (preg_match('/(remaja|anak muda|gen z|milenial|millennial)/i', $brief)) {
            $audience = 'Anak muda (18-30 tahun), aktif di social media, suka konten yang relatable dan trendy';
        } elseif (preg_match('/(ibu|orang tua|keluarga|anak)/i', $brief)) {
            $audience = 'Orang tua/keluarga (30-45 tahun), peduli kualitas dan keamanan, mencari solusi praktis';
        } elseif (preg_match('/(profesional|pekerja|karyawan|kantoran)/i', $brief)) {
            $audience = 'Profesional/pekerja (25-40 tahun), menghargai efisiensi dan kualitas, budget menengah-atas';
        } elseif (preg_match('/(pelajar|mahasiswa|siswa)/i', $brief)) {
            $audience = 'Pelajar/mahasiswa (15-25 tahun), budget terbatas, suka promo dan diskon';
        } elseif (preg_match('/(pengusaha|umkm|bisnis|entrepreneur)/i', $brief)) {
            $audience = 'Pengusaha/pemilik bisnis (25-50 tahun), fokus ROI dan pertumbuhan bisnis';
        }
        
        // Detect pain points
        $pain_points = 'Mencari solusi yang tepat untuk kebutuhan mereka';
        if (preg_match('/(mahal|harga|murah|terjangkau|hemat)/i', $brief)) {
            $pain_points = 'Sensitif terhadap harga, mencari value for money';
        } elseif (preg_match('/(sibuk|waktu|cepat|praktis|mudah)/i', $brief)) {
            $pain_points = 'Keterbatasan waktu, butuh solusi praktis dan cepat';
        } elseif (preg_match('/(kualitas|premium|terbaik|berkualitas)/i', $brief)) {
            $pain_points = 'Mengutamakan kualitas, tidak masalah dengan harga lebih tinggi';
        }
        
        // Detect desired action
        $desired_action = 'Tertarik dan melakukan pembelian';
        if (preg_match('/(follow|subscribe|ikuti)/i', $brief)) {
            $desired_action = 'Follow/subscribe untuk update konten';
        } elseif (preg_match('/(daftar|register|sign up)/i', $brief)) {
            $desired_action = 'Mendaftar atau registrasi';
        } elseif (preg_match('/(beli|order|pesan|checkout)/i', $brief)) {
            $desired_action = 'Melakukan pembelian/order';
        } elseif (preg_match('/(hubungi|contact|wa|whatsapp|dm)/i', $brief)) {
            $desired_action = 'Menghubungi untuk informasi lebih lanjut';
        }
        
        return [
            'audience' => $audience,
            'pain_points' => $pain_points,
            'desired_action' => $desired_action,
        ];
    }
    
    /**
     * Auto-adjust tone based on platform
     */
    protected function adjustToneForPlatform($tone, $platform)
    {
        // Platform-specific tone adjustments
        $platformToneMap = [
            'tiktok' => ['casual' => 'casual', 'formal' => 'casual', 'funny' => 'funny', 'persuasive' => 'casual', 'emotional' => 'emotional', 'educational' => 'casual'],
            'instagram' => ['casual' => 'casual', 'formal' => 'casual', 'funny' => 'funny', 'persuasive' => 'persuasive', 'emotional' => 'emotional', 'educational' => 'casual'],
            'linkedin' => ['casual' => 'formal', 'formal' => 'formal', 'funny' => 'casual', 'persuasive' => 'formal', 'emotional' => 'formal', 'educational' => 'formal'],
            'facebook' => ['casual' => 'casual', 'formal' => 'casual', 'funny' => 'funny', 'persuasive' => 'persuasive', 'emotional' => 'emotional', 'educational' => 'casual'],
            'twitter' => ['casual' => 'casual', 'formal' => 'casual', 'funny' => 'funny', 'persuasive' => 'casual', 'emotional' => 'casual', 'educational' => 'casual'],
        ];
        
        if (isset($platformToneMap[$platform][$tone])) {
            return $platformToneMap[$platform][$tone];
        }
        
        return $tone;
    }
    
    /**
     * Get platform-specific guidelines
     */
    protected function getPlatformGuidelines($platform)
    {
        $guidelines = [
            // Social Media Platforms
            'instagram' => "- Maksimal 150 kata untuk caption\n- Gunakan 8-12 hashtag relevan\n- Sertakan emoji yang sesuai\n- Hook di 3 kalimat pertama\n- Visual-first mindset (caption mendukung visual)\n- Ajak engagement (like, comment, save, share)",
            
            'tiktok' => "- Maksimal 100 kata (sangat singkat)\n- Gunakan bahasa Gen Z yang natural\n- Hook HARUS di 3 detik pertama\n- Sertakan 5-8 hashtag trending\n- CTA untuk like, comment, share\n- Fokus pada entertainment value",
            
            'facebook' => "- Maksimal 200 kata\n- Bisa lebih panjang dari Instagram\n- Opening yang relatable\n- Ajak diskusi di comment\n- 3-5 hashtag (tidak wajib)\n- Cocok untuk storytelling",
            
            'linkedin' => "- Tone profesional dan formal\n- Fokus pada value dan insights\n- Maksimal 300 kata\n- Gunakan data/fakta jika relevan\n- CTA untuk networking atau diskusi\n- Hindari emoji berlebihan",
            
            'twitter' => "- Maksimal 280 karakter (sangat singkat!)\n- Langsung to the point\n- Gunakan thread jika perlu lebih panjang\n- 1-3 hashtag maksimal\n- Cocok untuk announcement atau quick tips",
            
            // Video Platforms
            'youtube' => "- Title: 60-70 karakter, keyword di awal, clickable\n- Description: 5,000 karakter max, keyword rich, timestamps\n- Tags: 10-15 tags relevan\n- Hook di 15 detik pertama (retention crucial)\n- CTA: Subscribe, like, comment, share\n- End screen: Link ke video lain",
            
            'youtube_shorts' => "- Title: Catchy, maksimal 40 karakter\n- Description: Singkat, hashtag #Shorts di awal\n- Hashtag: #Shorts + 3-5 trending\n- Hook: 1 detik pertama CRUCIAL\n- Vertical format: 9:16\n- CTA: Like, subscribe, follow",
            
            // Indonesian Marketplace Platforms
            'shopee' => "- Judul: Maksimal 60 karakter, keyword di awal\n- Deskripsi: Bullet points untuk benefit, spesifikasi lengkap\n- Keywords: 10-15 keywords relevan (bukan hashtag!)\n- Highlight: Gratis ongkir, cashback, voucher, COD\n- CTA: 'Klik Beli Sekarang', 'Masukkan Keranjang'\n- Foto: Minimal 5 foto, white background untuk foto utama",
            
            'tokopedia' => "- Judul: Maksimal 70 karakter, keyword rich\n- Deskripsi: Paragraf pembuka + bullet points\n- Spesifikasi: Lengkap dan detail (wajib diisi)\n- Highlight: Bebas ongkir, cicilan 0%, cashback\n- CTA: 'Beli Sekarang', 'Chat Penjual', 'Tambah Wishlist'\n- Badge: Official Store, Power Merchant Plus (jika ada)",
            
            'bukalapak' => "- Judul: Maksimal 70 karakter, deskriptif\n- Deskripsi: Detail produk, kondisi, garansi\n- Keywords: Gunakan kata kunci pencarian populer\n- Highlight: Gratis ongkir, cicilan, promo\n- CTA: 'Beli Sekarang', 'Nego', 'Chat Penjual'\n- Foto: Minimal 3 foto, tampilkan detail produk",
            
            'lazada' => "- Judul: Maksimal 255 karakter, keyword optimized\n- Deskripsi: HTML format, rich content\n- Highlight: Free shipping, voucher, flash sale\n- Spesifikasi: Lengkap (brand, model, warranty)\n- CTA: 'Buy Now', 'Add to Cart'\n- Foto: High quality, white background",
            
            'blibli' => "- Judul: Jelas dan deskriptif, include brand\n- Deskripsi: Professional, detail spesifikasi\n- Highlight: Cicilan 0%, gratis ongkir, official store\n- Garansi: Jelas mention garansi resmi\n- CTA: 'Beli Sekarang', 'Tambah Keranjang'\n- Foto: Professional, multiple angles",
            
            'tiktok_shop' => "- Judul: Catchy, maksimal 34 karakter\n- Deskripsi: Singkat, fokus benefit, emoji friendly\n- Live Selling: Script untuk live, interactive\n- Flash Sale: Urgency & scarcity (Stok 10! Diskon 50%!)\n- CTA: 'Klik Keranjang Kuning', 'Checkout Sekarang', 'Buruan!'\n- Hashtag: 5-8 trending hashtags",
            
            // Classifieds & Marketplace
            'olx' => "- Judul: Jelas, include lokasi dan kondisi\n- Deskripsi: Detail kondisi, alasan jual, nego/tidak\n- Harga: Cantumkan harga jelas atau 'Nego'\n- Lokasi: Spesifik (kecamatan/kota)\n- CTA: 'Chat untuk nego', 'COD available'\n- Foto: Real photo, multiple angles",
            
            'facebook_marketplace' => "- Judul: Deskriptif, include brand/model\n- Deskripsi: Kondisi, lokasi, cara transaksi\n- Harga: Jelas, mention nego atau fixed\n- Lokasi: Spesifik untuk local pickup\n- CTA: 'Message untuk detail', 'Available for pickup'\n- Foto: Clear, well-lit, multiple angles",
            
            'carousell' => "- Judul: Catchy, include condition (New/Like New/Used)\n- Deskripsi: Honest condition, reason for selling\n- Price: Clear, negotiable or fixed\n- Meetup: Suggest safe meetup locations\n- CTA: 'Chat to offer', 'Make an offer'\n- Photos: Authentic, show any flaws",
            
            // International Marketplaces
            'amazon' => "- Title: 200 characters max, keyword rich, include brand\n- Bullet Points: 5 key features, benefit-focused\n- Description: Detailed, HTML formatted, SEO optimized\n- Keywords: Backend search terms (250 bytes)\n- A+ Content: Enhanced brand content (if eligible)\n- CTA: 'Add to Cart', 'Buy Now'",
            
            'ebay' => "- Title: 80 characters, keyword optimized\n- Description: Detailed, condition, shipping info\n- Item Specifics: Fill all relevant fields\n- Shipping: Clear shipping costs and times\n- Returns: Clear return policy\n- Photos: 12 photos max, show all angles",
            
            'etsy' => "- Title: 140 characters, descriptive and searchable\n- Description: Story behind product, materials, dimensions\n- Tags: 13 tags max, use all, specific to broad\n- Shipping: Clear processing time and shipping options\n- Personalization: Mention if customizable\n- Photos: 10 photos, lifestyle + detail shots",
            
            'alibaba' => "- Title: Professional, include specifications\n- Description: B2B focused, MOQ, lead time, certifications\n- Specifications: Complete technical specs\n- Trade Terms: FOB, CIF, payment terms\n- Certifications: ISO, CE, etc. (if applicable)\n- Photos: Professional, factory shots, certificates",
            
            'aliexpress' => "- Title: Keyword rich, include key features\n- Description: Detailed specs, size chart, shipping info\n- Variations: Clear options (color, size, etc.)\n- Shipping: Free shipping (if possible)\n- Buyer Protection: Mention guarantee\n- Photos: High quality, show variations",
            
            // eCommerce Platforms
            'shopify' => "- Product Title: Clear, SEO-friendly\n- Description: Benefit-focused, storytelling\n- Meta Description: 160 characters for SEO\n- Collections: Organize products logically\n- Variants: Clear options and pricing\n- CTA: 'Add to Cart', 'Buy It Now'",
            
            'walmart' => "- Title: 50-75 characters, keyword optimized\n- Short Description: 1,000 characters, key features\n- Long Description: 4,000 characters, detailed\n- Specifications: Complete all required fields\n- Images: High resolution, white background\n- Pricing: Competitive, clear",
        ];
        
        return $guidelines[$platform] ?? "- Sesuaikan dengan best practice platform\n- Fokus pada engagement\n- CTA yang jelas";
    }
    
    /**
     * Build industry-specific prompts for UMKM
     */
    protected function buildIndustryPresetPrompt($industry, $brief, $tone, $platform, $keywords, $variationCount, $autoHashtag, $localLanguage, $recentCaptions = [], $successfulCaptions = [])
    {
        $audienceContext = $this->analyzeAudience($brief);
        $adjustedTone = $this->adjustToneForPlatform($tone, $platform);
        
        $basePrompt = "Kamu adalah copywriter profesional yang SANGAT PAHAM UMKM Indonesia dan cara jualan yang efektif.\n\n";
        
        // Inject few-shot examples from guru training data
        $basePrompt .= $this->getFewShotExamples('industry_presets', $platform, $industry);
        
        // Add CRITICAL instruction to avoid repetition
        if (!empty($recentCaptions)) {
            $basePrompt .= "⚠️ PENTING - AVOID REPETITION:\n";
            $basePrompt .= "User ini sudah pernah generate caption sebelumnya. JANGAN BUAT CAPTION YANG MIRIP!\n";
            $basePrompt .= "Caption yang HARUS DIHINDARI:\n";
            foreach (array_slice($recentCaptions, 0, 5) as $index => $oldCaption) {
                $basePrompt .= ($index + 1) . ". " . substr($oldCaption, 0, 100) . "...\n";
            }
            $basePrompt .= "\nBuat caption yang BENAR-BENAR BERBEDA! Gunakan hook, struktur, dan angle yang berbeda!\n\n";
        }
        
        // Add successful captions as style reference
        if (!empty($successfulCaptions)) {
            $basePrompt .= "📊 REFERENCE - Caption Sukses (pelajari style-nya, JANGAN copy):\n";
            foreach (array_slice($successfulCaptions, 0, 3) as $index => $successCaption) {
                $basePrompt .= ($index + 1) . ". " . substr($successCaption, 0, 100) . "...\n";
            }
            $basePrompt .= "\n";
        }
        
        $basePrompt .= "CONTEXT:\n";
        $basePrompt .= "Target Audience: {$audienceContext['audience']}\n";
        $basePrompt .= "Pain Points: {$audienceContext['pain_points']}\n";
        $basePrompt .= "Desired Action: {$audienceContext['desired_action']}\n\n";
        $basePrompt .= "Brief: {$brief}\n\n";
        
        if ($keywords) {
            $basePrompt .= "Keywords: {$keywords}\n\n";
        }
        
        // Industry-specific guidelines
        $industryGuide = $this->getIndustryGuidelines($industry);
        $basePrompt .= "PANDUAN KHUSUS INDUSTRI:\n{$industryGuide}\n\n";
        
        // Variations - ALWAYS generate (5 or 20)
        $basePrompt .= "GENERATE {$variationCount} VARIASI caption yang berbeda!\n";
        $basePrompt .= "Format: 1. [caption], 2. [caption], dst\n\n";
        
        // Hashtag
        if ($autoHashtag) {
            $basePrompt .= "HASHTAG: Sertakan hashtag Indonesia yang relevan untuk industri ini.\n\n";
        }
        
        // Local language
        if ($localLanguage) {
            $basePrompt .= "BAHASA DAERAH: Tambahkan 1-2 kata/frasa bahasa {$localLanguage} yang natural.\n\n";
        }
        
        // UMKM language
        $basePrompt .= "SAPAAN: Pilih SATU sapaan yang paling sesuai target audience (jangan gabungkan): 'Kak' (umum/netral), 'Bun' (ibu-ibu/parenting), 'Gaes' (anak muda/gen-z), 'Sob' (casual pria), atau tidak pakai sapaan jika formal. Gunakan konsisten di seluruh caption.\n\n";
        
        $basePrompt .= "Platform: {$platform} | Tone: {$adjustedTone}\n\n";
        $basePrompt .= "Buatkan caption jualan yang FOKUS CLOSING, bukan cuma estetik!";
        
        return $basePrompt;
    }
    
    /**
     * Get industry-specific guidelines
     */
    protected function getIndustryGuidelines($industry)
    {
        $guidelines = [
            'fashion_clothing' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ SIZE CHART: Mention 'Size S, M, L, XL, XXL tersedia' atau 'Chart size di foto'\n" .
                "✅ MATERIAL: Sebutkan bahan (katun, polyester, denim, dll)\n" .
                "✅ CARE INSTRUCTIONS: 'Cuci dengan tangan' atau 'Machine washable'\n" .
                "✅ FIT TYPE: Slim fit, regular fit, oversized, dll\n" .
                "✅ COLOR OPTIONS: Sebutkan warna yang tersedia\n" .
                "✅ MEASUREMENTS: Panjang, lebar, lingkar (jika relevan)\n\n" .
                "Style & Trend:\n" .
                "- Gunakan istilah fashion (ootd, outfit, style, trendy)\n" .
                "- Highlight style: casual, formal, streetwear, vintage\n" .
                "- Mention occasion: daily wear, party, office, dll\n\n" .
                "CTA: 'Order sekarang', 'DM untuk tanya size', 'Klik link bio'\n" .
                "Pain point: Bingung mix & match, cari baju yang pas, takut salah size\n" .
                "Closing: Stok terbatas per warna, diskon hari ini, free ongkir",
            
            'food_beverage' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ HALAL CERTIFICATION: 'Halal & higienis' atau 'Sertifikat halal'\n" .
                "✅ INGREDIENTS: Bahan utama (ayam, sapi, sayur, dll)\n" .
                "✅ PORTION SIZE: 'Porsi untuk 1-2 orang' atau 'Netto 500gr'\n" .
                "✅ SHELF LIFE: 'Tahan 3 hari di kulkas' atau 'Best before date'\n" .
                "✅ ALLERGEN INFO: 'Mengandung kacang' atau 'Gluten-free'\n" .
                "✅ NUTRITION (optional): Kalori, protein, dll\n\n" .
                "Taste & Appeal:\n" .
                "- Gunakan kata sensory (lezat, gurih, segar, renyah, manis)\n" .
                "- Highlight unique selling point: resep rahasia, bahan premium\n" .
                "- Mention serving suggestion: dingin/panas, dengan nasi/roti\n\n" .
                "CTA: 'Pesan sekarang', 'Order via WA', 'Grab/GoFood available'\n" .
                "Pain point: Lapar, bingung mau makan apa, cari yang halal & enak\n" .
                "Closing: Promo hari ini, free ongkir, diskon paket",
            
            'beauty_skincare' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ BPOM NUMBER: 'BPOM NA18xxx' atau 'Terdaftar BPOM'\n" .
                "✅ HALAL CERTIFICATION: 'Halal MUI' atau 'Halal certified'\n" .
                "✅ INGREDIENTS: Key ingredients (niacinamide, vitamin C, hyaluronic acid)\n" .
                "✅ SKIN TYPE: 'Untuk kulit berminyak/kering/sensitif/normal'\n" .
                "✅ USAGE INSTRUCTIONS: 'Pakai pagi & malam' atau 'Apply setelah toner'\n" .
                "✅ VOLUME/SIZE: '30ml', '50gr', dll\n" .
                "✅ EXPIRY DATE: 'Exp 2026' atau 'PAO 12M'\n\n" .
                "Benefits & Results:\n" .
                "- Highlight manfaat: glowing, cerah, halus, lembab, anti-aging\n" .
                "- Mention timeline: 'Hasil terlihat dalam 2 minggu'\n" .
                "- Use before-after mindset: 'Dari kusam jadi glowing'\n" .
                "- Safety first: 'Aman untuk ibu hamil/menyusui' (jika applicable)\n\n" .
                "CTA: 'Konsultasi gratis', 'Cek testimoni', 'Order sekarang'\n" .
                "Pain point: Kulit bermasalah, insecure, takut produk palsu/berbahaya\n" .
                "Closing: Stok terbatas, bonus serum untuk pembelian hari ini, garansi uang kembali",
            
            'printing_service' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ FILE FORMAT: 'Terima file PDF, AI, CDR, PSD, JPG'\n" .
                "✅ PAPER TYPE: Art paper, HVS, ivory, sticker, vinyl, dll\n" .
                "✅ SIZE OPTIONS: A4, A3, A5, custom size\n" .
                "✅ FINISHING: Laminating (doff/glossy), jilid, pond, dll\n" .
                "✅ COLOR: Full color, black & white, spot color\n" .
                "✅ QUANTITY: Minimum order & price per piece\n" .
                "✅ TURNAROUND TIME: 'Express 3 jam', 'Regular 1-2 hari'\n" .
                "✅ RESOLUTION: 'Minimal 300 DPI untuk hasil terbaik'\n\n" .
                "Quality & Speed:\n" .
                "- Highlight: Mesin digital printing terbaru, hasil tajam\n" .
                "- Mention: Free konsultasi desain, free revisi minor\n" .
                "- Service: Antar/jemput file (area tertentu)\n\n" .
                "CTA: 'Konsultasi gratis', 'Minta quotation', 'WA untuk order'\n" .
                "Pain point: Butuh cepat, budget terbatas, takut hasil blur/pudar\n" .
                "Closing: Diskon untuk order hari ini, gratis desain simple, member get discount",
            
            'photography' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ PACKAGE DETAILS: 'Paket A: 4 jam, 100 foto edited, 1 album'\n" .
                "✅ DELIVERABLES: Jumlah foto (raw + edited), video, album, flashdisk\n" .
                "✅ EDITING STYLE: Natural, vintage, moody, bright & airy\n" .
                "✅ TURNAROUND TIME: 'Foto jadi 7-14 hari kerja'\n" .
                "✅ COVERAGE AREA: 'Melayani area Jabodetabek' atau 'Bisa ke luar kota'\n" .
                "✅ EQUIPMENT: 'Full frame camera, professional lighting'\n" .
                "✅ BOOKING TERMS: 'DP 30%, pelunasan H-7', 'Reschedule 1x gratis'\n" .
                "✅ ADDITIONAL SERVICES: Makeup artist, venue, props (jika ada)\n\n" .
                "Portfolio & Style:\n" .
                "- Mention specialty: Wedding, prewedding, maternity, product, dll\n" .
                "- Highlight: 'Portfolio di Instagram @xxx' atau 'Cek highlight'\n" .
                "- Experience: '5 tahun pengalaman', '100+ couple'\n\n" .
                "CTA: 'Book sekarang', 'Cek portfolio', 'DM untuk tanya paket'\n" .
                "Pain point: Cari fotografer yang cocok, budget terbatas, takut hasil mengecewakan\n" .
                "Closing: Slot terbatas (max 2 event per hari), early bird discount 20%, free pre-wedding untuk paket wedding",
            
            'catering' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ MENU LIST: Sebutkan menu yang tersedia (nasi kotak, prasmanan, snack box)\n" .
                "✅ PRICE PER PAX: 'Mulai Rp 25.000/pax' atau 'Paket 50 pax: Rp 1.250.000'\n" .
                "✅ MINIMUM ORDER: 'Min order 20 pax' atau 'Min order Rp 500.000'\n" .
                "✅ INCLUDED SERVICES: Antar, setup, alat makan, bersih-bersih\n" .
                "✅ CUSTOMIZATION: 'Menu bisa request' atau 'Bisa custom sesuai budget'\n" .
                "✅ BOOKING LEAD TIME: 'Order H-3' atau 'Untuk event besar H-7'\n" .
                "✅ COVERAGE AREA: 'Free ongkir radius 5km' atau 'Melayani area Jabodetabek'\n" .
                "✅ HALAL & HYGIENE: 'Halal, higienis, dapur bersih'\n\n" .
                "Taste & Quality:\n" .
                "- Highlight: Bahan fresh, bumbu meresap, porsi pas\n" .
                "- Mention: Spesialisasi (masakan Padang, Chinese, Western)\n" .
                "- Testimonial: 'Dipercaya 100+ event' atau 'Rating 4.9/5'\n\n" .
                "CTA: 'Pesan sekarang', 'Konsultasi menu gratis', 'WA untuk quotation'\n" .
                "Pain point: Ribet masak sendiri, cari yang enak & murah, takut telat/kurang\n" .
                "Closing: Diskon 10% untuk pemesanan minggu ini, free tester menu, paket hemat untuk acara besar",
            
            'tiktok_shop' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ FLASH SALE INFO: 'Flash sale jam 12.00!', 'Diskon 50% hari ini!'\n" .
                "✅ STOCK QUANTITY: 'Stok 100 pcs!', 'Stok terbatas!'\n" .
                "✅ SHIPPING: 'Gratis ongkir min 50rb', 'Bebas ongkir se-Indonesia'\n" .
                "✅ VOUCHER CODE: 'Pakai kode DISKON50', 'Klaim voucher di halaman produk'\n" .
                "✅ CASHBACK: 'Cashback 10%', 'Coins 5000'\n" .
                "✅ BUNDLE DEALS: 'Beli 2 gratis 1', 'Paket hemat 3 pcs'\n" .
                "✅ LIVE SCHEDULE: 'Live jam 8 malam!', 'Nonton live dapat diskon extra'\n\n" .
                "Viral & Trending:\n" .
                "- Gunakan bahasa Gen Z: 'Gak boong!', 'Murah banget!', 'Viral abis!'\n" .
                "- Highlight: Produk viral, best seller, trending\n" .
                "- FOMO trigger: 'Cuma hari ini!', 'Besok naik harga!'\n\n" .
                "CTA: 'Klik keranjang kuning', 'Checkout sekarang', 'Buruan sebelum sold out'\n" .
                "Pain point: Cari barang murah & viral, takut kehabisan, FOMO\n" .
                "Closing: Flash sale berakhir 23:59, stok tinggal sedikit, diskon 70% off",
            
            'shopee_affiliate' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ COMMISSION RATE: 'Komisi 5-15%', 'Komisi hingga Rp 500rb per produk'\n" .
                "✅ PAYMENT TERMS: 'Dibayar setiap tanggal 15', 'Minimal withdraw Rp 50rb'\n" .
                "✅ REQUIREMENTS: 'Tanpa modal', 'Tanpa stok', 'Cukup share link'\n" .
                "✅ EARNING POTENTIAL: 'Potensi income 1-10 juta/bulan'\n" .
                "✅ SUPPORT: 'Training gratis', 'Grup support', 'Materi promosi'\n" .
                "✅ REGISTRATION: 'Daftar gratis', 'Approval 1-3 hari'\n" .
                "✅ TOOLS: 'Dashboard analytics', 'Link tracker', 'Product catalog'\n\n" .
                "Success Story:\n" .
                "- Share testimonial: 'Member kami ada yang income 5 juta/bulan'\n" .
                "- Highlight: Passive income, flexible time, work from home\n" .
                "- Proof: Screenshot earnings, testimonial video\n\n" .
                "CTA: 'Daftar sekarang', 'Mulai jualan', 'Klik link registrasi'\n" .
                "Pain point: Cari side income, modal terbatas, takut ribet\n" .
                "Closing: Bonus Rp 100rb untuk 10 pendaftar pertama, free training, join komunitas 1000+ affiliates",
            
            'home_decor' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ DIMENSIONS: 'Ukuran 30x40cm', 'Tinggi 150cm', 'Diameter 20cm'\n" .
                "✅ MATERIALS: 'Kayu jati', 'Rotan sintetis', 'Kain linen', 'Metal powder coated'\n" .
                "✅ COLOR OPTIONS: 'Tersedia 5 warna: putih, hitam, natural, grey, navy'\n" .
                "✅ WEIGHT CAPACITY: 'Kuat hingga 50kg' (untuk rak/meja)\n" .
                "✅ ASSEMBLY: 'Sudah dirakit' atau 'Knock down (easy assembly)'\n" .
                "✅ CARE INSTRUCTIONS: 'Lap dengan kain lembab', 'Hindari sinar matahari langsung'\n" .
                "✅ ROOM SUITABILITY: 'Cocok untuk ruang tamu/kamar/dapur'\n" .
                "✅ STYLE: 'Minimalis', 'Scandinavian', 'Industrial', 'Bohemian'\n\n" .
                "Aesthetic & Function:\n" .
                "- Highlight: Transformasi ruangan, space-saving, multifungsi\n" .
                "- Describe: Cozy, aesthetic, minimalis, modern, vintage\n" .
                "- Benefit: Bikin ruangan lebih nyaman, rapi, instagrammable\n\n" .
                "CTA: 'Order sekarang', 'DM untuk custom warna/ukuran', 'Cek katalog lengkap'\n" .
                "Pain point: Rumah kurang nyaman, bingung dekor, budget terbatas\n" .
                "Closing: Diskon 20%, free konsultasi desain interior, bisa custom request",
            
            'handmade_craft' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ MATERIALS: 'Bahan kulit asli', 'Benang wol import', 'Kayu mahoni'\n" .
                "✅ DIMENSIONS: Ukuran detail produk\n" .
                "✅ PRODUCTION TIME: 'Ready stock' atau 'Pre-order 7-14 hari'\n" .
                "✅ CUSTOMIZATION: 'Bisa custom nama', 'Pilih warna sendiri', 'Request design'\n" .
                "✅ PACKAGING: 'Free gift box', 'Packaging cantik siap hadiah'\n" .
                "✅ CARE INSTRUCTIONS: Cara merawat produk handmade\n" .
                "✅ UNIQUENESS: 'Setiap produk unik', 'Limited edition', 'Handmade by artisan'\n" .
                "✅ STORY: Cerita di balik produk, proses pembuatan\n\n" .
                "Handmade Value:\n" .
                "- Highlight: Kualitas premium, detail rapi, made with love\n" .
                "- Emotional: Perfect gift, support local artisan, eco-friendly\n" .
                "- Exclusive: Limited stock, tidak mass production\n\n" .
                "CTA: 'Order custom', 'DM untuk request design', 'Cek katalog'\n" .
                "Pain point: Cari hadiah unik & bermakna, support local, bosan produk massal\n" .
                "Closing: Limited stock (hanya buat 10 pcs/bulan), bisa custom, free gift wrapping",
            
            'digital_service' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ SERVICE SCOPE: 'Logo design', 'Website development', 'Social media management'\n" .
                "✅ DELIVERABLES: 'File AI, PNG, JPG', '3 konsep design', 'Source code'\n" .
                "✅ REVISION POLICY: 'Free revisi 3x', 'Unlimited minor revision'\n" .
                "✅ TURNAROUND TIME: 'Selesai 3-5 hari kerja', 'Express 24 jam (+ biaya)'\n" .
                "✅ PRICE RANGE: 'Mulai Rp 500rb', 'Paket lengkap Rp 2 juta'\n" .
                "✅ PAYMENT TERMS: 'DP 50%, pelunasan setelah approve', 'Bisa cicil 2x'\n" .
                "✅ TOOLS/TECH: 'Adobe Illustrator', 'WordPress', 'React JS'\n" .
                "✅ PORTFOLIO: 'Portfolio di Behance/Dribbble' atau 'Cek Instagram @xxx'\n\n" .
                "Professional & Quality:\n" .
                "- Highlight: Pengalaman, sertifikasi, client list\n" .
                "- Showcase: Before-after, case study, testimonial\n" .
                "- Guarantee: Satisfaction guarantee, money back guarantee\n\n" .
                "CTA: 'Konsultasi gratis', 'Minta quotation', 'Order sekarang'\n" .
                "Pain point: Butuh jasa profesional, budget terbatas, takut hasil tidak sesuai\n" .
                "Closing: Diskon 20% untuk order hari ini, free konsultasi 30 menit, bonus minor revision unlimited",
            
            'automotive' => "WAJIB SERTAKAN DETAIL SPESIFIK:\n" .
                "✅ PART NUMBER: 'Part number: 12345-ABC-000'\n" .
                "✅ COMPATIBILITY: 'Untuk Honda Jazz " . \App\Services\DynamicDateService::getAutomotiveYearRange() . "', 'Universal fit'\n" .
                "✅ BRAND: 'Original', 'OEM', 'Aftermarket (merk xxx)'\n" .
                "✅ WARRANTY: 'Garansi 1 tahun', 'Garansi toko 6 bulan'\n" .
                "✅ CONDITION: 'Baru', 'Bekas (kondisi 90%)'\n" .
                "✅ INSTALLATION: 'Free pasang', 'Plug & play', 'Butuh instalasi bengkel'\n" .
                "✅ SPECIFICATIONS: Material, dimensi, kapasitas, dll\n" .
                "✅ CERTIFICATION: 'SNI', 'ISO certified', 'DOT approved'\n\n" .
                "Quality & Safety:\n" .
                "- Highlight: Kualitas OEM/original, tested & proven\n" .
                "- Safety: Aman, sesuai standar, tidak void garansi mobil\n" .
                "- Performance: Improve performa, hemat BBM, lebih awet\n\n" .
                "CTA: 'Order sekarang', 'Konsultasi gratis', 'Cek stok & harga'\n" .
                "Pain point: Cari spare part original, harga terjangkau, takut palsu/KW\n" .
                "Closing: Garansi uang kembali jika palsu, diskon 15%, free pasang (area tertentu), COD available",
        ];
        
        return $guidelines[$industry] ?? "- Fokus pada value proposition\n- Highlight keunggulan produk\n- CTA yang jelas\n- Closing yang kuat";
    }

    /**
     * Build specialized prompts for quick templates
     */
    protected function buildQuickTemplatePrompt($subcategory, $brief, $tone, $keywords, $platform = 'instagram', $variationCount = 5, $autoHashtag = true, $localLanguage = '', $recentCaptions = [], $successfulCaptions = [])
    {
        // AI Context Awareness
        $audienceContext = $this->analyzeAudience($brief);
        $adjustedTone = $this->adjustToneForPlatform($tone, $platform);
        
        $basePrompt = "Kamu adalah copywriter profesional yang ahli dalam membuat konten viral untuk social media.\n\n";
        
        // Inject few-shot examples from guru training data
        $basePrompt .= $this->getFewShotExamples('quick_templates', $platform, $subcategory);
        
        // Add CRITICAL instruction to avoid repetition
        if (!empty($recentCaptions)) {
            $basePrompt .= "⚠️ PENTING - AVOID REPETITION:\n";
            $basePrompt .= "User ini sudah pernah generate caption sebelumnya. JANGAN BUAT CAPTION YANG MIRIP!\n";
            $basePrompt .= "Caption yang HARUS DIHINDARI:\n";
            foreach (array_slice($recentCaptions, 0, 5) as $index => $oldCaption) {
                $basePrompt .= ($index + 1) . ". " . substr($oldCaption, 0, 100) . "...\n";
            }
            $basePrompt .= "\nBuat caption yang BENAR-BENAR BERBEDA! Gunakan hook, struktur, dan angle yang berbeda!\n\n";
        }
        
        // Add successful captions as style reference
        if (!empty($successfulCaptions)) {
            $basePrompt .= "📊 REFERENCE - Caption Sukses (pelajari style-nya, JANGAN copy):\n";
            foreach (array_slice($successfulCaptions, 0, 3) as $index => $successCaption) {
                $basePrompt .= ($index + 1) . ". " . substr($successCaption, 0, 100) . "...\n";
            }
            $basePrompt .= "\n";
        }
        
        // Add audience context
        $basePrompt .= "CONTEXT AWARENESS:\n";
        $basePrompt .= "Target Audience: {$audienceContext['audience']}\n";
        $basePrompt .= "Pain Points: {$audienceContext['pain_points']}\n";
        $basePrompt .= "Desired Action: {$audienceContext['desired_action']}\n\n";
        
        $basePrompt .= "Brief dari client:\n{$brief}\n\n";
        
        if ($keywords) {
            $basePrompt .= "Keywords: {$keywords}\n\n";
        }
        
        $basePrompt .= "PENTING: Sesuaikan konten dengan karakteristik target audience di atas!\n\n";
        
        // Add UMKM language
        $basePrompt .= "SAPAAN: Pilih SATU sapaan yang paling sesuai target audience (jangan gabungkan): 'Kak' (umum/netral), 'Bun' (ibu-ibu/parenting), 'Gaes' (anak muda/gen-z), 'Sob' (casual pria), atau tidak pakai sapaan jika formal. Gunakan konsisten di seluruh caption.\n\n";

        switch ($subcategory) {
            case 'caption_instagram':
                return $basePrompt . "Tugas: Buatkan caption Instagram yang menarik dengan tone {$adjustedTone}.\n\n" .
                    "Format:\n" .
                    "- Opening hook yang kuat (1 kalimat) - HARUS relate dengan target audience\n" .
                    "- Body: cerita/manfaat (3-5 kalimat) - address pain points mereka\n" .
                    "- Call to action yang jelas - sesuai desired action\n" .
                    "- 8-12 hashtag relevan dengan audience\n" .
                    "- Maksimal 150 kata\n" .
                    "- Gunakan emoji yang sesuai dengan audience\n\n" .
                    "Buatkan sekarang:";

            case 'caption_facebook':
                return $basePrompt . "Tugas: Buatkan caption Facebook yang engaging dengan tone {$adjustedTone}.\n\n" .
                    "Format:\n" .
                    "- Opening yang relatable dengan target audience\n" .
                    "- Cerita atau fakta menarik yang address pain points\n" .
                    "- Call to action untuk comment/share\n" .
                    "- 3-5 hashtag\n" .
                    "- Maksimal 200 kata\n\n" .
                    "Buatkan sekarang:";

            case 'caption_tiktok':
                return $basePrompt . "Tugas: Buatkan caption TikTok yang catchy dengan tone {$adjustedTone}.\n\n" .
                    "Format:\n" .
                    "- Hook singkat dan powerful (1 kalimat) - HARUS bikin target audience penasaran\n" .
                    "- Deskripsi video (2-3 kalimat)\n" .
                    "- CTA untuk like/comment/share\n" .
                    "- 5-8 hashtag trending yang relevan dengan audience\n" .
                    "- Maksimal 100 kata\n" .
                    "- Gunakan bahasa yang sesuai dengan target audience\n\n" .
                    "Buatkan sekarang:";

            case 'hook_opening':
                return $basePrompt . "Tugas: Buatkan 5 variasi hook pembuka yang powerful untuk 3 detik pertama video.\n\n" .
                    "Kriteria hook yang baik:\n" .
                    "- Maksimal 10 kata per hook\n" .
                    "- HARUS relate dengan pain points target audience\n" .
                    "- Bikin penasaran/shock/relatable untuk audience spesifik ini\n" .
                    "- Langsung to the point\n" .
                    "- Tone: {$adjustedTone}\n\n" .
                    "Format output:\n" .
                    "Hook 1: [text]\n" .
                    "Hook 2: [text]\n" .
                    "Hook 3: [text]\n" .
                    "Hook 4: [text]\n" .
                    "Hook 5: [text]\n\n" .
                    "Buatkan sekarang:";

            case 'hook_video':
                return $basePrompt . "Tugas: Buatkan hook video ads yang menarik perhatian target audience dalam 3 detik pertama.\n\n" .
                    "Format:\n" .
                    "- Visual suggestion (apa yang terlihat) - sesuai dengan audience\n" .
                    "- Hook text (narasi/text overlay) - address pain points\n" .
                    "- Sound suggestion (musik/sound effect) - cocok untuk audience\n" .
                    "- Tone: {$adjustedTone}\n\n" .
                    "Buatkan 3 variasi hook lengkap:";

            case 'quotes_motivasi':
                return $basePrompt . "Tugas: Buatkan 5 quotes motivasi yang inspiring dan relatable untuk target audience.\n\n" .
                    "Kriteria:\n" .
                    "- Bahasa Indonesia yang powerful\n" .
                    "- Maksimal 20 kata per quote\n" .
                    "- HARUS relate dengan situasi/pain points target audience\n" .
                    "- Bisa untuk caption atau story\n" .
                    "- Tone: {$adjustedTone}\n\n" .
                    "Format:\n" .
                    "1. [quote]\n" .
                    "2. [quote]\n" .
                    "3. [quote]\n" .
                    "4. [quote]\n" .
                    "5. [quote]\n\n" .
                    "Buatkan sekarang:";

            case 'quotes_bisnis':
                return $basePrompt . "Tugas: Buatkan 5 quotes bisnis yang inspiring untuk target audience.\n\n" .
                    "Kriteria:\n" .
                    "- Fokus pada mindset bisnis/hustle yang relate dengan audience\n" .
                    "- Maksimal 25 kata per quote\n" .
                    "- Motivasi untuk action\n" .
                    "- Tone: {$adjustedTone}\n\n" .
                    "Buatkan sekarang:";

            case 'humor_content':
                return $basePrompt . "Tugas: Buatkan konten humor yang relate dengan target audience.\n\n" .
                    "Format:\n" .
                    "- Setup (situasi lucu yang SANGAT relate dengan kehidupan target audience)\n" .
                    "- Punchline\n" .
                    "- Caption untuk post\n" .
                    "- Hashtag relevan\n" .
                    "- Maksimal 100 kata\n\n" .
                    "Catatan: Humor harus sopan, tidak menyinggung, dan SANGAT relate dengan brief dan target audience.\n\n" .
                    "Buatkan sekarang:";

            case 'viral_content':
                return $basePrompt . "Tugas: Buatkan konten yang berpotensi viral untuk target audience dengan tone {$adjustedTone}.\n\n" .
                    "Elemen viral content:\n" .
                    "- Hook yang bikin target audience penasaran\n" .
                    "- Konten yang relatable/shocking/inspiring untuk audience spesifik\n" .
                    "- CTA untuk share/tag teman\n" .
                    "- Hashtag trending yang relevan\n\n" .
                    "Format:\n" .
                    "- Opening hook (address pain points)\n" .
                    "- Body content (cerita/fakta/tips yang relate)\n" .
                    "- CTA viral\n" .
                    "- Hashtag strategy\n\n" .
                    "Buatkan sekarang:";

            case 'storytelling_short':
                return $basePrompt . "Tugas: Buatkan storytelling pendek yang engaging untuk target audience dengan tone {$adjustedTone}.\n\n" .
                    "Struktur:\n" .
                    "- Opening: hook yang menarik dan relate dengan audience\n" .
                    "- Conflict: masalah/tantangan yang familiar bagi audience\n" .
                    "- Resolution: solusi/hasil\n" .
                    "- Lesson: takeaway yang valuable untuk audience\n" .
                    "- CTA\n" .
                    "- Maksimal 200 kata\n\n" .
                    "Buatkan sekarang:";

            case 'cta_powerful':
                return $basePrompt . "Tugas: Buatkan 10 variasi Call To Action yang powerful untuk target audience.\n\n" .
                    "Kriteria:\n" .
                    "- Action-oriented\n" .
                    "- Menciptakan urgency\n" .
                    "- Jelas dan spesifik\n" .
                    "- Sesuai dengan desired action target audience\n" .
                    "- Tone: {$adjustedTone}\n\n" .
                    "Format:\n" .
                    "1. [CTA]\n" .
                    "2. [CTA]\n" .
                    "...\n" .
                    "10. [CTA]\n\n" .
                    "Buatkan sekarang:";

            case 'headline_catchy':
                return $basePrompt . "Tugas: Buatkan 8 variasi headline yang catchy dan attention-grabbing untuk target audience.\n\n" .
                    "Kriteria:\n" .
                    "- Maksimal 10 kata per headline\n" .
                    "- Bikin target audience penasaran\n" .
                    "- Highlight benefit/value yang penting bagi audience\n" .
                    "- Address pain points mereka\n" .
                    "- Tone: {$adjustedTone}\n\n" .
                    "Format:\n" .
                    "1. [headline]\n" .
                    "2. [headline]\n" .
                    "...\n" .
                    "8. [headline]\n\n" .
                    "Buatkan sekarang:";

            default:
                // Use TemplatePrompts for all other subcategories
                return \App\Services\TemplatePrompts::getPrompt($subcategory, $basePrompt, $adjustedTone, $audienceContext);
        }
    }

    /**
     * Train ML model with guru's feedback
     */
    public function trainModel(array $trainingData)
    {
        // TODO: Implement ML training logic
        // This will store training data for future model improvement
        
        return [
            'success' => true,
            'message' => 'Training data berhasil disimpan'
        ];
    }

    /**
     * Get model performance metrics
     */
    public function getModelMetrics()
    {
        // TODO: Implement metrics retrieval
        
        return [
            'accuracy' => 0.85,
            'total_trainings' => 0,
            'last_trained' => null
        ];
    }
    
    /**
     * Get local language guide with specific examples
     */
    protected function getLocalLanguageGuide($language)
    {
        $guides = [
            'jawa' => "Gunakan bahasa Jawa yang NATURAL dan MUDAH DIPAHAMI. WAJIB pakai minimal 2-3 kata/frasa Jawa!\n" .
                     "Contoh kata yang HARUS dipakai:\n" .
                     "- 'Monggo' (silakan/ayo) - contoh: 'Monggo langsung order!'\n" .
                     "- 'Murah meriah' atau 'Murah pol' (sangat murah)\n" .
                     "- 'Enak tenan' (enak sekali)\n" .
                     "- 'Apik' (bagus/cantik)\n" .
                     "- 'Mantul' atau 'Mantap jiwa'\n" .
                     "- 'Ojo nganti' (jangan sampai) - contoh: 'Ojo nganti kehabisan!'\n" .
                     "- 'Lho kok' (ekspresi heran)\n" .
                     "- 'Piye' (bagaimana)\n" .
                     "- 'Rek' (teman/bro) - contoh: 'Rek, murah banget iki!'\n\n" .
                     "CONTOH CAPTION YANG BENAR:\n" .
                     "'Monggo Kak! Produk apik, harga murah pol! Enak tenan kualitasnya. Ojo nganti kehabisan ya! 🔥'\n\n" .
                     "JANGAN cuma pakai bahasa Indonesia biasa! WAJIB ada kata Jawa!",
            
            'sunda' => "Gunakan bahasa Sunda yang NATURAL dan MUDAH DIPAHAMI. WAJIB pakai minimal 2-3 kata/frasa Sunda!\n" .
                      "Contoh kata yang HARUS dipakai:\n" .
                      "- 'Mangga' (silakan/ayo) - contoh: 'Mangga langsung order!'\n" .
                      "- 'Murah pisan' (murah sekali)\n" .
                      "- 'Nuhun' (terima kasih)\n" .
                      "- 'Alus' (bagus/halus)\n" .
                      "- 'Saé' (bagus)\n" .
                      "- 'Teu meunang' (jangan sampai) - contoh: 'Teu meunang telat!'\n" .
                      "- 'Kumaha' (bagaimana)\n" .
                      "- 'Atuh' (dong/lah) - contoh: 'Order atuh!'\n" .
                      "- 'Euy' (ekspresi)\n\n" .
                      "CONTOH CAPTION YANG BENAR:\n" .
                      "'Mangga Kak! Produk saé pisan, murah pisan! Teu meunang telat order atuh! 🔥'\n\n" .
                      "JANGAN cuma pakai bahasa Indonesia biasa! WAJIB ada kata Sunda!",
            
            'betawi' => "Gunakan bahasa Betawi yang NATURAL dan MUDAH DIPAHAMI. WAJIB pakai minimal 2-3 kata/frasa Betawi!\n" .
                       "Contoh kata yang HARUS dipakai:\n" .
                       "- 'Aye/Gue' (saya)\n" .
                       "- 'Elu/Lu' (kamu)\n" .
                       "- 'Kagak/Kaga' (tidak)\n" .
                       "- 'Nih ye' (nih)\n" .
                       "- 'Mana tau' (mana tahu)\n" .
                       "- 'Kece badai' (keren sekali)\n" .
                       "- 'Mantep jiwa' (mantap)\n" .
                       "- 'Jangan sampe' (jangan sampai)\n" .
                       "- 'Bro/Bray' (bro)\n\n" .
                       "CONTOH CAPTION YANG BENAR:\n" .
                       "'Bro, nih ye produk kece badai! Harga kagak mahal, kualitas mantep jiwa! Jangan sampe kehabisan! 🔥'\n\n" .
                       "JANGAN cuma pakai bahasa Indonesia biasa! WAJIB ada kata Betawi!",
            
            'minang' => "Gunakan bahasa Minang yang NATURAL dan MUDAH DIPAHAMI. WAJIB pakai minimal 2-3 kata/frasa Minang!\n" .
                       "Contoh kata yang HARUS dipakai:\n" .
                       "- 'Lah' (sudah) - contoh: 'Lah murah bana!'\n" .
                       "- 'Bana' (benar/sekali) - contoh: 'Murah bana!'\n" .
                       "- 'Ndak' (tidak)\n" .
                       "- 'Alun' (belum)\n" .
                       "- 'Beko' (nanti)\n" .
                       "- 'Ciek' (satu)\n" .
                       "- 'Rancak' (bagus/cantik)\n" .
                       "- 'Lamak' (enak)\n" .
                       "- 'Uni/Uda' (kakak)\n\n" .
                       "CONTOH CAPTION YANG BENAR:\n" .
                       "'Uni, lah murah bana produk rancak ko! Kualitas lamak, harga ndak mahal! Order ciek dulu! 🔥'\n\n" .
                       "JANGAN cuma pakai bahasa Indonesia biasa! WAJIB ada kata Minang!",
            
            'bali' => "Gunakan bahasa Bali yang NATURAL dan MUDAH DIPAHAMI. WAJIB pakai minimal 2-3 kata/frasa Bali!\n" .
                     "Contoh kata yang HARUS dipakai:\n" .
                     "- 'Om Swastiastu' (salam Bali)\n" .
                     "- 'Kenken' (bagaimana)\n" .
                     "- 'Sing' (tidak/jangan)\n" .
                     "- 'Niki' (ini)\n" .
                     "- 'Napi' (apa)\n" .
                     "- 'Becik' (bagus)\n" .
                     "- 'Murah pisan' (murah sekali)\n" .
                     "- 'Ajeg' (tetap/konsisten)\n" .
                     "- 'Suksma' (terima kasih)\n\n" .
                     "CONTOH CAPTION YANG BENAR:\n" .
                     "'Om Swastiastu! Niki produk becik, murah pisan! Kenken sing order? Ajeg berkualitas! 🔥'\n\n" .
                     "JANGAN cuma pakai bahasa Indonesia biasa! WAJIB ada kata Bali!",
            
            'batak' => "Gunakan bahasa Batak yang NATURAL dan MUDAH DIPAHAMI. WAJIB pakai minimal 2-3 kata/frasa Batak!\n" .
                      "Contoh kata yang HARUS dipakai:\n" .
                      "- 'Horas' (salam/sehat) - contoh: 'Horas Lae!'\n" .
                      "- 'Lae/Ito' (bro/saudara laki-laki)\n" .
                      "- 'Eda' (jangan) - contoh: 'Eda telat!'\n" .
                      "- 'Sai' (sangat) - contoh: 'Murah sai!'\n" .
                      "- 'Tung mansai' (sangat bagus/mantap)\n" .
                      "- 'Hatop' (bagus/keren)\n" .
                      "- 'Boasa' (bagaimana)\n" .
                      "- 'Nunga' (sudah)\n" .
                      "- 'Dang' (belum)\n\n" .
                      "CONTOH CAPTION YANG BENAR:\n" .
                      "'Horas Lae! Produk hatop, harga murah sai! Kualitas tung mansai! Eda telat order! 🔥'\n\n" .
                      "JANGAN cuma pakai bahasa Indonesia biasa! WAJIB ada kata Batak seperti 'Horas', 'Lae', 'tung mansai', 'hatop', 'sai', 'eda'!",
            
            'madura' => "Gunakan bahasa Madura yang NATURAL dan MUDAH DIPAHAMI. WAJIB pakai minimal 2-3 kata/frasa Madura!\n" .
                       "Contoh kata yang HARUS dipakai:\n" .
                       "- 'Salamat' (selamat/salam)\n" .
                       "- 'Kanca' (teman/bro)\n" .
                       "- 'Apah' (apa)\n" .
                       "- 'Bagus pisan' (bagus sekali)\n" .
                       "- 'Murah banget' (murah sekali)\n" .
                       "- 'Tadak' (tidak)\n" .
                       "- 'Enggi' (iya)\n" .
                       "- 'Paran' (bagaimana)\n" .
                       "- 'Sampean' (kamu/anda)\n" .
                       "- 'Kaulah' (kamu)\n\n" .
                       "CONTOH CAPTION YANG BENAR:\n" .
                       "'Salamat Kanca! Produk bagus pisan, murah banget! Paran sampean tadak order? Enggi mantap! 🔥'\n\n" .
                       "JANGAN cuma pakai bahasa Indonesia biasa! WAJIB ada kata Madura!",
            
            'bugis' => "Gunakan bahasa Bugis yang NATURAL dan MUDAH DIPAHAMI. WAJIB pakai minimal 2-3 kata/frasa Bugis!\n" .
                      "Contoh kata yang HARUS dipakai:\n" .
                      "- 'Assalamu Alaikum' (salam)\n" .
                      "- 'Daeng/Puang' (panggilan hormat)\n" .
                      "- 'Aga' (bagaimana)\n" .
                      "- 'Dekka' (bagus)\n" .
                      "- 'Murah pole' (murah sekali)\n" .
                      "- 'Tena' (tidak ada)\n" .
                      "- 'Engka' (ada)\n" .
                      "- 'Siaga' (bagaimana)\n" .
                      "- 'Makanja' (makanya)\n" .
                      "- 'Pole' (sekali/banget)\n\n" .
                      "CONTOH CAPTION YANG BENAR:\n" .
                      "'Daeng, produk dekka pole! Murah pole, kualitas tena tandingannya! Siaga tena order? 🔥'\n\n" .
                      "JANGAN cuma pakai bahasa Indonesia biasa! WAJIB ada kata Bugis!",
            
            'banjar' => "Gunakan bahasa Banjar yang NATURAL dan MUDAH DIPAHAMI. WAJIB pakai minimal 2-3 kata/frasa Banjar!\n" .
                       "Contoh kata yang HARUS dipakai:\n" .
                       "- 'Assalamu Alaikum' (salam)\n" .
                       "- 'Pian' (kamu/anda)\n" .
                       "- 'Apa kabar' (apa kabar)\n" .
                       "- 'Bagus banar' (bagus sekali)\n" .
                       "- 'Murah banar' (murah sekali)\n" .
                       "- 'Kada' (tidak)\n" .
                       "- 'Handak' (mau/ingin)\n" .
                       "- 'Banar' (benar/sekali)\n" .
                       "- 'Lawan' (dengan)\n" .
                       "- 'Kaya apa' (bagaimana)\n\n" .
                       "CONTOH CAPTION YANG BENAR:\n" .
                       "'Assalamu Alaikum! Pian handak produk bagus banar? Murah banar, kaya apa kada order? 🔥'\n\n" .
                       "JANGAN cuma pakai bahasa Indonesia biasa! WAJIB ada kata Banjar!",
            
            'mixed' => "Gunakan MIX BAHASA DAERAH yang NATURAL! Campurkan 2-3 bahasa daerah berbeda dalam 1 caption!\n" .
                      "WAJIB pakai kata dari minimal 2 bahasa daerah yang berbeda:\n\n" .
                      "PILIHAN KATA CAMPURAN:\n" .
                      "- Jawa: 'Monggo', 'Apik', 'Murah pol', 'Enak tenan', 'Ojo nganti'\n" .
                      "- Sunda: 'Mangga', 'Saé', 'Murah pisan', 'Atuh', 'Euy'\n" .
                      "- Betawi: 'Nih ye', 'Kece badai', 'Kagak', 'Mantep jiwa'\n" .
                      "- Minang: 'Lah', 'Bana', 'Rancak', 'Lamak', 'Uni/Uda'\n" .
                      "- Batak: 'Horas', 'Lae', 'Hatop', 'Sai', 'Tung mansai'\n" .
                      "- Bali: 'Kenken', 'Becik', 'Niki', 'Ajeg'\n" .
                      "- Madura: 'Kanca', 'Bagus pisan', 'Paran', 'Enggi'\n" .
                      "- Bugis: 'Daeng', 'Dekka', 'Pole', 'Siaga'\n" .
                      "- Banjar: 'Pian', 'Banar', 'Handak', 'Kada'\n\n" .
                      "CONTOH CAPTION MIX YANG BENAR:\n" .
                      "'Horas Lae! Monggo langsung order produk saé ini! Murah pol, kualitas tung mansai! Ojo nganti kehabisan atuh! 🔥'\n" .
                      "(Mix: Batak + Jawa + Sunda)\n\n" .
                      "ATAU:\n" .
                      "'Mangga Kanca! Niki produk kece badai, murah banar! Paran kagak order? Lamak pisan! 🔥'\n" .
                      "(Mix: Sunda + Madura + Bali + Betawi + Banjar + Minang)\n\n" .
                      "WAJIB: Campurkan minimal 2 bahasa daerah berbeda! Jangan cuma 1 bahasa!",
        ];
        
        return $guides[$language] ?? "Tambahkan 1-2 kata/frasa bahasa {$language} yang natural untuk relate dengan audience lokal.";
    }

    /**
     * Generate cache key for request
     */
    protected function generateCacheKey(array $params): string
    {
        // Create unique cache key based on important params
        $keyData = [
            'category' => $params['category'] ?? '',
            'subcategory' => $params['subcategory'] ?? '',
            'brief' => substr($params['brief'] ?? '', 0, 150), // First 150 chars
            'tone' => $params['tone'] ?? '',
            'platform' => $params['platform'] ?? '',
            'keywords' => $params['keywords'] ?? '',
            'local_language' => $params['local_language'] ?? '',
        ];
        
        $key = md5(json_encode($keyData));
        return "copywriting:v2:{$key}";
    }
    
    /**
     * Check if user is first time
     */
    protected function isFirstTimeUser($userId): bool
    {
        if (!$userId) {
            return true;
        }
        
        $historyCount = \App\Models\CaptionHistory::where('user_id', $userId)->count();
        return $historyCount === 0;
    }

    /**
     * Get current model being used
     */
    public function getCurrentModel(): string
    {
        return $this->currentModel;
    }
    
    /**
     * Get model usage statistics
     */
    public function getModelUsageStats(): array
    {
        return $this->fallbackManager->getUsageStats();
    }
    
    /**
     * Force switch to specific model
     */
    public function switchModel(string $modelName): bool
    {
        $model = $this->fallbackManager->getModelByName($modelName);
        
        if ($model) {
            $this->currentModel = $model['name'];
            $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->currentModel}:generateContent";
            
            Log::info('Manually switched model', [
                'model' => $this->currentModel,
                'url' => $this->apiUrl
            ]);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Reset model usage stats (for testing)
     */
    public function resetModelStats(): void
    {
        $this->fallbackManager->resetUsageStats();
    }

    /**
     * Generate image caption using Gemini Vision API
     */
    public function generateImageCaption(array $params)
    {
        $startTime = microtime(true);

        // Validate required params
        if (empty($params['image_data']) || empty($params['mime_type'])) {
            throw new \Exception('Image data and mime type are required');
        }

        // Check cache (using image hash as key)
        $imageHash = md5($params['image_data']);
        $cacheKey = "image_caption_{$imageHash}_" . md5(json_encode([
            'business_type' => $params['business_type'] ?? '',
            'product_name' => $params['product_name'] ?? '',
        ]));

        $cached = Cache::get($cacheKey);
        if ($cached) {
            Log::info('Cache hit for image caption', ['cache_key' => $cacheKey]);
            return $cached;
        }

                $prompt = DynamicDateService::replaceDatePlaceholders($this->buildImageCaptionPrompt($params));
        $maxRetries = 2;
        $attempt = 0;
        $bestResult = null;
        $bestScore = 0;

        while ($attempt < $maxRetries) {
            $attempt++;

            try {
                $requestData = [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => $params['mime_type'],
                                        'data' => $params['image_data']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 2048,
                        'responseMimeType' => 'application/json',
                    ],
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ]
                ];

                Log::info('Calling Gemini Vision API', [
                    'model' => $this->currentModel,
                    'user_id' => $params['user_id'] ?? null,
                    'attempt' => $attempt
                ]);

                $response = Http::timeout(120) // Increased timeout for image processing
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($this->apiUrl . '?key=' . $this->apiKey, $requestData);

                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['error']['message'] ?? 'Unknown error';
                    Log::error('Gemini Vision API Error', [
                        'status' => $response->status(),
                        'error' => $errorMessage
                    ]);
                    throw new \Exception('API Error: ' . $errorMessage);
                }

                $result = $response->json();
                $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

                // Parse JSON response
                $parsed = $this->parseImageCaptionResponse($aiResponse);

                // Calculate quality score
                $qualityScore = $this->calculateImageCaptionQuality($parsed);

                // Keep best result
                if ($qualityScore > $bestScore) {
                    $bestScore = $qualityScore;
                    $bestResult = $parsed;
                }

                // If quality is good enough, stop retrying
                if ($qualityScore >= 0.7) {
                    Log::info('Image caption quality acceptable', [
                        'score' => $qualityScore,
                        'attempt' => $attempt
                    ]);
                    break;
                }

                // If quality is low and we have retries left, try again
                if ($attempt < $maxRetries) {
                    Log::warning('Image caption quality low, retrying', [
                        'score' => $qualityScore,
                        'attempt' => $attempt
                    ]);
                    sleep(1); // Brief pause before retry
                }

            } catch (\Exception $e) {
                Log::error('Image caption generation attempt failed', [
                    'error' => $e->getMessage(),
                    'model' => $this->currentModel,
                    'attempt' => $attempt
                ]);
                
                if ($attempt >= $maxRetries) {
                    throw $e;
                }
            }
        }

        if (!$bestResult) {
            throw new \Exception('Failed to generate image caption after retries');
        }

        $generationTime = microtime(true) - $startTime;

        $finalResult = array_merge($bestResult, [
            'model_used' => $this->currentModel,
            'generation_time' => round($generationTime, 2),
            'quality_score' => $bestScore,
        ]);

        // Cache result for 24 hours
        Cache::put($cacheKey, $finalResult, 86400);

        return $finalResult;
    }

    /**
     * 🔍 Analyze image with Gemini Vision - Advanced Analysis
     */
    public function analyzeImageWithVision(array $params)
    {
        $startTime = microtime(true);

        // Validate required params
        if (empty($params['image_data']) || empty($params['mime_type'])) {
            throw new \Exception('Image data and mime type are required');
        }

        if (empty($params['analysis_options'])) {
            throw new \Exception('Analysis options are required');
        }

        // Check cache (using image hash + options as key)
        $imageHash = md5($params['image_data']);
        $optionsHash = md5(json_encode($params['analysis_options']));
        $cacheKey = "image_analysis_{$imageHash}_{$optionsHash}";

        $cached = Cache::get($cacheKey);
        if ($cached) {
            Log::info('Cache hit for image analysis', ['cache_key' => $cacheKey]);
            return $cached;
        }

        $prompt = DynamicDateService::replaceDatePlaceholders($this->buildImageAnalysisPrompt($params));
        $maxRetries = 2;
        $attempt = 0;
        $bestResult = null;
        $bestScore = 0;

        while ($attempt < $maxRetries) {
            $attempt++;

            try {
                $requestData = [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => $params['mime_type'],
                                        'data' => $params['image_data']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.8,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 4096, // Increased for detailed analysis
                        'responseMimeType' => 'text/plain',
                    ],
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ]
                ];

                Log::info('Calling Gemini Vision API for Analysis', [
                    'model' => $this->currentModel,
                    'user_id' => $params['user_id'] ?? null,
                    'options' => $params['analysis_options'],
                    'attempt' => $attempt
                ]);

                $response = Http::timeout(180) // Extended timeout for detailed analysis
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($this->apiUrl . '?key=' . $this->apiKey, $requestData);

                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['error']['message'] ?? 'Unknown error';
                    Log::error('Gemini Vision Analysis API Error', [
                        'status' => $response->status(),
                        'error' => $errorMessage
                    ]);
                    throw new \Exception('API Error: ' . $errorMessage);
                }

                $result = $response->json();
                $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

                // Format and enhance the analysis
                $formattedAnalysis = $this->formatImageAnalysis($aiResponse, $params['analysis_options']);

                // Calculate quality score for analysis
                $qualityScore = $this->calculateAnalysisQuality($formattedAnalysis, $params['analysis_options']);

                // Keep best result
                if ($qualityScore > $bestScore) {
                    $bestScore = $qualityScore;
                    $bestResult = [
                        'analysis' => $formattedAnalysis,
                        'quality_score' => $qualityScore
                    ];
                }

                // If quality is good enough, stop retrying
                if ($qualityScore >= 0.7) {
                    Log::info('Image analysis quality acceptable', [
                        'score' => $qualityScore,
                        'attempt' => $attempt
                    ]);
                    break;
                }

                // If quality is low and we have retries left, try again
                if ($attempt < $maxRetries) {
                    Log::warning('Image analysis quality low, retrying', [
                        'score' => $qualityScore,
                        'attempt' => $attempt
                    ]);
                    sleep(1);
                }

            } catch (\Exception $e) {
                Log::error('Image analysis attempt failed', [
                    'error' => $e->getMessage(),
                    'model' => $this->currentModel,
                    'attempt' => $attempt
                ]);
                
                if ($attempt >= $maxRetries) {
                    throw $e;
                }
            }
        }

        if (!$bestResult) {
            throw new \Exception('Failed to analyze image after retries');
        }

        $generationTime = microtime(true) - $startTime;

        $finalResult = array_merge($bestResult, [
            'model_used' => $this->currentModel,
            'generation_time' => round($generationTime, 2),
            'analysis_options' => $params['analysis_options'],
        ]);

        // Cache result for 12 hours (shorter than caption due to more dynamic nature)
        Cache::put($cacheKey, $finalResult, 43200);

        return $finalResult;
    }

    private function buildImageAnalysisPrompt(array $params)
    {
        $options = $params['analysis_options'];
        $context = $params['context'] ?? '';
        
        $analysisInstructions = [];
        
        if (in_array('objects', $options)) {
            $analysisInstructions[] = "🎯 DETEKSI OBJEK: Identifikasi semua objek, produk, dan elemen visual dalam gambar dengan detail";
        }
        
        if (in_array('colors', $options)) {
            $analysisInstructions[] = "🎨 ANALISIS WARNA: Analisis palet warna dominan, harmoni warna, dan dampak psikologis warna";
        }
        
        if (in_array('composition', $options)) {
            $analysisInstructions[] = "📐 KOMPOSISI: Evaluasi rule of thirds, leading lines, framing, balance, dan teknik fotografi";
        }
        
        if (in_array('mood', $options)) {
            $analysisInstructions[] = "😊 MOOD & EMOSI: Analisis mood, emosi yang terpancar, dan dampak psikologis pada viewer";
        }
        
        if (in_array('text', $options)) {
            $analysisInstructions[] = "📝 BACA TEKS: Identifikasi dan baca semua teks yang terlihat dalam gambar (OCR)";
        }
        
        if (in_array('marketing', $options)) {
            $analysisInstructions[] = "📈 TIPS MARKETING: Berikan rekomendasi marketing, platform yang cocok, dan strategi konten";
        }
        
        if (in_array('quality', $options)) {
            $analysisInstructions[] = "⭐ KUALITAS FOTO: Evaluasi kualitas teknis: lighting, focus, resolution, noise, exposure";
        }
        
        if (in_array('suggestions', $options)) {
            $analysisInstructions[] = "💡 SARAN PERBAIKAN: Berikan saran konkret untuk memperbaiki foto dan meningkatkan daya tarik";
        }

        if (in_array('typography', $options)) {
            $analysisInstructions[] = "🔤 TIPOGRAFI: Evaluasi pemilihan font, ukuran, keterbacaan, hierarki teks, dan kesesuaian dengan brand";
        }

        if (in_array('layout', $options)) {
            $analysisInstructions[] = "📏 LAYOUT & HIERARKI VISUAL: Analisis tata letak elemen, visual hierarchy, whitespace, grid, dan alur mata pembaca";
        }

        if (in_array('branding', $options)) {
            $analysisInstructions[] = "🏷️ KONSISTENSI BRAND: Nilai konsistensi warna brand, logo placement, tone visual, dan apakah desain mencerminkan identitas brand dengan baik";
        }

        if (in_array('cta_design', $options)) {
            $analysisInstructions[] = "⚡ EFEKTIVITAS CTA: Evaluasi tombol/teks call-to-action — visibilitas, warna kontras, posisi, ukuran, dan kemungkinan konversi";
        }

        $instructionText = implode("\n", $analysisInstructions);
        
        return <<<PROMPT
Analisis gambar ini secara mendalam. Berikan analisis yang komprehensif dan actionable.

KONTEKS BISNIS:
{$context}

ANALISIS YANG DIMINTA:
{$instructionText}

FORMAT OUTPUT:
Berikan analisis dalam format yang terstruktur dan mudah dibaca dengan:
- Header untuk setiap section (gunakan emoji)
- Bullet points untuk detail
- Skor/rating jika relevan (1-10)
- Rekomendasi konkret dan actionable

GAYA PENULISAN:
- Bahasa Indonesia yang profesional tapi friendly
- Gunakan emoji untuk visual appeal
- Berikan insight yang mendalam dan praktis
- Fokus pada actionable recommendations
- Sebutkan angka/data spesifik jika memungkinkan

PENTING: 
- Berikan analisis yang honest dan konstruktif
- Fokus pada improvement dan optimization
- Sesuaikan dengan konteks UMKM Indonesia
- Berikan tips yang bisa langsung diimplementasikan
PROMPT;
    }

    private function formatImageAnalysis($rawAnalysis, $options)
    {
        // Add header and footer for better presentation
        $header = "🔍 **HASIL ANALISIS GAMBAR**\n";
        $header .= "📅 Tanggal: " . now()->format('d M Y, H:i') . "\n";
        $header .= "🎯 Analisis: " . implode(', ', array_map('ucfirst', $options)) . "\n\n";
        
        $footer = "\n\n---\n";
        $footer .= "💡 **Tips Tambahan:**\n";
        $footer .= "• Gunakan hasil analisis ini untuk optimasi konten\n";
        $footer .= "• Test A/B dengan implementasi saran yang diberikan\n";
        $footer .= "• Monitor engagement setelah menerapkan perbaikan\n\n";
        $footer .= "🤖 Dianalisis dengan AI Vision | Noteds";
        
        return $header . $rawAnalysis . $footer;
    }

    private function calculateAnalysisQuality($analysis, $options)
    {
        $score = 0.5; // Base score
        
        // Check if analysis covers requested options
        foreach ($options as $option) {
            switch ($option) {
                case 'objects':
                    if (stripos($analysis, 'objek') !== false || stripos($analysis, 'elemen') !== false) {
                        $score += 0.1;
                    }
                    break;
                case 'colors':
                    if (stripos($analysis, 'warna') !== false || stripos($analysis, 'color') !== false) {
                        $score += 0.1;
                    }
                    break;
                case 'composition':
                    if (stripos($analysis, 'komposisi') !== false || stripos($analysis, 'rule of thirds') !== false) {
                        $score += 0.1;
                    }
                    break;
                case 'mood':
                    if (stripos($analysis, 'mood') !== false || stripos($analysis, 'emosi') !== false) {
                        $score += 0.1;
                    }
                    break;
                case 'marketing':
                    if (stripos($analysis, 'marketing') !== false || stripos($analysis, 'strategi') !== false) {
                        $score += 0.1;
                    }
                    break;
            }
        }
        
        // Check for actionable recommendations
        if (stripos($analysis, 'saran') !== false || stripos($analysis, 'rekomendasi') !== false) {
            $score += 0.1;
        }
        
        // Check for specific details
        if (preg_match('/\d+/', $analysis)) { // Contains numbers/ratings
            $score += 0.05;
        }
        
        // Check length (detailed analysis should be longer)
        if (strlen($analysis) > 500) {
            $score += 0.1;
        }
        
        return min($score, 1.0); // Cap at 1.0
    }

    /**
     * 🎬 Generate video content using Gemini AI
     */
    public function generateVideoContent(array $params)
    {
        $startTime = microtime(true);

        // Validate required params
        if (empty($params['content_type']) || empty($params['platform']) || empty($params['product'])) {
            throw new \Exception('Content type, platform, and product are required');
        }

        // Check cache
        $cacheKey = "video_content_" . md5(json_encode([
            'content_type' => $params['content_type'],
            'platform' => $params['platform'],
            'duration' => $params['duration'],
            'product' => $params['product'],
            'goal' => $params['goal'],
            'styles' => $params['styles'] ?? [],
        ]));

        $cached = Cache::get($cacheKey);
        if ($cached) {
            Log::info('Cache hit for video content', ['cache_key' => $cacheKey]);
            return $cached;
        }

        $prompt = DynamicDateService::replaceDatePlaceholders($this->buildVideoContentPrompt($params));
        $maxRetries = 2;
        $attempt = 0;
        $bestResult = null;
        $bestScore = 0;

        while ($attempt < $maxRetries) {
            $attempt++;

            try {
                $requestData = [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.9, // Higher creativity for video content
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 4096,
                        'responseMimeType' => 'text/plain',
                    ],
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ]
                ];

                Log::info('Calling Gemini API for Video Content', [
                    'model' => $this->currentModel,
                    'user_id' => $params['user_id'] ?? null,
                    'content_type' => $params['content_type'],
                    'platform' => $params['platform'],
                    'attempt' => $attempt
                ]);

                $response = Http::timeout(180)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($this->apiUrl . '?key=' . $this->apiKey, $requestData);

                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['error']['message'] ?? 'Unknown error';
                    Log::error('Gemini Video Content API Error', [
                        'status' => $response->status(),
                        'error' => $errorMessage
                    ]);
                    throw new \Exception('API Error: ' . $errorMessage);
                }

                $result = $response->json();
                $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

                // Format and enhance the video content
                $formattedContent = $this->formatVideoContent($aiResponse, $params);

                // Calculate quality score for video content
                $qualityScore = $this->calculateVideoContentQuality($formattedContent, $params);

                // Keep best result
                if ($qualityScore > $bestScore) {
                    $bestScore = $qualityScore;
                    $bestResult = [
                        'content' => $formattedContent,
                        'quality_score' => $qualityScore
                    ];
                }

                // If quality is good enough, stop retrying
                if ($qualityScore >= 0.7) {
                    Log::info('Video content quality acceptable', [
                        'score' => $qualityScore,
                        'attempt' => $attempt
                    ]);
                    break;
                }

                // If quality is low and we have retries left, try again
                if ($attempt < $maxRetries) {
                    Log::warning('Video content quality low, retrying', [
                        'score' => $qualityScore,
                        'attempt' => $attempt
                    ]);
                    sleep(1);
                }

            } catch (\Exception $e) {
                Log::error('Video content generation attempt failed', [
                    'error' => $e->getMessage(),
                    'model' => $this->currentModel,
                    'attempt' => $attempt
                ]);
                
                if ($attempt >= $maxRetries) {
                    throw $e;
                }
            }
        }

        if (!$bestResult) {
            throw new \Exception('Failed to generate video content after retries');
        }

        $generationTime = microtime(true) - $startTime;

        $finalResult = array_merge($bestResult, [
            'model_used' => $this->currentModel,
            'generation_time' => round($generationTime, 2),
            'content_type' => $params['content_type'],
            'platform' => $params['platform'],
        ]);

        // Cache result for 6 hours (video trends change quickly)
        Cache::put($cacheKey, $finalResult, 21600);

        return $finalResult;
    }

    private function buildVideoContentPrompt(array $params)
    {
        $contentType = $params['content_type'];
        $platform = $params['platform'];
        $duration = $params['duration'];
        $product = $params['product'];
        $targetAudience = $params['target_audience'] ?? 'Gen Z dan Millennial Indonesia';
        $goal = $params['goal'];
        $styles = implode(', ', $params['styles'] ?? []);
        $context = $params['context'] ?? '';
        $imageAnalysis = $params['image_analysis'] ?? null;

        $platformSpecs = $this->getVideoSpecs($platform, $duration);
        $contentTypeInstructions = $this->getContentTypeInstructions($contentType);

        // Build image analysis section if available
        $imageSection = '';
        if ($imageAnalysis) {
            $imageSection = "\n\nANALISIS VISUAL PRODUK:\n";
            $imageSection .= "Visual Elements: " . implode(', ', $imageAnalysis['visual_elements'] ?? []) . "\n";
            $imageSection .= "Colors: " . implode(', ', $imageAnalysis['colors'] ?? []) . "\n";
            $imageSection .= "Scene Recommendations: " . implode(', ', $imageAnalysis['scene_recommendations'] ?? []) . "\n";
            $imageSection .= "Camera Angles: " . implode(', ', $imageAnalysis['camera_angles'] ?? []) . "\n";
            $imageSection .= "Props & Settings: " . implode(', ', $imageAnalysis['props_and_settings'] ?? []) . "\n";
            $imageSection .= "Lighting Tips: " . implode(', ', $imageAnalysis['lighting_tips'] ?? []) . "\n";
            $imageSection .= "\nGUNAKAN ANALISIS VISUAL INI untuk membuat scene-by-scene yang detail dan spesifik!";
        }

        return <<<PROMPT
Generate konten video {$contentType} untuk platform {$platform} dengan durasi {$duration} detik.

INFORMASI PRODUK/TOPIK:
{$product}

TARGET AUDIENCE:
{$targetAudience}

TUJUAN VIDEO:
{$goal}

STYLE VIDEO:
{$styles}

KONTEKS TAMBAHAN:
{$context}{$imageSection}

SPESIFIKASI PLATFORM:
{$platformSpecs}

INSTRUKSI KONTEN:
{$contentTypeInstructions}

FORMAT OUTPUT:
Berikan output yang terstruktur dan siap pakai dengan:
- Header yang menarik dengan emoji
- Breakdown per detik/scene jika script (WAJIB jika ada analisis visual)
- Hook pembuka yang viral (3 detik pertama)
- Scene-by-scene description yang DETAIL (jika ada foto produk)
- Visual direction per scene (camera angle, lighting, props)
- Call to action yang kuat
- Hashtag trending yang relevan
- Tips shooting dan editing
- Music/sound recommendations

GAYA PENULISAN:
- Bahasa Indonesia yang engaging dan viral
- Gunakan slang Gen Z yang tepat
- Emoji yang relevan dan menarik
- Fokus pada engagement dan shareability
- Sesuaikan dengan kultur UMKM Indonesia

PENTING:
- Hook 3 detik pertama HARUS viral dan attention-grabbing
- Jika ada analisis visual, WAJIB buat scene-by-scene yang detail
- Setiap detik harus valuable dan engaging
- Gunakan visual elements dari analisis untuk scene description
- Call to action harus natural tapi persuasif
- Hashtag harus trending dan relevan {CURRENT_YEAR}
- Berikan tips praktis yang bisa langsung diimplementasikan
PROMPT;
    }

    private function getVideoSpecs($platform, $duration)
    {
        $specs = [
            'tiktok' => "TikTok - Format vertikal 9:16, maksimal 60 detik, fokus pada hook 3 detik pertama, trending sounds penting",
            'instagram-reels' => "Instagram Reels - Format vertikal 9:16, maksimal 90 detik, cover menarik, musik trending",
            'youtube-shorts' => "YouTube Shorts - Format vertikal 9:16, maksimal 60 detik, thumbnail eye-catching, SEO title",
            'instagram-story' => "Instagram Story - Format vertikal 9:16, 15 detik, interactive elements, swipe up",
            'facebook-reels' => "Facebook Reels - Format vertikal 9:16, maksimal 90 detik, engaging caption",
            'all-platforms' => "Multi-platform - Format vertikal 9:16, optimized untuk semua platform"
        ];

        return $specs[$platform] ?? $specs['all-platforms'];
    }

    private function getContentTypeInstructions($contentType)
    {
        $instructions = [
            'script' => "
SCRIPT VIDEO - Berikan script lengkap dengan:
- Timing per detik (0-3s: Hook, 4-10s: Problem, 11-20s: Solution, dst)
- Dialog/narasi yang natural
- Visual cues dan action
- Transition antar scene
- Background music suggestions",

            'storyboard' => "
STORYBOARD - Berikan storyboard visual dengan:
- Scene breakdown per 5-10 detik
- Visual description detail
- Camera angles dan movements
- Props dan setting yang dibutuhkan
- Color palette dan mood",

            'hook' => "
HOOK VIRAL - Berikan 5-10 variasi hook pembuka dengan:
- Hook 3 detik pertama yang attention-grabbing
- Pattern interrupt yang kuat
- Curiosity gap yang bikin penasaran
- Emotional trigger yang tepat
- A/B testing variations",

            'ideas' => "
IDE KONTEN - Berikan 10-15 ide konten dengan:
- Konsep unik dan fresh
- Angle yang berbeda-beda
- Trending topics integration
- Seasonal relevance
- Viral potential analysis"
        ];

        return $instructions[$contentType] ?? $instructions['script'];
    }

    private function formatVideoContent($rawContent, $params)
    {
        $contentType = ucfirst($params['content_type']);
        $platform = strtoupper($params['platform']);
        $duration = $params['duration'];

        $header = "🎬 **{$contentType} VIDEO UNTUK {$platform}**\n";
        $header .= "⏱️ Durasi: {$duration} detik\n";
        $header .= "📅 Generated: " . now()->format('d M Y, H:i') . "\n";
        $header .= "🎯 Produk: " . $params['product'] . "\n\n";

        $footer = "\n\n---\n";
        $footer .= "💡 **Tips Tambahan:**\n";
        $footer .= "• Test hook dengan audience kecil dulu\n";
        $footer .= "• Monitor engagement rate di 3 detik pertama\n";
        $footer .= "• Gunakan trending sounds untuk boost reach\n";
        $footer .= "• Post di golden hours untuk maksimal views\n\n";
        $footer .= "🎬 Generated by AI Video Generator | Noteds";

        return $header . $rawContent . $footer;
    }

    private function calculateVideoContentQuality($content, $params)
    {
        $score = 0.5; // Base score

        // Check for essential video elements
        if (stripos($content, 'hook') !== false || stripos($content, '3 detik') !== false) {
            $score += 0.15; // Hook presence
        }

        if (stripos($content, 'hashtag') !== false || stripos($content, '#') !== false) {
            $score += 0.1; // Hashtag presence
        }

        if (stripos($content, 'call to action') !== false || stripos($content, 'cta') !== false) {
            $score += 0.1; // CTA presence
        }

        // Check for timing/structure
        if (preg_match('/\d+\s*detik|\d+s|\d+-\d+/', $content)) {
            $score += 0.1; // Timing structure
        }

        // Check for platform-specific elements
        $platform = $params['platform'];
        if (stripos($content, $platform) !== false) {
            $score += 0.05; // Platform awareness
        }

        // Check for engagement elements
        if (stripos($content, 'viral') !== false || stripos($content, 'trending') !== false) {
            $score += 0.05; // Viral elements
        }

        // Check length (good video content should be detailed)
        if (strlen($content) > 800) {
            $score += 0.05;
        }

        return min($score, 1.0); // Cap at 1.0
    }

    private function buildImageCaptionPrompt(array $params)
    {
        $businessType = $params['business_type'] ?? 'UMKM';
        $productName = $params['product_name'] ?? '';

        return <<<PROMPT
Analisis foto produk ini dan berikan output dalam format JSON yang VALID.

Format JSON yang HARUS diikuti:
{
  "detected_objects": ["object1", "object2"],
  "dominant_colors": ["#hex1", "#hex2"],
  "caption_single": "Caption untuk single post",
  "caption_carousel": ["Slide 1 text", "Slide 2 text", "Slide 3 text"],
  "editing_tips": ["Tip 1", "Tip 2", "Tip 3"]
}

Konteks:
- Jenis Bisnis: {$businessType}
- Nama Produk: {$productName}

Instruksi Caption:
- Bahasa Indonesia
- Gunakan emoji yang relevan
- Hashtag untuk UMKM Indonesia
- Caption engaging dan persuasif
- Panjang caption 100-150 kata

Instruksi Carousel:
- Slide 1: Hook menarik perhatian (20-30 kata)
- Slide 2: Penjelasan produk/benefit (30-40 kata)
- Slide 3: Call to action kuat (20-30 kata)

Instruksi Tips:
- 3 tips praktis untuk editing foto
- Fokus pada lighting, komposisi, dan filter

PENTING: Berikan HANYA JSON yang valid, tanpa teks tambahan apapun.
PROMPT;
    }

    private function parseImageCaptionResponse($response)
    {
        // Remove markdown code blocks if present
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        $response = trim($response);

        // Try to extract JSON if it's embedded in text
        if (preg_match('/\{[\s\S]*\}/', $response, $matches)) {
            $response = $matches[0];
        }

        try {
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Parse Error', [
                    'error' => json_last_error_msg(),
                    'response' => substr($response, 0, 500)
                ]);
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }

            return [
                'detected_objects' => $data['detected_objects'] ?? [],
                'dominant_colors' => $data['dominant_colors'] ?? [],
                'caption_single' => $data['caption_single'] ?? '',
                'caption_carousel' => $data['caption_carousel'] ?? [],
                'editing_tips' => $data['editing_tips'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Parse Image Caption Response Failed', [
                'error' => $e->getMessage()
            ]);
            
            // Fallback
            return [
                'detected_objects' => ['Produk'],
                'dominant_colors' => [],
                'caption_single' => 'Caption tidak dapat di-generate. Silakan coba lagi.',
                'caption_carousel' => [],
                'editing_tips' => [],
            ];
        }
    }

    private function calculateImageCaptionQuality($parsed)
    {
        $score = 0.5; // Base score

        // Check if all fields are populated
        if (!empty($parsed['detected_objects'])) $score += 0.1;
        if (!empty($parsed['dominant_colors'])) $score += 0.1;
        if (!empty($parsed['caption_single']) && strlen($parsed['caption_single']) > 50) $score += 0.15;
        if (!empty($parsed['caption_carousel']) && count($parsed['caption_carousel']) >= 3) $score += 0.1;
        if (!empty($parsed['editing_tips']) && count($parsed['editing_tips']) >= 3) $score += 0.05;

        return min(1.0, $score);
    }

    /**
     * 🎯 Get Tier 1 Performance Stats
     */
    public function getTier1PerformanceStats(): array
    {
        $tier1Status = $this->fallbackManager->getTier1Status();
        $usageStats = $this->fallbackManager->getUsageStats();
        
        return [
            'tier_info' => $tier1Status,
            'usage_stats' => $usageStats,
            'performance_metrics' => [
                'requests_today' => $this->getRequestsToday(),
                'average_response_time' => $this->getAverageResponseTime(),
                'success_rate' => $this->getSuccessRate(),
                'tier1_benefits_utilized' => $tier1Status['is_tier1'],
            ],
            'recommendations' => $this->getPerformanceRecommendations($tier1Status, $usageStats),
        ];
    }
    
    /**
     * Get requests made today
     */
    protected function getRequestsToday(): int
    {
        $models = $this->fallbackManager->getAllModels();
        $totalRequests = 0;
        
        foreach ($models as $model) {
            $totalRequests += Cache::get("model_rpd:{$model['name']}", 0);
        }
        
        return $totalRequests;
    }
    
    /**
     * Get average response time (cached)
     */
    protected function getAverageResponseTime(): float
    {
        return Cache::get('gemini_avg_response_time', 2.5); // Default 2.5s
    }
    
    /**
     * Get success rate (cached)
     */
    protected function getSuccessRate(): float
    {
        $successful = Cache::get('gemini_successful_requests', 0);
        $total = Cache::get('gemini_total_requests', 1);
        
        return round(($successful / $total) * 100, 2);
    }
    
    /**
     * Get performance recommendations
     */
    protected function getPerformanceRecommendations(array $tier1Status, array $usageStats): array
    {
        $recommendations = [];
        
        if (!$tier1Status['is_tier1']) {
            $recommendations[] = '💳 Upgrade to Tier 1 for 10x performance boost';
            $recommendations[] = '📈 Current: 10 RPM → Tier 1: 1000+ RPM';
            return $recommendations;
        }
        
        // Tier 1 specific recommendations
        $primaryModel = $usageStats[$tier1Status['primary_model']] ?? null;
        
        if ($primaryModel && $primaryModel['rpm']['percentage'] > 80) {
            $recommendations[] = '⚡ Consider using Flash-Lite (4000 RPM) for high volume';
        }
        
        if ($primaryModel && $primaryModel['rpm']['percentage'] < 20) {
            $recommendations[] = '✅ Excellent utilization - you have plenty of capacity';
        }
        
        $recommendations[] = '🎯 Tier 1 Active - Enjoying premium performance!';
        $recommendations[] = '💡 Use Pro model for highest quality content';
        
        return $recommendations;
    }
    
    /**
     * Track performance metrics
     */
    public function trackPerformanceMetrics(float $responseTime, bool $success): void
    {
        // Update average response time
        $currentAvg = Cache::get('gemini_avg_response_time', 2.5);
        $newAvg = ($currentAvg + $responseTime) / 2;
        Cache::put('gemini_avg_response_time', $newAvg, now()->addDay());
        
        // Update success rate
        $successful = Cache::get('gemini_successful_requests', 0);
        $total = Cache::get('gemini_total_requests', 0);
        
        if ($success) {
            Cache::put('gemini_successful_requests', $successful + 1, now()->addDay());
        }
        Cache::put('gemini_total_requests', $total + 1, now()->addDay());
    }

    /**
     * 🎬 Analyze product image specifically for video content generation
     */
    public function analyzeProductImageForVideo(array $params)
    {
        $startTime = microtime(true);

        // Validate required params
        if (empty($params['image_data']) || empty($params['mime_type'])) {
            throw new \Exception('Image data and mime type are required');
        }

        $prompt = $this->buildVideoImageAnalysisPrompt($params);

        try {
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $params['mime_type'],
                                    'data' => $params['image_data']
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.8,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 2048,
                    'responseMimeType' => 'application/json',
                ],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ]
            ];

            Log::info('Calling Gemini Vision API for Video Image Analysis', [
                'model' => $this->currentModel,
                'product' => $params['product_name'] ?? 'unknown',
                'platform' => $params['video_platform'] ?? 'unknown'
            ]);

            $response = Http::timeout(120)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl . '?key=' . $this->apiKey, $requestData);

            if (!$response->successful()) {
                $errorBody = $response->json();
                $errorMessage = $errorBody['error']['message'] ?? 'Unknown error';
                Log::error('Gemini Video Image Analysis API Error', [
                    'status' => $response->status(),
                    'error' => $errorMessage
                ]);
                throw new \Exception('API Error: ' . $errorMessage);
            }

            $result = $response->json();
            $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Parse JSON response
            $parsed = json_decode($aiResponse, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Fallback to text parsing if JSON fails
                $parsed = $this->parseVideoImageAnalysisText($aiResponse);
            }

            $generationTime = microtime(true) - $startTime;

            $finalResult = [
                'visual_elements' => $parsed['visual_elements'] ?? [],
                'colors' => $parsed['colors'] ?? [],
                'composition_suggestions' => $parsed['composition_suggestions'] ?? [],
                'scene_recommendations' => $parsed['scene_recommendations'] ?? [],
                'camera_angles' => $parsed['camera_angles'] ?? [],
                'props_and_settings' => $parsed['props_and_settings'] ?? [],
                'lighting_tips' => $parsed['lighting_tips'] ?? [],
                'model_used' => $this->currentModel,
                'generation_time' => round($generationTime, 2),
            ];

            return $finalResult;

        } catch (\Exception $e) {
            Log::error('Video image analysis failed', [
                'error' => $e->getMessage(),
                'product' => $params['product_name'] ?? 'unknown'
            ]);
            
            // Return empty analysis on failure
            return null;
        }
    }

    private function buildVideoImageAnalysisPrompt(array $params)
    {
        $productName = $params['product_name'] ?? 'produk';
        $platform = $params['video_platform'] ?? 'social media';
        $goal = $params['video_goal'] ?? 'marketing';

        return <<<PROMPT
Analisis gambar produk ini untuk keperluan pembuatan konten video {$platform} dengan tujuan {$goal}.

PRODUK: {$productName}
PLATFORM: {$platform}
TUJUAN: {$goal}

Berikan analisis dalam format JSON yang VALID:

{
  "visual_elements": [
    "Deskripsi elemen visual utama",
    "Bentuk dan karakteristik produk",
    "Tekstur dan material"
  ],
  "colors": [
    "#hex1: Warna dominan",
    "#hex2: Warna aksen",
    "Mood warna yang terpancar"
  ],
  "composition_suggestions": [
    "Rule of thirds placement",
    "Framing recommendations",
    "Background suggestions"
  ],
  "scene_recommendations": [
    "Scene 1: Opening shot description",
    "Scene 2: Product showcase",
    "Scene 3: Detail/benefit focus",
    "Scene 4: Call to action visual"
  ],
  "camera_angles": [
    "Angle terbaik untuk produk ini",
    "Movement suggestions",
    "Zoom recommendations"
  ],
  "props_and_settings": [
    "Props yang cocok",
    "Setting/background ideal",
    "Lifestyle elements"
  ],
  "lighting_tips": [
    "Natural vs artificial lighting",
    "Shadow management",
    "Highlight recommendations"
  ]
}

FOKUS ANALISIS:
- Bagaimana memaksimalkan daya tarik visual produk
- Scene-by-scene breakdown yang detail
- Camera work yang optimal untuk platform {$platform}
- Props dan setting yang enhance produk
- Lighting yang membuat produk terlihat premium

PENTING: Berikan HANYA JSON yang valid, tanpa teks tambahan apapun.
PROMPT;
    }

    private function parseVideoImageAnalysisText($text)
    {
        // Fallback text parsing if JSON fails
        return [
            'visual_elements' => ['Produk terlihat menarik dengan karakteristik visual yang baik'],
            'colors' => ['Warna yang harmonis dan eye-catching'],
            'composition_suggestions' => ['Gunakan rule of thirds untuk komposisi optimal'],
            'scene_recommendations' => [
                'Scene 1: Wide shot untuk establish produk',
                'Scene 2: Close-up untuk detail',
                'Scene 3: Action shot penggunaan',
                'Scene 4: Final shot dengan CTA'
            ],
            'camera_angles' => ['45 degree angle untuk dimensi terbaik'],
            'props_and_settings' => ['Background minimalis untuk fokus pada produk'],
            'lighting_tips' => ['Soft lighting untuk hasil yang natural']
        ];
    }

    /**
     * Build prompt for Short Drama & Story content
     */
    protected function buildShortDramaPrompt(string $subcategory, string $brief, string $tone, string $keywords, int $variationCount = 1): string
    {
        $prompt  = "Kamu adalah penulis skenario profesional yang ahli dalam membuat short drama, mini series, dan cerita pendek untuk konten TikTok dan Instagram Reels.\n\n";
        $prompt .= "Gaya penulisan: Ala drakor (drama Korea) dan dracin (drama China) — emosional, dramatis, dialog natural, dan bikin penonton baper.\n\n";

        if ($brief) {
            $prompt .= "Brief dari kreator:\n{$brief}\n\n";
        }

        if ($keywords) {
            $prompt .= "Elemen yang harus ada: {$keywords}\n\n";
        }

        $prompt .= "PANDUAN PENULISAN:\n";
        $prompt .= "- Dialog harus natural, tidak kaku, dan terasa nyata\n";
        $prompt .= "- Gunakan bahasa Indonesia yang natural (boleh campur sedikit bahasa gaul)\n";
        $prompt .= "- Sertakan arahan akting dalam *tanda bintang* (contoh: *menatap dengan mata berkaca-kaca*)\n";
        $prompt .= "- Setiap scene harus ada setting yang jelas (lokasi, waktu, suasana)\n";
        $prompt .= "- Buat dialog yang memorable dan quotable\n";
        $prompt .= "- Cocok untuk format video pendek 1-7 menit\n\n";

        $audienceCtx = [
            'audience'       => 'penonton drama Indonesia',
            'pain_points'    => 'butuh konten drama yang relatable',
            'desired_action' => 'menonton sampai habis dan share',
        ];

        $prompt .= \App\Services\TemplatePrompts::getPrompt($subcategory, '', $tone, $audienceCtx);

        if ($variationCount > 1) {
            $prompt .= "\nBuatkan {$variationCount} variasi dengan ending yang berbeda-beda.\n";
        }

        return $prompt;
    }

    /**
     * 🎯 Generate complete Google Ads campaign package
     */
    public function generateGoogleAdsCampaign(array $params): array
    {
        $businessName   = $params['business_name'];
        $productService = $params['product_service'];
        $targetAudience = $params['target_audience'];
        $location       = $params['location'];
        $dailyBudget    = (int) $params['daily_budget'];
        $campaignGoal   = $params['campaign_goal'];
        $campaignType   = $params['campaign_type'];
        $landingUrl     = $params['landing_page_url'] ?? '';
        $keywordsHint   = $params['keywords_hint'] ?? '';
        $usp            = $params['usp'] ?? '';
        $lang           = $params['language'] ?? 'id';

        $goalLabels = [
            'sales'           => 'Penjualan / Konversi',
            'leads'           => 'Leads / Prospek',
            'traffic'         => 'Traffic Website',
            'brand_awareness' => 'Brand Awareness',
            'app_installs'    => 'Install Aplikasi',
        ];
        $typeLabels = [
            'search'          => 'Search Ads',
            'display'         => 'Display Ads',
            'shopping'        => 'Shopping Ads',
            'video'           => 'Video Ads (YouTube)',
            'performance_max' => 'Performance Max',
        ];

        $monthlyBudget  = $dailyBudget * 30;
        $goalLabel      = $goalLabels[$campaignGoal] ?? $campaignGoal;
        $typeLabel      = $typeLabels[$campaignType] ?? $campaignType;

        $prompt = <<<PROMPT
Kamu adalah Google Ads specialist berpengalaman 10+ tahun yang membantu UMKM Indonesia membuat kampanye Google Ads yang efektif dan siap pakai.

Buat PAKET KAMPANYE GOOGLE ADS LENGKAP untuk:
- Bisnis: {$businessName}
- Produk/Layanan: {$productService}
- Target Audience: {$targetAudience}
- Lokasi Target: {$location}
- Budget Harian: Rp {$dailyBudget} (Bulanan: Rp {$monthlyBudget})
- Tujuan Kampanye: {$goalLabel}
- Tipe Kampanye: {$typeLabel}
- Landing Page: {$landingUrl}
- Kata Kunci Awal: {$keywordsHint}
- Keunggulan Produk (USP): {$usp}

Berikan output dalam format JSON yang LENGKAP dan SIAP COPY-PASTE ke Google Ads:

```json
{
  "campaign_summary": {
    "name": "nama kampanye yang disarankan",
    "type": "{$typeLabel}",
    "goal": "{$goalLabel}",
    "daily_budget_idr": {$dailyBudget},
    "monthly_budget_idr": {$monthlyBudget},
    "recommended_bid_strategy": "strategi bidding yang disarankan (Target CPA/ROAS/Maximize Clicks/dll)",
    "estimated_daily_clicks": "estimasi klik per hari",
    "estimated_monthly_reach": "estimasi jangkauan per bulan",
    "estimated_cpc_idr": "estimasi cost per click dalam Rupiah",
    "quality_score_prediction": "prediksi Quality Score 1-10",
    "campaign_score": 85
  },
  "ad_groups": [
    {
      "name": "nama ad group 1",
      "theme": "tema/fokus ad group ini",
      "keywords": {
        "exact_match": ["[keyword exact 1]", "[keyword exact 2]", "[keyword exact 3]"],
        "phrase_match": ["\"keyword phrase 1\"", "\"keyword phrase 2\"", "\"keyword phrase 3\""],
        "broad_match_modifier": ["+keyword +broad 1", "+keyword +broad 2"],
        "negative_keywords": ["-keyword negatif 1", "-keyword negatif 2", "-keyword negatif 3"]
      },
      "ads": [
        {
          "ad_type": "Responsive Search Ad",
          "headlines": [
            "Headline 1 (maks 30 karakter)",
            "Headline 2 (maks 30 karakter)",
            "Headline 3 (maks 30 karakter)",
            "Headline 4 (maks 30 karakter)",
            "Headline 5 (maks 30 karakter)",
            "Headline 6 (maks 30 karakter)",
            "Headline 7 (maks 30 karakter)",
            "Headline 8 (maks 30 karakter)",
            "Headline 9 (maks 30 karakter)",
            "Headline 10 (maks 30 karakter)",
            "Headline 11 (maks 30 karakter)",
            "Headline 12 (maks 30 karakter)",
            "Headline 13 (maks 30 karakter)",
            "Headline 14 (maks 30 karakter)",
            "Headline 15 (maks 30 karakter)"
          ],
          "descriptions": [
            "Deskripsi 1 yang menarik dan informatif, maksimal 90 karakter, sertakan CTA",
            "Deskripsi 2 yang berbeda angle, maksimal 90 karakter, highlight USP",
            "Deskripsi 3 yang fokus pada benefit, maksimal 90 karakter",
            "Deskripsi 4 yang menciptakan urgency, maksimal 90 karakter"
          ],
          "display_url": "contoh-display-url.com/path",
          "final_url": "{$landingUrl}"
        }
      ]
    },
    {
      "name": "nama ad group 2",
      "theme": "tema/fokus ad group kedua",
      "keywords": {
        "exact_match": ["[keyword exact 1]", "[keyword exact 2]"],
        "phrase_match": ["\"keyword phrase 1\"", "\"keyword phrase 2\""],
        "broad_match_modifier": ["+keyword +broad 1"],
        "negative_keywords": ["-keyword negatif 1", "-keyword negatif 2"]
      },
      "ads": [
        {
          "ad_type": "Responsive Search Ad",
          "headlines": [
            "Headline 1", "Headline 2", "Headline 3", "Headline 4", "Headline 5",
            "Headline 6", "Headline 7", "Headline 8", "Headline 9", "Headline 10",
            "Headline 11", "Headline 12", "Headline 13", "Headline 14", "Headline 15"
          ],
          "descriptions": [
            "Deskripsi 1 maks 90 karakter",
            "Deskripsi 2 maks 90 karakter",
            "Deskripsi 3 maks 90 karakter",
            "Deskripsi 4 maks 90 karakter"
          ],
          "display_url": "contoh-display-url.com/path2",
          "final_url": "{$landingUrl}"
        }
      ]
    }
  ],
  "ad_extensions": {
    "sitelinks": [
      {"title": "Judul Sitelink 1 (maks 25 karakter)", "description1": "Deskripsi baris 1", "description2": "Deskripsi baris 2", "url": "{$landingUrl}/page1"},
      {"title": "Judul Sitelink 2", "description1": "Deskripsi baris 1", "description2": "Deskripsi baris 2", "url": "{$landingUrl}/page2"},
      {"title": "Judul Sitelink 3", "description1": "Deskripsi baris 1", "description2": "Deskripsi baris 2", "url": "{$landingUrl}/page3"},
      {"title": "Judul Sitelink 4", "description1": "Deskripsi baris 1", "description2": "Deskripsi baris 2", "url": "{$landingUrl}/page4"}
    ],
    "callouts": [
      "Callout 1 (maks 25 karakter)",
      "Callout 2 (maks 25 karakter)",
      "Callout 3 (maks 25 karakter)",
      "Callout 4 (maks 25 karakter)",
      "Callout 5 (maks 25 karakter)",
      "Callout 6 (maks 25 karakter)"
    ],
    "structured_snippets": {
      "header": "Pilih header yang sesuai (Merek/Layanan/Produk/dll)",
      "values": ["Value 1", "Value 2", "Value 3", "Value 4"]
    },
    "call_extension": {
      "phone_number": "nomor telepon bisnis",
      "call_reporting": true
    },
    "location_extension": "Aktifkan jika ada toko fisik di {$location}"
  },
  "targeting_settings": {
    "locations": ["{$location}", "area sekitar yang relevan"],
    "languages": ["Bahasa Indonesia"],
    "audiences": [
      {"type": "In-market", "segment": "segmen in-market yang relevan"},
      {"type": "Custom Intent", "keywords": ["keyword intent 1", "keyword intent 2"]},
      {"type": "Remarketing", "description": "pengunjung website sebelumnya"}
    ],
    "devices": ["Mobile", "Desktop", "Tablet"],
    "ad_schedule": {
      "recommended_hours": "jam tayang yang disarankan berdasarkan target audience",
      "peak_hours": "jam puncak yang disarankan"
    },
    "demographic": {
      "age": "rentang usia yang disarankan",
      "gender": "gender target jika relevan",
      "household_income": "segmen pendapatan jika relevan"
    }
  },
  "budget_allocation": {
    "daily_budget_idr": {$dailyBudget},
    "recommended_split": {
      "search": "persentase untuk search",
      "display_remarketing": "persentase untuk remarketing display"
    },
    "bid_adjustments": {
      "mobile": "+X% atau -X%",
      "top_performing_locations": "+X%",
      "peak_hours": "+X%"
    },
    "estimated_results": {
      "monthly_clicks": "estimasi klik per bulan",
      "monthly_impressions": "estimasi impresi per bulan",
      "estimated_conversions": "estimasi konversi per bulan",
      "estimated_cpa_idr": "estimasi cost per acquisition dalam Rupiah",
      "estimated_roas": "estimasi Return on Ad Spend"
    }
  },
  "campaign_analysis": {
    "strengths": [
      "Kelebihan kampanye ini 1",
      "Kelebihan kampanye ini 2",
      "Kelebihan kampanye ini 3"
    ],
    "weaknesses": [
      "Kelemahan/risiko yang perlu diperhatikan 1",
      "Kelemahan/risiko yang perlu diperhatikan 2"
    ],
    "opportunities": [
      "Peluang optimasi 1",
      "Peluang optimasi 2"
    ],
    "recommendations": [
      "Rekomendasi spesifik 1 untuk meningkatkan performa",
      "Rekomendasi spesifik 2",
      "Rekomendasi spesifik 3"
    ],
    "campaign_type_comparison": {
      "chosen_type": "{$typeLabel}",
      "why_suitable": "alasan tipe ini cocok untuk tujuan dan bisnis ini",
      "alternative_types": [
        {"type": "tipe alternatif 1", "pros": "kelebihan", "cons": "kekurangan", "when_to_use": "kapan pakai ini"},
        {"type": "tipe alternatif 2", "pros": "kelebihan", "cons": "kekurangan", "when_to_use": "kapan pakai ini"}
      ]
    }
  },
  "conversion_tracking": {
    "recommended_conversions": [
      {"name": "nama konversi 1", "type": "tipe (Purchase/Lead/Page View/dll)", "value": "nilai konversi jika ada"},
      {"name": "nama konversi 2", "type": "tipe", "value": "nilai"}
    ],
    "setup_instructions": "instruksi singkat cara setup conversion tracking"
  },
  "optimization_checklist": [
    "✅ Checklist optimasi 1",
    "✅ Checklist optimasi 2",
    "✅ Checklist optimasi 3",
    "✅ Checklist optimasi 4",
    "✅ Checklist optimasi 5"
  ],
  "ab_test_suggestions": [
    {"element": "elemen yang ditest", "variant_a": "versi A", "variant_b": "versi B", "hypothesis": "hipotesis"},
    {"element": "elemen 2", "variant_a": "versi A", "variant_b": "versi B", "hypothesis": "hipotesis"}
  ]
}
```

PENTING:
- Semua headline HARUS ≤ 30 karakter (hitung dengan teliti!)
- Semua deskripsi HARUS ≤ 90 karakter
- Semua callout HARUS ≤ 25 karakter
- Gunakan bahasa Indonesia yang natural dan persuasif
- Keywords harus relevan dengan bisnis dan target audience
- Estimasi budget harus realistis untuk pasar Indonesia
- Berikan HANYA JSON valid, tanpa teks tambahan di luar JSON
PROMPT;

        $rawResponse = $this->generateText($prompt, 8000, 0.7);

        return $this->_parseJson($rawResponse);
    }

    /**
     * 💬 Generate AI Product Explainer for WhatsApp
     */
    public function generateProductExplainer(array $params): array
    {
        $productName  = $params['product_name'];
        $productDesc  = $params['product_desc'];
        $price        = $params['price'] ?? '';
        $features     = $params['features'] ?? '';
        $targetBuyer  = $params['target_buyer'] ?? 'calon pembeli';
        $sellerName   = $params['seller_name'] ?? '';
        $waNumber     = $params['wa_number'] ?? '';

        $priceInfo   = $price    ? "Harga: {$price}" : '';
        $featInfo    = $features ? "Fitur/Keunggulan: {$features}" : '';
        $sellerInfo  = $sellerName ? "Nama Penjual/Toko: {$sellerName}" : '';

        $prompt = <<<PROMPT
Kamu adalah asisten penjualan profesional Indonesia yang membantu penjual membuat pesan WhatsApp otomatis yang terkesan responsif dan profesional.

Buat PAKET PESAN WHATSAPP untuk produk berikut:
- Produk: {$productName}
- Deskripsi: {$productDesc}
- {$priceInfo}
- {$featInfo}
- Target Pembeli: {$targetBuyer}
- {$sellerInfo}

Buat output dalam format JSON:

```json
{
  "product_name": "{$productName}",
  "seller_greeting": "pesan sambutan singkat dari penjual (1-2 kalimat, hangat dan profesional)",
  "messages": [
    {
      "type": "ringkasan_singkat",
      "label": "📋 Ringkasan Singkat",
      "description": "Cocok untuk pembeli yang mau info cepat",
      "message": "pesan WA ringkas 3-5 baris: salam, nama produk, 2-3 poin utama, harga, CTA. Gunakan emoji secukupnya."
    },
    {
      "type": "detail_lengkap",
      "label": "📦 Detail Lengkap",
      "description": "Cocok untuk pembeli yang ingin tahu semua fitur",
      "message": "pesan WA detail: salam, deskripsi produk, list fitur dengan bullet/emoji, harga, cara order, garansi jika ada, CTA. Format rapi dan mudah dibaca."
    },
    {
      "type": "tanya_harga",
      "label": "💰 Tanya Harga & Promo",
      "description": "Cocok untuk pembeli yang fokus ke harga",
      "message": "pesan WA fokus harga: salam, langsung ke harga, bandingkan value vs harga, promo jika ada, cara bayar, CTA urgent."
    },
    {
      "type": "tanya_stok",
      "label": "📦 Cek Ketersediaan",
      "description": "Cocok untuk pembeli yang mau cek stok/varian",
      "message": "pesan WA cek stok: salam, tanya ketersediaan produk, varian yang tersedia, estimasi pengiriman, CTA."
    }
  ],
  "quick_replies": [
    "pertanyaan umum calon pembeli 1 yang bisa dijawab otomatis",
    "pertanyaan umum calon pembeli 2",
    "pertanyaan umum calon pembeli 3"
  ],
  "seller_tips": [
    "tips singkat untuk penjual agar terkesan lebih profesional saat balas WA 1",
    "tips singkat 2",
    "tips singkat 3"
  ]
}
```

ATURAN:
- Semua pesan harus dalam Bahasa Indonesia yang natural, hangat, dan profesional
- Gunakan emoji yang relevan tapi tidak berlebihan
- Setiap pesan harus LENGKAP dan siap kirim tanpa perlu edit
- Format pesan harus mudah dibaca di layar HP (baris pendek, ada spasi)
- Berikan HANYA JSON valid, tanpa teks tambahan
PROMPT;

        $rawResponse = $this->generateText($prompt, 3000, 0.75);

        $decoded = $this->_parseJson($rawResponse);

        // Attach wa_number for deep link generation on frontend
        if (!isset($decoded['parse_error'])) {
            $decoded['wa_number'] = $waNumber;
        }

        return $decoded;
    }

    /**
     * 🔗 Generate Magic Promo Link — 3 caption styles + deep links
     */
    public function generateMagicPromoLink(array $params): array
    {
        $productName    = $params['product_name'];
        $productDesc    = $params['product_desc'];
        $price          = $params['price'] ?? '';
        $targetAudience = $params['target_audience'] ?? 'umum';
        $promoDetail    = $params['promo_detail'] ?? '';
        $waNumber       = $params['wa_number'] ?? '';
        $lang           = $params['language'] ?? 'id';

        $priceInfo  = $price ? "Harga: {$price}" : '';
        $promoInfo  = $promoDetail ? "Detail Promo: {$promoDetail}" : '';
        $waInfo     = $waNumber ? "Nomor WhatsApp: {$waNumber}" : '';

        $prompt = <<<PROMPT
Kamu adalah copywriter profesional Indonesia yang ahli dalam membuat caption promosi yang menghasilkan penjualan.

Buat 3 variasi caption promosi untuk:
- Produk: {$productName}
- Deskripsi: {$productDesc}
- {$priceInfo}
- Target Audience: {$targetAudience}
- {$promoInfo}
- {$waInfo}

Buat TEPAT 3 variasi dengan gaya berbeda:

1. **HARD SELL** — Langsung to the point, urgency tinggi, CTA kuat, angka/harga menonjol, cocok untuk flash sale
2. **SOFT SELL** — Pendekatan halus, fokus manfaat & value, bangun kepercayaan, tidak terkesan memaksa
3. **STORYTELLING** — Cerita singkat yang relatable, emotional hook, buat pembaca merasa "ini gue banget"

Berikan output dalam format JSON:

```json
{
  "product_name": "{$productName}",
  "captions": [
    {
      "style": "Hard Sell",
      "emoji": "🔥",
      "tone": "Agresif & Urgent",
      "best_for": "Flash sale, limited offer, diskon besar",
      "caption": "teks caption hard sell lengkap dengan emoji dan hashtag",
      "hook": "kalimat pembuka yang menarik perhatian",
      "cta": "call-to-action yang digunakan",
      "hashtags": ["#hashtag1", "#hashtag2", "#hashtag3", "#hashtag4", "#hashtag5"]
    },
    {
      "style": "Soft Sell",
      "emoji": "✨",
      "tone": "Hangat & Persuasif",
      "best_for": "Brand building, edukasi produk, audience baru",
      "caption": "teks caption soft sell lengkap dengan emoji dan hashtag",
      "hook": "kalimat pembuka yang menarik perhatian",
      "cta": "call-to-action yang digunakan",
      "hashtags": ["#hashtag1", "#hashtag2", "#hashtag3", "#hashtag4", "#hashtag5"]
    },
    {
      "style": "Storytelling",
      "emoji": "💬",
      "tone": "Relatable & Emosional",
      "best_for": "Engagement tinggi, viral content, membangun koneksi",
      "caption": "teks caption storytelling lengkap dengan emoji dan hashtag",
      "hook": "kalimat pembuka yang menarik perhatian",
      "cta": "call-to-action yang digunakan",
      "hashtags": ["#hashtag1", "#hashtag2", "#hashtag3", "#hashtag4", "#hashtag5"]
    }
  ],
  "tips": {
    "hard_sell": "tips kapan dan bagaimana menggunakan gaya hard sell",
    "soft_sell": "tips kapan dan bagaimana menggunakan gaya soft sell",
    "storytelling": "tips kapan dan bagaimana menggunakan gaya storytelling"
  }
}
```

ATURAN:
- Setiap caption harus LENGKAP, siap pakai, tidak perlu diedit lagi
- Gunakan Bahasa Indonesia yang natural dan sesuai target audience
- Sertakan emoji yang relevan di dalam caption
- Hashtag harus relevan dan populer di Indonesia
- Caption minimal 3 paragraf, maksimal 5 paragraf
- Berikan HANYA JSON valid, tanpa teks tambahan
PROMPT;

        $rawResponse = $this->generateText($prompt, 4000, 0.8);

        return $this->_parseJson($rawResponse);
    }

    // ═══════════════════════════════════════════════════════════════
    // FEATURES 3-10
    // ═══════════════════════════════════════════════════════════════

    /** 3. SEO Metadata Auto-Fill */
    public function generateSeoMetadata(array $p): array
    {
        $category = $p['category'] ?? 'umum';
        $keyword  = $p['keywords'] ?? $p['target_keyword'] ?? $p['product_name'];
        $urlSlug  = $p['url'] ?? $p['url_slug'] ?? '';

        $prompt = <<<PROMPT
Kamu adalah SEO specialist Indonesia. Generate meta title dan meta description yang optimal untuk Google.

Produk/Aset: {$p['product_name']}
Deskripsi: {$p['product_desc']}
Kategori: {$category}
Target Keyword: {$keyword}
URL Slug: {$urlSlug}

Berikan output JSON:
```json
{
  "meta_title": "judul SEO maks 60 karakter, sertakan keyword utama di depan",
  "meta_description": "deskripsi SEO 150-160 karakter, persuasif, sertakan keyword dan CTA",
  "og_title": "Open Graph title untuk social share",
  "og_description": "OG description 200 karakter",
  "focus_keyword": "keyword utama yang dioptimasi",
  "secondary_keywords": ["keyword sekunder 1", "keyword sekunder 2", "keyword sekunder 3"],
  "slug_suggestion": "url-slug-yang-disarankan",
  "schema_type": "tipe schema.org yang cocok (Product/Service/Article/dll)",
  "seo_score": 85,
  "tips": ["tips SEO spesifik 1", "tips SEO spesifik 2", "tips SEO spesifik 3"],
  "char_counts": {"meta_title": 0, "meta_description": 0}
}
```
Berikan HANYA JSON valid.
PROMPT;
        ['text' => $raw, 'sources' => $sources] = $this->generateTextWithSearch($prompt, 1500, 1.0);
        $result = $this->_parseJson($raw);
        if (!empty($result['parse_error'])) throw new \Exception('AI gagal menghasilkan SEO metadata. Coba lagi.');
        if (!empty($sources)) $result['search_sources'] = $sources;
        return $result;
    }

    /** 4. Smart Comparison Tool */
    public function generateComparison(array $p): array
    {
        $priceA   = $p['product_a_price'] ?? 'tidak disebutkan';
        $priceB   = $p['product_b_price'] ?? 'tidak disebutkan';
        $context  = $p['buyer_persona'] ?? $p['buyer_context'] ?? 'pembeli umum';

        $prompt = <<<PROMPT
Kamu adalah konsultan produk profesional Indonesia. Bandingkan dua produk/aset secara objektif dan bantu calon pembeli memutuskan.

Produk A: {$p['product_a_name']}
Deskripsi A: {$p['product_a_desc']}
Harga A: {$priceA}

Produk B: {$p['product_b_name']}
Deskripsi B: {$p['product_b_desc']}
Harga B: {$priceB}

Konteks Pembeli: {$context}

Berikan output JSON:
```json
{
  "summary": "ringkasan perbandingan 2-3 kalimat yang netral dan informatif",
  "product_a": {
    "name": "{$p['product_a_name']}",
    "strengths": ["keunggulan 1", "keunggulan 2", "keunggulan 3"],
    "weaknesses": ["kelemahan 1", "kelemahan 2"],
    "best_for": "cocok untuk siapa/kondisi apa",
    "score": 80
  },
  "product_b": {
    "name": "{$p['product_b_name']}",
    "strengths": ["keunggulan 1", "keunggulan 2", "keunggulan 3"],
    "weaknesses": ["kelemahan 1", "kelemahan 2"],
    "best_for": "cocok untuk siapa/kondisi apa",
    "score": 75
  },
  "verdict": "rekomendasi akhir yang jelas: pilih A jika... pilih B jika...",
  "comparison_table": [
    {"aspect": "Harga", "product_a": "nilai", "product_b": "nilai", "winner": "A atau B atau Seri"},
    {"aspect": "Fitur Utama", "product_a": "nilai", "product_b": "nilai", "winner": "A"},
    {"aspect": "Kemudahan Penggunaan", "product_a": "nilai", "product_b": "nilai", "winner": "B"},
    {"aspect": "Value for Money", "product_a": "nilai", "product_b": "nilai", "winner": "A"},
    {"aspect": "Dukungan/Garansi", "product_a": "nilai", "product_b": "nilai", "winner": "Seri"}
  ],
  "share_message": "pesan ringkas untuk dikirim ke calon klien via WA/chat, max 300 karakter, berisi poin utama perbandingan dan rekomendasi"
}
```
Berikan HANYA JSON valid.
PROMPT;
        ['text' => $raw, 'sources' => $sources] = $this->generateTextWithSearch($prompt, 2000, 0.8);
        $result = $this->_parseJson($raw);
        if (!empty($result['parse_error'])) throw new \Exception('AI gagal menghasilkan perbandingan. Coba lagi.');
        if (!empty($sources)) $result['search_sources'] = $sources;
        return $result;
    }

    /** 5. Automated FAQ Generator */
    public function generateFaq(array $p): array
    {
        $price    = $p['price'] ?? 'tidak disebutkan';
        $category = $p['category'] ?? 'umum';

        $prompt = <<<PROMPT
Kamu adalah customer service expert Indonesia. Buat FAQ berdasarkan deskripsi produk/aset.

Produk: {$p['product_name']}
Deskripsi: {$p['product_desc']}
Harga: {$price}
Kategori: {$category}

Buat 7 FAQ yang paling sering ditanyakan calon pembeli. Jawaban harus meyakinkan dan meningkatkan trust.

```json
{
  "product_name": "{$p['product_name']}",
  "faqs": [
    {"question": "pertanyaan 1 yang paling sering ditanya", "answer": "jawaban lengkap dan meyakinkan, 2-4 kalimat"},
    {"question": "pertanyaan 2", "answer": "jawaban"},
    {"question": "pertanyaan 3", "answer": "jawaban"},
    {"question": "pertanyaan 4", "answer": "jawaban"},
    {"question": "pertanyaan 5", "answer": "jawaban"},
    {"question": "pertanyaan 6", "answer": "jawaban"},
    {"question": "pertanyaan 7", "answer": "jawaban"}
  ],
  "schema_faq": "JSON-LD schema.org/FAQPage siap paste ke HTML",
  "tips": "tips cara menampilkan FAQ di halaman produk untuk maksimalkan konversi"
}
```
Berikan HANYA JSON valid.
PROMPT;
        $result = $this->_parseJson($this->generateText($prompt, 2000, 0.7));
        if (!empty($result['parse_error'])) throw new \Exception('AI gagal menghasilkan FAQ. Coba lagi.');
        return $result;
    }

    /** 6. AI Hook Generator for Reels/TikTok */
    public function generateReelsHook(array $p): array
    {
        $audience  = $p['target_audience'] ?? 'umum';
        $goal      = $p['video_goal'] ?? $p['tone'] ?? 'promosi produk dan drive traffic ke halaman produk';

        $prompt = <<<PROMPT
Kamu adalah content creator viral Indonesia yang ahli TikTok dan Reels. Buat hook dan naskah pendek yang viral.

Produk: {$p['product_name']}
Deskripsi: {$p['product_desc']}
Target Audience: {$audience}
Tujuan Video: {$goal}

```json
{
  "hooks": [
    {"hook": "kalimat pembuka hook 1 yang viral, max 10 kata", "style": "gaya (Shock/Question/Story/Trend/Pain Point)", "why_works": "alasan hook ini efektif"},
    {"hook": "hook 2", "style": "gaya", "why_works": "alasan"},
    {"hook": "hook 3", "style": "gaya", "why_works": "alasan"},
    {"hook": "hook 4", "style": "gaya", "why_works": "alasan"},
    {"hook": "hook 5", "style": "gaya", "why_works": "alasan"}
  ],
  "scripts": [
    {
      "hook": "hook terbaik dari list di atas",
      "duration": "15 detik",
      "script": "naskah lengkap 15 detik: [0-3s] hook, [3-10s] isi/demo, [10-15s] CTA. Format dengan timestamp.",
      "caption": "caption untuk postingan dengan hashtag",
      "hashtags": ["#hashtag1", "#hashtag2", "#hashtag3", "#hashtag4", "#hashtag5"]
    },
    {
      "hook": "hook kedua terbaik",
      "duration": "30 detik",
      "script": "naskah 30 detik lebih detail",
      "caption": "caption alternatif",
      "hashtags": ["#hashtag1", "#hashtag2", "#hashtag3"]
    }
  ],
  "bio_cta": "teks CTA untuk bio/kolom komentar yang mengarahkan ke produk",
  "posting_tips": ["tips waktu posting terbaik", "tips thumbnail", "tips engagement"]
}
```
Berikan HANYA JSON valid.
PROMPT;
        ['text' => $raw, 'sources' => $sources] = $this->generateTextWithSearch($prompt, 2500, 1.0);
        $result = $this->_parseJson($raw);
        if (!empty($result['parse_error'])) throw new \Exception('AI gagal menghasilkan hook. Coba lagi.');
        if (!empty($sources)) $result['search_sources'] = $sources;
        return $result;
    }

    /** 7. Digital Asset Quality Scanner */
    public function generateQualityBadge(array $p): array
    {
        $snippet   = mb_substr($p['code_or_doc'] ?? '', 0, 2000);
        $assetType = $p['asset_type'] ?? 'digital asset';
        $prompt = <<<PROMPT
Kamu adalah senior software engineer dan technical reviewer Indonesia. Review kualitas aset digital berikut.

Tipe Aset: {$assetType}
Nama: {$p['product_name']}
Deskripsi: {$p['product_desc']}
Cuplikan Kode/Dokumentasi:
---
{$snippet}
---

Lakukan review dan berikan output JSON:
```json
{
  "overall_score": 85,
  "badge": "AI-Verified Quality",
  "badge_level": "Gold/Silver/Bronze/Not Verified",
  "verdict": "ringkasan verdict 1-2 kalimat",
  "criteria": [
    {"name": "Keterbacaan Kode", "score": 80, "status": "Pass/Fail/Warning", "note": "catatan singkat"},
    {"name": "Dokumentasi", "score": 75, "status": "Pass", "note": "catatan"},
    {"name": "Struktur & Organisasi", "score": 90, "status": "Pass", "note": "catatan"},
    {"name": "Best Practices", "score": 85, "status": "Pass", "note": "catatan"},
    {"name": "Keamanan (Security)", "score": 70, "status": "Warning", "note": "catatan"},
    {"name": "Performa", "score": 80, "status": "Pass", "note": "catatan"}
  ],
  "strengths": ["kelebihan 1", "kelebihan 2", "kelebihan 3"],
  "improvements": ["saran perbaikan 1", "saran perbaikan 2"],
  "badge_text": "teks badge marketing yang bisa ditampilkan di halaman produk",
  "verified": true
}
```
Berikan HANYA JSON valid.
PROMPT;
        $result = $this->_parseJson($this->generateText($prompt, 1500, 0.4));
        if (!empty($result['parse_error'])) throw new \Exception('AI gagal menganalisis kualitas aset. Coba lagi.');
        return $result;
    }

    /** 8. Custom Discount Campaign Copywriter */
    public function generateDiscountCampaign(array $p): array
    {
        $origPrice   = $p['original_price'] ?? 'tidak disebutkan';
        $discPrice   = $p['discount_price'] ?? 'tidak disebutkan';
        $discPct     = $p['discount_pct'] ?? '0';
        $duration    = $p['duration'] ?? 'terbatas';
        $platform    = $p['platform'] ?? 'semua platform';
        $waNumber    = $p['wa_number'] ?? '';

        $prompt = <<<PROMPT
Kamu adalah copywriter kampanye promosi Indonesia yang ahli membuat copy diskon yang persuasif dan menghasilkan penjualan.

Nama Promo: {$p['promo_name']}
Produk: {$p['product_name']}
Deskripsi Produk: {$p['product_desc']}
Harga Normal: {$origPrice}
Harga Diskon: {$discPrice}
Persentase Diskon: {$discPct}%
Durasi Promo: {$duration}
Platform: {$platform}
Nomor WA: {$waNumber}

```json
{
  "promo_name": "{$p['promo_name']}",
  "copies": [
    {
      "platform": "Instagram/TikTok",
      "copy": "copy promosi lengkap dengan emoji, urgency, harga coret, dan CTA",
      "hashtags": ["#hashtag1", "#hashtag2", "#hashtag3", "#hashtag4", "#hashtag5"]
    },
    {
      "platform": "WhatsApp Broadcast",
      "copy": "copy WA broadcast yang personal dan langsung to the point"
    },
    {
      "platform": "Facebook/LinkedIn",
      "copy": "copy lebih formal dan informatif"
    },
    {
      "platform": "SMS/WA Status",
      "copy": "copy super singkat max 160 karakter"
    }
  ],
  "countdown_text": "teks countdown urgency yang bisa dipakai di story/banner",
  "wa_broadcast_link": "link wa.me dengan pesan promo pre-filled",
  "banner_headline": "headline untuk banner/flyer max 8 kata",
  "banner_subtext": "subtext banner max 15 kata"
}
```
Berikan HANYA JSON valid.
PROMPT;
        ['text' => $raw, 'sources' => $sources] = $this->generateTextWithSearch($prompt, 2000, 1.0);
        $result = $this->_parseJson($raw);
        if (!empty($result['parse_error'])) throw new \Exception('AI gagal menghasilkan kampanye diskon. Coba lagi.');
        // Build actual WA link
        $waNum = preg_replace('/\D/', '', $waNumber);
        if ($waNum && isset($result['copies'][1]['copy'])) {
            $result['wa_broadcast_link'] = "https://wa.me/{$waNum}?text=" . rawurlencode($result['copies'][1]['copy']);
        }
        if (!empty($sources)) $result['search_sources'] = $sources;
        return $result;
    }

    /** 9. Trend-Based Product Tagging */
    public function generateTrendTags(array $p): array
    {
        $category    = $p['category'] ?? 'teknologi';
        $currentTags = $p['current_tags'] ?? 'belum ada tag';

        $prompt = <<<PROMPT
Kamu adalah digital marketing strategist Indonesia yang paham tren teknologi dan pasar saat ini (2026).

Produk: {$p['product_name']}
Deskripsi: {$p['product_desc']}
Kategori: {$category}
Tag Saat Ini: {$currentTags}

Analisis relevansi produk dengan tren terkini dan sarankan tag yang akan meningkatkan visibilitas.

```json
{
  "trending_tags": [
    {"tag": "nama tag trending", "trend_score": 95, "relevance": "High/Medium/Low", "reason": "alasan relevan dengan produk ini", "search_volume": "estimasi volume pencarian"},
    {"tag": "tag 2", "trend_score": 88, "relevance": "High", "reason": "alasan", "search_volume": "estimasi"},
    {"tag": "tag 3", "trend_score": 82, "relevance": "Medium", "reason": "alasan", "search_volume": "estimasi"},
    {"tag": "tag 4", "trend_score": 78, "relevance": "High", "reason": "alasan", "search_volume": "estimasi"},
    {"tag": "tag 5", "trend_score": 72, "relevance": "Medium", "reason": "alasan", "search_volume": "estimasi"}
  ],
  "recommended_tags": ["tag yang paling direkomendasikan untuk ditambahkan sekarang"],
  "remove_tags": ["tag lama yang sudah tidak relevan jika ada"],
  "trend_alert": "peringatan tren yang sedang naik dan sangat relevan dengan produk ini",
  "action_urgency": "High/Medium/Low",
  "seo_impact": "estimasi dampak ke SEO jika tag diterapkan",
  "best_time_to_post": "waktu terbaik posting konten dengan tag ini"
}
```
Berikan HANYA JSON valid.
PROMPT;
        ['text' => $raw, 'sources' => $sources] = $this->generateTextWithSearch($prompt, 1500, 1.0);
        $result = $this->_parseJson($raw);
        if (!empty($result['parse_error'])) throw new \Exception('AI gagal menghasilkan trend tags. Coba lagi.');
        if (!empty($sources)) $result['search_sources'] = $sources;
        return $result;
    }

    /** 10. AI Lead Magnet Creator */
    public function generateLeadMagnet(array $p): array
    {
        $price    = $p['price'] ?? 'tidak disebutkan';
        $audience = $p['target_audience'] ?? 'umum';
        $goal     = $p['goal'] ?? 'kumpulkan email';
        $waNumber = $p['wa_number'] ?? '';

        $prompt = <<<PROMPT
Kamu adalah growth hacker dan lead generation expert Indonesia. Bantu seller membuat lead magnet yang efektif.

Produk/Jasa: {$p['product_name']}
Deskripsi: {$p['product_desc']}
Harga Produk: {$price}
Target Audience: {$audience}
Tujuan Lead: {$goal}

Balas HANYA dengan JSON valid berikut:
```json
{
  "magnet_title": "judul lead magnet terbaik yang direkomendasikan",
  "magnet_desc": "deskripsi singkat apa yang didapat calon pembeli",
  "format": "PDF Guide / Checklist / Template / Mini Course / Free Sample / Webinar",
  "effort_level": "Low / Medium / High",
  "conversion_potential": "High / Medium / Low",
  "summary": "satu kalimat kenapa lead magnet ini efektif untuk produk ini",
  "include_items": [
    {"item": "konten/bagian yang dimasukkan ke lead magnet", "reason": "kenapa bagian ini menarik"},
    {"item": "konten 2", "reason": "alasan"},
    {"item": "konten 3", "reason": "alasan"}
  ],
  "landing_copy": {
    "headline": "headline halaman opt-in",
    "subheadline": "subheadline yang memperkuat",
    "bullets": ["benefit 1", "benefit 2", "benefit 3"],
    "cta_button": "teks tombol CTA"
  },
  "followup_sequence": [
    {"timing": "Langsung setelah opt-in", "subject": "judul pesan", "preview": "preview isi pesan"},
    {"timing": "Hari ke-1", "subject": "judul", "preview": "preview"},
    {"timing": "Hari ke-3", "subject": "judul soft sell", "preview": "preview"},
    {"timing": "Hari ke-7", "subject": "judul penawaran", "preview": "preview"}
  ],
  "wa_link": "https://wa.me/NOMOR?text=pesan+optin+yang+sudah+di-encode"
}
```
PROMPT;
        ['text' => $raw, 'sources' => $sources] = $this->generateTextWithSearch($prompt, 2500, 0.8);
        $result = $this->_parseJson($raw);

        if (!empty($result['parse_error'])) {
            throw new \Exception('AI tidak dapat menghasilkan format yang valid. Coba lagi.');
        }

        // Build WA link if wa_number provided and not already set
        if ($waNumber && empty($result['wa_link'])) {
            $num = preg_replace('/\D/', '', $waNumber);
            $msg = $result['landing_copy']['cta_button'] ?? 'Saya mau dapat free resource';
            $result['wa_link'] = "https://wa.me/{$num}?text=" . rawurlencode($msg);
        }

        if (!empty($sources)) $result['search_sources'] = $sources;

        return $result;
    }
    /** Shared JSON parser helper */
    private function _parseJson(string $raw): array
    {
        // 1. Try JSON code fence first
        if (preg_match('/```(?:json)?\s*([\s\S]+?)\s*```/', $raw, $m)) {
            $decoded = json_decode(trim($m[1]), true);
            if (is_array($decoded)) return $decoded;
        }

        // 2. Try to find outermost JSON object
        if (preg_match('/\{[\s\S]+\}/u', $raw, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) return $decoded;
        }

        // 3. Try to find outermost JSON array (fallback)
        if (preg_match('/\[[\s\S]+\]/u', $raw, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) return ['items' => $decoded];
        }

        // 4. Try the raw string directly
        $decoded = json_decode(trim($raw), true);
        if (is_array($decoded)) return $decoded;

        return ['raw' => $raw, 'parse_error' => true];
    }
}
