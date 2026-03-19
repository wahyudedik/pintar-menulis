
<div x-show="generatorType === 'product-explainer'" x-cloak>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                <span class="text-xl">💬</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">AI Product Explainer</h2>
                <p class="text-sm text-gray-500">Generate pesan WA otomatis — calon pembeli klik "Tanya Detail", langsung masuk chat dengan pesan profesional</p>
            </div>
        </div>

        
        <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-xs font-semibold text-green-800 mb-2">⚡ Cara Kerja</p>
            <div class="flex items-center gap-2 text-xs text-green-700 flex-wrap">
                <span class="bg-white border border-green-200 rounded-lg px-2 py-1">1. Isi detail produk</span>
                <span class="text-green-400">→</span>
                <span class="bg-white border border-green-200 rounded-lg px-2 py-1">2. AI generate 4 variasi pesan WA</span>
                <span class="text-green-400">→</span>
                <span class="bg-white border border-green-200 rounded-lg px-2 py-1">3. Salin tombol "Tanya Detail" ke bio/postingan</span>
                <span class="text-green-400">→</span>
                <span class="bg-white border border-green-200 rounded-lg px-2 py-1">4. Pembeli klik → WA terbuka dengan pesan otomatis ✅</span>
            </div>
        </div>

        
        <div class="grid md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk / Layanan <span class="text-red-500">*</span></label>
                <input type="text" x-model="explainerForm.product_name"
                       placeholder="Contoh: Tas Kulit Handmade Premium"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <input type="text" x-model="explainerForm.price"
                       placeholder="Contoh: Rp 350.000"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk <span class="text-red-500">*</span></label>
                <textarea x-model="explainerForm.product_desc" rows="3"
                          placeholder="Jelaskan produk kamu: bahan, ukuran, warna, keunggulan, dll..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fitur / Keunggulan Utama</label>
                <textarea x-model="explainerForm.features" rows="2"
                          placeholder="Contoh: Anti air, jahitan tangan, bisa custom nama, garansi 1 tahun"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Target Pembeli</label>
                <input type="text" x-model="explainerForm.target_buyer"
                       placeholder="Contoh: Wanita karir 25-35 tahun"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko / Penjual</label>
                <input type="text" x-model="explainerForm.seller_name"
                       placeholder="Contoh: Toko Kirana Leather"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nomor WhatsApp Bisnis <span class="text-red-500">*</span>
                    <span class="text-xs text-gray-400 font-normal">(format: 628xxx)</span>
                </label>
                <input type="text" x-model="explainerForm.wa_number"
                       placeholder="Contoh: 6281234567890"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>

        <button @click="generateExplainer()"
                :disabled="explainerLoading || !explainerForm.product_name || !explainerForm.product_desc || !explainerForm.wa_number"
                class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <template x-if="explainerLoading">
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </template>
            <span x-text="explainerLoading ? 'AI sedang menyiapkan pesan WA...' : '🤖 Generate Pesan WA Otomatis'"></span>
        </button>

        <div x-show="explainerError" x-cloak class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="explainerError"></div>
    </div>

    
    <div x-show="explainerResult" x-cloak class="mt-6 space-y-5">

        
        <template x-if="explainerResult && explainerResult.seller_tips">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="text-xs font-bold text-amber-800 mb-2">💡 Tips Penjual Profesional</p>
                <ul class="space-y-1">
                    <template x-for="tip in explainerResult.seller_tips" :key="tip">
                        <li class="text-xs text-amber-700 flex items-start gap-1.5">
                            <span class="mt-0.5 text-amber-500">✓</span>
                            <span x-text="tip"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </template>

        
        <template x-if="explainerResult && explainerResult.messages">
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-gray-700">📱 4 Variasi Pesan WhatsApp — Pilih sesuai kebutuhan pembeli</h3>

                <template x-for="(msg, idx) in explainerResult.messages" :key="idx">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        
                        <div class="px-4 py-3 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="font-semibold text-gray-900 text-sm" x-text="msg.label"></span>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="msg.description"></p>
                            </div>
                            
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Siap Kirim</span>
                        </div>

                        <div class="p-4">
                            
                            <div class="bg-[#ECE5DD] rounded-xl p-3 mb-4 relative">
                                <div class="bg-white rounded-lg p-3 shadow-sm max-w-xs ml-auto">
                                    <p class="text-xs text-gray-800 whitespace-pre-wrap leading-relaxed" x-text="msg.message"></p>
                                    <p class="text-right text-[10px] text-gray-400 mt-1">✓✓</p>
                                </div>
                                <p class="text-[10px] text-gray-500 text-center mt-2">Preview tampilan di WhatsApp</p>
                            </div>

                            
                            <div class="flex flex-wrap gap-2">
                                
                                <button @click="explainerCopyMsg(msg.message, idx)"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-gray-800 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span x-text="explainerCopied[idx] ? '✓ Tersalin!' : 'Copy Pesan'"></span>
                                </button>

                                
                                <button @click="explainerOpenWA(msg.message)"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-green-500 text-white text-xs font-medium rounded-lg hover:bg-green-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    Buka di WA
                                </button>

                                
                                <button @click="explainerCopyLink(msg.message, idx)"
                                        class="flex items-center gap-1.5 px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    <span x-text="explainerLinkCopied[idx] ? '✓ Link Tersalin!' : 'Copy Link Tanya Detail'"></span>
                                </button>
                            </div>

                            
                            <div class="mt-3 p-2 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <p class="text-[10px] text-gray-400 mb-1 font-medium">🔗 Deep Link (tempel di bio/postingan):</p>
                                <p class="text-[10px] text-gray-600 break-all font-mono" x-text="explainerBuildLink(msg.message)"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        
        <template x-if="explainerResult && explainerResult.quick_replies">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-xs font-bold text-blue-800 mb-3">💬 Pertanyaan Umum Calon Pembeli (siapkan jawaban ini)</p>
                <div class="space-y-2">
                    <template x-for="(q, qi) in explainerResult.quick_replies" :key="qi">
                        <div class="flex items-start gap-2 bg-white rounded-lg p-2 border border-blue-100">
                            <span class="text-blue-400 text-xs font-bold mt-0.5" x-text="'Q' + (qi+1) + '.'"></span>
                            <span class="text-xs text-gray-700" x-text="q"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        
        <div class="text-center">
            <button @click="generateExplainer()"
                    :disabled="explainerLoading"
                    class="px-6 py-2 border-2 border-green-400 text-green-600 font-medium rounded-lg hover:bg-green-50 transition text-sm disabled:opacity-50">
                🔄 Generate Ulang
            </button>
        </div>
    </div>
</div>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/partials/product-explainer.blade.php ENDPATH**/ ?>