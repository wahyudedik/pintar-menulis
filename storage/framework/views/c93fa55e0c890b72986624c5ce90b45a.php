                <form @submit.prevent="generateCopywriting" x-show="generatorType === 'text' && mode === 'advanced'" x-cloak>
                    <!-- Category -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-600">*</span>
                        </label>
                        <select x-model="form.category" @change="updateSubcategories" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Kategori</option>
                            <optgroup label="🚀 Quick Templates">
                                <option value="quick_templates">Quick Templates (Caption, Hook, Quotes)</option>
                                <option value="viral_clickbait">Viral & Clickbait Content</option>
                                <option value="trend_fresh_ideas">Trend & Fresh Ideas</option>
                            </optgroup>
                            <optgroup label="🏭 Industry Presets (Khusus UMKM)">
                                <option value="industry_presets">Industry Presets (Fashion, Makanan, Jasa)</option>
                            </optgroup>
                            <optgroup label="📝 Kategori Lengkap">
                                <option value="website_landing">Website & Landing Page</option>
                                <option value="ads">Iklan (Ads)</option>
                                <option value="social_media">Social Media Content</option>
                                <option value="marketplace">Marketplace / Toko Online</option>
                                <option value="event_promo">Event & Promosi</option>
                                <option value="invitation_event">Undangan & Event</option>
                                <option value="hr_recruitment">HR & Recruitment</option>
                                <option value="branding_tagline">Branding & Tagline</option>
                                <option value="education_institution">Pendidikan & Lembaga</option>
                                <option value="email_whatsapp">Email & WhatsApp Marketing</option>
                                <option value="proposal_company">Proposal & Company Profile</option>
                                <option value="personal_branding">Personal Branding</option>
                                <option value="ux_writing">UX Writing</option>
                            </optgroup>
                            <optgroup label="🎯 Marketing & Sales">
                                <option value="marketing_funnel">Marketing Funnel Copy</option>
                                <option value="sales_page">Sales Page Generator</option>
                                <option value="lead_magnet">Lead Magnet Copy</option>
                            </optgroup>
                            <optgroup label="💰 Monetisasi & Penghasilan">
                                <option value="video_monetization">Video Monetization (TikTok, YouTube, dll)</option>
                                <option value="photo_monetization">Photo/Image Monetization (Stock Photo)</option>
                                <option value="print_on_demand">Print on Demand</option>
                                <option value="freelance">Freelance (Lokal & Global)</option>
                                <option value="digital_products">Produk Digital</option>
                                <option value="ebook_publishing">eBook & Publishing</option>
                                <option value="academic_writing">Jurnal & Paper Akademik</option>
                                <option value="writing_monetization">Monetisasi Tulisan (Medium, Substack)</option>
                                <option value="affiliate_marketing">Affiliate Marketing</option>
                                <option value="blog_seo">Blog & SEO Content</option>
                            </optgroup>
                            <optgroup label="🎭 Drama & Cerita">
                                <option value="short_drama">Short Drama & Story (Drakor/Dracin Style)</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Subcategory -->
                    <div class="mb-4" x-show="subcategories.length > 0" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Konten <span class="text-red-600">*</span>
                        </label>
                        <select x-model="form.subcategory" 
                                :required="subcategories.length > 0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Jenis Konten</option>
                            <template x-for="sub in subcategories" :key="sub.value">
                                <option :value="sub.value" x-text="sub.label"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Platform -->
                    <div class="mb-4" x-show="form.category === 'social_media' || form.category === 'ads' || form.category === 'video_monetization' || form.category === 'marketplace'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                        <select x-model="form.platform"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <optgroup label="Social Media">
                                <option value="instagram">Instagram</option>
                                <option value="facebook">Facebook</option>
                                <option value="tiktok">TikTok</option>
                                <option value="linkedin">LinkedIn</option>
                                <option value="x">X</option>
                                <option value="youtube">YouTube</option>
                                <option value="youtube_shorts">YouTube Shorts</option>
                            </optgroup>
                            <optgroup label="Marketplace Indonesia">
                                <option value="shopee">Shopee</option>
                                <option value="tokopedia">Tokopedia</option>
                                <option value="bukalapak">Bukalapak</option>
                                <option value="lazada">Lazada</option>
                                <option value="blibli">Blibli</option>
                                <option value="tiktok_shop">TikTok Shop</option>
                                <option value="olx">OLX</option>
                                <option value="facebook_marketplace">Facebook Marketplace</option>
                                <option value="carousell">Carousell</option>
                            </optgroup>
                            <optgroup label="Marketplace Global">
                                <option value="amazon">Amazon</option>
                                <option value="ebay">eBay</option>
                                <option value="etsy">Etsy</option>
                                <option value="alibaba">Alibaba</option>
                                <option value="aliexpress">AliExpress</option>
                                <option value="shopify">Shopify</option>
                                <option value="walmart">Walmart</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Brief -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Brief / Deskripsi <span class="text-red-600">*</span>
                        </label>
                        <textarea x-model="form.brief" required rows="5"
                                  placeholder="Jelaskan produk/jasa, target audience, dan keunikan Anda..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Semakin detail, semakin baik hasilnya</p>
                    </div>

                    <!-- Tone -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tone / Gaya Bahasa</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'casual' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="casual" class="mr-2">
                                <span class="text-sm">Casual</span>
                            </label>
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'formal' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="formal" class="mr-2">
                                <span class="text-sm">Formal</span>
                            </label>
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'persuasive' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="persuasive" class="mr-2">
                                <span class="text-sm">Persuasive</span>
                            </label>
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'funny' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="funny" class="mr-2">
                                <span class="text-sm">Funny</span>
                            </label>
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'emotional' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="emotional" class="mr-2">
                                <span class="text-sm">Emotional</span>
                            </label>
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                   :class="form.tone === 'educational' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                <input type="radio" x-model="form.tone" value="educational" class="mr-2">
                                <span class="text-sm">Educational</span>
                            </label>
                        </div>
                    </div>

                    <!-- Keywords -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keywords (Optional)</label>
                        <input type="text" x-model="form.keywords"
                               placeholder="Pisahkan dengan koma"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Generate Multiple Variations -->
                    <div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
                        <div class="mb-3">
                            <label class="flex items-center cursor-pointer mb-2">
                                <input type="checkbox" x-model="form.generate_variations" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3">
                                    <span class="text-sm font-medium text-gray-900">🔥 Generate Multiple Captions (Premium)</span>
                                    <span class="block text-xs text-gray-600 mt-1">
                                        Default: 1 caption terbaik<br>
                                        Centang untuk generate lebih banyak variasi (pakai kuota lebih)
                                    </span>
                                </span>
                            </label>
                        </div>
                        
                        <!-- Variation Count Options (shown when checkbox is checked) -->
                        <div x-show="form.generate_variations" x-cloak class="mt-3 pl-8 space-y-2">
                            <p class="text-xs font-semibold text-gray-700 mb-2">Pilih Jumlah Caption:</p>
                            
                            <label class="flex items-center cursor-pointer p-2 rounded hover:bg-white transition">
                                <input type="radio" x-model="form.variation_count" value="5" name="variation_count" class="w-4 h-4 text-blue-600">
                                <span class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-900">5 Captions</span>
                                    <span class="block text-xs text-gray-600">Pakai 2 kuota — pilihan cepat</span>
                                </span>
                            </label>
                            
                            <label class="flex items-center cursor-pointer p-2 rounded hover:bg-white transition">
                                <input type="radio" x-model="form.variation_count" value="10" name="variation_count" class="w-4 h-4 text-blue-600">
                                <span class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-900">10 Captions</span>
                                    <span class="block text-xs text-gray-600">Pakai 3 kuota — lebih banyak pilihan</span>
                                </span>
                            </label>
                            
                            <label class="flex items-center cursor-pointer p-2 rounded hover:bg-white transition">
                                <input type="radio" x-model="form.variation_count" value="15" name="variation_count" class="w-4 h-4 text-blue-600">
                                <span class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-900">15 Captions</span>
                                    <span class="block text-xs text-gray-600">Pakai 4 kuota — cocok untuk A/B testing</span>
                                </span>
                            </label>
                            
                            <label class="flex items-center cursor-pointer p-2 rounded hover:bg-white transition">
                                <input type="radio" x-model="form.variation_count" value="20" name="variation_count" class="w-4 h-4 text-blue-600">
                                <span class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-900">20 Captions</span>
                                    <span class="block text-xs text-gray-600">Pakai 5 kuota — untuk campaign besar</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Auto Hashtag Generator -->
                    <div class="mb-4 p-4 bg-green-50 rounded-lg border border-green-200">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" x-model="form.auto_hashtag" checked class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="ml-3">
                                <span class="text-sm font-medium text-gray-900">🏷️ Auto Hashtag Indonesia</span>
                                <span class="block text-xs text-gray-600 mt-1">Otomatis generate hashtag trending & relevan untuk market Indonesia</span>
                            </span>
                        </label>
                    </div>

                    <!-- Bahasa Daerah (Optional) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bahasa Daerah (Optional) 
                            <span class="text-xs text-gray-500">- Tambah sentuhan lokal</span>
                        </label>
                        <select x-model="form.local_language" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Tidak pakai bahasa daerah</option>
                            <option value="jawa">🗣️ Bahasa Jawa (Halus/Ngoko)</option>
                            <option value="sunda">🗣️ Bahasa Sunda</option>
                            <option value="betawi">🗣️ Bahasa Betawi</option>
                            <option value="minang">🗣️ Bahasa Minang</option>
                            <option value="bali">🗣️ Bahasa Bali</option>
                            <option value="batak">🗣️ Bahasa Batak</option>
                            <option value="madura">🗣️ Bahasa Madura</option>
                            <option value="bugis">🗣️ Bahasa Bugis</option>
                            <option value="banjar">🗣️ Bahasa Banjar</option>
                            <option value="mixed">🌍 Mix Bahasa (Indo + Daerah)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Cocok untuk target market lokal spesifik</p>
                    </div>

                    <!-- Generate Button -->
                    <button type="submit" :disabled="loading"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span x-show="!loading">Generate dengan AI <span class="text-blue-200 text-xs ml-1" x-text="'(' + (creditCosts.generate || 1) + ' kredit)'"></span></span>
                        <span x-show="loading" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="_loadingSeconds < 5 ? 'Generating...' : (_loadingSeconds < 15 ? 'Masih proses... (' + _loadingSeconds + 's)' : 'Hampir selesai... (' + _loadingSeconds + 's)')"></span>
                        </span>
                    </button>
                </form><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/partials/ai-generator/form-text-advanced.blade.php ENDPATH**/ ?>