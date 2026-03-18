
<div x-show="generatorType === 'promo-link'" x-cloak>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center">
                <span class="text-xl">🔗</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Magic Promo Link Generator</h2>
                <p class="text-sm text-gray-500">3 gaya caption (Hard Sell, Soft Sell, Storytelling) + deep link siap share</p>
            </div>
        </div>

        
        <div class="grid md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk / Layanan <span class="text-red-500">*</span></label>
                <input type="text" x-model="promoForm.product_name"
                       placeholder="Contoh: Hijab Satin Premium"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (opsional)</label>
                <input type="text" x-model="promoForm.price"
                       placeholder="Contoh: Rp 150.000 / Rp 99.000 (diskon 30%)"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk <span class="text-red-500">*</span></label>
                <textarea x-model="promoForm.product_desc" rows="3"
                          placeholder="Jelaskan keunggulan, bahan, manfaat, atau keunikan produk kamu..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-transparent resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
                <input type="text" x-model="promoForm.target_audience"
                       placeholder="Contoh: Ibu rumah tangga 25-40 tahun"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Detail Promo (opsional)</label>
                <input type="text" x-model="promoForm.promo_detail"
                       placeholder="Contoh: Gratis ongkir, beli 2 gratis 1, flash sale 24 jam"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nomor WhatsApp Bisnis
                    <span class="text-xs text-gray-400 font-normal">(untuk deep link WA, format: 628xxx)</span>
                </label>
                <input type="text" x-model="promoForm.wa_number"
                       placeholder="Contoh: 6281234567890"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>
        </div>

        <button @click="generatePromoLink()"
                :disabled="promoLoading || !promoForm.product_name || !promoForm.product_desc"
                class="w-full py-3 bg-gradient-to-r from-pink-500 to-rose-600 text-white font-semibold rounded-lg hover:from-pink-600 hover:to-rose-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <template x-if="promoLoading">
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </template>
            <span x-text="promoLoading ? 'Generating 3 variasi caption...' : '✨ Generate Magic Promo Link'"></span>
        </button>

        
        <div x-show="promoError" x-cloak class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="promoError"></div>
    </div>

    
    <div x-show="promoResult" x-cloak class="mt-6 space-y-6">

        
        <template x-if="promoResult && promoResult.tips">
            <div class="bg-gradient-to-r from-pink-50 to-rose-50 border border-pink-200 rounded-xl p-4">
                <h3 class="text-sm font-bold text-pink-800 mb-3">💡 Tips Penggunaan</h3>
                <div class="grid md:grid-cols-3 gap-3 text-xs text-pink-700">
                    <div class="bg-white/70 rounded-lg p-3">
                        <span class="font-semibold block mb-1">🔥 Hard Sell</span>
                        <span x-text="promoResult.tips.hard_sell"></span>
                    </div>
                    <div class="bg-white/70 rounded-lg p-3">
                        <span class="font-semibold block mb-1">✨ Soft Sell</span>
                        <span x-text="promoResult.tips.soft_sell"></span>
                    </div>
                    <div class="bg-white/70 rounded-lg p-3">
                        <span class="font-semibold block mb-1">💬 Storytelling</span>
                        <span x-text="promoResult.tips.storytelling"></span>
                    </div>
                </div>
            </div>
        </template>

        
        <template x-if="promoResult && promoResult.captions">
            <div class="space-y-5">
                <template x-for="(caption, idx) in promoResult.captions" :key="idx">
                    <div class="bg-white rounded-xl border-2 shadow-sm overflow-hidden"
                         :class="{
                             'border-red-200': caption.style === 'Hard Sell',
                             'border-blue-200': caption.style === 'Soft Sell',
                             'border-purple-200': caption.style === 'Storytelling'
                         }">
                        
                        <div class="px-5 py-3 flex items-center justify-between"
                             :class="{
                                 'bg-gradient-to-r from-red-50 to-orange-50': caption.style === 'Hard Sell',
                                 'bg-gradient-to-r from-blue-50 to-cyan-50': caption.style === 'Soft Sell',
                                 'bg-gradient-to-r from-purple-50 to-pink-50': caption.style === 'Storytelling'
                             }">
                            <div class="flex items-center gap-2">
                                <span class="text-xl" x-text="caption.emoji"></span>
                                <div>
                                    <span class="font-bold text-gray-900 text-sm" x-text="caption.style"></span>
                                    <span class="ml-2 text-xs px-2 py-0.5 rounded-full font-medium"
                                          :class="{
                                              'bg-red-100 text-red-700': caption.style === 'Hard Sell',
                                              'bg-blue-100 text-blue-700': caption.style === 'Soft Sell',
                                              'bg-purple-100 text-purple-700': caption.style === 'Storytelling'
                                          }"
                                          x-text="caption.tone"></span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 italic" x-text="'Best for: ' + caption.best_for"></span>
                        </div>

                        <div class="p-5">
                            
                            <div class="bg-gray-50 rounded-lg p-4 mb-4 text-sm text-gray-800 whitespace-pre-wrap leading-relaxed font-mono"
                                 x-text="caption.caption"></div>

                            
                            <div class="grid grid-cols-2 gap-3 mb-4 text-xs">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-2">
                                    <span class="font-semibold text-yellow-800 block mb-1">🎣 Hook</span>
                                    <span class="text-yellow-700" x-text="caption.hook"></span>
                                </div>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-2">
                                    <span class="font-semibold text-green-800 block mb-1">📣 CTA</span>
                                    <span class="text-green-700" x-text="caption.cta"></span>
                                </div>
                            </div>

                            
                            <div class="flex flex-wrap gap-1 mb-4">
                                <template x-for="tag in caption.hashtags" :key="tag">
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full" x-text="tag"></span>
                                </template>
                            </div>

                            
                            <div class="flex flex-wrap gap-2">
                                
                                <button @click="promoCopyCaption(caption, idx)"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-gray-800 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span x-text="promoCopied[idx] ? '✓ Tersalin!' : 'Copy Caption'"></span>
                                </button>

                                
                                <button @click="promoShareWA(caption)"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-green-500 text-white text-xs font-medium rounded-lg hover:bg-green-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    WhatsApp
                                </button>

                                
                                <button @click="promoShareX(caption)"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-black text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                    Post ke X
                                </button>

                                
                                <button @click="promoShareLinkedIn(caption)"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                    LinkedIn
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        
        <div class="text-center">
            <button @click="generatePromoLink()"
                    :disabled="promoLoading"
                    class="px-6 py-2 border-2 border-pink-400 text-pink-600 font-medium rounded-lg hover:bg-pink-50 transition text-sm disabled:opacity-50">
                🔄 Generate Ulang
            </button>
        </div>
    </div>
</div>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\partials\promo-link-generator.blade.php ENDPATH**/ ?>