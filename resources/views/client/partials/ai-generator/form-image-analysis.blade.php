                <form @submit.prevent="analyzeImageWithAI" x-show="generatorType === 'image-analysis'" x-cloak enctype="multipart/form-data">
                    <div class="mb-6 p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl">🔍</span>
                            <h3 class="text-lg font-semibold text-gray-900">AI Image & Design Analysis</h3>
                        </div>
                        <p class="text-sm text-purple-800">Upload foto produk atau desain grafis — AI akan menganalisis objek, warna, komposisi, mood, tipografi, layout, konsistensi brand, dan efektivitas CTA!</p>
                    </div>

                    <!-- Image Upload for Analysis -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar untuk Analisis *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-purple-400 transition cursor-pointer" 
                             @click="$refs.analysisImageInput.click()"
                             @dragover.prevent="$el.classList.add('border-purple-400', 'bg-purple-50')"
                             @dragleave.prevent="$el.classList.remove('border-purple-400', 'bg-purple-50')"
                             @drop.prevent="handleAnalysisImageDrop($event)">
                            
                            <input type="file" 
                                   x-ref="analysisImageInput" 
                                   @change="handleAnalysisImageSelect($event)" 
                                   accept="image/*" 
                                   class="hidden">
                            
                            <div x-show="!analysisForm.preview">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-600 mb-2 font-medium">Klik atau drag & drop gambar di sini</p>
                                <p class="text-sm text-gray-500">JPG, PNG, WebP (Max 10MB)</p>
                                <p class="text-xs text-gray-400 mt-2">AI akan menganalisis gambar secara detail</p>
                            </div>

                            <div x-show="analysisForm.preview" class="relative">
                                <img :src="analysisForm.preview" alt="Preview" class="max-h-80 mx-auto rounded-lg shadow-lg">
                                <button type="button" 
                                        @click.stop="removeAnalysisImage()" 
                                        class="mt-4 px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition">
                                    🗑️ Ganti Gambar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Analysis Options -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Jenis Analisis</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="objects" class="text-purple-600">
                                <span class="text-sm">🎯 Deteksi Objek</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="colors" class="text-purple-600">
                                <span class="text-sm">🎨 Analisis Warna</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="composition" class="text-purple-600">
                                <span class="text-sm">📐 Komposisi</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="mood" class="text-purple-600">
                                <span class="text-sm">😊 Mood & Emosi</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="text" class="text-purple-600">
                                <span class="text-sm">📝 Baca Teks</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="marketing" class="text-purple-600">
                                <span class="text-sm">📈 Tips Marketing</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="quality" class="text-purple-600">
                                <span class="text-sm">⭐ Kualitas Foto</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="suggestions" class="text-purple-600">
                                <span class="text-sm">💡 Saran Perbaikan</span>
                            </label>
                            {{-- Opsi Analisis Desain --}}
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="typography" class="text-purple-600">
                                <span class="text-sm">🔤 Tipografi</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="layout" class="text-purple-600">
                                <span class="text-sm">📏 Layout & Hierarki</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="branding" class="text-purple-600">
                                <span class="text-sm">🏷️ Konsistensi Brand</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" x-model="analysisForm.options" value="cta_design" class="text-purple-600">
                                <span class="text-sm">⚡ Efektivitas CTA</span>
                            </label>
                        </div>
                    </div>

                    <!-- Context Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konteks Bisnis (Opsional)</label>
                        <textarea x-model="analysisForm.context" 
                                  rows="3" 
                                  placeholder="Contoh: Ini foto produk makanan untuk Instagram. Saya ingin tahu apakah foto ini menarik untuk customer dan bagaimana cara memperbaikinya..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"></textarea>
                    </div>

                    <!-- Quick Analysis Presets -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Preset Analisis Cepat</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <button type="button" 
                                    @click="setAnalysisPreset('product')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🛍️</span>
                                    <span class="font-medium text-gray-900">Analisis Produk</span>
                                </div>
                                <p class="text-xs text-gray-600">Kualitas produk, daya tarik visual, dan tips marketing</p>
                            </button>
                            
                            <button type="button" 
                                    @click="setAnalysisPreset('social')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">📱</span>
                                    <span class="font-medium text-gray-900">Analisis Social Media</span>
                                </div>
                                <p class="text-xs text-gray-600">Komposisi, mood, engagement potential untuk Instagram/TikTok</p>
                            </button>

                            <button type="button" 
                                    @click="setAnalysisPreset('design')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-pink-400 hover:bg-pink-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🎨</span>
                                    <span class="font-medium text-gray-900">Analisis Desain</span>
                                </div>
                                <p class="text-xs text-gray-600">Tipografi, layout, konsistensi brand, dan efektivitas CTA</p>
                            </button>
                            
                            <button type="button" 
                                    @click="setAnalysisPreset('complete')"
                                    class="p-4 border border-gray-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 transition text-left">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🔍</span>
                                    <span class="font-medium text-gray-900">Analisis Lengkap</span>
                                </div>
                                <p class="text-xs text-gray-600">Semua aspek: foto, desain, warna, komposisi, marketing</p>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            :disabled="loading || !analysisForm.file || analysisForm.options.length === 0"
                            class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-4 rounded-lg hover:from-purple-700 hover:to-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed shadow-lg">
                        <span x-show="!loading" class="flex items-center justify-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold">🔍 Analisis dengan AI Vision</span>
                        </span>
                        <span x-show="loading" class="flex items-center justify-center gap-3">
                            <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="font-semibold">Menganalisis gambar...</span>
                        </span>
                    </button>

                    <!-- Analysis Info -->
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-2">AI akan menganalisis:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs text-blue-700">
                                    <div>• Objek dan elemen dalam gambar</div>
                                    <div>• Palet warna dan harmoni</div>
                                    <div>• Komposisi dan rule of thirds</div>
                                    <div>• Mood dan emosi yang terpancar</div>
                                    <div>• Teks yang terbaca (OCR)</div>
                                    <div>• Potensi viral dan engagement</div>
                                    <div>• Kualitas teknis foto</div>
                                    <div>• Saran perbaikan konkret</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>