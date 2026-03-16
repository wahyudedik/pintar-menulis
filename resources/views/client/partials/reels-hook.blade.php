{{-- 6. AI Hook Generator for Reels/TikTok --}}
<div x-show="generatorType === 'reels-hook'" x-cloak>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-red-500 rounded-lg flex items-center justify-center text-xl">🎬</div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">AI Hook Generator — Reels & TikTok</h2>
                <p class="text-sm text-gray-500">5 hook viral + naskah 15/30 detik siap baca — tinggal rekam dan tempel link di bio</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk/Jasa <span class="text-red-500">*</span></label>
                <input type="text" x-model="reelsForm.product_name" placeholder="Contoh: Kursus Desain Canva Online"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
                <input type="text" x-model="reelsForm.target_audience" placeholder="Contoh: Pemula yang mau belajar desain"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk <span class="text-red-500">*</span></label>
                <textarea x-model="reelsForm.product_desc" rows="3" placeholder="Jelaskan produk/jasa kamu: manfaat utama, keunggulan, hasil yang didapat..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-transparent resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Video</label>
                <select x-model="reelsForm.video_goal" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    <option value="">Pilih tujuan...</option>
                    <option value="awareness">Brand Awareness</option>
                    <option value="sales">Langsung Jualan</option>
                    <option value="leads">Kumpulkan Leads/DM</option>
                    <option value="education">Edukasi Produk</option>
                    <option value="viral">Konten Viral</option>
                </select>
            </div>
        </div>
        <button @click="generateReelsHook()" :disabled="reelsLoading || !reelsForm.product_name || !reelsForm.product_desc"
            class="w-full py-3 bg-gradient-to-r from-pink-500 to-red-500 text-white font-semibold rounded-lg hover:from-pink-600 hover:to-red-600 transition disabled:opacity-50 flex items-center justify-center gap-2">
            <template x-if="reelsLoading"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></template>
            <span x-text="reelsLoading ? 'Generating hook viral...' : '🎬 Generate Hook & Naskah'"></span>
        </button>
        <div x-show="reelsError" x-cloak class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="reelsError"></div>
    </div>

    <div x-show="reelsResult" x-cloak class="mt-5 space-y-5">
        <template x-if="reelsResult">
        <div class="space-y-5">
            {{-- 5 Hooks --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-gray-900 mb-4">🎣 5 Hook Viral — Pilih yang Paling Cocok</h3>
                <div class="space-y-3">
                    <template x-for="(h, idx) in (reelsResult.hooks || [])" :key="idx">
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-pink-300 transition cursor-pointer"
                             @click="reelsSelectedHook = idx"
                             :class="reelsSelectedHook === idx ? 'border-pink-400 bg-pink-50' : ''">
                            <span class="w-6 h-6 rounded-full bg-pink-500 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5" x-text="idx+1"></span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900" x-text="h.hook"></p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs px-2 py-0.5 bg-pink-100 text-pink-700 rounded-full" x-text="h.style"></span>
                                    <span class="text-xs text-gray-500" x-text="h.why_works"></span>
                                </div>
                            </div>
                            <button @click.stop="reelsCopyHook(h.hook, idx)" class="text-xs px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition shrink-0">
                                <span x-text="reelsCopied[idx] ? '✓' : 'Copy'"></span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Scripts --}}
            <template x-for="(script, si) in (reelsResult.scripts || [])" :key="si">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3 bg-gradient-to-r from-pink-50 to-red-50 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-gray-900 text-sm">📝 Naskah <span x-text="script.duration"></span></span>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="'Hook: ' + script.hook"></p>
                        </div>
                        <button @click="reelsCopyScript(script, si)" class="text-xs px-3 py-1 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition">
                            <span x-text="reelsScriptCopied[si] ? '✓ Tersalin!' : 'Copy Naskah'"></span>
                        </button>
                    </div>
                    <div class="p-5">
                        <div class="bg-gray-900 rounded-lg p-4 mb-4 text-sm text-green-400 font-mono whitespace-pre-wrap leading-relaxed" x-text="script.script"></div>
                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                            <p class="text-xs font-bold text-gray-700 mb-1">📱 Caption Postingan:</p>
                            <p class="text-xs text-gray-600" x-text="script.caption"></p>
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <template x-for="tag in (script.hashtags || [])" :key="tag">
                                <span class="px-2 py-0.5 bg-pink-100 text-pink-700 text-xs rounded-full" x-text="tag"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Bio CTA + Tips --}}
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-orange-800 mb-2">🔗 CTA untuk Bio/Komentar</p>
                    <p class="text-sm text-gray-700" x-text="reelsResult.bio_cta"></p>
                    <button @click="reelsCopyBio()" class="mt-2 text-xs px-2 py-1 bg-orange-500 text-white rounded hover:bg-orange-600 transition">
                        <span x-text="reelsBioCopied ? '✓' : 'Copy'"></span>
                    </button>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-blue-800 mb-2">💡 Tips Posting</p>
                    <ul class="space-y-1">
                        <template x-for="tip in (reelsResult.posting_tips || [])" :key="tip">
                            <li class="text-xs text-blue-700 flex items-start gap-1"><span>✓</span><span x-text="tip"></span></li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
        </template>
    </div>

    {{-- Google Search Sources --}}
    <div x-show="reelsResult && (reelsResult?.search_sources || []).length > 0" x-cloak
         class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
        <p class="text-xs font-bold text-blue-800 mb-2">🔍 Berdasarkan pencarian web terkini</p>
        <div class="flex flex-wrap gap-2">
            <template x-for="src in (reelsResult?.search_sources || [])" :key="src.url">
                <a :href="src.url" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-blue-200 rounded-lg text-xs text-blue-700 hover:bg-blue-100 transition">
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span x-text="src.title"></span>
                </a>
            </template>
        </div>
    </div>
</div>
