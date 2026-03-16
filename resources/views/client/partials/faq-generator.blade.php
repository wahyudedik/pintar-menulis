{{-- 5. Automated FAQ Generator --}}
<div x-show="generatorType === 'faq-generator'" x-cloak>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-lg flex items-center justify-center text-xl">❓</div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Automated FAQ Generator</h2>
                <p class="text-sm text-gray-500">Generate 7 FAQ otomatis dari deskripsi produk — tingkatkan trust & konversi halaman produk</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk/Aset <span class="text-red-500">*</span></label>
                <input type="text" x-model="faqForm.product_name" placeholder="Contoh: Jasa Desain Logo Premium"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <input type="text" x-model="faqForm.price" placeholder="Contoh: Rp 500.000"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk <span class="text-red-500">*</span></label>
                <textarea x-model="faqForm.product_desc" rows="4" placeholder="Jelaskan produk/jasa kamu secara detail: fitur, proses, garansi, cara order, dll..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <input type="text" x-model="faqForm.category" placeholder="Contoh: Jasa Desain, Template Digital"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
            </div>
        </div>
        <button @click="generateFaq()" :disabled="faqLoading || !faqForm.product_name || !faqForm.product_desc"
            class="w-full py-3 bg-gradient-to-r from-teal-500 to-cyan-600 text-white font-semibold rounded-lg hover:from-teal-600 hover:to-cyan-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
            <template x-if="faqLoading"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></template>
            <span x-text="faqLoading ? 'Generating FAQ...' : '❓ Generate FAQ Otomatis'"></span>
        </button>
        <div x-show="faqError" x-cloak class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="faqError"></div>
    </div>

    <div x-show="faqResult" x-cloak class="mt-5 space-y-4">
        <template x-if="faqResult">
        <div class="space-y-4">
            {{-- FAQ List --}}
            <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                <div class="px-5 py-3 flex items-center justify-between bg-teal-50 rounded-t-xl">
                    <h3 class="font-bold text-teal-800">❓ FAQ — <span x-text="faqResult.product_name"></span></h3>
                    <button @click="faqCopyAll()" class="text-xs px-3 py-1 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                        <span x-text="faqAllCopied ? '✓ Tersalin!' : '📋 Copy Semua'"></span>
                    </button>
                </div>
                <template x-for="(faq, idx) in (faqResult.faqs || [])" :key="idx">
                    <div class="px-5 py-4" x-data="{ open: idx === 0 }">
                        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
                            <span class="text-sm font-semibold text-gray-900 flex items-start gap-2">
                                <span class="text-teal-500 font-bold shrink-0" x-text="'Q' + (idx+1) + '.'"></span>
                                <span x-text="faq.question"></span>
                            </span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="mt-2 ml-6 text-sm text-gray-600 leading-relaxed" x-text="faq.answer"></div>
                    </div>
                </template>
            </div>

            {{-- Tips --}}
            <div x-show="faqResult.tips" class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <p class="text-xs font-bold text-yellow-800 mb-1">💡 Tips Tampilkan FAQ</p>
                <p class="text-xs text-yellow-700" x-text="faqResult.tips"></p>
            </div>

            {{-- Schema JSON-LD --}}
            <div class="bg-gray-900 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-gray-300">🏷️ Schema.org JSON-LD (paste ke &lt;head&gt; HTML)</p>
                    <button @click="faqCopySchema()" class="text-xs px-2 py-1 bg-gray-600 text-white rounded hover:bg-gray-500 transition">
                        <span x-text="faqSchemaCopied ? '✓' : 'Copy'"></span>
                    </button>
                </div>
                <pre class="text-xs text-green-400 overflow-x-auto whitespace-pre-wrap" x-text="faqResult.schema_faq"></pre>
            </div>
        </div>
        </template>
    </div>
</div>
