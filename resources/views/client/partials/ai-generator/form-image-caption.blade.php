                <form @submit.prevent="generateImageCaption" x-show="generatorType === 'image'" x-cloak enctype="multipart/form-data">
                    <div class="mb-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <p class="text-sm text-purple-800 font-medium">🖼️ Upload foto produk, AI generate caption otomatis!</p>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Produk *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition cursor-pointer" 
                             @click="$refs.imageInput.click()"
                             @dragover.prevent="$el.classList.add('border-blue-400', 'bg-blue-50')"
                             @dragleave.prevent="$el.classList.remove('border-blue-400', 'bg-blue-50')"
                             @drop.prevent="handleImageDrop($event)">
                            
                            <input type="file" 
                                   x-ref="imageInput" 
                                   @change="handleImageSelect($event)" 
                                   accept="image/*" 
                                   class="hidden">
                            
                            <div x-show="!imageForm.preview">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-600 mb-2">Klik atau drag & drop foto di sini</p>
                                <p class="text-xs text-gray-500">JPG, PNG (Max 5MB)</p>
                            </div>

                            <div x-show="imageForm.preview" class="relative">
                                <img :src="imageForm.preview" alt="Preview" class="max-h-64 mx-auto rounded-lg">
                                <button type="button" 
                                        @click.stop="removeImage()" 
                                        class="mt-3 text-sm text-red-600 hover:text-red-700">
                                    Ganti Foto
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Optional Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Bisnis (Opsional)</label>
                            <input type="text" 
                                   x-model="imageForm.business_type" 
                                   placeholder="Contoh: Kuliner, Fashion, Kosmetik" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk (Opsional)</label>
                            <input type="text" 
                                   x-model="imageForm.product_name" 
                                   placeholder="Contoh: Nasi Goreng Spesial" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">AI akan generate:</p>
                                <ul class="list-disc list-inside space-y-1 text-blue-700 text-xs">
                                    <li>Caption untuk single post</li>
                                    <li>Caption untuk carousel (3 slide)</li>
                                    <li>Deteksi objek dalam foto</li>
                                    <li>Tips editing & filter</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            :disabled="loading || !imageForm.file"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span x-show="!loading" class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Generate Caption dari Foto
                        </span>
                        <span x-show="loading" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Analyzing Image...
                        </span>
                    </button>
                </form>
