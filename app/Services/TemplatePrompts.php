<?php

namespace App\Services;

/**
 * Template Prompts for AI Generator
 * Provides specialized prompts for 200+ subcategories
 */
class TemplatePrompts
{
    /**
     * Get prompt template for specific subcategory
     */
    public static function getPrompt($subcategory, $basePrompt, $adjustedTone, $audienceContext)
    {
        // Get template configuration
        $template = self::getTemplate($subcategory);
        
        if (!$template) {
            // Fallback to generic prompt
            return $basePrompt . "Buatkan konten copywriting yang menarik sesuai brief di atas dan sangat relate dengan target audience.";
        }
        
        // Build prompt from template
        $prompt = $basePrompt;
        $prompt .= "Tugas: " . $template['task'] . "\n\n";
        
        if (isset($template['format'])) {
            $prompt .= "Format:\n" . $template['format'] . "\n\n";
        }
        
        if (isset($template['criteria'])) {
            $prompt .= "Kriteria:\n" . $template['criteria'] . "\n\n";
        }
        
        if (isset($template['tips'])) {
            $prompt .= "Tips:\n" . $template['tips'] . "\n\n";
        }
        
        $prompt .= "Buatkan sekarang:\n";
        
        return $prompt;
    }
    
    /**
     * Get template configuration for subcategory
     */
    protected static function getTemplate($subcategory)
    {
        $templates = self::getAllTemplates();
        return $templates[$subcategory] ?? null;
    }
    
    /**
     * Get all template definitions (200+ templates)
     */
    protected static function getAllTemplates()
    {
        return array_merge(
            self::getViralClickbaitTemplates(),
            self::getTrendFreshIdeasTemplates(),
            self::getEventPromoTemplates(),
            self::getHRRecruitmentTemplates(),
            self::getBrandingTaglineTemplates(),
            self::getEducationTemplates(),
            self::getMonetizationTemplates()
        );
    }

    
    /**
     * Viral & Clickbait Content Templates (20 items)
     */
    protected static function getViralClickbaitTemplates()
    {
        return [
            'clickbait_title' => [
                'task' => 'Buatkan clickbait title yang HONEST (tidak menyesatkan) tapi sangat menarik perhatian.',
                'criteria' => "- Maksimal 60 karakter\n- Bikin penasaran tapi tetap jujur\n- Gunakan angka, pertanyaan, atau kata kuat\n- Hindari clickbait palsu",
                'format' => "Buatkan 5 variasi title"
            ],
            'curiosity_gap' => [
                'task' => 'Buatkan hook dengan curiosity gap yang bikin audience penasaran.',
                'criteria' => "- Mulai dengan statement yang bikin penasaran\n- Jangan kasih jawaban di awal\n- Bikin audience mau tahu lebih lanjut\n- Maksimal 50 kata",
                'format' => "Buatkan 5 variasi hook"
            ],
            'shocking_statement' => [
                'task' => 'Buatkan shocking statement yang bikin audience kaget tapi tetap faktual.',
                'criteria' => "- Statement yang mengejutkan\n- Harus berdasarkan fakta/data\n- Relate dengan target audience\n- Maksimal 30 kata",
                'format' => "Buatkan 5 variasi statement"
            ],
            'controversial_take' => [
                'task' => 'Buatkan controversial take yang memicu diskusi (tapi tetap sopan).',
                'criteria' => "- Opini yang berbeda dari mainstream\n- Tidak menyinggung SARA\n- Bikin orang mau comment\n- Maksimal 100 kata",
                'format' => "Buatkan 3 variasi controversial take"
            ],
            'before_after' => [
                'task' => 'Buatkan before & after story yang inspiring dan relatable.',
                'format' => "- Before: Kondisi awal (struggle/masalah)\n- Turning point: Apa yang berubah\n- After: Hasil/transformasi\n- Lesson: Takeaway untuk audience\n- Maksimal 200 kata"
            ],
            'secret_reveal' => [
                'task' => 'Buatkan konten "secret reveal" yang bikin audience merasa dapat insider info.',
                'format' => "- Hook: 'Rahasia yang jarang orang tahu...'\n- Build up: Kenapa ini penting\n- Reveal: Rahasianya apa\n- Proof: Bukti/contoh\n- CTA: Ajak action"
            ],
            'mistake_warning' => [
                'task' => 'Buatkan warning tentang kesalahan umum yang harus dihindari.',
                'format' => "- Hook: 'Jangan sampai salah!'\n- List 3-5 kesalahan umum\n- Dampak dari setiap kesalahan\n- Solusi yang benar\n- CTA"
            ],
            'myth_busting' => [
                'task' => 'Buatkan konten myth busting yang membongkar mitos/kesalahpahaman.',
                'format' => "- Hook: 'Mitos atau Fakta?'\n- Mitos yang beredar\n- Fakta sebenarnya (dengan data/sumber)\n- Kenapa mitos ini berbahaya\n- Kesimpulan"
            ],
            'unpopular_opinion' => [
                'task' => 'Buatkan unpopular opinion yang thought-provoking.',
                'criteria' => "- Opini yang tidak mainstream\n- Didukung dengan reasoning yang kuat\n- Tidak menyinggung/offensive\n- Bikin orang berpikir\n- Maksimal 150 kata"
            ],
            'life_hack' => [
                'task' => 'Buatkan life hack / tips viral yang praktis dan mudah diterapkan.',
                'format' => "- Hook: 'Tips yang jarang orang tahu!'\n- Masalah yang dipecahkan\n- Solusi step-by-step (3-5 langkah)\n- Hasil yang didapat\n- CTA: Coba dan share"
            ],
            'challenge_trend' => [
                'task' => 'Buatkan ide challenge/trend yang bisa viral.',
                'criteria' => "- Mudah diikuti\n- Fun dan engaging\n- Relate dengan target audience\n- Ada hashtag khusus\n- Ajak orang tag teman"
            ],
            'reaction_bait' => [
                'task' => 'Buatkan konten yang memancing reaksi/comment (tapi tetap positif).',
                'criteria' => "- Ajukan pertanyaan kontroversial\n- Atau statement yang bikin orang setuju/tidak setuju\n- Ajak audience share pendapat\n- Maksimal 100 kata"
            ],
            'cliffhanger' => [
                'task' => 'Buatkan konten dengan cliffhanger ending yang bikin penasaran.',
                'format' => "- Opening: Hook yang menarik\n- Build up: Cerita yang engaging\n- Cliffhanger: Ending yang menggantung\n- CTA: 'Part 2 di post berikutnya!'"
            ],
            'number_list' => [
                'task' => 'Buatkan listicle (5 Cara, 10 Tips, dll) yang mudah dibaca.',
                'format' => "- Title: '[Angka] [Topik] yang [Benefit]'\n- Intro singkat\n- List dengan penjelasan singkat per poin\n- Kesimpulan\n- CTA"
            ],
            'question_hook' => [
                'task' => 'Buatkan hook berupa pertanyaan yang bikin audience mau jawab.',
                'criteria' => "- Pertanyaan yang relate dengan audience\n- Bikin orang mau comment\n- Tidak ada jawaban benar/salah\n- Maksimal 20 kata",
                'format' => "Buatkan 10 variasi pertanyaan"
            ],
            'emotional_trigger' => [
                'task' => 'Buatkan konten dengan emotional trigger yang kuat (nostalgia, empati, inspirasi, kebanggaan, harapan).',
                'format' => "- Hook emotional\n- Cerita yang menyentuh\n- Lesson/message\n- CTA"
            ],
            'fomo_content' => [
                'task' => 'Buatkan konten FOMO (Fear of Missing Out) yang bikin audience takut ketinggalan.',
                'format' => "- Urgency: 'Hanya hari ini!'\n- Scarcity: 'Stok terbatas!'\n- Social proof: 'Sudah 1000+ orang join!'\n- Benefit yang hilang: 'Kalau telat, rugi!'"
            ],
            'plot_twist' => [
                'task' => 'Buatkan story dengan plot twist yang unexpected.',
                'format' => "- Setup: Cerita yang normal\n- Build up: Audience pikir tahu endingnya\n- Plot twist: Ending yang unexpected\n- Lesson: Takeaway\n- Maksimal 200 kata"
            ],
            'relatable_content' => [
                'task' => 'Buatkan konten yang sangat relatable dengan kehidupan sehari-hari audience.',
                'format' => "- Hook: 'Siapa yang pernah...?'\n- Situasi yang relatable\n- Humor/insight\n- CTA: Tag teman yang relate"
            ],
            'storytime' => [
                'task' => 'Buatkan storytime dalam format viral (TikTok/Instagram style).',
                'format' => "- Hook: 'Story time! Jadi gini...'\n- Cerita dengan detail menarik\n- Klimaks\n- Ending\n- Maksimal 250 kata"
            ],
        ];
    }

    
    /**
     * Trend & Fresh Ideas Templates (20 items)
     */
    protected static function getTrendFreshIdeasTemplates()
    {
        return [
            'trending_topic' => [
                'task' => 'Buatkan konten berdasarkan trending topic saat ini.',
                'format' => "- Relate trending topic dengan produk/jasa\n- Gunakan hashtag trending\n- Timing is everything!",
                'tips' => "Jangan memaksakan jika tidak relevan"
            ],
            'viral_challenge' => [
                'task' => 'Buatkan ide viral challenge yang bisa diikuti audience.',
                'criteria' => "- Mudah diikuti\n- Fun dan engaging\n- Ada hashtag unik\n- Ajak tag teman"
            ],
            'seasonal_content' => [
                'task' => 'Buatkan konten seasonal yang relate dengan musim/waktu tertentu.',
                'format' => "Contoh: Musim hujan/kemarau, Awal/akhir bulan (gajian), Weekend/weekday, Pagi/siang/malam"
            ],
            'holiday_campaign' => [
                'task' => 'Buatkan campaign untuk hari besar/libur.',
                'format' => "Hari besar Indonesia: Lebaran, Natal, Imlek, HUT RI, Hari Kartini, Valentine, Mother's Day, Tahun Baru"
            ],
            'current_events' => [
                'task' => 'Buatkan konten yang relate dengan current events (berita terkini).',
                'tips' => "Pilih event yang positif, Hindari politik/SARA, Relate dengan produk/jasa, Timing penting!"
            ],
            'meme_marketing' => [
                'task' => 'Buatkan konten marketing menggunakan meme yang lagi viral.',
                'criteria' => "- Gunakan meme yang masih fresh\n- Relate dengan produk\n- Tetap on-brand\n- Humor yang sopan"
            ],
            'tiktok_trend' => [
                'task' => 'Buatkan ide konten berdasarkan TikTok trend terkini.',
                'format' => "Elemen: Sound/musik viral, Dance/movement, Format video, Hashtag challenge"
            ],
            'instagram_trend' => [
                'task' => 'Buatkan ide konten berdasarkan Instagram trend.',
                'format' => "Trends: Reels format, Filter/effect, Carousel post, Story interactive"
            ],
            'youtube_trend' => [
                'task' => 'Buatkan ide konten YouTube berdasarkan trend.',
                'format' => "Trends: Video format (vlog, tutorial, review), Thumbnail style, Title formula, Content angle"
            ],
            'twitter_trend' => [
                'task' => 'Buatkan konten berdasarkan Twitter/X trending topics.',
                'format' => "Format: Thread (1/n), Quote tweet, Poll, Maksimal 280 char per tweet"
            ],
            'content_series' => [
                'task' => 'Buatkan ide content series yang bisa jadi konten rutin.',
                'format' => "Contoh: Tips Senin, Behind the Scenes Jumat, Customer Story Rabu, FAQ Friday\n\nBuatkan 5 ide series"
            ],
            'collaboration_ideas' => [
                'task' => 'Buatkan ide kolaborasi dengan brand/influencer lain.',
                'format' => "Jenis: Giveaway bersama, Co-create content, Cross-promotion, Bundle product"
            ],
            'giveaway_ideas' => [
                'task' => 'Buatkan campaign giveaway yang engaging.',
                'format' => "Elemen: Hadiah menarik, Syarat mudah (follow, like, comment, tag), Durasi jelas, Pengumuman pemenang"
            ],
            'user_generated' => [
                'task' => 'Buatkan campaign untuk user generated content (UGC).',
                'format' => "Ide: Photo contest, Testimoni video, Review produk, Creative usage"
            ],
            'behind_scenes' => [
                'task' => 'Buatkan konten behind the scenes yang humanize brand.',
                'format' => "Ide: Proses produksi, Team introduction, Office tour, Day in the life"
            ],
            'educational_series' => [
                'task' => 'Buatkan series konten edukasi yang valuable.',
                'format' => "Format: Tutorial step-by-step, Tips & tricks, Did you know?, Myth vs Fact\n\nBuatkan 5 episode series"
            ],
            'storytelling_series' => [
                'task' => 'Buatkan series storytelling yang bikin audience penasaran.',
                'format' => "Episode 1: Introduction, Episode 2-4: Development, Episode 5: Conclusion, Cliffhanger di setiap episode\n\nBuatkan outline series"
            ],
            'product_launch' => [
                'task' => 'Buatkan campaign untuk product launch yang hype.',
                'format' => "Timeline: Teaser (1 minggu sebelum), Countdown (3 hari sebelum), Launch day, Post-launch follow up"
            ],
            'rebranding_ideas' => [
                'task' => 'Buatkan campaign untuk rebranding/refresh brand.',
                'format' => "Elemen: Announcement, Story behind rebrand, New vs old comparison, What stays the same"
            ],
            'crisis_content' => [
                'task' => 'Buatkan konten untuk crisis management atau comeback.',
                'criteria' => "Tone: Jujur dan transparan, Empati, Solution-focused, Rebuild trust"
            ],
        ];
    }
    
    /**
     * Event & Promo Templates (20 items)
     */
    protected static function getEventPromoTemplates()
    {
        $baseFormat = [
            'task' => 'Buatkan konten promosi untuk [EVENT].',
            'format' => "- Hook yang menarik perhatian\n- Detail event/promo (tanggal, lokasi, benefit)\n- Urgency & scarcity\n- CTA yang jelas\n- Hashtag relevan"
        ];
        
        return [
            'grand_opening' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Grand Opening.']),
            'flash_sale' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Flash Sale / Sale Kilat.']),
            'discount_promo' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Diskon & Promo Spesial.']),
            'bazaar' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Bazaar / Pameran.']),
            'exhibition' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Exhibition / Pameran Seni.']),
            'workshop' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Workshop / Seminar.']),
            'anniversary' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Anniversary / Ulang Tahun.']),
            'seasonal_promo' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Promo Musiman (Lebaran, Natal, dll).']),
            'clearance_sale' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Clearance Sale / Obral.']),
            'buy_1_get_1' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Buy 1 Get 1 / Bundling.']),
            'loyalty_program' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Program Loyalitas / Member.']),
            'giveaway' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Giveaway / Kuis Berhadiah.']),
            'pre_order' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Pre-Order Campaign.']),
            'limited_edition' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Limited Edition / Exclusive.']),
            'collaboration' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Kolaborasi Brand.']),
            'charity_event' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Event Charity / Sosial.']),
            'meet_greet' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Meet & Greet / Gathering.']),
            'live_shopping' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Live Shopping / Live Selling.']),
            'countdown_promo' => array_merge($baseFormat, ['task' => 'Buatkan konten promosi untuk Countdown Promo (24 Jam, 3 Hari, dll).']),
        ];
    }
    
    /**
     * HR & Recruitment Templates (20 items)
     */
    protected static function getHRRecruitmentTemplates()
    {
        return [
            'job_description' => [
                'task' => 'Buatkan Job Description yang jelas dan menarik.',
                'format' => "- Job Title\n- Company Overview\n- Job Summary\n- Responsibilities (bullet points)\n- Requirements (bullet points)\n- Benefits\n- How to Apply"
            ],
            'job_vacancy' => [
                'task' => 'Buatkan post lowongan kerja untuk social media.',
                'format' => "- Hook menarik\n- Posisi yang dibuka\n- Kualifikasi singkat\n- Benefit menarik\n- CTA: Cara apply\n- Hashtag #LowonganKerja #Hiring"
            ],
            'job_requirements' => [
                'task' => 'Buatkan daftar job requirements yang jelas.',
                'format' => "Kategori: Must Have (wajib), Nice to Have (plus), Soft Skills, Technical Skills"
            ],
            'company_culture' => [
                'task' => 'Buatkan deskripsi company culture yang menarik talent.',
                'format' => "Highlight: Values & mission, Work environment, Team dynamics, Growth opportunities"
            ],
            'employee_benefits' => [
                'task' => 'Buatkan deskripsi employee benefits yang menarik.',
                'format' => "Benefits: Salary & bonus, Health insurance, Work-life balance, Learning & development"
            ],
            'interview_questions' => [
                'task' => 'Buatkan daftar interview questions yang efektif.',
                'format' => "Kategori: Behavioral questions, Technical questions, Situational questions, Culture fit questions\n\nBuatkan 10 pertanyaan"
            ],
            'offer_letter' => [
                'task' => 'Buatkan offer letter yang profesional dan welcoming.',
                'format' => "- Congratulations message\n- Position details\n- Compensation & benefits\n- Start date\n- Next steps"
            ],
            'rejection_letter' => [
                'task' => 'Buatkan rejection letter yang sopan dan empathetic.',
                'criteria' => "Tone: Appreciative, Respectful, Encouraging, Leave door open for future"
            ],
            'onboarding_message' => [
                'task' => 'Buatkan welcome message untuk new employee.',
                'format' => "- Warm welcome\n- What to expect\n- First day info\n- Contact person"
            ],
            'internship_program' => [
                'task' => 'Buatkan deskripsi internship program.',
                'format' => "Highlight: Program overview, Learning opportunities, Mentorship, Potential for full-time"
            ],
            'career_page' => [
                'task' => 'Buatkan content untuk career page website.',
                'format' => "Sections: Why join us, Our culture, Growth opportunities, Employee testimonials"
            ],
            'linkedin_job_post' => [
                'task' => 'Buatkan job post untuk LinkedIn.',
                'criteria' => "Format: Professional tone, Clear structure, Highlight company, Easy apply CTA"
            ],
            'instagram_hiring' => [
                'task' => 'Buatkan hiring post untuk Instagram.',
                'criteria' => "Format: Visual-friendly, Emoji yang sesuai, Singkat & jelas, Hashtag #LowonganKerja"
            ],
            'whatsapp_recruitment' => [
                'task' => 'Buatkan recruitment message untuk WhatsApp broadcast.',
                'format' => "- Greeting personal\n- Info singkat\n- CTA jelas\n- Contact info"
            ],
            'employee_referral' => [
                'task' => 'Buatkan program employee referral.',
                'format' => "Elemen: Referral bonus, How it works, Eligible positions, Terms & conditions"
            ],
            'job_fair_booth' => [
                'task' => 'Buatkan deskripsi untuk booth di job fair.',
                'format' => "Isi: Company intro, Open positions, Why join us, On-spot interview info"
            ],
            'campus_recruitment' => [
                'task' => 'Buatkan pitch untuk campus recruitment.',
                'format' => "Target: Fresh graduates\nHighlight: Entry-level opportunities, Training program, Career path, Young & dynamic team"
            ],
            'remote_job' => [
                'task' => 'Buatkan job description untuk remote position.',
                'format' => "Highlight: Work from anywhere, Flexible hours, Remote tools & support, Communication expectations"
            ],
            'freelance_job' => [
                'task' => 'Buatkan brief untuk freelance job.',
                'format' => "Isi: Project scope, Deliverables, Timeline, Budget range, How to apply"
            ],
            'part_time_job' => [
                'task' => 'Buatkan post untuk part-time job.',
                'format' => "Highlight: Flexible schedule, Hourly rate, Suitable for students/side job, Simple requirements"
            ],
        ];
    }

    
    /**
     * Branding & Tagline Templates (25 items)
     */
    protected static function getBrandingTaglineTemplates()
    {
        $taglineBase = [
            'task' => 'Buatkan [TYPE] yang memorable dan impactful.',
            'criteria' => "- Singkat & mudah diingat\n- Reflect brand values\n- Unique & differentiated\n- Emotional connection",
            'format' => "Buatkan 10 variasi"
        ];
        
        return [
            'brand_tagline' => array_merge($taglineBase, ['task' => 'Buatkan brand tagline/slogan yang memorable dan impactful.']),
            'company_tagline' => array_merge($taglineBase, ['task' => 'Buatkan company tagline yang memorable dan impactful.']),
            'product_tagline' => array_merge($taglineBase, ['task' => 'Buatkan product tagline yang memorable dan impactful.']),
            'brand_name' => array_merge($taglineBase, ['task' => 'Buatkan brand name ideas yang memorable dan impactful.']),
            'product_name' => array_merge($taglineBase, ['task' => 'Buatkan product name ideas yang memorable dan impactful.']),
            'business_name' => array_merge($taglineBase, ['task' => 'Buatkan business name ideas yang memorable dan impactful.']),
            'tshirt_quote' => array_merge($taglineBase, ['task' => 'Buatkan T-shirt quote/text yang memorable dan impactful.']),
            'hoodie_text' => array_merge($taglineBase, ['task' => 'Buatkan hoodie text yang memorable dan impactful.']),
            'tote_bag_text' => array_merge($taglineBase, ['task' => 'Buatkan tote bag text yang memorable dan impactful.']),
            'mug_text' => array_merge($taglineBase, ['task' => 'Buatkan mug text yang memorable dan impactful.']),
            'sticker_text' => array_merge($taglineBase, ['task' => 'Buatkan sticker text yang memorable dan impactful.']),
            'poster_quote' => array_merge($taglineBase, ['task' => 'Buatkan poster quote yang memorable dan impactful.']),
            'motivational_quote' => array_merge($taglineBase, ['task' => 'Buatkan motivational quote yang memorable dan impactful.']),
            'funny_quote' => array_merge($taglineBase, ['task' => 'Buatkan funny quote yang memorable dan impactful.']),
            'inspirational_quote' => array_merge($taglineBase, ['task' => 'Buatkan inspirational quote yang memorable dan impactful.']),
            'logo_text' => array_merge($taglineBase, ['task' => 'Buatkan logo text/wordmark yang memorable dan impactful.']),
            'brand_story' => [
                'task' => 'Buatkan brand story yang compelling.',
                'format' => "- Origin story\n- Why we exist\n- What makes us different\n- Our mission\n- Maksimal 200 kata"
            ],
            'brand_mission' => [
                'task' => 'Buatkan brand mission statement.',
                'criteria' => "- Clear purpose\n- Inspiring\n- Actionable\n- Maksimal 50 kata"
            ],
            'brand_vision' => [
                'task' => 'Buatkan brand vision statement.',
                'criteria' => "- Future-focused\n- Aspirational\n- Achievable\n- Maksimal 50 kata"
            ],
            'brand_values' => [
                'task' => 'Buatkan brand values yang authentic.',
                'format' => "- 3-5 core values\n- Each with short description\n- Actionable & measurable"
            ],
            'usp' => [
                'task' => 'Buatkan USP (Unique Selling Proposition).',
                'criteria' => "- What makes you different\n- Specific benefit\n- Compelling reason to choose you\n- Maksimal 30 kata",
                'format' => "Buatkan 5 variasi"
            ],
            'elevator_pitch' => [
                'task' => 'Buatkan elevator pitch (30 detik).',
                'format' => "- Who you are\n- What you do\n- Who you serve\n- What makes you different\n- CTA\n- Maksimal 100 kata"
            ],
            'brand_positioning' => [
                'task' => 'Buatkan brand positioning statement.',
                'format' => "- Target audience\n- Category\n- Point of difference\n- Reason to believe"
            ],
            'catchphrase' => [
                'task' => 'Buatkan catchphrase/jargon brand yang catchy.',
                'criteria' => "- Easy to remember\n- Fun to say\n- Represent brand personality\n- Maksimal 5 kata",
                'format' => "Buatkan 10 variasi"
            ],
            'merchandise_collection' => [
                'task' => 'Buatkan nama untuk merchandise collection.',
                'criteria' => "- Thematic\n- Memorable\n- Reflect collection vibe",
                'format' => "Buatkan 10 nama"
            ],
        ];
    }
    
    /**
     * Education & Institution Templates (25 items)
     */
    protected static function getEducationTemplates()
    {
        $eduBase = [
            'task' => 'Buatkan konten untuk [TOPIC] dengan tone formal tapi friendly.',
            'criteria' => "Tone: Profesional, Inspiring, Inclusive, Positive"
        ];
        
        return [
            'school_achievement' => array_merge($eduBase, ['task' => 'Buatkan konten untuk pencapaian sekolah/kampus dengan tone formal tapi friendly.']),
            'student_achievement' => array_merge($eduBase, ['task' => 'Buatkan konten untuk prestasi siswa/mahasiswa dengan tone formal tapi friendly.']),
            'graduation_announcement' => array_merge($eduBase, ['task' => 'Buatkan konten untuk pengumuman kelulusan dengan tone formal tapi friendly.']),
            'new_student_admission' => array_merge($eduBase, ['task' => 'Buatkan konten untuk penerimaan siswa baru (PSB/PPDB) dengan tone formal tapi friendly.']),
            'school_event' => array_merge($eduBase, ['task' => 'Buatkan konten untuk event sekolah/kampus dengan tone formal tapi friendly.']),
            'national_holiday' => array_merge($eduBase, ['task' => 'Buatkan konten untuk hari besar nasional dengan tone formal tapi friendly.']),
            'education_day' => array_merge($eduBase, ['task' => 'Buatkan konten untuk hari pendidikan (Hardiknas, dll) dengan tone formal tapi friendly.']),
            'teacher_day' => array_merge($eduBase, ['task' => 'Buatkan konten untuk hari guru dengan tone formal tapi friendly.']),
            'independence_day' => array_merge($eduBase, ['task' => 'Buatkan konten untuk HUT RI / kemerdekaan dengan tone formal tapi friendly.']),
            'religious_holiday' => array_merge($eduBase, ['task' => 'Buatkan konten untuk hari besar keagamaan dengan tone formal tapi friendly.']),
            'school_anniversary' => array_merge($eduBase, ['task' => 'Buatkan konten untuk HUT sekolah/kampus dengan tone formal tapi friendly.']),
            'academic_info' => array_merge($eduBase, ['task' => 'Buatkan konten untuk informasi akademik dengan tone formal tapi friendly.']),
            'exam_announcement' => array_merge($eduBase, ['task' => 'Buatkan konten untuk pengumuman ujian dengan tone formal tapi friendly.']),
            'scholarship_info' => array_merge($eduBase, ['task' => 'Buatkan konten untuk info beasiswa dengan tone formal tapi friendly.']),
            'extracurricular' => array_merge($eduBase, ['task' => 'Buatkan konten untuk kegiatan ekstrakurikuler dengan tone formal tapi friendly.']),
            'parent_meeting' => array_merge($eduBase, ['task' => 'Buatkan konten untuk rapat orang tua dengan tone formal tapi friendly.']),
            'school_facility' => array_merge($eduBase, ['task' => 'Buatkan konten untuk fasilitas sekolah/kampus dengan tone formal tapi friendly.']),
            'teacher_profile' => array_merge($eduBase, ['task' => 'Buatkan konten untuk profil guru/dosen dengan tone formal tapi friendly.']),
            'alumni_success' => array_merge($eduBase, ['task' => 'Buatkan konten untuk kisah sukses alumni dengan tone formal tapi friendly.']),
            'government_program' => array_merge($eduBase, ['task' => 'Buatkan konten untuk program pemerintah/dinas dengan tone formal tapi friendly.']),
            'public_service' => array_merge($eduBase, ['task' => 'Buatkan konten untuk layanan publik dengan tone formal tapi friendly.']),
            'government_announcement' => array_merge($eduBase, ['task' => 'Buatkan konten untuk pengumuman resmi instansi dengan tone formal tapi friendly.']),
            'community_program' => array_merge($eduBase, ['task' => 'Buatkan konten untuk program kemasyarakatan dengan tone formal tapi friendly.']),
            'health_campaign' => array_merge($eduBase, ['task' => 'Buatkan konten untuk kampanye kesehatan dengan tone formal tapi friendly.']),
            'safety_awareness' => array_merge($eduBase, ['task' => 'Buatkan konten untuk sosialisasi keselamatan dengan tone formal tapi friendly.']),
        ];
    }
    
    /**
     * All Monetization Templates (Combined for efficiency)
     */
    protected static function getMonetizationTemplates()
    {
        return array_merge(
            self::getVideoMonetizationTemplates(),
            self::getPhotoMonetizationTemplates(),
            self::getPrintOnDemandTemplates(),
            self::getFreelanceTemplates(),
            self::getDigitalProductsTemplates(),
            self::getEbookPublishingTemplates(),
            self::getAcademicWritingTemplates(),
            self::getWritingMonetizationTemplates(),
            self::getAffiliateMarketingTemplates(),
            self::getBlogSEOTemplates()
        );
    }
    
    /**
     * Video Monetization Templates (9 items)
     */
    protected static function getVideoMonetizationTemplates()
    {
        return [
            'tiktok_viral' => [
                'task' => 'Buatkan script TikTok yang berpotensi viral.',
                'format' => "- Hook 1 detik pertama (CRUCIAL!)\n- Content 15-60 detik\n- Trending sound/music\n- Hashtag strategy\n- CTA: Like, comment, follow"
            ],
            'youtube_long' => [
                'task' => 'Buatkan script YouTube video panjang (8-15 menit).',
                'format' => "- Intro (30 detik): Hook + preview\n- Main content (7-13 menit): Value delivery\n- Outro (1 menit): CTA subscribe, like, comment\n- Timestamps untuk chapters\n\nBuatkan outline"
            ],
            'youtube_shorts' => [
                'task' => 'Buatkan script YouTube Shorts (max 60 detik).',
                'format' => "- Hook instant (1-2 detik)\n- Quick value delivery\n- Strong CTA\n- Vertical format (9:16)"
            ],
            'facebook_video' => [
                'task' => 'Buatkan script Facebook video.',
                'tips' => "First 3 seconds crucial, Captions/subtitles (many watch without sound), 1-3 minutes optimal, CTA: Share, tag friends"
            ],
            'snack_video' => [
                'task' => 'Buatkan script Snack Video.',
                'format' => "- Short & entertaining\n- Trending music\n- Easy to replicate\n- Hashtag Indonesia"
            ],
            'likee' => [
                'task' => 'Buatkan script Likee video.',
                'format' => "- Creative effects\n- Trending challenges\n- Fun & engaging\n- Hashtag strategy"
            ],
            'kwai' => [
                'task' => 'Buatkan script Kwai video.',
                'format' => "- Short & sweet\n- Relatable content\n- Trending music\n- Hashtag Indonesia"
            ],
            'bigo_live' => [
                'task' => 'Buatkan script untuk Bigo Live streaming.',
                'format' => "- Opening greeting\n- Content plan\n- Interaction with viewers\n- Gift request (natural)\n- Closing"
            ],
            'nimo_tv' => [
                'task' => 'Buatkan script untuk Nimo TV gaming stream.',
                'format' => "- Game intro\n- Commentary style\n- Viewer interaction\n- Subscribe CTA"
            ],
        ];
    }
    
    /**
     * Photo Monetization Templates (6 items)
     */
    protected static function getPhotoMonetizationTemplates()
    {
        return [
            'shutterstock' => [
                'task' => 'Buatkan deskripsi foto untuk Shutterstock.',
                'format' => "- Title (max 200 char): Descriptive & keyword-rich\n- Description (max 200 char): What, where, when, why\n- Keywords (50 max): Specific to broad\n- Categories: Choose relevant"
            ],
            'adobe_stock' => [
                'task' => 'Buatkan keywords untuk Adobe Stock.',
                'criteria' => "- 25-50 keywords\n- Mix specific & general\n- Include synonyms\n- Think buyer intent"
            ],
            'getty_images' => [
                'task' => 'Buatkan caption untuk Getty Images.',
                'format' => "- Detailed description\n- Context & story\n- Technical details\n- Usage suggestions"
            ],
            'istock' => [
                'task' => 'Buatkan metadata untuk iStock.',
                'format' => "- Title\n- Description\n- Keywords\n- Categories"
            ],
            'freepik' => [
                'task' => 'Buatkan tags untuk Freepik.',
                'format' => "Tag strategy: Descriptive tags, Style tags, Usage tags, Trend tags"
            ],
            'vecteezy' => [
                'task' => 'Buatkan description untuk Vecteezy.',
                'format' => "- What it is\n- Best use cases\n- File details\n- Keywords"
            ],
        ];
    }
    
    /**
     * Print on Demand Templates (5 items)
     */
    protected static function getPrintOnDemandTemplates()
    {
        return [
            'redbubble' => [
                'task' => 'Buatkan product title untuk Redbubble.',
                'format' => "- Descriptive title (max 100 char)\n- Include design theme\n- Target audience\n- Keywords for search"
            ],
            'teespring' => [
                'task' => 'Buatkan description untuk Teespring.',
                'format' => "- Product description\n- Design story\n- Target audience\n- Size/fit info"
            ],
            'spreadshirt' => [
                'task' => 'Buatkan tags untuk Spreadshirt.',
                'format' => "Tag strategy: Design keywords, Niche keywords, Occasion keywords, Style keywords"
            ],
            'zazzle' => [
                'task' => 'Buatkan product copy untuk Zazzle.',
                'format' => "- Product title\n- Description\n- Customization options\n- Gift ideas"
            ],
            'society6' => [
                'task' => 'Buatkan artist bio untuk Society6.',
                'format' => "- Your story\n- Design philosophy\n- Inspiration\n- Connect with audience"
            ],
        ];
    }

    
    /**
     * Freelance Templates (7 items)
     */
    protected static function getFreelanceTemplates()
    {
        return [
            'upwork_proposal' => [
                'task' => 'Buatkan proposal Upwork yang winning.',
                'format' => "- Personalized greeting\n- Show you understand the project\n- Your relevant experience\n- How you'll solve their problem\n- Timeline & deliverables\n- CTA: Let's discuss\n- Maksimal 300 kata"
            ],
            'fiverr_gig' => [
                'task' => 'Buatkan Fiverr gig description yang convert.',
                'format' => "- Catchy title (max 80 char)\n- What you offer\n- Why choose you\n- What's included\n- FAQ\n- CTA: Order now"
            ],
            'freelancer_bid' => [
                'task' => 'Buatkan bid untuk Freelancer.com.',
                'format' => "- Professional greeting\n- Project understanding\n- Your approach\n- Portfolio samples\n- Budget & timeline\n- CTA"
            ],
            'sribulancer' => [
                'task' => 'Buatkan penawaran untuk Sribulancer (Indonesia).',
                'format' => "- Salam pembuka (Bahasa Indonesia)\n- Pemahaman project\n- Pengalaman relevan\n- Solusi yang ditawarkan\n- Harga & timeline\n- CTA"
            ],
            'projects_id' => [
                'task' => 'Buatkan proposal untuk Projects.co.id.',
                'format' => "- Greeting formal (Bahasa Indonesia)\n- Analisis kebutuhan\n- Solusi yang ditawarkan\n- Portofolio\n- Penawaran harga\n- CTA"
            ],
            'portfolio' => [
                'task' => 'Buatkan portfolio description.',
                'format' => "- Project title\n- Client/context\n- Challenge\n- Your solution\n- Results/impact\n- Technologies used"
            ],
            'cover_letter' => [
                'task' => 'Buatkan cover letter untuk freelance application.',
                'format' => "- Professional greeting\n- Why you're interested\n- Relevant experience\n- What you bring\n- CTA: Interview request\n- Maksimal 250 kata"
            ],
        ];
    }
    
    /**
     * Digital Products Templates (6 items)
     */
    protected static function getDigitalProductsTemplates()
    {
        return [
            'gumroad' => [
                'task' => 'Buatkan product page untuk Gumroad.',
                'format' => "- Catchy title\n- Problem it solves\n- What's included\n- Who it's for\n- Testimonials/social proof\n- CTA: Buy now"
            ],
            'sellfy' => [
                'task' => 'Buatkan sales copy untuk Sellfy.',
                'format' => "- Headline yang kuat\n- Benefits (not features)\n- Social proof\n- Guarantee/refund policy\n- Urgency/scarcity\n- CTA"
            ],
            'payhip' => [
                'task' => 'Buatkan description untuk Payhip.',
                'format' => "- Product overview\n- Key features\n- What you'll learn/get\n- File details\n- CTA"
            ],
            'ebook_description' => [
                'task' => 'Buatkan e-book description yang menjual.',
                'format' => "- Hook: Problem statement\n- What's inside (chapter overview)\n- Who should read this\n- What you'll gain\n- Author credibility\n- CTA"
            ],
            'course_landing' => [
                'task' => 'Buatkan landing page untuk online course.',
                'format' => "Sections: Hero (Headline + subheadline), Problem (Pain points), Solution (Course overview), Curriculum (What's included), Instructor (Credibility), Testimonials, Pricing, FAQ, CTA"
            ],
            'template_description' => [
                'task' => 'Buatkan description untuk template (Notion, Canva, dll).',
                'format' => "- What it is\n- Use cases\n- What's included\n- How to use\n- Customization options\n- CTA"
            ],
        ];
    }
    
    /**
     * eBook Publishing Templates (15 items)
     */
    protected static function getEbookPublishingTemplates()
    {
        return [
            'kindle_description' => [
                'task' => 'Buatkan Amazon Kindle book description.',
                'format' => "Format (max 4,000 char):\n- Hook paragraph\n- What readers will discover\n- Who it's for\n- Author credibility\n- CTA: Buy now"
            ],
            'kindle_blurb' => [
                'task' => 'Buatkan Kindle back cover blurb.',
                'format' => "Format (max 200 words):\n- Compelling hook\n- Story/content teaser\n- Stakes/benefit\n- CTA"
            ],
            'google_play_books' => [
                'task' => 'Buatkan description untuk Google Play Books.',
                'format' => "- Engaging summary\n- Key themes\n- Target audience\n- Author bio"
            ],
            'apple_books' => [
                'task' => 'Buatkan synopsis untuk Apple Books.',
                'format' => "- Captivating opening\n- Story/content overview\n- What makes it unique\n- Reader benefits"
            ],
            'kobo' => [
                'task' => 'Buatkan description untuk Kobo Writing Life.',
                'format' => "- Book summary\n- Genre/category\n- Target readers\n- Series info (if applicable)"
            ],
            'barnes_noble' => [
                'task' => 'Buatkan copy untuk Barnes & Noble Press.',
                'format' => "- Professional summary\n- Content highlights\n- Author credentials\n- Reader appeal"
            ],
            'leanpub' => [
                'task' => 'Buatkan sales page untuk Leanpub.',
                'format' => "- Problem statement\n- Solution (your book)\n- What you'll learn\n- Who it's for\n- Sample chapters"
            ],
            'gumroad_ebook' => [
                'task' => 'Buatkan eBook landing page untuk Gumroad.',
                'format' => "- Headline\n- Benefits\n- Table of contents\n- Testimonials\n- Pricing\n- CTA"
            ],
            'gramedia_digital' => [
                'task' => 'Buatkan deskripsi untuk Gramedia Digital (Indonesia).',
                'format' => "Format (Bahasa Indonesia):\n- Sinopsis menarik\n- Tema utama\n- Target pembaca\n- Tentang penulis"
            ],
            'mizanstore' => [
                'task' => 'Buatkan sinopsis untuk Mizanstore.',
                'format' => "Format (Bahasa Indonesia):\n- Pembuka yang menarik\n- Isi buku\n- Keunikan\n- Manfaat untuk pembaca"
            ],
            'kubuku' => [
                'task' => 'Buatkan description untuk Kubuku.',
                'format' => "Format (Bahasa Indonesia):\n- Deskripsi buku\n- Genre\n- Target pembaca\n- Info penulis"
            ],
            'storial' => [
                'task' => 'Buatkan sinopsis untuk Storial (cerita online).',
                'format' => "Format (Bahasa Indonesia):\n- Hook menarik\n- Premis cerita\n- Karakter utama\n- Genre & tags"
            ],
            'book_title' => [
                'task' => 'Buatkan book title ideas.',
                'criteria' => "- Catchy & memorable\n- Reflect content\n- SEO-friendly\n- Genre-appropriate",
                'format' => "Buatkan 10 variasi"
            ],
            'chapter_outline' => [
                'task' => 'Buatkan chapter outline untuk buku.',
                'format' => "- Chapter titles (10-15 chapters)\n- Brief description per chapter\n- Logical flow\n- Key takeaways"
            ],
            'author_bio' => [
                'task' => 'Buatkan author bio.',
                'format' => "- Professional background\n- Writing experience\n- Expertise/credentials\n- Personal touch\n- Maksimal 150 kata"
            ],
        ];
    }
    
    /**
     * Academic Writing Templates (11 items)
     */
    protected static function getAcademicWritingTemplates()
    {
        return [
            'abstract' => [
                'task' => 'Buatkan abstract/abstrak untuk paper akademik.',
                'format' => "Struktur (max 250 words):\n- Background\n- Objective\n- Methods\n- Results\n- Conclusion"
            ],
            'research_title' => [
                'task' => 'Buatkan research title yang spesifik dan jelas.',
                'criteria' => "- Specific & focused\n- Clear variables\n- Academic tone\n- Max 20 words",
                'format' => "Buatkan 5 variasi"
            ],
            'introduction' => [
                'task' => 'Buatkan introduction untuk paper.',
                'format' => "Struktur:\n- Background/context\n- Problem statement\n- Research gap\n- Objectives\n- Significance\n\nBuatkan outline"
            ],
            'literature_review' => [
                'task' => 'Buatkan outline literature review.',
                'format' => "Struktur:\n- Themes/topics\n- Key studies\n- Theoretical framework\n- Research gap\n\nBuatkan outline"
            ],
            'methodology' => [
                'task' => 'Buatkan methodology description.',
                'format' => "Sections:\n- Research design\n- Population & sample\n- Data collection\n- Data analysis\n- Validity & reliability"
            ],
            'conclusion' => [
                'task' => 'Buatkan conclusion untuk paper.',
                'format' => "Struktur:\n- Summary of findings\n- Implications\n- Limitations\n- Recommendations\n- Future research"
            ],
            'keywords' => [
                'task' => 'Buatkan keywords untuk paper akademik.',
                'criteria' => "- 5-7 keywords\n- Specific to general\n- Searchable terms\n- Relevant to content"
            ],
            'researchgate_profile' => [
                'task' => 'Buatkan ResearchGate profile.',
                'format' => "Sections:\n- Research interests\n- Current position\n- Education\n- Publications\n- Skills"
            ],
            'academia_bio' => [
                'task' => 'Buatkan bio untuk Academia.edu.',
                'format' => "- Academic background\n- Research focus\n- Key publications\n- Current work"
            ],
            'paper_summary' => [
                'task' => 'Buatkan summary paper untuk general audience.',
                'format' => "- Plain language\n- Key findings\n- Practical implications\n- Max 200 words"
            ],
            'conference_abstract' => [
                'task' => 'Buatkan abstract untuk conference submission.',
                'format' => "- Research question\n- Methodology\n- Key findings\n- Contribution\n- Max 300 words"
            ],
        ];
    }
    
    /**
     * Writing Monetization Templates (9 items)
     */
    protected static function getWritingMonetizationTemplates()
    {
        return [
            'medium_article' => [
                'task' => 'Buatkan article untuk Medium.',
                'format' => "- Compelling headline\n- Strong opening hook\n- Subheadings for readability\n- Personal insights\n- Actionable takeaways\n- CTA: Clap, follow, comment\n\nBuatkan outline"
            ],
            'medium_headline' => [
                'task' => 'Buatkan headline untuk Medium article.',
                'criteria' => "- Curiosity-driven\n- Benefit-focused\n- SEO-friendly\n- Max 60 characters",
                'format' => "Buatkan 10 variasi"
            ],
            'substack_post' => [
                'task' => 'Buatkan newsletter post untuk Substack.',
                'format' => "- Personal greeting\n- This week's topic\n- Main content\n- Key takeaways\n- CTA: Reply, share, subscribe"
            ],
            'substack_welcome' => [
                'task' => 'Buatkan welcome email untuk Substack subscribers.',
                'format' => "- Warm welcome\n- What to expect\n- Why you started\n- How to engage\n- CTA: Reply & introduce yourself"
            ],
            'patreon_tier' => [
                'task' => 'Buatkan Patreon tier description.',
                'format' => "- Tier name\n- Price point\n- What's included\n- Exclusive benefits\n- Value proposition\n\nBuatkan 3 tiers"
            ],
            'patreon_post' => [
                'task' => 'Buatkan exclusive post untuk Patreon.',
                'format' => "- Thank supporters\n- Exclusive content\n- Behind the scenes\n- Sneak peek\n- CTA: Comment & engage"
            ],
            'kofi_page' => [
                'task' => 'Buatkan Ko-fi page description.',
                'format' => "- Who you are\n- What you create\n- Why support matters\n- What supporters get\n- CTA: Buy me a coffee"
            ],
            'newsletter_intro' => [
                'task' => 'Buatkan newsletter introduction.',
                'format' => "- Hook\n- What it's about\n- Frequency\n- What subscribers get\n- CTA: Subscribe"
            ],
            'paid_content' => [
                'task' => 'Buatkan teaser untuk paid content.',
                'format' => "- Free preview (first 200 words)\n- What's behind paywall\n- Value proposition\n- CTA: Unlock full content"
            ],
        ];
    }
    
    /**
     * Affiliate Marketing Templates (6 items)
     */
    protected static function getAffiliateMarketingTemplates()
    {
        return [
            'shopee_affiliate' => [
                'task' => 'Buatkan review produk untuk Shopee Affiliate.',
                'format' => "- Hook: Personal experience\n- Product overview\n- Pros & cons (honest!)\n- Who it's for\n- Price & promo info\n- CTA: Link di bio/comment"
            ],
            'tokopedia_affiliate' => [
                'task' => 'Buatkan caption untuk Tokopedia Affiliate.',
                'format' => "- Opening hook\n- Product highlight\n- Personal recommendation\n- Promo/discount info\n- CTA: Klik link\n- Hashtag #TokopediaAffiliate"
            ],
            'tiktok_affiliate' => [
                'task' => 'Buatkan script TikTok untuk affiliate product.',
                'format' => "- Hook 1 detik: Show product\n- Demo/unboxing\n- Key benefits\n- Price reveal\n- CTA: Link di bio"
            ],
            'amazon_associates' => [
                'task' => 'Buatkan review untuk Amazon Associates.',
                'format' => "- Product intro\n- Detailed review\n- Comparison with alternatives\n- Verdict\n- Affiliate disclosure\n- CTA: Check price on Amazon"
            ],
            'product_comparison' => [
                'task' => 'Buatkan product comparison untuk affiliate.',
                'format' => "- Product A vs Product B\n- Feature comparison table\n- Pros & cons each\n- Price comparison\n- Recommendation\n- Affiliate links"
            ],
            'honest_review' => [
                'task' => 'Buatkan honest review (dengan affiliate disclosure).',
                'format' => "- Disclosure upfront\n- Unbiased review\n- Real experience\n- Pros & cons\n- Final verdict\n- Affiliate link"
            ],
        ];
    }
    
    /**
     * Blog & SEO Templates (20 items)
     */
    protected static function getBlogSEOTemplates()
    {
        return [
            'blog_post' => [
                'task' => 'Buatkan blog post yang SEO-optimized.',
                'format' => "Struktur:\n- SEO title (60 char)\n- Meta description (160 char)\n- H1 headline\n- Introduction (hook + keyword)\n- H2 subheadings (with keywords)\n- Body content\n- Conclusion + CTA\n\nBuatkan outline"
            ],
            'article_intro' => [
                'task' => 'Buatkan article introduction yang engaging.',
                'format' => "- Hook (question/stat/story)\n- Problem statement\n- What article covers\n- Why reader should care\n- Max 150 words"
            ],
            'meta_description' => [
                'task' => 'Buatkan meta description untuk SEO.',
                'criteria' => "- 150-160 characters\n- Include target keyword\n- Compelling & actionable\n- Accurate summary",
                'format' => "Buatkan 5 variasi"
            ],
            'seo_title' => [
                'task' => 'Buatkan SEO title/H1.',
                'criteria' => "- 50-60 characters\n- Include primary keyword\n- Compelling & clickable\n- Accurate to content",
                'format' => "Buatkan 10 variasi"
            ],
            'h2_h3_headings' => [
                'task' => 'Buatkan H2/H3 headings untuk article.',
                'criteria' => "- Include keywords naturally\n- Clear & descriptive\n- Logical hierarchy\n- Scannable",
                'format' => "Buatkan 8-10 headings"
            ],
            'listicle' => [
                'task' => 'Buatkan listicle article (Top 10, Best 5, dll).',
                'format' => "- Catchy title with number\n- Introduction\n- List items (each with H3)\n- Brief description per item\n- Conclusion\n\nBuatkan outline"
            ],
            'how_to_guide' => [
                'task' => 'Buatkan how-to guide/tutorial.',
                'format' => "- Title: 'How to [Goal]'\n- Introduction\n- Prerequisites\n- Step-by-step instructions\n- Tips & troubleshooting\n- Conclusion\n\nBuatkan outline"
            ],
            'product_review' => [
                'task' => 'Buatkan product review blog post.',
                'format' => "- Product overview\n- Features & specs\n- Pros & cons\n- Performance testing\n- Comparison\n- Verdict & rating\n\nBuatkan outline"
            ],
            'comparison_post' => [
                'task' => 'Buatkan comparison post (A vs B).',
                'format' => "- Title: '[A] vs [B]: Which is Better?'\n- Introduction\n- Feature comparison\n- Pros & cons each\n- Use cases\n- Recommendation\n\nBuatkan outline"
            ],
            'pillar_content' => [
                'task' => 'Buatkan pillar content/ultimate guide.',
                'format' => "- Comprehensive title\n- Table of contents\n- Multiple sections (H2)\n- In-depth coverage\n- Internal links\n- 2000+ words\n\nBuatkan outline"
            ],
            'faq_schema' => [
                'task' => 'Buatkan FAQ dengan schema markup.',
                'format' => "- 8-10 common questions\n- Concise answers\n- Include keywords\n- Schema-ready format"
            ],
            'featured_snippet' => [
                'task' => 'Buatkan content untuk featured snippet.',
                'format' => "- Direct answer (40-60 words)\n- Bullet points or numbered list\n- Clear & concise\n- Answer the query completely"
            ],
            'local_seo' => [
                'task' => 'Buatkan local SEO content.',
                'format' => "- Include location keywords\n- Local landmarks/references\n- Service area\n- Local testimonials\n- NAP (Name, Address, Phone)"
            ],
            'keyword_cluster' => [
                'task' => 'Buatkan content untuk keyword cluster.',
                'format' => "- Main keyword (pillar)\n- Related keywords (cluster)\n- Natural integration\n- Internal linking strategy\n\nBuatkan outline"
            ],
            'internal_linking' => [
                'task' => 'Buatkan internal linking anchor text.',
                'criteria' => "- Descriptive\n- Include keywords\n- Natural in context\n- Varied anchor text",
                'format' => "Buatkan 10 variasi"
            ],
            'alt_text' => [
                'task' => 'Buatkan image alt text untuk SEO.',
                'criteria' => "- Descriptive\n- Include keyword (natural)\n- Max 125 characters\n- Accessible",
                'format' => "Buatkan 5 variasi"
            ],
            'schema_markup' => [
                'task' => 'Buatkan description untuk schema markup.',
                'format' => "Types: Article schema, Product schema, FAQ schema, Review schema"
            ],
            'guest_post' => [
                'task' => 'Buatkan guest post pitch.',
                'format' => "- Personalized greeting\n- Why their blog\n- Your expertise\n- 3 topic ideas\n- Writing samples\n- CTA"
            ],
            'content_update' => [
                'task' => 'Buatkan strategy untuk content update/refresh.',
                'format' => "Checklist:\n- Update statistics/data\n- Add new sections\n- Improve readability\n- Add images/media\n- Update internal links\n\nBuatkan plan"
            ],
            'roundup_post' => [
                'task' => 'Buatkan expert roundup post.',
                'format' => "- Introduction\n- Question asked\n- Expert responses (5-10)\n- Key takeaways\n- Conclusion\n\nBuatkan outline"
            ],
        ];
    }
}
