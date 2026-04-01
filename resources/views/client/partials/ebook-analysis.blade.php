{{-- 📚 Ebook Analysis Partial --}}
<div class="space-y-6">

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center gap-3 mb-4">
            <span class="text-2xl">📚</span>
            <div>
                <h2 class="font-semibold text-gray-900">AI Analisis Ebook</h2>
                <p class="text-xs text-gray-500 mt-0.5">Upload ebook PDF — AI akan menganalisis konten, kualitas, dan target pembaca</p>
            </div>
        </div>

        {{-- Tipe Analisis --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Analisis</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <template x-for="opt in [
                    {value:'full', icon:'🔍', label:'Lengkap', desc:'Semua aspek'},
                    {value:'summary', icon:'📝', label:'Ringkasan', desc:'Poin utama'},
                    {value:'quality', icon:'⭐', label:'Kualitas', desc:'Skor & saran'},
                    {value:'audience', icon:'👥', label:'Target', desc:'Profil pembaca'}
                ]" :key="opt.value">
                    <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition"
                           :class="ebookForm.analysis_type === opt.value ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" x-model="ebookForm.analysis_type" :value="opt.value" class="sr-only">
                        <span class="text-lg" x-text="opt.icon"></span>
                        <div>
                            <p class="text-xs font-medium text-gray-900" x-text="opt.label"></p>
                            <p class="text-xs text-gray-400" x-text="opt.desc"></p>
                        </div>
                    </label>
                </template>
            </div>
        </div>

        {{-- Upload PDF --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                📄 Upload Ebook
                <span class="text-xs text-gray-400 font-normal ml-1">(PDF, maks 3 file, 20MB/file)</span>
            </label>
            <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-indigo-300 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition">
                <svg class="w-7 h-7 text-indigo-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-sm text-indigo-500 font-medium">Klik untuk upload PDF</span>
                <span class="text-xs text-gray-400 mt-0.5">Ebook, modul, atau dokumen lainnya</span>
                <input type="file" accept=".pdf" multiple class="sr-only" @change="ebookAddDocs($event)">
            </label>
            <div x-show="ebookDocs.length > 0" class="mt-2 flex flex-wrap gap-2">
                <template x-for="(f, i) in ebookDocs" :key="i">
                    <div class="flex items-center gap-1 px-2 py-1 bg-indigo-100 rounded-lg text-xs text-indigo-800">
                        <span>📄</span>
                        <span x-text="f.name.length > 25 ? f.name.substring(0,25)+'...' : f.name"></span>
                        <button type="button" @click="ebookRemoveDoc(i)" class="ml-1 text-indigo-400 hover:text-red-500">✕</button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Konteks --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Konteks Tambahan <span class="text-xs text-gray-400 font-normal">(opsional)</span>
            </label>
            <textarea x-model="ebookForm.context" rows="2"
                      placeholder="Contoh: Ebook tentang digital marketing untuk UMKM, target pembaca pemula"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"></textarea>
        </div>

        {{-- Info kuota --}}
        <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
            ⚡ Fitur ini menggunakan <strong>1 kuota</strong> per analisis. Tersedia untuk paket Pro & Business.
        </div>

        <button @click="analyzeEbook()" :disabled="ebookLoading"
                class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <template x-if="ebookLoading">
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
            <span x-text="ebookLoading ? 'Menganalisis ebook...' : '📚 Analisis Ebook'"></span>
        </button>
    </div>

    {{-- Error --}}
    <div x-show="ebookError" x-cloak class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700" x-text="ebookError"></div>

    {{-- Result --}}
    <div x-show="ebookResult" x-cloak class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Hasil Analisis Ebook</h3>
            <button @click="ebookCopyResult()"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition"
                    :class="ebookCopied ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                <span x-text="ebookCopied ? '✅ Tersalin' : '📋 Copy'"></span>
            </button>
        </div>
        <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-wrap text-sm leading-relaxed" x-text="ebookResult"></div>
    </div>

</div>
