
<div x-show="generatorType === 'seo-metadata'" x-cloak>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-xl">🔍</div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">SEO Metadata Auto-Fill</h2>
                <p class="text-sm text-gray-500">Generate Meta Title & Description optimal untuk Google — tanpa perlu ahli SEO</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk/Aset <span class="text-red-500">*</span></label>
                <input type="text" x-model="seoForm.product_name" placeholder="Contoh: Template Invoice Otomatis Excel"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <input type="text" x-model="seoForm.category" placeholder="Contoh: Template Excel, Jasa Desain"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk <span class="text-red-500">*</span></label>
                <textarea x-model="seoForm.product_desc" rows="3" placeholder="Jelaskan produk/aset kamu secara singkat..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Target Keyword Utama</label>
                <input type="text" x-model="seoForm.keywords" placeholder="Contoh: template invoice gratis"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL Slug</label>
                <input type="text" x-model="seoForm.url" placeholder="Contoh: template-invoice-otomatis-excel"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        <button @click="generateSeoMetadata()" :disabled="seoLoading || !seoForm.product_name || !seoForm.product_desc"
            class="w-full py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-indigo-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
            <template x-if="seoLoading"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></template>
            <span x-text="seoLoading ? 'Generating SEO metadata...' : '🔍 Generate SEO Metadata'"></span>
        </button>
        <div x-show="seoError" x-cloak class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="seoError"></div>
    </div>

    <div x-show="seoResult" x-cloak class="mt-5 space-y-4">
        
        <template x-if="seoResult">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900">📊 SEO Score</h3>
                <div class="flex items-center gap-2">
                    <div class="w-32 bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all"
                             :class="seoResult.seo_score >= 80 ? 'bg-green-500' : seoResult.seo_score >= 60 ? 'bg-yellow-500' : 'bg-red-500'"
                             :style="'width:' + (seoResult.seo_score || 0) + '%'"></div>
                    </div>
                    <span class="font-bold text-lg" :class="seoResult.seo_score >= 80 ? 'text-green-600' : 'text-yellow-600'" x-text="(seoResult.seo_score || 0) + '/100'"></span>
                </div>
            </div>

            
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-blue-800 uppercase tracking-wide">Meta Title</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500" x-text="(seoResult.meta_title || '').length + '/60 karakter'"></span>
                        <button @click="seoCopy(seoResult.meta_title, 'title')" class="text-xs px-2 py-0.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            <span x-text="seoCopied.title ? '✓' : 'Copy'"></span>
                        </button>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-900" x-text="seoResult.meta_title"></p>
                <p class="text-xs text-gray-500 mt-1">Preview Google: <span class="text-blue-600 underline" x-text="seoResult.meta_title"></span></p>
            </div>

            
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-green-800 uppercase tracking-wide">Meta Description</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500" x-text="(seoResult.meta_description || '').length + '/160 karakter'"></span>
                        <button @click="seoCopy(seoResult.meta_description, 'desc')" class="text-xs px-2 py-0.5 bg-green-600 text-white rounded hover:bg-green-700 transition">
                            <span x-text="seoCopied.desc ? '✓' : 'Copy'"></span>
                        </button>
                    </div>
                </div>
                <p class="text-sm text-gray-800" x-text="seoResult.meta_description"></p>
            </div>

            
            <div class="mb-4">
                <p class="text-xs font-bold text-gray-700 mb-2">🔑 Focus Keyword & Secondary Keywords</p>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-blue-600 text-white text-xs rounded-full font-medium" x-text="seoResult.focus_keyword"></span>
                    <template x-for="kw in (seoResult.secondary_keywords || [])" :key="kw">
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full" x-text="kw"></span>
                    </template>
                </div>
            </div>

            
            <div class="grid md:grid-cols-2 gap-3 mb-4">
                <div class="p-3 bg-purple-50 border border-purple-200 rounded-lg">
                    <p class="text-xs font-bold text-purple-800 mb-1">📱 OG Title (Social Share)</p>
                    <p class="text-xs text-gray-700" x-text="seoResult.og_title"></p>
                </div>
                <div class="p-3 bg-orange-50 border border-orange-200 rounded-lg">
                    <p class="text-xs font-bold text-orange-800 mb-1">🏷️ Schema Type</p>
                    <p class="text-xs text-gray-700" x-text="seoResult.schema_type"></p>
                    <p class="text-xs text-gray-500 mt-1">Slug: <span class="font-mono" x-text="seoResult.slug_suggestion"></span></p>
                </div>
            </div>

            
            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-xs font-bold text-yellow-800 mb-2">💡 Tips SEO</p>
                <ul class="space-y-1">
                    <template x-for="tip in (seoResult.tips || [])" :key="tip">
                        <li class="text-xs text-yellow-700 flex items-start gap-1"><span>✓</span><span x-text="tip"></span></li>
                    </template>
                </ul>
            </div>

            
            <button @click="seoCopyAll()" class="mt-4 w-full py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
                <span x-text="seoCopied.all ? '✓ Semua Tersalin!' : '📋 Copy Semua Metadata'"></span>
            </button>
        </div>
        </template>
    </div>

    
    <div x-show="seoResult && (seoResult?.search_sources || []).length > 0" x-cloak
         class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
        <p class="text-xs font-bold text-blue-800 mb-2">🔍 Berdasarkan pencarian web terkini</p>
        <div class="flex flex-wrap gap-2">
            <template x-for="src in (seoResult?.search_sources || [])" :key="src.url">
                <a :href="src.url" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-blue-200 rounded-lg text-xs text-blue-700 hover:bg-blue-100 transition">
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span x-text="src.title"></span>
                </a>
            </template>
        </div>
    </div>
</div>

<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/partials/seo-metadata.blade.php ENDPATH**/ ?>