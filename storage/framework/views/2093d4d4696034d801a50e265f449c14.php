
<div x-show="generatorType === 'smart-comparison'" x-cloak>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center text-xl">⚖️</div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Smart Comparison Tool</h2>
                <p class="text-sm text-gray-500">Bandingkan 2 produk/aset — AI rangkum keunggulan + deep link siap kirim ke calon klien</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-6 mb-5">
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl space-y-3">
                <p class="text-sm font-bold text-blue-800">🅰️ Produk / Aset A</p>
                <input type="text" x-model="compForm.product_a_name" placeholder="Nama produk A *"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <textarea x-model="compForm.product_a_desc" rows="3" placeholder="Deskripsi, fitur, keunggulan produk A *"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                <input type="text" x-model="compForm.product_a_price" placeholder="Harga (opsional)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div class="p-4 bg-purple-50 border border-purple-200 rounded-xl space-y-3">
                <p class="text-sm font-bold text-purple-800">🅱️ Produk / Aset B</p>
                <input type="text" x-model="compForm.product_b_name" placeholder="Nama produk B *"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <textarea x-model="compForm.product_b_desc" rows="3" placeholder="Deskripsi, fitur, keunggulan produk B *"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"></textarea>
                <input type="text" x-model="compForm.product_b_price" placeholder="Harga (opsional)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
        </div>
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Konteks Pembeli (opsional)</label>
            <input type="text" x-model="compForm.buyer_persona" placeholder="Contoh: Pembeli butuh untuk bisnis kecil dengan budget terbatas"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent">
        </div>
        <button @click="generateComparison()" :disabled="compLoading || !compForm.product_a_name || !compForm.product_b_name"
            class="w-full py-3 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold rounded-lg hover:from-violet-600 hover:to-purple-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
            <template x-if="compLoading"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></template>
            <span x-text="compLoading ? 'AI sedang membandingkan...' : '⚖️ Bandingkan Sekarang'"></span>
        </button>
        <div x-show="compError" x-cloak class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="compError"></div>
    </div>

    <div x-show="compResult" x-cloak class="mt-5 space-y-4">
        <template x-if="compResult">
        <div class="space-y-4">
            
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm font-bold text-gray-800 mb-2">📋 Ringkasan Perbandingan</p>
                <p class="text-sm text-gray-700" x-text="compResult.summary"></p>
            </div>

            
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="font-bold text-blue-900" x-text="compResult.product_a?.name"></p>
                        <span class="text-2xl font-black text-blue-600" x-text="(compResult.product_a?.score || 0) + '/100'"></span>
                    </div>
                    <p class="text-xs text-blue-700 mb-2 font-medium">✅ Keunggulan:</p>
                    <ul class="space-y-1 mb-3">
                        <template x-for="s in (compResult.product_a?.strengths || [])" :key="s">
                            <li class="text-xs text-gray-700 flex items-start gap-1"><span class="text-green-500">+</span><span x-text="s"></span></li>
                        </template>
                    </ul>
                    <p class="text-xs text-blue-700 mb-1 font-medium">⚠️ Kelemahan:</p>
                    <ul class="space-y-1 mb-3">
                        <template x-for="w in (compResult.product_a?.weaknesses || [])" :key="w">
                            <li class="text-xs text-gray-600 flex items-start gap-1"><span class="text-red-400">-</span><span x-text="w"></span></li>
                        </template>
                    </ul>
                    <p class="text-xs bg-blue-100 text-blue-800 rounded-lg px-2 py-1"><span class="font-medium">Best for:</span> <span x-text="compResult.product_a?.best_for"></span></p>
                </div>
                <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="font-bold text-purple-900" x-text="compResult.product_b?.name"></p>
                        <span class="text-2xl font-black text-purple-600" x-text="(compResult.product_b?.score || 0) + '/100'"></span>
                    </div>
                    <p class="text-xs text-purple-700 mb-2 font-medium">✅ Keunggulan:</p>
                    <ul class="space-y-1 mb-3">
                        <template x-for="s in (compResult.product_b?.strengths || [])" :key="s">
                            <li class="text-xs text-gray-700 flex items-start gap-1"><span class="text-green-500">+</span><span x-text="s"></span></li>
                        </template>
                    </ul>
                    <p class="text-xs text-purple-700 mb-1 font-medium">⚠️ Kelemahan:</p>
                    <ul class="space-y-1 mb-3">
                        <template x-for="w in (compResult.product_b?.weaknesses || [])" :key="w">
                            <li class="text-xs text-gray-600 flex items-start gap-1"><span class="text-red-400">-</span><span x-text="w"></span></li>
                        </template>
                    </ul>
                    <p class="text-xs bg-purple-100 text-purple-800 rounded-lg px-2 py-1"><span class="font-medium">Best for:</span> <span x-text="compResult.product_b?.best_for"></span></p>
                </div>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-2 text-xs font-bold text-gray-600">Aspek</th>
                            <th class="text-center px-4 py-2 text-xs font-bold text-blue-600" x-text="compResult.product_a?.name || 'Produk A'"></th>
                            <th class="text-center px-4 py-2 text-xs font-bold text-purple-600" x-text="compResult.product_b?.name || 'Produk B'"></th>
                            <th class="text-center px-4 py-2 text-xs font-bold text-gray-600">Winner</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in (compResult.comparison_table || [])" :key="row.aspect">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-2 text-xs font-medium text-gray-700" x-text="row.aspect"></td>
                                <td class="px-4 py-2 text-xs text-center text-gray-600" x-text="row.product_a"></td>
                                <td class="px-4 py-2 text-xs text-center text-gray-600" x-text="row.product_b"></td>
                                <td class="px-4 py-2 text-xs text-center font-bold"
                                    :class="row.winner === 'A' ? 'text-blue-600' : row.winner === 'B' ? 'text-purple-600' : 'text-gray-500'"
                                    x-text="row.winner === 'A' ? (compResult.product_a?.name || 'A') : row.winner === 'B' ? (compResult.product_b?.name || 'B') : '🤝 Seri'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            
            <div class="bg-gradient-to-r from-violet-50 to-purple-50 border border-violet-200 rounded-xl p-4">
                <p class="text-sm font-bold text-violet-800 mb-2">🏆 Verdict AI</p>
                <p class="text-sm text-gray-800" x-text="compResult.verdict"></p>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-bold text-gray-800">📤 Kirim ke Calon Klien</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-700 mb-3" x-text="compResult.share_message"></div>
                <div class="flex gap-2">
                    <button @click="compCopyShare()" class="flex items-center gap-1.5 px-3 py-2 bg-gray-800 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition">
                        <span x-text="compCopied ? '✓ Tersalin!' : '📋 Copy Pesan'"></span>
                    </button>
                    <button @click="compShareWA()" class="flex items-center gap-1.5 px-3 py-2 bg-green-500 text-white text-xs font-medium rounded-lg hover:bg-green-600 transition">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        Kirim via WA
                    </button>
                </div>
            </div>
        </div>
        </template>
    </div>

    
    <div x-show="compResult && (compResult?.search_sources || []).length > 0" x-cloak
         class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
        <p class="text-xs font-bold text-blue-800 mb-2">🔍 Berdasarkan pencarian web terkini</p>
        <div class="flex flex-wrap gap-2">
            <template x-for="src in (compResult?.search_sources || [])" :key="src.url">
                <a :href="src.url" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-blue-200 rounded-lg text-xs text-blue-700 hover:bg-blue-100 transition">
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span x-text="src.title"></span>
                </a>
            </template>
        </div>
    </div>
</div>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/partials/smart-comparison.blade.php ENDPATH**/ ?>