        <div x-show="generatorType === 'trends'" x-cloak>

            {{-- Sub-tab selector --}}
            <div class="mb-5 flex justify-center">
                <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-gray-50 gap-1">
                    <button @click="trendSubTab = 'alert'"
                            :class="trendSubTab === 'alert' ? 'bg-white shadow-sm' : 'text-gray-600'"
                            class="px-5 py-2 rounded-md text-sm font-medium transition">
                        🔔 Trend & Konten
                    </button>
                    <button @click="trendSubTab = 'tags'"
                            :class="trendSubTab === 'tags' ? 'bg-white shadow-sm' : 'text-gray-600'"
                            class="px-5 py-2 rounded-md text-sm font-medium transition">
                        📈 Trend Tags Produk
                    </button>
                </div>
            </div>

            {{-- Sub-tab: Trend Alert --}}
            <div x-show="trendSubTab === 'alert'" x-cloak>
            <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">🔔 Trend Alert Indonesia</h3>
                <p class="text-gray-600">Stay updated dengan trending topics terbaru dan generate konten viral!</p>
                <div class="mt-3 p-3 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg border border-red-200">
                    <p class="text-sm text-red-800">💡 <strong>Pro Tip:</strong> Manfaatkan trending topics untuk boost engagement dan reach konten Anda!</p>
                </div>
            </div>

            <!-- Trend Categories -->
            <div class="mb-6">
                <div class="flex flex-wrap gap-2 mb-4">
                    <button @click="trendCategory = 'daily'" 
                            :class="trendCategory === 'daily' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        🔥 Daily Trends
                    </button>
                    <button @click="trendCategory = 'viral'" 
                            :class="trendCategory === 'viral' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        📈 Viral Content
                    </button>
                    <button @click="trendCategory = 'seasonal'" 
                            :class="trendCategory === 'seasonal' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        🗓️ Seasonal Events
                    </button>
                    <button @click="trendCategory = 'national'" 
                            :class="trendCategory === 'national' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        🇮🇩 National Days
                    </button>
                </div>
            </div>

            <!-- Daily Trends -->
            <div x-show="trendCategory === 'daily'" x-cloak>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900">🔥 Trending Topics Hari Ini</h4>
                    <button @click="refreshTrends()" 
                            :disabled="trendsLoading"
                            class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition disabled:opacity-50">
                        <span x-show="!trendsLoading">🔄 Refresh</span>
                        <span x-show="trendsLoading">Loading...</span>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <template x-for="trend in dailyTrends" :key="trend.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                             @click="selectTrend(trend)">
                            <div class="flex items-start justify-between mb-2">
                                <h5 class="font-medium text-gray-900" x-text="trend.title"></h5>
                                <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded-full" x-text="trend.category"></span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2" x-text="trend.description"></p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span x-text="'🔥 ' + trend.popularity + ' mentions'"></span>
                                <span x-text="trend.timeAgo"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Viral Content Ideas -->
            <div x-show="trendCategory === 'viral'" x-cloak>
                <h4 class="text-lg font-semibold text-gray-900 mb-4">📈 Viral Content Ideas</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <template x-for="idea in viralIdeas" :key="idea.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                             @click="selectTrend(idea)">
                            <div class="flex items-start justify-between mb-2">
                                <h5 class="font-medium text-gray-900" x-text="idea.title"></h5>
                                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded-full" x-text="idea.type"></span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2" x-text="idea.description"></p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span x-text="'📊 ' + idea.engagement + ' avg engagement'"></span>
                                <span x-text="idea.platform"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Seasonal Events -->
            <div x-show="trendCategory === 'seasonal'" x-cloak>
                <h4 class="text-lg font-semibold text-gray-900 mb-4">🗓️ Upcoming Seasonal Events</h4>
                
                <div class="space-y-4 mb-6">
                    <template x-for="event in seasonalEvents" :key="event.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                             @click="selectTrend(event)">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center">
                                    <span x-text="event.icon" class="text-2xl mr-3"></span>
                                    <div>
                                        <h5 class="font-medium text-gray-900" x-text="event.title"></h5>
                                        <p class="text-sm text-gray-600" x-text="event.date"></p>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full" x-text="event.daysLeft + ' days'"></span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2" x-text="event.description"></p>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="tag in event.contentIdeas" :key="tag">
                                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded" x-text="tag"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- National Days -->
            <div x-show="trendCategory === 'national'" x-cloak>
                <h4 class="text-lg font-semibold text-gray-900 mb-4">🇮🇩 National Days & Commemorations</h4>
                
                <div class="space-y-4 mb-6">
                    <template x-for="day in nationalDays" :key="day.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                             @click="selectTrend(day)">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center">
                                    <span x-text="day.icon" class="text-2xl mr-3"></span>
                                    <div>
                                        <h5 class="font-medium text-gray-900" x-text="day.title"></h5>
                                        <p class="text-sm text-gray-600" x-text="day.date"></p>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full" x-text="day.category"></span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2" x-text="day.description"></p>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="hashtag in day.hashtags" :key="hashtag">
                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded" x-text="hashtag"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Selected Trend & Content Generation -->
            <div x-show="selectedTrend" x-cloak class="mt-6 border-t pt-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">✨ Generate Content dari Trend</h4>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center mb-2">
                        <span x-text="selectedTrend?.icon || '🔥'" class="text-xl mr-2"></span>
                        <h5 class="font-medium text-gray-900" x-text="selectedTrend?.title"></h5>
                    </div>
                    <p class="text-sm text-gray-600" x-text="selectedTrend?.description"></p>
                </div>

                <!-- Content Type Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Konten yang Mau Dibuat:</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="caption" class="mr-2">
                            <span class="text-sm">📱 Caption IG/FB</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="story" class="mr-2">
                            <span class="text-sm">📸 IG Story</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="tiktok" class="mr-2">
                            <span class="text-sm">🎵 TikTok Script</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="thread" class="mr-2">
                            <span class="text-sm">🧵 Twitter Thread</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="blog" class="mr-2">
                            <span class="text-sm">📝 Blog Post</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="email" class="mr-2">
                            <span class="text-sm">📧 Email Marketing</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="ads" class="mr-2">
                            <span class="text-sm">💰 FB/IG Ads</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="trendContentTypes" value="whatsapp" class="mr-2">
                            <span class="text-sm">💬 WhatsApp Blast</span>
                        </label>
                    </div>
                </div>

                <!-- Business Context -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bisnis/Produk Anda:</label>
                    <input type="text" x-model="trendBusinessContext" 
                           placeholder="Contoh: Toko baju online, Warung makan, Jasa desain, dll"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Generate Button -->
                <button @click="generateTrendContent()" 
                        :disabled="!selectedTrend || trendContentTypes.length === 0 || !trendBusinessContext || trendLoading"
                        class="w-full bg-gradient-to-r from-red-600 to-orange-600 text-white py-3 px-6 rounded-lg hover:from-red-700 hover:to-orange-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <span x-show="!trendLoading" x-cloak class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        🔔 Generate Trend Content (<span x-text="trendContentTypes.length"></span> format)
                    </span>
                    <span x-show="trendLoading" x-cloak class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent mr-2"></div>
                        Generating trend content...
                    </span>
                </button>

                <!-- Results -->
                <div x-show="trendResults && trendResults.length > 0" x-cloak class="mt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">🎯 Konten Berdasarkan Trend</h4>
                    
                    <div class="space-y-6">
                        <template x-for="result in trendResults" :key="result.type">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <span x-text="result.icon" class="mr-2 text-lg"></span>
                                        <h5 class="font-medium text-gray-900" x-text="result.title"></h5>
                                        <span class="ml-2 text-xs px-2 py-1 bg-red-100 text-red-700 rounded-full">
                                            Trending
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500" x-text="result.content.length + ' chars'"></span>
                                        <button @click="copyTrendContent(result.content)" 
                                                class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                                            Copy
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                    <pre class="whitespace-pre-wrap text-sm text-gray-800" x-text="result.content"></pre>
                                </div>
                                
                                <!-- Hashtags -->
                                <div x-show="result.hashtags && result.hashtags.length > 0" class="flex flex-wrap gap-1">
                                    <template x-for="hashtag in result.hashtags" :key="hashtag">
                                        <span class="text-xs px-2 py-1 bg-red-100 text-red-600 rounded cursor-pointer"
                                              @click="copyTrendContent(hashtag)" x-text="hashtag"></span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button @click="copyAllTrendContent()" 
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm">
                            📋 Copy All Content
                        </button>
                        <button @click="exportTrendContent('txt')" 
                                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm">
                            📄 Export as TXT
                        </button>
                        <button @click="resetTrends()" 
                                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                            🔄 Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
            </div> {{-- end sub-tab alert --}}

            {{-- Sub-tab: Trend Tags --}}
            <div x-show="trendSubTab === 'tags'" x-cloak>
                @include('client.partials.trend-tags')
            </div>

        </div> {{-- end generatorType === 'trends' --}}