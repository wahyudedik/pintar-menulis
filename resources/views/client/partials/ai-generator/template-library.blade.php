        <div x-show="generatorType === 'templates'" x-cloak class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">📚 Template Library</h3>
                <p class="text-gray-600">500+ template siap pakai untuk berbagai kebutuhan konten</p>
            </div>

            <!-- Template Filters -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select x-model="templateFilters.category" @change="filterTemplates()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kategori</option>
                        <option value="viral_clickbait">🔥 Viral & Clickbait</option>
                        <option value="trend_fresh_ideas">✨ Trend & Fresh Ideas</option>
                        <option value="event_promo">🎉 Event & Promo</option>
                        <option value="hr_recruitment">💼 HR & Recruitment</option>
                        <option value="branding_tagline">🎯 Branding & Tagline</option>
                        <option value="education">🎓 Education & Institution</option>
                        <option value="monetization">💰 Monetization</option>
                        <option value="video_monetization">📹 Video Content</option>
                        <option value="freelance">💻 Freelance</option>
                        <option value="digital_products">📱 Digital Products</option>
                        <option value="ebook_publishing">📚 eBook & Publishing</option>
                        <option value="academic_writing">🎓 Academic Writing</option>
                        <option value="affiliate_marketing">🤝 Affiliate Marketing</option>
                        <option value="blog_seo">📝 Blog & SEO</option>
                    </select>
                </div>

                <!-- Platform Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                    <select x-model="templateFilters.platform" @change="filterTemplates()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Platform</option>
                        <option value="instagram">Instagram</option>
                        <option value="facebook">Facebook</option>
                        <option value="tiktok">TikTok</option>
                        <option value="youtube">YouTube</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="twitter">Twitter/X</option>
                        <option value="shopee">Shopee</option>
                        <option value="tokopedia">Tokopedia</option>
                    </select>
                </div>

                <!-- Tone Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tone</label>
                    <select x-model="templateFilters.tone" @change="filterTemplates()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tone</option>
                        <option value="casual">Casual</option>
                        <option value="formal">Formal</option>
                        <option value="persuasive">Persuasive</option>
                        <option value="funny">Funny</option>
                        <option value="emotional">Emotional</option>
                        <option value="educational">Educational</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Template</label>
                    <input type="text" x-model="templateFilters.search" @input="filterTemplates()" 
                           placeholder="Cari template..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Template Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <template x-for="template in filteredTemplates" :key="template.id">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                         @click="selectTemplate(template)">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 mb-1" x-text="template.title"></h4>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span x-text="template.category_label"></span>
                                    <span>•</span>
                                    <span x-text="template.platform || 'Universal'"></span>
                                </div>
                            </div>
                            <button @click.stop="toggleFavorite(template)" 
                                    :class="template.is_favorite ? 'text-red-500' : 'text-gray-400'"
                                    class="hover:text-red-500 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2" x-text="template.description"></p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded" x-text="template.tone || 'Universal'"></span>
                                <span class="text-xs text-gray-500" x-text="template.usage_count + ' kali digunakan'"></span>
                            </div>
                            <button @click.stop="useTemplate(template)" 
                                    class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition">
                                Gunakan
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="filteredTemplates.length === 0" x-cloak class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada template ditemukan</h3>
                <p class="text-gray-500">Coba ubah filter atau kata kunci pencarian</p>
            </div>

            <!-- Template Preview Modal -->
            <div x-show="selectedTemplate" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="selectedTemplate?.title"></h3>
                        <button @click="selectedTemplate = null" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                            <span x-text="selectedTemplate?.category_label"></span>
                            <span>•</span>
                            <span x-text="selectedTemplate?.platform || 'Universal'"></span>
                            <span>•</span>
                            <span x-text="selectedTemplate?.tone || 'Universal'"></span>
                        </div>
                        <p class="text-gray-700 mb-4" x-text="selectedTemplate?.description"></p>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-2">Template Format:</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-800 whitespace-pre-wrap" x-text="selectedTemplate?.format"></pre>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button @click="toggleFavorite(selectedTemplate)" 
                                :class="selectedTemplate?.is_favorite ? 'text-red-500' : 'text-gray-500'"
                                class="flex items-center space-x-2 hover:text-red-500 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                            </svg>
                            <span x-text="selectedTemplate?.is_favorite ? 'Hapus dari Favorit' : 'Tambah ke Favorit'"></span>
                        </button>
                        <div class="space-x-3">
                            <button @click="selectedTemplate = null" 
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                Tutup
                            </button>
                            <button @click="useTemplate(selectedTemplate)" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Gunakan Template
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
