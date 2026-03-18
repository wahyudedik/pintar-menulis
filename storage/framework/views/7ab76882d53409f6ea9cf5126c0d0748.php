
<div x-show="generatorType === 'reader_trend'" x-cloak class="space-y-6">

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-3 mb-4">
            <span class="text-2xl">📖</span>
            <div>
                <h2 class="font-semibold text-gray-900">AI Analisis Tren Pembaca</h2>
                <p class="text-xs text-gray-500 mt-0.5">Temukan topik populer, pola engagement, dan peluang pasar konten digital</p>
            </div>
        </div>

        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Genre / Kategori Konten</label>
            <select x-model="readerTrendForm.genre"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                <option value="">Semua Genre</option>
                <option value="bisnis & keuangan">Bisnis & Keuangan</option>
                <option value="self-improvement & motivasi">Self-Improvement & Motivasi</option>
                <option value="teknologi & digital">Teknologi & Digital</option>
                <option value="pendidikan & akademik">Pendidikan & Akademik</option>
                <option value="fiksi & novel">Fiksi & Novel</option>
                <option value="kesehatan & gaya hidup">Kesehatan & Gaya Hidup</option>
                <option value="parenting & keluarga">Parenting & Keluarga</option>
                <option value="kuliner & resep">Kuliner & Resep</option>
                <option value="travel & wisata">Travel & Wisata</option>
                <option value="agama & spiritualitas">Agama & Spiritualitas</option>
                <option value="marketing & media sosial">Marketing & Media Sosial</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>

        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Platform Distribusi</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <template x-for="p in [
                    {value:'', label:'Semua Platform', icon:'🌐'},
                    {value:'Google Play Books', label:'Google Play Books', icon:'📗'},
                    {value:'Tokopedia / Shopee', label:'Tokopedia/Shopee', icon:'🛒'},
                    {value:'Instagram & TikTok', label:'Instagram/TikTok', icon:'📱'},
                    {value:'Website & Blog', label:'Website/Blog', icon:'💻'},
                    {value:'WhatsApp', label:'WhatsApp', icon:'💬'},
                    {value:'YouTube', label:'YouTube', icon:'▶️'},
                    {value:'Podcast', label:'Podcast', icon:'🎙️'}
                ]" :key="p.value">
                    <label class="flex items-center gap-1.5 p-2 border-2 rounded-lg cursor-pointer transition text-xs"
                           :class="readerTrendForm.platform === p.value ? 'border-teal-500 bg-teal-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" x-model="readerTrendForm.platform" :value="p.value" class="sr-only">
                        <span x-text="p.icon"></span>
                        <span class="font-medium text-gray-800" x-text="p.label"></span>
                    </label>
                </template>
            </div>
        </div>

        
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Fokus Analisis <span class="text-xs text-gray-400 font-normal">(opsional)</span>
            </label>
            <textarea x-model="readerTrendForm.context" rows="2"
                      placeholder="Contoh: Fokus pada pembaca usia 25-35 tahun, konten edukasi investasi untuk pemula"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none"></textarea>
        </div>

        
        <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
            ⚡ Fitur ini menggunakan <strong>1 kuota</strong> per analisis. Tersedia untuk paket Pro & Business.
        </div>

        <button @click="analyzeReaderTrend()" :disabled="readerTrendLoading"
                class="w-full py-3 bg-gradient-to-r from-teal-600 to-cyan-600 text-white font-semibold rounded-xl hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <template x-if="readerTrendLoading">
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
            <span x-text="readerTrendLoading ? 'Menganalisis tren...' : '📖 Analisis Tren Pembaca'"></span>
        </button>
    </div>

    
    <div x-show="readerTrendError" x-cloak class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700" x-text="readerTrendError"></div>

    
    <div x-show="readerTrendResult" x-cloak class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Hasil Analisis Tren Pembaca</h3>
            <button @click="readerTrendCopyResult()"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition"
                    :class="readerTrendCopied ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                <span x-text="readerTrendCopied ? '✅ Tersalin' : '📋 Copy'"></span>
            </button>
        </div>
        <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-wrap text-sm leading-relaxed" x-text="readerTrendResult"></div>
    </div>

</div>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\partials\reader-trend.blade.php ENDPATH**/ ?>