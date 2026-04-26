<script>
    // Clipboard helper � works on HTTP (non-secure) and HTTPS
    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text);
        }
        // Fallback for non-secure contexts (HTTP)
        return new Promise((resolve, reject) => {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.cssText = 'position:fixed;top:0;left:0;opacity:0';
            document.body.appendChild(ta);
            ta.focus(); ta.select();
            try { document.execCommand('copy') ? resolve() : reject(); }
            catch (e) { reject(e); }
            finally { document.body.removeChild(ta); }
        });
    }

    // Handle feature_locked response dari middleware G�� tampilkan notif upgrade
    function handleFeatureLocked(data, showNotificationFn) {
        if (data && data.feature_locked) {
            const msg = (data.message || 'Fitur ini tidak tersedia di paket kamu.')
                + ' <a href="<?php echo e(route("pricing")); ?>" class="underline font-semibold">Upgrade sekarang G��</a>';
            // Tampilkan sebagai toast dengan link
            const toast = document.createElement('div');
            toast.innerHTML = msg;
            toast.className = 'fixed bottom-6 right-6 z-50 bg-yellow-500 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium max-w-xs cursor-pointer';
            toast.onclick = () => window.location.href = '<?php echo e(route("pricing")); ?>';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 6000);
            return true;
        }
        return false;
    }

    function aiGenerator() {
        return {
            mode: 'simple',
            generatorType: 'text',
            isFirstTimeUser: true,
            quotaRemaining: <?php echo e($quotaRemaining ?? 0); ?>,
            creditCosts: <?php echo json_encode($creditCosts ?? [], 15, 512) ?>,
            selectedRating: 0,
            ratingFeedback: '',
            rated: false,
            submittingRating: false,
            lastCaptionId: null,
            keywordInsights: [], // =��� Keyword insights data
            form: {
                category: '',
                subcategory: '',
                platform: 'instagram',
                brief: '',
                tone: 'casual',
                keywords: '',
                generate_variations: false,
                variation_count: 5, // default 5 when checkbox is checked
                auto_hashtag: true,
                local_language: ''
            },
            simpleForm: {
                content_type: '',
                subcategory: '',
                product_name: '',
                price: '',
                target_market: '',
                goal: '',
                platform: 'instagram'
            },
            simpleSubcategories: [],
            imageForm: {
                file: null,
                preview: null,
                business_type: '',
                product_name: ''
            },
            videoForm: {
                content_type: '',
                platform: '',
                duration: '30',
                product: '',
                target_audience: '',
                goal: '',
                styles: [],
                context: '',
                image_file: null,
                image_preview: null
            },
            subcategories: [],
            loading: false,
            _loadingSeconds: 0,
            _loadingTimer: null,
            result: '',
            copied: false,
            copiedIdx: null,
            previewPlatform: 'raw',
            parsedCaptions: [],
            saved: false,
            showSaveBrandVoiceModal: false,
            savingBrandVoice: false,
            brandVoices: [],
            brandVoiceForm: {
                name: '',
                brand_description: '',
                is_default: false
            },
            
            // Analysis state
            showAnalysis: false,
            analysisLoading: false,
            analysisError: null,
            analysisResult: null,
            analysisTab: 'quality',

            // =��� Performance Predictor state
            predictorForm: {
                caption: '',
                platform: 'instagram',
                industry: 'general',
                target_audience: 'general'
            },
            predictorLoading: false,
            predictionResults: null,
            predictorError: null,
            variantsLoading: false,

            // ── Multi-Platform Optimizer state (removed)
            // ── Content Repurposing state (removed)
            // ── Google Ads state (removed)
            // ── Promo Link state (removed)
            // ── Product Explainer state (removed)
            // ── SEO Metadata state (removed)
            // ── Smart Comparison state (removed)
            // ── FAQ Generator state (removed)
            // ── Reels Hook state (removed)
            // ── Quality Badge state (removed)
            // ── Discount Campaign state (removed)
            // ── Trend Tags state (removed)
            // ── Lead Magnet state (removed)
            // ── Financial Analysis state (removed)
            // ── Ebook Analysis state (removed)
            // ── Reader Trend state (removed)
            // ── Trend Alert state (removed)
                    category: 'Food',
                    popularity: '1.8M',
                    timeAgo: '4 jam lalu',
                    icon: '=���',
                    hashtags: ['#EsKepalMilo', '#MinumanViral', '#Kuliner', '#UMKM']
                },
                {
                    id: 3,
                    title: 'Gaya Fashion "Old Money Aesthetic"',
                    description: 'Trend fashion dengan gaya klasik dan elegan yang sedang populer',
                    category: 'Fashion',
                    popularity: '3.2M',
                    timeAgo: '6 jam lalu',
                    icon: '=���',
                    hashtags: ['#OldMoney', '#Fashion', '#Aesthetic', '#Style']
                },
                {
                    id: 4,
                    title: 'Teknologi AI untuk UMKM',
                    description: 'Pembahasan penggunaan AI untuk membantu bisnis UMKM berkembang',
                    category: 'Technology',
                    popularity: '950K',
                    timeAgo: '8 jam lalu',
                    icon: '=���',
                    hashtags: ['#AI', '#UMKM', '#Technology', '#DigitalTransformation']
                }
            ],
            viralIdeas: [
                {
                    id: 1,
                    title: 'Before & After Transformation',
                    description: 'Konten transformasi produk/jasa yang menunjukkan hasil dramatis',
                    type: 'Visual',
                    engagement: '85%',
                    platform: 'Instagram/TikTok',
                    icon: 'G��'
                },
                {
                    id: 2,
                    title: 'Behind The Scenes Process',
                    description: 'Tunjukkan proses pembuatan produk atau layanan dari belakang layar',
                    type: 'Educational',
                    engagement: '78%',
                    platform: 'All Platforms',
                    icon: '=�ļ'
                },
                {
                    id: 3,
                    title: 'Customer Testimonial Stories',
                    description: 'Cerita nyata pelanggan dengan hasil yang memuaskan',
                    type: 'Social Proof',
                    engagement: '92%',
                    platform: 'Instagram/Facebook',
                    icon: '=�Ƽ'
                },
                {
                    id: 4,
                    title: 'Quick Tips & Hacks',
                    description: 'Tips cepat dan mudah yang bisa langsung dipraktikkan',
                    type: 'Educational',
                    engagement: '88%',
                    platform: 'TikTok/Instagram',
                    icon: '=���'
                }
            ],
            seasonalEvents: [
                {
                    id: 1,
                    title: `Ramadan ${new Date().getFullYear()}`,
                    date: `11 Maret - 9 April ${new Date().getFullYear()}`,
                    daysLeft: 45,
                    description: 'Bulan suci Ramadan dengan berbagai peluang konten dan promo',
                    icon: '=���',
                    contentIdeas: ['Sahur Ideas', 'Iftar Menu', 'Spiritual Content', 'Charity Campaign']
                },
                {
                    id: 2,
                    title: 'Lebaran/Eid Mubarak',
                    date: `10-11 April ${new Date().getFullYear()}`,
                    daysLeft: 75,
                    description: 'Hari Raya Idul Fitri dengan tradisi mudik dan berkumpul keluarga',
                    icon: '=���',
                    contentIdeas: ['Lebaran Outfit', 'Mudik Tips', 'Family Gathering', 'THR Campaign']
                },
                {
                    id: 3,
                    title: 'Back to School',
                    date: `Juli ${new Date().getFullYear()}`,
                    daysLeft: 120,
                    description: 'Musim kembali ke sekolah dengan kebutuhan perlengkapan baru',
                    icon: '=���',
                    contentIdeas: ['School Supplies', 'Study Tips', 'Uniform Fashion', 'Parent Guide']
                },
                {
                    id: 4,
                    title: 'Indonesian Independence Day',
                    date: `17 Agustus ${new Date().getFullYear()}`,
                    daysLeft: 150,
                    description: 'Hari Kemerdekaan Indonesia dengan semangat nasionalisme',
                    icon: '=��=��',
                    contentIdeas: ['Patriotic Content', 'Local Pride', 'Indonesian Products', 'Unity Campaign']
                }
            ],
            nationalDays: [
                {
                    id: 1,
                    title: 'Hari Kartini',
                    date: `21 April ${new Date().getFullYear()}`,
                    description: 'Memperingati perjuangan R.A. Kartini untuk emansipasi wanita',
                    icon: '=��',
                    category: 'Women',
                    hashtags: ['#HariKartini', '#WomenEmpowerment', '#Emansipasi', '#PerempuanIndonesia']
                },
                {
                    id: 2,
                    title: 'Hari Pendidikan Nasional',
                    date: `2 Mei ${new Date().getFullYear()}`,
                    description: 'Memperingati hari lahir Ki Hajar Dewantara, Bapak Pendidikan Indonesia',
                    icon: '=���',
                    category: 'Education',
                    hashtags: ['#HardikNas', '#Pendidikan', '#KiHajarDewantara', '#BelajarSepanjangHayat']
                },
                {
                    id: 3,
                    title: 'Hari Kebangkitan Nasional',
                    date: `20 Mei ${new Date().getFullYear()}`,
                    description: 'Memperingati kebangkitan semangat kebangsaan Indonesia',
                    icon: '=���',
                    category: 'National',
                    hashtags: ['#HariKebangkitanNasional', '#SemangitKebangsaan', '#Indonesia', '#Patriotisme']
                },
                {
                    id: 4,
                    title: 'Hari Lingkungan Hidup Sedunia',
                    date: `5 Juni ${new Date().getFullYear()}`,
                    description: 'Kampanye global untuk kesadaran dan aksi lingkungan hidup',
                    icon: '=���',
                    category: 'Environment',
                    hashtags: ['#WorldEnvironmentDay', '#LingkunganHidup', '#GoGreen', '#SustainableLiving']
                }
            ],

            // =��� Notification toast state
            notificationVisible: false,
            notificationMessage: '',
            notificationType: 'success', // 'success' | 'error'
            _notificationTimer: null,

            showNotification(message, type = 'success') {
                this.notificationMessage = message;
                this.notificationType = type;
                this.notificationVisible = true;
                if (this._notificationTimer) clearTimeout(this._notificationTimer);
                this._notificationTimer = setTimeout(() => { this.notificationVisible = false; }, 3000);
            },

            // Initialize - check if user is first time
            async init() {
                await this.checkFirstTimeStatus();
                // Load dynamic dates
                await this.loadDynamicDates();

                // Pre-fill from onboarding data
                const ob = <?php echo json_encode($onboarding ?? [], 15, 512) ?>;
                if (ob.primary_platform) {
                    this.form.platform = ob.primary_platform;
                    this.simpleForm.platform = ob.primary_platform;
                }
                if (ob.content_goal) {
                    this.simpleForm.goal = ob.content_goal;
                }

                // Watch result changes ? parse into individual captions
                this.$watch('result', (val) => {
                    this.parsedCaptions = this.parseResultToCaptions(val);
                    this.copiedIdx = null;
                });
            },

            // =��� Load dynamic dates from API
            async loadDynamicDates() {
                try {
                    // Load seasonal events
                    const seasonalResponse = await fetch('/api/dynamic-dates/seasonal-events');
                    if (seasonalResponse.ok) {
                        const seasonalData = await seasonalResponse.json();
                        if (seasonalData.success) {
                            this.seasonalEvents = seasonalData.data;
                        }
                    }

                    // Load national days
                    const nationalResponse = await fetch('/api/dynamic-dates/national-days');
                    if (nationalResponse.ok) {
                        const nationalData = await nationalResponse.json();
                        if (nationalData.success) {
                            this.nationalDays = nationalData.data;
                        }
                    }

                } catch (error) {
                    // Keep the existing hardcoded data as fallback
                }
            },
            
            async checkFirstTimeStatus() {
                try {
                    const response = await fetch('/api/check-first-time', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        this.isFirstTimeUser = data.is_first_time || false;
                    }
                } catch (error) {
                    // Default to true if error
                    this.isFirstTimeUser = true;
                }
            },
            
            subcategoryOptions: {
                quick_templates: [
                    {value: 'caption_instagram', label: '=��+ Caption Instagram'},
                    {value: 'caption_facebook', label: '=��� Caption Facebook'},
                    {value: 'caption_tiktok', label: '=�Ħ Caption TikTok'},
                    {value: 'caption_youtube', label: '=��� Caption YouTube'},
                    {value: 'caption_linkedin', label: '=��+ Caption LinkedIn'},
                    {value: 'hook_opening', label: '=��� Hook Pembuka (3 detik)'},
                    {value: 'hook_video', label: '=�ļ Hook Video Ads'},
                    {value: 'quotes_motivasi', label: '=�Ƭ Quotes Motivasi'},
                    {value: 'quotes_bisnis', label: '=��+ Quotes Bisnis'},
                    {value: 'humor_content', label: '=��� Konten Humor'},
                    {value: 'viral_content', label: '=��� Konten Viral'},
                    {value: 'storytelling_short', label: '=��� Storytelling Pendek'},
                    {value: 'cta_powerful', label: 'G�� Call To Action Kuat'},
                    {value: 'headline_catchy', label: 'G�� Headline Menarik'}
                ],
                viral_clickbait: [
                    {value: 'clickbait_title', label: '=�Ļ Clickbait Title (Honest)'},
                    {value: 'curiosity_gap', label: '=��� Curiosity Gap Hook'},
                    {value: 'shocking_statement', label: '=��� Shocking Statement'},
                    {value: 'controversial_take', label: '=��� Controversial Take'},
                    {value: 'before_after', label: '=��� Before & After Story'},
                    {value: 'secret_reveal', label: '=��� Secret Reveal'},
                    {value: 'mistake_warning', label: 'G��n+� Mistake Warning'},
                    {value: 'myth_busting', label: '=��� Myth Busting'},
                    {value: 'unpopular_opinion', label: '=���n+� Unpopular Opinion'},
                    {value: 'life_hack', label: '=��� Life Hack / Tips Viral'},
                    {value: 'challenge_trend', label: '=�ī Challenge / Trend'},
                    {value: 'reaction_bait', label: '=�Ƽ Reaction Bait'},
                    {value: 'cliffhanger', label: 'G�+n+� Cliffhanger Ending'},
                    {value: 'number_list', label: '=��� Number List (5 Cara, 10 Tips)'},
                    {value: 'question_hook', label: 'G�� Question Hook'},
                    {value: 'emotional_trigger', label: '=��� Emotional Trigger'},
                    {value: 'fomo_content', label: 'GŦ FOMO Content'},
                    {value: 'plot_twist', label: '=�ġ Plot Twist Story'},
                    {value: 'relatable_content', label: '=��� Relatable Content'},
                    {value: 'storytime', label: '=��� Storytime (Viral Format)'}
                ],
                trend_fresh_ideas: [
                    {value: 'trending_topic', label: '=��� Trending Topic Ideas'},
                    {value: 'viral_challenge', label: '=�Ļ Viral Challenge Ideas'},
                    {value: 'seasonal_content', label: '=��� Seasonal Content Ideas'},
                    {value: 'holiday_campaign', label: '=��� Holiday Campaign Ideas'},
                    {value: 'current_events', label: '=��� Current Events Angle'},
                    {value: 'meme_marketing', label: '=��� Meme Marketing Ideas'},
                    {value: 'tiktok_trend', label: '=�Ħ TikTok Trend Ideas'},
                    {value: 'instagram_trend', label: '=��� Instagram Trend Ideas'},
                    {value: 'youtube_trend', label: '=��� YouTube Trend Ideas'},
                    {value: 'x_trend', label: 'X Trend Ideas'},
                    {value: 'content_series', label: '=��� Content Series Ideas'},
                    {value: 'collaboration_ideas', label: '=�� Collaboration Ideas'},
                    {value: 'giveaway_ideas', label: '=��� Giveaway Campaign Ideas'},
                    {value: 'user_generated', label: '=��� User Generated Content Ideas'},
                    {value: 'behind_scenes', label: '=�ļ Behind The Scenes Ideas'},
                    {value: 'educational_series', label: '=��� Educational Series Ideas'},
                    {value: 'storytelling_series', label: '=��� Storytelling Series Ideas'},
                    {value: 'product_launch', label: '=��� Product Launch Ideas'},
                    {value: 'rebranding_ideas', label: 'G�� Rebranding Campaign Ideas'},
                    {value: 'crisis_content', label: '=��� Crisis/Comeback Content Ideas'}
                ],
                industry_presets: [
                    {value: 'fashion_clothing', label: '=��� Fashion & Pakaian'},
                    {value: 'food_beverage', label: '=��� Makanan & Minuman'},
                    {value: 'beauty_skincare', label: '=��� Kecantikan & Skincare'},
                    {value: 'printing_service', label: '=���n+� Jasa Printing & Percetakan'},
                    {value: 'photography', label: '=��+ Jasa Fotografi'},
                    {value: 'catering', label: '=�� Catering & Katering'},
                    {value: 'tiktok_shop', label: '=���n+� TikTok Shop'},
                    {value: 'shopee_affiliate', label: '=��� Affiliate Shopee'},
                    {value: 'home_decor', label: '=��� Dekorasi Rumah'},
                    {value: 'handmade_craft', label: 'G��n+� Kerajinan Tangan'},
                    {value: 'digital_service', label: '=��+ Jasa Digital'},
                    {value: 'automotive', label: '=��� Otomotif & Aksesoris'}
                ],
                website_landing: [
                    {value: 'headline', label: 'Headline Halaman Utama'},
                    {value: 'subheadline', label: 'Subheadline'},
                    {value: 'service_description', label: 'Deskripsi Layanan'},
                    {value: 'about_us', label: 'Tentang Kami'},
                    {value: 'cta', label: 'Call To Action'},
                    {value: 'faq', label: 'FAQ'},
                    {value: 'pricing_page', label: 'Halaman Pricing'},
                    {value: 'product_description', label: 'Deskripsi Produk Digital'}
                ],
                marketing_funnel: [
                    {value: 'tofu_awareness', label: '=�Ļ TOFU - Awareness Stage (Top of Funnel)'},
                    {value: 'tofu_blog_post', label: '=��� TOFU - Blog Post Edukatif'},
                    {value: 'tofu_social_media', label: '=��� TOFU - Social Media Content'},
                    {value: 'tofu_video_content', label: '=�ļ TOFU - Video Content Script'},
                    {value: 'mofu_consideration', label: '=��� MOFU - Consideration Stage (Middle of Funnel)'},
                    {value: 'mofu_case_study', label: '=��� MOFU - Case Study / Success Story'},
                    {value: 'mofu_comparison', label: 'G��n+� MOFU - Product Comparison'},
                    {value: 'mofu_webinar', label: '=��� MOFU - Webinar / Workshop Copy'},
                    {value: 'mofu_email_nurture', label: '=��� MOFU - Email Nurture Sequence'},
                    {value: 'bofu_decision', label: '=�Ʀ BOFU - Decision Stage (Bottom of Funnel)'},
                    {value: 'bofu_sales_page', label: '=�Ļ BOFU - Sales Page Copy'},
                    {value: 'bofu_demo_trial', label: '=��� BOFU - Demo / Free Trial Copy'},
                    {value: 'bofu_testimonial', label: 'G�� BOFU - Testimonial / Social Proof'},
                    {value: 'bofu_urgency', label: 'GŦ BOFU - Urgency & Scarcity Copy'},
                    {value: 'retention_onboarding', label: '=��� Retention - Onboarding Sequence'},
                    {value: 'retention_upsell', label: '=��� Retention - Upsell / Cross-sell'},
                    {value: 'retention_reactivation', label: '=��� Retention - Reactivation Campaign'},
                    {value: 'complete_funnel', label: '=�Ļ Complete Funnel Sequence (All Stages)'}
                ],
                sales_page: [
                    {value: 'complete_sales_page', label: '=�Ļ Complete Sales Page (Full Structure)'},
                    {value: 'hero_section', label: '=��+ Hero Section (Headline + Subheadline + CTA)'},
                    {value: 'problem_agitate', label: '=��� Problem & Agitate Section'},
                    {value: 'solution_presentation', label: 'G�� Solution Presentation'},
                    {value: 'features_benefits', label: 'G�� Features & Benefits Section'},
                    {value: 'social_proof', label: 'G�� Social Proof (Testimonials + Reviews)'},
                    {value: 'pricing_section', label: '=�Ʀ Pricing Section (Value Stack)'},
                    {value: 'faq_objections', label: 'G�� FAQ & Objection Handling'},
                    {value: 'guarantee_risk', label: '=���n+� Guarantee / Risk Reversal'},
                    {value: 'urgency_scarcity', label: 'GŦ Urgency & Scarcity Elements'},
                    {value: 'final_cta', label: '=�Ļ Final CTA (Call to Action)'},
                    {value: 'bonus_stack', label: '=��� Bonus Stack Section'},
                    {value: 'about_creator', label: '=��� About Creator / Company'},
                    {value: 'vsl_script', label: '=�ļ VSL (Video Sales Letter) Script'},
                    {value: 'webinar_sales', label: '=��� Webinar Sales Pitch'},
                    {value: 'product_launch_sales', label: '=��� Product Launch Sales Page'},
                    {value: 'saas_sales_page', label: '=��+ SaaS Sales Page'},
                    {value: 'course_sales_page', label: '=��� Course Sales Page'},
                    {value: 'coaching_sales_page', label: '=�Ļ Coaching/Consulting Sales Page'},
                    {value: 'ecommerce_product_page', label: '=���n+� E-commerce Product Page'}
                ],
                lead_magnet: [
                    {value: 'ebook_landing', label: '=��� Free eBook Landing Page'},
                    {value: 'checklist_template', label: 'G�� Checklist / Template Opt-in'},
                    {value: 'webinar_registration', label: '=��� Webinar Registration Page'},
                    {value: 'free_trial', label: '=��� Free Trial Sign-up Copy'},
                    {value: 'resource_library', label: '=��� Resource Library Access'},
                    {value: 'quiz_assessment', label: '=��� Quiz / Assessment Lead Magnet'},
                    {value: 'video_series', label: '=�ļ Video Series Opt-in'},
                    {value: 'mini_course', label: '=��� Mini Course / Email Course'},
                    {value: 'toolkit_bundle', label: '=��� Toolkit / Bundle Offer'},
                    {value: 'cheat_sheet', label: '=��� Cheat Sheet / Quick Guide'},
                    {value: 'case_study_download', label: '=��� Case Study Download'},
                    {value: 'whitepaper_report', label: '=��� Whitepaper / Industry Report'},
                    {value: 'swipe_file', label: '=��� Swipe File / Templates'},
                    {value: 'calculator_tool', label: '=��� Calculator / Tool Access'},
                    {value: 'challenge_signup', label: '=��� Challenge Sign-up Page'},
                    {value: 'consultation_booking', label: '=��P Free Consultation Booking'},
                    {value: 'demo_request', label: '=�Ļ Demo Request Page'},
                    {value: 'newsletter_signup', label: '=��� Newsletter Sign-up Copy'},
                    {value: 'discount_coupon', label: '=�ăn+� Discount Coupon Opt-in'},
                    {value: 'lead_magnet_delivery', label: '=��� Lead Magnet Delivery Email'}
                ],
                ads: [
                    {value: 'headline', label: 'Headline Iklan'},
                    {value: 'body_text', label: 'Body Text'},
                    {value: 'hook_3sec', label: 'Hook 3 Detik Pertama'},
                    {value: 'video_script', label: 'Script Video Promosi'},
                    {value: 'caption_promo', label: 'Caption Promosi'}
                ],
                social_media: [
                    {value: 'instagram_caption', label: 'Caption Instagram'},
                    {value: 'instagram_reels', label: 'Script Instagram Reels'},
                    {value: 'facebook_post', label: 'Facebook Post'},
                    {value: 'tiktok_caption', label: 'Caption TikTok'},
                    {value: 'youtube_description', label: 'Deskripsi YouTube'},
                    {value: 'linkedin_post', label: 'LinkedIn Post'},
                    {value: 'thread_edukasi', label: 'Thread Edukasi'},
                    {value: 'storytelling', label: 'Konten Storytelling'},
                    {value: 'soft_selling', label: 'Soft Selling'},
                    {value: 'hard_selling', label: 'Hard Selling'},
                    {value: 'reels_tiktok_script', label: 'Script Reels/TikTok'},
                    {value: 'educational_content', label: 'Konten Edukasi'}
                ],
                marketplace: [
                    {value: 'product_title', label: 'Judul Produk'},
                    {value: 'product_description', label: 'Deskripsi Produk'},
                    {value: 'bullet_benefits', label: 'Bullet Benefit'},
                    {value: 'faq', label: 'FAQ Produk'},
                    {value: 'auto_reply', label: 'Auto-Reply Chat'},
                    {value: 'promo_banner', label: 'Banner Promo'},
                    {value: 'flash_sale', label: 'Flash Sale Copy'}
                ],
                event_promo: [
                    {value: 'grand_opening', label: '=��� Grand Opening'},
                    {value: 'flash_sale', label: 'G�� Flash Sale / Sale Kilat'},
                    {value: 'discount_promo', label: '=�Ʀ Diskon & Promo Spesial'},
                    {value: 'bazaar', label: '=���n+� Bazaar / Pameran'},
                    {value: 'exhibition', label: '=�Ŀ Exhibition / Pameran Seni'},
                    {value: 'workshop', label: '=��G��=�Ž Workshop / Seminar'},
                    {value: 'product_launch', label: '=��� Product Launch'},
                    {value: 'anniversary', label: '=��� Anniversary / Ulang Tahun'},
                    {value: 'seasonal_promo', label: '=��� Promo Musiman (Lebaran, Natal, dll)'},
                    {value: 'clearance_sale', label: '=��+n+� Clearance Sale / Obral'},
                    {value: 'buy_1_get_1', label: '=��� Buy 1 Get 1 / Bundling'},
                    {value: 'loyalty_program', label: 'G�� Program Loyalitas / Member'},
                    {value: 'giveaway', label: '=��� Giveaway / Kuis Berhadiah'},
                    {value: 'pre_order', label: '=��� Pre-Order Campaign'},
                    {value: 'limited_edition', label: '=��� Limited Edition / Exclusive'},
                    {value: 'collaboration', label: '=�� Kolaborasi Brand'},
                    {value: 'charity_event', label: 'G��n+� Event Charity / Sosial'},
                    {value: 'meet_greet', label: '=��� Meet & Greet / Gathering'},
                    {value: 'live_shopping', label: '=��� Live Shopping / Live Selling'},
                    {value: 'countdown_promo', label: 'GŦ Countdown Promo (24 Jam, 3 Hari, dll)'}
                ],
                hr_recruitment: [
                    {value: 'job_description', label: '=��� Job Description / JD'},
                    {value: 'job_vacancy', label: '=��� Lowongan Kerja / Vacancy Post'},
                    {value: 'job_requirements', label: 'G�� Job Requirements / Kualifikasi'},
                    {value: 'company_culture', label: '=��� Company Culture Description'},
                    {value: 'employee_benefits', label: '=��� Employee Benefits Package'},
                    {value: 'interview_questions', label: 'G�� Interview Questions'},
                    {value: 'offer_letter', label: '=��� Offer Letter'},
                    {value: 'rejection_letter', label: '=��� Rejection Letter (Polite)'},
                    {value: 'onboarding_message', label: '=��� Onboarding Welcome Message'},
                    {value: 'internship_program', label: '=��� Internship Program Description'},
                    {value: 'career_page', label: '=��+ Career Page Content'},
                    {value: 'linkedin_job_post', label: '=��+ LinkedIn Job Post'},
                    {value: 'instagram_hiring', label: '=��� Instagram Hiring Post'},
                    {value: 'whatsapp_recruitment', label: '=�Ƽ WhatsApp Recruitment Message'},
                    {value: 'employee_referral', label: '=�� Employee Referral Program'},
                    {value: 'job_fair_booth', label: '=�Ĭ Job Fair Booth Description'},
                    {value: 'campus_recruitment', label: '=��� Campus Recruitment Pitch'},
                    {value: 'remote_job', label: '=��� Remote Job Description'},
                    {value: 'freelance_job', label: '=��+ Freelance Job Brief'},
                    {value: 'part_time_job', label: 'GŦ Part-Time Job Post'}
                ],
                branding_tagline: [
                    {value: 'brand_tagline', label: 'G�� Brand Tagline / Slogan'},
                    {value: 'company_tagline', label: '=��� Company Tagline'},
                    {value: 'product_tagline', label: '=��� Product Tagline'},
                    {value: 'brand_name', label: '=�Ļ Brand Name Ideas'},
                    {value: 'product_name', label: '=��+n+� Product Name Ideas'},
                    {value: 'business_name', label: '=��+ Business Name Ideas'},
                    {value: 'tshirt_quote', label: '=��� T-Shirt Quote / Text'},
                    {value: 'hoodie_text', label: '=��� Hoodie Text'},
                    {value: 'tote_bag_text', label: '=�� Tote Bag Text'},
                    {value: 'mug_text', label: 'G�� Mug Text'},
                    {value: 'sticker_text', label: '=��+n+� Sticker Text'},
                    {value: 'poster_quote', label: '=��+n+� Poster Quote'},
                    {value: 'motivational_quote', label: '=�Ƭ Motivational Quote'},
                    {value: 'funny_quote', label: '=��� Funny Quote'},
                    {value: 'inspirational_quote', label: 'G�� Inspirational Quote'},
                    {value: 'logo_text', label: '=�Ŀ Logo Text / Wordmark'},
                    {value: 'brand_story', label: '=��� Brand Story (Short)'},
                    {value: 'brand_mission', label: '=�Ļ Brand Mission Statement'},
                    {value: 'brand_vision', label: '=��� Brand Vision Statement'},
                    {value: 'brand_values', label: '=��� Brand Values'},
                    {value: 'usp', label: 'G�� USP (Unique Selling Proposition)'},
                    {value: 'elevator_pitch', label: '=��� Elevator Pitch (30 detik)'},
                    {value: 'brand_positioning', label: '=��� Brand Positioning Statement'},
                    {value: 'catchphrase', label: '=���n+� Catchphrase / Jargon Brand'},
                    {value: 'merchandise_collection', label: '=��� Merchandise Collection Name'}
                ],
                education_institution: [
                    {value: 'school_achievement', label: '=��� Pencapaian Sekolah/Kampus'},
                    {value: 'student_achievement', label: 'G�� Prestasi Siswa/Mahasiswa'},
                    {value: 'graduation_announcement', label: '=��� Pengumuman Kelulusan'},
                    {value: 'new_student_admission', label: '=��� Penerimaan Siswa Baru (PSB/PPDB)'},
                    {value: 'school_event', label: '=��� Event Sekolah/Kampus'},
                    {value: 'national_holiday', label: '=��=�� Hari Besar Nasional'},
                    {value: 'education_day', label: '=��� Hari Pendidikan (Hardiknas, dll)'},
                    {value: 'teacher_day', label: '=��G��=�Ž Hari Guru'},
                    {value: 'independence_day', label: '=��� HUT RI / Kemerdekaan'},
                    {value: 'religious_holiday', label: '=��� Hari Besar Keagamaan'},
                    {value: 'school_anniversary', label: '=��� HUT Sekolah/Kampus'},
                    {value: 'academic_info', label: '=��� Informasi Akademik'},
                    {value: 'exam_announcement', label: '=��� Pengumuman Ujian'},
                    {value: 'scholarship_info', label: '=�Ʀ Info Beasiswa'},
                    {value: 'extracurricular', label: 'G�+ Kegiatan Ekstrakurikuler'},
                    {value: 'parent_meeting', label: '=��G��=��G��=�� Rapat Orang Tua'},
                    {value: 'school_facility', label: '=�Ž Fasilitas Sekolah/Kampus'},
                    {value: 'teacher_profile', label: '=��G��=�Ž Profil Guru/Dosen'},
                    {value: 'alumni_success', label: '=�� Kisah Sukses Alumni'},
                    {value: 'government_program', label: '=�Ţn+� Program Pemerintah/Dinas'},
                    {value: 'public_service', label: '=��� Layanan Publik'},
                    {value: 'government_announcement', label: '=��� Pengumuman Resmi Instansi'},
                    {value: 'community_program', label: '=�� Program Kemasyarakatan'},
                    {value: 'health_campaign', label: '=��� Kampanye Kesehatan'},
                    {value: 'safety_awareness', label: 'G��n+� Sosialisasi Keselamatan'}
                ],
                video_monetization: [
                    {value: 'tiktok_viral', label: '=�Ħ TikTok - Konten Viral'},
                    {value: 'youtube_long', label: '=��� YouTube - Video Panjang'},
                    {value: 'youtube_shorts', label: '=�ļ YouTube Shorts'},
                    {value: 'facebook_video', label: '=��� Facebook Video'},
                    {value: 'snack_video', label: '=��+ Snack Video'},
                    {value: 'likee', label: 'G��n+� Likee'},
                    {value: 'kwai', label: '=��� Kwai'},
                    {value: 'bigo_live', label: '=��� Bigo Live'},
                    {value: 'nimo_tv', label: '=�ī Nimo TV'}
                ],
                photo_monetization: [
                    {value: 'shutterstock', label: '=��+ Shutterstock - Deskripsi Foto'},
                    {value: 'adobe_stock', label: '=�Ŀ Adobe Stock - Keywords'},
                    {value: 'getty_images', label: '=��+n+� Getty Images - Caption'},
                    {value: 'istock', label: '=��+ iStock - Metadata'},
                    {value: 'freepik', label: '=�ġ Freepik - Tags'},
                    {value: 'vecteezy', label: '=�Ŀ Vecteezy - Description'}
                ],
                print_on_demand: [
                    {value: 'redbubble', label: '=�Ŀ Redbubble - Product Title'},
                    {value: 'teespring', label: '=��� Teespring - Description'},
                    {value: 'spreadshirt', label: '=��� Spreadshirt - Tags'},
                    {value: 'zazzle', label: '=��� Zazzle - Product Copy'},
                    {value: 'society6', label: '=��+n+� Society6 - Bio'}
                ],
                freelance: [
                    {value: 'upwork_proposal', label: '=��+ Upwork - Proposal'},
                    {value: 'fiverr_gig', label: '=�Ļ Fiverr - Gig Description'},
                    {value: 'freelancer_bid', label: '=��� Freelancer - Bid'},
                    {value: 'sribulancer', label: '=��=�� Sribulancer - Penawaran'},
                    {value: 'projects_id', label: '=��=�� Projects.co.id - Proposal'},
                    {value: 'portfolio', label: '=��� Portfolio Description'},
                    {value: 'cover_letter', label: 'G��n+� Cover Letter'}
                ],
                digital_products: [
                    {value: 'gumroad', label: '=���n+� Gumroad - Product Page'},
                    {value: 'sellfy', label: '=�Ʀ Sellfy - Sales Copy'},
                    {value: 'payhip', label: '=�Ʀ Payhip - Description'},
                    {value: 'ebook_description', label: '=��� E-book Description'},
                    {value: 'course_landing', label: '=��� Course Landing Page'},
                    {value: 'template_description', label: '=��� Template Description'}
                ],
                ebook_publishing: [
                    {value: 'kindle_description', label: '=��� Amazon Kindle - Book Description'},
                    {value: 'kindle_blurb', label: '=��� Kindle - Back Cover Blurb'},
                    {value: 'google_play_books', label: '=��� Google Play Books - Description'},
                    {value: 'apple_books', label: '=��� Apple Books - Synopsis'},
                    {value: 'kobo', label: '=��� Kobo Writing Life - Description'},
                    {value: 'barnes_noble', label: '=��� Barnes & Noble Press - Copy'},
                    {value: 'leanpub', label: '=��� Leanpub - Sales Page'},
                    {value: 'gumroad_ebook', label: '=���n+� Gumroad - eBook Landing'},
                    {value: 'gramedia_digital', label: '=��=�� Gramedia Digital - Deskripsi'},
                    {value: 'mizanstore', label: '=��=�� Mizanstore - Sinopsis'},
                    {value: 'kubuku', label: '=��=�� Kubuku - Description'},
                    {value: 'storial', label: '=��=�� Storial - Cerita'},
                    {value: 'book_title', label: 'G�� Book Title Generator'},
                    {value: 'chapter_outline', label: '=��� Chapter Outline'},
                    {value: 'author_bio', label: '=��� Author Bio'}
                ],
                academic_writing: [
                    {value: 'abstract', label: '=��� Abstract / Abstrak'},
                    {value: 'research_title', label: '=�Ļ Research Title'},
                    {value: 'introduction', label: '=��� Introduction'},
                    {value: 'literature_review', label: '=��� Literature Review Outline'},
                    {value: 'methodology', label: '=��� Methodology Description'},
                    {value: 'conclusion', label: 'G�� Conclusion'},
                    {value: 'keywords', label: '=��� Keywords Generator'},
                    {value: 'researchgate_profile', label: '=��� ResearchGate - Profile'},
                    {value: 'academia_bio', label: '=��� Academia.edu - Bio'},
                    {value: 'paper_summary', label: '=��� Paper Summary'},
                    {value: 'conference_abstract', label: '=��� Conference Abstract'}
                ],
                writing_monetization: [
                    {value: 'medium_article', label: '=��� Medium - Article'},
                    {value: 'medium_headline', label: 'G�� Medium - Headline'},
                    {value: 'substack_post', label: '=��� Substack - Newsletter Post'},
                    {value: 'substack_welcome', label: '=��� Substack - Welcome Email'},
                    {value: 'patreon_tier', label: '=��� Patreon - Tier Description'},
                    {value: 'patreon_post', label: '=��� Patreon - Exclusive Post'},
                    {value: 'kofi_page', label: 'G�� Ko-fi - Page Description'},
                    {value: 'newsletter_intro', label: '=��� Newsletter Introduction'},
                    {value: 'paid_content', label: '=�Ʀ Paid Content Teaser'}
                ],
                affiliate_marketing: [
                    {value: 'shopee_affiliate', label: '=��� Shopee Affiliate - Review'},
                    {value: 'tokopedia_affiliate', label: '=���n+� Tokopedia Affiliate - Caption'},
                    {value: 'tiktok_affiliate', label: '=�Ħ TikTok Affiliate - Script'},
                    {value: 'amazon_associates', label: '=��� Amazon Associates - Review'},
                    {value: 'product_comparison', label: 'G��n+� Product Comparison'},
                    {value: 'honest_review', label: 'G�� Honest Review'}
                ],
                blog_seo: [
                    {value: 'blog_post', label: '=��� Blog Post (SEO Optimized)'},
                    {value: 'article_intro', label: '=�Ļ Article Introduction'},
                    {value: 'meta_description', label: '=��� Meta Description'},
                    {value: 'seo_title', label: '=��� SEO Title / H1'},
                    {value: 'h2_h3_headings', label: '=��� H2/H3 Headings Generator'},
                    {value: 'listicle', label: '=��� Listicle Article (Top 10, Best 5)'},
                    {value: 'how_to_guide', label: '=��� How-to Guide / Tutorial'},
                    {value: 'product_review', label: 'G�� Product Review Blog'},
                    {value: 'comparison_post', label: 'G��n+� Comparison Post (A vs B)'},
                    {value: 'pillar_content', label: '=�Ţn+� Pillar Content / Ultimate Guide'},
                    {value: 'faq_schema', label: 'G�� FAQ Schema Markup'},
                    {value: 'featured_snippet', label: '=�Ļ Featured Snippet Optimization'},
                    {value: 'local_seo', label: '=��� Local SEO Content'},
                    {value: 'keyword_cluster', label: '=��� Keyword Cluster Content'},
                    {value: 'internal_linking', label: '=��� Internal Linking Anchor Text'},
                    {value: 'alt_text', label: '=��+n+� Image Alt Text'},
                    {value: 'schema_markup', label: '=��� Schema Markup Description'},
                    {value: 'guest_post', label: 'G��n+� Guest Post Pitch'},
                    {value: 'content_update', label: '=��� Content Update/Refresh'},
                    {value: 'roundup_post', label: '=��� Roundup Post (Expert Roundup)'}
                ],
                email_whatsapp: [
                    {value: 'broadcast_promo', label: 'Broadcast Promo'},
                    {value: 'follow_up', label: 'Follow Up Calon Client'},
                    {value: 'partnership_offer', label: 'Penawaran Kerja Sama'},
                    {value: 'payment_reminder', label: 'Reminder Pembayaran'},
                    {value: 'closing_script', label: 'Script Closing'},
                    {value: 'welcome_email', label: 'Welcome Email'},
                    {value: 'abandoned_cart', label: 'Abandoned Cart'}
                ],
                proposal_company: [
                    {value: 'project_proposal', label: 'Proposal Proyek'},
                    {value: 'company_profile', label: 'Company Profile'},
                    {value: 'service_offer', label: 'Penawaran Jasa'},
                    {value: 'pitch_deck', label: 'Pitch Deck SaaS'},
                    {value: 'investor_presentation', label: 'Presentasi Investor'}
                ],
                personal_branding: [
                    {value: 'instagram_bio', label: 'Bio Instagram'},
                    {value: 'linkedin_summary', label: 'LinkedIn Summary'},
                    {value: 'freelance_profile', label: 'Deskripsi Profil Freelance'},
                    {value: 'portfolio', label: 'Portofolio'},
                    {value: 'about_me', label: 'About Me'}
                ],
                ux_writing: [
                    {value: 'feature_name', label: 'Nama Fitur'},
                    {value: 'feature_description', label: 'Deskripsi Fitur'},
                    {value: 'onboarding_message', label: 'Onboarding Message'},
                    {value: 'notification', label: 'Notifikasi Dalam Aplikasi'},
                    {value: 'error_message', label: 'Error Message'},
                    {value: 'empty_state', label: 'Empty State'},
                    {value: 'success_message', label: 'Success Message'},
                    {value: 'button_copy', label: 'Button Copy'}
                ],
                invitation_event: [
                    {value: 'wedding', label: '=��� Undangan Pernikahan'},
                    {value: 'engagement', label: '=��� Undangan Lamaran / Tunangan'},
                    {value: 'birthday', label: '=��� Undangan Ulang Tahun'},
                    {value: 'aqiqah', label: '=�� Undangan Aqiqah / Syukuran Bayi'},
                    {value: 'khitanan', label: '=��� Undangan Khitanan / Sunatan'},
                    {value: 'graduation', label: '=��� Undangan Wisuda'},
                    {value: 'grand_opening', label: '=��� Undangan Grand Opening'},
                    {value: 'meeting', label: '=��� Undangan Rapat / Meeting'},
                    {value: 'seminar', label: '=��� Undangan Seminar / Workshop'},
                    {value: 'reunion', label: '=��� Undangan Reuni / Gathering'},
                    {value: 'other_event', label: '=��� Undangan Acara Lainnya'},
                    {value: 'wedding_caption', label: '=��� Caption Pernikahan (Social Media)'},
                    {value: 'event_announcement', label: '=��� Pengumuman Event'},
                    {value: 'save_the_date', label: '=��� Save The Date'},
                    {value: 'thank_you_card', label: '=��� Kartu Ucapan Terima Kasih'},
                    {value: 'rsvp_message', label: 'G��n+� Pesan RSVP / Konfirmasi Kehadiran'}
                ],
                short_drama: [
                    {value: 'drama_script', label: '=�ļ Script Short Drama (Full Scene)'},
                    {value: 'romantic_dialogue', label: '=��� Percakapan Romantis (Baper)'},
                    {value: 'conflict_scene', label: '=��� Adegan Konflik / Pertengkaran'},
                    {value: 'plot_twist_drama', label: '=��� Plot Twist Drama'},
                    {value: 'character_monologue', label: '=�ġ Monolog Karakter (Emosional)'},
                    {value: 'drama_opening', label: '=��� Opening Scene (Hook 30 Detik)'},
                    {value: 'breakup_scene', label: '=��� Adegan Putus Cinta'},
                    {value: 'reunion_scene', label: '=�Ѧ Adegan Reuni Mengharukan'},
                    {value: 'misunderstanding_scene', label: '=��� Adegan Salah Paham'},
                    {value: 'confession_scene', label: '=��� Adegan Confess Perasaan'},
                    {value: 'villain_dialogue', label: '=��� Dialog Villain / Antagonis'},
                    {value: 'family_drama', label: '=��G��=��G��=�� Drama Keluarga'},
                    {value: 'office_romance', label: '=��+ Office Romance'},
                    {value: 'enemies_to_lovers', label: 'G��n+� Enemies to Lovers'},
                    {value: 'second_chance_romance', label: '=��� Second Chance Romance (Mantan)'}
                ]
            },
            
            updateSubcategories() {
                this.subcategories = this.subcategoryOptions[this.form.category] || [];
                this.form.subcategory = '';
            },
            
            updateSimpleSubcategories() {
                this.simpleSubcategories = this.subcategoryOptions[this.simpleForm.content_type] || [];
                this.simpleForm.subcategory = '';
            },

            // Image Caption Methods
            handleImageSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.imageForm.file = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imageForm.preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            handleImageDrop(event) {
                event.target.classList.remove('border-blue-400', 'bg-blue-50');
                const file = event.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    this.imageForm.file = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imageForm.preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            removeImage() {
                this.imageForm.file = null;
                this.imageForm.preview = null;
                this.$refs.imageInput.value = '';
            },

            // Video Generator Methods
            setVideoPreset(preset) {
                switch(preset) {
                    case 'viral-tiktok':
                        this.videoForm.content_type = 'script';
                        this.videoForm.platform = 'tiktok';
                        this.videoForm.duration = '30';
                        this.videoForm.goal = 'viral';
                        this.videoForm.styles = ['trending', 'funny'];
                        this.videoForm.context = 'Video viral TikTok dengan hook kuat dan trending sound';
                        break;
                    case 'product-demo':
                        this.videoForm.content_type = 'script';
                        this.videoForm.platform = 'instagram-reels';
                        this.videoForm.duration = '60';
                        this.videoForm.goal = 'sales';
                        this.videoForm.styles = ['professional', 'casual'];
                        this.videoForm.context = 'Demo produk dengan showcase benefit dan testimoni';
                        break;
                    case 'educational':
                        this.videoForm.content_type = 'script';
                        this.videoForm.platform = 'youtube-shorts';
                        this.videoForm.duration = '60';
                        this.videoForm.goal = 'education';
                        this.videoForm.styles = ['professional'];
                        this.videoForm.context = 'Konten edukatif dengan tips dan tutorial step-by-step';
                        break;
                }
            },

            // Video Image Upload Methods
            handleVideoImageSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.videoForm.image_file = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.videoForm.image_preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            handleVideoImageDrop(event) {
                event.target.classList.remove('border-red-400', 'bg-red-50');
                const file = event.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    this.videoForm.image_file = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.videoForm.image_preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            removeVideoImage() {
                this.videoForm.image_file = null;
                this.videoForm.image_preview = null;
                this.$refs.videoImageInput.value = '';
            },

            async generateVideoContent() {
                if (!this.videoForm.content_type || !this.videoForm.platform || !this.videoForm.product || !this.videoForm.goal) {
                    alert('Mohon lengkapi semua field yang wajib diisi');
                    return;
                }

                this.loading = true;
                this.result = '';
                this.error = '';

                // Use FormData if image is uploaded, otherwise JSON
                let requestData;
                let headers = {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                };

                if (this.videoForm.image_file) {
                    // Use FormData for image upload
                    requestData = new FormData();
                    requestData.append('content_type', this.videoForm.content_type);
                    requestData.append('platform', this.videoForm.platform);
                    requestData.append('duration', this.videoForm.duration);
                    requestData.append('product', this.videoForm.product);
                    requestData.append('target_audience', this.videoForm.target_audience);
                    requestData.append('goal', this.videoForm.goal);
                    requestData.append('styles', JSON.stringify(this.videoForm.styles));
                    requestData.append('context', this.videoForm.context);
                    requestData.append('product_image', this.videoForm.image_file);
                } else {
                    // Use JSON for text-only
                    requestData = JSON.stringify({
                        content_type: this.videoForm.content_type,
                        platform: this.videoForm.platform,
                        duration: this.videoForm.duration,
                        product: this.videoForm.product,
                        target_audience: this.videoForm.target_audience,
                        goal: this.videoForm.goal,
                        styles: this.videoForm.styles,
                        context: this.videoForm.context
                    });
                    headers['Content-Type'] = 'application/json';
                }

                try {
                    const response = await fetch('/api/ai/generate-video-content', {
                        method: 'POST',
                        headers: headers,
                        body: requestData
                    });

                    const data = await response.json();

                    if (handleFeatureLocked(data)) return;

                    if (data.success) {
                        this.result = data.content;
                        this.lastCaptionId = data.caption_id || null;
                        const imageText = this.videoForm.image_file ? ' dengan analisis visual' : '';
                        this.showNotification(`G�� Konten video berhasil di-generate${imageText}!`, 'success');
                        setTimeout(() => {
                            document.querySelector('.mt-6').scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                    } else {
                        this.error = data.message || 'Terjadi kesalahan saat generate konten video';
                        this.showNotification('G�� ' + this.error, 'error');
                    }
                } catch (error) {
                    this.error = 'Terjadi kesalahan jaringan';
                    this.showNotification('G�� Terjadi kesalahan jaringan', 'error');
                } finally {
                    this.loading = false;
                }
            },

            async generateImageCaption() {
                if (!this.imageForm.file) {
                    alert('Mohon upload foto terlebih dahulu');
                    return;
                }

                this.loading = true;
                this.result = '';
                this.error = '';

                const formData = new FormData();
                formData.append('image', this.imageForm.file);
                formData.append('business_type', this.imageForm.business_type);
                formData.append('product_name', this.imageForm.product_name);

                try {
                    const response = await fetch('/api/ai/generate-image-caption', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Format result untuk ditampilkan
                        let resultText = '=��+n+� IMAGE CAPTION RESULT\n\n';
                        
                        if (data.detected_objects && data.detected_objects.length > 0) {
                            resultText += '=��� Objek Terdeteksi:\n';
                            data.detected_objects.forEach(obj => {
                                resultText += `G�� ${obj}\n`;
                            });
                            resultText += '\n';
                        }

                        if (data.caption_single) {
                            resultText += '=��� CAPTION SINGLE POST:\n';
                            resultText += data.caption_single + '\n\n';
                        }

                        if (data.caption_carousel && data.caption_carousel.length > 0) {
                            resultText += '=��� CAPTION CAROUSEL:\n\n';
                            data.caption_carousel.forEach((slide, index) => {
                                resultText += `Slide ${index + 1}:\n${slide}\n\n`;
                            });
                        }

                        if (data.editing_tips && data.editing_tips.length > 0) {
                            resultText += '=��� TIPS EDITING:\n';
                            data.editing_tips.forEach(tip => {
                                resultText += `G�� ${tip}\n`;
                            });
                        }

                        this.result = resultText;
                        this.lastCaptionId = data.caption_id;
                    } else {
                        throw new Error(data.message || 'Generation failed');
                    }
                } catch (error) {
                    this.error = error.message || 'Terjadi kesalahan saat generate caption';
                } finally {
                    this.loading = false;
                }
            },
            
            async generateCopywriting() {
                // Handle Simple Mode
                if (this.mode === 'simple') {
                    // Validate simple form
                    if (!this.simpleForm.content_type) {
                        alert('Pilih jenis konten dulu ya!');
                        return;
                    }
                    
                    if (!this.simpleForm.subcategory) {
                        alert('Pilih jenis konten spesifik dulu ya!');
                        return;
                    }
                    
                    if (!this.simpleForm.product_name || this.simpleForm.product_name.length < 10) {
                        alert('Ceritakan tentang konten kamu minimal 10 karakter ya!');
                        return;
                    }
                    
                    if (!this.simpleForm.target_market || !this.simpleForm.goal || !this.simpleForm.platform) {
                        alert('Mohon isi semua pertanyaan yang wajib (*)');
                        return;
                    }
                    
                    // Convert Simple Mode to Advanced Mode format
                    this.form.category = this.simpleForm.content_type;
                    this.form.subcategory = this.simpleForm.subcategory;
                    this.form.platform = this.simpleForm.platform;
                    
                    // GOAL-BASED TONE MAPPING (Smart!)
                    const goalToneMap = {
                        'closing': 'persuasive',      // Closing G�� Persuasive
                        'awareness': 'educational',   // Awareness G�� Educational
                        'engagement': 'casual',       // Engagement G�� Casual
                        'viral': 'funny'              // Viral G�� Funny
                    };
                    this.form.tone = goalToneMap[this.simpleForm.goal] || 'casual';
                    
                    this.form.generate_variations = false; // default 5 variasi
                    this.form.auto_hashtag = true;
                    
                    // Build brief from simple inputs
                    let brief = `${this.simpleForm.product_name}\n`;
                    
                    if (this.simpleForm.price) {
                        brief += `Harga: ${this.simpleForm.price}\n`;
                    }
                    brief += `Target: ${this.getTargetLabel(this.simpleForm.target_market)}\n`;
                    brief += `Tujuan: ${this.getGoalLabel(this.simpleForm.goal)}`;
                    
                    // TARGET MARKET-BASED LANGUAGE ADAPTATION
                    const targetLanguageMap = {
                        'remaja': '\n\nGaya Bahasa: Gen Z language (singkat, emoji banyak, slang, relate dengan anak muda)',
                        'ibu_muda': '\n\nGaya Bahasa: Friendly & caring (Bun, Kak, emoji G��n+�=���, bahasa ibu-ibu)',
                        'profesional': '\n\nGaya Bahasa: Professional & efficient (formal, to the point, minimal emoji)',
                        'pelajar': '\n\nGaya Bahasa: Casual & relatable (hemat, promo, diskon, bahasa anak sekolah)',
                        'umum': '\n\nGaya Bahasa: Universal (balance semua, mudah dipahami semua kalangan)'
                    };
                    
                    // Add language style to brief
                    if (targetLanguageMap[this.simpleForm.target_market]) {
                        brief += targetLanguageMap[this.simpleForm.target_market];
                    }
                    
                    this.form.brief = brief;
                } else {
                    // Advanced Mode - Validate form
                    if (!this.form.category) {
                        alert('Pilih kategori terlebih dahulu');
                        return;
                    }
                    
                    if (!this.form.subcategory) {
                        alert('Pilih jenis konten terlebih dahulu');
                        return;
                    }
                    
                    if (!this.form.brief || this.form.brief.length < 10) {
                        alert('Isi brief minimal 10 karakter');
                        return;
                    }
                    
                    if (!this.form.tone) {
                        alert('Pilih tone terlebih dahulu');
                        return;
                    }
                }
                
                this.loading = true;
                this.result = '';
                
                // Start elapsed timer for UX feedback
                this._loadingSeconds = 0;
                this._loadingTimer = setInterval(() => { this._loadingSeconds++; }, 1000);
                
                try {
                    // =��� Add ML data to request
                    const industry = this.getIndustryFromForm();
                    const requestData = {
                        ...this.form,
                        mode: this.mode, // simple or advanced
                        industry: industry, // =��� ML: industry
                        goal: this.form.goal || 'closing', // =��� ML: goal
                    };
                    
                    const response = await fetch('/api/ai/generate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(requestData)
                    });
                    
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        throw new Error('Server returned non-JSON response. Please check server logs.');
                    }
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.result = data.result;
                        this.lastCaptionId = data.caption_id || null;
                        this.keywordInsights = data.keyword_insights || [];
                        
                        // =��� ML: Store ML data
                        if (data.ml_data) {
                            this.mlPreview = data.ml_data;
                        }

                        // Update quota remaining badge if present
                        if (data.quota_remaining !== undefined) {
                            const el = document.getElementById('quota-remaining-badge');
                            if (el) el.textContent = data.quota_remaining + ' tersisa';
                        }
                        
                        // Hide quota error banner on success
                        const errBanner = document.getElementById('quota-error-banner');
                        if (errBanner) errBanner.classList.add('hidden');
                        
                        if (this.isFirstTimeUser) this.isFirstTimeUser = false;
                        this.rated = false;
                        this.selectedRating = 0;
                        this.ratingFeedback = '';
                    } else {
                        const errorMessage = data.message || 'Terjadi kesalahan saat generate konten';
                        
                        // Quota / subscription error G�� show inline banner
                        if (data.quota_error) {
                            let banner = document.getElementById('quota-error-banner');
                            if (!banner) {
                                banner = document.createElement('div');
                                banner.id = 'quota-error-banner';
                                banner.className = 'mb-5 bg-red-50 border border-red-300 rounded-xl p-4 text-sm text-red-800 flex items-start gap-3';
                                const wrapper = document.querySelector('.p-6[x-data]');
                                if (wrapper) wrapper.insertBefore(banner, wrapper.children[1]);
                            }
                            banner.innerHTML = '<span class="text-xl flex-shrink-0">=�ܽ</span><span>' + errorMessage + '</span>';
                            banner.classList.remove('hidden');
                            banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else if (errorMessage.includes('API key') || errorMessage.includes('tidak valid') || errorMessage.includes('expired')) {
                            this.error = 'G��n+� Layanan AI sedang tidak tersedia. Silakan coba beberapa saat lagi atau hubungi admin.';
                        } else {
                            this.error = errorMessage;
                        }
                    }
                } catch (error) {
                    this.error = error.message && !error.message.includes('JSON')
                        ? error.message
                        : 'Tidak dapat terhubung ke server. Silakan coba lagi.';
                } finally {
                    this.loading = false;
                    clearInterval(this._loadingTimer);
                    this._loadingSeconds = 0;
                }
            },
            
            getTargetLabel(value) {
                const labels = {
                    'ibu_muda': 'Ibu-ibu muda yang peduli kualitas dan keamanan produk',
                    'remaja': 'Remaja dan anak muda yang suka trend dan style kekinian',
                    'profesional': 'Pekerja kantoran yang menghargai efisiensi dan kualitas',
                    'pelajar': 'Pelajar dan mahasiswa dengan budget terbatas',
                    'umum': 'Semua kalangan'
                };
                return labels[value] || value;
            },
            
            getGoalLabel(value) {
                const labels = {
                    'closing': 'Fokus closing - biar langsung beli',
                    'awareness': 'Branding - biar orang kenal produk',
                    'engagement': 'Engagement - biar banyak like dan comment',
                    'viral': 'Viral - biar banyak yang share'
                };
                return labels[value] || value;
            },
            
            copyToClipboard(event) {
                // Prevent default if event is passed
                if (event && event.preventDefault) {
                    event.preventDefault();
                }
                
                try {
                    // Clean markdown formatting before copying
                    let textToCopy = this.result || '';
                    
                    // Remove markdown bold (**text** or __text__)
                    textToCopy = textToCopy.replace(/\*\*(.+?)\*\*/g, '$1');
                    textToCopy = textToCopy.replace(/__(.+?)__/g, '$1');
                    
                    // Remove markdown italic (*text* or _text_)
                    textToCopy = textToCopy.replace(/\*(.+?)\*/g, '$1');
                    textToCopy = textToCopy.replace(/_(.+?)_/g, '$1');
                    
                    // Remove markdown headers (## or ###)
                    textToCopy = textToCopy.replace(/^#{1,6}\s+/gm, '');
                    
                    // Modern method
                    copyToClipboard(textToCopy)
                        .then(() => {
                            this.copied = true;
                            setTimeout(() => this.copied = false, 2000);
                        })
                        .catch(err => {
                            this.fallbackCopy(textToCopy);
                        });
                } catch (err) {
                    this.fallbackCopy(this.result);
                }
            },
            
            fallbackCopy(text = null) {
                try {
                    const textToCopy = text || this.result;
                    // Create temporary textarea
                    const textarea = document.createElement('textarea');
                    textarea.value = textToCopy;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.select();
                    
                    const successful = document.execCommand('copy');
                    document.body.removeChild(textarea);
                    
                    if (successful) {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    } else {
                        alert('Gagal copy. Silakan copy manual.');
                    }
                } catch (err) {
                    alert('Gagal copy. Silakan copy manual dengan Ctrl+C');
                }
            },

            sendResultToWhatsApp() {
                let text = (this.result || '').replace(/\*\*(.+?)\*\*/g, '*$1*')
                    .replace(/^#{1,6}\s+/gm, '');
                if (text.length > 4000) text = text.substring(0, 4000) + '...';
                window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
            },

            parseResultToCaptions(text) {
                if (!text) return [];
                let clean = text.replace(/\*\*(.+?)\*\*/g, '$1').replace(/__(.+?)__/g, '$1')
                                .replace(/\*(.+?)\*/g, '$1').replace(/_(.+?)_/g, '$1')
                                .replace(/^#{1,6}\s+/gm, '');
                let parts = clean.split(/\n\s*\d+[\.\)]\s+/).filter(s => s.trim().length > 20);
                if (parts.length <= 1) {
                    parts = clean.split(/\n\s*[-=]{3,}\s*\n/).filter(s => s.trim().length > 20);
                }
                if (parts.length <= 1) {
                    parts = clean.split(/\n\n\n+/).filter(s => s.trim().length > 20);
                }
                return parts.length > 0 ? parts.map(s => s.trim()) : [clean.trim()];
            },

            copySingleCaption(idx) {
                const text = this.parsedCaptions[idx] || '';
                copyToClipboard(text).then(() => {
                    this.copiedIdx = idx;
                    setTimeout(() => { this.copiedIdx = null; }, 2000);
                });
            },

            shareToWhatsApp(idx) {
                const text = encodeURIComponent(this.parsedCaptions[idx] || '');
                window.open('https://wa.me/?text=' + text, '_blank');
            },

            async shareToExplore() {
                if (!this.lastCaptionId) return;
                try {
                    const resp = await fetch('/caption/' + this.lastCaptionId + '/toggle-public', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                    const data = await resp.json();
                    if (data.success) {
                        this.showNotification(data.is_public ? '?? Caption dipublikasikan ke Explore!' : 'Caption dihapus dari Explore.', 'success');
                    }
                } catch (e) {
                    this.showNotification('Gagal share ke Explore', 'error');
                }
            },

            downloadCaptionImage(idx) {
                const caption = this.parsedCaptions[idx] || '';
                const canvas = document.createElement('canvas');
                canvas.width = 1080; canvas.height = 1080;
                const ctx = canvas.getContext('2d');
                const grad = ctx.createLinearGradient(0, 0, 1080, 1080);
                grad.addColorStop(0, '#667eea'); grad.addColorStop(1, '#764ba2');
                ctx.fillStyle = grad; ctx.fillRect(0, 0, 1080, 1080);
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 36px -apple-system, BlinkMacSystemFont, sans-serif';
                ctx.textAlign = 'center';
                const words = caption.split(' ');
                let lines = [], line = '', maxWidth = 900;
                words.forEach(w => {
                    const test = line + w + ' ';
                    if (ctx.measureText(test).width > maxWidth && line) { lines.push(line.trim()); line = w + ' '; }
                    else { line = test; }
                });
                if (line.trim()) lines.push(line.trim());
                if (lines.length > 14) lines = lines.slice(0, 14);
                const lineHeight = 48, startY = (1080 - lines.length * lineHeight) / 2;
                lines.forEach((l, i) => { ctx.fillText(l, 540, startY + i * lineHeight); });
                ctx.font = '20px sans-serif'; ctx.fillStyle = 'rgba(255,255,255,0.5)';
                ctx.fillText('pintar-menulis.com', 540, 1050);
                const link = document.createElement('a');
                link.download = 'caption-' + (idx + 1) + '.png';
                link.href = canvas.toDataURL('image/png'); link.click();
            },

            reset() {
                this.result = '';
                this.form.brief = '';
                this.saved = false;
                this.rated = false;
                this.selectedRating = 0;
                this.ratingFeedback = '';
                this.lastCaptionId = null;
            },
            
            async submitRating() {
                if (this.selectedRating === 0) {
                    alert('Pilih rating terlebih dahulu');
                    return;
                }
                
                if (!this.lastCaptionId) {
                    alert('Caption ID tidak ditemukan. Generate ulang caption.');
                    return;
                }
                
                this.submittingRating = true;
                
                try {
                    const response = await fetch(`/api/caption/${this.lastCaptionId}/rate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            rating: this.selectedRating,
                            feedback: this.ratingFeedback || null
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.rated = true;
                        
                        setTimeout(() => {
                            // Optional: Show success message
                        }, 100);
                    } else {
                        alert('Failed to submit rating: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    alert('Failed to submit rating');
                } finally {
                    this.submittingRating = false;
                }
            },
            
            async saveForAnalytics() {
                try {
                    const response = await fetch('/analytics', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            caption_text: this.result,
                            category: this.form.category,
                            subcategory: this.form.subcategory,
                            platform: this.form.platform,
                            tone: this.form.tone
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.saved = true;
                        setTimeout(() => {
                            alert('G�� Caption saved for analytics tracking!\n\nYou can now track its performance in Analytics page.');
                        }, 100);
                    } else {
                        alert('Failed to save: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    alert('Failed to save caption for analytics');
                }
            },
            
            async loadBrandVoices() {
                try {
                    const response = await fetch('/brand-voices');
                    const data = await response.json();
                    if (data.success) {
                        this.brandVoices = data.brand_voices;
                    }
                } catch (error) {
                }
            },
            
            loadBrandVoice(voice) {
                // Load into advanced mode form
                this.mode = 'advanced';
                this.form.category = voice.industry ? 'industry_presets' : 'quick_templates';
                this.updateSubcategories();
                this.form.subcategory = voice.industry || 'caption_instagram';
                this.form.tone = voice.tone || 'casual';
                this.form.platform = voice.platform || 'instagram';
                this.form.keywords = voice.keywords || '';
                this.form.local_language = voice.local_language || '';
                
                if (voice.brand_description) {
                    this.form.brief = voice.brand_description;
                }
                
                this.showNotification('G�� Brand Voice dimuat! Tinggal isi brief dan generate.');
            },
            
            async saveBrandVoice() {
                if (!this.brandVoiceForm.name) {
                    alert('Nama brand voice wajib diisi');
                    return;
                }
                
                this.savingBrandVoice = true;
                
                try {
                    const response = await fetch('/brand-voices', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            name: this.brandVoiceForm.name,
                            brand_description: this.brandVoiceForm.brand_description,
                            is_default: this.brandVoiceForm.is_default,
                            industry: this.form.category === 'industry_presets' ? this.form.subcategory : null,
                            target_market: this.simpleForm.target_market || null,
                            tone: this.form.tone,
                            platform: this.form.platform,
                            keywords: this.form.keywords,
                            local_language: this.form.local_language
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showNotification('G�� Brand Voice berhasil disimpan!');
                        this.showSaveBrandVoiceModal = false;
                        this.brandVoiceForm = { name: '', brand_description: '', is_default: false };
                        this.loadBrandVoices(); // Reload list
                    } else {
                        alert('Failed: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    alert('Failed to save brand voice');
                } finally {
                    this.savingBrandVoice = false;
                }
            },
            
            // =��� ML FEATURES (Simplified - no upgrade modal)
            mlStatus: null,
            mlPreview: {},
            showMLPreview: false,
            mlLoading: false,
            weeklyTrends: {},
            refreshing: false,
            
            // Initialize ML features
            async initML() {
                try {
                    const response = await fetch('/api/ml/status', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        this.mlStatus = data;
                    }
                } catch (error) {
                }
            },
            
            // Toggle ML preview
            async toggleMLPreview() {
                if (this.showMLPreview) {
                    this.showMLPreview = false;
                    return;
                }

                // Show modal immediately with loading state
                this.mlLoading = true;
                this.showMLPreview = true;

                const industry = this.getIndustryFromForm();
                const platform = this.form?.platform || 'instagram';

                try {
                    const response = await fetch(`/api/ml/preview?industry=${industry}&platform=${platform}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.mlPreview = data;
                    }
                } catch (error) {
                    // silent fail
                } finally {
                    this.mlLoading = false;
                }
            },
            
            // Get industry from form
            getIndustryFromForm() {
                if (this.mode === 'simple') {
                    const businessTypeMap = {
                        'fashion': 'fashion',
                        'food': 'food',
                        'beauty': 'beauty',
                        'printing': 'printing',
                        'photography': 'photography',
                        'catering': 'catering',
                        'tiktok_shop': 'tiktok_shop',
                        'shopee_affiliate': 'shopee_affiliate',
                        'home_decor': 'home_decor',
                        'handmade': 'handmade',
                        'digital_service': 'digital_service',
                        'automotive': 'automotive',
                    };
                    return businessTypeMap[this.simpleForm?.business_type] || 'fashion';
                } else {
                    const industryMap = {
                        'fashion_pakaian': 'fashion',
                        'makanan_minuman': 'food',
                        'kecantikan_skincare': 'beauty',
                        'jasa_printing': 'printing',
                        'jasa_fotografi': 'photography',
                        'catering': 'catering',
                        'tiktok_shop': 'tiktok_shop',
                        'affiliate_shopee': 'shopee_affiliate',
                        'dekorasi_rumah': 'home_decor',
                        'kerajinan_tangan': 'handmade',
                        'jasa_digital': 'digital_service',
                        'otomotif': 'automotive',
                    };
                    return industryMap[this.form?.category] || 'general';
                }
            },

            // =��� ANALYSIS METHODS
            async analyzeCaption() {
                if (!this.result) {
                    alert('Generate caption terlebih dahulu');
                    return;
                }

                this.showAnalysis = true; // Show analysis widget
                this.analysisLoading = true;
                this.analysisError = null;
                this.analysisTab = 'quality';

                try {
                    // Truncate caption if too long (max 2000 chars for analysis)
                    const captionToAnalyze = this.result.length > 2000 ? this.result.substring(0, 2000) : this.result;

                    // Map platform to valid analysis platform
                    const platformMap = {
                        'shopee': 'instagram',
                        'tokopedia': 'instagram',
                        'bukalapak': 'instagram',
                        'lazada': 'instagram',
                        'blibli': 'instagram',
                        'tiktok_shop': 'tiktok',
                        'olx': 'facebook',
                        'facebook_marketplace': 'facebook',
                        'carousell': 'instagram',
                        'amazon': 'instagram',
                        'ebay': 'instagram',
                        'etsy': 'instagram',
                        'alibaba': 'instagram',
                        'aliexpress': 'instagram',
                        'shopify': 'instagram',
                        'walmart': 'instagram',
                        'youtube': 'instagram',
                        'youtube_shorts': 'tiktok',
                        'twitter': 'x',
                        'linkedin': 'linkedin'
                    };
                    
                    const rawPlatform = this.form.platform || this.simpleForm.platform || 'instagram';
                    const validPlatform = platformMap[rawPlatform] || rawPlatform;
                    
                    // Ensure platform is one of: instagram, tiktok, facebook, x, linkedin
                    const finalPlatform = ['instagram', 'tiktok', 'facebook', 'x', 'linkedin'].includes(validPlatform) 
                        ? validPlatform 
                        : 'instagram';

                    // Get quality score
                    const qualityRes = await fetch('/api/analysis/score-caption', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            caption: captionToAnalyze,
                            platform: finalPlatform,
                            industry: this.getIndustryFromForm()
                        })
                    });

                    if (!qualityRes.ok) {
                        const text = await qualityRes.text();
                        throw new Error(`Quality analysis failed: ${qualityRes.status}`);
                    }

                    // Get sentiment
                    const sentimentRes = await fetch('/api/analysis/sentiment', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ caption: captionToAnalyze })
                    });

                    if (!sentimentRes.ok) {
                        const text = await sentimentRes.text();
                        throw new Error(`Sentiment analysis failed: ${sentimentRes.status}`);
                    }

                    // Get recommendations
                    const recsRes = await fetch('/api/analysis/recommendations', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            caption: captionToAnalyze,
                            platform: finalPlatform,
                            target_audience: this.simpleForm.target_market || 'umum'
                        })
                    });

                    if (!recsRes.ok) {
                        const text = await recsRes.text();
                        throw new Error(`Recommendations failed: ${recsRes.status}`);
                    }

                    const quality = await qualityRes.json();
                    const sentiment = await sentimentRes.json();
                    const recommendations = await recsRes.json();

                    // Check if all responses were successful
                    if (!quality.success || !sentiment.success || !recommendations.success) {
                        const errors = [];
                        if (!quality.success) errors.push('Quality: ' + (quality.error || 'Invalid JSON response'));
                        if (!sentiment.success) errors.push('Sentiment: ' + (sentiment.error || 'Invalid JSON response'));
                        if (!recommendations.success) errors.push('Recommendations: ' + (recommendations.error || 'Invalid JSON response'));
                        
                        // User-friendly error message
                        throw new Error('G��n+� AI sedang tidak stabil. Coba lagi dalam beberapa saat atau gunakan caption yang lebih pendek.');
                    }

                    this.analysisResult = {
                        quality: quality.data,
                        sentiment: sentiment.data,
                        recommendations: recommendations.data
                    };

                    this.showAnalysis = true;
                } catch (error) {
                    this.analysisError = error.message || 'Gagal menganalisis caption. Silakan coba lagi.';
                } finally {
                    this.analysisLoading = false;
                }
            },

            useImprovedCaption() {
                if (this.analysisResult?.quality?.improved_caption) {
                    this.result = this.analysisResult.quality.improved_caption;
                    this.showNotification('G�� Caption berhasil diperbarui!');
                }
            },

            addHashtag(hashtag) {
                if (!this.result.includes(hashtag)) {
                    this.result += ' ' + hashtag;
                    this.copyToClipboard();
                }
            },

            addEmoji(emoji) {
                this.result += ' ' + emoji;
            },

            // Remove duplicate copyToClipboard - already defined above
            
            // Clean markdown formatting for display
            cleanMarkdown(text) {
                if (!text) return '';
                
                let cleaned = text;
                
                // Remove markdown bold (**text** or __text__)
                cleaned = cleaned.replace(/\*\*(.+?)\*\*/g, '$1');
                cleaned = cleaned.replace(/__(.+?)__/g, '$1');
                
                // Remove markdown italic (*text* or _text_)  
                cleaned = cleaned.replace(/\*(.+?)\*/g, '$1');
                cleaned = cleaned.replace(/_(.+?)_/g, '$1');
                
                // Remove markdown headers (## or ###)
                cleaned = cleaned.replace(/^#{1,6}\s+/gm, '');
                
                return cleaned;
            },

            // =��� ML FEATURES - Additional Methods

            // Refresh ML suggestions manually
            async refreshSuggestions() {
                this.refreshing = true;
                try {
                    const industry = this.getIndustryFromForm();
                    const platform = this.form?.platform || 'instagram';

                    const response = await fetch('/api/ml/refresh', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ industry, platform })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.mlPreview = data;

                        // Also refresh weekly trends
                        const trendsRes = await fetch(`/api/ml/weekly-trends?industry=${industry}`, {
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        if (trendsRes.ok) {
                            this.weeklyTrends = await trendsRes.json();
                        }
                    }
                } catch (error) {
                    // silent fail
                } finally {
                    this.refreshing = false;
                }
            },

            // Get freshness indicator
            getFreshnessIndicator() {
                if (!this.mlPreview?.generated_at) return '';
                
                const generatedAt = new Date(this.mlPreview.generated_at);
                const now = new Date();
                const hoursAgo = Math.floor((now - generatedAt) / (1000 * 60 * 60));
                
                if (hoursAgo < 1) return '=��� Baru saja diperbarui';
                if (hoursAgo < 6) return '=��� Diperbarui ' + hoursAgo + ' jam lalu';
                if (hoursAgo < 24) return '=��� Diperbarui hari ini';
                if (hoursAgo < 48) return '=��� Diperbarui kemarin';
                return '=��� Perlu diperbarui';
            },

            // Check if data is fresh (less than 24 hours)
            isDataFresh() {
                if (!this.mlPreview?.generated_at) return false;
                
                const generatedAt = new Date(this.mlPreview.generated_at);
                const now = new Date();
                const hoursAgo = Math.floor((now - generatedAt) / (1000 * 60 * 60));
                
                return hoursAgo < 24;
            },

            // =��� PERFORMANCE PREDICTOR FUNCTIONS
            async predictCaptionPerformance() {
                if (!this.predictorForm.caption.trim()) {
                    alert('Masukkan caption yang mau dianalisis!');
                    return;
                }

                this.predictorLoading = true;
                this.predictorError = null;
                this.predictionResults = null;

                try {
                    // Try authenticated endpoint first, fallback to demo if needed
                    let endpoint = '/api/ai/predict-performance';
                    let headers = {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    };

                    let response = await fetch(endpoint, {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify(this.predictorForm)
                    });

                    // If authentication fails, try demo endpoint (GET)
                    if (response.status === 401 || response.status === 419 || response.status === 500) {
                        const encodedCaption = encodeURIComponent(this.predictorForm.caption);
                        endpoint = `/demo-predict/${encodedCaption}`;
                        response = await fetch(endpoint, { method: 'GET' });
                    }

                    const data = await response.json();

                    if (handleFeatureLocked(data)) return;

                    if (data.success) {
                        this.predictionResults = data;
                    } else {
                        throw new Error(data.message || 'Gagal memprediksi performa caption');
                    }

                } catch (error) {
                    this.predictorError = error.message || 'Terjadi kesalahan saat memprediksi performa';
                    this.showNotification('G�� ' + this.predictorError, 'error');
                } finally {
                    this.predictorLoading = false;
                }
            },

            async generateMoreVariants() {
                if (!this.predictorForm.caption.trim()) {
                    alert('Caption tidak ditemukan!');
                    return;
                }

                this.variantsLoading = true;

                try {
                    // Try authenticated endpoint first, fallback to demo if needed
                    let endpoint = '/api/ai/generate-ab-variants';
                    let headers = {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    };

                    let response = await fetch(endpoint, {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify({
                            caption: this.predictorForm.caption,
                            platform: this.predictorForm.platform,
                            industry: this.predictorForm.industry,
                            variant_count: 3,
                            focus_area: 'overall'
                        })
                    });

                    // If authentication fails, try demo endpoint (GET)
                    if (response.status === 401 || response.status === 419 || response.status === 500) {
                        const encodedCaption = encodeURIComponent(this.predictorForm.caption);
                        endpoint = `/demo-variants/${encodedCaption}`;
                        response = await fetch(endpoint, { method: 'GET' });
                    }

                    const data = await response.json();

                    if (handleFeatureLocked(data)) return;

                    if (data.success) {
                        this.showVariantsModal(data);
                    } else {
                        throw new Error(data.message || 'Gagal generate variants');
                    }

                } catch (error) {
                    this.showNotification('G�� ' + (error.message || 'Terjadi kesalahan saat generate variants'), 'error');
                } finally {
                    this.variantsLoading = false;
                }
            },

            showVariantsModal(data) {
                let modalContent = `
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-semibold">=��� A/B Testing Variants</h3>
                                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-500 hover:text-gray-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="space-y-4">
                `;

                // Add original
                modalContent += `
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold mb-2">=��� Original (A)</h4>
                        <div class="bg-gray-50 p-3 rounded text-sm">${data.original_caption}</div>
                    </div>
                `;

                // Add variants
                data.variants.forEach((variant, index) => {
                    modalContent += `
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold">=��� Variant ${String.fromCharCode(66 + index)} (${variant.focus})</h4>
                                <button onclick="copyToClipboard('${variant.caption.replace(/'/g, "\\'")}').then(()=>{this.textContent='G�� Copied!';setTimeout(()=>{this.textContent='Copy'},2000)})" 
                                        class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                    Copy
                                </button>
                            </div>
                            <div class="bg-blue-50 p-3 rounded text-sm mb-2">${variant.caption}</div>
                            <div class="text-xs text-gray-600">Hypothesis: ${variant.test_hypothesis}</div>
                        </div>
                    `;
                });

                modalContent += `
                            </div>
                            <div class="mt-6 bg-gray-50 p-4 rounded">
                                <h4 class="font-semibold mb-2">=��� Test Setup Recommendations</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div><strong>Duration:</strong> ${data.test_setup.recommended_duration}</div>
                                    <div><strong>Sample Size:</strong> ${data.test_setup.minimum_sample_size}</div>
                                    <div><strong>Confidence:</strong> ${data.test_setup.statistical_significance}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                document.body.insertAdjacentHTML('beforeend', modalContent);
            }

        }
    }
)
</script>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/partials/ai-generator/_script.blade.php ENDPATH**/ ?>