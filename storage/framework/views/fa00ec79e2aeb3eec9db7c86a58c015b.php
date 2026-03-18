
<div x-show="generatorType === 'trend-tags'" x-cloak>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center text-xl">📈</div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Trend-Based Product Tagging</h2>
                <p class="text-sm text-gray-500">AI menyarankan tag tren teknologi yang relevan agar produk naik di pencarian saat tren sedang tinggi</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk/Aset <span class="text-red-500">*</span></label>
                <input type="text" x-model="tagsForm.product_name" placeholder="Contoh: AI Chatbot Template"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Produk</label>
                <select x-model="tagsForm.category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Pilih kategori...</option>
                    <option value="AI & Machine Learning">AI & Machine Learning</option>
                    <option value="Web Development">Web Development</option>
                    <option value="Mobile App">Mobile App</option>
                    <option value="UI/UX Design">UI/UX Design</option>
                    <option value="Data Science">Data Science</option>
                    <option value="Cybersecurity">Cybersecurity</option>
                    <option value="Cloud & DevOps">Cloud & DevOps</option>
                    <option value="E-commerce">E-commerce</option>
                    <option value="Digital Marketing">Digital Marketing</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk <span class="text-red-500">*</span></label>
                <textarea x-model="tagsForm.product_desc" rows="3" placeholder="Jelaskan fitur utama, teknologi yang digunakan, dan target pengguna..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tag Saat Ini (opsional)</label>
                <input type="text" x-model="tagsForm.current_tags" placeholder="Contoh: laravel, php, api"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <p class="text-xs text-gray-400 mt-1">Pisahkan dengan koma</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Target Platform</label>
                <select x-model="tagsForm.platform" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="marketplace">Marketplace Digital</option>
                    <option value="github">GitHub</option>
                    <option value="social_media">Social Media</option>
                    <option value="blog">Blog/SEO</option>
                </select>
            </div>
        </div>
        <button @click="generateTrendTags()" :disabled="tagsLoading || !tagsForm.product_name || !tagsForm.product_desc"
            class="w-full py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold rounded-lg hover:from-purple-600 hover:to-pink-600 transition disabled:opacity-50 flex items-center justify-center gap-2">
            <template x-if="tagsLoading"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></template>
            <span x-text="tagsLoading ? 'AI menganalisis tren...' : '📈 Generate Trend Tags'"></span>
        </button>
        <div x-show="tagsError" x-cloak class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="tagsError"></div>
    </div>

    <div x-show="tagsResult" x-cloak class="mt-5 space-y-4">
        <template x-if="tagsResult">
        <div class="space-y-4">
            
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl p-5 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wide opacity-80">Trend Alert</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/20"
                          x-text="'Urgensi: ' + (tagsResult.action_urgency || 'Medium')"></span>
                </div>
                <p class="text-sm font-medium" x-text="tagsResult.trend_alert"></p>
                <div class="mt-3 grid grid-cols-2 gap-3 text-xs">
                    <div class="bg-white/10 rounded-lg p-2">
                        <p class="opacity-70 mb-0.5">Dampak SEO</p>
                        <p class="font-semibold" x-text="tagsResult.seo_impact"></p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-2">
                        <p class="opacity-70 mb-0.5">Waktu Terbaik Posting</p>
                        <p class="font-semibold" x-text="tagsResult.best_time_to_post"></p>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-gray-900">🔥 Tag Tren yang Disarankan</h3>
                    <button @click="tagsCopyAll()" class="text-xs px-3 py-1 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                        <span x-text="tagsCopied ? '✓ Tersalin!' : 'Copy Semua Tag'"></span>
                    </button>
                </div>
                <div class="space-y-2">
                    <template x-for="tag in (tagsResult.trending_tags || [])" :key="tag.tag">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-medium text-sm text-gray-900" x-text="tag.tag"></span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                          :class="tag.relevance === 'High' ? 'bg-green-100 text-green-700' : tag.relevance === 'Medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600'"
                                          x-text="tag.relevance"></span>
                                </div>
                                <p class="text-xs text-gray-500" x-text="tag.reason"></p>
                                <p class="text-xs text-purple-600 mt-0.5" x-text="'Volume: ' + (tag.search_volume || '-')"></p>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-lg font-black text-purple-600" x-text="tag.trend_score"></div>
                                <div class="text-xs text-gray-400">score</div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-green-800 mb-2">✅ Tag yang Direkomendasikan Sekarang</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="tag in (tagsResult.recommended_tags || [])" :key="tag">
                            <span class="px-2 py-1 bg-green-600 text-white text-xs rounded-full font-medium" x-text="tag"></span>
                        </template>
                    </div>
                </div>
                <div x-show="(tagsResult.remove_tags || []).length > 0" class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-red-800 mb-2">🗑️ Tag Lama yang Perlu Dihapus</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="tag in (tagsResult.remove_tags || [])" :key="tag">
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full line-through" x-text="tag"></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        </template>
    </div>

    
    <div x-show="tagsResult && (tagsResult?.search_sources || []).length > 0" x-cloak
         class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
        <p class="text-xs font-bold text-blue-800 mb-2">🔍 Berdasarkan pencarian web terkini</p>
        <div class="flex flex-wrap gap-2">
            <template x-for="src in (tagsResult?.search_sources || [])" :key="src.url">
                <a :href="src.url" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-blue-200 rounded-lg text-xs text-blue-700 hover:bg-blue-100 transition">
                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span x-text="src.title"></span>
                </a>
            </template>
        </div>
    </div>
</div>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/partials/trend-tags.blade.php ENDPATH**/ ?>