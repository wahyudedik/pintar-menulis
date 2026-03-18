
<div x-show="generatorType === 'lead-magnet'" x-cloak>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-500 rounded-lg flex items-center justify-center text-xl">🧲</div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">AI Lead Magnet Creator</h2>
                <p class="text-sm text-gray-500">Buat "versi gratis" atau sampel aset sebagai pancingan untuk mendapatkan email/kontak calon pembeli</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk/Jasa <span class="text-red-500">*</span></label>
                <input type="text" x-model="magnetForm.product_name" placeholder="Contoh: Kursus Laravel Advanced"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Produk</label>
                <select x-model="magnetForm.product_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <option value="">Pilih tipe...</option>
                    <option value="Kursus Online">Kursus Online</option>
                    <option value="Template/Theme">Template/Theme</option>
                    <option value="Plugin/Script">Plugin/Script</option>
                    <option value="E-book">E-book</option>
                    <option value="Jasa Freelance">Jasa Freelance</option>
                    <option value="SaaS/Aplikasi">SaaS/Aplikasi</option>
                    <option value="Konsultasi">Konsultasi</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Produk</label>
                <input type="text" x-model="magnetForm.price" placeholder="Contoh: Rp 500.000"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Target Audiens</label>
                <input type="text" x-model="magnetForm.target_audience" placeholder="Contoh: Developer pemula, UMKM, Desainer"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk <span class="text-red-500">*</span></label>
                <textarea x-model="magnetForm.product_desc" rows="3" placeholder="Jelaskan isi produk, fitur utama, dan manfaat yang didapat pembeli..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Lead Magnet</label>
                <select x-model="magnetForm.goal" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <option value="email">Kumpulkan Email</option>
                    <option value="whatsapp">Dapatkan Kontak WA</option>
                    <option value="follow">Tambah Followers</option>
                    <option value="trial">Trial/Demo Produk</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WA (opsional)</label>
                <input type="text" x-model="magnetForm.wa_number" placeholder="Contoh: 6281234567890"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
            </div>
        </div>
        <button @click="generateLeadMagnet()" :disabled="magnetLoading || !magnetForm.product_name || !magnetForm.product_desc"
            class="w-full py-3 bg-gradient-to-r from-teal-500 to-cyan-500 text-white font-semibold rounded-lg hover:from-teal-600 hover:to-cyan-600 transition disabled:opacity-50 flex items-center justify-center gap-2">
            <template x-if="magnetLoading"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></template>
            <span x-text="magnetLoading ? 'AI merancang lead magnet...' : '🧲 Generate Lead Magnet'"></span>
        </button>
        <div x-show="magnetError" x-cloak class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="magnetError"></div>
    </div>

    <div x-show="magnetResult" x-cloak class="mt-5 space-y-4">
        <template x-if="magnetResult">
        <div class="space-y-4">
            
            <div class="bg-gradient-to-r from-teal-500 to-cyan-500 rounded-xl p-5 text-white">
                <p class="text-xs opacity-80 mb-1">Ide Lead Magnet Terbaik</p>
                <h3 class="text-xl font-black mb-2" x-text="magnetResult.magnet_title"></h3>
                <p class="text-sm opacity-90" x-text="magnetResult.magnet_desc"></p>
                <p class="text-xs mt-2 opacity-80 italic" x-show="magnetResult.summary" x-text="magnetResult.summary"></p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs" x-text="'Format: ' + magnetResult.format"></span>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs" x-text="'Effort: ' + magnetResult.effort_level"></span>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs" x-text="'Konversi: ' + magnetResult.conversion_potential"></span>
                </div>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-gray-900 mb-3">📦 Apa yang Dimasukkan ke Lead Magnet</h3>
                <ul class="space-y-2">
                    <template x-for="item in (magnetResult.include_items || [])" :key="item.item">
                        <li class="flex items-start gap-3 p-3 bg-teal-50 rounded-lg">
                            <span class="text-teal-500 font-bold text-sm mt-0.5">✓</span>
                            <div>
                                <p class="text-sm font-medium text-gray-800" x-text="item.item"></p>
                                <p class="text-xs text-gray-500" x-text="item.reason"></p>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-sm font-bold text-gray-800">📝 Copy Landing Page / Opt-in</span>
                    <button @click="magnetCopyLanding()" class="text-xs px-3 py-1 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition">
                        <span x-text="magnetCopied ? '✓ Tersalin!' : 'Copy'"></span>
                    </button>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Headline</p>
                        <p class="text-base font-bold text-gray-900" x-text="magnetResult.landing_copy?.headline"></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Sub-headline</p>
                        <p class="text-sm text-gray-700" x-text="magnetResult.landing_copy?.subheadline"></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Bullet Points</p>
                        <ul class="space-y-1">
                            <template x-for="b in (magnetResult.landing_copy?.bullets || [])" :key="b">
                                <li class="text-sm text-gray-700 flex items-start gap-2"><span class="text-teal-500">✓</span><span x-text="b"></span></li>
                            </template>
                        </ul>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">CTA Button</p>
                        <span class="inline-block px-4 py-2 bg-teal-500 text-white text-sm font-bold rounded-lg" x-text="magnetResult.landing_copy?.cta_button"></span>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-bold text-gray-900 mb-3">📧 Urutan Follow-up (Email/WA)</h3>
                <div class="space-y-3">
                    <template x-for="(msg, mi) in (magnetResult.followup_sequence || [])" :key="mi">
                        <div class="flex gap-3 p-3 border border-gray-100 rounded-lg">
                            <div class="w-8 h-8 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center text-xs font-bold shrink-0" x-text="mi + 1"></div>
                            <div>
                                <p class="text-xs font-bold text-gray-500" x-text="msg.timing"></p>
                                <p class="text-sm font-medium text-gray-800" x-text="msg.subject"></p>
                                <p class="text-xs text-gray-600 mt-0.5" x-text="msg.preview"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            
            <div x-show="magnetResult.wa_link" class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-xs font-bold text-green-800 mb-2">📲 WA Deep Link untuk Opt-in</p>
                <p class="text-xs text-gray-600 font-mono break-all mb-2" x-text="magnetResult.wa_link"></p>
                <button @click="magnetCopyWA()" class="text-xs px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                    <span x-text="magnetWACopied ? '✓ Tersalin!' : 'Copy WA Link'"></span>
                </button>
            </div>
        </div>
        </template>
    </div>

    
    <div x-show="magnetResult && (magnetResult?.search_sources || []).length > 0" x-cloak
         class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
        <p class="text-xs font-bold text-blue-800 mb-2">🔍 Berdasarkan pencarian web terkini</p>
        <div class="flex flex-wrap gap-2">
            <template x-for="src in (magnetResult?.search_sources || [])" :key="src.url">
                <a :href="src.url" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-blue-200 rounded-lg text-xs text-blue-700 hover:bg-blue-100 transition">
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span x-text="src.title"></span>
                </a>
            </template>
        </div>
    </div>
</div>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\partials\lead-magnet.blade.php ENDPATH**/ ?>