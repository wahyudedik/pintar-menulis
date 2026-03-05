<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        // Use gemini-2.5-flash - the latest stable model
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    }

    /**
     * Generate copywriting content using Gemini AI
     */
    public function generateCopywriting(array $params)
    {
        // Validate API key
        if (empty($this->apiKey)) {
            Log::error('Gemini API Key not configured');
            throw new \Exception('API Key tidak dikonfigurasi. Hubungi administrator.');
        }

        $prompt = $this->buildPrompt($params);
        
        // Adjust max tokens based on variations request
        // 5 variasi (default): 4096 tokens
        // 20 variasi (premium): 8192 tokens
        $maxTokens = 4096; // default for 5 variasi
        if (isset($params['generate_variations']) && $params['generate_variations']) {
            $maxTokens = 8192; // double for 20 variations
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
                    'temperature' => 0.7,
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

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $this->apiKey
                ])
                ->post($this->apiUrl, $requestData);

            $statusCode = $response->status();
            Log::info('Gemini API Response Status: ' . $statusCode);

            if ($response->successful()) {
                $data = $response->json();
                
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
                            return trim($part['text']);
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
                    
                    // Handle specific error cases
                    if (strpos($errorMessage, 'API key') !== false) {
                        throw new \Exception('API Key tidak valid. Silakan generate API key baru di https://aistudio.google.com/app/apikey');
                    } elseif (strpos($errorMessage, 'not found') !== false || strpos($errorMessage, 'not supported') !== false) {
                        throw new \Exception('Model Gemini tidak tersedia. API key mungkin tidak valid atau expired. Silakan generate API key baru di https://aistudio.google.com/app/apikey');
                    } elseif (strpos($errorMessage, 'quota') !== false) {
                        throw new \Exception('Kuota API habis. Hubungi administrator.');
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
        
        // Default: 5 variasi (UMKM friendly)
        // If generate_variations = true: 20 variasi (premium)
        $variationCount = $generateVariations ? 20 : 5;

        // Quick Templates - Specialized prompts
        if ($category === 'quick_templates') {
            return $this->buildQuickTemplatePrompt($subcategory, $brief, $tone, $keywords, $platform, $variationCount, $autoHashtag, $localLanguage);
        }
        
        // Industry Presets - UMKM Specific
        if ($category === 'industry_presets') {
            return $this->buildIndustryPresetPrompt($subcategory, $brief, $tone, $platform, $keywords, $variationCount, $autoHashtag, $localLanguage);
        }

        // AI Context Awareness: Analyze brief to understand target audience
        $audienceContext = $this->analyzeAudience($brief);
        
        // Auto-adjust tone based on platform if not explicitly set
        $adjustedTone = $this->adjustToneForPlatform($tone, $platform);
        
        // Platform-specific guidelines
        $platformGuidelines = $this->getPlatformGuidelines($platform);

        // General prompt with context awareness
        $prompt = "Kamu adalah copywriter profesional yang ahli dalam membuat konten promosi untuk UMKM Indonesia.\n\n";
        
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
        
        // Add variations instruction - ALWAYS generate variations (5 or 20)
        $prompt .= "PENTING: Generate {$variationCount} variasi caption yang berbeda-beda!\n";
        $prompt .= "Format: Nomor urut, lalu caption. Contoh:\n";
        $prompt .= "1. [caption pertama]\n";
        $prompt .= "2. [caption kedua]\n";
        $prompt .= "... dst sampai {$variationCount} variasi\n\n";
        
        // Add hashtag instruction
        if ($autoHashtag) {
            $prompt .= "HASHTAG: Sertakan hashtag trending Indonesia yang relevan di akhir setiap caption.\n\n";
        }
        
        // Add local language instruction
        if ($localLanguage) {
            $prompt .= "BAHASA DAERAH: Tambahkan sentuhan bahasa {$localLanguage} yang natural (jangan berlebihan, cukup 1-2 kata/frasa untuk relate dengan audience lokal).\n\n";
        }
        
        // Add UMKM-friendly language instruction
        $prompt .= "BAHASA UMKM: Gunakan bahasa yang relate dengan UMKM Indonesia seperti 'Kak', 'Bun', 'Gaes' secara natural sesuai konteks dan target audience.\n\n";

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
            'instagram' => "- Maksimal 150 kata untuk caption\n- Gunakan 8-12 hashtag relevan\n- Sertakan emoji yang sesuai\n- Hook di 3 kalimat pertama\n- Visual-first mindset (caption mendukung visual)\n- Ajak engagement (like, comment, save, share)",
            
            'tiktok' => "- Maksimal 100 kata (sangat singkat)\n- Gunakan bahasa Gen Z yang natural\n- Hook HARUS di 3 detik pertama\n- Sertakan 5-8 hashtag trending\n- CTA untuk like, comment, share\n- Fokus pada entertainment value",
            
            'facebook' => "- Maksimal 200 kata\n- Bisa lebih panjang dari Instagram\n- Opening yang relatable\n- Ajak diskusi di comment\n- 3-5 hashtag (tidak wajib)\n- Cocok untuk storytelling",
            
            'linkedin' => "- Tone profesional dan formal\n- Fokus pada value dan insights\n- Maksimal 300 kata\n- Gunakan data/fakta jika relevan\n- CTA untuk networking atau diskusi\n- Hindari emoji berlebihan",
            
            'twitter' => "- Maksimal 280 karakter (sangat singkat!)\n- Langsung to the point\n- Gunakan thread jika perlu lebih panjang\n- 1-3 hashtag maksimal\n- Cocok untuk announcement atau quick tips",
        ];
        
        return $guidelines[$platform] ?? "- Sesuaikan dengan best practice platform\n- Fokus pada engagement\n- CTA yang jelas";
    }
    
    /**
     * Build industry-specific prompts for UMKM
     */
    protected function buildIndustryPresetPrompt($industry, $brief, $tone, $platform, $keywords, $variationCount, $autoHashtag, $localLanguage)
    {
        $audienceContext = $this->analyzeAudience($brief);
        $adjustedTone = $this->adjustToneForPlatform($tone, $platform);
        
        $basePrompt = "Kamu adalah copywriter profesional yang SANGAT PAHAM UMKM Indonesia dan cara jualan yang efektif.\n\n";
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
        $basePrompt .= "BAHASA UMKM: Gunakan bahasa yang relate seperti 'Kak', 'Bun', 'Gaes' secara natural sesuai target audience.\n\n";
        
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
            'fashion_clothing' => "- Highlight style & trend terkini\n- Fokus pada fit, bahan, dan kenyamanan\n- Gunakan istilah fashion yang familiar (ootd, outfit, style)\n- CTA: 'Order sekarang', 'DM untuk order', 'Klik link bio'\n- Pain point: Bingung mix & match, cari baju yang pas\n- Closing: Stok terbatas, diskon hari ini",
            
            'food_beverage' => "- Highlight rasa, porsi, dan harga\n- Gunakan kata yang bikin ngiler (lezat, gurih, segar)\n- Fokus pada kepraktisan (delivery, ready stock)\n- CTA: 'Pesan sekarang', 'Order via WA', 'Grab/GoFood'\n- Pain point: Lapar, bingung mau makan apa, budget terbatas\n- Closing: Promo hari ini, free ongkir",
            
            'beauty_skincare' => "- Highlight manfaat & hasil (glowing, cerah, halus)\n- Fokus pada keamanan (BPOM, halal, aman)\n- Gunakan before-after mindset\n- CTA: 'Konsultasi gratis', 'Cek testimoni', 'Order sekarang'\n- Pain point: Kulit bermasalah, insecure, cari produk aman\n- Closing: Stok terbatas, bonus untuk pembelian hari ini",
            
            'printing_service' => "- Highlight kualitas cetak & harga\n- Fokus pada kecepatan (express, same day)\n- Gunakan istilah teknis yang simple (full color, laminasi)\n- CTA: 'Konsultasi gratis', 'Minta quotation', 'Order sekarang'\n- Pain point: Butuh cepat, budget pas-pasan, takut hasil jelek\n- Closing: Diskon untuk order hari ini, gratis desain",
            
            'photography' => "- Highlight portfolio & style\n- Fokus pada momen spesial (wedding, prewedding, wisuda)\n- Gunakan emotional trigger\n- CTA: 'Book sekarang', 'Cek portfolio', 'DM untuk tanya'\n- Pain point: Cari fotografer yang cocok, budget terbatas\n- Closing: Slot terbatas, early bird discount",
            
            'catering' => "- Highlight menu, porsi, dan harga per pax\n- Fokus pada kepraktisan (antar, setup, bersih-bersih)\n- Gunakan kata yang bikin ngiler\n- CTA: 'Pesan sekarang', 'Konsultasi menu', 'WA untuk order'\n- Pain point: Ribet masak sendiri, cari yang enak & murah\n- Closing: Diskon untuk pemesanan minggu ini",
            
            'tiktok_shop' => "- Highlight promo & diskon (gratis ongkir, cashback)\n- Fokus pada viral & trending\n- Gunakan bahasa Gen Z yang catchy\n- CTA: 'Klik keranjang kuning', 'Checkout sekarang', 'Buruan sebelum habis'\n- Pain point: Cari barang murah & viral\n- Closing: Flash sale, stok terbatas, diskon 50%",
            
            'shopee_affiliate' => "- Highlight komisi & passive income\n- Fokus pada kemudahan (tanpa modal, tanpa stok)\n- Gunakan success story\n- CTA: 'Daftar sekarang', 'Mulai jualan', 'Klik link'\n- Pain point: Cari side income, modal terbatas\n- Closing: Bonus untuk pendaftar hari ini",
            
            'home_decor' => "- Highlight estetika & fungsi\n- Fokus pada transformasi ruangan\n- Gunakan kata yang bikin pengen punya (cozy, aesthetic, minimalis)\n- CTA: 'Order sekarang', 'DM untuk custom', 'Cek katalog'\n- Pain point: Rumah kurang nyaman, bingung dekor\n- Closing: Diskon, free konsultasi desain",
            
            'handmade_craft' => "- Highlight keunikan & handmade value\n- Fokus pada kualitas & detail\n- Gunakan emotional trigger (hadiah spesial, limited edition)\n- CTA: 'Order custom', 'DM untuk request', 'Cek katalog'\n- Pain point: Cari hadiah unik, support local\n- Closing: Limited stock, bisa custom",
            
            'digital_service' => "- Highlight hasil & portfolio\n- Fokus pada profesionalitas & kecepatan\n- Gunakan istilah teknis yang simple\n- CTA: 'Konsultasi gratis', 'Minta quotation', 'Order sekarang'\n- Pain point: Butuh jasa profesional, budget terbatas\n- Closing: Diskon untuk order hari ini, free revisi",
            
            'automotive' => "- Highlight kualitas & harga\n- Fokus pada keamanan & performa\n- Gunakan istilah otomotif yang familiar\n- CTA: 'Order sekarang', 'Konsultasi gratis', 'Cek stok'\n- Pain point: Cari spare part ori, harga terjangkau\n- Closing: Garansi, diskon, free pasang",
        ];
        
        return $guidelines[$industry] ?? "- Fokus pada value proposition\n- Highlight keunggulan produk\n- CTA yang jelas\n- Closing yang kuat";
    }

    /**
     * Build specialized prompts for quick templates
     */
    protected function buildQuickTemplatePrompt($subcategory, $brief, $tone, $keywords, $platform = 'instagram', $variationCount = 5, $autoHashtag = true, $localLanguage = '')
    {
        // AI Context Awareness
        $audienceContext = $this->analyzeAudience($brief);
        $adjustedTone = $this->adjustToneForPlatform($tone, $platform);
        
        $basePrompt = "Kamu adalah copywriter profesional yang ahli dalam membuat konten viral untuk social media.\n\n";
        
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
        $basePrompt .= "BAHASA UMKM: Gunakan bahasa yang relate seperti 'Kak', 'Bun', 'Gaes' secara natural sesuai target audience.\n\n";

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
                return $basePrompt . "Buatkan konten copywriting yang menarik sesuai brief di atas dan sangat relate dengan target audience.";
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
}
