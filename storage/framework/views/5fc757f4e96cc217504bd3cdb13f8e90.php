        <div x-show="generatorType === 'text' || generatorType === 'image' || generatorType === 'image-analysis' || generatorType === 'video'" x-cloak class="mt-6">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hasil Generate</h3>
                
                <div x-show="!result && !loading" class="text-center py-12 text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-sm">Hasil akan muncul di sini</p>
                </div>

                <div x-show="loading" x-cloak class="text-center py-12">
                    <svg class="animate-spin h-10 w-10 mx-auto text-blue-600 mb-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm text-gray-600">AI sedang bekerja...</p>
                </div>

                <div x-show="result && !loading" x-cloak>

                    
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-xs text-gray-500 font-medium">Preview:</span>
                        <button @click="previewPlatform = 'raw'" :class="previewPlatform === 'raw' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-full text-xs font-medium transition">📝 Teks</button>
                        <button @click="previewPlatform = 'instagram'" :class="previewPlatform === 'instagram' ? 'bg-pink-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-full text-xs font-medium transition">📱 Instagram</button>
                        <button @click="previewPlatform = 'whatsapp'" :class="previewPlatform === 'whatsapp' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-full text-xs font-medium transition">💬 WhatsApp</button>
                        <button @click="previewPlatform = 'tiktok'" :class="previewPlatform === 'tiktok' ? 'bg-black text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-full text-xs font-medium transition">🎵 TikTok</button>
                    </div>

                    
                    <div class="space-y-4 mb-4">
                        <template x-for="(caption, idx) in parsedCaptions" :key="idx">
                            <div class="border border-gray-200 rounded-xl overflow-hidden">
                                
                                <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                                    <span class="text-xs font-semibold text-gray-500">Variasi <span x-text="idx + 1"></span></span>
                                    <div class="flex items-center gap-1.5">
                                        <button @click="copySingleCaption(idx)"
                                                class="px-2.5 py-1 rounded-md text-xs font-medium transition"
                                                :class="copiedIdx === idx ? 'bg-green-100 text-green-700' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-100'">
                                            <span x-text="copiedIdx === idx ? '✓ Tersalin' : '📋 Copy'"></span>
                                        </button>
                                        <button @click="downloadCaptionImage(idx)"
                                                class="px-2.5 py-1 rounded-md text-xs font-medium bg-white border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                                            📥 Download
                                        </button>
                                        <button @click="shareToWhatsApp(idx)"
                                                class="px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 border border-green-300 text-green-700 hover:bg-green-100 transition">
                                            💬 WA
                                        </button>
                                    </div>
                                </div>

                                
                                <div x-show="previewPlatform === 'raw'" class="p-4">
                                    <pre class="whitespace-pre-wrap text-sm text-gray-800 leading-relaxed" x-text="caption"></pre>
                                </div>

                                
                                <div x-show="previewPlatform === 'instagram'" class="p-4 flex justify-center">
                                    <div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                        <div class="flex items-center gap-2 px-3 py-2 border-b border-gray-100">
                                            <div class="w-8 h-8 bg-gradient-to-br from-pink-500 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold" x-text="'<?php echo e(substr(auth()->user()->name, 0, 1)); ?>'"></div>
                                            <span class="text-xs font-semibold text-gray-900"><?php echo e(auth()->user()->business_name ?? auth()->user()->name); ?></span>
                                        </div>
                                        <div class="w-full aspect-square bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                                            <span class="text-4xl">📸</span>
                                        </div>
                                        <div class="px-3 py-2">
                                            <div class="flex gap-3 mb-2 text-gray-800">
                                                <span>❤️</span><span>💬</span><span>📤</span>
                                            </div>
                                            <p class="text-xs text-gray-800 leading-relaxed line-clamp-4" x-text="caption"></p>
                                            <p class="text-xs text-gray-400 mt-1">... selengkapnya</p>
                                        </div>
                                    </div>
                                </div>

                                
                                <div x-show="previewPlatform === 'whatsapp'" class="p-4 flex justify-center">
                                    <div class="w-full max-w-sm bg-[#e5ddd5] rounded-lg p-3">
                                        <div class="bg-[#dcf8c6] rounded-lg p-3 ml-8 shadow-sm relative">
                                            <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap" x-text="caption.length > 300 ? caption.substring(0, 300) + '...' : caption"></p>
                                            <div class="flex items-center justify-end gap-1 mt-1">
                                                <span class="text-xs text-gray-500"><?php echo e(now()->format('H:i')); ?></span>
                                                <span class="text-blue-500 text-xs">✓✓</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div x-show="previewPlatform === 'tiktok'" class="p-4 flex justify-center">
                                    <div class="w-full max-w-xs bg-black rounded-2xl overflow-hidden aspect-[9/16] max-h-80 relative flex flex-col justify-end p-4">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                        <div class="relative z-10">
                                            <p class="text-xs font-semibold text-white mb-1">{{ strtolower(str_replace(' ', '', auth()->user()->business_name ?? auth()->user()->name)) }}</p>
                                            <p class="text-xs text-white/90 leading-relaxed line-clamp-3" x-text="caption"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button @click="copyToClipboard"
                                class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition text-sm">
                            <span x-show="!copied">📋 Copy Semua</span>
                            <span x-show="copied">✓ Tersalin!</span>
                        </button>
                        <button @click="sendResultToWhatsApp()"
                                class="bg-[#25D366] text-white py-2 px-4 rounded-lg hover:bg-[#1da851] transition text-sm flex items-center gap-1.5">
                            💬 Kirim ke WA
                        </button>
                        <button @click="analyzeCaption()" :disabled="analysisLoading"
                                class="bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition text-sm disabled:bg-gray-400 flex items-center gap-1.5">
                            <div x-show="analysisLoading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                            <span x-text="analysisLoading ? 'Analyzing...' : '🔍 Analyze'"></span>
                        </button>
                        <button @click="saveForAnalytics" :disabled="saved"
                                class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition text-sm disabled:bg-gray-400">
                            <span x-show="!saved">💾 Save</span>
                            <span x-show="saved">✓ Saved!</span>
                        </button>
                        <button @click="showSaveBrandVoiceModal = true"
                                class="bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition text-sm">
                            💼 Brand Voice
                        </button>
                        <button @click="shareToExplore()"
                                class="bg-yellow-500 text-white py-2 px-4 rounded-lg hover:bg-yellow-600 transition text-sm"
                                :disabled="!lastCaptionId"
                                x-show="lastCaptionId">
                            🌟 Share ke Explore
                        </button>
                    </div>

                    
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200" x-show="!rated">
                        <p class="text-sm font-medium text-gray-900 mb-2">⭐ Bagaimana hasilnya?</p>
                        <p class="text-xs text-gray-600 mb-3">Rating kamu membantu AI belajar dan improve!</p>
                        <div class="flex items-center justify-center space-x-2 mb-3">
                            <template x-for="star in 5" :key="star">
                                <button @click="selectedRating = star"
                                        class="text-3xl transition-transform hover:scale-110"
                                        :style="star <= selectedRating ? 'filter: grayscale(0%);' : 'filter: grayscale(100%) brightness(1.5);'">⭐</button>
                            </template>
                        </div>
                        <div x-show="selectedRating > 0" x-cloak>
                            <textarea x-model="ratingFeedback" placeholder="Feedback (opsional)" rows="2"
                                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 mb-2"></textarea>
                            <button @click="submitRating" :disabled="submittingRating"
                                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-sm disabled:bg-gray-400">
                                <span x-text="submittingRating ? 'Submitting...' : 'Submit Rating'"></span>
                            </button>
                        </div>
                    </div>
                    <div x-show="rated" x-cloak class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-sm text-green-800 text-center">✓ Terima kasih atas rating kamu! 🙏</p>
                    </div>
                    
                    <!-- 🔄 Reset Button -->
                    <button @click="reset"
                            class="w-full mt-3 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                        🔄 Generate Lagi
                    </button>
                </div>
            </div>
        </div>
        
        
        <!-- Save Brand Voice Modal -->
        <div x-show="showSaveBrandVoiceModal" x-cloak 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @click.self="showSaveBrandVoiceModal = false">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">💼 Save Brand Voice</h3>
                <p class="text-sm text-gray-600 mb-4">Simpan preferensi ini untuk generate lebih cepat di lain waktu</p>
                
                <form @submit.prevent="saveBrandVoice">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Brand Voice <span class="text-red-600">*</span></label>
                        <input type="text" x-model="brandVoiceForm.name" required
                               placeholder="Contoh: Toko Baju Anak Saya"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                        <textarea x-model="brandVoiceForm.brand_description" rows="2"
                                  placeholder="Contoh: Brand baju anak umur 2-5 tahun, target ibu muda"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" x-model="brandVoiceForm.is_default" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700">Set sebagai default (auto-load)</span>
                        </label>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" @click="showSaveBrandVoiceModal = false"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="savingBrandVoice"
                                class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:bg-gray-400">
                            <span x-show="!savingBrandVoice">Simpan</span>
                            <span x-show="savingBrandVoice">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/partials/ai-generator/result-section.blade.php ENDPATH**/ ?>