
<div x-show="generatorType === 'discount-campaign'" x-cloak>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-orange-500 rounded-lg flex items-center justify-center text-xl">🏷️</div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Discount Campaign Copywriter</h2>
                <p class="text-sm text-gray-500">Generate copy promo persuasif untuk semua platform — Ramadhan, Gajian, Flash Sale, dll</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Promo <span class="text-red-500">*</span></label>
                <input type="text" x-model="discForm.promo_name" placeholder="Contoh: Promo Ramadhan, Flash Sale Gajian"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" x-model="discForm.product_name" placeholder="Contoh: Paket Desain Logo"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Normal</label>
                <input type="text" x-model="discForm.original_price" placeholder="Contoh: Rp 500.000"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Diskon</label>
                <input type="text" x-model="discForm.discount_price" placeholder="Contoh: Rp 299.000"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Persentase Diskon (%)</label>
                <input type="text" x-model="discForm.discount_pct" placeholder="Contoh: 40"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Promo</label>
                <input type="text" x-model="discForm.duration" placeholder="Contoh: 24 jam, 3 hari, sampai akhir bulan"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WA Bisnis</label>
                <input type="text" x-model="discForm.wa_number" placeholder="Contoh: 6281234567890"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk <span class="text-red-500">*</span></label>
                <textarea x-model="discForm.product_desc" rows="2" placeholder="Jelaskan produk/jasa singkat..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
            </div>
        </div>
        <button @click="generateDiscountCampaign()" :disabled="discLoading || !discForm.promo_name || !discForm.product_name || !discForm.product_desc"
            class="w-full py-3 bg-gradient-to-r from-red-500 to-orange-500 text-white font-semibold rounded-lg hover:from-red-600 hover:to-orange-600 transition disabled:opacity-50 flex items-center justify-center gap-2">
            <template x-if="discLoading"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></template>
            <span x-text="discLoading ? 'Generating copy promo...' : '🏷️ Generate Campaign Copy'"></span>
        </button>
        <div x-show="discError" x-cloak class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="discError"></div>
    </div>

    <div x-show="discResult" x-cloak class="mt-5 space-y-4">
        <template x-if="discResult">
        <div class="space-y-4">
            
            <div class="bg-gradient-to-r from-red-500 to-orange-500 rounded-xl p-5 text-white text-center">
                <p class="text-2xl font-black mb-1" x-text="discResult.banner_headline"></p>
                <p class="text-sm opacity-90" x-text="discResult.banner_subtext"></p>
                <p class="text-xs mt-2 bg-white/20 rounded-lg px-3 py-1 inline-block" x-text="discResult.countdown_text"></p>
            </div>

            
            <template x-for="(copy, ci) in (discResult.copies || [])" :key="ci">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-800" x-text="copy.platform"></span>
                        <button @click="discCopy(copy.copy, ci)" class="text-xs px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                            <span x-text="discCopied[ci] ? '✓ Tersalin!' : 'Copy'"></span>
                        </button>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-800 whitespace-pre-wrap" x-text="copy.copy"></p>
                        <div x-show="copy.hashtags" class="flex flex-wrap gap-1 mt-2">
                            <template x-for="tag in (copy.hashtags || [])" :key="tag">
                                <span class="px-2 py-0.5 bg-red-50 text-red-600 text-xs rounded-full" x-text="tag"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            
            <div x-show="discResult.wa_broadcast_link" class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-xs font-bold text-green-800 mb-2">📲 WA Broadcast Deep Link</p>
                <p class="text-xs text-gray-600 font-mono break-all mb-2" x-text="discResult.wa_broadcast_link"></p>
                <button @click="discCopyWALink()" class="text-xs px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                    <span x-text="discWACopied ? '✓ Tersalin!' : 'Copy Link WA Broadcast'"></span>
                </button>
            </div>
        </div>
        </template>
    </div>

    
    <div x-show="discResult && (discResult?.search_sources || []).length > 0" x-cloak
         class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
        <p class="text-xs font-bold text-blue-800 mb-2">🔍 Berdasarkan pencarian web terkini</p>
        <div class="flex flex-wrap gap-2">
            <template x-for="src in (discResult?.search_sources || [])" :key="src.url">
                <a :href="src.url" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-blue-200 rounded-lg text-xs text-blue-700 hover:bg-blue-100 transition">
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span x-text="src.title"></span>
                </a>
            </template>
        </div>
    </div>
</div>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\partials\discount-campaign.blade.php ENDPATH**/ ?>