{{-- 📊 Financial Analysis Partial --}}
<div class="space-y-6">

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-3 mb-4">
            <span class="text-2xl">📊</span>
            <div>
                <h2 class="font-semibold text-gray-900">Analisis Saham & Keuangan</h2>
                <p class="text-xs text-gray-500 mt-0.5">Upload chart saham (gambar) atau laporan keuangan (PDF) — bisa lebih dari satu file</p>
            </div>
        </div>

        {{-- Tipe Analisis --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Analisis</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition"
                       :class="financialForm.analysis_type === 'stock_chart' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" x-model="financialForm.analysis_type" value="stock_chart" class="sr-only">
                    <span class="text-xl">📈</span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Chart Saham</p>
                        <p class="text-xs text-gray-500">Analisis teknikal</p>
                    </div>
                </label>
                <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition"
                       :class="financialForm.analysis_type === 'financial_report' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" x-model="financialForm.analysis_type" value="financial_report" class="sr-only">
                    <span class="text-xl">📄</span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Laporan Keuangan</p>
                        <p class="text-xs text-gray-500">Analisis fundamental</p>
                    </div>
                </label>
                <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition"
                       :class="financialForm.analysis_type === 'combined' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" x-model="financialForm.analysis_type" value="combined" class="sr-only">
                    <span class="text-xl">🔍</span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Gabungan</p>
                        <p class="text-xs text-gray-500">Teknikal + Fundamental</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Upload Chart (Gambar) --}}
        <div class="mb-4" x-show="financialForm.analysis_type !== 'financial_report'">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                📈 Upload Chart Saham
                <span class="text-xs text-gray-400 font-normal ml-1">(JPG/PNG/WebP, maks 5 file, 10MB/file)</span>
            </label>
            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-blue-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                <svg class="w-6 h-6 text-blue-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span class="text-sm text-blue-500">Klik untuk upload chart</span>
                <input type="file" accept="image/*" multiple class="sr-only" @change="financialAddImages($event)">
            </label>
            <div x-show="financialImages.length > 0" class="mt-2 flex flex-wrap gap-2">
                <template x-for="(f, i) in financialImages" :key="i">
                    <div class="flex items-center gap-1 px-2 py-1 bg-blue-100 rounded-lg text-xs text-blue-800">
                        <span x-text="f.name.length > 20 ? f.name.substring(0,20)+'...' : f.name"></span>
                        <button type="button" @click="financialRemoveImage(i)" class="ml-1 text-blue-500 hover:text-red-500">✕</button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Upload PDF --}}
        <div class="mb-4" x-show="financialForm.analysis_type !== 'stock_chart'">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                📄 Upload Dokumen Keuangan
                <span class="text-xs text-gray-400 font-normal ml-1">(PDF, maks 5 file, 20MB/file)</span>
            </label>
            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-green-300 rounded-xl cursor-pointer hover:border-green-400 hover:bg-green-50 transition">
                <svg class="w-6 h-6 text-green-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-sm text-green-500">Klik untuk upload PDF</span>
                <input type="file" accept=".pdf" multiple class="sr-only" @change="financialAddDocs($event)">
            </label>
            <div x-show="financialDocs.length > 0" class="mt-2 flex flex-wrap gap-2">
                <template x-for="(f, i) in financialDocs" :key="i">
                    <div class="flex items-center gap-1 px-2 py-1 bg-green-100 rounded-lg text-xs text-green-800">
                        <span x-text="f.name.length > 20 ? f.name.substring(0,20)+'...' : f.name"></span>
                        <button type="button" @click="financialRemoveDoc(i)" class="ml-1 text-green-500 hover:text-red-500">✕</button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Konteks Tambahan --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Konteks Tambahan <span class="text-xs text-gray-400 font-normal">(opsional)</span>
            </label>
            <textarea x-model="financialForm.context" rows="2"
                      placeholder="Contoh: Saham BBCA, timeframe 1 bulan, fokus pada sinyal beli/jual"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
        </div>

        {{-- Info kuota --}}
        <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
            ⚡ Fitur ini menggunakan <strong>1 kuota</strong> per analisis. Tersedia untuk paket Pro & Business.
        </div>

        <button @click="analyzeFinancial()" :disabled="financialLoading"
                class="w-full py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <template x-if="financialLoading">
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
            <span x-text="financialLoading ? 'Menganalisis...' : '📊 Analisis Sekarang'"></span>
        </button>
    </div>

    {{-- Error --}}
    <div x-show="financialError" x-cloak class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700" x-text="financialError"></div>

    {{-- Result --}}
    <div x-show="financialResult" x-cloak class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Hasil Analisis</h3>
            <button @click="financialCopyResult()"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition"
                    :class="financialCopied ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                <span x-text="financialCopied ? '✅ Tersalin' : '📋 Copy'"></span>
            </button>
        </div>
        <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-wrap text-sm leading-relaxed" x-text="financialResult"></div>
        <p class="text-xs text-gray-400 mt-4 pt-3 border-t border-gray-100">
            ⚠️ Analisis ini hanya untuk tujuan edukasi dan bukan merupakan saran investasi.
        </p>
    </div>

</div>
