{{-- 🏅 Quality Badge Scanner --}}
<div x-show="generatorType === 'quality-badge'" x-cloak>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-yellow-500 rounded-lg flex items-center justify-center text-xl">🏅</div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Quality Badge Scanner</h2>
                <p class="text-sm text-gray-500">Review kualitas produk digital kamu, dapatkan skor & badge untuk tingkatkan kepercayaan pembeli</p>
            </div>
        </div>

        {{-- Penjelasan singkat --}}
        <div class="mb-5 p-3 bg-amber-50 border border-amber-200 rounded-lg">
            <p class="text-xs text-amber-800 leading-relaxed">
                <span class="font-semibold">Cara kerja:</span> AI akan menganalisis deskripsi & dokumentasi produk digital kamu, 
                lalu memberikan skor kualitas (Gold/Silver/Bronze) beserta saran perbaikan. 
                Hasilnya bisa kamu pasang di halaman produk sebagai bukti kualitas.
            </p>
            <div class="flex items-center gap-4 mt-2 text-xs text-amber-700">
                <span>🏅 Gold ≥ 80</span>
                <span>🥈 Silver ≥ 60</span>
                <span>🥉 Bronze ≥ 40</span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" x-model="badgeForm.product_name" placeholder="Contoh: Template Canva Premium, Ebook Marketing 101"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Produk</label>
                <select x-model="badgeForm.asset_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    <option value="">Pilih tipe...</option>
                    <optgroup label="📦 Produk Digital">
                        <option value="Ebook / PDF">Ebook / PDF</option>
                        <option value="Template Desain">Template Desain (Canva, Figma, dll)</option>
                        <option value="Kursus Online">Kursus Online / Video Tutorial</option>
                        <option value="Preset / Filter">Preset / Filter Foto & Video</option>
                        <option value="Spreadsheet / Planner">Spreadsheet / Planner</option>
                        <option value="Digital Product">Produk Digital Lainnya</option>
                    </optgroup>
                    <optgroup label="💻 Software & Tools">
                        <option value="WordPress Plugin">WordPress Plugin / Theme</option>
                        <option value="Mobile App">Mobile App</option>
                        <option value="API Service">API / SaaS</option>
                        <option value="Script / Library">Script / Library</option>
                    </optgroup>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk <span class="text-red-500">*</span></label>
                <textarea x-model="badgeForm.product_desc" rows="3" placeholder="Jelaskan produk kamu: apa fungsinya, untuk siapa, fitur utama, dan apa yang membedakan dari kompetitor..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent resize-none"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Detail Tambahan
                    <span class="text-xs text-gray-400 font-normal">(opsional — paste daftar isi, fitur list, cuplikan kode, atau README)</span>
                </label>
                <textarea x-model="badgeForm.code_or_doc" rows="6" placeholder="Contoh:&#10;- Bab 1: Dasar-dasar Digital Marketing&#10;- Bab 2: Strategi Instagram Ads&#10;- Bonus: Template Copywriting&#10;- Format: PDF 120 halaman, full color"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent resize-none"></textarea>
                <p class="text-xs text-gray-400 mt-1" x-text="(badgeForm.code_or_doc || '').length + '/3000 karakter'"></p>
            </div>
        </div>

        <button @click="generateQualityBadge()" :disabled="badgeLoading || !badgeForm.product_name || !badgeForm.product_desc"
            class="w-full py-3 bg-gradient-to-r from-amber-500 to-yellow-500 text-white font-semibold rounded-lg hover:from-amber-600 hover:to-yellow-600 transition disabled:opacity-50 flex items-center justify-center gap-2">
            <template x-if="badgeLoading"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></template>
            <span x-text="badgeLoading ? 'AI sedang mereview produk...' : '🏅 Scan & Generate Badge'"></span>
        </button>
        <div x-show="badgeError" x-cloak class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="badgeError"></div>
    </div>

    {{-- Result --}}
    <div x-show="badgeResult" x-cloak class="mt-5">
        <template x-if="badgeResult">
        <div class="space-y-4">
            {{-- Badge Display --}}
            <div class="bg-white rounded-xl border-2 p-6 text-center"
                 :class="badgeResult.verified ? 'border-amber-400 bg-amber-50' : 'border-gray-300'">
                <div class="text-5xl mb-3" x-text="badgeResult.verified ? '🏅' : '⚠️'"></div>
                <h3 class="text-2xl font-black mb-1"
                    :class="badgeResult.badge_level === 'Gold' ? 'text-amber-600' : badgeResult.badge_level === 'Silver' ? 'text-gray-500' : badgeResult.badge_level === 'Bronze' ? 'text-orange-600' : 'text-red-500'"
                    x-text="badgeResult.badge || 'Review Complete'"></h3>
                <span class="inline-block px-4 py-1 rounded-full text-sm font-bold mb-3"
                      :class="badgeResult.badge_level === 'Gold' ? 'bg-amber-100 text-amber-800' : badgeResult.badge_level === 'Silver' ? 'bg-gray-100 text-gray-700' : 'bg-orange-100 text-orange-700'"
                      x-text="badgeResult.badge_level + ' Level'"></span>
                <div class="flex items-center justify-center gap-3 mb-3">
                    <span class="text-4xl font-black" :class="badgeResult.overall_score >= 80 ? 'text-green-600' : badgeResult.overall_score >= 60 ? 'text-yellow-600' : 'text-red-600'" x-text="badgeResult.overall_score + '/100'"></span>
                </div>
                <p class="text-sm text-gray-700" x-text="badgeResult.verdict"></p>
                <button @click="badgeCopyText()" class="mt-3 px-4 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition">
                    <span x-text="badgeCopied ? '✓ Tersalin!' : '📋 Copy Teks Badge untuk Halaman Produk'"></span>
                </button>
            </div>

            {{-- Criteria --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-gray-900 mb-4">📊 Detail Penilaian</h3>
                <div class="space-y-3">
                    <template x-for="c in (badgeResult.criteria || [])" :key="c.name">
                        <div class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-xs shrink-0"
                                  :class="c.status === 'Pass' ? 'bg-green-100 text-green-700' : c.status === 'Warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'"
                                  x-text="c.status === 'Pass' ? '✓' : c.status === 'Warning' ? '!' : '✗'"></span>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-700" x-text="c.name"></span>
                                    <span class="text-xs font-bold" x-text="c.score + '/100'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full" :class="c.score >= 80 ? 'bg-green-500' : c.score >= 60 ? 'bg-yellow-500' : 'bg-red-500'" :style="'width:' + c.score + '%'"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="c.note"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Strengths & Improvements --}}
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-green-800 mb-2">✅ Kelebihan</p>
                    <ul class="space-y-1">
                        <template x-for="s in (badgeResult.strengths || [])" :key="s">
                            <li class="text-xs text-green-700 flex items-start gap-1"><span>+</span><span x-text="s"></span></li>
                        </template>
                    </ul>
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-orange-800 mb-2">🔧 Saran Perbaikan</p>
                    <ul class="space-y-1">
                        <template x-for="i in (badgeResult.improvements || [])" :key="i">
                            <li class="text-xs text-orange-700 flex items-start gap-1"><span>→</span><span x-text="i"></span></li>
                        </template>
                    </ul>
                </div>
            </div>

            {{-- Cara Pakai Badge --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-xs font-bold text-blue-800 mb-2">💡 Cara Pakai Badge Ini</p>
                <ul class="space-y-1 text-xs text-blue-700">
                    <li>1. Copy teks badge di atas, paste di halaman produk / deskripsi marketplace</li>
                    <li>2. Tampilkan skor & level badge sebagai social proof untuk calon pembeli</li>
                    <li>3. Perbaiki saran di atas untuk naikkan skor, lalu scan ulang</li>
                </ul>
            </div>
        </div>
        </template>
    </div>
</div>
