<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Str;
use Exception;

class ArticleGeneratorService
{
    protected $geminiService;
    protected $industries = ['fashion', 'food', 'beauty', 'tech', 'lifestyle', 'health'];
    
    // Rotation pattern: Day 1 = Industry, Day 2 = Tips, Day 3 = Quote (repeats every 3 days)
    protected $rotationPattern = [
        1 => 'industry',
        2 => 'tips',
        3 => 'quote',
    ];

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Generate 1 article per day with rotation pattern
     * Day 1: Industry article (random industry)
     * Day 2: Tips article (digital marketing tips)
     * Day 3: Quote article (inspirational quote)
     * Pattern repeats every 3 days
     */
    public function generateDailyArticles()
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'articles' => [],
            'errors' => [],
        ];

        try {
            $dayInCycle = $this->getDayInCycle();
            $articleType = $this->rotationPattern[$dayInCycle];
            
            $article = $this->generateArticleByType($articleType);

            if ($article) {
                $results['success']++;
                $results['articles'][] = [
                    'id' => $article->id,
                    'title' => $article->title,
                    'type' => $articleType,
                    'industry' => $article->industry,
                ];
                
                \Log::info("✅ Daily article generated", [
                    'type' => $articleType,
                    'day_in_cycle' => $dayInCycle,
                    'article_id' => $article->id,
                ]);
            } else {
                $results['failed']++;
                $results['errors'][] = "Failed to generate {$articleType} article";
            }
        } catch (Exception $e) {
            $results['failed']++;
            $results['errors'][] = "Error: {$e->getMessage()}";
            \Log::error("❌ Daily article generation failed", ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Generate article based on type
     */
    protected function generateArticleByType($type)
    {
        if ($type === 'industry') {
            $industry = $this->getRandomIndustry();
            return $this->generateIndustryArticle($industry);
        } elseif ($type === 'tips') {
            return $this->generateTipsArticle();
        } elseif ($type === 'quote') {
            return $this->generateQuoteArticle();
        }

        return null;
    }

    /**
     * Generate industry article
     */
    protected function generateIndustryArticle($industry)
    {
        $industryIndo = $this->translateIndustry($industry);
        
        // Strategy: Generate article in one go with very explicit instructions
        $content = $this->generateContent(
            "Kamu adalah penulis artikel profesional. Tugas kamu adalah menulis artikel LENGKAP tentang tren terbaru di industri {$industryIndo}.

PENTING: Artikel harus LENGKAP dan TIDAK BOLEH TERPOTONG!

Tulis artikel dengan struktur berikut (WAJIB LENGKAP):

**Judul: Tren Terbaru di Industri {$industryIndo} 2026**

**Paragraf 1 - Pembukaan (100-150 kata):**
Mulai dengan hook menarik tentang perkembangan industri {$industryIndo} di Indonesia. Jelaskan mengapa UMKM perlu memperhatikan tren ini.

**Paragraf 2 - Tren Pertama (150-200 kata):**
Jelaskan tren pertama yang sedang berkembang di industri {$industryIndo}. Berikan contoh konkret dan bagaimana UMKM bisa memanfaatkannya.

**Paragraf 3 - Tren Kedua (150-200 kata):**
Jelaskan tren kedua yang penting untuk diperhatikan. Sertakan data atau insight yang relevan untuk UMKM Indonesia.

**Paragraf 4 - Tips Praktis (100-150 kata):**
Berikan 3-5 tips praktis yang bisa langsung diterapkan oleh UMKM untuk mengikuti tren ini.

**Paragraf 5 - Penutup (50-100 kata):**
Kesimpulan yang memotivasi dan call-to-action yang jelas untuk UMKM.

ATURAN PENULISAN:
- Gunakan Bahasa Indonesia yang mudah dipahami
- Fokus pada UMKM Indonesia
- Berikan contoh konkret dan actionable
- Total minimal 500 kata
- TULIS SAMPAI SELESAI, jangan berhenti di tengah

Sekarang tulis artikel LENGKAP:"
        );

        if (!$content || str_word_count($content) < 300) {
            // If content too short or failed, try fallback with simpler prompt
            \Log::warning("Article too short, trying fallback", ['words' => str_word_count($content ?? '')]);
            
            $content = $this->generateContent(
                "Tulis artikel lengkap 500 kata dalam Bahasa Indonesia tentang tren industri {$industryIndo} untuk UMKM. 
                
                Struktur:
                1. Pembukaan menarik
                2. 2-3 tren utama dengan penjelasan detail
                3. Tips praktis untuk UMKM
                4. Kesimpulan dan motivasi
                
                Tulis artikel lengkap sekarang, jangan berhenti sampai selesai:"
            );
        }

        if (!$content) {
            return null;
        }

        $title = "Tren Terbaru di Industri " . ucfirst($industryIndo) . " 2026";
        $slug = $this->generateUniqueSlug($title);
        $description = Str::limit(strip_tags($content), 160);
        $keywords = "{$industryIndo}, tren {$industryIndo}, bisnis {$industryIndo}, tips {$industryIndo}";

        $article = Article::create([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'description' => $description,
            'keywords' => $keywords,
            'category' => 'industry',
            'industry' => $industry,
            'day_number' => $this->getDayInCycle(),
        ]);
        
        // Feed to ML system for learning
        $this->feedToMLSystem($article);
        
        return $article;
    }

    /**
     * Generate tips article (digital marketing tips)
     */
    protected function generateTipsArticle()
    {
        // Variasi topik tips agar tidak monoton
        $tipsTopics = [
            'Digital Marketing' => 'digital marketing untuk meningkatkan penjualan online',
            'Social Media' => 'social media marketing untuk UMKM',
            'Content Creation' => 'membuat konten yang engaging dan viral',
            'Instagram Marketing' => 'Instagram marketing untuk bisnis kecil',
            'TikTok Marketing' => 'TikTok marketing untuk UMKM',
            'Copywriting' => 'copywriting yang menjual untuk caption produk',
            'Branding' => 'membangun brand awareness untuk UMKM',
            'Customer Engagement' => 'meningkatkan engagement dengan pelanggan',
            'Video Marketing' => 'video marketing untuk social media',
            'Email Marketing' => 'email marketing untuk UMKM',
        ];
        
        // Pilih topik secara random tapi track agar tidak berulang dalam 30 hari
        $recentTopics = \App\Models\Article::where('category', 'tips')
            ->where('created_at', '>=', now()->subDays(30))
            ->pluck('title')
            ->toArray();
        
        // Filter topik yang belum digunakan
        $availableTopics = array_filter($tipsTopics, function($topic, $key) use ($recentTopics) {
            foreach ($recentTopics as $recentTitle) {
                if (stripos($recentTitle, $key) !== false) {
                    return false;
                }
            }
            return true;
        }, ARRAY_FILTER_USE_BOTH);
        
        // Jika semua topik sudah digunakan, reset
        if (empty($availableTopics)) {
            $availableTopics = $tipsTopics;
        }
        
        $selectedTopic = array_rand($availableTopics);
        $topicDescription = $availableTopics[$selectedTopic];
        
        $content = $this->generateContent(
            "Tulis artikel tips praktis tentang {$topicDescription} dalam Bahasa Indonesia untuk UMKM dan content creator.
            
            WAJIB:
            - MINIMAL 500 KATA
            - Berisi 7-10 tips praktis dan actionable
            - Setiap tip dijelaskan dengan detail dan contoh konkret
            - Menggunakan bahasa yang mudah dipahami
            - Relevan untuk bisnis kecil di Indonesia
            - Format dengan numbering yang jelas
            
            Struktur:
            **Paragraf 1 - Pembukaan (100 kata):**
            Jelaskan pentingnya {$topicDescription} untuk UMKM di era digital.
            
            **Paragraf 2-4 - Tips Praktis (300-400 kata):**
            Berikan 7-10 tips dengan penjelasan detail dan contoh. Format:
            1. [Tip pertama] - Penjelasan dan contoh
            2. [Tip kedua] - Penjelasan dan contoh
            ... dst
            
            **Paragraf 5 - Penutup (100 kata):**
            Kesimpulan dan motivasi untuk mulai menerapkan tips.
            
            Tulis artikel lengkap sekarang:"
        );

        if (!$content) {
            return null;
        }

        $title = "Tips {$selectedTopic} untuk UMKM 2026";
        $slug = $this->generateUniqueSlug($title);
        $description = "Temukan tips {$topicDescription} praktis untuk meningkatkan bisnis Anda.";
        $keywords = "{$selectedTopic}, tips marketing, strategi konten, social media marketing, UMKM";

        $article = Article::create([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'description' => $description,
            'keywords' => $keywords,
            'category' => 'tips',
            'industry' => 'marketing',
            'day_number' => $this->getDayInCycle(),
        ]);
        
        // Feed to ML system for learning
        $this->feedToMLSystem($article);
        
        return $article;
    }

    /**
     * Generate quote article
     */
    protected function generateQuoteArticle()
    {
        // Variasi tema quote agar tidak monoton
        $quoteThemes = [
            'Kegigihan' => 'kegigihan dan pantang menyerah dalam berbisnis',
            'Inovasi' => 'inovasi dan kreativitas dalam bisnis',
            'Kepemimpinan' => 'kepemimpinan dan memimpin tim',
            'Kesuksesan' => 'meraih kesuksesan dalam bisnis',
            'Kegagalan' => 'belajar dari kegagalan dan bangkit kembali',
            'Kerja Keras' => 'kerja keras dan dedikasi',
            'Mimpi' => 'mengejar mimpi dan passion',
            'Perubahan' => 'menghadapi perubahan dan adaptasi',
            'Kesempatan' => 'memanfaatkan kesempatan dalam bisnis',
            'Kolaborasi' => 'kolaborasi dan teamwork',
        ];
        
        // Pilih tema secara random tapi track agar tidak berulang dalam 30 hari
        $recentThemes = \App\Models\Article::where('category', 'quote')
            ->where('created_at', '>=', now()->subDays(30))
            ->pluck('content')
            ->toArray();
        
        $selectedTheme = array_rand($quoteThemes);
        $themeDescription = $quoteThemes[$selectedTheme];
        
        $content = $this->generateContent(
            "Tulis artikel inspirasi dengan quote motivasi tentang {$themeDescription} dalam Bahasa Indonesia untuk content creator dan entrepreneur.
            
            WAJIB:
            - MINIMAL 400 KATA
            - Berisi 1 quote inspiratif yang powerful dan memorable
            - Penjelasan mendalam tentang makna quote tersebut
            - Bagaimana menerapkan quote dalam bisnis/kehidupan
            - Contoh konkret dan cerita inspiratif
            - Menggunakan bahasa yang menyentuh dan memotivasi
            
            Struktur:
            **Paragraf 1 - Quote (50 kata):**
            Tampilkan quote yang powerful tentang {$themeDescription}. Format:
            > \"[Quote yang inspiratif]\"
            > — [Nama tokoh/penulis]
            
            **Paragraf 2 - Makna Quote (150 kata):**
            Jelaskan makna mendalam dari quote tersebut dan relevansinya untuk entrepreneur Indonesia.
            
            **Paragraf 3 - Penerapan Praktis (150 kata):**
            Bagaimana menerapkan wisdom dari quote ini dalam bisnis sehari-hari. Berikan contoh konkret.
            
            **Paragraf 4 - Penutup (50 kata):**
            Motivasi dan call-to-action untuk mulai menerapkan.
            
            Tulis artikel lengkap sekarang:"
        );

        if (!$content) {
            return null;
        }

        $title = "Inspirasi Harian: Quote tentang {$selectedTheme} untuk Creator";
        $slug = $this->generateUniqueSlug($title);
        $description = "Dapatkan inspirasi dengan quote motivasi tentang {$themeDescription} untuk entrepreneur Indonesia.";
        $keywords = "inspirasi, motivasi, quote, {$selectedTheme}, entrepreneurship, content creator, UMKM";

        $article = Article::create([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'description' => $description,
            'keywords' => $keywords,
            'category' => 'quote',
            'industry' => 'general',
            'day_number' => $this->getDayInCycle(),
        ]);
        
        // Feed to ML system for learning
        $this->feedToMLSystem($article);
        
        return $article;
    }

    /**
     * Generate content using Gemini API with continuation support
     */
    protected function generateContent($prompt)
    {
        try {
            // First attempt - generate article
            $response = $this->geminiService->generateText($prompt, 3000, 0.8);
            
            $wordCount = str_word_count($response);
            \Log::info("Article generated (first attempt)", ['word_count' => $wordCount]);
            
            // Check if article seems incomplete (ends abruptly)
            $lastWords = substr($response, -100);
            $endsAbruptly = !preg_match('/[.!?]\s*$/', trim($response)); // Doesn't end with punctuation
            
            // If article is too short OR ends abruptly, try to continue
            if ($wordCount < 400 || $endsAbruptly) {
                \Log::warning("Article seems incomplete, attempting continuation", [
                    'word_count' => $wordCount,
                    'ends_abruptly' => $endsAbruptly,
                    'last_100_chars' => $lastWords
                ]);
                
                // Try to continue the article
                $continuePrompt = "Lanjutkan artikel berikut sampai selesai dengan kesimpulan yang lengkap:\n\n" . $response . "\n\n(Lanjutkan dari kalimat terakhir dan selesaikan artikel dengan kesimpulan yang kuat)";
                
                $continuation = $this->geminiService->generateText($continuePrompt, 2000, 0.8);
                
                if ($continuation && str_word_count($continuation) > 50) {
                    // Merge the continuation
                    $response = $response . "\n\n" . $continuation;
                    $finalWordCount = str_word_count($response);
                    \Log::info("Article continued successfully", ['final_word_count' => $finalWordCount]);
                }
            }
            
            // Clean up the response - remove preamble and meta text
            $response = $this->cleanArticleContent($response);
            
            return $response;
        } catch (Exception $e) {
            \Log::error("Gemini API error: {$e->getMessage()}");
            return null;
        }
    }
    
    /**
     * Clean article content by removing preamble and meta text
     */
    protected function cleanArticleContent($content)
    {
        // Remove common preambles
        $patterns = [
            '/^(Tentu|Baik|Oke|Ok),?\s+(berikut|ini)\s+(adalah|ialah)?\s+artikel.*?:\s*/is',
            '/^\*\*Judul:.*?\*\*\s*/is',
            '/^Judul:.*?\n/is',
            '/^Artikel.*?:\s*/is',
            '/^Berikut.*?artikel.*?:\s*/is',
        ];
        
        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }
        
        // Trim whitespace
        $content = trim($content);
        
        return $content;
    }
    
    /**
     * Translate industry to Indonesian
     */
    protected function translateIndustry($industry)
    {
        $translations = [
            'fashion' => 'Fashion',
            'food' => 'Kuliner',
            'beauty' => 'Kecantikan',
            'tech' => 'Teknologi',
            'lifestyle' => 'Lifestyle',
            'health' => 'Kesehatan',
        ];
        
        return $translations[$industry] ?? ucfirst($industry);
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug($title)
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Get current day in 3-day cycle (1, 2, or 3)
     */
    protected function getDayInCycle()
    {
        $dayOfYear = now()->dayOfYear;
        $dayInCycle = (($dayOfYear - 1) % 3) + 1;
        return $dayInCycle;
    }

    /**
     * Get random industry
     */
    protected function getRandomIndustry()
    {
        return $this->industries[array_rand($this->industries)];
    }

    /**
     * Get all industries
     */
    public function getIndustries()
    {
        return $this->industries;
    }
    
    /**
     * Feed article to ML system for learning
     */
    protected function feedToMLSystem($article)
    {
        try {
            // Extract keywords and patterns from article
            $content = $article->content;
            $category = $article->category;
            $industry = $article->industry;
            
            // Analyze article for ML patterns
            $patterns = $this->extractMLPatterns($content);
            
            // Store in ML optimized data for future use
            \App\Models\MLOptimizedData::create([
                'industry' => $industry,
                'platform' => 'article', // Special platform for articles
                'data_type' => $category, // industry, tips, or quote
                'content' => json_encode([
                    'article_id' => $article->id,
                    'title' => $article->title,
                    'keywords' => $article->keywords,
                    'word_count' => str_word_count($content),
                    'patterns' => $patterns,
                    'excerpt' => Str::limit($content, 200),
                ]),
                'performance_score' => 0, // Will be updated based on views/engagement
                'last_trained_at' => now(),
            ]);
            
            \Log::info("Article fed to ML system", [
                'article_id' => $article->id,
                'category' => $category,
                'industry' => $industry,
                'patterns_count' => count($patterns)
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Failed to feed article to ML system", [
                'article_id' => $article->id ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Extract ML patterns from article content
     */
    protected function extractMLPatterns($content)
    {
        $patterns = [];
        
        // Extract common phrases (3-5 words)
        preg_match_all('/\b(\w+\s+\w+\s+\w+)\b/u', $content, $matches);
        $phrases = array_count_values($matches[1]);
        arsort($phrases);
        $patterns['top_phrases'] = array_slice(array_keys($phrases), 0, 10);
        
        // Extract sentence structures
        $sentences = preg_split('/[.!?]+/', $content);
        $patterns['avg_sentence_length'] = count($sentences) > 0 ? str_word_count($content) / count($sentences) : 0;
        $patterns['total_sentences'] = count($sentences);
        
        // Extract writing style indicators
        $patterns['has_questions'] = substr_count($content, '?') > 0;
        $patterns['has_lists'] = preg_match('/\d+\.\s/', $content) > 0;
        $patterns['has_quotes'] = substr_count($content, '"') > 0 || substr_count($content, '«') > 0;
        
        // Extract emotional tone indicators
        $motivationalWords = ['sukses', 'hebat', 'luar biasa', 'mantap', 'keren', 'wow', 'amazing'];
        $motivationalCount = 0;
        foreach ($motivationalWords as $word) {
            $motivationalCount += substr_count(strtolower($content), $word);
        }
        $patterns['motivational_score'] = min(10, $motivationalCount);
        
        return $patterns;
    }
}
