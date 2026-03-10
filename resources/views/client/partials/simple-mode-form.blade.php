<!-- SIMPLE MODE FORM -->
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
