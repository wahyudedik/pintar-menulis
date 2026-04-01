        <!-- 🎯 MULTI-PLATFORM OPTIMIZER -->
        <div x-show="generatorType === 'multiplatform'" x-cloak class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">🎯 Multi-Platform Optimizer</h3>
                <p class="text-gray-600">Generate 1 caption → Auto-optimize untuk 6+ platform sekaligus dengan format yang tepat!</p>
                <div class="mt-3 p-3 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-800"><strong>💡 Keunggulan:</strong> ChatGPT gak bisa auto-optimize per platform seperti ini!</p>
                </div>
            </div>

            <!-- Input Form -->
            <form @submit.prevent="generateMultiPlatform()" class="mb-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Konten yang mau dioptimasi <span class="text-red-600">*</span>
                    </label>
                    <textarea x-model="multiPlatformForm.content" required rows="4"
                              placeholder="Tulis konten dasar kamu di sini. AI akan otomatis optimize untuk setiap platform..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1">💡 Tulis konten dasar, AI akan sesuaikan panjang, tone, dan format untuk setiap platform</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Bisnis</label>
                        <select x-model="multiPlatformForm.business_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="fashion">Fashion & Pakaian</option>
                            <option value="food">Makanan & Minuman</option>
                            <option value="beauty">Beauty & Skincare</option>
                            <option value="tech">Technology</option>
                            <option value="education">Education</option>
                            <option value="service">Jasa & Layanan</option>
                            <option value="general">General/Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                        <select x-model="multiPlatformForm.target_audience" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="remaja">Remaja (15-25)</option>
                            <option value="dewasa_muda">Dewasa Muda (25-35)</option>
                            <option value="profesional">Profesional (30-45)</option>
                            <option value="keluarga">Keluarga (35-50)</option>
                            <option value="general">Semua Umur</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan</label>
                        <select x-model="multiPlatformForm.goal" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="awareness">Brand Awareness</option>
                            <option value="engagement">Engagement & Interaksi</option>
                            <option value="conversion">Sales & Conversion</option>
                            <option value="traffic">Drive Traffic</option>
                            <option value="viral">Viral & Share</option>
                        </select>
                    </div>
                </div>

                <!-- Platform Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Platform yang mau dioptimasi</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('instagram') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="instagram" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">📸</div>
                                <div class="text-xs font-medium">Instagram</div>
                                <div class="text-xs text-gray-500">2200 char</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('tiktok') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="tiktok" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">🎵</div>
                                <div class="text-xs font-medium">TikTok</div>
                                <div class="text-xs text-gray-500">150 char</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('facebook') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="facebook" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">👥</div>
                                <div class="text-xs font-medium">Facebook</div>
                                <div class="text-xs text-gray-500">Storytelling</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('twitter') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="twitter" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">🐦</div>
                                <div class="text-xs font-medium">Twitter/X</div>
                                <div class="text-xs text-gray-500">280 char</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('whatsapp') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="whatsapp" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">💬</div>
                                <div class="text-xs font-medium">WhatsApp</div>
                                <div class="text-xs text-gray-500">Short & Punchy</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                               :class="multiPlatformForm.platforms.includes('marketplace') ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                            <input type="checkbox" x-model="multiPlatformForm.platforms" value="marketplace" class="mr-2">
                            <div class="text-center flex-1">
                                <div class="text-lg">🛒</div>
                                <div class="text-xs font-medium">Marketplace</div>
                                <div class="text-xs text-gray-500">SEO Optimized</div>
                            </div>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">💡 Pilih minimal 2 platform untuk optimasi</p>
                </div>

                <!-- Generate Button -->
                <button type="submit" :disabled="multiPlatformLoading || multiPlatformForm.platforms.length < 2"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!multiPlatformLoading">🎯 Generate Multi-Platform Content</span>
                    <span x-show="multiPlatformLoading" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Optimizing for <span x-text="multiPlatformForm.platforms.length"></span> platforms...
                    </span>
                </button>
            </form>

            <!-- Results -->
            <div x-show="multiPlatformResults" x-cloak class="space-y-6">
                <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-4 border border-green-200">
                    <h4 class="font-semibold text-gray-900 mb-2">✅ Optimasi Selesai!</h4>
                    <p class="text-sm text-gray-700">Konten berhasil dioptimasi untuk <span class="font-semibold" x-text="Object.keys(multiPlatformResults || {}).length"></span> platform dengan format yang tepat.</p>
                </div>

                <!-- Platform Results -->
                <template x-for="(result, platform) in multiPlatformResults" :key="platform">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <!-- Platform Header -->
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="text-lg" x-text="getPlatformEmoji(platform)"></span>
                                    <div>
                                        <h5 class="font-semibold text-gray-900" x-text="getPlatformName(platform)"></h5>
                                        <p class="text-xs text-gray-600" x-text="getPlatformSpecs(platform)"></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full" x-text="result.char_count + ' chars'"></span>
                                    <button @click="copyPlatformContent(platform, result.content)" 
                                            class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition">
                                        📋 Copy
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Platform Content -->
                        <div class="p-4">
                            <div class="bg-gray-50 rounded-lg p-4 mb-3">
                                <pre class="whitespace-pre-wrap text-sm text-gray-800" x-text="result.content"></pre>
                            </div>
                            
                            <!-- Platform-specific features -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <div x-show="result.hashtags">
                                    <h6 class="font-medium text-gray-700 mb-1">🏷️ Hashtags:</h6>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="hashtag in result.hashtags" :key="hashtag">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded" x-text="hashtag"></span>
                                        </template>
                                    </div>
                                </div>
                                
                                <div x-show="result.optimization_notes">
                                    <h6 class="font-medium text-gray-700 mb-1">💡 Optimasi:</h6>
                                    <ul class="text-gray-600 space-y-1">
                                        <template x-for="note in result.optimization_notes" :key="note">
                                            <li class="flex items-start">
                                                <span class="text-green-500 mr-1">•</span>
                                                <span x-text="note"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Bulk Actions -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h5 class="font-medium text-gray-900 mb-3">🚀 Bulk Actions</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <button @click="copyAllPlatforms()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                            📋 Copy All Platforms
                        </button>
                        <button @click="exportMultiPlatform('txt')" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                            📄 Export as TXT
                        </button>
                        <button @click="exportMultiPlatform('csv')" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                            📊 Export as CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
