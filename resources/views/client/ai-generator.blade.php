@extends('layouts.client')

@section('title', 'AI Generator')

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

@section('content')
<div class="p-6" x-data="aiGenerator()" x-init="init()">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">AI Copywriting Generator</h1>
        <p class="text-sm text-gray-500 mt-1">Generate copywriting berkualitas dengan AI</p>
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
        <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-gray-50">
            <button @click="mode = 'simple'" 
                    :class="mode === 'simple' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-6 py-2 rounded-md text-sm font-medium transition">
                🎯 Mode Simpel (Untuk Pemula)
            </button>
            <button @click="mode = 'advanced'" 
                    :class="mode === 'advanced' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-6 py-2 rounded-md text-sm font-medium transition">
                ⚙️ Mode Lengkap (Advanced)
            </button>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Form Input -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                
                <!-- SIMPLE MODE -->
                <form @submit.prevent="generateCopywriting" x-show="mode === 'simple'" x-cloak>
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
                <form @submit.prevent="generateCopywriting" x-show="mode === 'advanced'" x-cloak>
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
                            </optgroup>
                            <optgroup label="🏭 Industry Presets (Khusus UMKM)">
                                <option value="industry_presets">Industry Presets (Fashion, Makanan, Jasa)</option>
                            </optgroup>
                            <optgroup label="📝 Kategori Lengkap">
                                <option value="website_landing">Website & Landing Page</option>
                                <option value="ads">Iklan (Ads)</option>
                                <option value="social_media">Social Media Content</option>
                                <option value="marketplace">Marketplace</option>
                                <option value="email_whatsapp">Email & WhatsApp Marketing</option>
                                <option value="proposal_company">Proposal & Company Profile</option>
                                <option value="personal_branding">Personal Branding</option>
                                <option value="ux_writing">UX Writing</option>
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
                    <div class="mb-4" x-show="form.category === 'social_media' || form.category === 'ads'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                        <select x-model="form.platform"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="instagram">Instagram</option>
                            <option value="facebook">Facebook</option>
                            <option value="tiktok">TikTok</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="twitter">Twitter/X</option>
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
            </div>
        </div>

        <!-- Result -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border border-gray-200 p-6 sticky top-6">
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
                        <pre class="whitespace-pre-wrap text-sm text-gray-800" x-text="result"></pre>
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
                    
                    <div class="space-y-2">
                        <button @click="copyToClipboard" 
                                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition text-sm">
                            <span x-show="!copied">Copy to Clipboard</span>
                            <span x-show="copied">Copied!</span>
                        </button>
                        <button @click="saveForAnalytics"
                                :disabled="saved"
                                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-sm disabled:bg-gray-400">
                            <span x-show="!saved">💾 Save for Analytics</span>
                            <span x-show="saved">✓ Saved!</span>
                        </button>
                        <button @click="showSaveBrandVoiceModal = true"
                                class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition text-sm">
                            💼 Save as Brand Voice
                        </button>
                        <button @click="reset"
                                class="w-full border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                            Generate Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
</div>

<script>
    function aiGenerator() {
        return {
            mode: 'simple', // default simple mode
            isFirstTimeUser: true, // will be checked on init
            selectedRating: 0,
            ratingFeedback: '',
            rated: false,
            submittingRating: false,
            lastCaptionId: null,
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
                    {value: 'auto_reply', label: 'Auto-Reply Chat'}
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
            
            async generateCopywriting() {
                // Handle Simple Mode
                if (this.mode === 'simple') {
                    if (!this.simpleForm.product_name || !this.simpleForm.target_market || !this.simpleForm.goal || !this.simpleForm.platform) {
                        alert('Mohon isi semua pertanyaan yang wajib (*)');
                        return;
                    }
                    
                    // Convert simple form to advanced form
                    if (this.simpleForm.business_type) {
                        // If business type selected, use industry preset
                        this.form.category = 'industry_presets';
                        this.form.subcategory = this.simpleForm.business_type;
                    } else {
                        // Otherwise use quick templates
                        this.form.category = 'quick_templates';
                        this.form.subcategory = 'caption_instagram';
                    }
                    
                    this.form.platform = this.simpleForm.platform;
                    this.form.tone = this.simpleForm.goal === 'closing' ? 'persuasive' : 'casual';
                    this.form.generate_variations = false; // default 5 variasi
                    this.form.auto_hashtag = true;
                    
                    // Build brief from simple inputs
                    let brief = `Produk: ${this.simpleForm.product_name}\n`;
                    if (this.simpleForm.price) {
                        brief += `Harga: ${this.simpleForm.price}\n`;
                    }
                    brief += `Target: ${this.getTargetLabel(this.simpleForm.target_market)}\n`;
                    brief += `Tujuan: ${this.getGoalLabel(this.simpleForm.goal)}`;
                    
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
                    
                    // Add mode to request
                    const requestData = {
                        ...this.form,
                        mode: this.mode // simple or advanced
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
                        console.log('Success! Result:', this.result);
                        
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
            
            copyToClipboard() {
                try {
                    // Modern method
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(this.result)
                            .then(() => {
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            })
                            .catch(err => {
                                console.error('Clipboard error:', err);
                                this.fallbackCopy();
                            });
                    } else {
                        // Fallback method
                        this.fallbackCopy();
                    }
                } catch (err) {
                    console.error('Copy error:', err);
                    this.fallbackCopy();
                }
            },
            
            fallbackCopy() {
                try {
                    // Create temporary textarea
                    const textarea = document.createElement('textarea');
                    textarea.value = this.result;
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
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
