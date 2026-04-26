            <div x-show="generatorType === 'bulk'" x-cloak class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Bulk Content Generator</h3>
                <p class="text-gray-600 mb-4">Fitur ini tidak tersedia.</p>
            </div>

            <!-- CAPTION HISTORY MODE -->
            <div x-show="generatorType === 'history'" x-cloak class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Caption History</h3>
                <p class="text-gray-600 mb-4">Lihat semua caption yang pernah kamu generate</p>
                <a href="<?php echo e(route('caption-history.index')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                        Lihat Caption History
                    </a>
                </div>

            <!-- MY STATS MODE -->
            <div x-show="generatorType === 'stats'" x-cloak class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">My Stats & ML Insights</h3>
                <p class="text-gray-600 mb-4">Lihat statistik dan insights dari AI kamu</p>
                <a href="<?php echo e(route('my-stats')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Lihat My Stats
                </a>
            </div>

            <!-- 📈 CAPTION PERFORMANCE PREDICTOR -->
            <div x-show="generatorType === 'predictor'" x-cloak class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">📈 Caption Performance Predictor</h3>
                    <p class="text-gray-600">Prediksi performa caption sebelum posting! Dapatkan score, saran improvement, dan A/B testing variants.</p>
                </div>

                <!-- Input Form -->
                <form @submit.prevent="predictCaptionPerformance()" class="mb-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Caption yang mau dianalisis <span class="text-red-600">*</span>
                        </label>
                        <textarea x-model="predictorForm.caption" required rows="4"
                                  placeholder="Paste caption kamu di sini untuk dianalisis..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                            <select x-model="predictorForm.platform" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="instagram">Instagram</option>
                                <option value="facebook">Facebook</option>
                                <option value="tiktok">TikTok</option>
                                <option value="youtube">YouTube</option>
                                <option value="linkedin">LinkedIn</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                            <select x-model="predictorForm.industry" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="fashion">Fashion</option>
                                <option value="food">Food & Beverage</option>
                                <option value="beauty">Beauty & Skincare</option>
                                <option value="tech">Technology</option>
                                <option value="fitness">Fitness & Health</option>
                                <option value="education">Education</option>
                                <option value="general">General/Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                            <select x-model="predictorForm.target_audience" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="remaja">Remaja (15-25)</option>
                                <option value="dewasa_muda">Dewasa Muda (25-35)</option>
                                <option value="profesional">Profesional (30-45)</option>
                                <option value="keluarga">Keluarga (35-50)</option>
                                <option value="general">Semua Umur</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" :disabled="predictorLoading || !predictorForm.caption"
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!predictorLoading">🔮 Prediksi Performance</span>
                        <span x-show="predictorLoading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menganalisis...
                        </span>
                    </button>
                </form>

                <!-- Results -->
                <div x-show="predictionResults" x-cloak class="space-y-6">
                    <!-- Quality Score -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-6 border border-blue-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">💯 Quality Score</h4>
                            <div class="text-right">
                                <div class="text-3xl font-bold" :class="getScoreColor(predictionResults?.quality_score?.total_score)" x-text="predictionResults?.quality_score?.total_score || 0"></div>
                                <div class="text-sm text-gray-600">Grade: <span class="font-semibold" x-text="predictionResults?.quality_score?.grade || 'N/A'"></span></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-lg font-semibold text-blue-600" x-text="predictionResults?.quality_score?.breakdown?.structure || 0"></div>
                                <div class="text-xs text-gray-600">Structure</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-green-600" x-text="predictionResults?.quality_score?.breakdown?.engagement || 0"></div>
                                <div class="text-xs text-gray-600">Engagement</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-purple-600" x-text="predictionResults?.quality_score?.breakdown?.quality || 0"></div>
                                <div class="text-xs text-gray-600">Quality</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-orange-600" x-text="predictionResults?.quality_score?.breakdown?.performance || 0"></div>
                                <div class="text-xs text-gray-600">Performance</div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Prediction -->
                    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">📊 Prediksi Engagement</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600" x-text="(predictionResults?.prediction?.engagement_rate || 0) + '%'"></div>
                                <div class="text-sm text-gray-600">Total Engagement</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-500" x-text="(predictionResults?.prediction?.likes_rate || 0) + '%'"></div>
                                <div class="text-sm text-gray-600">Likes Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-500" x-text="(predictionResults?.prediction?.comments_rate || 0) + '%'"></div>
                                <div class="text-sm text-gray-600">Comments Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-500" x-text="(predictionResults?.prediction?.shares_rate || 0) + '%'"></div>
                                <div class="text-sm text-gray-600">Shares Rate</div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                  :class="getConfidenceColor(predictionResults?.prediction?.confidence)">
                                <span class="w-2 h-2 rounded-full mr-2" :class="getConfidenceDot(predictionResults?.prediction?.confidence)"></span>
                                Confidence: <span x-text="predictionResults?.prediction?.confidence || 'medium'"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Improvement Suggestions -->
                    <div x-show="predictionResults?.improvements?.length > 0" class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">💡 Saran Improvement</h4>
                        <div class="space-y-4">
                            <template x-for="improvement in predictionResults?.improvements || []" :key="improvement.type">
                                <div class="flex items-start space-x-3 p-4 bg-white rounded-lg border border-yellow-100">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              :class="getPriorityColor(improvement.priority)">
                                            <span x-text="improvement.priority"></span>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="font-semibold text-gray-900" x-text="improvement.title"></h5>
                                        <p class="text-sm text-gray-600 mt-1" x-text="improvement.description"></p>
                                        <div x-show="improvement.examples" class="mt-2">
                                            <p class="text-xs text-gray-500 mb-1">Contoh:</p>
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="example in improvement.examples || []" :key="example">
                                                    <span class="inline-block px-2 py-1 bg-gray-100 text-xs rounded" x-text="example"></span>
                                                </template>
                                            </div>
                                        </div>
                                        <div x-show="improvement.impact" class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                                📈 <span x-text="improvement.impact"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- A/B Testing Variant -->
                    <div x-show="predictionResults?.ab_variant" class="bg-purple-50 rounded-lg p-6 border border-purple-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">🧪 A/B Testing Variant</h4>
                            <button @click="generateMoreVariants()" 
                                    :disabled="variantsLoading"
                                    class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!variantsLoading">Generate More Variants</span>
                                <span x-show="variantsLoading" class="flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Generating...
                                </span>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Original -->
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h5 class="font-semibold text-gray-900 mb-2">📝 Original (A)</h5>
                                <div class="text-sm text-gray-700 bg-gray-50 p-3 rounded border" x-text="predictorForm.caption"></div>
                            </div>
                            
                            <!-- Variant -->
                            <div class="bg-white rounded-lg p-4 border border-purple-200">
                                <h5 class="font-semibold text-gray-900 mb-2">🔄 Variant (B)</h5>
                                <div class="text-sm text-gray-700 bg-purple-50 p-3 rounded border" x-text="predictionResults?.ab_variant?.variant_caption"></div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="bg-white p-3 rounded border">
                                <div class="font-medium text-gray-900">Test Focus</div>
                                <div class="text-gray-600" x-text="predictionResults?.ab_variant?.test_focus"></div>
                            </div>
                            <div class="bg-white p-3 rounded border">
                                <div class="font-medium text-gray-900">Duration</div>
                                <div class="text-gray-600" x-text="predictionResults?.ab_variant?.recommended_duration"></div>
                            </div>
                            <div class="bg-white p-3 rounded border">
                                <div class="font-medium text-gray-900">Sample Size</div>
                                <div class="text-gray-600" x-text="predictionResults?.ab_variant?.sample_size_needed"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Best Posting Time -->
                    <div class="bg-orange-50 rounded-lg p-6 border border-orange-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">⏰ Best Posting Time</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">🎯 Recommended Times Today</h5>
                                <div class="space-y-2">
                                    <template x-for="time in predictionResults?.best_posting_time?.best_times_today || []" :key="time">
                                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                                            <span x-text="time"></span>
                                            <span class="text-xs text-green-600 font-medium">Optimal</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">📅 Best Days</h5>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="day in predictionResults?.best_posting_time?.best_days || []" :key="day">
                                        <span class="px-3 py-1 bg-orange-100 text-orange-800 text-sm rounded-full" x-text="day"></span>
                                    </template>
                                </div>
                                <div class="mt-4">
                                    <h5 class="font-medium text-gray-900 mb-2">🚫 Avoid Times</h5>
                                    <div class="text-sm text-gray-600">
                                        <template x-for="time in predictionResults?.best_posting_time?.avoid_times || []" :key="time">
                                            <span class="inline-block mr-2" x-text="time"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/partials/ai-generator/placeholder-and-predictor.blade.php ENDPATH**/ ?>