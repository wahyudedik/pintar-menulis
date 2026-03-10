@extends('layouts.client')

@section('title', 'AI Generator')

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<script src="{{ asset('js/caption-analysis.js') }}"></script>
@endpush

@section('content')
<div class="p-6" x-data="aiGenerator()" x-init="init()">
    <!-- Header dengan ML Insights Button -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">AI Copywriting Generator</h1>
            <p class="text-sm text-gray-500 mt-1">Generate copywriting berkualitas dengan AI</p>
        </div>
        <!-- 🤖 ML Insights Button (Top Right) -->
        <button @click="toggleMLPreview()" 
                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl transition flex items-center space-x-2 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <span>ML Insights</span>
        </button>
    </div>

    <!-- Load Brand Voice -->
    <div class="mb-6 bg-purple-50 border border-purple-200 rounded-lg p-4" x-data="{ showBrandVoices: false }">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">💼 Brand Voice</h3>
                <p class="text-xs text-gray-600 mt-1">Load preferensi brand kamu untuk generate lebih cepat</p>
            </div>
            <button @click="showBrandVoices = !showBrandVoices" 
                    class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition">
                <span x-show="!showBrandVoices">Load Brand Voice</span>
                <span x-show="showBrandVoices">Tutup</span>
            </button>
        </div>
        
        <div x-show="showBrandVoices" x-cloak class="mt-4" x-init="$watch('showBrandVoices', value => { if(value) loadBrandVoices() })">
            <div x-show="brandVoices.length === 0" class="text-center py-4 text-gray-500 text-sm">
                Belum ada brand voice tersimpan
            </div>
            <div x-show="brandVoices.length > 0" class="space-y-2">
                <template x-for="voice in brandVoices" :key="voice.id">
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-900" x-text="voice.name"></span>
                                <span x-show="voice.is_default" class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded">Default</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1" x-text="voice.brand_description || 'No description'"></p>
                        </div>
                        <button @click="loadBrandVoice(voice)" 
                                class="px-3 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700 transition">
                            Load
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Mode Toggle -->
    <div class="mb-6 flex justify-center">
        <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-gray-50 flex-wrap gap-1">
            <button @click="generatorType = 'text'; mode = 'simple'" 
                    :class="generatorType === 'text' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                📝 Text Generator
            </button>
            <button @click="generatorType = 'image'" 
                    :class="generatorType === 'image' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🖼️ Image Caption
            </button>
            <button @click="generatorType = 'bulk'" 
                    :class="generatorType === 'bulk' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                📅 Bulk Content
            </button>
            <button @click="generatorType = 'history'" 
                    :class="generatorType === 'history' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                📜 History
            </button>
            <button @click="generatorType = 'stats'" 
                    :class="generatorType === 'stats' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                📊 My Stats
            </button>
        </div>
    </div>

    <!-- Text Mode Toggle (only show when text generator is active) -->
    <div class="mb-6 flex justify-center" x-show="generatorType === 'text'" x-cloak>
        <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-gray-50">
            <button @click="mode = 'simple'" 
                    :class="mode === 'simple' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-6 py-2 rounded-md text-sm font-medium transition">
                🎯 Mode Simpel
            </button>
            <button @click="mode = 'advanced'" 
                    :class="mode === 'advanced' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-6 py-2 rounded-md text-sm font-medium transition">
                ⚙️ Mode Lengkap
            </button>
        </div>
    </div>

    <!-- Content Area -->
    <div class="w-full">
        <!-- Form Input -->
        <div class="w-full">
            <div class="bg-white rounded-lg border border-gray-200 p-6" x-show="generatorType === 'text' || generatorType === 'image'" x-cloak>
                
                <!-- SIMPLE MODE -->
                <form @submit.prevent="generateCopywriting" x-show="generatorType === 'text' && mode === 'simple'" x-cloak>
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm text-blue-800 font-medium">✨ Mode Simpel - Isi 6 pertanyaan, langsung jadi!</p>
                    </div>

                    <!-- Jenis Usaha -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            1. Jenis usaha kamu apa?
                        </label>
                        <select x-model="simpleForm.business_type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih jenis usaha (optional)</option>
                            <option value="fashion_clothing">👗 Fashion & Pakaian</option>
                            <option value="food_beverage">🍔 Makanan & Minuman</option>
                            <option value="beauty_skincare">💄 Kecantikan & Skincare</option>
                            <option value="printing_service">🖨️ Jasa Printing</option>
                            <option value="photography">📷 Jasa Fotografi</option>
                            <option value="catering">🍱 Catering</option>
                            <option value="tiktok_shop">🛍️ TikTok Shop</option>
                            <option value="shopee_affiliate">🛒 Affiliate Shopee</option>
                            <option value="home_decor">🏠 Dekorasi Rumah</option>
                            <option value="handmade_craft">✂️ Kerajinan Tangan</option>
                            <option value="digital_service">💻 Jasa Digital</option>
                            <option value="automotive">🚗 Otomotif</option>
                            <option value="other">🏪 Lainnya</option>
                        </select>
                    </div>

                    <!-- Nama Produk/Jasa -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            2. Apa yang mau dijual? <span class="text-red-600">*</span>
                        </label>
                        <input type="text" x-model="simpleForm.product_name" required
                               placeholder="Contoh: Baju anak umur 2 tahun"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Harga -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            3. Berapa harganya?
                        </label>
                        <input type="text" x-model="simpleForm.price"
                               placeholder="Contoh: 50.000 atau Mulai 50rb"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Target Market -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            4. Mau dijual ke siapa? <span class="text-red-600">*</span>
                        </label>
                        <select x-model="simpleForm.target_market" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih target pembeli</option>
                            <option value="ibu_muda">Ibu-ibu Muda (25-35 tahun)</option>
                            <option value="remaja">Remaja & Anak Muda (15-25 tahun)</option>
                            <option value="profesional">Pekerja Kantoran (25-40 tahun)</option>
                            <option value="pelajar">Pelajar & Mahasiswa</option>
                            <option value="umum">Umum (Semua kalangan)</option>
                        </select>
                    </div>

                    <!-- Tujuan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            5. Tujuannya apa? <span class="text-red-600">*</span>
                        </label>
                        <select x-model="simpleForm.goal" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih tujuan</option>
                            <option value="closing">Biar Langsung Beli (Closing)</option>
                            <option value="awareness">Biar Orang Kenal Produk (Branding)</option>
                            <option value="engagement">Biar Banyak yang Like & Comment</option>
                            <option value="viral">Biar Viral & Banyak Share</option>
                        </select>
                    </div>

                    <!-- Platform -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            6. Mau posting di mana? <span class="text-red-600">*</span>
                        </label>
                        <select x-model="simpleForm.platform" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="instagram">Instagram</option>
                            <option value="facebook">Facebook</option>
                            <option value="tiktok">TikTok</option>
                            <option value="shopee">Shopee</option>
                        </select>
                    </div>

                    <!-- Generate Button -->
                    <button type="submit" :disabled="loading"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span x-show="!loading">🚀 Bikin Caption Sekarang!</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sedang Bikin...
                        </span>
                    </button>
                    <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-xs text-green-800 text-center" x-show="isFirstTimeUser">
                            <strong>🎉 Generate pertama: 5 variasi GRATIS!</strong><br>
                            Generate berikutnya: 1 caption terbaik (hemat & efisien)
                        </p>
                        <p class="text-xs text-green-800 text-center" x-show="!isFirstTimeUser" x-cloak>
                            <strong>✨ Generate 1 caption terbaik</strong><br>
                            Hemat waktu, langsung pakai! (GRATIS)
                        </p>
                    </div>
                </form>

                <!-- ADVANCED MODE -->
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
                                <option value="hr_recruitment">HR & Recruitment</option>
                                <option value="branding_tagline">Branding & Tagline</option>
                                <option value="education_institution">Pendidikan & Lembaga</option>
                                <option value="email_whatsapp">Email & WhatsApp Marketing</option>
                                <option value="proposal_company">Proposal & Company Profile</option>
                                <option value="personal_branding">Personal Branding</option>
                                <option value="ux_writing">UX Writing</option>
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
                                <option value="twitter">Twitter/X</option>
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
                                        Default: 1 caption terbaik (GRATIS)<br>
                                        Centang untuk pilih jumlah caption (berbayar)
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
                                    <span class="block text-xs text-gray-600">Rp 5,000 - Perfect untuk pilihan cepat</span>
                                </span>
                            </label>
                            
                            <label class="flex items-center cursor-pointer p-2 rounded hover:bg-white transition">
                                <input type="radio" x-model="form.variation_count" value="10" name="variation_count" class="w-4 h-4 text-blue-600">
                                <span class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-900">10 Captions</span>
                                    <span class="block text-xs text-gray-600">Rp 9,000 - Hemat 10% untuk lebih banyak pilihan</span>
                                </span>
                            </label>
                            
                            <label class="flex items-center cursor-pointer p-2 rounded hover:bg-white transition">
                                <input type="radio" x-model="form.variation_count" value="15" name="variation_count" class="w-4 h-4 text-blue-600">
                                <span class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-900">15 Captions</span>
                                    <span class="block text-xs text-gray-600">Rp 12,000 - Hemat 20% untuk A/B testing</span>
                                </span>
                            </label>
                            
                            <label class="flex items-center cursor-pointer p-2 rounded hover:bg-white transition">
                                <input type="radio" x-model="form.variation_count" value="20" name="variation_count" class="w-4 h-4 text-blue-600">
                                <span class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-900">20 Captions</span>
                                    <span class="block text-xs text-gray-600">Rp 15,000 - Hemat 25% untuk campaign besar</span>
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
                            <option value="batak">🗣️ Bahasa Batak</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Cocok untuk target market lokal spesifik</p>
                    </div>

                    <!-- Generate Button -->
                    <button type="submit" :disabled="loading"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span x-show="!loading">Generate dengan AI</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Generating...
                        </span>
                    </button>
                </form>

                <!-- IMAGE CAPTION MODE -->
                <form @submit.prevent="generateImageCaption" x-show="generatorType === 'image'" x-cloak enctype="multipart/form-data">
                    <div class="mb-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <p class="text-sm text-purple-800 font-medium">🖼️ Upload foto produk, AI generate caption otomatis!</p>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Produk *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition cursor-pointer" 
                             @click="$refs.imageInput.click()"
                             @dragover.prevent="$el.classList.add('border-blue-400', 'bg-blue-50')"
                             @dragleave.prevent="$el.classList.remove('border-blue-400', 'bg-blue-50')"
                             @drop.prevent="handleImageDrop($event)">
                            
                            <input type="file" 
                                   x-ref="imageInput" 
                                   @change="handleImageSelect($event)" 
                                   accept="image/*" 
                                   class="hidden">
                            
                            <div x-show="!imageForm.preview">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-600 mb-2">Klik atau drag & drop foto di sini</p>
                                <p class="text-xs text-gray-500">JPG, PNG (Max 5MB)</p>
                            </div>

                            <div x-show="imageForm.preview" class="relative">
                                <img :src="imageForm.preview" alt="Preview" class="max-h-64 mx-auto rounded-lg">
                                <button type="button" 
                                        @click.stop="removeImage()" 
                                        class="mt-3 text-sm text-red-600 hover:text-red-700">
                                    Ganti Foto
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Optional Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Bisnis (Opsional)</label>
                            <input type="text" 
                                   x-model="imageForm.business_type" 
                                   placeholder="Contoh: Kuliner, Fashion, Kosmetik" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk (Opsional)</label>
                            <input type="text" 
                                   x-model="imageForm.product_name" 
                                   placeholder="Contoh: Nasi Goreng Spesial" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">AI akan generate:</p>
                                <ul class="list-disc list-inside space-y-1 text-blue-700 text-xs">
                                    <li>Caption untuk single post</li>
                                    <li>Caption untuk carousel (3 slide)</li>
                                    <li>Deteksi objek dalam foto</li>
                                    <li>Tips editing & filter</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            :disabled="loading || !imageForm.file"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span x-show="!loading" class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Generate Caption dari Foto
                        </span>
                        <span x-show="loading" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Analyzing Image...
                        </span>
                    </button>
                </form>
            </div>

            <!-- BULK CONTENT MODE -->
            <div x-show="generatorType === 'bulk'" x-cloak class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Bulk Content Generator</h3>
                <p class="text-gray-600 mb-4">Generate 7 atau 30 hari konten sekaligus dengan content calendar</p>
                <a href="{{ route('bulk-content.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Buka Bulk Content Generator
                </a>
            </div>

            <!-- CAPTION HISTORY MODE -->
            <div x-show="generatorType === 'history'" x-cloak class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Caption History</h3>
                <p class="text-gray-600 mb-4">Lihat semua caption yang pernah kamu generate</p>
                <a href="{{ route('caption-history.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                        Lihat Caption History
                    </a>
                </div>

            <!-- MY STATS MODE -->
            <div x-show="generatorType === 'stats'" x-cloak class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">My Stats & ML Insights</h3>
                <p class="text-gray-600 mb-4">Lihat statistik dan insights dari AI kamu</p>
                <a href="{{ route('my-stats') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Lihat My Stats
                </a>
            </div>
        </div>

        </div>

        <!-- Result Section -->
        <div x-show="generatorType === 'text' || generatorType === 'image'" x-cloak class="mt-6">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hasil Generate</h3>
                
                <div x-show="!result && !loading" class="text-center py-12 text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-sm">Hasil akan muncul di sini</p>
                </div>

                <div x-show="loading" x-cloak class="text-center py-12">
                    <svg class="animate-spin h-10 w-10 mx-auto text-blue-600 mb-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm text-gray-600">AI sedang bekerja...</p>
                </div>

                <div x-show="result && !loading" x-cloak>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4 max-h-96 overflow-y-auto">
                        <pre class="whitespace-pre-wrap text-sm text-gray-800" x-text="cleanMarkdown(result)"></pre>
                    </div>
                    
                    <!-- 🔍 KEYWORD INSIGHTS SECTION -->
                    <div x-show="keywordInsights && keywordInsights.length > 0" x-cloak class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Analisis Keyword
                        </h4>
                        
                        <template x-for="(kw, index) in keywordInsights" :key="index">
                            <div class="mb-3 p-3 bg-white rounded-lg shadow-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <span class="font-medium text-gray-900" x-text="kw.keyword"></span>
                                        <span class="ml-2 text-xs px-2 py-1 rounded-full"
                                              :class="{
                                                  'bg-green-100 text-green-700': kw.competition === 'LOW',
                                                  'bg-yellow-100 text-yellow-700': kw.competition === 'MEDIUM',
                                                  'bg-red-100 text-red-700': kw.competition === 'HIGH'
                                              }"
                                              x-text="kw.competition === 'LOW' ? 'Rendah' : (kw.competition === 'MEDIUM' ? 'Sedang' : 'Tinggi')">
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-semibold text-blue-600" x-text="kw.search_volume ? kw.search_volume.toLocaleString('id-ID') + '/bln' : '-'"></div>
                                        <div class="text-xs text-gray-500">pencarian</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between text-xs text-gray-600 mb-2">
                                    <span>CPC:</span>
                                    <span class="font-medium">
                                        Rp <span x-text="kw.cpc_low ? kw.cpc_low.toLocaleString('id-ID') : '0'"></span> - 
                                        Rp <span x-text="kw.cpc_high ? kw.cpc_high.toLocaleString('id-ID') : '0'"></span>
                                    </span>
                                </div>
                                
                                <template x-if="kw.related_keywords && kw.related_keywords.length > 0">
                                    <div class="mt-2 pt-2 border-t border-gray-100">
                                        <div class="text-xs text-gray-500 mb-1">Related Keywords:</div>
                                        <div class="flex flex-wrap gap-1">
                                            <template x-for="(related, idx) in kw.related_keywords.slice(0, 5)" :key="idx">
                                                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded" x-text="related"></span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        
                        <div class="mt-3 p-2 bg-blue-100 rounded text-xs text-blue-800">
                            <strong>💡 Tips:</strong> Fokus ke keyword dengan volume tinggi & kompetisi rendah untuk hasil maksimal!
                        </div>
                    </div>
                    
                    <!-- Rating Section -->
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200" x-show="!rated">
                        <p class="text-sm font-medium text-gray-900 mb-2">⭐ Bagaimana hasilnya?</p>
                        <p class="text-xs text-gray-600 mb-3">Rating Anda membantu AI belajar dan improve!</p>
                        <div class="flex items-center justify-center space-x-2 mb-3">
                            <template x-for="star in 5" :key="star">
                                <button @click="selectedRating = star" 
                                        class="text-3xl transition-transform hover:scale-110"
                                        :style="star <= selectedRating ? 'filter: grayscale(0%);' : 'filter: grayscale(100%) brightness(1.5);'">
                                    ⭐
                                </button>
                            </template>
                        </div>
                        <div x-show="selectedRating > 0" x-cloak>
                            <textarea x-model="ratingFeedback" 
                                      placeholder="Feedback (optional): Apa yang bisa diperbaiki?"
                                      rows="2"
                                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 mb-2"></textarea>
                            <button @click="submitRating"
                                    :disabled="submittingRating"
                                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-sm disabled:bg-gray-400">
                                <span x-show="!submittingRating">Submit Rating</span>
                                <span x-show="submittingRating">Submitting...</span>
                            </button>
                        </div>
                    </div>
                    
                    <div x-show="rated" x-cloak class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-sm text-green-800 text-center">
                            ✓ Terima kasih atas rating Anda! 🙏
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        <button @click="copyToClipboard" 
                                class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition text-sm">
                            <span x-show="!copied">📋 Copy</span>
                            <span x-show="copied">✓ Copied!</span>
                        </button>
                        <button @click="analyzeCaption()"
                                :disabled="analysisLoading"
                                class="bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition text-sm flex items-center justify-center space-x-2 disabled:bg-gray-400">
                            <svg x-show="!analysisLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <!-- Loading spinner -->
                            <div x-show="analysisLoading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                            <span x-show="!analysisLoading">🔍 Analyze</span>
                            <span x-show="analysisLoading">Analyzing...</span>
                        </button>
                        <button @click="saveForAnalytics"
                                :disabled="saved"
                                class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition text-sm disabled:bg-gray-400">
                            <span x-show="!saved">💾 Save</span>
                            <span x-show="saved">✓ Saved!</span>
                        </button>
                        <button @click="showSaveBrandVoiceModal = true"
                                class="bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition text-sm">
                            💼 Brand Voice
                        </button>
                    </div>
                    
                    <button @click="reset"
                            class="w-full mt-3 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                        🔄 Generate Lagi
                    </button>
                </div>
            </div>
        </div>
        
        <!-- 🔍 Caption Analysis Modal - Full Screen -->
        @include('client.partials.caption-analysis')
        
        <!-- Save Brand Voice Modal -->
        <div x-show="showSaveBrandVoiceModal" x-cloak 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @click.self="showSaveBrandVoiceModal = false">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">💼 Save Brand Voice</h3>
                <p class="text-sm text-gray-600 mb-4">Simpan preferensi ini untuk generate lebih cepat di lain waktu</p>
                
                <form @submit.prevent="saveBrandVoice">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Brand Voice <span class="text-red-600">*</span></label>
                        <input type="text" x-model="brandVoiceForm.name" required
                               placeholder="Contoh: Toko Baju Anak Saya"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                        <textarea x-model="brandVoiceForm.brand_description" rows="2"
                                  placeholder="Contoh: Brand baju anak umur 2-5 tahun, target ibu muda"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" x-model="brandVoiceForm.is_default" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700">Set sebagai default (auto-load)</span>
                        </label>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" @click="showSaveBrandVoiceModal = false"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="savingBrandVoice"
                                class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:bg-gray-400">
                            <span x-show="!savingBrandVoice">Simpan</span>
                            <span x-show="savingBrandVoice">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- 🤖 ML Upgrade Modal & Features -->
        @include('client.partials.ml-upgrade-modal')
    </div>

<script>
    function aiGenerator() {
        return {
            mode: 'simple', // default simple mode
            generatorType: 'text', // text or image
            isFirstTimeUser: true, // will be checked on init
            selectedRating: 0,
            ratingFeedback: '',
            rated: false,
            submittingRating: false,
            lastCaptionId: null,
            keywordInsights: [], // 🔍 Keyword insights data
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
                business_type: '',
                product_name: '',
                price: '',
                target_market: '',
                goal: '',
                platform: 'instagram'
            },
            imageForm: {
                file: null,
                preview: null,
                business_type: '',
                product_name: ''
            },
            subcategories: [],
            loading: false,
            result: '',
            copied: false,
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

            // Initialize - check if user is first time
            async init() {
                await this.checkFirstTimeStatus();
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
                    console.error('Check first time error:', error);
                    // Default to true if error
                    this.isFirstTimeUser = true;
                }
            },
            
            subcategoryOptions: {
                quick_templates: [
                    {value: 'caption_instagram', label: '📸 Caption Instagram'},
                    {value: 'caption_facebook', label: '👥 Caption Facebook'},
                    {value: 'caption_tiktok', label: '🎵 Caption TikTok'},
                    {value: 'caption_youtube', label: '📺 Caption YouTube'},
                    {value: 'caption_linkedin', label: '💼 Caption LinkedIn'},
                    {value: 'hook_opening', label: '🎣 Hook Pembuka (3 detik)'},
                    {value: 'hook_video', label: '🎬 Hook Video Ads'},
                    {value: 'quotes_motivasi', label: '💪 Quotes Motivasi'},
                    {value: 'quotes_bisnis', label: '💼 Quotes Bisnis'},
                    {value: 'humor_content', label: '😂 Konten Humor'},
                    {value: 'viral_content', label: '🔥 Konten Viral'},
                    {value: 'storytelling_short', label: '📖 Storytelling Pendek'},
                    {value: 'cta_powerful', label: '⚡ Call To Action Kuat'},
                    {value: 'headline_catchy', label: '✨ Headline Menarik'}
                ],
                viral_clickbait: [
                    {value: 'clickbait_title', label: '🎯 Clickbait Title (Honest)'},
                    {value: 'curiosity_gap', label: '🤔 Curiosity Gap Hook'},
                    {value: 'shocking_statement', label: '😱 Shocking Statement'},
                    {value: 'controversial_take', label: '🔥 Controversial Take'},
                    {value: 'before_after', label: '📊 Before & After Story'},
                    {value: 'secret_reveal', label: '🔓 Secret Reveal'},
                    {value: 'mistake_warning', label: '⚠️ Mistake Warning'},
                    {value: 'myth_busting', label: '💥 Myth Busting'},
                    {value: 'unpopular_opinion', label: '🗣️ Unpopular Opinion'},
                    {value: 'life_hack', label: '💡 Life Hack / Tips Viral'},
                    {value: 'challenge_trend', label: '🎮 Challenge / Trend'},
                    {value: 'reaction_bait', label: '💬 Reaction Bait'},
                    {value: 'cliffhanger', label: '⏸️ Cliffhanger Ending'},
                    {value: 'number_list', label: '🔢 Number List (5 Cara, 10 Tips)'},
                    {value: 'question_hook', label: '❓ Question Hook'},
                    {value: 'emotional_trigger', label: '💔 Emotional Trigger'},
                    {value: 'fomo_content', label: '⏰ FOMO Content'},
                    {value: 'plot_twist', label: '🎭 Plot Twist Story'},
                    {value: 'relatable_content', label: '😂 Relatable Content'},
                    {value: 'storytime', label: '📖 Storytime (Viral Format)'}
                ],
                trend_fresh_ideas: [
                    {value: 'trending_topic', label: '🔥 Trending Topic Ideas'},
                    {value: 'viral_challenge', label: '🎯 Viral Challenge Ideas'},
                    {value: 'seasonal_content', label: '📅 Seasonal Content Ideas'},
                    {value: 'holiday_campaign', label: '🎉 Holiday Campaign Ideas'},
                    {value: 'current_events', label: '📰 Current Events Angle'},
                    {value: 'meme_marketing', label: '😂 Meme Marketing Ideas'},
                    {value: 'tiktok_trend', label: '🎵 TikTok Trend Ideas'},
                    {value: 'instagram_trend', label: '📱 Instagram Trend Ideas'},
                    {value: 'youtube_trend', label: '📺 YouTube Trend Ideas'},
                    {value: 'twitter_trend', label: '🐦 Twitter/X Trend Ideas'},
                    {value: 'content_series', label: '📺 Content Series Ideas'},
                    {value: 'collaboration_ideas', label: '🤝 Collaboration Ideas'},
                    {value: 'giveaway_ideas', label: '🎁 Giveaway Campaign Ideas'},
                    {value: 'user_generated', label: '👥 User Generated Content Ideas'},
                    {value: 'behind_scenes', label: '🎬 Behind The Scenes Ideas'},
                    {value: 'educational_series', label: '🎓 Educational Series Ideas'},
                    {value: 'storytelling_series', label: '📖 Storytelling Series Ideas'},
                    {value: 'product_launch', label: '🚀 Product Launch Ideas'},
                    {value: 'rebranding_ideas', label: '✨ Rebranding Campaign Ideas'},
                    {value: 'crisis_content', label: '🆘 Crisis/Comeback Content Ideas'}
                ],
                industry_presets: [
                    {value: 'fashion_clothing', label: '👗 Fashion & Pakaian'},
                    {value: 'food_beverage', label: '🍔 Makanan & Minuman'},
                    {value: 'beauty_skincare', label: '💄 Kecantikan & Skincare'},
                    {value: 'printing_service', label: '🖨️ Jasa Printing & Percetakan'},
                    {value: 'photography', label: '📷 Jasa Fotografi'},
                    {value: 'catering', label: '🍱 Catering & Katering'},
                    {value: 'tiktok_shop', label: '🛍️ TikTok Shop'},
                    {value: 'shopee_affiliate', label: '🛒 Affiliate Shopee'},
                    {value: 'home_decor', label: '🏠 Dekorasi Rumah'},
                    {value: 'handmade_craft', label: '✂️ Kerajinan Tangan'},
                    {value: 'digital_service', label: '💻 Jasa Digital'},
                    {value: 'automotive', label: '🚗 Otomotif & Aksesoris'}
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
                    {value: 'grand_opening', label: '🎉 Grand Opening'},
                    {value: 'flash_sale', label: '⚡ Flash Sale / Sale Kilat'},
                    {value: 'discount_promo', label: '💰 Diskon & Promo Spesial'},
                    {value: 'bazaar', label: '🛍️ Bazaar / Pameran'},
                    {value: 'exhibition', label: '🎨 Exhibition / Pameran Seni'},
                    {value: 'workshop', label: '👨‍🏫 Workshop / Seminar'},
                    {value: 'product_launch', label: '🚀 Product Launch'},
                    {value: 'anniversary', label: '🎂 Anniversary / Ulang Tahun'},
                    {value: 'seasonal_promo', label: '🎄 Promo Musiman (Lebaran, Natal, dll)'},
                    {value: 'clearance_sale', label: '🏷️ Clearance Sale / Obral'},
                    {value: 'buy_1_get_1', label: '🎁 Buy 1 Get 1 / Bundling'},
                    {value: 'loyalty_program', label: '⭐ Program Loyalitas / Member'},
                    {value: 'giveaway', label: '🎁 Giveaway / Kuis Berhadiah'},
                    {value: 'pre_order', label: '📦 Pre-Order Campaign'},
                    {value: 'limited_edition', label: '💎 Limited Edition / Exclusive'},
                    {value: 'collaboration', label: '🤝 Kolaborasi Brand'},
                    {value: 'charity_event', label: '❤️ Event Charity / Sosial'},
                    {value: 'meet_greet', label: '👋 Meet & Greet / Gathering'},
                    {value: 'live_shopping', label: '📱 Live Shopping / Live Selling'},
                    {value: 'countdown_promo', label: '⏰ Countdown Promo (24 Jam, 3 Hari, dll)'}
                ],
                hr_recruitment: [
                    {value: 'job_description', label: '📋 Job Description / JD'},
                    {value: 'job_vacancy', label: '📢 Lowongan Kerja / Vacancy Post'},
                    {value: 'job_requirements', label: '✅ Job Requirements / Kualifikasi'},
                    {value: 'company_culture', label: '🏢 Company Culture Description'},
                    {value: 'employee_benefits', label: '🎁 Employee Benefits Package'},
                    {value: 'interview_questions', label: '❓ Interview Questions'},
                    {value: 'offer_letter', label: '💌 Offer Letter'},
                    {value: 'rejection_letter', label: '📧 Rejection Letter (Polite)'},
                    {value: 'onboarding_message', label: '👋 Onboarding Welcome Message'},
                    {value: 'internship_program', label: '🎓 Internship Program Description'},
                    {value: 'career_page', label: '💼 Career Page Content'},
                    {value: 'linkedin_job_post', label: '💼 LinkedIn Job Post'},
                    {value: 'instagram_hiring', label: '📱 Instagram Hiring Post'},
                    {value: 'whatsapp_recruitment', label: '💬 WhatsApp Recruitment Message'},
                    {value: 'employee_referral', label: '🤝 Employee Referral Program'},
                    {value: 'job_fair_booth', label: '🎪 Job Fair Booth Description'},
                    {value: 'campus_recruitment', label: '🎓 Campus Recruitment Pitch'},
                    {value: 'remote_job', label: '🏠 Remote Job Description'},
                    {value: 'freelance_job', label: '💻 Freelance Job Brief'},
                    {value: 'part_time_job', label: '⏰ Part-Time Job Post'}
                ],
                branding_tagline: [
                    {value: 'brand_tagline', label: '✨ Brand Tagline / Slogan'},
                    {value: 'company_tagline', label: '🏢 Company Tagline'},
                    {value: 'product_tagline', label: '📦 Product Tagline'},
                    {value: 'brand_name', label: '🎯 Brand Name Ideas'},
                    {value: 'product_name', label: '🏷️ Product Name Ideas'},
                    {value: 'business_name', label: '💼 Business Name Ideas'},
                    {value: 'tshirt_quote', label: '👕 T-Shirt Quote / Text'},
                    {value: 'hoodie_text', label: '🧥 Hoodie Text'},
                    {value: 'tote_bag_text', label: '👜 Tote Bag Text'},
                    {value: 'mug_text', label: '☕ Mug Text'},
                    {value: 'sticker_text', label: '🏷️ Sticker Text'},
                    {value: 'poster_quote', label: '🖼️ Poster Quote'},
                    {value: 'motivational_quote', label: '💪 Motivational Quote'},
                    {value: 'funny_quote', label: '😂 Funny Quote'},
                    {value: 'inspirational_quote', label: '✨ Inspirational Quote'},
                    {value: 'logo_text', label: '🎨 Logo Text / Wordmark'},
                    {value: 'brand_story', label: '📖 Brand Story (Short)'},
                    {value: 'brand_mission', label: '🎯 Brand Mission Statement'},
                    {value: 'brand_vision', label: '🔮 Brand Vision Statement'},
                    {value: 'brand_values', label: '💎 Brand Values'},
                    {value: 'usp', label: '⚡ USP (Unique Selling Proposition)'},
                    {value: 'elevator_pitch', label: '🎤 Elevator Pitch (30 detik)'},
                    {value: 'brand_positioning', label: '📍 Brand Positioning Statement'},
                    {value: 'catchphrase', label: '🗣️ Catchphrase / Jargon Brand'},
                    {value: 'merchandise_collection', label: '🎁 Merchandise Collection Name'}
                ],
                education_institution: [
                    {value: 'school_achievement', label: '🏆 Pencapaian Sekolah/Kampus'},
                    {value: 'student_achievement', label: '⭐ Prestasi Siswa/Mahasiswa'},
                    {value: 'graduation_announcement', label: '🎓 Pengumuman Kelulusan'},
                    {value: 'new_student_admission', label: '📝 Penerimaan Siswa Baru (PSB/PPDB)'},
                    {value: 'school_event', label: '🎉 Event Sekolah/Kampus'},
                    {value: 'national_holiday', label: '🇮🇩 Hari Besar Nasional'},
                    {value: 'education_day', label: '📚 Hari Pendidikan (Hardiknas, dll)'},
                    {value: 'teacher_day', label: '👨‍🏫 Hari Guru'},
                    {value: 'independence_day', label: '🎊 HUT RI / Kemerdekaan'},
                    {value: 'religious_holiday', label: '🕌 Hari Besar Keagamaan'},
                    {value: 'school_anniversary', label: '🎂 HUT Sekolah/Kampus'},
                    {value: 'academic_info', label: '📢 Informasi Akademik'},
                    {value: 'exam_announcement', label: '📝 Pengumuman Ujian'},
                    {value: 'scholarship_info', label: '💰 Info Beasiswa'},
                    {value: 'extracurricular', label: '⚽ Kegiatan Ekstrakurikuler'},
                    {value: 'parent_meeting', label: '👨‍👩‍👧 Rapat Orang Tua'},
                    {value: 'school_facility', label: '🏫 Fasilitas Sekolah/Kampus'},
                    {value: 'teacher_profile', label: '👩‍🏫 Profil Guru/Dosen'},
                    {value: 'alumni_success', label: '🌟 Kisah Sukses Alumni'},
                    {value: 'government_program', label: '🏛️ Program Pemerintah/Dinas'},
                    {value: 'public_service', label: '📋 Layanan Publik'},
                    {value: 'government_announcement', label: '📢 Pengumuman Resmi Instansi'},
                    {value: 'community_program', label: '🤝 Program Kemasyarakatan'},
                    {value: 'health_campaign', label: '🏥 Kampanye Kesehatan'},
                    {value: 'safety_awareness', label: '⚠️ Sosialisasi Keselamatan'}
                ],
                video_monetization: [
                    {value: 'tiktok_viral', label: '🎵 TikTok - Konten Viral'},
                    {value: 'youtube_long', label: '📺 YouTube - Video Panjang'},
                    {value: 'youtube_shorts', label: '🎬 YouTube Shorts'},
                    {value: 'facebook_video', label: '👥 Facebook Video'},
                    {value: 'snack_video', label: '🍿 Snack Video'},
                    {value: 'likee', label: '❤️ Likee'},
                    {value: 'kwai', label: '🎥 Kwai'},
                    {value: 'bigo_live', label: '📹 Bigo Live'},
                    {value: 'nimo_tv', label: '🎮 Nimo TV'}
                ],
                photo_monetization: [
                    {value: 'shutterstock', label: '📷 Shutterstock - Deskripsi Foto'},
                    {value: 'adobe_stock', label: '🎨 Adobe Stock - Keywords'},
                    {value: 'getty_images', label: '🖼️ Getty Images - Caption'},
                    {value: 'istock', label: '📸 iStock - Metadata'},
                    {value: 'freepik', label: '🎭 Freepik - Tags'},
                    {value: 'vecteezy', label: '🎨 Vecteezy - Description'}
                ],
                print_on_demand: [
                    {value: 'redbubble', label: '🎨 Redbubble - Product Title'},
                    {value: 'teespring', label: '👕 Teespring - Description'},
                    {value: 'spreadshirt', label: '👔 Spreadshirt - Tags'},
                    {value: 'zazzle', label: '🎁 Zazzle - Product Copy'},
                    {value: 'society6', label: '🖼️ Society6 - Bio'}
                ],
                freelance: [
                    {value: 'upwork_proposal', label: '💼 Upwork - Proposal'},
                    {value: 'fiverr_gig', label: '🎯 Fiverr - Gig Description'},
                    {value: 'freelancer_bid', label: '📝 Freelancer - Bid'},
                    {value: 'sribulancer', label: '🇮🇩 Sribulancer - Penawaran'},
                    {value: 'projects_id', label: '🇮🇩 Projects.co.id - Proposal'},
                    {value: 'portfolio', label: '📁 Portfolio Description'},
                    {value: 'cover_letter', label: '✉️ Cover Letter'}
                ],
                digital_products: [
                    {value: 'gumroad', label: '🛍️ Gumroad - Product Page'},
                    {value: 'sellfy', label: '💳 Sellfy - Sales Copy'},
                    {value: 'payhip', label: '💰 Payhip - Description'},
                    {value: 'ebook_description', label: '📚 E-book Description'},
                    {value: 'course_landing', label: '🎓 Course Landing Page'},
                    {value: 'template_description', label: '📄 Template Description'}
                ],
                ebook_publishing: [
                    {value: 'kindle_description', label: '📱 Amazon Kindle - Book Description'},
                    {value: 'kindle_blurb', label: '📖 Kindle - Back Cover Blurb'},
                    {value: 'google_play_books', label: '📚 Google Play Books - Description'},
                    {value: 'apple_books', label: '🍎 Apple Books - Synopsis'},
                    {value: 'kobo', label: '📘 Kobo Writing Life - Description'},
                    {value: 'barnes_noble', label: '📕 Barnes & Noble Press - Copy'},
                    {value: 'leanpub', label: '📗 Leanpub - Sales Page'},
                    {value: 'gumroad_ebook', label: '🛍️ Gumroad - eBook Landing'},
                    {value: 'gramedia_digital', label: '🇮🇩 Gramedia Digital - Deskripsi'},
                    {value: 'mizanstore', label: '🇮🇩 Mizanstore - Sinopsis'},
                    {value: 'kubuku', label: '🇮🇩 Kubuku - Description'},
                    {value: 'storial', label: '🇮🇩 Storial - Cerita'},
                    {value: 'book_title', label: '✨ Book Title Generator'},
                    {value: 'chapter_outline', label: '📋 Chapter Outline'},
                    {value: 'author_bio', label: '👤 Author Bio'}
                ],
                academic_writing: [
                    {value: 'abstract', label: '📄 Abstract / Abstrak'},
                    {value: 'research_title', label: '🎯 Research Title'},
                    {value: 'introduction', label: '📝 Introduction'},
                    {value: 'literature_review', label: '📚 Literature Review Outline'},
                    {value: 'methodology', label: '🔬 Methodology Description'},
                    {value: 'conclusion', label: '✅ Conclusion'},
                    {value: 'keywords', label: '🔑 Keywords Generator'},
                    {value: 'researchgate_profile', label: '🔬 ResearchGate - Profile'},
                    {value: 'academia_bio', label: '🎓 Academia.edu - Bio'},
                    {value: 'paper_summary', label: '📊 Paper Summary'},
                    {value: 'conference_abstract', label: '🎤 Conference Abstract'}
                ],
                writing_monetization: [
                    {value: 'medium_article', label: '📝 Medium - Article'},
                    {value: 'medium_headline', label: '✨ Medium - Headline'},
                    {value: 'substack_post', label: '📧 Substack - Newsletter Post'},
                    {value: 'substack_welcome', label: '👋 Substack - Welcome Email'},
                    {value: 'patreon_tier', label: '💎 Patreon - Tier Description'},
                    {value: 'patreon_post', label: '📢 Patreon - Exclusive Post'},
                    {value: 'kofi_page', label: '☕ Ko-fi - Page Description'},
                    {value: 'newsletter_intro', label: '📬 Newsletter Introduction'},
                    {value: 'paid_content', label: '💰 Paid Content Teaser'}
                ],
                affiliate_marketing: [
                    {value: 'shopee_affiliate', label: '🛒 Shopee Affiliate - Review'},
                    {value: 'tokopedia_affiliate', label: '🛍️ Tokopedia Affiliate - Caption'},
                    {value: 'tiktok_affiliate', label: '🎵 TikTok Affiliate - Script'},
                    {value: 'amazon_associates', label: '📦 Amazon Associates - Review'},
                    {value: 'product_comparison', label: '⚖️ Product Comparison'},
                    {value: 'honest_review', label: '⭐ Honest Review'}
                ],
                blog_seo: [
                    {value: 'blog_post', label: '📝 Blog Post (SEO Optimized)'},
                    {value: 'article_intro', label: '🎯 Article Introduction'},
                    {value: 'meta_description', label: '🔍 Meta Description'},
                    {value: 'seo_title', label: '📌 SEO Title / H1'},
                    {value: 'h2_h3_headings', label: '📑 H2/H3 Headings Generator'},
                    {value: 'listicle', label: '📋 Listicle Article (Top 10, Best 5)'},
                    {value: 'how_to_guide', label: '📖 How-to Guide / Tutorial'},
                    {value: 'product_review', label: '⭐ Product Review Blog'},
                    {value: 'comparison_post', label: '⚖️ Comparison Post (A vs B)'},
                    {value: 'pillar_content', label: '🏛️ Pillar Content / Ultimate Guide'},
                    {value: 'faq_schema', label: '❓ FAQ Schema Markup'},
                    {value: 'featured_snippet', label: '🎯 Featured Snippet Optimization'},
                    {value: 'local_seo', label: '📍 Local SEO Content'},
                    {value: 'keyword_cluster', label: '🔑 Keyword Cluster Content'},
                    {value: 'internal_linking', label: '🔗 Internal Linking Anchor Text'},
                    {value: 'alt_text', label: '🖼️ Image Alt Text'},
                    {value: 'schema_markup', label: '📊 Schema Markup Description'},
                    {value: 'guest_post', label: '✍️ Guest Post Pitch'},
                    {value: 'content_update', label: '🔄 Content Update/Refresh'},
                    {value: 'roundup_post', label: '🎁 Roundup Post (Expert Roundup)'}
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
                ]
            },
            
            updateSubcategories() {
                this.subcategories = this.subcategoryOptions[this.form.category] || [];
                this.form.subcategory = '';
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
                        let resultText = '🖼️ IMAGE CAPTION RESULT\n\n';
                        
                        if (data.detected_objects && data.detected_objects.length > 0) {
                            resultText += '🔍 Objek Terdeteksi:\n';
                            data.detected_objects.forEach(obj => {
                                resultText += `• ${obj}\n`;
                            });
                            resultText += '\n';
                        }

                        if (data.caption_single) {
                            resultText += '📝 CAPTION SINGLE POST:\n';
                            resultText += data.caption_single + '\n\n';
                        }

                        if (data.caption_carousel && data.caption_carousel.length > 0) {
                            resultText += '📱 CAPTION CAROUSEL:\n\n';
                            data.caption_carousel.forEach((slide, index) => {
                                resultText += `Slide ${index + 1}:\n${slide}\n\n`;
                            });
                        }

                        if (data.editing_tips && data.editing_tips.length > 0) {
                            resultText += '💡 TIPS EDITING:\n';
                            data.editing_tips.forEach(tip => {
                                resultText += `• ${tip}\n`;
                            });
                        }

                        this.result = resultText;
                        this.lastCaptionId = data.caption_id;
                    } else {
                        throw new Error(data.message || 'Generation failed');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.error = error.message || 'Terjadi kesalahan saat generate caption';
                } finally {
                    this.loading = false;
                }
            },
            
            async generateCopywriting() {
                // Handle Simple Mode
                if (this.mode === 'simple') {
                    if (!this.simpleForm.product_name || !this.simpleForm.target_market || !this.simpleForm.goal || !this.simpleForm.platform) {
                        alert('Mohon isi semua pertanyaan yang wajib (*)');
                        return;
                    }
                    
                    // IMPROVED CONVERSION LOGIC v2.0
                    // Convert simple form to advanced form with smart mapping
                    
                    if (this.simpleForm.business_type) {
                        // If business type selected, use industry preset
                        this.form.category = 'industry_presets';
                        this.form.subcategory = this.simpleForm.business_type;
                    } else {
                        // Use quick templates based on PLATFORM (not always Instagram!)
                        this.form.category = 'quick_templates';
                        
                        // Platform-specific subcategory mapping
                        const platformMap = {
                            'instagram': 'caption_instagram',
                            'facebook': 'caption_facebook',
                            'tiktok': 'caption_tiktok',
                            'youtube': 'caption_youtube',
                            'youtube_shorts': 'caption_youtube',
                            'linkedin': 'caption_linkedin',
                            'twitter': 'caption_instagram', // fallback
                            'shopee': 'caption_instagram', // marketplace uses general
                            'tokopedia': 'caption_instagram',
                            'bukalapak': 'caption_instagram',
                            'lazada': 'caption_instagram',
                            'blibli': 'caption_instagram',
                            'tiktok_shop': 'caption_tiktok',
                        };
                        
                        this.form.subcategory = platformMap[this.simpleForm.platform] || 'caption_instagram';
                    }
                    
                    this.form.platform = this.simpleForm.platform;
                    
                    // GOAL-BASED TONE MAPPING (Smart!)
                    const goalToneMap = {
                        'closing': 'persuasive',      // Closing → Persuasive
                        'awareness': 'educational',   // Awareness → Educational
                        'engagement': 'casual',       // Engagement → Casual
                        'viral': 'funny'              // Viral → Funny
                    };
                    this.form.tone = goalToneMap[this.simpleForm.goal] || 'casual';
                    
                    this.form.generate_variations = false; // default 5 variasi
                    this.form.auto_hashtag = true;
                    
                    // Build brief from simple inputs
                    let brief = `Produk: ${this.simpleForm.product_name}\n`;
                    if (this.simpleForm.price) {
                        brief += `Harga: ${this.simpleForm.price}\n`;
                    }
                    brief += `Target: ${this.getTargetLabel(this.simpleForm.target_market)}\n`;
                    brief += `Tujuan: ${this.getGoalLabel(this.simpleForm.goal)}`;
                    
                    // TARGET MARKET-BASED LANGUAGE ADAPTATION
                    const targetLanguageMap = {
                        'remaja': '\n\nGaya Bahasa: Gen Z language (singkat, emoji banyak, slang, relate dengan anak muda)',
                        'ibu_muda': '\n\nGaya Bahasa: Friendly & caring (Bun, Kak, emoji ❤️🙏, bahasa ibu-ibu)',
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
                        alert('Please select a category');
                        return;
                    }
                    
                    if (!this.form.subcategory) {
                        alert('Please select content type');
                        return;
                    }
                    
                    if (!this.form.brief || this.form.brief.length < 10) {
                        alert('Please provide a brief (minimum 10 characters)');
                        return;
                    }
                    
                    if (!this.form.tone) {
                        alert('Please select a tone');
                        return;
                    }
                }
                
                this.loading = true;
                this.result = '';
                
                try {
                    console.log('Sending request with data:', this.form);
                    
                    // 🤖 Add ML data to request
                    const industry = this.getIndustryFromForm();
                    const requestData = {
                        ...this.form,
                        mode: this.mode, // simple or advanced
                        industry: industry, // 🤖 ML: industry
                        goal: this.form.goal || 'closing', // 🤖 ML: goal
                    };
                    
                    const response = await fetch('/api/ai/generate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(requestData)
                    });
                    
                    console.log('Response status:', response.status);
                    console.log('Response content-type:', response.headers.get('content-type'));
                    
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('Non-JSON response:', text.substring(0, 500));
                        throw new Error('Server returned non-JSON response. Please check server logs.');
                    }
                    
                    const data = await response.json();
                    console.log('Response data:', data);
                    
                    if (data.success) {
                        this.result = data.result;
                        this.lastCaptionId = data.caption_id || null; // Store caption ID for rating
                        this.keywordInsights = data.keyword_insights || []; // 🔍 Store keyword insights
                        
                        // 🤖 ML: Store ML data
                        if (data.ml_data) {
                            this.mlPreview = data.ml_data;
                            console.log('ML Data:', data.ml_data);
                        }
                        
                        console.log('Success! Result:', this.result);
                        console.log('Keyword Insights:', this.keywordInsights);
                        
                        // Update first-time status after successful generation
                        if (this.isFirstTimeUser) {
                            this.isFirstTimeUser = false;
                        }
                        
                        // Reset rating state for new caption
                        this.rated = false;
                        this.selectedRating = 0;
                        this.ratingFeedback = '';
                    } else {
                        const errorMessage = data.message || 'Terjadi kesalahan saat generate konten';
                        console.error('API returned error:', errorMessage);
                        
                        // Show user-friendly error
                        if (errorMessage.includes('API key') || errorMessage.includes('tidak valid') || errorMessage.includes('expired')) {
                            alert('⚠️ API Key Gemini Tidak Valid\n\n' + 
                                  'API key sudah expired atau tidak valid.\n\n' +
                                  'Solusi:\n' +
                                  '1. Generate API key baru di:\n' +
                                  '   https://aistudio.google.com/app/apikey\n\n' +
                                  '2. Update di file .env:\n' +
                                  '   GEMINI_API_KEY=your_new_key\n\n' +
                                  '3. Clear cache: php artisan config:clear\n\n' +
                                  'Hubungi administrator untuk bantuan.');
                        } else {
                            alert('Error: ' + errorMessage);
                        }
                        
                        console.error('API Error:', data);
                    }
                } catch (error) {
                    console.error('Generate Error:', error);
                    console.error('Error stack:', error.stack);
                    
                    let errorMsg = 'Terjadi kesalahan: ';
                    if (error.message.includes('JSON')) {
                        errorMsg += 'Server error. Please check:\n' +
                                   '1. Category and Subcategory are selected\n' +
                                   '2. Brief is filled\n' +
                                   '3. Server logs for details';
                    } else {
                        errorMsg += (error.message || 'Tidak dapat terhubung ke server');
                    }
                    alert(errorMsg);
                } finally {
                    this.loading = false;
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
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(textToCopy)
                            .then(() => {
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            })
                            .catch(err => {
                                console.error('Clipboard error:', err);
                                this.fallbackCopy(textToCopy);
                            });
                    } else {
                        // Fallback method
                        this.fallbackCopy(textToCopy);
                    }
                } catch (err) {
                    console.error('Copy error:', err);
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
                    console.error('Fallback copy error:', err);
                    alert('Gagal copy. Silakan copy manual dengan Ctrl+C');
                }
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
                    console.error('Rating error:', error);
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
                            alert('✓ Caption saved for analytics tracking!\n\nYou can now track its performance in Analytics page.');
                        }, 100);
                    } else {
                        alert('Failed to save: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Save error:', error);
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
                    console.error('Load brand voices error:', error);
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
                
                alert('✓ Brand Voice loaded! Tinggal isi brief dan generate.');
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
                        alert('✓ Brand Voice berhasil disimpan!');
                        this.showSaveBrandVoiceModal = false;
                        this.brandVoiceForm = { name: '', brand_description: '', is_default: false };
                        this.loadBrandVoices(); // Reload list
                    } else {
                        alert('Failed: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Save brand voice error:', error);
                    alert('Failed to save brand voice');
                } finally {
                    this.savingBrandVoice = false;
                }
            },
            
            // 🤖 ML FEATURES (Simplified - no upgrade modal)
            mlStatus: null,
            mlPreview: {},
            showMLPreview: false,
            
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
                    console.error('ML Init Error:', error);
                }
            },
            
            // Toggle ML preview
            async toggleMLPreview() {
                if (!this.showMLPreview) {
                    // Get industry from form
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
                        console.error('ML Preview Error:', error);
                    }
                }
                this.showMLPreview = !this.showMLPreview;
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

            // 🔍 ANALYSIS METHODS
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
                        'twitter': 'twitter',
                        'linkedin': 'linkedin'
                    };
                    
                    const rawPlatform = this.form.platform || this.simpleForm.platform || 'instagram';
                    const validPlatform = platformMap[rawPlatform] || rawPlatform;
                    
                    // Ensure platform is one of: instagram, tiktok, facebook, twitter, linkedin
                    const finalPlatform = ['instagram', 'tiktok', 'facebook', 'twitter', 'linkedin'].includes(validPlatform) 
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
                        console.error('Quality response error:', qualityRes.status, text);
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
                        console.error('Sentiment response error:', sentimentRes.status, text);
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
                        console.error('Recommendations response error:', recsRes.status, text);
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
                        throw new Error('⚠️ Gemini API sedang tidak stabil. Coba lagi dalam beberapa saat atau gunakan caption yang lebih pendek.');
                    }

                    this.analysisResult = {
                        quality: quality.data,
                        sentiment: sentiment.data,
                        recommendations: recommendations.data
                    };

                    this.showAnalysis = true;
                } catch (error) {
                    console.error('Analysis error:', error);
                    this.analysisError = error.message || 'Gagal menganalisis caption. Silakan coba lagi.';
                } finally {
                    this.analysisLoading = false;
                }
            },

            useImprovedCaption() {
                if (this.analysisResult?.quality?.improved_caption) {
                    this.result = this.analysisResult.quality.improved_caption;
                    alert('✓ Caption updated!');
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

            // 🤖 ML FEATURES - Additional Methods
            weeklyTrends: {},
            refreshing: false,

            // Refresh ML suggestions manually
            async refreshSuggestions() {
                this.refreshing = true;
                try {
                    const response = await fetch('/api/ml/refresh', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        // Reload preview data
                        const industry = this.getIndustryFromForm();
                        const platform = this.form?.platform || 'instagram';
                        
                        const previewRes = await fetch(`/api/ml/preview?industry=${industry}&platform=${platform}`, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        if (previewRes.ok) {
                            this.mlPreview = await previewRes.json();
                        }

                        const trendsRes = await fetch(`/api/ml/weekly-trends?industry=${industry}`, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        if (trendsRes.ok) {
                            this.weeklyTrends = await trendsRes.json();
                        }
                        
                        alert('✅ ML Suggestions berhasil diperbarui dengan data terbaru!');
                    } else {
                        alert('❌ Gagal memperbarui suggestions. Coba lagi nanti.');
                    }
                } catch (error) {
                    console.error('Refresh error:', error);
                    alert('❌ Terjadi kesalahan saat memperbarui suggestions.');
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
                
                if (hoursAgo < 1) return '🟢 Baru saja diperbarui';
                if (hoursAgo < 6) return '🟢 Diperbarui ' + hoursAgo + ' jam lalu';
                if (hoursAgo < 24) return '🟡 Diperbarui hari ini';
                if (hoursAgo < 48) return '🟡 Diperbarui kemarin';
                return '🔴 Perlu diperbarui';
            },

            // Check if data is fresh (less than 24 hours)
            isDataFresh() {
                if (!this.mlPreview?.generated_at) return false;
                
                const generatedAt = new Date(this.mlPreview.generated_at);
                const now = new Date();
                const hoursAgo = Math.floor((now - generatedAt) / (1000 * 60 * 60));
                
                return hoursAgo < 24;
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

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
