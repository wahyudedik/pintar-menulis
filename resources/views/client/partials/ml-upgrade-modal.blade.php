    <!-- 🤖 Dynamic ML Suggestions Modal - Full Screen -->
    <div x-show="showMLPreview" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="showMLPreview = false"
         style="display: none;">
        
        <div class="bg-white rounded-lg w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-blue-50">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">🤖 AI-Powered ML Suggestions</h3>
                        <p class="text-sm text-gray-600">Data trending terbaru yang diperbarui setiap hari</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Freshness Indicator -->
                    <div class="text-right">
                        <div class="text-xs font-medium" :class="isDataFresh() ? 'text-green-600' : 'text-yellow-600'" x-text="getFreshnessIndicator()"></div>
                        <div class="text-xs text-gray-500" x-show="mlPreview?.generated_at">
                            <span x-text="new Date(mlPreview.generated_at).toLocaleDateString('id-ID')"></span>
                        </div>
                    </div>
                    <!-- Refresh Button -->
                    <button @click="refreshSuggestions()" 
                            :disabled="refreshing"
                            class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm disabled:bg-gray-400">
                        <svg x-show="!refreshing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <svg x-show="refreshing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                    <!-- Close Button -->
                    <button @click="showMLPreview = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto p-6">

                <!-- Loading Skeleton -->
                <div x-show="mlLoading" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <template x-for="i in 4">
                            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 space-y-3">
                                <div class="h-5 bg-gray-200 rounded animate-pulse w-1/3"></div>
                                <div class="h-4 bg-gray-100 rounded animate-pulse w-full"></div>
                                <div class="h-4 bg-gray-100 rounded animate-pulse w-4/5"></div>
                                <div class="h-4 bg-gray-100 rounded animate-pulse w-3/5"></div>
                            </div>
                        </template>
                    </div>
                    <div class="text-center text-sm text-gray-400 mt-4">
                        <svg class="w-5 h-5 animate-spin inline mr-2 text-purple-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses ML data...
                    </div>
                </div>

                <!-- Actual Content (hidden while loading) -->
                <div x-show="!mlLoading">
                <div x-show="mlPreview?.personalized" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg border border-green-200">
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl">🎯</span>
                        <div>
                            <h4 class="font-bold text-green-800">Personalized Suggestions</h4>
                            <p class="text-sm text-green-700">Suggestions ini dibuat khusus berdasarkan riwayat konten Anda</p>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Trending Hashtags -->
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                                <span class="text-2xl mr-2">🏷️</span> Trending Hashtags
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="hashtag in (mlPreview?.trending_hashtags || [])" :key="hashtag">
                                    <button @click="copyToClipboard(hashtag)" 
                                            class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-medium"
                                            x-text="hashtag"></button>
                                </template>
                            </div>
                            <p class="text-xs text-gray-500 mt-3">Klik untuk copy hashtag</p>
                        </div>

                        <!-- Best Hooks -->
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                                <span class="text-2xl mr-2">🎣</span> Best Hooks
                            </h4>
                            <div class="space-y-3">
                                <template x-for="(hook, index) in (mlPreview?.best_hooks || [])" :key="index">
                                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <p class="text-gray-700 mb-2" x-text="hook"></p>
                                        <button @click="copyToClipboard(hook)" 
                                                class="px-3 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700 transition">
                                            📋 Copy
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Current Trends -->
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                                <span class="text-2xl mr-2">📈</span> Current Trends
                            </h4>
                            <ul class="space-y-2">
                                <template x-for="trend in (mlPreview?.current_trends || [])" :key="trend">
                                    <li class="flex items-center text-gray-700">
                                        <span class="text-green-500 mr-2">•</span>
                                        <span x-text="trend"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Best CTAs -->
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                                <span class="text-2xl mr-2">🎯</span> Best CTAs
                            </h4>
                            <div class="space-y-3">
                                <template x-for="(cta, index) in (mlPreview?.best_ctas || [])" :key="index">
                                    <div class="p-3 bg-orange-50 rounded-lg border border-orange-200">
                                        <p class="text-gray-700 font-medium mb-2" x-text="cta"></p>
                                        <button @click="copyToClipboard(cta)" 
                                                class="px-3 py-1 bg-orange-600 text-white text-xs rounded hover:bg-orange-700 transition">
                                            📋 Copy
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Engagement Tips -->
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                                <span class="text-2xl mr-2">💡</span> Engagement Tips
                            </h4>
                            <ul class="space-y-2">
                                <template x-for="tip in (mlPreview?.engagement_tips || [])" :key="tip">
                                    <li class="flex items-start text-gray-700">
                                        <span class="text-yellow-500 mr-2 mt-1">💡</span>
                                        <span x-text="tip"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Optimal Posting Times -->
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                                <span class="text-2xl mr-2">⏰</span> Optimal Posting Times
                            </h4>
                            <div class="grid grid-cols-1 gap-2">
                                <template x-for="time in (mlPreview?.optimal_posting_times || [])" :key="time">
                                    <div class="p-2 bg-blue-50 rounded text-center text-blue-700 font-medium" x-text="time"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Ideas Section -->
                <div class="mt-6 bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                    <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                        <span class="text-2xl mr-2">💭</span> Content Ideas
                    </h4>
                    <div class="grid md:grid-cols-3 gap-4">
                        <template x-for="idea in (mlPreview?.content_ideas || [])" :key="idea">
                            <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                                <p class="text-gray-700 font-medium" x-text="idea"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Weekly Trends Section -->
                <div x-show="weeklyTrends?.data" class="mt-6 bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                    <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                        <span class="text-2xl mr-2">📊</span> Weekly Trend Analysis
                    </h4>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h5 class="font-semibold text-green-700 mb-2">📈 Rising Trends</h5>
                            <ul class="space-y-1">
                                <template x-for="trend in (weeklyTrends?.data?.rising_trends || [])" :key="trend">
                                    <li class="text-sm text-gray-700 flex items-center">
                                        <span class="text-green-500 mr-2">↗️</span>
                                        <span x-text="trend"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <div>
                            <h5 class="font-semibold text-red-700 mb-2">📉 Declining Trends</h5>
                            <ul class="space-y-1">
                                <template x-for="trend in (weeklyTrends?.data?.declining_trends || [])" :key="trend">
                                    <li class="text-sm text-gray-700 flex items-center">
                                        <span class="text-red-500 mr-2">↘️</span>
                                        <span x-text="trend"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-600">
                                <strong>Industry:</strong> <span x-text="mlPreview?.industry || 'General'"></span>
                            </span>
                            <span class="text-gray-600">
                                <strong>Platform:</strong> <span x-text="mlPreview?.platform || 'Instagram'"></span>
                            </span>
                        </div>
                        <div class="text-gray-500">
                            <span>🤖 Powered by AI • Updates daily at 6 AM</span>
                        </div>
                    </div>
                </div>
                </div>{{-- end x-show="!mlLoading" --}}
            </div>
        </div>
    </div>
