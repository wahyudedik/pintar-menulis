

<?php $__env->startSection('title', 'Buat Template Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6" x-data="templateForm()">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">✍️ Buat Template Baru</h1>
                <p class="text-sm text-gray-500 mt-1">Buat template copywriting dan bagikan ke community</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('templates.index')); ?>"
                   class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                <button @click="submitTemplate" :disabled="loading"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 disabled:opacity-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Template'"></span>
                </button>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-4 bg-blue-50 border-l-4 border-blue-600 p-4 rounded-r-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-blue-900 mb-1">💡 Tips Membuat Template yang Baik</h4>
                    <ul class="text-sm text-blue-800 space-y-1 ml-4 list-disc">
                        <li>Gunakan <strong>[PLACEHOLDER]</strong> untuk variabel yang bisa diganti (contoh: [NAMA_PRODUK], [HARGA])</li>
                        <li>Judul yang jelas dan deskriptif membantu user menemukan template Anda</li>
                        <li>Tambahkan instruksi format agar user tahu cara menggunakan template</li>
                        <li>Template yang dipublikasikan akan direview admin sebelum tampil di marketplace</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Success/Error -->
    <div x-show="alertMessage" x-cloak x-transition
         :class="alertType === 'success' ? 'bg-green-50 border-green-500 text-green-800' : 'bg-red-50 border-red-500 text-red-800'"
         class="mb-6 border-l-4 p-4 rounded-r-lg">
        <p class="text-sm font-medium" x-text="alertMessage"></p>
    </div>

    <!-- Form -->
    <form @submit.prevent="submitTemplate" class="space-y-6">

        <!-- Section 1: Info Dasar -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">1. Informasi Dasar</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Template <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.title" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Contoh: Instagram Caption untuk Flash Sale">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea x-model="form.description" required rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Jelaskan kegunaan dan keunggulan template ini..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select x-model="form.category" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Kategori</option>
                            <option value="viral_clickbait">Viral & Clickbait</option>
                            <option value="event_promo">Event & Promo</option>
                            <option value="branding_tagline">Branding & Tagline</option>
                            <option value="monetization">Monetization</option>
                            <option value="engagement">Engagement</option>
                            <option value="product_launch">Product Launch</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform <span class="text-red-500">*</span></label>
                        <select x-model="form.platform" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="universal">Universal</option>
                            <option value="instagram">Instagram</option>
                            <option value="tiktok">TikTok</option>
                            <option value="facebook">Facebook</option>
                            <option value="twitter">Twitter/X</option>
                            <option value="linkedin">LinkedIn</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tone <span class="text-red-500">*</span></label>
                        <select x-model="form.tone" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="universal">Universal</option>
                            <option value="casual">Casual</option>
                            <option value="formal">Formal</option>
                            <option value="persuasive">Persuasive</option>
                            <option value="humorous">Humorous</option>
                            <option value="inspirational">Inspirational</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Konten Template -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">2. Konten Template</h3>

            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">Template Content <span class="text-red-500">*</span></label>
                        <span class="text-xs text-gray-500">Gunakan [PLACEHOLDER] untuk variabel</span>
                    </div>
                    <textarea x-model="form.template_content" required rows="12"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                              placeholder="🚨 FLASH SALE ALERT! 🚨&#10;&#10;⏰ HANYA [DURASI]!&#10;💥 DISKON [PERSENTASE]% untuk [NAMA_PRODUK]&#10;&#10;✅ [BENEFIT_1]&#10;✅ [BENEFIT_2]&#10;✅ [BENEFIT_3]&#10;&#10;📱 Order sekarang: [LINK]&#10;&#10;#flashsale #promo #[HASHTAG]"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instruksi Format</label>
                    <textarea x-model="form.format_instructions" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Panduan cara menggunakan template ini. Contoh: Ganti [NAMA_PRODUK] dengan nama produk Anda, [DURASI] dengan durasi promo..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                    <input type="text" x-model="tagsInput"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="promo, flash sale, diskon (pisahkan dengan koma)">
                    <p class="text-xs text-gray-500 mt-1">Tags membantu user menemukan template Anda</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Pengaturan Publikasi -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">3. Pengaturan Publikasi</h3>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" x-model="form.is_public"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Publikasikan ke Community</span>
                                <p class="text-xs text-gray-500">Template akan direview admin sebelum tampil</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" x-model="form.is_premium"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Template Premium (Berbayar)</span>
                                <p class="text-xs text-gray-500">User perlu membeli untuk menggunakan</p>
                            </div>
                        </label>
                    </div>

                    <div class="space-y-4">
                        <div x-show="form.is_premium" x-cloak>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga (Rp)</label>
                            <input type="number" x-model="form.price" min="0" step="1000"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   placeholder="50000">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Lisensi <span class="text-red-500">*</span></label>
                            <select x-model="form.license_type" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="free">Free - Gratis untuk semua</option>
                                <option value="personal">Personal - Hanya penggunaan pribadi</option>
                                <option value="commercial">Commercial - Boleh untuk bisnis</option>
                                <option value="extended">Extended - Hak penuh</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4">
            <button type="submit" :disabled="loading"
                    class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 font-medium transition">
                <span x-text="loading ? '⏳ Menyimpan...' : '💾 Simpan Template'"></span>
            </button>
            <a href="<?php echo e(route('templates.index')); ?>"
               class="px-8 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                Batal
            </a>
        </div>

    </form>
</div>

<script>
    function templateForm() {
        return {
            form: {
                title: '',
                description: '',
                category: '',
                platform: 'universal',
                tone: 'universal',
                template_content: '',
                format_instructions: '',
                tags: [],
                is_public: false,
                is_premium: false,
                price: null,
                license_type: 'free'
            },
            tagsInput: '',
            loading: false,
            alertMessage: '',
            alertType: 'success',

            async submitTemplate() {
                this.loading = true;
                this.alertMessage = '';

                if (this.tagsInput) {
                    this.form.tags = this.tagsInput.split(',').map(t => t.trim()).filter(t => t);
                }

                try {
                    const response = await fetch('/templates', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.alertType = 'success';
                        this.alertMessage = '✅ Template berhasil dibuat! Mengalihkan...';
                        setTimeout(() => window.location.href = '/templates', 1500);
                    } else {
                        this.alertType = 'error';
                        this.alertMessage = '❌ Gagal: ' + (data.message || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    this.alertType = 'error';
                    this.alertMessage = '❌ Terjadi kesalahan. Silakan coba lagi.';
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\template-marketplace\create-new.blade.php ENDPATH**/ ?>