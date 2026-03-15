{{-- 9. Trend-Based Product Tagging --}}
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
            {{-- Trend Score --}}
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl p-5 text-white">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm opacity-80">Trend Relevance Score</p>
                        <p class="text-4xl font-black" x-text="tagsResult.trend_score + '/100'"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-80">Potensi Kenaikan Traffic</p>
                        <p class="text-2xl font-bold" x-text="tagsResult.traffic_potential"></p>
                    </div>
                </div>
                <p class="text-sm opacity-90" x-text="tagsResult.summary"></p>
            </div>

            {{-- Trending Tags --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-gray-900">🔥 Tag Tren yang Disarankan</h3>
                    <button @click="tagsCopyAll()" class="text-xs px-3 py-1 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                        <span x-text="tagsCopied ? '✓ Tersalin!' : 'Copy Semua Tag'"></span>
                    </button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <template x-for="tag in (tagsResult.trending_tags || [])" :key="tag.tag">
                        <div class="group relative">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-medium cursor-pointer transition"
                                  :class="tag.heat === 'hot' ? 'bg-red-100 text-red-700 hover:bg-red-200' : tag.heat === 'warm' ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-purple-100 text-purple-700 hover:bg-purple-200'">
                                <span x-text="tag.heat === 'hot' ? '🔥' : tag.heat === 'warm' ? '⚡' : '📌'"></span>
                                <span x-text="tag.tag"></span>
                                <span class="text-xs opacity-60" x-text="tag.volume"></span>
                            </span>
                            <div class="absolute bottom-full left-0 mb-1 hidden group-hover:block bg-gray-900 text-white text-xs rounded-lg px-2 py-1 whitespace-nowrap z-10" x-text="tag.reason"></div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Trend Insights --}}
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <p class="text-xs font-bold text-gray-700 mb-3">📊 Tren Sedang Naik</p>
                    <ul class="space-y-2">
                        <template x-for="t in (tagsResult.rising_trends || [])" :key="t.trend">
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 text-xs mt-0.5">↑</span>
                                <div>
                                    <p class="text-xs font-medium text-gray-800" x-text="t.trend"></p>
                                    <p class="text-xs text-gray-500" x-text="t.relevance"></p>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <p class="text-xs font-bold text-gray-700 mb-3">💡 Saran Optimasi</p>
                    <ul class="space-y-2">
                        <template x-for="tip in (tagsResult.optimization_tips || [])" :key="tip">
                            <li class="flex items-start gap-2">
                                <span class="text-purple-500 text-xs mt-0.5">→</span>
                                <p class="text-xs text-gray-700" x-text="tip"></p>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            {{-- Tag String to Copy --}}
            <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                <p class="text-xs font-bold text-purple-800 mb-2">📋 String Tag Siap Pakai</p>
                <p class="text-sm text-gray-700 font-mono break-all" x-text="tagsResult.tag_string"></p>
            </div>
        </div>
        </template>
    </div>
</div>
