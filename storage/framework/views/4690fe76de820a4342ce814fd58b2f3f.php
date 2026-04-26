                <!-- AI VIDEO GENERATOR MODE -->
                <form @submit.prevent="generateVideoContent" x-show="generatorType === 'video'" x-cloak>
                    <div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl">🎬</span>
                            <h3 class="text-lg font-semibold text-gray-900">AI Video Content Generator</h3>
                        </div>
                        <p class="text-sm text-red-800">Generate script video, storyboard, hook viral, dan ide konten video untuk TikTok, Instagram Reels, dan YouTube Shorts!</p>
                    </div>

                    <!-- Video Content Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Konten Video *</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" x-model="videoForm.content_type" value="script" class="text-red-600">
                                <span class="text-sm">📝 Script Video</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" x-model="videoForm.content_type" value="storyboard" class="text-red-600">
                                <span class="text-sm">🎨 Storyboard</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" x-model="videoForm.content_type" value="hook" class="text-red-600">
                                <span class="text-sm">🎯 Hook Viral</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" x-model="videoForm.content_type" value="ideas" class="text-red-600">
                                <span class="text-sm">💡 Ide Konten</span>
                            </label>
                        </div>
                    </div>

                    <!-- Platform Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Platform Target *</label>
                        <select x-model="videoForm.platform" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Pilih Platform</option>
                            <option value="tiktok">TikTok (15-60 detik)</option>
                            <option value="instagram-reels">Instagram Reels (15-90 detik)</option>
                            <option value="youtube-shorts">YouTube Shorts (15-60 detik)</option>
                            <option value="instagram-story">Instagram Story (15 detik)</option>
                            <option value="facebook-reels">Facebook Reels (15-90 detik)</option>
                            <option value="all-platforms">Semua Platform</option>
                        </select>
                    </div>

                    <!-- Video Duration -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durasi Video</label>
                        <select x-model="videoForm.duration" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="15">15 detik (Ultra Short)</option>
                            <option value="30">30 detik (Short)</option>
                            <option value="60">60 detik (Medium)</option>
                            <option value="90">90 detik (Long)</option>
                        </select>
                    </div>

                    <!-- Product/Topic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Produk/Topik *</label>
                            <input type="text" 
                                   x-model="videoForm.product" 
                                   placeholder="Contoh: Skincare anti aging, Makanan sehat, Tips bisnis" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                            <input type="text" 
                                   x-model="videoForm.target_audience" 
                                   placeholder="Contoh: Wanita 25-35 tahun, Gen Z, Ibu rumah tangga" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Product Image Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Produk (Opsional)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-red-400 transition cursor-pointer" 
                             @click="$refs.videoImageInput.click()"
                             @dragover.prevent="$el.classList.add('border-red-400', 'bg-red-50')"
                             @dragleave.prevent="$el.classList.remove('border-red-400', 'bg-red-50')"
                             @drop.prevent="handleVideoImageDrop($event)">
                            
                            <input type="file" 
                                   x-ref="videoImageInput" 
                                   @change="handleVideoImageSelect($event)" 
                                   accept="image/*" 
                                   class="hidden">
                            
                            <div x-show="!videoForm.image_preview">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-600 mb-2">Upload foto produk untuk video yang lebih spesifik</p>
                                <p class="text-xs text-gray-500">JPG, PNG (Max 5MB) - Opsional tapi sangat direkomendasikan</p>
                            </div>

                            <div x-show="videoForm.image_preview" class="relative">
                                <img :src="videoForm.image_preview" alt="Product Preview" class="max-h-48 mx-auto rounded-lg">
                                <button type="button" 
                                        @click.stop="removeVideoImage()" 
                                        class="mt-3 text-sm text-red-600 hover:text-red-700">
                                    🗑️ Hapus Foto
                                </button>
                            </div>
                        </div>
                        
                        <!-- Image Benefits Info -->
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex gap-2">
                                <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-xs text-blue-800">
                                    <p class="font-medium mb-1">💡 Dengan foto produk, AI akan generate:</p>
                                    <div class="grid grid-cols-2 gap-1 text-xs text-blue-700">
                                        <div>• Scene-by-scene visual yang detail</div>
                                        <div>• Camera angle yang optimal</div>
                                        <div>• Props dan setting yang cocok</div>
                                        <div>• Lighting recommendations</div>
                                        <div>• Color scheme yang harmonis</div>
                                        <div>• Product showcase yang menarik</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Video Goal -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan Video *</label>
                        <select x-model="videoForm.goal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Pilih Tujuan</option>
                            <option value="viral">Viral & Awareness</option>
                            <option value="sales">Jualan & Conversion</option>
                            <option value="education">Edukasi & Tips</option>
                            <option value="entertainment">Hiburan & Engagement</option>
                            <option value="testimonial">Testimoni & Review</option>
                            <option value="behind-scenes">Behind The Scenes</option>
                            <option value="tutorial">Tutorial & How-to</option>
                        </select>
                    </div>

                    <!-- Video Style -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Style Video</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="trending" class="text-red-600">
                                <span class="text-sm">🔥 Trending</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="funny" class="text-red-600">
                                <span class="text-sm">😂 Funny</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="emotional" class="text-red-600">
                                <span class="text-sm">😢 Emotional</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="professional" class="text-red-600">
                                <span class="text-sm">👔 Professional</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="casual" class="text-red-600">
                                <span class="text-sm">😎 Casual</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="videoForm.styles" value="dramatic" class="text-red-600">
                                <span class="text-sm">🎭 Dramatic</span>
                            </label>
                        </div>
                    </div>

                    <!-- Additional Context -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konteks Tambahan (Opsional)</label>
                        <textarea x-model="videoForm.context" 
                                  rows="3" 
                                  placeholder="Contoh: Video ini untuk promosi flash sale, target engagement tinggi, harus ada call to action yang kuat..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
                    </div>

                    <!-- Quick Video Presets -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Preset Video Cepat</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <button type="button" 
                                    @click="setVideoPreset('viral-tiktok')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-red-400 hover:bg-red-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🔥</span>
                                    <span class="font-medium text-gray-900">Viral TikTok</span>
                                </div>
                                <p class="text-xs text-gray-600">Hook kuat, trending sound, call to action viral</p>
                            </button>
                            
                            <button type="button" 
                                    @click="setVideoPreset('product-demo')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-red-400 hover:bg-red-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🛍️</span>
                                    <span class="font-medium text-gray-900">Product Demo</span>
                                </div>
                                <p class="text-xs text-gray-600">Showcase produk, benefit, testimoni, closing</p>
                            </button>
                            
                            <button type="button" 
                                    @click="setVideoPreset('educational')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-red-400 hover:bg-red-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🎓</span>
                                    <span class="font-medium text-gray-900">Educational</span>
                                </div>
                                <p class="text-xs text-gray-600">Tips, tutorial, step-by-step, value content</p>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            :disabled="loading || !videoForm.content_type || !videoForm.platform || !videoForm.product || !videoForm.goal"
                            class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white py-4 rounded-lg hover:from-red-700 hover:to-pink-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed shadow-lg">
                        <span x-show="!loading" class="flex items-center justify-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-semibold">🎬 Generate Konten Video</span>
                        </span>
                        <span x-show="loading" class="flex items-center justify-center gap-3">
                            <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="font-semibold">Generating video content...</span>
                        </span>
                    </button>

                    <!-- Video Info -->
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-2">AI akan generate:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs text-blue-700">
                                    <div>• Script lengkap dengan timing</div>
                                    <div>• Hook pembuka yang viral</div>
                                    <div>• Storyboard visual per scene</div>
                                    <div>• Call to action yang kuat</div>
                                    <div>• Hashtag trending yang relevan</div>
                                    <div>• Tips shooting dan editing</div>
                                    <div>• Music/sound recommendations</div>
                                    <div>• Engagement optimization tips</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/partials/ai-generator/form-video.blade.php ENDPATH**/ ?>