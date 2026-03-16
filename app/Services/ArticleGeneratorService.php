<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Str;
use Exception;

class ArticleGeneratorService
{
    protected $geminiService;

    // ── 30 industri/niche bisnis — tidak akan habis dalam sebulan ──────────
    protected $industries = [
        'fashion'         => 'Fashion & Pakaian',
        'food'            => 'Kuliner & F&B',
        'beauty'          => 'Kecantikan & Skincare',
        'tech'            => 'Teknologi & Software',
        'lifestyle'       => 'Lifestyle & Wellness',
        'health'          => 'Kesehatan & Medis',
        'education'       => 'Pendidikan & Kursus Online',
        'finance'         => 'Keuangan & Investasi',
        'property'        => 'Properti & Real Estate',
        'automotive'      => 'Otomotif & Kendaraan',
        'travel'          => 'Travel & Pariwisata',
        'photography'     => 'Fotografi & Videografi',
        'handicraft'      => 'Kerajinan Tangan & Handmade',
        'pet'             => 'Hewan Peliharaan & Pet Care',
        'sports'          => 'Olahraga & Fitness',
        'gaming'          => 'Gaming & Esports',
        'music'           => 'Musik & Entertainment',
        'agriculture'     => 'Pertanian & Agribisnis',
        'logistics'       => 'Logistik & Ekspedisi',
        'printing'        => 'Percetakan & Desain Grafis',
        'wedding'         => 'Wedding & Event Organizer',
        'childcare'       => 'Parenting & Produk Anak',
        'homecare'        => 'Perawatan Rumah & Cleaning',
        'legal'           => 'Jasa Hukum & Konsultasi',
        'hr'              => 'HR & Rekrutmen',
        'ecommerce'       => 'E-commerce & Marketplace',
        'saas'            => 'SaaS & Aplikasi Bisnis',
        'freelance'       => 'Freelance & Jasa Digital',
        'nonprofit'       => 'Sosial & Nirlaba',
        'media'           => 'Media & Jurnalisme',
    ];

    // ── 30 topik tips marketing — tidak akan habis dalam sebulan ──────────
    protected $tipsTopics = [
        'instagram_growth'    => 'strategi organik menumbuhkan followers Instagram dari 0',
        'tiktok_algorithm'    => 'cara kerja algoritma TikTok dan cara memanfaatkannya',
        'copywriting_formula' => 'formula copywriting AIDA, PAS, dan BAB untuk caption produk',
        'content_calendar'    => 'membuat content calendar 30 hari yang konsisten',
        'hashtag_strategy'    => 'riset dan strategi hashtag yang tepat untuk setiap platform',
        'reels_viral'         => 'membuat Reels dan TikTok yang berpotensi viral',
        'whatsapp_broadcast'  => 'WhatsApp marketing dan broadcast yang tidak dianggap spam',
        'email_marketing'     => 'email marketing untuk UMKM dengan open rate tinggi',
        'seo_marketplace'     => 'SEO produk di Shopee, Tokopedia, dan marketplace lain',
        'google_ads_umkm'     => 'Google Ads dengan budget kecil untuk UMKM',
        'facebook_ads'        => 'Facebook & Instagram Ads untuk pemula',
        'personal_branding'   => 'membangun personal branding sebagai content creator',
        'storytelling'        => 'teknik storytelling untuk konten bisnis yang engaging',
        'video_marketing'     => 'video marketing pendek yang efektif untuk social media',
        'customer_retention'  => 'strategi mempertahankan pelanggan dan meningkatkan repeat order',
        'pricing_strategy'    => 'strategi penetapan harga produk yang kompetitif',
        'collab_marketing'    => 'kolaborasi dengan influencer dan brand lain',
        'ugc_strategy'        => 'mendorong user-generated content dari pelanggan',
        'live_selling'        => 'tips live selling di TikTok Shop dan Instagram',
        'affiliate_marketing' => 'membangun program afiliasi untuk produk digital',
        'landing_page'        => 'membuat landing page yang mengkonversi',
        'funnel_marketing'    => 'membangun sales funnel dari awareness sampai purchase',
        'analytics_insight'   => 'membaca dan memanfaatkan data analytics social media',
        'crisis_management'   => 'menangani krisis reputasi brand di media sosial',
        'community_building'  => 'membangun komunitas loyal di sekitar brand',
        'podcast_marketing'   => 'podcast sebagai strategi content marketing',
        'pinterest_marketing' => 'Pinterest marketing untuk produk visual',
        'linkedin_b2b'        => 'LinkedIn marketing untuk bisnis B2B',
        'chatbot_automation'  => 'otomasi pemasaran dengan chatbot dan AI',
        'seasonal_campaign'   => 'membuat kampanye musiman yang efektif (Ramadan, Lebaran, dll)',
    ];

    // ── 30 tema quote inspirasi bisnis ─────────────────────────────────────
    protected $quoteThemes = [
        'kegigihan'       => 'kegigihan dan pantang menyerah dalam berbisnis',
        'inovasi'         => 'inovasi dan kreativitas sebagai kunci pertumbuhan bisnis',
        'kepemimpinan'    => 'kepemimpinan yang menginspirasi dan memimpin dengan hati',
        'kesuksesan'      => 'definisi sukses yang sesungguhnya bagi entrepreneur',
        'kegagalan'       => 'belajar dari kegagalan dan bangkit lebih kuat',
        'kerja_keras'     => 'kerja keras, disiplin, dan konsistensi',
        'mimpi'           => 'mengejar mimpi besar dan berani bermimpi',
        'perubahan'       => 'menghadapi perubahan dan beradaptasi dengan cepat',
        'kesempatan'      => 'memanfaatkan setiap kesempatan dalam bisnis',
        'kolaborasi'      => 'kolaborasi, teamwork, dan kekuatan bersama',
        'fokus'           => 'fokus, prioritas, dan menghindari distraksi',
        'pelanggan'       => 'pelayanan pelanggan sebagai pondasi bisnis',
        'nilai'           => 'membangun bisnis berbasis nilai dan integritas',
        'waktu'           => 'manajemen waktu dan produktivitas entrepreneur',
        'risiko'          => 'keberanian mengambil risiko yang terukur',
        'belajar'         => 'belajar terus-menerus dan growth mindset',
        'networking'      => 'kekuatan jaringan dan relasi dalam bisnis',
        'kreativitas'     => 'kreativitas sebagai senjata utama bisnis modern',
        'resiliensi'      => 'ketangguhan mental menghadapi tekanan bisnis',
        'visi'            => 'memiliki visi jangka panjang yang jelas',
        'action'          => 'mengambil tindakan nyata, bukan sekadar rencana',
        'gratitude'       => 'rasa syukur dan mindset kelimpahan dalam bisnis',
        'simplicity'      => 'kesederhanaan sebagai kekuatan dalam bisnis',
        'passion'         => 'passion dan purpose sebagai bahan bakar bisnis',
        'digital'         => 'transformasi digital dan peluang era baru',
        'umkm'            => 'semangat UMKM Indonesia yang tangguh dan berdaya',
        'generasi'        => 'generasi muda dan wirausaha masa depan Indonesia',
        'dampak'          => 'bisnis yang memberi dampak positif bagi masyarakat',
        'autentik'        => 'menjadi autentik dan jujur dalam berbisnis',
        'momentum'        => 'menangkap momentum dan bergerak di waktu yang tepat',
    ];

    // ── Rotation: 3-day cycle ──────────────────────────────────────────────
    protected $rotationPattern = [
        1 => 'industry',
        2 => 'tips',
        3 => 'quote',
    ];

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    // ── Main entry point ───────────────────────────────────────────────────
    public function generateDailyArticles(): array
    {
        $results = ['success' => 0, 'failed' => 0, 'articles' => [], 'errors' => []];

        try {
            $dayInCycle  = $this->getDayInCycle();
            $articleType = $this->rotationPattern[$dayInCycle];
            $article     = $this->generateArticleByType($articleType);

            if ($article) {
                $results['success']++;
                $results['articles'][] = [
                    'id'       => $article->id,
                    'title'    => $article->title,
                    'type'     => $articleType,
                    'industry' => $article->industry,
                ];
                \Log::info('✅ Daily article generated', [
                    'type'         => $articleType,
                    'day_in_cycle' => $dayInCycle,
                    'article_id'   => $article->id,
                ]);
            } else {
                $results['failed']++;
                $results['errors'][] = "Failed to generate {$articleType} article";
            }
        } catch (Exception $e) {
            $results['failed']++;
            $results['errors'][] = 'Error: ' . $e->getMessage();
            \Log::error('❌ Daily article generation failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    protected function generateArticleByType(string $type): ?Article
    {
        return match ($type) {
            'industry' => $this->generateIndustryArticle($this->pickUnusedIndustry()),
            'tips'     => $this->generateTipsArticle($this->pickUnusedTipsTopic()),
            'quote'    => $this->generateQuoteArticle($this->pickUnusedQuoteTheme()),
            default    => null,
        };
    }

    // ── Smart pickers — avoid repeats within 30 days ───────────────────────

    protected function pickUnusedIndustry(): string
    {
        $used = Article::where('category', 'industry')
            ->where('created_at', '>=', now()->subDays(30))
            ->pluck('industry')
            ->toArray();

        $available = array_diff(array_keys($this->industries), $used);
        if (empty($available)) $available = array_keys($this->industries);

        return $available[array_rand($available)];
    }

    protected function pickUnusedTipsTopic(): string
    {
        $used = Article::where('category', 'tips')
            ->where('created_at', '>=', now()->subDays(30))
            ->pluck('industry') // we store topic key in industry column
            ->toArray();

        $available = array_diff(array_keys($this->tipsTopics), $used);
        if (empty($available)) $available = array_keys($this->tipsTopics);

        return $available[array_rand($available)];
    }

    protected function pickUnusedQuoteTheme(): string
    {
        $used = Article::where('category', 'quote')
            ->where('created_at', '>=', now()->subDays(30))
            ->pluck('industry') // we store theme key in industry column
            ->toArray();

        $available = array_diff(array_keys($this->quoteThemes), $used);
        if (empty($available)) $available = array_keys($this->quoteThemes);

        return $available[array_rand($available)];
    }

    // ── Article generators ─────────────────────────────────────────────────

    protected function generateIndustryArticle(string $industryKey): ?Article
    {
        $industryLabel = $this->industries[$industryKey];

        $prompt = "Kamu adalah penulis artikel profesional bisnis Indonesia. Tulis artikel LENGKAP minimal 600 kata tentang tren terbaru di industri {$industryLabel} untuk UMKM dan pelaku bisnis Indonesia tahun 2026.

Struktur WAJIB:
1. Pembukaan menarik (100-150 kata) — hook tentang kondisi industri {$industryLabel} saat ini
2. Tren Pertama (150-200 kata) — tren paling signifikan, contoh konkret, cara UMKM memanfaatkannya
3. Tren Kedua (150-200 kata) — tren kedua yang penting, data/insight relevan
4. Tips Praktis (100-150 kata) — 3-5 langkah actionable yang bisa langsung diterapkan
5. Penutup & CTA (50-100 kata) — motivasi dan ajakan bertindak

Aturan: Bahasa Indonesia natural, fokus UMKM, contoh konkret, TULIS SAMPAI SELESAI.";

        $content = $this->generateContent($prompt);
        if (!$content) return null;

        $title       = "Tren Industri {$industryLabel} 2026: Peluang dan Strategi untuk UMKM";
        $slug        = $this->generateUniqueSlug($title);
        $description = Str::limit(strip_tags($content), 160);
        $keywords    = "{$industryLabel}, tren {$industryLabel} 2026, bisnis {$industryLabel}, UMKM {$industryLabel}";

        $article = Article::create([
            'title'       => $title,
            'slug'        => $slug,
            'content'     => $content,
            'description' => $description,
            'keywords'    => $keywords,
            'category'    => 'industry',
            'industry'    => $industryKey,
            'day_number'  => $this->getDayInCycle(),
        ]);

        $this->feedToMLSystem($article);
        return $article;
    }

    protected function generateTipsArticle(string $topicKey): ?Article
    {
        $topicDesc = $this->tipsTopics[$topicKey];

        $prompt = "Tulis artikel tips praktis tentang {$topicDesc} dalam Bahasa Indonesia untuk UMKM dan content creator Indonesia.

WAJIB: minimal 600 kata, 7-10 tips actionable, setiap tip dijelaskan dengan contoh konkret.

Struktur:
1. Pembukaan (100 kata) — pentingnya {$topicDesc} di era digital
2. Tips 1-10 (400-500 kata) — format bernomor, setiap tip ada penjelasan + contoh nyata
3. Penutup (100 kata) — motivasi dan langkah pertama yang bisa dilakukan hari ini

Bahasa: mudah dipahami, tidak terlalu teknis, relevan untuk bisnis kecil Indonesia. TULIS SAMPAI SELESAI.";

        $content = $this->generateContent($prompt);
        if (!$content) return null;

        $topicLabel  = ucwords(str_replace('_', ' ', $topicKey));
        $title       = "Tips {$topicLabel}: Panduan Praktis untuk UMKM 2026";
        $slug        = $this->generateUniqueSlug($title);
        $description = "Panduan lengkap {$topicDesc} — tips praktis yang bisa langsung diterapkan untuk bisnis kamu.";
        $keywords    = "{$topicLabel}, tips marketing, strategi digital, UMKM, content creator";

        $article = Article::create([
            'title'       => $title,
            'slug'        => $slug,
            'content'     => $content,
            'description' => $description,
            'keywords'    => $keywords,
            'category'    => 'tips',
            'industry'    => $topicKey, // store topic key for dedup tracking
            'day_number'  => $this->getDayInCycle(),
        ]);

        $this->feedToMLSystem($article);
        return $article;
    }

    protected function generateQuoteArticle(string $themeKey): ?Article
    {
        $themeDesc = $this->quoteThemes[$themeKey];

        $prompt = "Tulis artikel inspirasi dengan quote motivasi tentang {$themeDesc} dalam Bahasa Indonesia untuk entrepreneur dan content creator Indonesia.

WAJIB: minimal 450 kata, 1 quote powerful, penjelasan mendalam, contoh nyata dari bisnis Indonesia.

Struktur:
1. Quote (50 kata) — format: > \"[quote inspiratif]\" — [Nama Tokoh]
2. Makna Quote (150 kata) — penjelasan mendalam dan relevansi untuk entrepreneur Indonesia
3. Penerapan Praktis (150 kata) — cara menerapkan wisdom ini dalam bisnis sehari-hari, contoh konkret
4. Penutup (50-100 kata) — motivasi dan ajakan bertindak

Bahasa: menyentuh, memotivasi, autentik. TULIS SAMPAI SELESAI.";

        $content = $this->generateContent($prompt);
        if (!$content) return null;

        $themeLabel  = ucwords(str_replace('_', ' ', $themeKey));
        $title       = "Inspirasi Bisnis: Quote tentang {$themeLabel} untuk Entrepreneur Indonesia";
        $slug        = $this->generateUniqueSlug($title);
        $description = "Quote inspiratif tentang {$themeDesc} — motivasi harian untuk entrepreneur dan content creator Indonesia.";
        $keywords    = "inspirasi, motivasi, quote, {$themeLabel}, entrepreneur, UMKM, bisnis Indonesia";

        $article = Article::create([
            'title'       => $title,
            'slug'        => $slug,
            'content'     => $content,
            'description' => $description,
            'keywords'    => $keywords,
            'category'    => 'quote',
            'industry'    => $themeKey, // store theme key for dedup tracking
            'day_number'  => $this->getDayInCycle(),
        ]);

        $this->feedToMLSystem($article);
        return $article;
    }

    // ── Content generation with auto-continuation ──────────────────────────

    protected function generateContent(string $prompt): ?string
    {
        try {
            $response  = $this->geminiService->generateText($prompt, 3000, 0.8);
            $wordCount = str_word_count($response);

            // If too short or ends abruptly, continue
            $endsAbruptly = !preg_match('/[.!?]\s*$/', trim($response));
            if ($wordCount < 400 || $endsAbruptly) {
                \Log::warning('Article incomplete, continuing', ['words' => $wordCount]);
                $continuation = $this->geminiService->generateText(
                    "Lanjutkan artikel berikut sampai selesai dengan kesimpulan yang kuat:\n\n{$response}\n\n(Lanjutkan dan selesaikan):",
                    2000, 0.8
                );
                if ($continuation && str_word_count($continuation) > 50) {
                    $response = $response . "\n\n" . $continuation;
                }
            }

            return $this->cleanArticleContent($response);
        } catch (Exception $e) {
            \Log::error('Gemini API error: ' . $e->getMessage());
            return null;
        }
    }

    protected function cleanArticleContent(string $content): string
    {
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
        return trim($content);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    protected function generateUniqueSlug(string $title): string
    {
        $base    = Str::slug($title);
        $slug    = $base;
        $counter = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }
        return $slug;
    }

    protected function getDayInCycle(): int
    {
        return (((int) now()->dayOfYear - 1) % 3) + 1;
    }

    public function getIndustries(): array
    {
        return $this->industries;
    }

    // ── ML feed ────────────────────────────────────────────────────────────

    protected function feedToMLSystem(Article $article): void
    {
        try {
            $patterns = $this->extractMLPatterns($article->content);

            \App\Models\MLOptimizedData::create([
                'type'              => 'article',
                'industry'          => $article->industry,
                'platform'          => 'article',
                'data'              => json_encode([
                    'article_id' => $article->id,
                    'title'      => $article->title,
                    'keywords'   => $article->keywords,
                    'word_count' => str_word_count($article->content),
                    'patterns'   => $patterns,
                    'excerpt'    => Str::limit($article->content, 200),
                ]),
                'performance_score' => 0,
                'last_trained_at'   => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to feed article to ML system', [
                'article_id' => $article->id ?? null,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    protected function extractMLPatterns(string $content): array
    {
        preg_match_all('/\b(\w+\s+\w+\s+\w+)\b/u', $content, $matches);
        $phrases = array_count_values($matches[1]);
        arsort($phrases);

        $sentences = preg_split('/[.!?]+/', $content);

        return [
            'top_phrases'         => array_slice(array_keys($phrases), 0, 10),
            'avg_sentence_length' => count($sentences) > 0 ? round(str_word_count($content) / count($sentences), 1) : 0,
            'total_sentences'     => count($sentences),
            'has_questions'       => substr_count($content, '?') > 0,
            'has_lists'           => (bool) preg_match('/\d+\.\s/', $content),
            'has_quotes'          => substr_count($content, '"') > 0,
        ];
    }
}
