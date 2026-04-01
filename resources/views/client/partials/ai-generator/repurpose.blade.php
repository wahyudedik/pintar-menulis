        <div x-show="generatorType === 'repurpose'" x-cloak class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">♻️ Content Repurposing</h3>
                <p class="text-gray-600">Ubah 1 konten jadi 10+ variasi untuk berbagai platform dan format!</p>
                <div class="mt-3 p-3 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg border border-green-200">
                    <p class="text-sm text-green-800">
                        <strong>💡 Value:</strong> Hemat waktu 90%! ChatGPT gak bisa auto-suggest repurposing ideas seperti ini.
                    </p>
                </div>
            </div>

            <!-- Input Original Content -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Konten Asli</label>
                <textarea x-model="repurposeForm.originalContent" 
                          placeholder="Paste konten yang ingin di-repurpose di sini..."
                          rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                <p class="text-xs text-gray-500 mt-1">Bisa berupa caption, artikel, email, atau konten apapun</p>
            </div>

            <!-- Content Type Detection -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Konten Asli</label>
                    <select x-model="repurposeForm.originalType" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Auto-detect</option>
                        <option value="caption">Caption Social Media</option>
                        <option value="article">Artikel/Blog</option>
                        <option value="email">Email Marketing</option>
                        <option value="ad_copy">Iklan</option>
                        <option value="product_desc">Deskripsi Produk</option>
                        <option value="video_script">Script Video</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Industri/Niche</label>
                    <select x-model="repurposeForm.industry" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="general">General</option>
                        <option value="fashion">Fashion & Beauty</option>
                        <option value="food">Food & Beverage</option>
                        <option value="tech">Technology</option>
                        <option value="health">Health & Fitness</option>
                        <option value="education">Education</option>
                        <option value="business">Business & Finance</option>
                        <option value="travel">Travel & Lifestyle</option>
                        <option value="ecommerce">E-commerce</option>
                    </select>
                </div>
            </div>

            <!-- Repurposing Options -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Format Repurposing</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="option in repurposeOptions" :key="option.value">
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" 
                                   :value="option.value"
                                   x-model="repurposeForm.selectedFormats"
                                   class="mr-3 text-green-600 focus:ring-green-500">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <span x-text="option.icon" class="mr-2"></span>
                                    <span class="font-medium text-sm" x-text="option.label"></span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1" x-text="option.description"></p>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <!-- Advanced Options -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-900 mb-3">Opsi Lanjutan</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="repurposeForm.includeHashtags" 
                                   class="mr-2 text-green-600 focus:ring-green-500">
                            <span class="text-sm">Include hashtags yang relevan</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="repurposeForm.includeCTA" 
                                   class="mr-2 text-green-600 focus:ring-green-500">
                            <span class="text-sm">Tambahkan Call-to-Action</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="repurposeForm.optimizeLength" 
                                   class="mr-2 text-green-600 focus:ring-green-500">
                            <span class="text-sm">Optimize panjang per platform</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="repurposeForm.generateVariations" 
                                   class="mr-2 text-green-600 focus:ring-green-500">
                            <span class="text-sm">Generate 3 variasi per format</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Generate Button -->
            <button @click="generateRepurposedContent()" 
                    :disabled="!repurposeForm?.originalContent || (repurposeForm?.selectedFormats?.length || 0) === 0 || repurposeLoading"
                    class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white py-3 px-6 rounded-lg hover:from-green-700 hover:to-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                <span x-show="!repurposeLoading" x-cloak class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    ♻️ Repurpose Content (<span x-text="repurposeForm?.selectedFormats?.length || 0"></span> format)
                </span>
                <span x-show="repurposeLoading" x-cloak class="flex items-center justify-center">
                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent mr-2"></div>
                    Generating repurposed content...
                </span>
            </button>

            <!-- Results -->
            <div x-show="repurposeResults && repurposeResults.length > 0" x-cloak class="mt-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">📋 Hasil Repurposing</h4>
                
                <!-- Results Grid -->
                <div class="space-y-6">
                    <template x-for="result in repurposeResults" :key="result.format">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <span x-text="result.icon" class="mr-2 text-lg"></span>
                                    <h5 class="font-medium text-gray-900" x-text="result.title"></h5>
                                    <span class="ml-2 text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full" 
                                          x-text="result.platform"></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500" x-text="result.content.length + ' chars'"></span>
                                    <button @click="copyRepurposedContent(result.content, $event.target)" 
                                            class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">
                                        Copy
                                    </button>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                <pre class="whitespace-pre-wrap text-sm text-gray-800" x-text="result.content"></pre>
                            </div>
                            
                            <!-- Variations if enabled -->
                            <template x-if="result.variations && result.variations.length > 0">
                                <div class="mt-3">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Variasi:</p>
                                    <div class="space-y-2">
                                        <template x-for="(variation, index) in result.variations" :key="index">
                                            <div class="bg-white border border-gray-100 rounded p-2">
                                                <div class="flex justify-between items-start">
                                                    <pre class="whitespace-pre-wrap text-xs text-gray-700 flex-1" x-text="variation"></pre>
                                                    <button @click="copyRepurposedContent(variation, $event.target)" 
                                                            class="ml-2 text-xs bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600">
                                                        Copy
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Bulk Actions -->
                <div class="mt-6 flex flex-wrap gap-3">
                    <button @click="copyAllRepurposed()" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        📋 Copy All Content
                    </button>
                    <button @click="exportRepurposed('txt')" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm">
                        📄 Export as TXT
                    </button>
                    <button @click="exportRepurposed('csv')" 
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                        📊 Export as CSV
                    </button>
                    <button @click="resetRepurpose()" 
                            class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                        🔄 Reset
                    </button>
                </div>
            </div>
        </div>