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

    {{-- ── Subscription / Quota Banner ── --}}
    @php
        $sub = auth()->user()->currentSubscription();
    @endphp
    @if(!$sub || !$sub->isValid())
    <div class="mb-5 bg-yellow-50 border border-yellow-300 rounded-xl p-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">⚡</span>
            <div>
                <p class="font-semibold text-yellow-900 text-sm">Belum ada langganan aktif</p>
                <p class="text-xs text-yellow-700 mt-0.5">Mulai trial gratis 30 hari untuk menggunakan AI Generator.</p>
            </div>
        </div>
        <a href="{{ route('pricing') }}"
           class="flex-shrink-0 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold rounded-lg transition">
            Mulai Trial Gratis
        </a>
    </div>
    @elseif($sub->isOnTrial())
    <div class="mb-5 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🎉</span>
            <div>
                <p class="font-semibold text-blue-900 text-sm">Trial aktif — {{ $sub->days_remaining }} hari tersisa</p>
                <div class="flex items-center gap-2 mt-1">
                    <div class="w-32 bg-blue-200 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full bg-blue-500" style="width: {{ $sub->trial_progress }}%"></div>
                    </div>
                    <span class="text-xs text-blue-600">Kuota: {{ $sub->remaining_quota }}/{{ $sub->ai_quota_limit }}
                        @if($sub->package && $sub->package->price == 0)
                            · Maks 5/hari
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <a href="{{ route('pricing') }}"
           class="flex-shrink-0 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition">
            Upgrade Paket
        </a>
    </div>
    @elseif($sub->isActive())
    @php $quotaPct = $sub->quota_percentage; @endphp
    @if($quotaPct >= 80)
    <div class="mb-5 bg-orange-50 border border-orange-200 rounded-xl p-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">{{ $quotaPct >= 100 ? '🚫' : '⚠️' }}</span>
            <div>
                <p class="font-semibold text-orange-900 text-sm">
                    {{ $quotaPct >= 100 ? 'Kuota habis!' : 'Kuota hampir habis' }}
                </p>
                <div class="flex items-center gap-2 mt-1">
                    <div class="w-32 bg-orange-200 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $quotaPct >= 100 ? 'bg-red-500' : 'bg-orange-500' }}"
                             style="width: {{ min(100, $quotaPct) }}%"></div>
                    </div>
                    <span class="text-xs text-orange-700">{{ $sub->remaining_quota }} generate tersisa</span>
                </div>
            </div>
        </div>
        <a href="{{ route('pricing') }}"
           class="flex-shrink-0 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-lg transition">
            Upgrade
        </a>
    </div>
    @endif
    @endif

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
            <button @click="generatorType = 'image-analysis'" 
                    :class="generatorType === 'image-analysis' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🔍 AI Image Analysis
            </button>
            <button @click="generatorType = 'video'" 
                    :class="generatorType === 'video' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🎬 AI Video Generator
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
            <button @click="generatorType = 'predictor'" 
                    :class="generatorType === 'predictor' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                📈 Performance Predictor
            </button>
            <button @click="generatorType = 'templates'" 
                    :class="generatorType === 'templates' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                📚 Template Library
            </button>
            <button @click="generatorType = 'multiplatform'" 
                    :class="generatorType === 'multiplatform' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🎯 Multi-Platform Optimizer
            </button>
            <button @click="generatorType = 'repurpose'" 
                    :class="generatorType === 'repurpose' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                ♻️ Content Repurposing
            </button>
            <button @click="generatorType = 'trends'" 
                    :class="generatorType === 'trends' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🔔 Trend Alert
            </button>
            <button @click="generatorType = 'google-ads'" 
                    :class="generatorType === 'google-ads' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🎯 Google Ads
            </button>
            <button @click="generatorType = 'promo-link'" 
                    :class="generatorType === 'promo-link' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🔗 Magic Promo Link
            </button>
            <button @click="generatorType = 'product-explainer'" 
                    :class="generatorType === 'product-explainer' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                💬 Product Explainer
            </button>
            <button @click="generatorType = 'seo-metadata'" 
                    :class="generatorType === 'seo-metadata' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🔍 SEO Metadata
            </button>
            <button @click="generatorType = 'smart-comparison'" 
                    :class="generatorType === 'smart-comparison' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                ⚖️ Smart Comparison
            </button>
            <button @click="generatorType = 'faq-generator'" 
                    :class="generatorType === 'faq-generator' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                ❓ FAQ Generator
            </button>
            <button @click="generatorType = 'reels-hook'" 
                    :class="generatorType === 'reels-hook' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🎬 Reels Hook
            </button>
            <button @click="generatorType = 'quality-badge'" 
                    :class="generatorType === 'quality-badge' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🏅 Quality Badge
            </button>
            <button @click="generatorType = 'discount-campaign'" 
                    :class="generatorType === 'discount-campaign' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🏷️ Discount Campaign
            </button>
            <button @click="generatorType = 'trend-tags'" 
                    :class="generatorType === 'trend-tags' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                📈 Trend Tags
            </button>
            <button @click="generatorType = 'lead-magnet'" 
                    :class="generatorType === 'lead-magnet' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition">
                🧲 Lead Magnet
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
            <div class="bg-white rounded-lg border border-gray-200 p-6" x-show="generatorType === 'text' || generatorType === 'image' || generatorType === 'image-analysis' || generatorType === 'video'" x-cloak>
                
                <!-- SIMPLE MODE -->
                <form @submit.prevent="generateCopywriting" x-show="generatorType === 'text' && mode === 'simple'" x-cloak>
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm text-blue-800 font-medium">✨ Mode Simpel - Jawab pertanyaan mudah, langsung jadi!</p>
                    </div>

                    <!-- 1. Mau bikin konten apa? (Category) -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            1. Mau bikin konten apa? <span class="text-red-600">*</span>
                        </label>
                        <select x-model="simpleForm.content_type" required @change="simpleForm.subcategory = ''; updateSimpleSubcategories()"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih jenis konten</option>
                            <optgroup label="📱 Social Media & Viral">
                                <option value="social_media">Caption Social Media (IG, FB, TikTok)</option>
                                <option value="quick_templates">Template Cepat (Hook, Quotes, CTA)</option>
                                <option value="viral_clickbait">Konten Viral & Clickbait</option>
                            </optgroup>
                            <optgroup label="🏪 Jualan & Bisnis">
                                <option value="industry_presets">Jualan Produk (Fashion, Makanan, dll)</option>
                                <option value="marketplace">Jualan di Marketplace (Shopee, Tokopedia)</option>
                                <option value="ads">Iklan Berbayar (FB Ads, Google Ads)</option>
                                <option value="event_promo">Promo & Event (Flash Sale, Diskon)</option>
                            </optgroup>
                            <optgroup label="🎯 Marketing & Sales">
                                <option value="marketing_funnel">Marketing Funnel (TOFU, MOFU, BOFU)</option>
                                <option value="sales_page">Sales Page (Landing Page Jualan)</option>
                                <option value="lead_magnet">Lead Magnet (Free eBook, Webinar)</option>
                            </optgroup>
                            <optgroup label="💼 Profesional & Kantor">
                                <option value="hr_recruitment">Lowongan Kerja & Recruitment</option>
                                <option value="proposal_company">Proposal & Company Profile</option>
                                <option value="email_whatsapp">Email & WhatsApp Marketing</option>
                            </optgroup>
                            <optgroup label="🎓 Pendidikan & Lembaga">
                                <option value="education_institution">Sekolah, Kampus, Lembaga</option>
                                <option value="academic_writing">Jurnal & Paper Akademik</option>
                            </optgroup>
                            <optgroup label="💌 Undangan & Event">
                                <option value="invitation_event">Undangan (Nikah, Ulang Tahun, dll)</option>
                            </optgroup>
                            <optgroup label="✍️ Konten & Tulisan">
                                <option value="blog_seo">Blog & Artikel SEO</option>
                                <option value="website_landing">Website & Landing Page</option>
                                <option value="ebook_publishing">eBook & Buku Digital</option>
                            </optgroup>
                            <optgroup label="🎨 Branding & Kreatif">
                                <option value="branding_tagline">Tagline & Slogan Brand</option>
                                <option value="personal_branding">Personal Branding (Bio, About Me)</option>
                                <option value="ux_writing">UX Writing (App, Website)</option>
                            </optgroup>
                            <optgroup label="💰 Monetisasi & Penghasilan">
                                <option value="video_monetization">Video (YouTube, TikTok)</option>
                                <option value="freelance">Freelance (Upwork, Fiverr)</option>
                                <option value="affiliate_marketing">Affiliate Marketing</option>
                                <option value="digital_products">Produk Digital</option>
                            </optgroup>
                            <optgroup label="🎭 Drama & Cerita">
                                <option value="short_drama">Short Drama & Story (Drakor/Dracin Style)</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- 2. Jenis Konten Spesifik (Subcategory) -->
                    <div class="mb-4" x-show="simpleForm.content_type && simpleSubcategories.length > 0" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            2. Lebih spesifiknya? <span class="text-red-600">*</span>
                        </label>
                        <select x-model="simpleForm.subcategory" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih jenis konten spesifik</option>
                            <template x-for="sub in simpleSubcategories" :key="sub.value">
                                <option :value="sub.value" x-text="sub.label"></option>
                            </template>
                        </select>
                    </div>

                    <!-- 3. Ceritakan tentang konten kamu (Brief) -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            3. Ceritakan tentang konten kamu <span class="text-red-600">*</span>
                        </label>
                        <textarea x-model="simpleForm.product_name" required rows="3"
                                  placeholder="Contoh: Baju anak umur 2 tahun, bahan katun, warna pink, harga 50rb"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        <p class="text-xs text-gray-500 mt-1">💡 Semakin detail, semakin bagus hasilnya</p>
                    </div>

                    <!-- 4. Target Audience -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            4. Mau ditujukan ke siapa? <span class="text-red-600">*</span>
                        </label>
                        <select x-model="simpleForm.target_market" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih target audience</option>
                            <option value="ibu_muda">Ibu-ibu Muda (25-35 tahun)</option>
                            <option value="remaja">Remaja & Anak Muda (15-25 tahun)</option>
                            <option value="profesional">Pekerja Kantoran (25-40 tahun)</option>
                            <option value="pelajar">Pelajar & Mahasiswa</option>
                            <option value="umum">Umum (Semua kalangan)</option>
                        </select>
                    </div>

                    <!-- 5. Tujuan -->
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

                    <!-- 6. Platform -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            6. Mau posting di mana? <span class="text-red-600">*</span>
                        </label>
                        <select x-model="simpleForm.platform" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="instagram">Instagram</option>
                            <option value="facebook">Facebook</option>
                            <option value="tiktok">TikTok</option>
                            <option value="youtube">YouTube</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="shopee">Shopee</option>
                            <option value="tokopedia">Tokopedia</option>
                        </select>
                    </div>

                    <!-- Generate Button -->
                    <button type="submit" :disabled="loading"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span x-show="!loading">🚀 Bikin Sekarang!</span>
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

                <!-- AI IMAGE ANALYSIS MODE -->
                <form @submit.prevent="analyzeImageWithAI" x-show="generatorType === 'image-analysis'" x-cloak enctype="multipart/form-data">
                    <div class="mb-6 p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg border border-purple-200">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl">🔍</span>
                            <h3 class="text-lg font-semibold text-gray-900">AI Image Analysis dengan Gemini Vision</h3>
                        </div>
                        <p class="text-sm text-purple-800">Upload gambar dan dapatkan analisis mendalam: objek detection, warna dominan, komposisi, mood, dan rekomendasi marketing!</p>
                    </div>

                    <!-- Image Upload for Analysis -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar untuk Analisis *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-purple-400 transition cursor-pointer" 
                             @click="$refs.analysisImageInput.click()"
                             @dragover.prevent="$el.classList.add('border-purple-400', 'bg-purple-50')"
                             @dragleave.prevent="$el.classList.remove('border-purple-400', 'bg-purple-50')"
                             @drop.prevent="handleAnalysisImageDrop($event)">
                            
                            <input type="file" 
                                   x-ref="analysisImageInput" 
                                   @change="handleAnalysisImageSelect($event)" 
                                   accept="image/*" 
                                   class="hidden">
                            
                            <div x-show="!analysisForm.preview">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-600 mb-2 font-medium">Klik atau drag & drop gambar di sini</p>
                                <p class="text-sm text-gray-500">JPG, PNG, WebP (Max 10MB)</p>
                                <p class="text-xs text-gray-400 mt-2">Gemini Vision akan menganalisis gambar secara detail</p>
                            </div>

                            <div x-show="analysisForm.preview" class="relative">
                                <img :src="analysisForm.preview" alt="Preview" class="max-h-80 mx-auto rounded-lg shadow-lg">
                                <button type="button" 
                                        @click.stop="removeAnalysisImage()" 
                                        class="mt-4 px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition">
                                    🗑️ Ganti Gambar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Analysis Options -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Jenis Analisis</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="objects" class="text-purple-600">
                                <span class="text-sm">🎯 Deteksi Objek</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="colors" class="text-purple-600">
                                <span class="text-sm">🎨 Analisis Warna</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="composition" class="text-purple-600">
                                <span class="text-sm">📐 Komposisi</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="mood" class="text-purple-600">
                                <span class="text-sm">😊 Mood & Emosi</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="text" class="text-purple-600">
                                <span class="text-sm">📝 Baca Teks</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="marketing" class="text-purple-600">
                                <span class="text-sm">📈 Tips Marketing</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="quality" class="text-purple-600">
                                <span class="text-sm">⭐ Kualitas Foto</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="suggestions" class="text-purple-600">
                                <span class="text-sm">💡 Saran Perbaikan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Context Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konteks Bisnis (Opsional)</label>
                        <textarea x-model="analysisForm.context" 
                                  rows="3" 
                                  placeholder="Contoh: Ini foto produk makanan untuk Instagram. Saya ingin tahu apakah foto ini menarik untuk customer dan bagaimana cara memperbaikinya..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"></textarea>
                    </div>

                    <!-- Quick Analysis Presets -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Preset Analisis Cepat</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <button type="button" 
                                    @click="setAnalysisPreset('product')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🛍️</span>
                                    <span class="font-medium text-gray-900">Analisis Produk</span>
                                </div>
                                <p class="text-xs text-gray-600">Fokus pada kualitas produk, daya tarik visual, dan tips marketing</p>
                            </button>
                            
                            <button type="button" 
                                    @click="setAnalysisPreset('social')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">📱</span>
                                    <span class="font-medium text-gray-900">Analisis Social Media</span>
                                </div>
                                <p class="text-xs text-gray-600">Komposisi, mood, engagement potential untuk Instagram/TikTok</p>
                            </button>
                            
                            <button type="button" 
                                    @click="setAnalysisPreset('complete')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🔍</span>
                                    <span class="font-medium text-gray-900">Analisis Lengkap</span>
                                </div>
                                <p class="text-xs text-gray-600">Semua aspek: objek, warna, komposisi, mood, teks, marketing</p>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            :disabled="loading || !analysisForm.file || analysisForm.options.length === 0"
                            class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-4 rounded-lg hover:from-purple-700 hover:to-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed shadow-lg">
                        <span x-show="!loading" class="flex items-center justify-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold">🔍 Analisis dengan Gemini Vision</span>
                        </span>
                        <span x-show="loading" class="flex items-center justify-center gap-3">
                            <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="font-semibold">Menganalisis gambar...</span>
                        </span>
                    </button>

                    <!-- Analysis Info -->
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-2">Gemini Vision akan menganalisis:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs text-blue-700">
                                    <div>• Objek dan elemen dalam gambar</div>
                                    <div>• Palet warna dan harmoni</div>
                                    <div>• Komposisi dan rule of thirds</div>
                                    <div>• Mood dan emosi yang terpancar</div>
                                    <div>• Teks yang terbaca (OCR)</div>
                                    <div>• Potensi viral dan engagement</div>
                                    <div>• Kualitas teknis foto</div>
                                    <div>• Saran perbaikan konkret</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- AI VIDEO GENERATOR MODE -->
                <form @submit.prevent="generateVideoContent" x-show="generatorType === 'video'" x-cloak>
                    <div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl">🎬</span>
                            <h3 class="text-lg font-semibold text-gray-900">AI Video Content Generator</h3>
                        </div>
                        <p class="text-sm text-red-800">Generate script video, storyboard, hook viral, dan ide konten video untuk TikTok, Instagram Reels, dan YouTube Shorts!</p>
                    </div>

                    <!-- Video Content Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Konten Video *</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" x-model="videoForm.content_type" value="script" class="text-red-600">
                                <span class="text-sm">📝 Script Video</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" x-model="videoForm.content_type" value="storyboard" class="text-red-600">
                                <span class="text-sm">🎨 Storyboard</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" x-model="videoForm.content_type" value="hook" class="text-red-600">
                                <span class="text-sm">🎯 Hook Viral</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" x-model="videoForm.content_type" value="ideas" class="text-red-600">
                                <span class="text-sm">💡 Ide Konten</span>
                            </label>
                        </div>
                    </div>

                    <!-- Platform Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform Target *</label>
                        <select x-model="videoForm.platform" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Pilih Platform</option>
                            <option value="tiktok">TikTok (15-60 detik)</option>
                            <option value="instagram-reels">Instagram Reels (15-90 detik)</option>
                            <option value="youtube-shorts">YouTube Shorts (15-60 detik)</option>
                            <option value="instagram-story">Instagram Story (15 detik)</option>
                            <option value="facebook-reels">Facebook Reels (15-90 detik)</option>
                            <option value="all-platforms">Semua Platform</option>
                        </select>
                    </div>

                    <!-- Video Duration -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durasi Video</label>
                        <select x-model="videoForm.duration" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="15">15 detik (Ultra Short)</option>
                            <option value="30">30 detik (Short)</option>
                            <option value="60">60 detik (Medium)</option>
                            <option value="90">90 detik (Long)</option>
                        </select>
                    </div>

                    <!-- Product/Topic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Produk/Topik *</label>
                            <input type="text" 
                                   x-model="videoForm.product" 
                                   placeholder="Contoh: Skincare anti aging, Makanan sehat, Tips bisnis" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                            <input type="text" 
                                   x-model="videoForm.target_audience" 
                                   placeholder="Contoh: Wanita 25-35 tahun, Gen Z, Ibu rumah tangga" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Product Image Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Produk (Opsional)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-red-400 transition cursor-pointer" 
                             @click="$refs.videoImageInput.click()"
                             @dragover.prevent="$el.classList.add('border-red-400', 'bg-red-50')"
                             @dragleave.prevent="$el.classList.remove('border-red-400', 'bg-red-50')"
                             @drop.prevent="handleVideoImageDrop($event)">
                            
                            <input type="file" 
                                   x-ref="videoImageInput" 
                                   @change="handleVideoImageSelect($event)" 
                                   accept="image/*" 
                                   class="hidden">
                            
                            <div x-show="!videoForm.image_preview">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-600 mb-2">Upload foto produk untuk video yang lebih spesifik</p>
                                <p class="text-xs text-gray-500">JPG, PNG (Max 5MB) - Opsional tapi sangat direkomendasikan</p>
                            </div>

                            <div x-show="videoForm.image_preview" class="relative">
                                <img :src="videoForm.image_preview" alt="Product Preview" class="max-h-48 mx-auto rounded-lg">
                                <button type="button" 
                                        @click.stop="removeVideoImage()" 
                                        class="mt-3 text-sm text-red-600 hover:text-red-700">
                                    🗑️ Hapus Foto
                                </button>
                            </div>
                        </div>
                        
                        <!-- Image Benefits Info -->
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex gap-2">
                                <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-xs text-blue-800">
                                    <p class="font-medium mb-1">💡 Dengan foto produk, AI akan generate:</p>
                                    <div class="grid grid-cols-2 gap-1 text-xs text-blue-700">
                                        <div>• Scene-by-scene visual yang detail</div>
                                        <div>• Camera angle yang optimal</div>
                                        <div>• Props dan setting yang cocok</div>
                                        <div>• Lighting recommendations</div>
                                        <div>• Color scheme yang harmonis</div>
                                        <div>• Product showcase yang menarik</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Video Goal -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan Video *</label>
                        <select x-model="videoForm.goal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Pilih Tujuan</option>
                            <option value="viral">Viral & Awareness</option>
                            <option value="sales">Jualan & Conversion</option>
                            <option value="education">Edukasi & Tips</option>
                            <option value="entertainment">Hiburan & Engagement</option>
                            <option value="testimonial">Testimoni & Review</option>
                            <option value="behind-scenes">Behind The Scenes</option>
                            <option value="tutorial">Tutorial & How-to</option>
                        </select>
                    </div>

                    <!-- Video Style -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Style Video</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="trending" class="text-red-600">
                                <span class="text-sm">🔥 Trending</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="funny" class="text-red-600">
                                <span class="text-sm">😂 Funny</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="emotional" class="text-red-600">
                                <span class="text-sm">😢 Emotional</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="professional" class="text-red-600">
                                <span class="text-sm">👔 Professional</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="casual" class="text-red-600">
                                <span class="text-sm">😎 Casual</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="dramatic" class="text-red-600">
                                <span class="text-sm">🎭 Dramatic</span>
                            </label>
                        </div>
                    </div>

                    <!-- Additional Context -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konteks Tambahan (Opsional)</label>
                        <textarea x-model="videoForm.context" 
                                  rows="3" 
                                  placeholder="Contoh: Video ini untuk promosi flash sale, target engagement tinggi, harus ada call to action yang kuat..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
                    </div>

                    <!-- Quick Video Presets -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Preset Video Cepat</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <button type="button" 
                                    @click="setVideoPreset('viral-tiktok')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-red-400 hover:bg-red-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🔥</span>
                                    <span class="font-medium text-gray-900">Viral TikTok</span>
                                </div>
                                <p class="text-xs text-gray-600">Hook kuat, trending sound, call to action viral</p>
                            </button>
                            
                            <button type="button" 
                                    @click="setVideoPreset('product-demo')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-red-400 hover:bg-red-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🛍️</span>
                                    <span class="font-medium text-gray-900">Product Demo</span>
                                </div>
                                <p class="text-xs text-gray-600">Showcase produk, benefit, testimoni, closing</p>
                            </button>
                            
                            <button type="button" 
                                    @click="setVideoPreset('educational')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-red-400 hover:bg-red-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🎓</span>
                                    <span class="font-medium text-gray-900">Educational</span>
                                </div>
                                <p class="text-xs text-gray-600">Tips, tutorial, step-by-step, value content</p>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            :disabled="loading || !videoForm.content_type || !videoForm.platform || !videoForm.product || !videoForm.goal"
                            class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white py-4 rounded-lg hover:from-red-700 hover:to-pink-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed shadow-lg">
                        <span x-show="!loading" class="flex items-center justify-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-semibold">🎬 Generate Konten Video</span>
                        </span>
                        <span x-show="loading" class="flex items-center justify-center gap-3">
                            <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="font-semibold">Generating video content...</span>
                        </span>
                    </button>

                    <!-- Video Info -->
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-2">AI akan generate:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs text-blue-700">
                                    <div>• Script lengkap dengan timing</div>
                                    <div>• Hook pembuka yang viral</div>
                                    <div>• Storyboard visual per scene</div>
                                    <div>• Call to action yang kuat</div>
                                    <div>• Hashtag trending yang relevan</div>
                                    <div>• Tips shooting dan editing</div>
                                    <div>• Music/sound recommendations</div>
                                    <div>• Engagement optimization tips</div>
                                </div>
                            </div>
                        </div>
                    </div>
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

            <!-- 📈 CAPTION PERFORMANCE PREDICTOR -->
            <div x-show="generatorType === 'predictor'" x-cloak class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">📈 Caption Performance Predictor</h3>
                    <p class="text-gray-600">Prediksi performa caption sebelum posting! Dapatkan score, saran improvement, dan A/B testing variants.</p>
                </div>

                <!-- Input Form -->
                <form @submit.prevent="predictCaptionPerformance()" class="mb-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Caption yang mau dianalisis <span class="text-red-600">*</span>
                        </label>
                        <textarea x-model="predictorForm.caption" required rows="4"
                                  placeholder="Paste caption kamu di sini untuk dianalisis..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                            <select x-model="predictorForm.platform" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="instagram">Instagram</option>
                                <option value="facebook">Facebook</option>
                                <option value="tiktok">TikTok</option>
                                <option value="youtube">YouTube</option>
                                <option value="linkedin">LinkedIn</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                            <select x-model="predictorForm.industry" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="fashion">Fashion</option>
                                <option value="food">Food & Beverage</option>
                                <option value="beauty">Beauty & Skincare</option>
                                <option value="tech">Technology</option>
                                <option value="fitness">Fitness & Health</option>
                                <option value="education">Education</option>
                                <option value="general">General/Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                            <select x-model="predictorForm.target_audience" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="remaja">Remaja (15-25)</option>
                                <option value="dewasa_muda">Dewasa Muda (25-35)</option>
                                <option value="profesional">Profesional (30-45)</option>
                                <option value="keluarga">Keluarga (35-50)</option>
                                <option value="general">Semua Umur</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" :disabled="predictorLoading || !predictorForm.caption"
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!predictorLoading">🔮 Prediksi Performance</span>
                        <span x-show="predictorLoading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menganalisis...
                        </span>
                    </button>
                </form>

                <!-- Results -->
                <div x-show="predictionResults" x-cloak class="space-y-6">
                    <!-- Quality Score -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-6 border border-blue-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">💯 Quality Score</h4>
                            <div class="text-right">
                                <div class="text-3xl font-bold" :class="getScoreColor(predictionResults?.quality_score?.total_score)" x-text="predictionResults?.quality_score?.total_score || 0"></div>
                                <div class="text-sm text-gray-600">Grade: <span class="font-semibold" x-text="predictionResults?.quality_score?.grade || 'N/A'"></span></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-lg font-semibold text-blue-600" x-text="predictionResults?.quality_score?.breakdown?.structure || 0"></div>
                                <div class="text-xs text-gray-600">Structure</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-green-600" x-text="predictionResults?.quality_score?.breakdown?.engagement || 0"></div>
                                <div class="text-xs text-gray-600">Engagement</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-purple-600" x-text="predictionResults?.quality_score?.breakdown?.quality || 0"></div>
                                <div class="text-xs text-gray-600">Quality</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-orange-600" x-text="predictionResults?.quality_score?.breakdown?.performance || 0"></div>
                                <div class="text-xs text-gray-600">Performance</div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Prediction -->
                    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">📊 Prediksi Engagement</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600" x-text="(predictionResults?.prediction?.engagement_rate || 0) + '%'"></div>
                                <div class="text-sm text-gray-600">Total Engagement</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-500" x-text="(predictionResults?.prediction?.likes_rate || 0) + '%'"></div>
                                <div class="text-sm text-gray-600">Likes Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-500" x-text="(predictionResults?.prediction?.comments_rate || 0) + '%'"></div>
                                <div class="text-sm text-gray-600">Comments Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-500" x-text="(predictionResults?.prediction?.shares_rate || 0) + '%'"></div>
                                <div class="text-sm text-gray-600">Shares Rate</div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                  :class="getConfidenceColor(predictionResults?.prediction?.confidence)">
                                <span class="w-2 h-2 rounded-full mr-2" :class="getConfidenceDot(predictionResults?.prediction?.confidence)"></span>
                                Confidence: <span x-text="predictionResults?.prediction?.confidence || 'medium'"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Improvement Suggestions -->
                    <div x-show="predictionResults?.improvements?.length > 0" class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">💡 Saran Improvement</h4>
                        <div class="space-y-4">
                            <template x-for="improvement in predictionResults?.improvements || []" :key="improvement.type">
                                <div class="flex items-start space-x-3 p-4 bg-white rounded-lg border border-yellow-100">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              :class="getPriorityColor(improvement.priority)">
                                            <span x-text="improvement.priority"></span>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="font-semibold text-gray-900" x-text="improvement.title"></h5>
                                        <p class="text-sm text-gray-600 mt-1" x-text="improvement.description"></p>
                                        <div x-show="improvement.examples" class="mt-2">
                                            <p class="text-xs text-gray-500 mb-1">Contoh:</p>
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="example in improvement.examples || []" :key="example">
                                                    <span class="inline-block px-2 py-1 bg-gray-100 text-xs rounded" x-text="example"></span>
                                                </template>
                                            </div>
                                        </div>
                                        <div x-show="improvement.impact" class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                                📈 <span x-text="improvement.impact"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- A/B Testing Variant -->
                    <div x-show="predictionResults?.ab_variant" class="bg-purple-50 rounded-lg p-6 border border-purple-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">🧪 A/B Testing Variant</h4>
                            <button @click="generateMoreVariants()" 
                                    :disabled="variantsLoading"
                                    class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!variantsLoading">Generate More Variants</span>
                                <span x-show="variantsLoading" class="flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Generating...
                                </span>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Original -->
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h5 class="font-semibold text-gray-900 mb-2">📝 Original (A)</h5>
                                <div class="text-sm text-gray-700 bg-gray-50 p-3 rounded border" x-text="predictorForm.caption"></div>
                            </div>
                            
                            <!-- Variant -->
                            <div class="bg-white rounded-lg p-4 border border-purple-200">
                                <h5 class="font-semibold text-gray-900 mb-2">🔄 Variant (B)</h5>
                                <div class="text-sm text-gray-700 bg-purple-50 p-3 rounded border" x-text="predictionResults?.ab_variant?.variant_caption"></div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="bg-white p-3 rounded border">
                                <div class="font-medium text-gray-900">Test Focus</div>
                                <div class="text-gray-600" x-text="predictionResults?.ab_variant?.test_focus"></div>
                            </div>
                            <div class="bg-white p-3 rounded border">
                                <div class="font-medium text-gray-900">Duration</div>
                                <div class="text-gray-600" x-text="predictionResults?.ab_variant?.recommended_duration"></div>
                            </div>
                            <div class="bg-white p-3 rounded border">
                                <div class="font-medium text-gray-900">Sample Size</div>
                                <div class="text-gray-600" x-text="predictionResults?.ab_variant?.sample_size_needed"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Best Posting Time -->
                    <div class="bg-orange-50 rounded-lg p-6 border border-orange-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">⏰ Best Posting Time</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">🎯 Recommended Times Today</h5>
                                <div class="space-y-2">
                                    <template x-for="time in predictionResults?.best_posting_time?.best_times_today || []" :key="time">
                                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                                            <span x-text="time"></span>
                                            <span class="text-xs text-green-600 font-medium">Optimal</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">📅 Best Days</h5>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="day in predictionResults?.best_posting_time?.best_days || []" :key="day">
                                        <span class="px-3 py-1 bg-orange-100 text-orange-800 text-sm rounded-full" x-text="day"></span>
                                    </template>
                                </div>
                                <div class="mt-4">
                                    <h5 class="font-medium text-gray-900 mb-2">🚫 Avoid Times</h5>
                                    <div class="text-sm text-gray-600">
                                        <template x-for="time in predictionResults?.best_posting_time?.avoid_times || []" :key="time">
                                            <span class="inline-block mr-2" x-text="time"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 📚 TEMPLATE LIBRARY -->
        <div x-show="generatorType === 'templates'" x-cloak class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">📚 Template Library</h3>
                <p class="text-gray-600">500+ template siap pakai untuk berbagai kebutuhan konten</p>
            </div>

            <!-- Template Filters -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select x-model="templateFilters.category" @change="filterTemplates()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kategori</option>
                        <option value="viral_clickbait">🔥 Viral & Clickbait</option>
                        <option value="trend_fresh_ideas">✨ Trend & Fresh Ideas</option>
                        <option value="event_promo">🎉 Event & Promo</option>
                        <option value="hr_recruitment">💼 HR & Recruitment</option>
                        <option value="branding_tagline">🎯 Branding & Tagline</option>
                        <option value="education">🎓 Education & Institution</option>
                        <option value="monetization">💰 Monetization</option>
                        <option value="video_monetization">📹 Video Content</option>
                        <option value="freelance">💻 Freelance</option>
                        <option value="digital_products">📱 Digital Products</option>
                        <option value="ebook_publishing">📚 eBook & Publishing</option>
                        <option value="academic_writing">🎓 Academic Writing</option>
                        <option value="affiliate_marketing">🤝 Affiliate Marketing</option>
                        <option value="blog_seo">📝 Blog & SEO</option>
                    </select>
                </div>

                <!-- Platform Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                    <select x-model="templateFilters.platform" @change="filterTemplates()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Platform</option>
                        <option value="instagram">Instagram</option>
                        <option value="facebook">Facebook</option>
                        <option value="tiktok">TikTok</option>
                        <option value="youtube">YouTube</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="twitter">Twitter/X</option>
                        <option value="shopee">Shopee</option>
                        <option value="tokopedia">Tokopedia</option>
                    </select>
                </div>

                <!-- Tone Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tone</label>
                    <select x-model="templateFilters.tone" @change="filterTemplates()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tone</option>
                        <option value="casual">Casual</option>
                        <option value="formal">Formal</option>
                        <option value="persuasive">Persuasive</option>
                        <option value="funny">Funny</option>
                        <option value="emotional">Emotional</option>
                        <option value="educational">Educational</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Template</label>
                    <input type="text" x-model="templateFilters.search" @input="filterTemplates()" 
                           placeholder="Cari template..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Template Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <template x-for="template in filteredTemplates" :key="template.id">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                         @click="selectTemplate(template)">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 mb-1" x-text="template.title"></h4>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span x-text="template.category_label"></span>
                                    <span>•</span>
                                    <span x-text="template.platform || 'Universal'"></span>
                                </div>
                            </div>
                            <button @click.stop="toggleFavorite(template)" 
                                    :class="template.is_favorite ? 'text-red-500' : 'text-gray-400'"
                                    class="hover:text-red-500 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2" x-text="template.description"></p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded" x-text="template.tone || 'Universal'"></span>
                                <span class="text-xs text-gray-500" x-text="template.usage_count + ' kali digunakan'"></span>
                            </div>
                            <button @click.stop="useTemplate(template)" 
                                    class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition">
                                Gunakan
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="filteredTemplates.length === 0" x-cloak class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada template ditemukan</h3>
                <p class="text-gray-500">Coba ubah filter atau kata kunci pencarian</p>
            </div>

            <!-- Template Preview Modal -->
            <div x-show="selectedTemplate" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="selectedTemplate?.title"></h3>
                        <button @click="selectedTemplate = null" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                            <span x-text="selectedTemplate?.category_label"></span>
                            <span>•</span>
                            <span x-text="selectedTemplate?.platform || 'Universal'"></span>
                            <span>•</span>
                            <span x-text="selectedTemplate?.tone || 'Universal'"></span>
                        </div>
                        <p class="text-gray-700 mb-4" x-text="selectedTemplate?.description"></p>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-2">Template Format:</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-800 whitespace-pre-wrap" x-text="selectedTemplate?.format"></pre>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button @click="toggleFavorite(selectedTemplate)" 
                                :class="selectedTemplate?.is_favorite ? 'text-red-500' : 'text-gray-500'"
                                class="flex items-center space-x-2 hover:text-red-500 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                            </svg>
                            <span x-text="selectedTemplate?.is_favorite ? 'Hapus dari Favorit' : 'Tambah ke Favorit'"></span>
                        </button>
                        <div class="space-x-3">
                            <button @click="selectedTemplate = null" 
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                Tutup
                            </button>
                            <button @click="useTemplate(selectedTemplate)" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Gunakan Template
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🎯 MULTI-PLATFORM OPTIMIZER -->
        <div x-show="generatorType === 'multiplatform'" x-cloak class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">🎯 Multi-Platform Optimizer</h3>
                <p class="text-gray-600">Generate 1 caption → Auto-optimize untuk 6+ platform sekaligus dengan format yang tepat!</p>
                <div class="mt-3 p-3 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-800"><strong>💡 Keunggulan:</strong> ChatGPT gak bisa auto-optimize per platform seperti ini!</p>
                </div>
            </div>

            <!-- Input Form -->
            <form @submit.prevent="generateMultiPlatform()" class="mb-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Konten yang mau dioptimasi <span class="text-red-600">*</span>
                    </label>
                    <textarea x-model="multiPlatformForm.content" required rows="4"
                              placeholder="Tulis konten dasar kamu di sini. AI akan otomatis optimize untuk setiap platform..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1">💡 Tulis konten dasar, AI akan sesuaikan panjang, tone, dan format untuk setiap platform</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Bisnis</label>
                        <select x-model="multiPlatformForm.business_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="fashion">Fashion & Pakaian</option>
                            <option value="food">Makanan & Minuman</option>
                            <option value="beauty">Beauty & Skincare</option>
                            <option value="tech">Technology</option>
                            <option value="education">Education</option>
                            <option value="service">Jasa & Layanan</option>
                            <option value="general">General/Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                        <select x-model="multiPlatformForm.target_audience" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="remaja">Remaja (15-25)</option>
                            <option value="dewasa_muda">Dewasa Muda (25-35)</option>
                            <option value="profesional">Profesional (30-45)</option>
                            <option value="keluarga">Keluarga (35-50)</option>
                            <option value="general">Semua Umur</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan</label>
                        <select x-model="multiPlatformForm.goal" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="awareness">Brand Awareness</option>
                            <option value="engagement">Engagement & Interaksi</option>
                            <option value="conversion">Sales & Conversion</option>
                            <option value="traffic">Drive Traffic</option>
                            <option value="viral">Viral & Share</option>
                        </select>
                    </div>
                </div>

                <!-- Platform Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Platform yang mau dioptimasi</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('instagram') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="instagram" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">📸</div>
                                <div class="text-xs font-medium">Instagram</div>
                                <div class="text-xs text-gray-500">2200 char</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('tiktok') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="tiktok" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">🎵</div>
                                <div class="text-xs font-medium">TikTok</div>
                                <div class="text-xs text-gray-500">150 char</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('facebook') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="facebook" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">👥</div>
                                <div class="text-xs font-medium">Facebook</div>
                                <div class="text-xs text-gray-500">Storytelling</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('twitter') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="twitter" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">🐦</div>
                                <div class="text-xs font-medium">Twitter/X</div>
                                <div class="text-xs text-gray-500">280 char</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('whatsapp') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="whatsapp" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">💬</div>
                                <div class="text-xs font-medium">WhatsApp</div>
                                <div class="text-xs text-gray-500">Short & Punchy</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('marketplace') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="marketplace" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">🛒</div>
                                <div class="text-xs font-medium">Marketplace</div>
                                <div class="text-xs text-gray-500">SEO Optimized</div>
                            </div>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">💡 Pilih minimal 2 platform untuk optimasi</p>
                </div>

                <!-- Generate Button -->
                <button type="submit" :disabled="multiPlatformLoading || multiPlatformForm.platforms.length < 2"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!multiPlatformLoading">🎯 Generate Multi-Platform Content</span>
                    <span x-show="multiPlatformLoading" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Optimizing for <span x-text="multiPlatformForm.platforms.length"></span> platforms...
                    </span>
                </button>
            </form>

            <!-- Results -->
            <div x-show="multiPlatformResults" x-cloak class="space-y-6">
                <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-4 border border-green-200">
                    <h4 class="font-semibold text-gray-900 mb-2">✅ Optimasi Selesai!</h4>
                    <p class="text-sm text-gray-700">Konten berhasil dioptimasi untuk <span class="font-semibold" x-text="Object.keys(multiPlatformResults || {}).length"></span> platform dengan format yang tepat.</p>
                </div>

                <!-- Platform Results -->
                <template x-for="(result, platform) in multiPlatformResults" :key="platform">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <!-- Platform Header -->
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="text-lg" x-text="getPlatformEmoji(platform)"></span>
                                    <div>
                                        <h5 class="font-semibold text-gray-900" x-text="getPlatformName(platform)"></h5>
                                        <p class="text-xs text-gray-600" x-text="getPlatformSpecs(platform)"></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full" x-text="result.char_count + ' chars'"></span>
                                    <button @click="copyPlatformContent(platform, result.content)" 
                                            class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition">
                                        📋 Copy
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Platform Content -->
                        <div class="p-4">
                            <div class="bg-gray-50 rounded-lg p-4 mb-3">
                                <pre class="whitespace-pre-wrap text-sm text-gray-800" x-text="result.content"></pre>
                            </div>
                            
                            <!-- Platform-specific features -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <div x-show="result.hashtags">
                                    <h6 class="font-medium text-gray-700 mb-1">🏷️ Hashtags:</h6>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="hashtag in result.hashtags" :key="hashtag">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded" x-text="hashtag"></span>
                                        </template>
                                    </div>
                                </div>
                                
                                <div x-show="result.optimization_notes">
                                    <h6 class="font-medium text-gray-700 mb-1">💡 Optimasi:</h6>
                                    <ul class="text-gray-600 space-y-1">
                                        <template x-for="note in result.optimization_notes" :key="note">
                                            <li class="flex items-start">
                                                <span class="text-green-500 mr-1">•</span>
                                                <span x-text="note"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Bulk Actions -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h5 class="font-medium text-gray-900 mb-3">🚀 Bulk Actions</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <button @click="copyAllPlatforms()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                            📋 Copy All Platforms
                        </button>
                        <button @click="exportMultiPlatform('txt')" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                            📄 Export as TXT
                        </button>
                        <button @click="exportMultiPlatform('csv')" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                            📊 Export as CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        </div>

        <!-- Result Section -->
        <div x-show="generatorType === 'text' || generatorType === 'image' || generatorType === 'image-analysis' || generatorType === 'video'" x-cloak class="mt-6">
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
                    
                    <!-- 🔍 Caption Optimizer Tools -->
                    @include('client.partials.caption-optimizer')
                    
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
        
        <!-- ♻️ CONTENT REPURPOSING -->
        <div x-show="generatorType === 'repurpose'" x-cloak class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">♻️ Content Repurposing</h3>
                <p class="text-gray-600">Ubah 1 konten jadi 10+ variasi untuk berbagai platform dan format!</p>
                <div class="mt-3 p-3 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg border border-green-200">
                    <p class="text-sm text-green-800">
                        <strong>💡 Value:</strong> Hemat waktu 90%! ChatGPT gak bisa auto-suggest repurposing ideas seperti ini.
                    </p>
                </div>
            </div>

            <!-- Input Original Content -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Konten Asli</label>
                <textarea x-model="repurposeForm.originalContent" 
                          placeholder="Paste konten yang ingin di-repurpose di sini..."
                          rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                <p class="text-xs text-gray-500 mt-1">Bisa berupa caption, artikel, email, atau konten apapun</p>
            </div>

            <!-- Content Type Detection -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Konten Asli</label>
                    <select x-model="repurposeForm.originalType" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Auto-detect</option>
                        <option value="caption">Caption Social Media</option>
                        <option value="article">Artikel/Blog</option>
                        <option value="email">Email Marketing</option>
                        <option value="ad_copy">Iklan</option>
                        <option value="product_desc">Deskripsi Produk</option>
                        <option value="video_script">Script Video</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Industri/Niche</label>
                    <select x-model="repurposeForm.industry" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="general">General</option>
                        <option value="fashion">Fashion & Beauty</option>
                        <option value="food">Food & Beverage</option>
                        <option value="tech">Technology</option>
                        <option value="health">Health & Fitness</option>
                        <option value="education">Education</option>
                        <option value="business">Business & Finance</option>
                        <option value="travel">Travel & Lifestyle</option>
                        <option value="ecommerce">E-commerce</option>
                    </select>
                </div>
            </div>

            <!-- Repurposing Options -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Format Repurposing</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="option in repurposeOptions" :key="option.value">
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" 
                                   :value="option.value"
                                   x-model="repurposeForm.selectedFormats"
                                   class="mr-3 text-green-600 focus:ring-green-500">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <span x-text="option.icon" class="mr-2"></span>
                                    <span class="font-medium text-sm" x-text="option.label"></span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1" x-text="option.description"></p>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <!-- Advanced Options -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-900 mb-3">Opsi Lanjutan</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="repurposeForm.includeHashtags" 
                                   class="mr-2 text-green-600 focus:ring-green-500">
                            <span class="text-sm">Include hashtags yang relevan</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="repurposeForm.includeCTA" 
                                   class="mr-2 text-green-600 focus:ring-green-500">
                            <span class="text-sm">Tambahkan Call-to-Action</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="repurposeForm.optimizeLength" 
                                   class="mr-2 text-green-600 focus:ring-green-500">
                            <span class="text-sm">Optimize panjang per platform</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="repurposeForm.generateVariations" 
                                   class="mr-2 text-green-600 focus:ring-green-500">
                            <span class="text-sm">Generate 3 variasi per format</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Generate Button -->
            <button @click="generateRepurposedContent()" 
                    :disabled="!repurposeForm?.originalContent || (repurposeForm?.selectedFormats?.length || 0) === 0 || repurposeLoading"
                    class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white py-3 px-6 rounded-lg hover:from-green-700 hover:to-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                <span x-show="!repurposeLoading" x-cloak class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    ♻️ Repurpose Content (<span x-text="repurposeForm?.selectedFormats?.length || 0"></span> format)
                </span>
                <span x-show="repurposeLoading" x-cloak class="flex items-center justify-center">
                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent mr-2"></div>
                    Generating repurposed content...
                </span>
            </button>

            <!-- Results -->
            <div x-show="repurposeResults && repurposeResults.length > 0" x-cloak class="mt-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">📋 Hasil Repurposing</h4>
                
                <!-- Results Grid -->
                <div class="space-y-6">
                    <template x-for="result in repurposeResults" :key="result.format">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <span x-text="result.icon" class="mr-2 text-lg"></span>
                                    <h5 class="font-medium text-gray-900" x-text="result.title"></h5>
                                    <span class="ml-2 text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full" 
                                          x-text="result.platform"></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500" x-text="result.content.length + ' chars'"></span>
                                    <button @click="copyRepurposedContent(result.content)" 
                                            class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">
                                        Copy
                                    </button>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                <pre class="whitespace-pre-wrap text-sm text-gray-800" x-text="result.content"></pre>
                            </div>
                            
                            <!-- Variations if enabled -->
                            <template x-if="result.variations && result.variations.length > 0">
                                <div class="mt-3">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Variasi:</p>
                                    <div class="space-y-2">
                                        <template x-for="(variation, index) in result.variations" :key="index">
                                            <div class="bg-white border border-gray-100 rounded p-2">
                                                <div class="flex justify-between items-start">
                                                    <pre class="whitespace-pre-wrap text-xs text-gray-700 flex-1" x-text="variation"></pre>
                                                    <button @click="copyRepurposedContent(variation)" 
                                                            class="ml-2 text-xs bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600">
                                                        Copy
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Bulk Actions -->
                <div class="mt-6 flex flex-wrap gap-3">
                    <button @click="copyAllRepurposed()" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        📋 Copy All Content
                    </button>
                    <button @click="exportRepurposed('txt')" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm">
                        📄 Export as TXT
                    </button>
                    <button @click="exportRepurposed('csv')" 
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                        📊 Export as CSV
                    </button>
                    <button @click="resetRepurpose()" 
                            class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                        🔄 Reset
                    </button>
                </div>
            </div>
        </div>
        
        <!-- 🔔 TREND ALERT -->
        <div x-show="generatorType === 'trends'" x-cloak class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">🔔 Trend Alert Indonesia</h3>
                <p class="text-gray-600">Stay updated dengan trending topics terbaru dan generate konten viral!</p>
                <div class="mt-3 p-3 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg border border-red-200">
                    <p class="text-sm text-red-800">💡 <strong>Pro Tip:</strong> Manfaatkan trending topics untuk boost engagement dan reach konten Anda!</p>
                </div>
            </div>

            <!-- Trend Categories -->
            <div class="mb-6">
                <div class="flex flex-wrap gap-2 mb-4">
                    <button @click="trendCategory = 'daily'" 
                            :class="trendCategory === 'daily' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        🔥 Daily Trends
                    </button>
                    <button @click="trendCategory = 'viral'" 
                            :class="trendCategory === 'viral' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        📈 Viral Content
                    </button>
                    <button @click="trendCategory = 'seasonal'" 
                            :class="trendCategory === 'seasonal' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        🗓️ Seasonal Events
                    </button>
                    <button @click="trendCategory = 'national'" 
                            :class="trendCategory === 'national' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        🇮🇩 National Days
                    </button>
                </div>
            </div>

            <!-- Daily Trends -->
            <div x-show="trendCategory === 'daily'" x-cloak>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900">🔥 Trending Topics Hari Ini</h4>
                    <button @click="refreshTrends()" 
                            :disabled="trendsLoading"
                            class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition disabled:opacity-50">
                        <span x-show="!trendsLoading">🔄 Refresh</span>
                        <span x-show="trendsLoading">Loading...</span>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <template x-for="trend in dailyTrends" :key="trend.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                             @click="selectTrend(trend)">
                            <div class="flex items-start justify-between mb-2">
                                <h5 class="font-medium text-gray-900" x-text="trend.title"></h5>
                                <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded-full" x-text="trend.category"></span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2" x-text="trend.description"></p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span x-text="'🔥 ' + trend.popularity + ' mentions'"></span>
                                <span x-text="trend.timeAgo"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Viral Content Ideas -->
            <div x-show="trendCategory === 'viral'" x-cloak>
                <h4 class="text-lg font-semibold text-gray-900 mb-4">📈 Viral Content Ideas</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <template x-for="idea in viralIdeas" :key="idea.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                             @click="selectTrend(idea)">
                            <div class="flex items-start justify-between mb-2">
                                <h5 class="font-medium text-gray-900" x-text="idea.title"></h5>
                                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded-full" x-text="idea.type"></span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2" x-text="idea.description"></p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span x-text="'📊 ' + idea.engagement + ' avg engagement'"></span>
                                <span x-text="idea.platform"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Seasonal Events -->
            <div x-show="trendCategory === 'seasonal'" x-cloak>
                <h4 class="text-lg font-semibold text-gray-900 mb-4">🗓️ Upcoming Seasonal Events</h4>
                
                <div class="space-y-4 mb-6">
                    <template x-for="event in seasonalEvents" :key="event.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                             @click="selectTrend(event)">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center">
                                    <span x-text="event.icon" class="text-2xl mr-3"></span>
                                    <div>
                                        <h5 class="font-medium text-gray-900" x-text="event.title"></h5>
                                        <p class="text-sm text-gray-600" x-text="event.date"></p>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full" x-text="event.daysLeft + ' days'"></span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2" x-text="event.description"></p>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="tag in event.contentIdeas" :key="tag">
                                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded" x-text="tag"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- National Days -->
            <div x-show="trendCategory === 'national'" x-cloak>
                <h4 class="text-lg font-semibold text-gray-900 mb-4">🇮🇩 National Days & Commemorations</h4>
                
                <div class="space-y-4 mb-6">
                    <template x-for="day in nationalDays" :key="day.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                             @click="selectTrend(day)">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center">
                                    <span x-text="day.icon" class="text-2xl mr-3"></span>
                                    <div>
                                        <h5 class="font-medium text-gray-900" x-text="day.title"></h5>
                                        <p class="text-sm text-gray-600" x-text="day.date"></p>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full" x-text="day.category"></span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2" x-text="day.description"></p>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="hashtag in day.hashtags" :key="hashtag">
                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded" x-text="hashtag"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Selected Trend & Content Generation -->
            <div x-show="selectedTrend" x-cloak class="mt-6 border-t pt-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">✨ Generate Content dari Trend</h4>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center mb-2">
                        <span x-text="selectedTrend?.icon || '🔥'" class="text-xl mr-2"></span>
                        <h5 class="font-medium text-gray-900" x-text="selectedTrend?.title"></h5>
                    </div>
                    <p class="text-sm text-gray-600" x-text="selectedTrend?.description"></p>
                </div>

                <!-- Content Type Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Konten yang Mau Dibuat:</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="caption" class="mr-2">
                            <span class="text-sm">📱 Caption IG/FB</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="story" class="mr-2">
                            <span class="text-sm">📸 IG Story</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="tiktok" class="mr-2">
                            <span class="text-sm">🎵 TikTok Script</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="thread" class="mr-2">
                            <span class="text-sm">🧵 Twitter Thread</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="blog" class="mr-2">
                            <span class="text-sm">📝 Blog Post</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="email" class="mr-2">
                            <span class="text-sm">📧 Email Marketing</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="ads" class="mr-2">
                            <span class="text-sm">💰 FB/IG Ads</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="whatsapp" class="mr-2">
                            <span class="text-sm">💬 WhatsApp Blast</span>
                        </label>
                    </div>
                </div>

                <!-- Business Context -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bisnis/Produk Anda:</label>
                    <input type="text" x-model="trendBusinessContext" 
                           placeholder="Contoh: Toko baju online, Warung makan, Jasa desain, dll"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Generate Button -->
                <button @click="generateTrendContent()" 
                        :disabled="!selectedTrend || trendContentTypes.length === 0 || !trendBusinessContext || trendLoading"
                        class="w-full bg-gradient-to-r from-red-600 to-orange-600 text-white py-3 px-6 rounded-lg hover:from-red-700 hover:to-orange-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <span x-show="!trendLoading" x-cloak class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        🔔 Generate Trend Content (<span x-text="trendContentTypes.length"></span> format)
                    </span>
                    <span x-show="trendLoading" x-cloak class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent mr-2"></div>
                        Generating trend content...
                    </span>
                </button>

                <!-- Results -->
                <div x-show="trendResults && trendResults.length > 0" x-cloak class="mt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">🎯 Konten Berdasarkan Trend</h4>
                    
                    <div class="space-y-6">
                        <template x-for="result in trendResults" :key="result.type">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <span x-text="result.icon" class="mr-2 text-lg"></span>
                                        <h5 class="font-medium text-gray-900" x-text="result.title"></h5>
                                        <span class="ml-2 text-xs px-2 py-1 bg-red-100 text-red-700 rounded-full">
                                            Trending
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500" x-text="result.content.length + ' chars'"></span>
                                        <button @click="copyTrendContent(result.content)" 
                                                class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                                            Copy
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                    <pre class="whitespace-pre-wrap text-sm text-gray-800" x-text="result.content"></pre>
                                </div>
                                
                                <!-- Hashtags -->
                                <div x-show="result.hashtags && result.hashtags.length > 0" class="flex flex-wrap gap-1">
                                    <template x-for="hashtag in result.hashtags" :key="hashtag">
                                        <span class="text-xs px-2 py-1 bg-red-100 text-red-600 rounded cursor-pointer"
                                              @click="copyTrendContent(hashtag)" x-text="hashtag"></span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button @click="copyAllTrendContent()" 
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm">
                            📋 Copy All Content
                        </button>
                        <button @click="exportTrendContent('txt')" 
                                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm">
                            📄 Export as TXT
                        </button>
                        <button @click="resetTrends()" 
                                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                            🔄 Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- 🎯 Google Ads Campaign Generator --}}
        @include('client.partials.google-ads-generator')

        {{-- 🔗 Magic Promo Link Generator --}}
        @include('client.partials.promo-link-generator')

        {{-- 💬 AI Product Explainer for WhatsApp --}}
        @include('client.partials.product-explainer')

        {{-- 🔍 SEO Metadata Auto-Fill --}}
        @include('client.partials.seo-metadata')

        {{-- ⚖️ Smart Comparison Tool --}}
        @include('client.partials.smart-comparison')

        {{-- ❓ Automated FAQ Generator --}}
        @include('client.partials.faq-generator')

        {{-- 🎬 AI Reels/TikTok Hook Generator --}}
        @include('client.partials.reels-hook')

        {{-- 🏅 Digital Asset Quality Badge --}}
        @include('client.partials.quality-badge')

        {{-- 🏷️ Discount Campaign Copywriter --}}
        @include('client.partials.discount-campaign')

        {{-- 📈 Trend-Based Product Tagging --}}
        @include('client.partials.trend-tags')

        {{-- 🧲 AI Lead Magnet Creator --}}
        @include('client.partials.lead-magnet')

        <!-- 🤖 ML Upgrade Modal & Features -->
        @include('client.partials.ml-upgrade-modal')
    </div>

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

            // 📈 Performance Predictor state
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

            // 📚 Template Library state
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

            // 🎯 Multi-Platform Optimizer state
            multiPlatformForm: {
                content: '',
                business_type: 'general',
                target_audience: 'general',
                goal: 'engagement',
                platforms: ['instagram', 'tiktok'] // default selection
            },
            multiPlatformLoading: false,
            multiPlatformResults: null,

            // ♻️ Content Repurposing state
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

            // 🎯 Google Ads state
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
                {id: 'copy',       label: '📝 Ad Copy'},
                {id: 'keywords',   label: '🔑 Keywords'},
                {id: 'extensions', label: '🔗 Sitelinks & Extensions'},
                {id: 'targeting',  label: '🎯 Targeting'},
                {id: 'budget',     label: '💰 Budget & Estimasi'},
                {id: 'review',     label: '📊 Review & Analysis'},
            ],

            // 🔗 Magic Promo Link state
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

            // 💬 AI Product Explainer state
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

            // 🔍 SEO Metadata state
            seoForm: { product_name: '', product_desc: '', category: '', keywords: '', url: '' },
            seoLoading: false,
            seoResult: null,
            seoError: null,
            seoCopied: {},

            // ⚖️ Smart Comparison state
            compForm: { product_a_name: '', product_a_desc: '', product_a_price: '', product_b_name: '', product_b_desc: '', product_b_price: '', buyer_persona: '' },
            compLoading: false,
            compResult: null,
            compError: null,
            compCopied: false,

            // ❓ FAQ Generator state
            faqForm: { product_name: '', product_desc: '', category: '', price: '' },
            faqLoading: false,
            faqResult: null,
            faqError: null,
            faqCopied: {},
            faqAllCopied: false,
            faqSchemaCopied: false,

            // 🎬 Reels Hook state
            reelsForm: { product_name: '', product_desc: '', target_audience: '', video_goal: '', platform: 'reels', tone: 'energetic' },
            reelsLoading: false,
            reelsResult: null,
            reelsError: null,
            reelsCopied: {},
            reelsScriptCopied: {},
            reelsSelectedHook: null,
            reelsBioCopied: false,

            // 🏅 Quality Badge state
            badgeForm: { product_name: '', product_desc: '', asset_type: '', code_or_doc: '' },
            badgeLoading: false,
            badgeResult: null,
            badgeError: null,
            badgeCopied: false,

            // 🏷️ Discount Campaign state
            discForm: { promo_name: '', product_name: '', product_desc: '', original_price: '', discount_price: '', discount_pct: '', duration: '', platform: '', wa_number: '' },
            discLoading: false,
            discResult: null,
            discError: null,
            discCopied: {},
            discWACopied: false,

            // 📈 Trend Tags state
            tagsForm: { product_name: '', product_desc: '', category: '', current_tags: '', platform: 'marketplace' },
            tagsLoading: false,
            tagsResult: null,
            tagsError: null,
            tagsCopied: false,

            // 🧲 Lead Magnet state
            magnetForm: { product_name: '', product_desc: '', product_type: '', price: '', target_audience: '', goal: 'email', wa_number: '' },
            magnetLoading: false,
            magnetResult: null,
            magnetError: null,
            magnetCopied: false,
            magnetWACopied: false,
            repurposeOptions: [
                {value: 'instagram_story', label: '📱 Instagram Story', description: 'Short, visual, engaging'},
                {value: 'tiktok_script', label: '🎵 TikTok Video Script', description: 'Hook + content + CTA'},
                {value: 'blog_outline', label: '📝 Blog Post Outline', description: 'Structured long-form'},
                {value: 'email_copy', label: '📧 Email Marketing', description: 'Subject + body + CTA'},
                {value: 'product_description', label: '🛍️ Product Description', description: 'Features + benefits'},
                {value: 'linkedin_post', label: '💼 LinkedIn Post', description: 'Professional tone'},
                {value: 'facebook_post', label: '👥 Facebook Post', description: 'Community focused'},
                {value: 'youtube_description', label: '📺 YouTube Description', description: 'SEO optimized'},
                {value: 'twitter_thread', label: '🐦 Twitter Thread', description: 'Multi-tweet story'},
                {value: 'whatsapp_broadcast', label: '💬 WhatsApp Broadcast', description: 'Direct & personal'},
                {value: 'carousel_slides', label: '📊 Carousel Slides', description: 'Multi-slide content'},
                {value: 'podcast_script', label: '🎙️ Podcast Script', description: 'Audio-friendly format'}
            ],

            // 🔔 Trend Alert state
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
                    icon: '💃',
                    hashtags: ['#GerakanSehat', '#ViralDance', '#HealthyLifestyle', '#Indonesia']
                },
                {
                    id: 2,
                    title: 'Trending Makanan Viral "Es Kepal Milo"',
                    description: 'Minuman es kepal dengan topping milo yang sedang viral di media sosial',
                    category: 'Food',
                    popularity: '1.8M',
                    timeAgo: '4 jam lalu',
                    icon: '🧊',
                    hashtags: ['#EsKepalMilo', '#MinumanViral', '#Kuliner', '#UMKM']
                },
                {
                    id: 3,
                    title: 'Gaya Fashion "Old Money Aesthetic"',
                    description: 'Trend fashion dengan gaya klasik dan elegan yang sedang populer',
                    category: 'Fashion',
                    popularity: '3.2M',
                    timeAgo: '6 jam lalu',
                    icon: '👗',
                    hashtags: ['#OldMoney', '#Fashion', '#Aesthetic', '#Style']
                },
                {
                    id: 4,
                    title: 'Teknologi AI untuk UMKM',
                    description: 'Pembahasan penggunaan AI untuk membantu bisnis UMKM berkembang',
                    category: 'Technology',
                    popularity: '950K',
                    timeAgo: '8 jam lalu',
                    icon: '🤖',
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
                    icon: '✨'
                },
                {
                    id: 2,
                    title: 'Behind The Scenes Process',
                    description: 'Tunjukkan proses pembuatan produk atau layanan dari belakang layar',
                    type: 'Educational',
                    engagement: '78%',
                    platform: 'All Platforms',
                    icon: '🎬'
                },
                {
                    id: 3,
                    title: 'Customer Testimonial Stories',
                    description: 'Cerita nyata pelanggan dengan hasil yang memuaskan',
                    type: 'Social Proof',
                    engagement: '92%',
                    platform: 'Instagram/Facebook',
                    icon: '💬'
                },
                {
                    id: 4,
                    title: 'Quick Tips & Hacks',
                    description: 'Tips cepat dan mudah yang bisa langsung dipraktikkan',
                    type: 'Educational',
                    engagement: '88%',
                    platform: 'TikTok/Instagram',
                    icon: '💡'
                }
            ],
            seasonalEvents: [
                {
                    id: 1,
                    title: `Ramadan ${new Date().getFullYear()}`,
                    date: `11 Maret - 9 April ${new Date().getFullYear()}`,
                    daysLeft: 45,
                    description: 'Bulan suci Ramadan dengan berbagai peluang konten dan promo',
                    icon: '🌙',
                    contentIdeas: ['Sahur Ideas', 'Iftar Menu', 'Spiritual Content', 'Charity Campaign']
                },
                {
                    id: 2,
                    title: 'Lebaran/Eid Mubarak',
                    date: `10-11 April ${new Date().getFullYear()}`,
                    daysLeft: 75,
                    description: 'Hari Raya Idul Fitri dengan tradisi mudik dan berkumpul keluarga',
                    icon: '🎉',
                    contentIdeas: ['Lebaran Outfit', 'Mudik Tips', 'Family Gathering', 'THR Campaign']
                },
                {
                    id: 3,
                    title: 'Back to School',
                    date: `Juli ${new Date().getFullYear()}`,
                    daysLeft: 120,
                    description: 'Musim kembali ke sekolah dengan kebutuhan perlengkapan baru',
                    icon: '🎒',
                    contentIdeas: ['School Supplies', 'Study Tips', 'Uniform Fashion', 'Parent Guide']
                },
                {
                    id: 4,
                    title: 'Indonesian Independence Day',
                    date: `17 Agustus ${new Date().getFullYear()}`,
                    daysLeft: 150,
                    description: 'Hari Kemerdekaan Indonesia dengan semangat nasionalisme',
                    icon: '🇮🇩',
                    contentIdeas: ['Patriotic Content', 'Local Pride', 'Indonesian Products', 'Unity Campaign']
                }
            ],
            nationalDays: [
                {
                    id: 1,
                    title: 'Hari Kartini',
                    date: `21 April ${new Date().getFullYear()}`,
                    description: 'Memperingati perjuangan R.A. Kartini untuk emansipasi wanita',
                    icon: '👩',
                    category: 'Women',
                    hashtags: ['#HariKartini', '#WomenEmpowerment', '#Emansipasi', '#PerempuanIndonesia']
                },
                {
                    id: 2,
                    title: 'Hari Pendidikan Nasional',
                    date: `2 Mei ${new Date().getFullYear()}`,
                    description: 'Memperingati hari lahir Ki Hajar Dewantara, Bapak Pendidikan Indonesia',
                    icon: '📚',
                    category: 'Education',
                    hashtags: ['#HardikNas', '#Pendidikan', '#KiHajarDewantara', '#BelajarSepanjangHayat']
                },
                {
                    id: 3,
                    title: 'Hari Kebangkitan Nasional',
                    date: `20 Mei ${new Date().getFullYear()}`,
                    description: 'Memperingati kebangkitan semangat kebangsaan Indonesia',
                    icon: '🌅',
                    category: 'National',
                    hashtags: ['#HariKebangkitanNasional', '#SemangitKebangsaan', '#Indonesia', '#Patriotisme']
                },
                {
                    id: 4,
                    title: 'Hari Lingkungan Hidup Sedunia',
                    date: `5 Juni ${new Date().getFullYear()}`,
                    description: 'Kampanye global untuk kesadaran dan aksi lingkungan hidup',
                    icon: '🌍',
                    category: 'Environment',
                    hashtags: ['#WorldEnvironmentDay', '#LingkunganHidup', '#GoGreen', '#SustainableLiving']
                }
            ],

            // Initialize - check if user is first time
            async init() {
                await this.checkFirstTimeStatus();
                // Load templates for Template Library
                await this.loadTemplates();
                // Load favorite templates from localStorage
                this.loadFavoriteTemplates();
                // Load dynamic dates
                await this.loadDynamicDates();
            },

            // 📅 Load dynamic dates from API
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
                    console.warn('⚠️ Failed to load dynamic dates, using fallback data');
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
                    {value: 'x_trend', label: 'X Trend Ideas'},
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
                marketing_funnel: [
                    {value: 'tofu_awareness', label: '🎯 TOFU - Awareness Stage (Top of Funnel)'},
                    {value: 'tofu_blog_post', label: '📝 TOFU - Blog Post Edukatif'},
                    {value: 'tofu_social_media', label: '📱 TOFU - Social Media Content'},
                    {value: 'tofu_video_content', label: '🎬 TOFU - Video Content Script'},
                    {value: 'mofu_consideration', label: '🤔 MOFU - Consideration Stage (Middle of Funnel)'},
                    {value: 'mofu_case_study', label: '📊 MOFU - Case Study / Success Story'},
                    {value: 'mofu_comparison', label: '⚖️ MOFU - Product Comparison'},
                    {value: 'mofu_webinar', label: '🎓 MOFU - Webinar / Workshop Copy'},
                    {value: 'mofu_email_nurture', label: '📧 MOFU - Email Nurture Sequence'},
                    {value: 'bofu_decision', label: '💰 BOFU - Decision Stage (Bottom of Funnel)'},
                    {value: 'bofu_sales_page', label: '🎯 BOFU - Sales Page Copy'},
                    {value: 'bofu_demo_trial', label: '🔓 BOFU - Demo / Free Trial Copy'},
                    {value: 'bofu_testimonial', label: '⭐ BOFU - Testimonial / Social Proof'},
                    {value: 'bofu_urgency', label: '⏰ BOFU - Urgency & Scarcity Copy'},
                    {value: 'retention_onboarding', label: '👋 Retention - Onboarding Sequence'},
                    {value: 'retention_upsell', label: '📈 Retention - Upsell / Cross-sell'},
                    {value: 'retention_reactivation', label: '🔄 Retention - Reactivation Campaign'},
                    {value: 'complete_funnel', label: '🎯 Complete Funnel Sequence (All Stages)'}
                ],
                sales_page: [
                    {value: 'complete_sales_page', label: '🎯 Complete Sales Page (Full Structure)'},
                    {value: 'hero_section', label: '🦸 Hero Section (Headline + Subheadline + CTA)'},
                    {value: 'problem_agitate', label: '😰 Problem & Agitate Section'},
                    {value: 'solution_presentation', label: '✨ Solution Presentation'},
                    {value: 'features_benefits', label: '⚡ Features & Benefits Section'},
                    {value: 'social_proof', label: '⭐ Social Proof (Testimonials + Reviews)'},
                    {value: 'pricing_section', label: '💰 Pricing Section (Value Stack)'},
                    {value: 'faq_objections', label: '❓ FAQ & Objection Handling'},
                    {value: 'guarantee_risk', label: '🛡️ Guarantee / Risk Reversal'},
                    {value: 'urgency_scarcity', label: '⏰ Urgency & Scarcity Elements'},
                    {value: 'final_cta', label: '🎯 Final CTA (Call to Action)'},
                    {value: 'bonus_stack', label: '🎁 Bonus Stack Section'},
                    {value: 'about_creator', label: '👤 About Creator / Company'},
                    {value: 'vsl_script', label: '🎬 VSL (Video Sales Letter) Script'},
                    {value: 'webinar_sales', label: '🎓 Webinar Sales Pitch'},
                    {value: 'product_launch_sales', label: '🚀 Product Launch Sales Page'},
                    {value: 'saas_sales_page', label: '💻 SaaS Sales Page'},
                    {value: 'course_sales_page', label: '📚 Course Sales Page'},
                    {value: 'coaching_sales_page', label: '🎯 Coaching/Consulting Sales Page'},
                    {value: 'ecommerce_product_page', label: '🛍️ E-commerce Product Page'}
                ],
                lead_magnet: [
                    {value: 'ebook_landing', label: '📚 Free eBook Landing Page'},
                    {value: 'checklist_template', label: '✅ Checklist / Template Opt-in'},
                    {value: 'webinar_registration', label: '🎓 Webinar Registration Page'},
                    {value: 'free_trial', label: '🔓 Free Trial Sign-up Copy'},
                    {value: 'resource_library', label: '📁 Resource Library Access'},
                    {value: 'quiz_assessment', label: '📊 Quiz / Assessment Lead Magnet'},
                    {value: 'video_series', label: '🎬 Video Series Opt-in'},
                    {value: 'mini_course', label: '🎓 Mini Course / Email Course'},
                    {value: 'toolkit_bundle', label: '🧰 Toolkit / Bundle Offer'},
                    {value: 'cheat_sheet', label: '📋 Cheat Sheet / Quick Guide'},
                    {value: 'case_study_download', label: '📊 Case Study Download'},
                    {value: 'whitepaper_report', label: '📄 Whitepaper / Industry Report'},
                    {value: 'swipe_file', label: '📝 Swipe File / Templates'},
                    {value: 'calculator_tool', label: '🔢 Calculator / Tool Access'},
                    {value: 'challenge_signup', label: '🏆 Challenge Sign-up Page'},
                    {value: 'consultation_booking', label: '📞 Free Consultation Booking'},
                    {value: 'demo_request', label: '🎯 Demo Request Page'},
                    {value: 'newsletter_signup', label: '📧 Newsletter Sign-up Copy'},
                    {value: 'discount_coupon', label: '🎟️ Discount Coupon Opt-in'},
                    {value: 'lead_magnet_delivery', label: '📬 Lead Magnet Delivery Email'}
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
                ],
                invitation_event: [
                    {value: 'wedding', label: '💒 Undangan Pernikahan'},
                    {value: 'engagement', label: '💍 Undangan Lamaran / Tunangan'},
                    {value: 'birthday', label: '🎂 Undangan Ulang Tahun'},
                    {value: 'aqiqah', label: '👶 Undangan Aqiqah / Syukuran Bayi'},
                    {value: 'khitanan', label: '🕌 Undangan Khitanan / Sunatan'},
                    {value: 'graduation', label: '🎓 Undangan Wisuda'},
                    {value: 'grand_opening', label: '🎉 Undangan Grand Opening'},
                    {value: 'meeting', label: '📋 Undangan Rapat / Meeting'},
                    {value: 'seminar', label: '🎤 Undangan Seminar / Workshop'},
                    {value: 'reunion', label: '👥 Undangan Reuni / Gathering'},
                    {value: 'other_event', label: '🎊 Undangan Acara Lainnya'},
                    {value: 'wedding_caption', label: '💒 Caption Pernikahan (Social Media)'},
                    {value: 'event_announcement', label: '📢 Pengumuman Event'},
                    {value: 'save_the_date', label: '📅 Save The Date'},
                    {value: 'thank_you_card', label: '🙏 Kartu Ucapan Terima Kasih'},
                    {value: 'rsvp_message', label: '✉️ Pesan RSVP / Konfirmasi Kehadiran'}
                ],
                short_drama: [
                    {value: 'drama_script', label: '🎬 Script Short Drama (Full Scene)'},
                    {value: 'romantic_dialogue', label: '💕 Percakapan Romantis (Baper)'},
                    {value: 'conflict_scene', label: '😤 Adegan Konflik / Pertengkaran'},
                    {value: 'plot_twist_drama', label: '🌀 Plot Twist Drama'},
                    {value: 'character_monologue', label: '🎭 Monolog Karakter (Emosional)'},
                    {value: 'drama_opening', label: '🎥 Opening Scene (Hook 30 Detik)'},
                    {value: 'breakup_scene', label: '💔 Adegan Putus Cinta'},
                    {value: 'reunion_scene', label: '🥹 Adegan Reuni Mengharukan'},
                    {value: 'misunderstanding_scene', label: '😤 Adegan Salah Paham'},
                    {value: 'confession_scene', label: '💌 Adegan Confess Perasaan'},
                    {value: 'villain_dialogue', label: '😈 Dialog Villain / Antagonis'},
                    {value: 'family_drama', label: '👨‍👩‍👧 Drama Keluarga'},
                    {value: 'office_romance', label: '💼 Office Romance'},
                    {value: 'enemies_to_lovers', label: '⚔️ Enemies to Lovers'},
                    {value: 'second_chance_romance', label: '🔄 Second Chance Romance (Mantan)'}
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
                    case 'complete':
                        this.analysisForm.options = ['objects', 'colors', 'composition', 'mood', 'text', 'marketing', 'quality', 'suggestions'];
                        this.analysisForm.context = 'Analisis lengkap semua aspek gambar untuk keperluan bisnis';
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

                    if (data.success) {
                        this.result = data.analysis;
                        this.lastCaptionId = data.caption_id || null;
                        
                        // Show success notification
                        this.showNotification('✅ Analisis gambar berhasil!', 'success');
                        
                        // Scroll to result
                        setTimeout(() => {
                            document.querySelector('.mt-6').scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                    } else {
                        this.error = data.message || 'Terjadi kesalahan saat menganalisis gambar';
                        this.showNotification('❌ ' + this.error, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.error = 'Terjadi kesalahan jaringan';
                    this.showNotification('❌ Terjadi kesalahan jaringan', 'error');
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

                    if (data.success) {
                        this.result = data.content;
                        this.lastCaptionId = data.caption_id || null;
                        
                        // Show success notification
                        const imageText = this.videoForm.image_file ? ' dengan analisis visual' : '';
                        this.showNotification(`✅ Konten video berhasil di-generate${imageText}!`, 'success');
                        
                        // Scroll to result
                        setTimeout(() => {
                            document.querySelector('.mt-6').scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                    } else {
                        this.error = data.message || 'Terjadi kesalahan saat generate konten video';
                        this.showNotification('❌ ' + this.error, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.error = 'Terjadi kesalahan jaringan';
                    this.showNotification('❌ Terjadi kesalahan jaringan', 'error');
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
                        'closing': 'persuasive',      // Closing → Persuasive
                        'awareness': 'educational',   // Awareness → Educational
                        'engagement': 'casual',       // Engagement → Casual
                        'viral': 'funny'              // Viral → Funny
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
                        
                        // 🤖 ML: Store ML data
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
                        
                        // Quota / subscription error — show inline banner
                        if (data.quota_error) {
                            let banner = document.getElementById('quota-error-banner');
                            if (!banner) {
                                banner = document.createElement('div');
                                banner.id = 'quota-error-banner';
                                banner.className = 'mb-5 bg-red-50 border border-red-300 rounded-xl p-4 text-sm text-red-800 flex items-start gap-3';
                                const wrapper = document.querySelector('.p-6[x-data]');
                                if (wrapper) wrapper.insertBefore(banner, wrapper.children[1]);
                            }
                            banner.innerHTML = '<span class="text-xl flex-shrink-0">🚫</span><span>' + errorMessage + '</span>';
                            banner.classList.remove('hidden');
                            banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else if (errorMessage.includes('API key') || errorMessage.includes('tidak valid') || errorMessage.includes('expired')) {
                            this.error = '⚠️ Layanan AI sedang tidak tersedia. Silakan coba beberapa saat lagi atau hubungi admin.';
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
                            console.error('Clipboard error:', err);
                            this.fallbackCopy(textToCopy);
                        });
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

            // 📈 PERFORMANCE PREDICTOR FUNCTIONS
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

                    if (data.success) {
                        this.predictionResults = data;
                    } else {
                        throw new Error(data.message || 'Gagal memprediksi performa caption');
                    }

                } catch (error) {
                    console.error('Prediction error:', error);
                    this.predictorError = error.message || 'Terjadi kesalahan saat memprediksi performa';
                    alert(this.predictorError);
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

                    if (data.success) {
                        // Show variants in a modal or new section
                        this.showVariantsModal(data);
                    } else {
                        throw new Error(data.message || 'Gagal generate variants');
                    }

                } catch (error) {
                    console.error('Variants generation error:', error);
                    alert(error.message || 'Terjadi kesalahan saat generate variants');
                } finally {
                    this.variantsLoading = false;
                }
            },

            showVariantsModal(data) {
                let modalContent = `
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-semibold">🧪 A/B Testing Variants</h3>
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
                        <h4 class="font-semibold mb-2">📝 Original (A)</h4>
                        <div class="bg-gray-50 p-3 rounded text-sm">${data.original_caption}</div>
                    </div>
                `;

                // Add variants
                data.variants.forEach((variant, index) => {
                    modalContent += `
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold">🔄 Variant ${String.fromCharCode(66 + index)} (${variant.focus})</h4>
                                <button onclick="copyToClipboard('${variant.caption.replace(/'/g, "\\'")}'); alert('Copied!')" 
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
                                <h4 class="font-semibold mb-2">📊 Test Setup Recommendations</h4>
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

            // 📚 TEMPLATE LIBRARY FUNCTIONS
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
                    console.error('Load templates error:', error);
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
                        category_label: '🔥 Viral & Clickbait',
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
                        category_label: '🔥 Viral & Clickbait',
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
                        category_label: '🎉 Event & Promo',
                        platform: 'universal',
                        tone: 'persuasive',
                        format: '🚨 FLASH SALE ALERT! 🚨\n\n⏰ HANYA [DURASI]!\n💥 DISKON [PERSENTASE]% untuk [PRODUK]\n🔥 Stok terbatas: [JUMLAH] pcs saja!\n\n✅ Kualitas premium\n✅ Garansi resmi\n✅ Free ongkir seluruh Indonesia\n\n⚡ Jangan sampai kehabisan!\n📱 Order sekarang: [LINK/KONTAK]\n\n#FlashSale #Diskon #PromoTerbatas',
                        usage_count: 2100,
                        is_favorite: false
                    },
                    {
                        id: 4,
                        title: 'Job Vacancy Post',
                        description: 'Template lowongan kerja yang menarik talent terbaik',
                        category: 'hr_recruitment',
                        category_label: '💼 HR & Recruitment',
                        platform: 'linkedin',
                        tone: 'formal',
                        format: '🚀 WE ARE HIRING! 🚀\n\n📍 Posisi: [JABATAN]\n🏢 Perusahaan: [NAMA PERUSAHAAN]\n📍 Lokasi: [KOTA]\n💼 Tipe: [FULL TIME/PART TIME/CONTRACT]\n\n✨ Yang Kami Cari:\n• [KUALIFIKASI 1]\n• [KUALIFIKASI 2]\n• [KUALIFIKASI 3]\n\n🎁 Yang Kami Tawarkan:\n• Gaji kompetitif\n• Benefit menarik\n• Lingkungan kerja yang supportive\n• Kesempatan berkembang\n\n📧 Kirim CV ke: [EMAIL]\n💬 Info lebih lanjut: [KONTAK]\n\n#LowonganKerja #Hiring #Karir #[KOTA]Jobs',
                        usage_count: 750,
                        is_favorite: false
                    },
                    {
                        id: 5,
                        title: 'Brand Tagline Generator',
                        description: 'Buat tagline brand yang memorable dan impactful',
                        category: 'branding_tagline',
                        category_label: '🎯 Branding & Tagline',
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
                        category_label: '📹 Video Content',
                        platform: 'tiktok',
                        tone: 'funny',
                        format: '⏱️ DETIK 1-3 (HOOK CRUCIAL!):\n"POV: Kamu [SITUASI RELATABLE]..."\n"Jangan scroll dulu! Ini [BENEFIT]"\n"Siapa yang [PERTANYAAN ENGAGING]?"\n\n⏱️ DETIK 4-30 (CONTENT):\n- Deliver value/entertainment\n- Keep it visual & engaging\n- Use trending sounds\n- Quick cuts/transitions\n\n⏱️ DETIK 30-60 (CTA):\n"Follow untuk tips lainnya!"\n"Tag teman yang butuh ini!"\n"Comment pengalaman kamu!"\n\n#FYP #Viral #[NICHE]TikTok',
                        usage_count: 3200,
                        is_favorite: false
                    },
                    {
                        id: 7,
                        title: 'Shopee Product Description',
                        description: 'Deskripsi produk Shopee yang convert dan SEO-friendly',
                        category: 'affiliate_marketing',
                        category_label: '🤝 Affiliate Marketing',
                        platform: 'shopee',
                        tone: 'persuasive',
                        format: '🛍️ [NAMA PRODUK] - [BENEFIT UTAMA]\n\n✨ KEUNGGULAN:\n✅ [KEUNGGULAN 1]\n✅ [KEUNGGULAN 2]\n✅ [KEUNGGULAN 3]\n\n📦 SPESIFIKASI:\n• Material: [BAHAN]\n• Ukuran: [DIMENSI]\n• Warna: [PILIHAN WARNA]\n• Berat: [BERAT]\n\n🎁 BONUS:\n• Free bubble wrap\n• Garansi [DURASI]\n• Customer service 24/7\n\n⭐ TESTIMONI: "[REVIEW CUSTOMER]"\n\n🚚 PENGIRIMAN:\n• Same day (Jakarta)\n• 1-3 hari (Jawa)\n• 3-7 hari (luar Jawa)\n\n💰 HARGA SPESIAL: Rp [HARGA] (Normal: Rp [HARGA_NORMAL])\n\n🛒 ORDER SEKARANG! Stok terbatas!\n\n#[KATEGORI] #[BRAND] #QualityProduct',
                        usage_count: 1890,
                        is_favorite: false
                    },
                    {
                        id: 8,
                        title: 'Blog SEO Article Outline',
                        description: 'Outline artikel blog yang SEO-optimized dan engaging',
                        category: 'blog_seo',
                        category_label: '📝 Blog & SEO',
                        platform: 'universal',
                        tone: 'educational',
                        format: '📝 STRUKTUR ARTIKEL SEO:\n\n1. SEO TITLE (50-60 char):\n"[KEYWORD UTAMA]: [BENEFIT/PROMISE]"\n\n2. META DESCRIPTION (150-160 char):\n"[RINGKASAN ARTIKEL + KEYWORD + CTA]"\n\n3. H1 HEADLINE:\n"[KEYWORD UTAMA] - [ANGLE UNIK]"\n\n4. INTRODUCTION (150 kata):\n- Hook (statistik/pertanyaan)\n- Problem statement\n- Preview artikel\n- Keyword naturally\n\n5. H2 SUBHEADINGS:\n- [KEYWORD] untuk Pemula\n- Cara [KEYWORD] yang Efektif\n- Tips [KEYWORD] Terbaik\n- Kesalahan [KEYWORD] yang Harus Dihindari\n\n6. CONCLUSION + CTA:\n- Ringkasan key points\n- Call to action\n- Related articles\n\n7. INTERNAL LINKS: 3-5 artikel terkait\n8. IMAGES: Alt text dengan keyword\n9. FAQ SECTION: 5-8 pertanyaan umum',
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
                alert(`✅ Template "${template.title}" berhasil dimuat! Sesuaikan dengan kebutuhan Anda.`);
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
                    console.error('Load favorites error:', error);
                }
            },

            // 🎯 MULTI-PLATFORM OPTIMIZER FUNCTIONS
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

                    if (data.success) {
                        this.multiPlatformResults = data.results;
                        
                        // Scroll to results
                        setTimeout(() => {
                            document.querySelector('[x-show="multiPlatformResults"]').scrollIntoView({ 
                                behavior: 'smooth' 
                            });
                        }, 100);
                    } else {
                        alert('Error: ' + (data.message || 'Gagal generate multi-platform content'));
                    }

                } catch (error) {
                    console.error('Multi-platform generation error:', error);
                    alert('Terjadi kesalahan saat generate content. Silakan coba lagi.');
                } finally {
                    this.multiPlatformLoading = false;
                }
            },

            getPlatformEmoji(platform) {
                const emojis = {
                    'instagram': '📸',
                    'tiktok': '🎵',
                    'facebook': '👥',
                    'twitter': '🐦',
                    'whatsapp': '💬',
                    'marketplace': '🛒'
                };
                return emojis[platform] || '📱';
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
                    'instagram': 'Max 2200 chars • 30 hashtags • Visual focus',
                    'tiktok': 'Max 150 chars • Trending sounds • Short & catchy',
                    'facebook': 'Storytelling format • Longer content • Community focus',
                    'twitter': 'Max 280 chars • Thread support • News style',
                    'whatsapp': 'Short & punchy • Personal tone • Direct message',
                    'marketplace': 'SEO optimized • Product focus • Conversion oriented'
                };
                return specs[platform] || 'Platform optimized';
            },

            copyPlatformContent(platform, content) {
                copyToClipboard(content).then(() => {
                    // Show temporary success message
                    const button = event.target;
                    const originalText = button.textContent;
                    button.textContent = '✅ Copied!';
                    button.classList.add('bg-green-600');
                    button.classList.remove('bg-blue-600');
                    
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-600');
                        button.classList.add('bg-blue-600');
                    }, 2000);
                }).catch(err => {
                    console.error('Copy failed:', err);
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
                    alert('✅ Semua content berhasil di-copy!');
                }).catch(err => {
                    console.error('Copy all failed:', err);
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
                                content += `• ${note}\n`;
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

            // ♻️ CONTENT REPURPOSING FUNCTIONS
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

                    if (data.success) {
                        this.repurposeResults = data.results;
                        
                        // Scroll to results
                        setTimeout(() => {
                            const resultsElement = document.querySelector('[x-show="repurposeResults && repurposeResults.length > 0"]');
                            if (resultsElement) {
                                resultsElement.scrollIntoView({ behavior: 'smooth' });
                            }
                        }, 100);
                    } else {
                        alert('Error: ' + (data.message || 'Gagal repurpose content'));
                    }

                } catch (error) {
                    console.error('Content repurposing error:', error);
                    alert('Terjadi kesalahan saat repurpose content. Silakan coba lagi.');
                } finally {
                    this.repurposeLoading = false;
                }
            },

            copyRepurposedContent(content) {
                copyToClipboard(content).then(() => {
                    // Show temporary success message
                    const button = event.target;
                    const originalText = button.textContent;
                    button.textContent = '✅ Copied!';
                    button.classList.add('bg-green-600');
                    button.classList.remove('bg-blue-600', 'bg-gray-500');
                    
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-600');
                        button.classList.add('bg-blue-600');
                    }, 2000);
                }).catch(err => {
                    console.error('Copy failed:', err);
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
                    alert('✅ Semua repurposed content berhasil di-copy!');
                }).catch(err => {
                    console.error('Copy all failed:', err);
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

            // 🔔 TREND ALERT FUNCTIONS
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
                            icon: '💪',
                            hashtags: ['#IndonesiaSehat', '#MentalHealth', '#Wellness', '#Viral']
                        },
                        ...this.dailyTrends.slice(0, 3)
                    ];
                    
                    alert('✅ Trends berhasil di-refresh!');
                } catch (error) {
                    console.error('Refresh trends error:', error);
                    alert('❌ Gagal refresh trends. Silakan coba lagi.');
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

                    if (data.success) {
                        this.trendResults = data.results;
                        alert('✅ Konten trend berhasil di-generate!');
                    } else {
                        throw new Error(data.error || 'Failed to generate trend content');
                    }

                } catch (error) {
                    console.error('Trend content generation error:', error);
                    
                    // Fallback to basic generation
                    this.generateBasicTrendContent();
                } finally {
                    this.trendLoading = false;
                }
            },

            generateBasicTrendContent() {
                const contentTypeMap = {
                    caption: { icon: '📱', title: 'Instagram/Facebook Caption' },
                    story: { icon: '📸', title: 'Instagram Story' },
                    tiktok: { icon: '🎵', title: 'TikTok Script' },
                    thread: { icon: '🧵', title: 'Twitter Thread' },
                    blog: { icon: '📝', title: 'Blog Post' },
                    email: { icon: '📧', title: 'Email Marketing' },
                    ads: { icon: '💰', title: 'Facebook/Instagram Ads' },
                    whatsapp: { icon: '💬', title: 'WhatsApp Blast' }
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

                alert('✅ Konten trend berhasil di-generate (mode basic)!');
            },

            generateTrendContentByType(type) {
                const trend = this.selectedTrend;
                const business = this.trendBusinessContext;
                
                const templates = {
                    caption: `🔥 IKUTAN TREND ${trend.title.toUpperCase()}! 🔥

${trend.description}

Sebagai ${business}, kita juga mau ikutan nih! 

✨ Gimana kalau kita bikin versi kita sendiri?
💡 Yuk share ide kalian di comment!

#TrendAlert #${business.replace(/\s+/g, '')} ${trend.hashtags?.join(' ') || ''}`,

                    story: `📸 STORY SERIES: ${trend.title}

Slide 1: "Lagi viral nih!"
Slide 2: "${trend.description}"
Slide 3: "Versi ${business} gimana ya?"
Slide 4: "Swipe up untuk lihat!"
Slide 5: CTA - "Follow untuk update trend terbaru!"`,

                    tiktok: `🎵 TIKTOK SCRIPT: ${trend.title}

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

                    thread: `🧵 TWITTER THREAD: ${trend.title}

1/5 🔥 Lagi viral banget nih ${trend.title}! 

2/5 📈 ${trend.description}

3/5 💡 Sebagai ${business}, kita lihat peluang buat ikutan dengan cara yang unik

4/5 ✨ Ide kita: [sesuaikan dengan bisnis]

5/5 🚀 Kalian ada ide lain? Reply thread ini! ${trend.hashtags?.join(' ') || ''}`,

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

                    email: `Subject: 🔥 Jangan Ketinggalan Trend ${trend.title}!

Halo [Nama],

Pasti udah tau dong trend ${trend.title} yang lagi viral banget?

${trend.description}

Nah, sebagai ${business}, kita gak mau ketinggalan dong! 

Makanya kita bikin [produk/promo spesial] yang terinspirasi dari trend ini.

[CTA Button: Lihat Sekarang]

Jangan sampai kehabisan ya!

Salam,
Tim ${business}`,

                    ads: `🔥 TRENDING ALERT: ${trend.title}

${trend.description}

Sebagai ${business} terdepan, kita ikutan trend ini dengan cara yang unik!

✅ [Benefit 1]
✅ [Benefit 2] 
✅ [Benefit 3]

Jangan sampai ketinggalan!

[CTA: Pesan Sekarang]

#TrendAlert ${trend.hashtags?.join(' ') || ''}`,

                    whatsapp: `🔥 *TREND ALERT!*

Hai! Pasti udah tau kan trend *${trend.title}* yang lagi viral?

${trend.description}

Nah, sebagai ${business}, kita juga mau ikutan nih! 

Gimana kalau kita bikin versi kita sendiri? 

Yuk chat balik kalau tertarik! 😊

${trend.hashtags?.join(' ') || ''}`
                };

                return templates[type] || `Konten ${type} untuk trend ${trend.title} - ${business}`;
            },

            copyTrendContent(content) {
                copyToClipboard(content).then(() => {
                    alert('✅ Konten berhasil di-copy!');
                }).catch(() => {
                    alert('❌ Gagal copy konten');
                });
            },

            copyAllTrendContent() {
                const allContent = this.trendResults.map(result => 
                    `${result.title}:\n${result.content}\n\nHashtags: ${result.hashtags.join(' ')}\n\n---\n`
                ).join('\n');
                
                copyToClipboard(allContent).then(() => {
                    alert('✅ Semua konten berhasil di-copy!');
                }).catch(() => {
                    alert('❌ Gagal copy konten');
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

            // 📝 CAPTION OPTIMIZER FUNCTIONS
            
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
                    const response = await fetch('/api/optimizer/check-grammar', {
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
                    console.error('Grammar check error:', error);
                    alert('Terjadi kesalahan saat check grammar');
                } finally {
                    this.grammarLoading = false;
                }
            },

            async quickGrammarFix() {
                if (!this.result) return;

                this.grammarFixing = true;

                try {
                    const response = await fetch('/api/optimizer/quick-grammar-fix', {
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
                        alert('✅ Grammar berhasil diperbaiki!');
                        this.showGrammarChecker = false;
                    } else {
                        alert('Error: ' + (data.message || 'Gagal fix grammar'));
                    }

                } catch (error) {
                    console.error('Quick fix error:', error);
                    alert('Terjadi kesalahan saat fix grammar');
                } finally {
                    this.grammarFixing = false;
                }
            },

            useCorrectedText() {
                if (this.grammarResult?.corrected_text) {
                    this.result = this.grammarResult.corrected_text;
                    alert('✅ Corrected text berhasil digunakan!');
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
                    const response = await fetch('/api/optimizer/shorten-caption', {
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
                    console.error('Caption shortening error:', error);
                    alert('Terjadi kesalahan saat shorten caption');
                } finally {
                    this.shortenerLoading = false;
                }
            },

            useShortened(shortenedText) {
                this.result = shortenedText;
                alert('✅ Shortened caption berhasil digunakan!');
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
                    const response = await fetch('/api/optimizer/expand-caption', {
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
                    console.error('Caption expansion error:', error);
                    alert('Terjadi kesalahan saat expand caption');
                } finally {
                    this.expanderLoading = false;
                }
            },

            useExpanded(expandedText) {
                this.result = expandedText;
                alert('✅ Expanded caption berhasil digunakan!');
                this.showCaptionExpander = false;
            },

            // ─── Google Ads Functions ───────────────────────────────────
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
                    if (data.success) {
                        this.adsResult = data.campaign;
                        this.adsTab = 'copy';
                        if (data.quota_remaining !== undefined) {
                            this.quotaRemaining = data.quota_remaining;
                        }
                    } else if (data.quota_error) {
                        alert('⚡ ' + data.message);
                    } else {
                        alert('❌ ' + (data.message || 'Gagal generate kampanye'));
                    }
                } catch (e) {
                    alert('Terjadi kesalahan. Coba lagi.');
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

            // ─── Magic Promo Link Functions ─────────────────────────────
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
                    if (data.success) {
                        this.promoResult = data.data;
                        if (data.quota_remaining !== undefined) {
                            this.quotaRemaining = data.quota_remaining;
                        }
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
                // X (Twitter) has 280 char limit — use hook + CTA + hashtags
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

            // ─── Product Explainer Functions ────────────────────────────
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
                    if (data.success) {
                        this.explainerResult = data.data;
                        if (data.quota_remaining !== undefined) {
                            this.quotaRemaining = data.quota_remaining;
                        }
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

            // ─── SEO Metadata ───────────────────────────────────────────────
            async generateSeoMetadata() {
                this.seoLoading = true; this.seoResult = null; this.seoError = null;
                try {
                    const response = await fetch('/api/ai/generate-seo-metadata', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.seoForm)
                    });
                    const data = await response.json();
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

            // ─── Smart Comparison ────────────────────────────────────────────
            async generateComparison() {
                this.compLoading = true; this.compResult = null; this.compError = null;
                try {
                    const response = await fetch('/api/ai/generate-comparison', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.compForm)
                    });
                    const data = await response.json();
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

            // ─── FAQ Generator ───────────────────────────────────────────────
            async generateFaq() {
                this.faqLoading = true; this.faqResult = null; this.faqError = null;
                try {
                    const response = await fetch('/api/ai/generate-faq', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.faqForm)
                    });
                    const data = await response.json();
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

            // ─── Reels Hook ──────────────────────────────────────────────────
            async generateReelsHook() {
                this.reelsLoading = true; this.reelsResult = null; this.reelsError = null;
                try {
                    const response = await fetch('/api/ai/generate-reels-hook', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.reelsForm)
                    });
                    const data = await response.json();
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

            // ─── Quality Badge ───────────────────────────────────────────────
            async generateQualityBadge() {
                this.badgeLoading = true; this.badgeResult = null; this.badgeError = null;
                try {
                    const response = await fetch('/api/ai/generate-quality-badge', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.badgeForm)
                    });
                    const data = await response.json();
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

            // ─── Discount Campaign ───────────────────────────────────────────
            async generateDiscountCampaign() {
                this.discLoading = true; this.discResult = null; this.discError = null;
                try {
                    const response = await fetch('/api/ai/generate-discount-campaign', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.discForm)
                    });
                    const data = await response.json();
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

            // ─── Trend Tags ──────────────────────────────────────────────────
            async generateTrendTags() {
                this.tagsLoading = true; this.tagsResult = null; this.tagsError = null;
                try {
                    const response = await fetch('/api/ai/generate-trend-tags', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.tagsForm)
                    });
                    const data = await response.json();
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

            // ─── Lead Magnet ─────────────────────────────────────────────────
            async generateLeadMagnet() {
                this.magnetLoading = true; this.magnetResult = null; this.magnetError = null;
                try {
                    const response = await fetch('/api/ai/generate-lead-magnet', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.magnetForm)
                    });
                    const data = await response.json();
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

