<script>
    // Clipboard helper — works on HTTP (non-secure) and HTTPS
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

    // Handle feature_locked response dari middleware ΓÇö tampilkan notif upgrade
    function handleFeatureLocked(data, showNotificationFn) {
        if (data && data.feature_locked) {
            const msg = (data.message || 'Fitur ini tidak tersedia di paket kamu.')
                + ' <a href="{{ route("pricing") }}" class="underline font-semibold">Upgrade sekarang ΓåÆ</a>';
            // Tampilkan sebagai toast dengan link
            const toast = document.createElement('div');
            toast.innerHTML = msg;
            toast.className = 'fixed bottom-6 right-6 z-50 bg-yellow-500 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium max-w-xs cursor-pointer';
            toast.onclick = () => window.location.href = '{{ route("pricing") }}';
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
            quotaRemaining: {{ $quotaRemaining ?? 0 }},
            creditCosts: @json($creditCosts ?? []),
            selectedRating: 0,
            ratingFeedback: '',
            rated: false,
            submittingRating: false,
            lastCaptionId: null,
            keywordInsights: [], // ≡ƒöì Keyword insights data
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
            analysisForm: {
                file: null,
                preview: null,
                options: ['objects', 'colors', 'composition', 'mood'], // default selected
                context: ''
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

            // ≡ƒôê Performance Predictor state
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

            // ≡ƒôÜ Template Library state
            templateFilters: {
                category: '',
                platform: '',
                tone: '',
                search: ''
            },
            allTemplates: [],
            filteredTemplates: [],
            selectedTemplate: null,
            favoriteTemplates: [],

            // ≡ƒÄ» Multi-Platform Optimizer state
            multiPlatformForm: {
                content: '',
                business_type: 'general',
                target_audience: 'general',
                goal: 'engagement',
                platforms: ['instagram', 'tiktok'] // default selection
            },
            multiPlatformLoading: false,
            multiPlatformResults: null,

            // ΓÖ╗∩╕Å Content Repurposing state
            repurposeForm: {
                originalContent: '',
                originalType: '',
                industry: 'general',
                selectedFormats: [],
                includeHashtags: true,
                includeCTA: true,
                optimizeLength: true,
                generateVariations: false
            },
            repurposeLoading: false,
            repurposeResults: [],

            // ≡ƒÄ» Google Ads state
            adsForm: {
                business_name: '',
                product_service: '',
                target_audience: '',
                location: '',
                daily_budget: '',
                campaign_goal: '',
                campaign_type: '',
                landing_page_url: '',
                keywords_hint: '',
                usp: '',
                language: 'id'
            },
            adsLoading: false,
            adsResult: null,
            adsTab: 'copy',
            adsCopied: false,
            adsTabs: [
                {id: 'copy',       label: '≡ƒô¥ Ad Copy'},
                {id: 'keywords',   label: '≡ƒöæ Keywords'},
                {id: 'extensions', label: '≡ƒöù Sitelinks & Extensions'},
                {id: 'targeting',  label: '≡ƒÄ» Targeting'},
                {id: 'budget',     label: '≡ƒÆ░ Budget & Estimasi'},
                {id: 'review',     label: '≡ƒôè Review & Analysis'},
            ],

            // ≡ƒöù Magic Promo Link state
            promoForm: {
                product_name: '',
                product_desc: '',
                price: '',
                target_audience: '',
                promo_detail: '',
                wa_number: '',
                language: 'id'
            },
            promoLoading: false,
            promoResult: null,
            promoError: null,
            promoCopied: {},

            // ≡ƒÆ¼ AI Product Explainer state
            explainerForm: {
                product_name: '',
                product_desc: '',
                price: '',
                features: '',
                target_buyer: '',
                seller_name: '',
                wa_number: '',
            },
            explainerLoading: false,
            explainerResult: null,
            explainerError: null,
            explainerCopied: {},
            explainerLinkCopied: {},

            // ≡ƒöì SEO Metadata state
            seoForm: { product_name: '', product_desc: '', category: '', keywords: '', url: '' },
            seoLoading: false,
            seoResult: null,
            seoError: null,
            seoCopied: {},

            // ΓÜû∩╕Å Smart Comparison state
            compForm: { product_a_name: '', product_a_desc: '', product_a_price: '', product_b_name: '', product_b_desc: '', product_b_price: '', buyer_persona: '' },
            compLoading: false,
            compResult: null,
            compError: null,
            compCopied: false,

            // Γ¥ô FAQ Generator state
            faqForm: { product_name: '', product_desc: '', category: '', price: '' },
            faqLoading: false,
            faqResult: null,
            faqError: null,
            faqCopied: {},
            faqAllCopied: false,
            faqSchemaCopied: false,

            // ≡ƒÄ¼ Reels Hook state
            reelsForm: { product_name: '', product_desc: '', target_audience: '', video_goal: '', platform: 'reels', tone: 'energetic' },
            reelsLoading: false,
            reelsResult: null,
            reelsError: null,
            reelsCopied: {},
            reelsScriptCopied: {},
            reelsSelectedHook: null,
            reelsBioCopied: false,

            // ≡ƒÅà Quality Badge state
            badgeForm: { product_name: '', product_desc: '', asset_type: '', code_or_doc: '' },
            badgeLoading: false,
            badgeResult: null,
            badgeError: null,
            badgeCopied: false,

            // ≡ƒÅ╖∩╕Å Discount Campaign state
            discForm: { promo_name: '', product_name: '', product_desc: '', original_price: '', discount_price: '', discount_pct: '', duration: '', platform: '', wa_number: '' },
            discLoading: false,
            discResult: null,
            discError: null,
            discCopied: {},
            discWACopied: false,

            // ≡ƒôê Trend Tags state
            tagsForm: { product_name: '', product_desc: '', category: '', current_tags: '', platform: 'marketplace' },
            tagsLoading: false,
            tagsResult: null,
            tagsError: null,
            tagsCopied: false,

            // ≡ƒº▓ Lead Magnet state
            magnetForm: { product_name: '', product_desc: '', product_type: '', price: '', target_audience: '', goal: 'email', wa_number: '' },
            magnetLoading: false,
            magnetResult: null,
            magnetError: null,
            magnetCopied: false,
            magnetWACopied: false,

            // ≡ƒôè Financial Analysis state
            financialForm: { analysis_type: 'stock_chart', context: '' },
            financialImages: [],
            financialDocs: [],
            financialLoading: false,
            financialResult: null,
            financialError: null,
            financialCopied: false,

            // ΓöÇΓöÇ Ebook Analysis ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            ebookForm: { analysis_type: 'full', context: '' },
            ebookDocs: [],
            ebookLoading: false,
            ebookResult: null,
            ebookError: null,
            ebookCopied: false,

            // ΓöÇΓöÇ Reader Trend Analysis ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            readerTrendForm: { genre: '', platform: '' , context: '' },
            readerTrendLoading: false,
            readerTrendResult: null,
            readerTrendError: null,
            readerTrendCopied: false,
            repurposeOptions: [
                {value: 'instagram_story', label: '≡ƒô▒ Instagram Story', description: 'Short, visual, engaging'},
                {value: 'tiktok_script', label: '≡ƒÄ╡ TikTok Video Script', description: 'Hook + content + CTA'},
                {value: 'blog_outline', label: '≡ƒô¥ Blog Post Outline', description: 'Structured long-form'},
                {value: 'email_copy', label: '≡ƒôº Email Marketing', description: 'Subject + body + CTA'},
                {value: 'product_description', label: '≡ƒ¢ì∩╕Å Product Description', description: 'Features + benefits'},
                {value: 'linkedin_post', label: '≡ƒÆ╝ LinkedIn Post', description: 'Professional tone'},
                {value: 'facebook_post', label: '≡ƒæÑ Facebook Post', description: 'Community focused'},
                {value: 'youtube_description', label: '≡ƒô║ YouTube Description', description: 'SEO optimized'},
                {value: 'twitter_thread', label: '≡ƒÉª Twitter Thread', description: 'Multi-tweet story'},
                {value: 'whatsapp_broadcast', label: '≡ƒÆ¼ WhatsApp Broadcast', description: 'Direct & personal'},
                {value: 'carousel_slides', label: '≡ƒôè Carousel Slides', description: 'Multi-slide content'},
                {value: 'podcast_script', label: '≡ƒÄÖ∩╕Å Podcast Script', description: 'Audio-friendly format'}
            ],

            // ≡ƒöö Trend Alert state
            trendCategory: 'daily',
            selectedTrend: null,
            trendContentTypes: [],
            trendBusinessContext: '',
            trendsLoading: false,
            trendLoading: false,
            trendResults: [],
            dailyTrends: [
                {
                    id: 1,
                    title: 'Viral Dance Challenge #GerakanSehat',
                    description: 'Challenge dance untuk promosi gaya hidup sehat yang viral di TikTok dan Instagram',
                    category: 'Health',
                    popularity: '2.5M',
                    timeAgo: '2 jam lalu',
                    icon: '≡ƒÆâ',
                    hashtags: ['#GerakanSehat', '#ViralDance', '#HealthyLifestyle', '#Indonesia']
                },
                {
                    id: 2,
                    title: 'Trending Makanan Viral "Es Kepal Milo"',
                    description: 'Minuman es kepal dengan topping milo yang sedang viral di media sosial',
                    category: 'Food',
                    popularity: '1.8M',
                    timeAgo: '4 jam lalu',
                    icon: '≡ƒºè',
                    hashtags: ['#EsKepalMilo', '#MinumanViral', '#Kuliner', '#UMKM']
                },
                {
                    id: 3,
                    title: 'Gaya Fashion "Old Money Aesthetic"',
                    description: 'Trend fashion dengan gaya klasik dan elegan yang sedang populer',
                    category: 'Fashion',
                    popularity: '3.2M',
                    timeAgo: '6 jam lalu',
                    icon: '≡ƒæù',
                    hashtags: ['#OldMoney', '#Fashion', '#Aesthetic', '#Style']
                },
                {
                    id: 4,
                    title: 'Teknologi AI untuk UMKM',
                    description: 'Pembahasan penggunaan AI untuk membantu bisnis UMKM berkembang',
                    category: 'Technology',
                    popularity: '950K',
                    timeAgo: '8 jam lalu',
                    icon: '≡ƒñû',
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
                    icon: 'Γ£¿'
                },
                {
                    id: 2,
                    title: 'Behind The Scenes Process',
                    description: 'Tunjukkan proses pembuatan produk atau layanan dari belakang layar',
                    type: 'Educational',
                    engagement: '78%',
                    platform: 'All Platforms',
                    icon: '≡ƒÄ¼'
                },
                {
                    id: 3,
                    title: 'Customer Testimonial Stories',
                    description: 'Cerita nyata pelanggan dengan hasil yang memuaskan',
                    type: 'Social Proof',
                    engagement: '92%',
                    platform: 'Instagram/Facebook',
                    icon: '≡ƒÆ¼'
                },
                {
                    id: 4,
                    title: 'Quick Tips & Hacks',
                    description: 'Tips cepat dan mudah yang bisa langsung dipraktikkan',
                    type: 'Educational',
                    engagement: '88%',
                    platform: 'TikTok/Instagram',
                    icon: '≡ƒÆí'
                }
            ],
            seasonalEvents: [
                {
                    id: 1,
                    title: `Ramadan ${new Date().getFullYear()}`,
                    date: `11 Maret - 9 April ${new Date().getFullYear()}`,
                    daysLeft: 45,
                    description: 'Bulan suci Ramadan dengan berbagai peluang konten dan promo',
                    icon: '≡ƒîÖ',
                    contentIdeas: ['Sahur Ideas', 'Iftar Menu', 'Spiritual Content', 'Charity Campaign']
                },
                {
                    id: 2,
                    title: 'Lebaran/Eid Mubarak',
                    date: `10-11 April ${new Date().getFullYear()}`,
                    daysLeft: 75,
                    description: 'Hari Raya Idul Fitri dengan tradisi mudik dan berkumpul keluarga',
                    icon: '≡ƒÄë',
                    contentIdeas: ['Lebaran Outfit', 'Mudik Tips', 'Family Gathering', 'THR Campaign']
                },
                {
                    id: 3,
                    title: 'Back to School',
                    date: `Juli ${new Date().getFullYear()}`,
                    daysLeft: 120,
                    description: 'Musim kembali ke sekolah dengan kebutuhan perlengkapan baru',
                    icon: '≡ƒÄÆ',
                    contentIdeas: ['School Supplies', 'Study Tips', 'Uniform Fashion', 'Parent Guide']
                },
                {
                    id: 4,
                    title: 'Indonesian Independence Day',
                    date: `17 Agustus ${new Date().getFullYear()}`,
                    daysLeft: 150,
                    description: 'Hari Kemerdekaan Indonesia dengan semangat nasionalisme',
                    icon: '≡ƒç«≡ƒç⌐',
                    contentIdeas: ['Patriotic Content', 'Local Pride', 'Indonesian Products', 'Unity Campaign']
                }
            ],
            nationalDays: [
                {
                    id: 1,
                    title: 'Hari Kartini',
                    date: `21 April ${new Date().getFullYear()}`,
                    description: 'Memperingati perjuangan R.A. Kartini untuk emansipasi wanita',
                    icon: '≡ƒæ⌐',
                    category: 'Women',
                    hashtags: ['#HariKartini', '#WomenEmpowerment', '#Emansipasi', '#PerempuanIndonesia']
                },
                {
                    id: 2,
                    title: 'Hari Pendidikan Nasional',
                    date: `2 Mei ${new Date().getFullYear()}`,
                    description: 'Memperingati hari lahir Ki Hajar Dewantara, Bapak Pendidikan Indonesia',
                    icon: '≡ƒôÜ',
                    category: 'Education',
                    hashtags: ['#HardikNas', '#Pendidikan', '#KiHajarDewantara', '#BelajarSepanjangHayat']
                },
                {
                    id: 3,
                    title: 'Hari Kebangkitan Nasional',
                    date: `20 Mei ${new Date().getFullYear()}`,
                    description: 'Memperingati kebangkitan semangat kebangsaan Indonesia',
                    icon: '≡ƒîà',
                    category: 'National',
                    hashtags: ['#HariKebangkitanNasional', '#SemangitKebangsaan', '#Indonesia', '#Patriotisme']
                },
                {
                    id: 4,
                    title: 'Hari Lingkungan Hidup Sedunia',
                    date: `5 Juni ${new Date().getFullYear()}`,
                    description: 'Kampanye global untuk kesadaran dan aksi lingkungan hidup',
                    icon: '≡ƒîì',
                    category: 'Environment',
                    hashtags: ['#WorldEnvironmentDay', '#LingkunganHidup', '#GoGreen', '#SustainableLiving']
                }
            ],

            // ≡ƒöö Notification toast state
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
                // Load templates for Template Library
                await this.loadTemplates();
                // Load favorite templates from localStorage
                this.loadFavoriteTemplates();
                // Load dynamic dates
                await this.loadDynamicDates();

                // Pre-fill from onboarding data
                const ob = @json($onboarding ?? []);
                if (ob.primary_platform) {
                    this.form.platform = ob.primary_platform;
                    this.simpleForm.platform = ob.primary_platform;
                }
                if (ob.content_goal) {
                    this.simpleForm.goal = ob.content_goal;
                }

                // Watch result changes → parse into individual captions
                this.$watch('result', (val) => {
                    this.parsedCaptions = this.parseResultToCaptions(val);
                    this.copiedIdx = null;
                });
            },

            // ≡ƒôà Load dynamic dates from API
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
                    {value: 'caption_instagram', label: '≡ƒô╕ Caption Instagram'},
                    {value: 'caption_facebook', label: '≡ƒæÑ Caption Facebook'},
                    {value: 'caption_tiktok', label: '≡ƒÄ╡ Caption TikTok'},
                    {value: 'caption_youtube', label: '≡ƒô║ Caption YouTube'},
                    {value: 'caption_linkedin', label: '≡ƒÆ╝ Caption LinkedIn'},
                    {value: 'hook_opening', label: '≡ƒÄú Hook Pembuka (3 detik)'},
                    {value: 'hook_video', label: '≡ƒÄ¼ Hook Video Ads'},
                    {value: 'quotes_motivasi', label: '≡ƒÆ¬ Quotes Motivasi'},
                    {value: 'quotes_bisnis', label: '≡ƒÆ╝ Quotes Bisnis'},
                    {value: 'humor_content', label: '≡ƒÿé Konten Humor'},
                    {value: 'viral_content', label: '≡ƒöÑ Konten Viral'},
                    {value: 'storytelling_short', label: '≡ƒôû Storytelling Pendek'},
                    {value: 'cta_powerful', label: 'ΓÜí Call To Action Kuat'},
                    {value: 'headline_catchy', label: 'Γ£¿ Headline Menarik'}
                ],
                viral_clickbait: [
                    {value: 'clickbait_title', label: '≡ƒÄ» Clickbait Title (Honest)'},
                    {value: 'curiosity_gap', label: '≡ƒñö Curiosity Gap Hook'},
                    {value: 'shocking_statement', label: '≡ƒÿ▒ Shocking Statement'},
                    {value: 'controversial_take', label: '≡ƒöÑ Controversial Take'},
                    {value: 'before_after', label: '≡ƒôè Before & After Story'},
                    {value: 'secret_reveal', label: '≡ƒöô Secret Reveal'},
                    {value: 'mistake_warning', label: 'ΓÜá∩╕Å Mistake Warning'},
                    {value: 'myth_busting', label: '≡ƒÆÑ Myth Busting'},
                    {value: 'unpopular_opinion', label: '≡ƒùú∩╕Å Unpopular Opinion'},
                    {value: 'life_hack', label: '≡ƒÆí Life Hack / Tips Viral'},
                    {value: 'challenge_trend', label: '≡ƒÄ« Challenge / Trend'},
                    {value: 'reaction_bait', label: '≡ƒÆ¼ Reaction Bait'},
                    {value: 'cliffhanger', label: 'ΓÅ╕∩╕Å Cliffhanger Ending'},
                    {value: 'number_list', label: '≡ƒöó Number List (5 Cara, 10 Tips)'},
                    {value: 'question_hook', label: 'Γ¥ô Question Hook'},
                    {value: 'emotional_trigger', label: '≡ƒÆö Emotional Trigger'},
                    {value: 'fomo_content', label: 'ΓÅ░ FOMO Content'},
                    {value: 'plot_twist', label: '≡ƒÄ¡ Plot Twist Story'},
                    {value: 'relatable_content', label: '≡ƒÿé Relatable Content'},
                    {value: 'storytime', label: '≡ƒôû Storytime (Viral Format)'}
                ],
                trend_fresh_ideas: [
                    {value: 'trending_topic', label: '≡ƒöÑ Trending Topic Ideas'},
                    {value: 'viral_challenge', label: '≡ƒÄ» Viral Challenge Ideas'},
                    {value: 'seasonal_content', label: '≡ƒôà Seasonal Content Ideas'},
                    {value: 'holiday_campaign', label: '≡ƒÄë Holiday Campaign Ideas'},
                    {value: 'current_events', label: '≡ƒô░ Current Events Angle'},
                    {value: 'meme_marketing', label: '≡ƒÿé Meme Marketing Ideas'},
                    {value: 'tiktok_trend', label: '≡ƒÄ╡ TikTok Trend Ideas'},
                    {value: 'instagram_trend', label: '≡ƒô▒ Instagram Trend Ideas'},
                    {value: 'youtube_trend', label: '≡ƒô║ YouTube Trend Ideas'},
                    {value: 'x_trend', label: 'X Trend Ideas'},
                    {value: 'content_series', label: '≡ƒô║ Content Series Ideas'},
                    {value: 'collaboration_ideas', label: '≡ƒñ¥ Collaboration Ideas'},
                    {value: 'giveaway_ideas', label: '≡ƒÄü Giveaway Campaign Ideas'},
                    {value: 'user_generated', label: '≡ƒæÑ User Generated Content Ideas'},
                    {value: 'behind_scenes', label: '≡ƒÄ¼ Behind The Scenes Ideas'},
                    {value: 'educational_series', label: '≡ƒÄô Educational Series Ideas'},
                    {value: 'storytelling_series', label: '≡ƒôû Storytelling Series Ideas'},
                    {value: 'product_launch', label: '≡ƒÜÇ Product Launch Ideas'},
                    {value: 'rebranding_ideas', label: 'Γ£¿ Rebranding Campaign Ideas'},
                    {value: 'crisis_content', label: '≡ƒåÿ Crisis/Comeback Content Ideas'}
                ],
                industry_presets: [
                    {value: 'fashion_clothing', label: '≡ƒæù Fashion & Pakaian'},
                    {value: 'food_beverage', label: '≡ƒìö Makanan & Minuman'},
                    {value: 'beauty_skincare', label: '≡ƒÆä Kecantikan & Skincare'},
                    {value: 'printing_service', label: '≡ƒû¿∩╕Å Jasa Printing & Percetakan'},
                    {value: 'photography', label: '≡ƒô╖ Jasa Fotografi'},
                    {value: 'catering', label: '≡ƒì▒ Catering & Katering'},
                    {value: 'tiktok_shop', label: '≡ƒ¢ì∩╕Å TikTok Shop'},
                    {value: 'shopee_affiliate', label: '≡ƒ¢Æ Affiliate Shopee'},
                    {value: 'home_decor', label: '≡ƒÅá Dekorasi Rumah'},
                    {value: 'handmade_craft', label: 'Γ£é∩╕Å Kerajinan Tangan'},
                    {value: 'digital_service', label: '≡ƒÆ╗ Jasa Digital'},
                    {value: 'automotive', label: '≡ƒÜù Otomotif & Aksesoris'}
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
                    {value: 'tofu_awareness', label: '≡ƒÄ» TOFU - Awareness Stage (Top of Funnel)'},
                    {value: 'tofu_blog_post', label: '≡ƒô¥ TOFU - Blog Post Edukatif'},
                    {value: 'tofu_social_media', label: '≡ƒô▒ TOFU - Social Media Content'},
                    {value: 'tofu_video_content', label: '≡ƒÄ¼ TOFU - Video Content Script'},
                    {value: 'mofu_consideration', label: '≡ƒñö MOFU - Consideration Stage (Middle of Funnel)'},
                    {value: 'mofu_case_study', label: '≡ƒôè MOFU - Case Study / Success Story'},
                    {value: 'mofu_comparison', label: 'ΓÜû∩╕Å MOFU - Product Comparison'},
                    {value: 'mofu_webinar', label: '≡ƒÄô MOFU - Webinar / Workshop Copy'},
                    {value: 'mofu_email_nurture', label: '≡ƒôº MOFU - Email Nurture Sequence'},
                    {value: 'bofu_decision', label: '≡ƒÆ░ BOFU - Decision Stage (Bottom of Funnel)'},
                    {value: 'bofu_sales_page', label: '≡ƒÄ» BOFU - Sales Page Copy'},
                    {value: 'bofu_demo_trial', label: '≡ƒöô BOFU - Demo / Free Trial Copy'},
                    {value: 'bofu_testimonial', label: 'Γ¡É BOFU - Testimonial / Social Proof'},
                    {value: 'bofu_urgency', label: 'ΓÅ░ BOFU - Urgency & Scarcity Copy'},
                    {value: 'retention_onboarding', label: '≡ƒæï Retention - Onboarding Sequence'},
                    {value: 'retention_upsell', label: '≡ƒôê Retention - Upsell / Cross-sell'},
                    {value: 'retention_reactivation', label: '≡ƒöä Retention - Reactivation Campaign'},
                    {value: 'complete_funnel', label: '≡ƒÄ» Complete Funnel Sequence (All Stages)'}
                ],
                sales_page: [
                    {value: 'complete_sales_page', label: '≡ƒÄ» Complete Sales Page (Full Structure)'},
                    {value: 'hero_section', label: '≡ƒª╕ Hero Section (Headline + Subheadline + CTA)'},
                    {value: 'problem_agitate', label: '≡ƒÿ░ Problem & Agitate Section'},
                    {value: 'solution_presentation', label: 'Γ£¿ Solution Presentation'},
                    {value: 'features_benefits', label: 'ΓÜí Features & Benefits Section'},
                    {value: 'social_proof', label: 'Γ¡É Social Proof (Testimonials + Reviews)'},
                    {value: 'pricing_section', label: '≡ƒÆ░ Pricing Section (Value Stack)'},
                    {value: 'faq_objections', label: 'Γ¥ô FAQ & Objection Handling'},
                    {value: 'guarantee_risk', label: '≡ƒ¢í∩╕Å Guarantee / Risk Reversal'},
                    {value: 'urgency_scarcity', label: 'ΓÅ░ Urgency & Scarcity Elements'},
                    {value: 'final_cta', label: '≡ƒÄ» Final CTA (Call to Action)'},
                    {value: 'bonus_stack', label: '≡ƒÄü Bonus Stack Section'},
                    {value: 'about_creator', label: '≡ƒæñ About Creator / Company'},
                    {value: 'vsl_script', label: '≡ƒÄ¼ VSL (Video Sales Letter) Script'},
                    {value: 'webinar_sales', label: '≡ƒÄô Webinar Sales Pitch'},
                    {value: 'product_launch_sales', label: '≡ƒÜÇ Product Launch Sales Page'},
                    {value: 'saas_sales_page', label: '≡ƒÆ╗ SaaS Sales Page'},
                    {value: 'course_sales_page', label: '≡ƒôÜ Course Sales Page'},
                    {value: 'coaching_sales_page', label: '≡ƒÄ» Coaching/Consulting Sales Page'},
                    {value: 'ecommerce_product_page', label: '≡ƒ¢ì∩╕Å E-commerce Product Page'}
                ],
                lead_magnet: [
                    {value: 'ebook_landing', label: '≡ƒôÜ Free eBook Landing Page'},
                    {value: 'checklist_template', label: 'Γ£à Checklist / Template Opt-in'},
                    {value: 'webinar_registration', label: '≡ƒÄô Webinar Registration Page'},
                    {value: 'free_trial', label: '≡ƒöô Free Trial Sign-up Copy'},
                    {value: 'resource_library', label: '≡ƒôü Resource Library Access'},
                    {value: 'quiz_assessment', label: '≡ƒôè Quiz / Assessment Lead Magnet'},
                    {value: 'video_series', label: '≡ƒÄ¼ Video Series Opt-in'},
                    {value: 'mini_course', label: '≡ƒÄô Mini Course / Email Course'},
                    {value: 'toolkit_bundle', label: '≡ƒº░ Toolkit / Bundle Offer'},
                    {value: 'cheat_sheet', label: '≡ƒôï Cheat Sheet / Quick Guide'},
                    {value: 'case_study_download', label: '≡ƒôè Case Study Download'},
                    {value: 'whitepaper_report', label: '≡ƒôä Whitepaper / Industry Report'},
                    {value: 'swipe_file', label: '≡ƒô¥ Swipe File / Templates'},
                    {value: 'calculator_tool', label: '≡ƒöó Calculator / Tool Access'},
                    {value: 'challenge_signup', label: '≡ƒÅå Challenge Sign-up Page'},
                    {value: 'consultation_booking', label: '≡ƒô₧ Free Consultation Booking'},
                    {value: 'demo_request', label: '≡ƒÄ» Demo Request Page'},
                    {value: 'newsletter_signup', label: '≡ƒôº Newsletter Sign-up Copy'},
                    {value: 'discount_coupon', label: '≡ƒÄƒ∩╕Å Discount Coupon Opt-in'},
                    {value: 'lead_magnet_delivery', label: '≡ƒô¼ Lead Magnet Delivery Email'}
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
                    {value: 'grand_opening', label: '≡ƒÄë Grand Opening'},
                    {value: 'flash_sale', label: 'ΓÜí Flash Sale / Sale Kilat'},
                    {value: 'discount_promo', label: '≡ƒÆ░ Diskon & Promo Spesial'},
                    {value: 'bazaar', label: '≡ƒ¢ì∩╕Å Bazaar / Pameran'},
                    {value: 'exhibition', label: '≡ƒÄ¿ Exhibition / Pameran Seni'},
                    {value: 'workshop', label: '≡ƒæ¿ΓÇì≡ƒÅ½ Workshop / Seminar'},
                    {value: 'product_launch', label: '≡ƒÜÇ Product Launch'},
                    {value: 'anniversary', label: '≡ƒÄé Anniversary / Ulang Tahun'},
                    {value: 'seasonal_promo', label: '≡ƒÄä Promo Musiman (Lebaran, Natal, dll)'},
                    {value: 'clearance_sale', label: '≡ƒÅ╖∩╕Å Clearance Sale / Obral'},
                    {value: 'buy_1_get_1', label: '≡ƒÄü Buy 1 Get 1 / Bundling'},
                    {value: 'loyalty_program', label: 'Γ¡É Program Loyalitas / Member'},
                    {value: 'giveaway', label: '≡ƒÄü Giveaway / Kuis Berhadiah'},
                    {value: 'pre_order', label: '≡ƒôª Pre-Order Campaign'},
                    {value: 'limited_edition', label: '≡ƒÆÄ Limited Edition / Exclusive'},
                    {value: 'collaboration', label: '≡ƒñ¥ Kolaborasi Brand'},
                    {value: 'charity_event', label: 'Γ¥ñ∩╕Å Event Charity / Sosial'},
                    {value: 'meet_greet', label: '≡ƒæï Meet & Greet / Gathering'},
                    {value: 'live_shopping', label: '≡ƒô▒ Live Shopping / Live Selling'},
                    {value: 'countdown_promo', label: 'ΓÅ░ Countdown Promo (24 Jam, 3 Hari, dll)'}
                ],
                hr_recruitment: [
                    {value: 'job_description', label: '≡ƒôï Job Description / JD'},
                    {value: 'job_vacancy', label: '≡ƒôó Lowongan Kerja / Vacancy Post'},
                    {value: 'job_requirements', label: 'Γ£à Job Requirements / Kualifikasi'},
                    {value: 'company_culture', label: '≡ƒÅó Company Culture Description'},
                    {value: 'employee_benefits', label: '≡ƒÄü Employee Benefits Package'},
                    {value: 'interview_questions', label: 'Γ¥ô Interview Questions'},
                    {value: 'offer_letter', label: '≡ƒÆî Offer Letter'},
                    {value: 'rejection_letter', label: '≡ƒôº Rejection Letter (Polite)'},
                    {value: 'onboarding_message', label: '≡ƒæï Onboarding Welcome Message'},
                    {value: 'internship_program', label: '≡ƒÄô Internship Program Description'},
                    {value: 'career_page', label: '≡ƒÆ╝ Career Page Content'},
                    {value: 'linkedin_job_post', label: '≡ƒÆ╝ LinkedIn Job Post'},
                    {value: 'instagram_hiring', label: '≡ƒô▒ Instagram Hiring Post'},
                    {value: 'whatsapp_recruitment', label: '≡ƒÆ¼ WhatsApp Recruitment Message'},
                    {value: 'employee_referral', label: '≡ƒñ¥ Employee Referral Program'},
                    {value: 'job_fair_booth', label: '≡ƒÄ¬ Job Fair Booth Description'},
                    {value: 'campus_recruitment', label: '≡ƒÄô Campus Recruitment Pitch'},
                    {value: 'remote_job', label: '≡ƒÅá Remote Job Description'},
                    {value: 'freelance_job', label: '≡ƒÆ╗ Freelance Job Brief'},
                    {value: 'part_time_job', label: 'ΓÅ░ Part-Time Job Post'}
                ],
                branding_tagline: [
                    {value: 'brand_tagline', label: 'Γ£¿ Brand Tagline / Slogan'},
                    {value: 'company_tagline', label: '≡ƒÅó Company Tagline'},
                    {value: 'product_tagline', label: '≡ƒôª Product Tagline'},
                    {value: 'brand_name', label: '≡ƒÄ» Brand Name Ideas'},
                    {value: 'product_name', label: '≡ƒÅ╖∩╕Å Product Name Ideas'},
                    {value: 'business_name', label: '≡ƒÆ╝ Business Name Ideas'},
                    {value: 'tshirt_quote', label: '≡ƒæò T-Shirt Quote / Text'},
                    {value: 'hoodie_text', label: '≡ƒºÑ Hoodie Text'},
                    {value: 'tote_bag_text', label: '≡ƒæ£ Tote Bag Text'},
                    {value: 'mug_text', label: 'Γÿò Mug Text'},
                    {value: 'sticker_text', label: '≡ƒÅ╖∩╕Å Sticker Text'},
                    {value: 'poster_quote', label: '≡ƒû╝∩╕Å Poster Quote'},
                    {value: 'motivational_quote', label: '≡ƒÆ¬ Motivational Quote'},
                    {value: 'funny_quote', label: '≡ƒÿé Funny Quote'},
                    {value: 'inspirational_quote', label: 'Γ£¿ Inspirational Quote'},
                    {value: 'logo_text', label: '≡ƒÄ¿ Logo Text / Wordmark'},
                    {value: 'brand_story', label: '≡ƒôû Brand Story (Short)'},
                    {value: 'brand_mission', label: '≡ƒÄ» Brand Mission Statement'},
                    {value: 'brand_vision', label: '≡ƒö« Brand Vision Statement'},
                    {value: 'brand_values', label: '≡ƒÆÄ Brand Values'},
                    {value: 'usp', label: 'ΓÜí USP (Unique Selling Proposition)'},
                    {value: 'elevator_pitch', label: '≡ƒÄñ Elevator Pitch (30 detik)'},
                    {value: 'brand_positioning', label: '≡ƒôì Brand Positioning Statement'},
                    {value: 'catchphrase', label: '≡ƒùú∩╕Å Catchphrase / Jargon Brand'},
                    {value: 'merchandise_collection', label: '≡ƒÄü Merchandise Collection Name'}
                ],
                education_institution: [
                    {value: 'school_achievement', label: '≡ƒÅå Pencapaian Sekolah/Kampus'},
                    {value: 'student_achievement', label: 'Γ¡É Prestasi Siswa/Mahasiswa'},
                    {value: 'graduation_announcement', label: '≡ƒÄô Pengumuman Kelulusan'},
                    {value: 'new_student_admission', label: '≡ƒô¥ Penerimaan Siswa Baru (PSB/PPDB)'},
                    {value: 'school_event', label: '≡ƒÄë Event Sekolah/Kampus'},
                    {value: 'national_holiday', label: '≡ƒç«≡ƒç⌐ Hari Besar Nasional'},
                    {value: 'education_day', label: '≡ƒôÜ Hari Pendidikan (Hardiknas, dll)'},
                    {value: 'teacher_day', label: '≡ƒæ¿ΓÇì≡ƒÅ½ Hari Guru'},
                    {value: 'independence_day', label: '≡ƒÄè HUT RI / Kemerdekaan'},
                    {value: 'religious_holiday', label: '≡ƒòî Hari Besar Keagamaan'},
                    {value: 'school_anniversary', label: '≡ƒÄé HUT Sekolah/Kampus'},
                    {value: 'academic_info', label: '≡ƒôó Informasi Akademik'},
                    {value: 'exam_announcement', label: '≡ƒô¥ Pengumuman Ujian'},
                    {value: 'scholarship_info', label: '≡ƒÆ░ Info Beasiswa'},
                    {value: 'extracurricular', label: 'ΓÜ╜ Kegiatan Ekstrakurikuler'},
                    {value: 'parent_meeting', label: '≡ƒæ¿ΓÇì≡ƒæ⌐ΓÇì≡ƒæº Rapat Orang Tua'},
                    {value: 'school_facility', label: '≡ƒÅ½ Fasilitas Sekolah/Kampus'},
                    {value: 'teacher_profile', label: '≡ƒæ⌐ΓÇì≡ƒÅ½ Profil Guru/Dosen'},
                    {value: 'alumni_success', label: '≡ƒîƒ Kisah Sukses Alumni'},
                    {value: 'government_program', label: '≡ƒÅ¢∩╕Å Program Pemerintah/Dinas'},
                    {value: 'public_service', label: '≡ƒôï Layanan Publik'},
                    {value: 'government_announcement', label: '≡ƒôó Pengumuman Resmi Instansi'},
                    {value: 'community_program', label: '≡ƒñ¥ Program Kemasyarakatan'},
                    {value: 'health_campaign', label: '≡ƒÅÑ Kampanye Kesehatan'},
                    {value: 'safety_awareness', label: 'ΓÜá∩╕Å Sosialisasi Keselamatan'}
                ],
                video_monetization: [
                    {value: 'tiktok_viral', label: '≡ƒÄ╡ TikTok - Konten Viral'},
                    {value: 'youtube_long', label: '≡ƒô║ YouTube - Video Panjang'},
                    {value: 'youtube_shorts', label: '≡ƒÄ¼ YouTube Shorts'},
                    {value: 'facebook_video', label: '≡ƒæÑ Facebook Video'},
                    {value: 'snack_video', label: '≡ƒì┐ Snack Video'},
                    {value: 'likee', label: 'Γ¥ñ∩╕Å Likee'},
                    {value: 'kwai', label: '≡ƒÄÑ Kwai'},
                    {value: 'bigo_live', label: '≡ƒô╣ Bigo Live'},
                    {value: 'nimo_tv', label: '≡ƒÄ« Nimo TV'}
                ],
                photo_monetization: [
                    {value: 'shutterstock', label: '≡ƒô╖ Shutterstock - Deskripsi Foto'},
                    {value: 'adobe_stock', label: '≡ƒÄ¿ Adobe Stock - Keywords'},
                    {value: 'getty_images', label: '≡ƒû╝∩╕Å Getty Images - Caption'},
                    {value: 'istock', label: '≡ƒô╕ iStock - Metadata'},
                    {value: 'freepik', label: '≡ƒÄ¡ Freepik - Tags'},
                    {value: 'vecteezy', label: '≡ƒÄ¿ Vecteezy - Description'}
                ],
                print_on_demand: [
                    {value: 'redbubble', label: '≡ƒÄ¿ Redbubble - Product Title'},
                    {value: 'teespring', label: '≡ƒæò Teespring - Description'},
                    {value: 'spreadshirt', label: '≡ƒæö Spreadshirt - Tags'},
                    {value: 'zazzle', label: '≡ƒÄü Zazzle - Product Copy'},
                    {value: 'society6', label: '≡ƒû╝∩╕Å Society6 - Bio'}
                ],
                freelance: [
                    {value: 'upwork_proposal', label: '≡ƒÆ╝ Upwork - Proposal'},
                    {value: 'fiverr_gig', label: '≡ƒÄ» Fiverr - Gig Description'},
                    {value: 'freelancer_bid', label: '≡ƒô¥ Freelancer - Bid'},
                    {value: 'sribulancer', label: '≡ƒç«≡ƒç⌐ Sribulancer - Penawaran'},
                    {value: 'projects_id', label: '≡ƒç«≡ƒç⌐ Projects.co.id - Proposal'},
                    {value: 'portfolio', label: '≡ƒôü Portfolio Description'},
                    {value: 'cover_letter', label: 'Γ£ë∩╕Å Cover Letter'}
                ],
                digital_products: [
                    {value: 'gumroad', label: '≡ƒ¢ì∩╕Å Gumroad - Product Page'},
                    {value: 'sellfy', label: '≡ƒÆ│ Sellfy - Sales Copy'},
                    {value: 'payhip', label: '≡ƒÆ░ Payhip - Description'},
                    {value: 'ebook_description', label: '≡ƒôÜ E-book Description'},
                    {value: 'course_landing', label: '≡ƒÄô Course Landing Page'},
                    {value: 'template_description', label: '≡ƒôä Template Description'}
                ],
                ebook_publishing: [
                    {value: 'kindle_description', label: '≡ƒô▒ Amazon Kindle - Book Description'},
                    {value: 'kindle_blurb', label: '≡ƒôû Kindle - Back Cover Blurb'},
                    {value: 'google_play_books', label: '≡ƒôÜ Google Play Books - Description'},
                    {value: 'apple_books', label: '≡ƒìÄ Apple Books - Synopsis'},
                    {value: 'kobo', label: '≡ƒôÿ Kobo Writing Life - Description'},
                    {value: 'barnes_noble', label: '≡ƒôò Barnes & Noble Press - Copy'},
                    {value: 'leanpub', label: '≡ƒôù Leanpub - Sales Page'},
                    {value: 'gumroad_ebook', label: '≡ƒ¢ì∩╕Å Gumroad - eBook Landing'},
                    {value: 'gramedia_digital', label: '≡ƒç«≡ƒç⌐ Gramedia Digital - Deskripsi'},
                    {value: 'mizanstore', label: '≡ƒç«≡ƒç⌐ Mizanstore - Sinopsis'},
                    {value: 'kubuku', label: '≡ƒç«≡ƒç⌐ Kubuku - Description'},
                    {value: 'storial', label: '≡ƒç«≡ƒç⌐ Storial - Cerita'},
                    {value: 'book_title', label: 'Γ£¿ Book Title Generator'},
                    {value: 'chapter_outline', label: '≡ƒôï Chapter Outline'},
                    {value: 'author_bio', label: '≡ƒæñ Author Bio'}
                ],
                academic_writing: [
                    {value: 'abstract', label: '≡ƒôä Abstract / Abstrak'},
                    {value: 'research_title', label: '≡ƒÄ» Research Title'},
                    {value: 'introduction', label: '≡ƒô¥ Introduction'},
                    {value: 'literature_review', label: '≡ƒôÜ Literature Review Outline'},
                    {value: 'methodology', label: '≡ƒö¼ Methodology Description'},
                    {value: 'conclusion', label: 'Γ£à Conclusion'},
                    {value: 'keywords', label: '≡ƒöæ Keywords Generator'},
                    {value: 'researchgate_profile', label: '≡ƒö¼ ResearchGate - Profile'},
                    {value: 'academia_bio', label: '≡ƒÄô Academia.edu - Bio'},
                    {value: 'paper_summary', label: '≡ƒôè Paper Summary'},
                    {value: 'conference_abstract', label: '≡ƒÄñ Conference Abstract'}
                ],
                writing_monetization: [
                    {value: 'medium_article', label: '≡ƒô¥ Medium - Article'},
                    {value: 'medium_headline', label: 'Γ£¿ Medium - Headline'},
                    {value: 'substack_post', label: '≡ƒôº Substack - Newsletter Post'},
                    {value: 'substack_welcome', label: '≡ƒæï Substack - Welcome Email'},
                    {value: 'patreon_tier', label: '≡ƒÆÄ Patreon - Tier Description'},
                    {value: 'patreon_post', label: '≡ƒôó Patreon - Exclusive Post'},
                    {value: 'kofi_page', label: 'Γÿò Ko-fi - Page Description'},
                    {value: 'newsletter_intro', label: '≡ƒô¼ Newsletter Introduction'},
                    {value: 'paid_content', label: '≡ƒÆ░ Paid Content Teaser'}
                ],
                affiliate_marketing: [
                    {value: 'shopee_affiliate', label: '≡ƒ¢Æ Shopee Affiliate - Review'},
                    {value: 'tokopedia_affiliate', label: '≡ƒ¢ì∩╕Å Tokopedia Affiliate - Caption'},
                    {value: 'tiktok_affiliate', label: '≡ƒÄ╡ TikTok Affiliate - Script'},
                    {value: 'amazon_associates', label: '≡ƒôª Amazon Associates - Review'},
                    {value: 'product_comparison', label: 'ΓÜû∩╕Å Product Comparison'},
                    {value: 'honest_review', label: 'Γ¡É Honest Review'}
                ],
                blog_seo: [
                    {value: 'blog_post', label: '≡ƒô¥ Blog Post (SEO Optimized)'},
                    {value: 'article_intro', label: '≡ƒÄ» Article Introduction'},
                    {value: 'meta_description', label: '≡ƒöì Meta Description'},
                    {value: 'seo_title', label: '≡ƒôî SEO Title / H1'},
                    {value: 'h2_h3_headings', label: '≡ƒôæ H2/H3 Headings Generator'},
                    {value: 'listicle', label: '≡ƒôï Listicle Article (Top 10, Best 5)'},
                    {value: 'how_to_guide', label: '≡ƒôû How-to Guide / Tutorial'},
                    {value: 'product_review', label: 'Γ¡É Product Review Blog'},
                    {value: 'comparison_post', label: 'ΓÜû∩╕Å Comparison Post (A vs B)'},
                    {value: 'pillar_content', label: '≡ƒÅ¢∩╕Å Pillar Content / Ultimate Guide'},
                    {value: 'faq_schema', label: 'Γ¥ô FAQ Schema Markup'},
                    {value: 'featured_snippet', label: '≡ƒÄ» Featured Snippet Optimization'},
                    {value: 'local_seo', label: '≡ƒôì Local SEO Content'},
                    {value: 'keyword_cluster', label: '≡ƒöæ Keyword Cluster Content'},
                    {value: 'internal_linking', label: '≡ƒöù Internal Linking Anchor Text'},
                    {value: 'alt_text', label: '≡ƒû╝∩╕Å Image Alt Text'},
                    {value: 'schema_markup', label: '≡ƒôè Schema Markup Description'},
                    {value: 'guest_post', label: 'Γ£ì∩╕Å Guest Post Pitch'},
                    {value: 'content_update', label: '≡ƒöä Content Update/Refresh'},
                    {value: 'roundup_post', label: '≡ƒÄü Roundup Post (Expert Roundup)'}
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
                    {value: 'wedding', label: '≡ƒÆÆ Undangan Pernikahan'},
                    {value: 'engagement', label: '≡ƒÆì Undangan Lamaran / Tunangan'},
                    {value: 'birthday', label: '≡ƒÄé Undangan Ulang Tahun'},
                    {value: 'aqiqah', label: '≡ƒæ╢ Undangan Aqiqah / Syukuran Bayi'},
                    {value: 'khitanan', label: '≡ƒòî Undangan Khitanan / Sunatan'},
                    {value: 'graduation', label: '≡ƒÄô Undangan Wisuda'},
                    {value: 'grand_opening', label: '≡ƒÄë Undangan Grand Opening'},
                    {value: 'meeting', label: '≡ƒôï Undangan Rapat / Meeting'},
                    {value: 'seminar', label: '≡ƒÄñ Undangan Seminar / Workshop'},
                    {value: 'reunion', label: '≡ƒæÑ Undangan Reuni / Gathering'},
                    {value: 'other_event', label: '≡ƒÄè Undangan Acara Lainnya'},
                    {value: 'wedding_caption', label: '≡ƒÆÆ Caption Pernikahan (Social Media)'},
                    {value: 'event_announcement', label: '≡ƒôó Pengumuman Event'},
                    {value: 'save_the_date', label: '≡ƒôà Save The Date'},
                    {value: 'thank_you_card', label: '≡ƒÖÅ Kartu Ucapan Terima Kasih'},
                    {value: 'rsvp_message', label: 'Γ£ë∩╕Å Pesan RSVP / Konfirmasi Kehadiran'}
                ],
                short_drama: [
                    {value: 'drama_script', label: '≡ƒÄ¼ Script Short Drama (Full Scene)'},
                    {value: 'romantic_dialogue', label: '≡ƒÆò Percakapan Romantis (Baper)'},
                    {value: 'conflict_scene', label: '≡ƒÿñ Adegan Konflik / Pertengkaran'},
                    {value: 'plot_twist_drama', label: '≡ƒîÇ Plot Twist Drama'},
                    {value: 'character_monologue', label: '≡ƒÄ¡ Monolog Karakter (Emosional)'},
                    {value: 'drama_opening', label: '≡ƒÄÑ Opening Scene (Hook 30 Detik)'},
                    {value: 'breakup_scene', label: '≡ƒÆö Adegan Putus Cinta'},
                    {value: 'reunion_scene', label: '≡ƒÑ╣ Adegan Reuni Mengharukan'},
                    {value: 'misunderstanding_scene', label: '≡ƒÿñ Adegan Salah Paham'},
                    {value: 'confession_scene', label: '≡ƒÆî Adegan Confess Perasaan'},
                    {value: 'villain_dialogue', label: '≡ƒÿê Dialog Villain / Antagonis'},
                    {value: 'family_drama', label: '≡ƒæ¿ΓÇì≡ƒæ⌐ΓÇì≡ƒæº Drama Keluarga'},
                    {value: 'office_romance', label: '≡ƒÆ╝ Office Romance'},
                    {value: 'enemies_to_lovers', label: 'ΓÜö∩╕Å Enemies to Lovers'},
                    {value: 'second_chance_romance', label: '≡ƒöä Second Chance Romance (Mantan)'}
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

            // Image Analysis Methods
            handleAnalysisImageSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.analysisForm.file = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.analysisForm.preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            handleAnalysisImageDrop(event) {
                event.target.classList.remove('border-purple-400', 'bg-purple-50');
                const file = event.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    this.analysisForm.file = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.analysisForm.preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            removeAnalysisImage() {
                this.analysisForm.file = null;
                this.analysisForm.preview = null;
                this.$refs.analysisImageInput.value = '';
            },

            setAnalysisPreset(preset) {
                switch(preset) {
                    case 'product':
                        this.analysisForm.options = ['objects', 'colors', 'quality', 'marketing', 'suggestions'];
                        this.analysisForm.context = 'Analisis foto produk untuk keperluan marketing dan penjualan online';
                        break;
                    case 'social':
                        this.analysisForm.options = ['composition', 'mood', 'colors', 'marketing'];
                        this.analysisForm.context = 'Analisis foto untuk konten social media (Instagram/TikTok)';
                        break;
                    case 'design':
                        this.analysisForm.options = ['typography', 'layout', 'branding', 'cta_design', 'colors', 'suggestions'];
                        this.analysisForm.context = 'Analisis desain grafis: banner, poster, feed Instagram, atau materi marketing. Nilai tipografi, layout, konsistensi brand, dan efektivitas CTA.';
                        break;
                    case 'complete':
                        this.analysisForm.options = ['objects', 'colors', 'composition', 'mood', 'text', 'marketing', 'quality', 'suggestions', 'typography', 'layout', 'branding', 'cta_design'];
                        this.analysisForm.context = 'Analisis lengkap semua aspek gambar dan desain untuk keperluan bisnis';
                        break;
                }
            },

            async analyzeImageWithAI() {
                if (!this.analysisForm.file) {
                    alert('Mohon upload gambar terlebih dahulu');
                    return;
                }

                if (this.analysisForm.options.length === 0) {
                    alert('Mohon pilih minimal satu jenis analisis');
                    return;
                }

                this.loading = true;
                this.result = '';
                this.error = '';

                const formData = new FormData();
                formData.append('image', this.analysisForm.file);
                formData.append('options', JSON.stringify(this.analysisForm.options));
                formData.append('context', this.analysisForm.context);

                try {
                    const response = await fetch('/api/ai/analyze-image', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (handleFeatureLocked(data)) return;

                    if (data.success) {
                        this.result = data.analysis;
                        this.lastCaptionId = data.caption_id || null;
                        this.showNotification('Γ£à Analisis gambar berhasil!', 'success');
                        setTimeout(() => {
                            document.querySelector('.mt-6').scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                    } else {
                        this.error = data.message || 'Terjadi kesalahan saat menganalisis gambar';
                        this.showNotification('Γ¥î ' + this.error, 'error');
                    }
                } catch (error) {
                    this.error = 'Terjadi kesalahan jaringan';
                    this.showNotification('Γ¥î Terjadi kesalahan jaringan', 'error');
                } finally {
                    this.loading = false;
                }
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
                        this.showNotification(`Γ£à Konten video berhasil di-generate${imageText}!`, 'success');
                        setTimeout(() => {
                            document.querySelector('.mt-6').scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                    } else {
                        this.error = data.message || 'Terjadi kesalahan saat generate konten video';
                        this.showNotification('Γ¥î ' + this.error, 'error');
                    }
                } catch (error) {
                    this.error = 'Terjadi kesalahan jaringan';
                    this.showNotification('Γ¥î Terjadi kesalahan jaringan', 'error');
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
                        let resultText = '≡ƒû╝∩╕Å IMAGE CAPTION RESULT\n\n';
                        
                        if (data.detected_objects && data.detected_objects.length > 0) {
                            resultText += '≡ƒöì Objek Terdeteksi:\n';
                            data.detected_objects.forEach(obj => {
                                resultText += `ΓÇó ${obj}\n`;
                            });
                            resultText += '\n';
                        }

                        if (data.caption_single) {
                            resultText += '≡ƒô¥ CAPTION SINGLE POST:\n';
                            resultText += data.caption_single + '\n\n';
                        }

                        if (data.caption_carousel && data.caption_carousel.length > 0) {
                            resultText += '≡ƒô▒ CAPTION CAROUSEL:\n\n';
                            data.caption_carousel.forEach((slide, index) => {
                                resultText += `Slide ${index + 1}:\n${slide}\n\n`;
                            });
                        }

                        if (data.editing_tips && data.editing_tips.length > 0) {
                            resultText += '≡ƒÆí TIPS EDITING:\n';
                            data.editing_tips.forEach(tip => {
                                resultText += `ΓÇó ${tip}\n`;
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
                        'closing': 'persuasive',      // Closing ΓåÆ Persuasive
                        'awareness': 'educational',   // Awareness ΓåÆ Educational
                        'engagement': 'casual',       // Engagement ΓåÆ Casual
                        'viral': 'funny'              // Viral ΓåÆ Funny
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
                        'ibu_muda': '\n\nGaya Bahasa: Friendly & caring (Bun, Kak, emoji Γ¥ñ∩╕Å≡ƒÖÅ, bahasa ibu-ibu)',
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
                    // ≡ƒñû Add ML data to request
                    const industry = this.getIndustryFromForm();
                    const requestData = {
                        ...this.form,
                        mode: this.mode, // simple or advanced
                        industry: industry, // ≡ƒñû ML: industry
                        goal: this.form.goal || 'closing', // ≡ƒñû ML: goal
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
                        
                        // ≡ƒñû ML: Store ML data
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
                        
                        // Quota / subscription error ΓÇö show inline banner
                        if (data.quota_error) {
                            let banner = document.getElementById('quota-error-banner');
                            if (!banner) {
                                banner = document.createElement('div');
                                banner.id = 'quota-error-banner';
                                banner.className = 'mb-5 bg-red-50 border border-red-300 rounded-xl p-4 text-sm text-red-800 flex items-start gap-3';
                                const wrapper = document.querySelector('.p-6[x-data]');
                                if (wrapper) wrapper.insertBefore(banner, wrapper.children[1]);
                            }
                            banner.innerHTML = '<span class="text-xl flex-shrink-0">≡ƒÜ½</span><span>' + errorMessage + '</span>';
                            banner.classList.remove('hidden');
                            banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else if (errorMessage.includes('API key') || errorMessage.includes('tidak valid') || errorMessage.includes('expired')) {
                            this.error = 'ΓÜá∩╕Å Layanan AI sedang tidak tersedia. Silakan coba beberapa saat lagi atau hubungi admin.';
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
                        this.showNotification(data.is_public ? '🌟 Caption dipublikasikan ke Explore!' : 'Caption dihapus dari Explore.', 'success');
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
                            alert('Γ£ô Caption saved for analytics tracking!\n\nYou can now track its performance in Analytics page.');
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
                
                this.showNotification('Γ£à Brand Voice dimuat! Tinggal isi brief dan generate.');
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
                        this.showNotification('Γ£à Brand Voice berhasil disimpan!');
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
            
            // ≡ƒñû ML FEATURES (Simplified - no upgrade modal)
            mlStatus: null,
            mlPreview: {},
            showMLPreview: false,
            mlLoading: false,
            
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

            // ≡ƒöì ANALYSIS METHODS
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
                        throw new Error('ΓÜá∩╕Å AI sedang tidak stabil. Coba lagi dalam beberapa saat atau gunakan caption yang lebih pendek.');
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
                    this.showNotification('Γ£à Caption berhasil diperbarui!');
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

            // ≡ƒñû ML FEATURES - Additional Methods
            weeklyTrends: {},
            refreshing: false,

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
                
                if (hoursAgo < 1) return '≡ƒƒó Baru saja diperbarui';
                if (hoursAgo < 6) return '≡ƒƒó Diperbarui ' + hoursAgo + ' jam lalu';
                if (hoursAgo < 24) return '≡ƒƒí Diperbarui hari ini';
                if (hoursAgo < 48) return '≡ƒƒí Diperbarui kemarin';
                return '≡ƒö┤ Perlu diperbarui';
            },

            // Check if data is fresh (less than 24 hours)
            isDataFresh() {
                if (!this.mlPreview?.generated_at) return false;
                
                const generatedAt = new Date(this.mlPreview.generated_at);
                const now = new Date();
                const hoursAgo = Math.floor((now - generatedAt) / (1000 * 60 * 60));
                
                return hoursAgo < 24;
            },

            // ≡ƒôê PERFORMANCE PREDICTOR FUNCTIONS
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
                    this.showNotification('Γ¥î ' + this.predictorError, 'error');
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
                    this.showNotification('Γ¥î ' + (error.message || 'Terjadi kesalahan saat generate variants'), 'error');
                } finally {
                    this.variantsLoading = false;
                }
            },

            showVariantsModal(data) {
                let modalContent = `
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-semibold">≡ƒº¬ A/B Testing Variants</h3>
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
                        <h4 class="font-semibold mb-2">≡ƒô¥ Original (A)</h4>
                        <div class="bg-gray-50 p-3 rounded text-sm">${data.original_caption}</div>
                    </div>
                `;

                // Add variants
                data.variants.forEach((variant, index) => {
                    modalContent += `
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold">≡ƒöä Variant ${String.fromCharCode(66 + index)} (${variant.focus})</h4>
                                <button onclick="copyToClipboard('${variant.caption.replace(/'/g, "\\'")}').then(()=>{this.textContent='Γ£à Copied!';setTimeout(()=>{this.textContent='Copy'},2000)})" 
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
                                <h4 class="font-semibold mb-2">≡ƒôè Test Setup Recommendations</h4>
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
            },

            // ≡ƒôÜ TEMPLATE LIBRARY FUNCTIONS
            async loadTemplates() {
                try {
                    // Load templates from the TemplatePrompts service
                    const response = await fetch('/api/templates/all', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.allTemplates = data.templates || [];
                        this.filteredTemplates = this.allTemplates;
                    } else {
                        // Fallback to demo templates if API fails
                        this.loadDemoTemplates();
                    }
                } catch (error) {
                    this.loadDemoTemplates();
                }
            },

            loadDemoTemplates() {
                // Demo templates based on TemplatePrompts service
                this.allTemplates = [
                    {
                        id: 1,
                        title: 'Clickbait Title Generator',
                        description: 'Buat judul clickbait yang honest tapi menarik perhatian',
                        category: 'viral_clickbait',
                        category_label: '≡ƒöÑ Viral & Clickbait',
                        platform: 'universal',
                        tone: 'persuasive',
                        format: '- Maksimal 60 karakter\n- Bikin penasaran tapi tetap jujur\n- Gunakan angka, pertanyaan, atau kata kuat\n- Hindari clickbait palsu\n\nContoh:\n"5 Rahasia yang Bikin Bisnis Online Sukses"\n"Kenapa 90% UMKM Gagal? (Dan Cara Menghindarinya)"',
                        usage_count: 1250,
                        is_favorite: false
                    },
                    {
                        id: 2,
                        title: 'Instagram Caption Hook',
                        description: 'Hook pembuka yang bikin audience penasaran dan mau baca lebih lanjut',
                        category: 'viral_clickbait',
                        category_label: '≡ƒöÑ Viral & Clickbait',
                        platform: 'instagram',
                        tone: 'casual',
                        format: '- Mulai dengan statement yang bikin penasaran\n- Jangan kasih jawaban di awal\n- Bikin audience mau tahu lebih lanjut\n- Maksimal 50 kata\n\nContoh:\n"Tau gak sih, ada 1 kesalahan yang bikin 80% bisnis online bangkrut..."\n"Pernah nggak kamu ngerasa udah kerja keras tapi hasil masih zonk?"',
                        usage_count: 890,
                        is_favorite: false
                    },
                    {
                        id: 3,
                        title: 'Flash Sale Promo',
                        description: 'Template promosi flash sale yang bikin FOMO dan urgency',
                        category: 'event_promo',
                        category_label: '≡ƒÄë Event & Promo',
                        platform: 'universal',
                        tone: 'persuasive',
                        format: '≡ƒÜ¿ FLASH SALE ALERT! ≡ƒÜ¿\n\nΓÅ░ HANYA [DURASI]!\n≡ƒÆÑ DISKON [PERSENTASE]% untuk [PRODUK]\n≡ƒöÑ Stok terbatas: [JUMLAH] pcs saja!\n\nΓ£à Kualitas premium\nΓ£à Garansi resmi\nΓ£à Free ongkir seluruh Indonesia\n\nΓÜí Jangan sampai kehabisan!\n≡ƒô▒ Order sekarang: [LINK/KONTAK]\n\n#FlashSale #Diskon #PromoTerbatas',
                        usage_count: 2100,
                        is_favorite: false
                    },
                    {
                        id: 4,
                        title: 'Job Vacancy Post',
                        description: 'Template lowongan kerja yang menarik talent terbaik',
                        category: 'hr_recruitment',
                        category_label: '≡ƒÆ╝ HR & Recruitment',
                        platform: 'linkedin',
                        tone: 'formal',
                        format: '≡ƒÜÇ WE ARE HIRING! ≡ƒÜÇ\n\n≡ƒôì Posisi: [JABATAN]\n≡ƒÅó Perusahaan: [NAMA PERUSAHAAN]\n≡ƒôì Lokasi: [KOTA]\n≡ƒÆ╝ Tipe: [FULL TIME/PART TIME/CONTRACT]\n\nΓ£¿ Yang Kami Cari:\nΓÇó [KUALIFIKASI 1]\nΓÇó [KUALIFIKASI 2]\nΓÇó [KUALIFIKASI 3]\n\n≡ƒÄü Yang Kami Tawarkan:\nΓÇó Gaji kompetitif\nΓÇó Benefit menarik\nΓÇó Lingkungan kerja yang supportive\nΓÇó Kesempatan berkembang\n\n≡ƒôº Kirim CV ke: [EMAIL]\n≡ƒÆ¼ Info lebih lanjut: [KONTAK]\n\n#LowonganKerja #Hiring #Karir #[KOTA]Jobs',
                        usage_count: 750,
                        is_favorite: false
                    },
                    {
                        id: 5,
                        title: 'Brand Tagline Generator',
                        description: 'Buat tagline brand yang memorable dan impactful',
                        category: 'branding_tagline',
                        category_label: '≡ƒÄ» Branding & Tagline',
                        platform: 'universal',
                        tone: 'inspirational',
                        format: 'Kriteria tagline yang baik:\n- Singkat & mudah diingat (3-7 kata)\n- Reflect brand values\n- Unique & differentiated\n- Emotional connection\n- Easy to pronounce\n\nFormula:\n1. [BENEFIT] + [EMOTION]\n2. [ACTION] + [RESULT]\n3. [PROMISE] + [ASPIRATION]\n\nContoh:\n"Wujudkan Impian Bersama" (Bank)\n"Solusi Cerdas, Hidup Mudah" (Tech)\n"Cantik Alami, Percaya Diri" (Beauty)',
                        usage_count: 1680,
                        is_favorite: false
                    },
                    {
                        id: 6,
                        title: 'TikTok Viral Script',
                        description: 'Script TikTok yang berpotensi viral dengan hook kuat',
                        category: 'video_monetization',
                        category_label: '≡ƒô╣ Video Content',
                        platform: 'tiktok',
                        tone: 'funny',
                        format: 'ΓÅ▒∩╕Å DETIK 1-3 (HOOK CRUCIAL!):\n"POV: Kamu [SITUASI RELATABLE]..."\n"Jangan scroll dulu! Ini [BENEFIT]"\n"Siapa yang [PERTANYAAN ENGAGING]?"\n\nΓÅ▒∩╕Å DETIK 4-30 (CONTENT):\n- Deliver value/entertainment\n- Keep it visual & engaging\n- Use trending sounds\n- Quick cuts/transitions\n\nΓÅ▒∩╕Å DETIK 30-60 (CTA):\n"Follow untuk tips lainnya!"\n"Tag teman yang butuh ini!"\n"Comment pengalaman kamu!"\n\n#FYP #Viral #[NICHE]TikTok',
                        usage_count: 3200,
                        is_favorite: false
                    },
                    {
                        id: 7,
                        title: 'Shopee Product Description',
                        description: 'Deskripsi produk Shopee yang convert dan SEO-friendly',
                        category: 'affiliate_marketing',
                        category_label: '≡ƒñ¥ Affiliate Marketing',
                        platform: 'shopee',
                        tone: 'persuasive',
                        format: '≡ƒ¢ì∩╕Å [NAMA PRODUK] - [BENEFIT UTAMA]\n\nΓ£¿ KEUNGGULAN:\nΓ£à [KEUNGGULAN 1]\nΓ£à [KEUNGGULAN 2]\nΓ£à [KEUNGGULAN 3]\n\n≡ƒôª SPESIFIKASI:\nΓÇó Material: [BAHAN]\nΓÇó Ukuran: [DIMENSI]\nΓÇó Warna: [PILIHAN WARNA]\nΓÇó Berat: [BERAT]\n\n≡ƒÄü BONUS:\nΓÇó Free bubble wrap\nΓÇó Garansi [DURASI]\nΓÇó Customer service 24/7\n\nΓ¡É TESTIMONI: "[REVIEW CUSTOMER]"\n\n≡ƒÜÜ PENGIRIMAN:\nΓÇó Same day (Jakarta)\nΓÇó 1-3 hari (Jawa)\nΓÇó 3-7 hari (luar Jawa)\n\n≡ƒÆ░ HARGA SPESIAL: Rp [HARGA] (Normal: Rp [HARGA_NORMAL])\n\n≡ƒ¢Æ ORDER SEKARANG! Stok terbatas!\n\n#[KATEGORI] #[BRAND] #QualityProduct',
                        usage_count: 1890,
                        is_favorite: false
                    },
                    {
                        id: 8,
                        title: 'Blog SEO Article Outline',
                        description: 'Outline artikel blog yang SEO-optimized dan engaging',
                        category: 'blog_seo',
                        category_label: '≡ƒô¥ Blog & SEO',
                        platform: 'universal',
                        tone: 'educational',
                        format: '≡ƒô¥ STRUKTUR ARTIKEL SEO:\n\n1. SEO TITLE (50-60 char):\n"[KEYWORD UTAMA]: [BENEFIT/PROMISE]"\n\n2. META DESCRIPTION (150-160 char):\n"[RINGKASAN ARTIKEL + KEYWORD + CTA]"\n\n3. H1 HEADLINE:\n"[KEYWORD UTAMA] - [ANGLE UNIK]"\n\n4. INTRODUCTION (150 kata):\n- Hook (statistik/pertanyaan)\n- Problem statement\n- Preview artikel\n- Keyword naturally\n\n5. H2 SUBHEADINGS:\n- [KEYWORD] untuk Pemula\n- Cara [KEYWORD] yang Efektif\n- Tips [KEYWORD] Terbaik\n- Kesalahan [KEYWORD] yang Harus Dihindari\n\n6. CONCLUSION + CTA:\n- Ringkasan key points\n- Call to action\n- Related articles\n\n7. INTERNAL LINKS: 3-5 artikel terkait\n8. IMAGES: Alt text dengan keyword\n9. FAQ SECTION: 5-8 pertanyaan umum',
                        usage_count: 920,
                        is_favorite: false
                    }
                ];
                this.filteredTemplates = this.allTemplates;
            },

            filterTemplates() {
                let filtered = this.allTemplates;

                // Filter by category
                if (this.templateFilters.category) {
                    filtered = filtered.filter(template => 
                        template.category === this.templateFilters.category
                    );
                }

                // Filter by platform
                if (this.templateFilters.platform) {
                    filtered = filtered.filter(template => 
                        template.platform === this.templateFilters.platform || 
                        template.platform === 'universal'
                    );
                }

                // Filter by tone
                if (this.templateFilters.tone) {
                    filtered = filtered.filter(template => 
                        template.tone === this.templateFilters.tone || 
                        template.tone === 'universal'
                    );
                }

                // Filter by search
                if (this.templateFilters.search) {
                    const search = this.templateFilters.search.toLowerCase();
                    filtered = filtered.filter(template => 
                        template.title.toLowerCase().includes(search) ||
                        template.description.toLowerCase().includes(search) ||
                        template.category_label.toLowerCase().includes(search)
                    );
                }

                this.filteredTemplates = filtered;
            },

            selectTemplate(template) {
                this.selectedTemplate = template;
            },

            useTemplate(template) {
                // Close modal if open
                this.selectedTemplate = null;
                
                // Switch to text generator mode
                this.generatorType = 'text';
                this.mode = 'advanced';
                
                // Set form based on template
                this.form.category = template.category;
                this.updateSubcategories();
                
                // Set the brief with template format
                this.form.brief = `Gunakan template: ${template.title}\n\nFormat:\n${template.format}\n\n[Sesuaikan dengan produk/jasa Anda]`;
                
                // Set platform if specified
                if (template.platform && template.platform !== 'universal') {
                    this.form.platform = template.platform;
                }
                
                // Set tone if specified
                if (template.tone && template.tone !== 'universal') {
                    this.form.tone = template.tone;
                }

                // Scroll to form
                document.querySelector('form').scrollIntoView({ behavior: 'smooth' });
                
                // Show success message
                this.showNotification(`Γ£à Template "${template.title}" berhasil dimuat!`);
            },

            toggleFavorite(template) {
                template.is_favorite = !template.is_favorite;
                
                if (template.is_favorite) {
                    if (!this.favoriteTemplates.find(t => t.id === template.id)) {
                        this.favoriteTemplates.push(template);
                    }
                } else {
                    this.favoriteTemplates = this.favoriteTemplates.filter(t => t.id !== template.id);
                }
                
                // Save to localStorage
                localStorage.setItem('favoriteTemplates', JSON.stringify(this.favoriteTemplates));
            },

            loadFavoriteTemplates() {
                try {
                    const saved = localStorage.getItem('favoriteTemplates');
                    if (saved) {
                        this.favoriteTemplates = JSON.parse(saved);
                        // Update favorite status in allTemplates
                        this.allTemplates.forEach(template => {
                            template.is_favorite = this.favoriteTemplates.some(fav => fav.id === template.id);
                        });
                    }
                } catch (error) {
                }
            },

            // ≡ƒÄ» MULTI-PLATFORM OPTIMIZER FUNCTIONS
            async generateMultiPlatform() {
                if (this.multiPlatformForm.platforms.length < 2) {
                    alert('Pilih minimal 2 platform untuk optimasi');
                    return;
                }

                this.multiPlatformLoading = true;
                this.multiPlatformResults = null;

                try {
                    const response = await fetch('/api/ai/generate-multiplatform', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            content: this.multiPlatformForm.content,
                            business_type: this.multiPlatformForm.business_type,
                            target_audience: this.multiPlatformForm.target_audience,
                            goal: this.multiPlatformForm.goal,
                            platforms: this.multiPlatformForm.platforms
                        })
                    });

                    const data = await response.json();

                    if (handleFeatureLocked(data)) return;

                    if (data.success) {
                        this.multiPlatformResults = data.results;
                        setTimeout(() => {
                            document.querySelector('[x-show="multiPlatformResults"]').scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                    } else {
                        this.showNotification('Γ¥î ' + (data.message || 'Gagal generate multi-platform content'), 'error');
                    }

                } catch (error) {
                    this.showNotification('Γ¥î Terjadi kesalahan jaringan', 'error');
                } finally {
                    this.multiPlatformLoading = false;
                }
            },

            getPlatformEmoji(platform) {
                const emojis = {
                    'instagram': '≡ƒô╕',
                    'tiktok': '≡ƒÄ╡',
                    'facebook': '≡ƒæÑ',
                    'twitter': '≡ƒÉª',
                    'whatsapp': '≡ƒÆ¼',
                    'marketplace': '≡ƒ¢Æ'
                };
                return emojis[platform] || '≡ƒô▒';
            },

            getPlatformName(platform) {
                const names = {
                    'instagram': 'Instagram',
                    'tiktok': 'TikTok',
                    'facebook': 'Facebook',
                    'twitter': 'Twitter/X',
                    'whatsapp': 'WhatsApp Status',
                    'marketplace': 'Marketplace (Shopee/Tokped)'
                };
                return names[platform] || platform;
            },

            getPlatformSpecs(platform) {
                const specs = {
                    'instagram': 'Max 2200 chars ΓÇó 30 hashtags ΓÇó Visual focus',
                    'tiktok': 'Max 150 chars ΓÇó Trending sounds ΓÇó Short & catchy',
                    'facebook': 'Storytelling format ΓÇó Longer content ΓÇó Community focus',
                    'twitter': 'Max 280 chars ΓÇó Thread support ΓÇó News style',
                    'whatsapp': 'Short & punchy ΓÇó Personal tone ΓÇó Direct message',
                    'marketplace': 'SEO optimized ΓÇó Product focus ΓÇó Conversion oriented'
                };
                return specs[platform] || 'Platform optimized';
            },

            copyPlatformContent(platform, content) {
                copyToClipboard(content).then(() => {
                    // Show temporary success message
                    const button = event.target;
                    const originalText = button.textContent;
                    button.textContent = 'Γ£à Copied!';
                    button.classList.add('bg-green-600');
                    button.classList.remove('bg-blue-600');
                    
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-600');
                        button.classList.add('bg-blue-600');
                    }, 2000);
                }).catch(err => {
                    alert('Gagal copy content');
                });
            },

            copyAllPlatforms() {
                if (!this.multiPlatformResults) return;

                let allContent = '';
                Object.entries(this.multiPlatformResults).forEach(([platform, result]) => {
                    allContent += `=== ${this.getPlatformName(platform).toUpperCase()} ===\n`;
                    allContent += result.content + '\n\n';
                    if (result.hashtags && result.hashtags.length > 0) {
                        allContent += 'Hashtags: ' + result.hashtags.join(' ') + '\n\n';
                    }
                    allContent += '---\n\n';
                });

                copyToClipboard(allContent).then(() => {
                    this.showNotification('Γ£à Semua content berhasil di-copy!');
                }).catch(err => {
                    alert('Gagal copy content');
                });
            },

            exportMultiPlatform(format) {
                if (!this.multiPlatformResults) return;

                let content = '';
                const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');

                if (format === 'txt') {
                    Object.entries(this.multiPlatformResults).forEach(([platform, result]) => {
                        content += `=== ${this.getPlatformName(platform).toUpperCase()} ===\n`;
                        content += `Character Count: ${result.char_count}\n`;
                        content += `Content:\n${result.content}\n\n`;
                        if (result.hashtags && result.hashtags.length > 0) {
                            content += `Hashtags: ${result.hashtags.join(' ')}\n\n`;
                        }
                        if (result.optimization_notes && result.optimization_notes.length > 0) {
                            content += `Optimization Notes:\n`;
                            result.optimization_notes.forEach(note => {
                                content += `ΓÇó ${note}\n`;
                            });
                            content += '\n';
                        }
                        content += '---\n\n';
                    });

                    this.downloadFile(content, `multiplatform-content-${timestamp}.txt`, 'text/plain');

                } else if (format === 'csv') {
                    content = 'Platform,Character Count,Content,Hashtags,Optimization Notes\n';
                    Object.entries(this.multiPlatformResults).forEach(([platform, result]) => {
                        const hashtags = result.hashtags ? result.hashtags.join(' ') : '';
                        const notes = result.optimization_notes ? result.optimization_notes.join('; ') : '';
                        const contentEscaped = '"' + result.content.replace(/"/g, '""') + '"';
                        content += `${this.getPlatformName(platform)},${result.char_count},${contentEscaped},"${hashtags}","${notes}"\n`;
                    });

                    this.downloadFile(content, `multiplatform-content-${timestamp}.csv`, 'text/csv');
                }
            },

            downloadFile(content, filename, mimeType) {
                const blob = new Blob([content], { type: mimeType });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            },

            // Helper functions for UI styling
            getScoreColor(score) {
                if (score >= 90) return 'text-green-600';
                if (score >= 80) return 'text-blue-600';
                if (score >= 70) return 'text-yellow-600';
                if (score >= 60) return 'text-orange-600';
                return 'text-red-600';
            },

            getPriorityColor(priority) {
                switch (priority) {
                    case 'high': return 'bg-red-100 text-red-800';
                    case 'medium': return 'bg-yellow-100 text-yellow-800';
                    case 'low': return 'bg-green-100 text-green-800';
                    default: return 'bg-gray-100 text-gray-800';
                }
            },

            getConfidenceColor(confidence) {
                switch (confidence) {
                    case 'high': return 'bg-green-100 text-green-800';
                    case 'medium': return 'bg-yellow-100 text-yellow-800';
                    case 'low': return 'bg-red-100 text-red-800';
                    default: return 'bg-gray-100 text-gray-800';
                }
            },

            getConfidenceDot(confidence) {
                switch (confidence) {
                    case 'high': return 'bg-green-500';
                    case 'medium': return 'bg-yellow-500';
                    case 'low': return 'bg-red-500';
                    default: return 'bg-gray-500';
                }
            },

            // ΓÖ╗∩╕Å CONTENT REPURPOSING FUNCTIONS
            async generateRepurposedContent() {
                if (!this.repurposeForm.originalContent.trim()) {
                    alert('Mohon masukkan konten asli yang ingin di-repurpose');
                    return;
                }

                if (this.repurposeForm.selectedFormats.length === 0) {
                    alert('Pilih minimal 1 format repurposing');
                    return;
                }

                this.repurposeLoading = true;
                this.repurposeResults = [];

                try {
                    const response = await fetch('/api/ai/repurpose-content', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            original_content: this.repurposeForm.originalContent,
                            original_type: this.repurposeForm.originalType,
                            industry: this.repurposeForm.industry,
                            selected_formats: this.repurposeForm.selectedFormats,
                            include_hashtags: this.repurposeForm.includeHashtags,
                            include_cta: this.repurposeForm.includeCTA,
                            optimize_length: this.repurposeForm.optimizeLength,
                            generate_variations: this.repurposeForm.generateVariations
                        })
                    });

                    const data = await response.json();

                    if (handleFeatureLocked(data)) return;

                    if (data.success) {
                        this.repurposeResults = data.results;
                        setTimeout(() => {
                            const resultsElement = document.querySelector('[x-show="repurposeResults && repurposeResults.length > 0"]');
                            if (resultsElement) resultsElement.scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                    } else {
                        this.showNotification('Γ¥î ' + (data.message || 'Gagal repurpose content'), 'error');
                    }

                } catch (error) {
                    this.showNotification('Γ¥î Terjadi kesalahan jaringan', 'error');
                } finally {
                    this.repurposeLoading = false;
                }
            },

            copyRepurposedContent(content, btn) {
                copyToClipboard(content).then(() => {
                    if (btn) {
                        const originalText = btn.textContent;
                        btn.textContent = 'Γ£à Copied!';
                        setTimeout(() => { btn.textContent = originalText; }, 2000);
                    }
                }).catch(() => {
                    alert('Gagal copy content');
                });
            },

            copyAllRepurposed() {
                if (!this.repurposeResults || this.repurposeResults.length === 0) return;

                let allContent = '';
                this.repurposeResults.forEach(result => {
                    allContent += `=== ${result.format.toUpperCase().replace('_', ' ')} ===\n`;
                    allContent += result.content + '\n\n';
                    
                    if (result.variations && result.variations.length > 0) {
                        allContent += 'VARIATIONS:\n';
                        result.variations.forEach((variation, index) => {
                            allContent += `${index + 1}. ${variation}\n`;
                        });
                        allContent += '\n';
                    }
                    allContent += '---\n\n';
                });

                copyToClipboard(allContent).then(() => {
                    this.showNotification('Γ£à Semua repurposed content berhasil di-copy!');
                }).catch(() => {
                    alert('Gagal copy content');
                });
            },

            exportRepurposed(format) {
                if (!this.repurposeResults || this.repurposeResults.length === 0) return;

                let content = '';
                const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');

                if (format === 'txt') {
                    this.repurposeResults.forEach(result => {
                        content += `=== ${result.format.toUpperCase().replace('_', ' ')} ===\n`;
                        content += `Character Count: ${result.content.length}\n`;
                        content += `Content:\n${result.content}\n\n`;
                        
                        if (result.variations && result.variations.length > 0) {
                            content += 'VARIATIONS:\n';
                            result.variations.forEach((variation, index) => {
                                content += `${index + 1}. ${variation}\n`;
                            });
                            content += '\n';
                        }
                        content += '---\n\n';
                    });

                    const blob = new Blob([content], { type: 'text/plain' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `repurposed-content-${timestamp}.txt`;
                    a.click();
                    URL.revokeObjectURL(url);

                } else if (format === 'csv') {
                    content = 'Format,Content,Character Count,Variations\n';
                    this.repurposeResults.forEach(result => {
                        const variations = result.variations ? result.variations.join(' | ') : '';
                        content += `"${result.format}","${result.content.replace(/"/g, '""')}","${result.content.length}","${variations.replace(/"/g, '""')}"\n`;
                    });

                    const blob = new Blob([content], { type: 'text/csv' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `repurposed-content-${timestamp}.csv`;
                    a.click();
                    URL.revokeObjectURL(url);
                }
            },

            resetRepurpose() {
                this.repurposeForm = {
                    originalContent: '',
                    originalType: '',
                    industry: 'general',
                    selectedFormats: [],
                    includeHashtags: true,
                    includeCTA: true,
                    optimizeLength: true,
                    generateVariations: false
                };
                this.repurposeResults = [];
            },

            // ≡ƒöö TREND ALERT FUNCTIONS
            selectTrend(trend) {
                this.selectedTrend = trend;
                this.trendContentTypes = [];
                this.trendBusinessContext = '';
            },

            async refreshTrends() {
                this.trendsLoading = true;
                
                try {
                    // Simulate API call to get latest trends
                    await new Promise(resolve => setTimeout(resolve, 1500));
                    
                    // Update trends with fresh data (in real implementation, this would be from API)
                    this.dailyTrends = [
                        {
                            id: Date.now(),
                            title: 'New Viral Challenge #IndonesiaSehat',
                            description: 'Challenge baru untuk promosi kesehatan mental dan fisik',
                            category: 'Health',
                            popularity: '3.1M',
                            timeAgo: 'Baru saja',
                            icon: '≡ƒÆ¬',
                            hashtags: ['#IndonesiaSehat', '#MentalHealth', '#Wellness', '#Viral']
                        },
                        ...this.dailyTrends.slice(0, 3)
                    ];
                    
                    alert('Γ£à Trends berhasil di-refresh!');
                } catch (error) {
                    alert('Γ¥î Gagal refresh trends. Silakan coba lagi.');
                } finally {
                    this.trendsLoading = false;
                }
            },

            async generateTrendContent() {
                if (!this.selectedTrend || this.trendContentTypes.length === 0 || !this.trendBusinessContext) {
                    alert('Mohon lengkapi semua field yang diperlukan');
                    return;
                }

                this.trendLoading = true;

                try {
                    const response = await fetch('/api/ai/generate-trend-content', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            trend: this.selectedTrend,
                            content_types: this.trendContentTypes,
                            business_context: this.trendBusinessContext
                        })
                    });

                    const data = await response.json();

                    if (handleFeatureLocked(data)) return;

                    if (data.success) {
                        this.trendResults = data.results;
                        this.showNotification('Γ£à Konten trend berhasil di-generate!', 'success');
                    } else {
                        throw new Error(data.error || 'Failed to generate trend content');
                    }

                } catch (error) {
                    // Fallback to basic generation
                    this.generateBasicTrendContent();
                } finally {
                    this.trendLoading = false;
                }
            },

            generateBasicTrendContent() {
                const contentTypeMap = {
                    caption: { icon: '≡ƒô▒', title: 'Instagram/Facebook Caption' },
                    story: { icon: '≡ƒô╕', title: 'Instagram Story' },
                    tiktok: { icon: '≡ƒÄ╡', title: 'TikTok Script' },
                    thread: { icon: '≡ƒº╡', title: 'Twitter Thread' },
                    blog: { icon: '≡ƒô¥', title: 'Blog Post' },
                    email: { icon: '≡ƒôº', title: 'Email Marketing' },
                    ads: { icon: '≡ƒÆ░', title: 'Facebook/Instagram Ads' },
                    whatsapp: { icon: '≡ƒÆ¼', title: 'WhatsApp Blast' }
                };

                this.trendResults = this.trendContentTypes.map(type => {
                    const typeInfo = contentTypeMap[type];
                    return {
                        type: type,
                        icon: typeInfo.icon,
                        title: typeInfo.title,
                        content: this.generateTrendContentByType(type),
                        hashtags: this.selectedTrend.hashtags || []
                    };
                });

                alert('Γ£à Konten trend berhasil di-generate (mode basic)!');
            },

            generateTrendContentByType(type) {
                const trend = this.selectedTrend;
                const business = this.trendBusinessContext;
                
                const templates = {
                    caption: `≡ƒöÑ IKUTAN TREND ${trend.title.toUpperCase()}! ≡ƒöÑ

${trend.description}

Sebagai ${business}, kita juga mau ikutan nih! 

Γ£¿ Gimana kalau kita bikin versi kita sendiri?
≡ƒÆí Yuk share ide kalian di comment!

#TrendAlert #${business.replace(/\s+/g, '')} ${trend.hashtags?.join(' ') || ''}`,

                    story: `≡ƒô╕ STORY SERIES: ${trend.title}

Slide 1: "Lagi viral nih!"
Slide 2: "${trend.description}"
Slide 3: "Versi ${business} gimana ya?"
Slide 4: "Swipe up untuk lihat!"
Slide 5: CTA - "Follow untuk update trend terbaru!"`,

                    tiktok: `≡ƒÄ╡ TIKTOK SCRIPT: ${trend.title}

HOOK (0-3 detik):
"Eh tau gak sih trend ${trend.title} yang lagi viral?"

CONTENT (3-15 detik):
"Jadi ceritanya ${trend.description}. Nah sebagai ${business}, kita juga bisa loh ikutan!"

TRANSITION (15-20 detik):
"Tapi versi kita gimana ya?"

REVEAL (20-25 detik):
[Tunjukkan produk/jasa dengan twist trend]

CTA (25-30 detik):
"Kalian mau coba juga? Comment 'TREND' ya!"`,

                    thread: `≡ƒº╡ TWITTER THREAD: ${trend.title}

1/5 ≡ƒöÑ Lagi viral banget nih ${trend.title}! 

2/5 ≡ƒôê ${trend.description}

3/5 ≡ƒÆí Sebagai ${business}, kita lihat peluang buat ikutan dengan cara yang unik

4/5 Γ£¿ Ide kita: [sesuaikan dengan bisnis]

5/5 ≡ƒÜÇ Kalian ada ide lain? Reply thread ini! ${trend.hashtags?.join(' ') || ''}`,

                    blog: `# Memanfaatkan Trend ${trend.title} untuk Bisnis ${business}

## Apa itu ${trend.title}?
${trend.description}

## Mengapa Trend Ini Penting?
- Engagement tinggi di media sosial
- Reach organik yang lebih luas
- Kesempatan viral marketing

## Cara ${business} Memanfaatkan Trend Ini:
1. Adaptasi sesuai brand voice
2. Buat konten yang relevan
3. Gunakan hashtag yang tepat
4. Timing yang pas

## Kesimpulan
Trend ${trend.title} memberikan peluang besar untuk ${business} meningkatkan visibility dan engagement.`,

                    email: `Subject: ≡ƒöÑ Jangan Ketinggalan Trend ${trend.title}!

Halo [Nama],

Pasti udah tau dong trend ${trend.title} yang lagi viral banget?

${trend.description}

Nah, sebagai ${business}, kita gak mau ketinggalan dong! 

Makanya kita bikin [produk/promo spesial] yang terinspirasi dari trend ini.

[CTA Button: Lihat Sekarang]

Jangan sampai kehabisan ya!

Salam,
Tim ${business}`,

                    ads: `≡ƒöÑ TRENDING ALERT: ${trend.title}

${trend.description}

Sebagai ${business} terdepan, kita ikutan trend ini dengan cara yang unik!

Γ£à [Benefit 1]
Γ£à [Benefit 2] 
Γ£à [Benefit 3]

Jangan sampai ketinggalan!

[CTA: Pesan Sekarang]

#TrendAlert ${trend.hashtags?.join(' ') || ''}`,

                    whatsapp: `≡ƒöÑ *TREND ALERT!*

Hai! Pasti udah tau kan trend *${trend.title}* yang lagi viral?

${trend.description}

Nah, sebagai ${business}, kita juga mau ikutan nih! 

Gimana kalau kita bikin versi kita sendiri? 

Yuk chat balik kalau tertarik! ≡ƒÿè

${trend.hashtags?.join(' ') || ''}`
                };

                return templates[type] || `Konten ${type} untuk trend ${trend.title} - ${business}`;
            },

            copyTrendContent(content) {
                copyToClipboard(content).then(() => {
                    alert('Γ£à Konten berhasil di-copy!');
                }).catch(() => {
                    alert('Γ¥î Gagal copy konten');
                });
            },

            copyAllTrendContent() {
                const allContent = this.trendResults.map(result => 
                    `${result.title}:\n${result.content}\n\nHashtags: ${result.hashtags.join(' ')}\n\n---\n`
                ).join('\n');
                
                copyToClipboard(allContent).then(() => {
                    alert('Γ£à Semua konten berhasil di-copy!');
                }).catch(() => {
                    alert('Γ¥î Gagal copy konten');
                });
            },

            exportTrendContent(format) {
                if (this.trendResults.length === 0) {
                    alert('Belum ada konten untuk di-export');
                    return;
                }

                let content = '';
                let filename = `trend-content-${Date.now()}`;

                if (format === 'txt') {
                    content = this.trendResults.map(result => 
                        `${result.title}\n${'='.repeat(result.title.length)}\n\n${result.content}\n\nHashtags: ${result.hashtags.join(' ')}\n\n`
                    ).join('\n');
                    filename += '.txt';
                }

                const blob = new Blob([content], { type: 'text/plain' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            },

            resetTrends() {
                this.selectedTrend = null;
                this.trendContentTypes = [];
                this.trendBusinessContext = '';
                this.trendResults = [];
            },

            // ≡ƒô¥ CAPTION OPTIMIZER FUNCTIONS
            
            // Grammar Checker
            showGrammarChecker: false,
            grammarLoading: false,
            grammarResult: null,
            grammarFixing: false,

            openGrammarChecker() {
                this.showGrammarChecker = true;
                this.checkGrammar();
            },

            async checkGrammar() {
                if (!this.result) {
                    alert('Tidak ada caption untuk dicheck');
                    return;
                }

                this.grammarLoading = true;
                this.grammarResult = null;

                try {
                    const response = await fetch('/api/optimizer/grammar/check', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            text: this.result,
                            language: 'id'
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.grammarResult = data.data;
                    } else {
                        alert('Error: ' + (data.message || 'Gagal check grammar'));
                    }

                } catch (error) {
                    // silent fail
                } finally {
                    this.grammarLoading = false;
                }
            },

            async quickGrammarFix() {
                if (!this.result) return;

                this.grammarFixing = true;

                try {
                    const response = await fetch('/api/optimizer/grammar/quick-fix', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            text: this.result,
                            language: 'id'
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.result = data.data.corrected_text;
                        this.showGrammarChecker = false;
                    }

                } catch (error) {
                    // silent fail
                } finally {
                    this.grammarFixing = false;
                }
            },

            useCorrectedText() {
                if (this.grammarResult?.corrected_text) {
                    this.result = this.grammarResult.corrected_text;
                    this.showGrammarChecker = false;
                }
            },

            // Caption Shortener
            showCaptionShortener: false,
            shortenerLoading: false,
            shortenerResult: null,
            shortenerTargetLength: 150,
            shortenerPreserveHashtags: true,
            shortenerPreserveEmojis: true,
            shortenerPreserveCTA: true,

            openCaptionShortener() {
                if (this.result) {
                    this.shortenerTargetLength = Math.floor(this.result.length * 0.7); // Default 70% of current
                }
                this.showCaptionShortener = true;
            },

            async shortenCaption() {
                if (!this.result) {
                    alert('Tidak ada caption untuk diperpendek');
                    return;
                }

                this.shortenerLoading = true;
                this.shortenerResult = null;

                try {
                    const response = await fetch('/api/optimizer/length/shorten', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            caption: this.result,
                            target_length: this.shortenerTargetLength,
                            platform: this.form.platform || 'instagram',
                            preserve_hashtags: this.shortenerPreserveHashtags,
                            preserve_emojis: this.shortenerPreserveEmojis,
                            preserve_cta: this.shortenerPreserveCTA
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.shortenerResult = data.data;
                    } else {
                        alert('Error: ' + (data.message || 'Gagal shorten caption'));
                    }

                } catch (error) {
                    alert('Terjadi kesalahan saat shorten caption');
                } finally {
                    this.shortenerLoading = false;
                }
            },

            useShortened(shortenedText) {
                this.result = shortenedText;
                alert('Γ£à Shortened caption berhasil digunakan!');
                this.showCaptionShortener = false;
            },

            // Caption Expander
            showCaptionExpander: false,
            expanderLoading: false,
            expanderResult: null,
            expanderTargetLength: 300,
            expanderType: 'detailed',
            expanderAddHashtags: true,
            expanderAddEmojis: true,

            openCaptionExpander() {
                if (this.result) {
                    this.expanderTargetLength = Math.floor(this.result.length * 1.5); // Default 150% of current
                }
                this.showCaptionExpander = true;
            },

            async expandCaption() {
                if (!this.result) {
                    alert('Tidak ada caption untuk diperpanjang');
                    return;
                }

                this.expanderLoading = true;
                this.expanderResult = null;

                try {
                    const response = await fetch('/api/optimizer/length/expand', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            caption: this.result,
                            target_length: this.expanderTargetLength,
                            platform: this.form.platform || 'instagram',
                            expansion_type: this.expanderType,
                            industry: this.form.category || 'general',
                            add_hashtags: this.expanderAddHashtags,
                            add_emojis: this.expanderAddEmojis
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.expanderResult = data.data;
                    } else {
                        alert('Error: ' + (data.message || 'Gagal expand caption'));
                    }

                } catch (error) {
                    alert('Terjadi kesalahan saat expand caption');
                } finally {
                    this.expanderLoading = false;
                }
            },

            useExpanded(expandedText) {
                this.result = expandedText;
                alert('Γ£à Expanded caption berhasil digunakan!');
                this.showCaptionExpander = false;
            },

            // ΓöÇΓöÇΓöÇ Google Ads Functions ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async generateGoogleAds() {
                this.adsLoading = true;
                this.adsResult = null;
                try {
                    const response = await fetch('/api/ai/generate-google-ads', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.adsForm)
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) {
                        this.adsResult = data.campaign;
                        this.adsTab = 'copy';
                        if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining;
                    } else if (data.quota_error) {
                        this.showNotification('ΓÜí ' + data.message, 'error');
                    } else {
                        this.showNotification('Γ¥î ' + (data.message || 'Gagal generate kampanye'), 'error');
                    }
                } catch (e) {
                    this.showNotification('Γ¥î Terjadi kesalahan jaringan', 'error');
                } finally {
                    this.adsLoading = false;
                }
            },

            copyText(text) {
                if (!text) return;
                copyToClipboard(text).then(() => {
                    this.adsCopied = true;
                    setTimeout(() => this.adsCopied = false, 2000);
                });
            },

            copyAdGroup(group) {
                if (!group) return;
                let out = `=== ${group.name} ===\n${group.theme}\n\n`;
                (group.ads || []).forEach(ad => {
                    out += `HEADLINES:\n${(ad.headlines || []).join('\n')}\n\nDESCRIPTIONS:\n${(ad.descriptions || []).join('\n')}\n\n`;
                });
                this.copyText(out);
            },

            copyAllKeywords(group) {
                if (!group?.keywords) return;
                const kw = group.keywords;
                const out = [
                    'EXACT MATCH:\n' + (kw.exact_match || []).join('\n'),
                    'PHRASE MATCH:\n' + (kw.phrase_match || []).join('\n'),
                    'BROAD MATCH:\n' + (kw.broad_match_modifier || []).join('\n'),
                    'NEGATIVE:\n' + (kw.negative_keywords || []).join('\n'),
                ].join('\n\n');
                this.copyText(out);
            },

            copyAllSitelinks() {
                const sitelinks = this.adsResult?.ad_extensions?.sitelinks || [];
                const out = sitelinks.map(sl =>
                    `Title: ${sl.title}\nDesc1: ${sl.description1}\nDesc2: ${sl.description2}\nURL: ${sl.url}`
                ).join('\n\n');
                this.copyText(out);
            },

            copySitelink(sl) {
                this.copyText(`Title: ${sl.title}\nDesc1: ${sl.description1}\nDesc2: ${sl.description2}\nURL: ${sl.url}`);
            },

            copyFullCampaign() {
                if (!this.adsResult) return;
                const r = this.adsResult;
                let out = `============================\nGOOGLE ADS CAMPAIGN\n============================\n`;
                out += `Nama: ${r.campaign_summary?.name}\nTipe: ${r.campaign_summary?.type}\nTujuan: ${r.campaign_summary?.goal}\n`;
                out += `Budget Harian: Rp ${(r.campaign_summary?.daily_budget_idr || 0).toLocaleString('id-ID')}\n`;
                out += `Campaign Score: ${r.campaign_summary?.campaign_score}/100\n\n`;

                (r.ad_groups || []).forEach((g, i) => {
                    out += `\n--- AD GROUP ${i+1}: ${g.name} ---\n`;
                    (g.ads || []).forEach(ad => {
                        out += `\nHEADLINES:\n${(ad.headlines || []).join('\n')}\n`;
                        out += `\nDESCRIPTIONS:\n${(ad.descriptions || []).join('\n')}\n`;
                    });
                    const kw = g.keywords || {};
                    out += `\nEXACT MATCH:\n${(kw.exact_match || []).join('\n')}\n`;
                    out += `\nPHRASE MATCH:\n${(kw.phrase_match || []).join('\n')}\n`;
                    out += `\nBROAD MATCH:\n${(kw.broad_match_modifier || []).join('\n')}\n`;
                    out += `\nNEGATIVE:\n${(kw.negative_keywords || []).join('\n')}\n`;
                });

                const ext = r.ad_extensions || {};
                if (ext.sitelinks?.length) {
                    out += `\n--- SITELINKS ---\n`;
                    ext.sitelinks.forEach(sl => out += `${sl.title} | ${sl.description1} | ${sl.url}\n`);
                }
                if (ext.callouts?.length) {
                    out += `\n--- CALLOUTS ---\n${ext.callouts.join('\n')}\n`;
                }
                this.copyText(out);
            },

            // ΓöÇΓöÇΓöÇ Magic Promo Link Functions ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async generatePromoLink() {
                if (!this.promoForm.product_name || !this.promoForm.product_desc) return;
                this.promoLoading = true;
                this.promoResult = null;
                this.promoError = null;
                this.promoCopied = {};
                try {
                    const response = await fetch('/api/ai/generate-promo-link', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.promoForm)
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) {
                        this.promoResult = data.data;
                        if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining;
                    } else {
                        this.promoError = data.message || 'Terjadi kesalahan';
                    }
                } catch (e) {
                    this.promoError = 'Gagal terhubung ke server';
                } finally {
                    this.promoLoading = false;
                }
            },

            promoCopyCaption(caption, idx) {
                const text = caption.caption + '\n\n' + (caption.hashtags || []).join(' ');
                copyToClipboard(text).then(() => {
                    this.promoCopied = { ...this.promoCopied, [idx]: true };
                    setTimeout(() => {
                        this.promoCopied = { ...this.promoCopied, [idx]: false };
                    }, 2000);
                });
            },

            promoShareWA(caption) {
                const text = encodeURIComponent(caption.caption + '\n\n' + (caption.hashtags || []).join(' '));
                const waNum = this.promoForm.wa_number ? this.promoForm.wa_number.replace(/\D/g, '') : '';
                const url = waNum
                    ? `https://wa.me/${waNum}?text=${text}`
                    : `https://wa.me/?text=${text}`;
                window.open(url, '_blank');
            },

            promoShareX(caption) {
                // X (Twitter) has 280 char limit ΓÇö use hook + CTA + hashtags
                const tweet = encodeURIComponent(
                    (caption.hook || '') + '\n\n' + (caption.cta || '') + '\n\n' +
                    (caption.hashtags || []).slice(0, 3).join(' ')
                );
                window.open(`https://twitter.com/intent/tweet?text=${tweet}`, '_blank');
            },

            promoShareLinkedIn(caption) {
                const text = encodeURIComponent(caption.caption);
                window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.location.href)}&summary=${text}`, '_blank');
            },

            // ΓöÇΓöÇΓöÇ Product Explainer Functions ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async generateExplainer() {
                if (!this.explainerForm.product_name || !this.explainerForm.product_desc || !this.explainerForm.wa_number) return;
                this.explainerLoading = true;
                this.explainerResult = null;
                this.explainerError = null;
                this.explainerCopied = {};
                this.explainerLinkCopied = {};
                try {
                    const response = await fetch('/api/ai/generate-product-explainer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.explainerForm)
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) {
                        this.explainerResult = data.data;
                        if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining;
                    } else {
                        this.explainerError = data.message || 'Terjadi kesalahan';
                    }
                } catch (e) {
                    this.explainerError = 'Gagal terhubung ke server';
                } finally {
                    this.explainerLoading = false;
                }
            },

            explainerBuildLink(message) {
                const waNum = (this.explainerResult?.wa_number || this.explainerForm.wa_number || '').replace(/\D/g, '');
                if (!waNum) return 'Masukkan nomor WA untuk generate link';
                return `https://wa.me/${waNum}?text=${encodeURIComponent(message)}`;
            },

            explainerOpenWA(message) {
                window.open(this.explainerBuildLink(message), '_blank');
            },

            explainerCopyMsg(message, idx) {
                copyToClipboard(message).then(() => {
                    this.explainerCopied = { ...this.explainerCopied, [idx]: true };
                    setTimeout(() => { this.explainerCopied = { ...this.explainerCopied, [idx]: false }; }, 2000);
                });
            },

            explainerCopyLink(message, idx) {
                const link = this.explainerBuildLink(message);
                copyToClipboard(link).then(() => {
                    this.explainerLinkCopied = { ...this.explainerLinkCopied, [idx]: true };
                    setTimeout(() => { this.explainerLinkCopied = { ...this.explainerLinkCopied, [idx]: false }; }, 2000);
                });
            },

            // ΓöÇΓöÇΓöÇ SEO Metadata ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async generateSeoMetadata() {
                this.seoLoading = true; this.seoResult = null; this.seoError = null;
                try {
                    const response = await fetch('/api/ai/generate-seo-metadata', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.seoForm)
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) { this.seoResult = data.data; if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining; }
                    else this.seoError = data.message || 'Terjadi kesalahan';
                } catch (e) { this.seoError = 'Gagal terhubung ke server'; }
                finally { this.seoLoading = false; }
            },
            seoCopy(text, key) {
                copyToClipboard(text).then(() => {
                    this.seoCopied = { ...this.seoCopied, [key]: true };
                    setTimeout(() => { this.seoCopied = { ...this.seoCopied, [key]: false }; }, 2000);
                });
            },
            seoCopyAll() {
                const r = this.seoResult || {};
                const text = [
                    'Meta Title: ' + (r.meta_title || ''),
                    'Meta Description: ' + (r.meta_description || ''),
                    'Focus Keyword: ' + (r.focus_keyword || ''),
                    'OG Title: ' + (r.og_title || ''),
                    'Slug: ' + (r.slug_suggestion || ''),
                ].join('\n');
                copyToClipboard(text).then(() => {
                    this.seoCopied = { ...this.seoCopied, all: true };
                    setTimeout(() => { this.seoCopied = { ...this.seoCopied, all: false }; }, 2000);
                });
            },

            // ΓöÇΓöÇΓöÇ Smart Comparison ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async generateComparison() {
                this.compLoading = true; this.compResult = null; this.compError = null;
                try {
                    const response = await fetch('/api/ai/generate-comparison', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.compForm)
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) { this.compResult = data.data; if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining; }
                    else this.compError = data.message || 'Terjadi kesalahan';
                } catch (e) { this.compError = 'Gagal terhubung ke server'; }
                finally { this.compLoading = false; }
            },
            compCopyShare() {
                const text = this.compResult?.share_message || this.compResult?.share_text || '';
                copyToClipboard(text).then(() => {
                    this.compCopied = true;
                    setTimeout(() => { this.compCopied = false; }, 2000);
                });
            },
            compShareWA() {
                const text = encodeURIComponent(this.compResult?.share_message || this.compResult?.share_text || '');
                window.open(`https://wa.me/?text=${text}`, '_blank');
            },

            // ΓöÇΓöÇΓöÇ FAQ Generator ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async generateFaq() {
                this.faqLoading = true; this.faqResult = null; this.faqError = null;
                try {
                    const response = await fetch('/api/ai/generate-faq', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.faqForm)
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) { this.faqResult = data.data; if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining; }
                    else this.faqError = data.message || 'Terjadi kesalahan';
                } catch (e) { this.faqError = 'Gagal terhubung ke server'; }
                finally { this.faqLoading = false; }
            },
            faqCopyItem(text, idx) {
                copyToClipboard(text).then(() => {
                    this.faqCopied = { ...this.faqCopied, [idx]: true };
                    setTimeout(() => { this.faqCopied = { ...this.faqCopied, [idx]: false }; }, 2000);
                });
            },
            faqCopySchema() {
                const schema = this.faqResult?.schema_faq || JSON.stringify(this.faqResult?.schema_markup || {}, null, 2);
                copyToClipboard(schema).then(() => {
                    this.faqSchemaCopied = true;
                    setTimeout(() => { this.faqSchemaCopied = false; }, 2000);
                });
            },
            faqCopyAll() {
                const faqs = (this.faqResult?.faqs || []).map((f, i) => `Q${i+1}. ${f.question}\nA: ${f.answer}`).join('\n\n');
                copyToClipboard(faqs).then(() => {
                    this.faqAllCopied = true;
                    setTimeout(() => { this.faqAllCopied = false; }, 2000);
                });
            },

            // ΓöÇΓöÇΓöÇ Reels Hook ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async generateReelsHook() {
                this.reelsLoading = true; this.reelsResult = null; this.reelsError = null;
                try {
                    const response = await fetch('/api/ai/generate-reels-hook', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.reelsForm)
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) { this.reelsResult = data.data; if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining; }
                    else this.reelsError = data.message || 'Terjadi kesalahan';
                } catch (e) { this.reelsError = 'Gagal terhubung ke server'; }
                finally { this.reelsLoading = false; }
            },
            reelsCopyHook(text, idx) {
                copyToClipboard(text).then(() => {
                    this.reelsCopied = { ...this.reelsCopied, [idx]: true };
                    setTimeout(() => { this.reelsCopied = { ...this.reelsCopied, [idx]: false }; }, 2000);
                });
            },
            reelsCopyScript(script, idx) {
                const text = (script && script.script) ? script.script : (this.reelsResult?.full_script || '');
                copyToClipboard(text).then(() => {
                    this.reelsScriptCopied = typeof idx !== 'undefined' ? { ...this.reelsScriptCopied, [idx]: true } : true;
                    setTimeout(() => {
                        this.reelsScriptCopied = typeof idx !== 'undefined' ? { ...this.reelsScriptCopied, [idx]: false } : false;
                    }, 2000);
                });
            },
            reelsCopyBio() {
                const bio = this.reelsResult?.bio_cta || this.reelsResult?.bio_link_text || '';
                copyToClipboard(bio).then(() => {
                    this.reelsBioCopied = true;
                    setTimeout(() => { this.reelsBioCopied = false; }, 2000);
                });
            },

            // ΓöÇΓöÇΓöÇ Quality Badge ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async generateQualityBadge() {
                this.badgeLoading = true; this.badgeResult = null; this.badgeError = null;
                try {
                    const response = await fetch('/api/ai/generate-quality-badge', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.badgeForm)
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) { this.badgeResult = data.data; if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining; }
                    else this.badgeError = data.message || 'Terjadi kesalahan';
                } catch (e) { this.badgeError = 'Gagal terhubung ke server'; }
                finally { this.badgeLoading = false; }
            },
            badgeCopyText() {
                const text = this.badgeResult?.badge_text || '';
                copyToClipboard(text).then(() => {
                    this.badgeCopied = true;
                    setTimeout(() => { this.badgeCopied = false; }, 2000);
                });
            },

            // ΓöÇΓöÇΓöÇ Discount Campaign ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async generateDiscountCampaign() {
                this.discLoading = true; this.discResult = null; this.discError = null;
                try {
                    const response = await fetch('/api/ai/generate-discount-campaign', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.discForm)
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) { this.discResult = data.data; if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining; }
                    else this.discError = data.message || 'Terjadi kesalahan';
                } catch (e) { this.discError = 'Gagal terhubung ke server'; }
                finally { this.discLoading = false; }
            },
            discCopy(text, idx) {
                copyToClipboard(text).then(() => {
                    this.discCopied = { ...this.discCopied, [idx]: true };
                    setTimeout(() => { this.discCopied = { ...this.discCopied, [idx]: false }; }, 2000);
                });
            },
            discCopyWALink() {
                const link = this.discResult?.wa_broadcast_link || '';
                copyToClipboard(link).then(() => {
                    this.discWACopied = true;
                    setTimeout(() => { this.discWACopied = false; }, 2000);
                });
            },

            // ΓöÇΓöÇΓöÇ Trend Tags ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async generateTrendTags() {
                this.tagsLoading = true; this.tagsResult = null; this.tagsError = null;
                try {
                    const response = await fetch('/api/ai/generate-trend-tags', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.tagsForm)
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) { this.tagsResult = data.data; if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining; }
                    else this.tagsError = data.message || 'Terjadi kesalahan';
                } catch (e) { this.tagsError = 'Gagal terhubung ke server'; }
                finally { this.tagsLoading = false; }
            },
            tagsCopyAll() {
                const tags = (this.tagsResult?.trending_tags || []).map(t => t.tag).join(', ');
                copyToClipboard(tags).then(() => {
                    this.tagsCopied = true;
                    setTimeout(() => { this.tagsCopied = false; }, 2000);
                });
            },

            // ΓöÇΓöÇΓöÇ Lead Magnet ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async generateLeadMagnet() {
                this.magnetLoading = true; this.magnetResult = null; this.magnetError = null;
                try {
                    const response = await fetch('/api/ai/generate-lead-magnet', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.magnetForm)
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) { this.magnetResult = data.data; if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining; }
                    else this.magnetError = data.message || 'Terjadi kesalahan';
                } catch (e) { this.magnetError = 'Gagal terhubung ke server'; }
                finally { this.magnetLoading = false; }
            },
            magnetCopyLanding() {
                const lc = this.magnetResult?.landing_copy || {};
                const text = [lc.headline, lc.subheadline, ...(lc.bullets || []), lc.cta_button].filter(Boolean).join('\n');
                copyToClipboard(text).then(() => {
                    this.magnetCopied = true;
                    setTimeout(() => { this.magnetCopied = false; }, 2000);
                });
            },
            magnetCopyWA() {
                const link = this.magnetResult?.wa_link || '';
                copyToClipboard(link).then(() => {
                    this.magnetWACopied = true;
                    setTimeout(() => { this.magnetWACopied = false; }, 2000);
                });
            },

            // ΓöÇΓöÇΓöÇ Financial Analysis ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async analyzeFinancial() {
                if (this.financialImages.length === 0 && this.financialDocs.length === 0) {
                    this.showNotification('Γ¥î Upload minimal satu file (chart atau PDF)', 'error');
                    return;
                }
                this.financialLoading = true;
                this.financialResult = null;
                this.financialError = null;

                const formData = new FormData();
                formData.append('analysis_type', this.financialForm.analysis_type);
                formData.append('context', this.financialForm.context);
                this.financialImages.forEach((f, i) => formData.append(`images[${i}]`, f));
                this.financialDocs.forEach((f, i) => formData.append(`documents[${i}]`, f));

                try {
                    const response = await fetch('/api/ai/analyze-financial', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData,
                    });
                    const data = await response.json();
                    if (handleFeatureLocked(data)) return;
                    if (data.success) {
                        this.financialResult = data.analysis;
                        if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining;
                        this.showNotification('Γ£à Analisis selesai!', 'success');
                    } else {
                        this.financialError = data.message || 'Terjadi kesalahan';
                        this.showNotification('Γ¥î ' + this.financialError, 'error');
                    }
                } catch (e) {
                    this.financialError = 'Terjadi kesalahan jaringan';
                    this.showNotification('Γ¥î Terjadi kesalahan jaringan', 'error');
                } finally {
                    this.financialLoading = false;
                }
            },

            financialAddImages(event) {
                const files = Array.from(event.target.files);
                this.financialImages = [...this.financialImages, ...files].slice(0, 5);
            },

            financialAddDocs(event) {
                const files = Array.from(event.target.files);
                this.financialDocs = [...this.financialDocs, ...files].slice(0, 5);
            },

            financialRemoveImage(idx) { this.financialImages.splice(idx, 1); },
            financialRemoveDoc(idx)   { this.financialDocs.splice(idx, 1); },

            financialCopyResult() {
                copyToClipboard(this.financialResult || '').then(() => {
                    this.financialCopied = true;
                    setTimeout(() => { this.financialCopied = false; }, 2000);
                });
            },

            // ΓöÇΓöÇ Ebook Analysis ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            ebookAddDocs(event) {
                const files = Array.from(event.target.files);
                this.ebookDocs = [...this.ebookDocs, ...files].slice(0, 3);
            },
            ebookRemoveDoc(idx) { this.ebookDocs.splice(idx, 1); },

            async analyzeEbook() {
                if (this.ebookDocs.length === 0) {
                    this.showNotification('ΓÜá∩╕Å Upload minimal satu file PDF', 'error');
                    return;
                }
                this.ebookLoading = true;
                this.ebookResult = null;
                this.ebookError = null;

                const formData = new FormData();
                formData.append('analysis_type', this.ebookForm.analysis_type);
                formData.append('context', this.ebookForm.context || '');
                this.ebookDocs.forEach(f => formData.append('documents[]', f));

                try {
                    const resp = await fetch('/api/ai/analyze-ebook', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData,
                    });
                    const data = await resp.json();
                    if (!resp.ok) {
                        if (handleFeatureLocked(data)) return;
                        this.ebookError = data.message || 'Terjadi kesalahan';
                        this.showNotification('Γ¥î ' + this.ebookError, 'error');
                    } else if (data.success) {
                        this.ebookResult = data.analysis;
                        if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining;
                        this.showNotification('Γ£à Analisis ebook selesai!', 'success');
                    } else {
                        this.ebookError = data.message || 'Terjadi kesalahan';
                        this.showNotification('Γ¥î ' + this.ebookError, 'error');
                    }
                } catch (e) {
                    this.ebookError = 'Terjadi kesalahan jaringan';
                    this.showNotification('Γ¥î Terjadi kesalahan jaringan', 'error');
                } finally {
                    this.ebookLoading = false;
                }
            },

            ebookCopyResult() {
                copyToClipboard(this.ebookResult || '').then(() => {
                    this.ebookCopied = true;
                    setTimeout(() => { this.ebookCopied = false; }, 2000);
                });
            },

            // ΓöÇΓöÇ Reader Trend Analysis ΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇΓöÇ
            async analyzeReaderTrend() {
                this.readerTrendLoading = true;
                this.readerTrendResult = null;
                this.readerTrendError = null;

                try {
                    const resp = await fetch('/api/ai/analyze-reader-trend', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            genre: this.readerTrendForm.genre,
                            platform: this.readerTrendForm.platform,
                            context: this.readerTrendForm.context,
                        }),
                    });
                    const data = await resp.json();
                    if (!resp.ok) {
                        if (handleFeatureLocked(data)) return;
                        this.readerTrendError = data.message || 'Terjadi kesalahan';
                        this.showNotification('Γ¥î ' + this.readerTrendError, 'error');
                    } else if (data.success) {
                        this.readerTrendResult = data.analysis;
                        if (data.quota_remaining !== undefined) this.quotaRemaining = data.quota_remaining;
                        this.showNotification('Γ£à Analisis tren selesai!', 'success');
                    } else {
                        this.readerTrendError = data.message || 'Terjadi kesalahan';
                        this.showNotification('Γ¥î ' + this.readerTrendError, 'error');
                    }
                } catch (e) {
                    this.readerTrendError = 'Terjadi kesalahan jaringan';
                    this.showNotification('Γ¥î Terjadi kesalahan jaringan', 'error');
                } finally {
                    this.readerTrendLoading = false;
                }
            },

            readerTrendCopyResult() {
                copyToClipboard(this.readerTrendResult || '').then(() => {
                    this.readerTrendCopied = true;
                    setTimeout(() => { this.readerTrendCopied = false; }, 2000);
                });
            },
        }
    }
    
    // Initialize ML features on page load
    document.addEventListener('alpine:init', () => {
        Alpine.data('aiGenerator', aiGenerator);
    });
    
    // Auto-init ML after component is ready
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const generator = Alpine.$data(document.querySelector('[x-data="aiGenerator()"]'));
            if (generator && generator.initML) {
                generator.initML();
            }
        }, 1000);
    });

</script>
