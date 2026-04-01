    <!-- Header dengan ML Insights Button + Quota Badge -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-900">AI Copywriting Generator</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Generate copywriting berkualitas dengan AI</p>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            {{-- Quota Badge --}}
            <div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-gray-100 rounded-full text-xs sm:text-sm" id="quota-badge">
                <span class="text-xs text-gray-500 hidden sm:inline">Kuota:</span>
                <span class="font-bold" :class="quotaRemaining > 10 ? 'text-green-600' : quotaRemaining > 0 ? 'text-yellow-600' : 'text-red-600'"
                      x-text="quotaRemaining"></span>
                <span class="text-xs text-gray-400">/ {{ $quotaLimit ?? '∞' }}</span>
            </div>
            {{-- ML Insights Button --}}
            <button @click="toggleMLPreview()" 
                :disabled="mlLoading"
                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl transition flex items-center space-x-2 text-sm font-medium disabled:opacity-75">
            <!-- Loading spinner -->
            <svg x-show="mlLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <!-- Normal icon -->
            <svg x-show="!mlLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <span x-text="mlLoading ? 'Memuat...' : 'ML Insights'"></span>
        </button>
        </div> {{-- end flex gap-3 --}}
    </div>

    <!-- Load Brand Voice -->
    <div class="mb-6 bg-purple-50 border border-purple-200 rounded-lg p-4" x-data="{ showBrandVoices: false }">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">💼 Brand Voice</h3>
                <p class="text-xs text-gray-600 mt-1">Load preferensi brand kamu untuk generate lebih cepat</p>
            </div>
            <button @click="showBrandVoices = !showBrandVoices" 
                    class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition">
                <span x-show="!showBrandVoices">Load Brand Voice</span>
                <span x-show="showBrandVoices">Tutup</span>
            </button>
        </div>
        
        <div x-show="showBrandVoices" x-cloak class="mt-4" x-init="$watch('showBrandVoices', value => { if(value) loadBrandVoices() })">
            <div x-show="brandVoices.length === 0" class="text-center py-4 text-gray-500 text-sm">
                Belum ada brand voice tersimpan
            </div>
            <div x-show="brandVoices.length > 0" class="space-y-2">
                <template x-for="voice in brandVoices" :key="voice.id">
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-900" x-text="voice.name"></span>
                                <span x-show="voice.is_default" class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded">Default</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1" x-text="voice.brand_description || 'No description'"></p>
                        </div>
                        <button @click="loadBrandVoice(voice)" 
                                class="px-3 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700 transition">
                            Load
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Grouped Feature Navigation -->
    <div class="mb-6" x-data="{ openGroup: null }">
        {{-- Primary tabs — horizontal scroll di mobile --}}
        <div class="mb-3 -mx-6 px-6 overflow-x-auto scrollbar-hide">
            <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-gray-50 gap-1 min-w-max">
                <button @click="generatorType = 'text'; mode = 'simple'"
                        :class="generatorType === 'text' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600'"
                        class="px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition whitespace-nowrap">
                    📝 Text
                </button>
                <button @click="generatorType = 'image'"
                        :class="generatorType === 'image' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600'"
                        class="px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition whitespace-nowrap">
                    🖼️ Image
                </button>
                <button @click="generatorType = 'video'"
                        :class="generatorType === 'video' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600'"
                        class="px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition whitespace-nowrap">
                    🎬 Video
                </button>
                <button @click="generatorType = 'templates'"
                        :class="generatorType === 'templates' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600'"
                        class="px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition whitespace-nowrap">
                    📚 Template
                </button>
                <button @click="generatorType = 'history'"
                        :class="generatorType === 'history' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600'"
                        class="px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition whitespace-nowrap">
                    📜 History
                </button>
                <button @click="generatorType = 'stats'"
                        :class="generatorType === 'stats' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600'"
                        class="px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition whitespace-nowrap">
                    📊 Stats
                </button>
            </div>
        </div>

        {{-- Category dropdowns — wrap di mobile --}}
        <div class="flex justify-center">
            <div class="flex flex-wrap gap-1.5 sm:gap-2 justify-center">

                {{-- Optimasi & Analitik --}}
                <div class="relative" @click.away="openGroup = openGroup === 'optimasi' ? null : openGroup">
                    <button @click="openGroup = openGroup === 'optimasi' ? null : 'optimasi'"
                            class="flex items-center gap-1 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition border"
                            :class="['predictor','multiplatform','repurpose','bulk','image-analysis'].includes(generatorType) ? 'bg-blue-50 text-blue-600 border-blue-300' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'">
                        <span class="hidden sm:inline">⚡ Optimasi & Analitik</span>
                        <span class="sm:hidden">⚡ Optimasi</span>
                        <svg class="w-3 h-3 transition" :class="openGroup === 'optimasi' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openGroup === 'optimasi'" x-cloak x-transition
                         class="absolute z-20 mt-1 left-0 sm:left-0 right-auto w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 max-h-64 overflow-y-auto">
                        <button @click="generatorType = 'predictor'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'predictor' ? 'text-blue-600 bg-blue-50' : 'text-gray-700'">📈 Performance Predictor</button>
                        <button @click="generatorType = 'multiplatform'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'multiplatform' ? 'text-blue-600 bg-blue-50' : 'text-gray-700'">🎯 Multi-Platform Optimizer</button>
                        <button @click="generatorType = 'repurpose'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'repurpose' ? 'text-blue-600 bg-blue-50' : 'text-gray-700'">♻️ Content Repurposing</button>
                        <button @click="generatorType = 'bulk'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'bulk' ? 'text-blue-600 bg-blue-50' : 'text-gray-700'">📅 Bulk Content</button>
                        <button @click="generatorType = 'image-analysis'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'image-analysis' ? 'text-blue-600 bg-blue-50' : 'text-gray-700'">🔍 Image & Design Analysis</button>
                    </div>
                </div>

                {{-- Marketing & Iklan --}}
                <div class="relative" @click.away="openGroup = openGroup === 'marketing' ? null : openGroup">
                    <button @click="openGroup = openGroup === 'marketing' ? null : 'marketing'"
                            class="flex items-center gap-1 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition border"
                            :class="['google-ads','promo-link','product-explainer','discount-campaign','lead-magnet'].includes(generatorType) ? 'bg-green-50 text-green-600 border-green-300' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'">
                        <span class="hidden sm:inline">💰 Marketing & Iklan</span>
                        <span class="sm:hidden">💰 Marketing</span>
                        <svg class="w-3 h-3 transition" :class="openGroup === 'marketing' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openGroup === 'marketing'" x-cloak x-transition
                         class="absolute z-20 mt-1 left-0 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1">
                        <button @click="generatorType = 'google-ads'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'google-ads' ? 'text-green-600 bg-green-50' : 'text-gray-700'">🎯 Google Ads</button>
                        <button @click="generatorType = 'promo-link'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'promo-link' ? 'text-green-600 bg-green-50' : 'text-gray-700'">🔗 Magic Promo Link</button>
                        <button @click="generatorType = 'product-explainer'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'product-explainer' ? 'text-green-600 bg-green-50' : 'text-gray-700'">💬 Product Explainer</button>
                        <button @click="generatorType = 'discount-campaign'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'discount-campaign' ? 'text-green-600 bg-green-50' : 'text-gray-700'">🏷️ Discount Campaign</button>
                        <button @click="generatorType = 'lead-magnet'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'lead-magnet' ? 'text-green-600 bg-green-50' : 'text-gray-700'">🧲 Lead Magnet</button>
                    </div>
                </div>

                {{-- SEO & Riset --}}
                <div class="relative" @click.away="openGroup = openGroup === 'seo' ? null : openGroup">
                    <button @click="openGroup = openGroup === 'seo' ? null : 'seo'"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition border"
                            :class="['seo-metadata','smart-comparison','faq-generator','trends'].includes(generatorType) ? 'bg-purple-50 text-purple-600 border-purple-300' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'">
                        🔍 SEO & Riset
                        <svg class="w-3.5 h-3.5 transition" :class="openGroup === 'seo' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openGroup === 'seo'" x-cloak x-transition
                         class="absolute z-20 mt-1 left-0 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1">
                        <button @click="generatorType = 'seo-metadata'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'seo-metadata' ? 'text-purple-600 bg-purple-50' : 'text-gray-700'">🔍 SEO Metadata</button>
                        <button @click="generatorType = 'smart-comparison'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'smart-comparison' ? 'text-purple-600 bg-purple-50' : 'text-gray-700'">⚖️ Smart Comparison</button>
                        <button @click="generatorType = 'faq-generator'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'faq-generator' ? 'text-purple-600 bg-purple-50' : 'text-gray-700'">❓ FAQ Generator</button>
                        <button @click="generatorType = 'trends'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'trends' ? 'text-purple-600 bg-purple-50' : 'text-gray-700'">🔔 Trend Intelligence</button>
                    </div>
                </div>

                {{-- Kreativitas --}}
                <div class="relative" @click.away="openGroup = openGroup === 'kreativitas' ? null : openGroup">
                    <button @click="openGroup = openGroup === 'kreativitas' ? null : 'kreativitas'"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition border"
                            :class="['reels-hook','quality-badge'].includes(generatorType) ? 'bg-orange-50 text-orange-600 border-orange-300' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'">
                        🎨 Kreativitas
                        <svg class="w-3.5 h-3.5 transition" :class="openGroup === 'kreativitas' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openGroup === 'kreativitas'" x-cloak x-transition
                         class="absolute z-20 mt-1 left-0 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1">
                        <button @click="generatorType = 'reels-hook'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'reels-hook' ? 'text-orange-600 bg-orange-50' : 'text-gray-700'">🎬 Reels Hook</button>
                        <button @click="generatorType = 'quality-badge'; openGroup = null" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition" :class="generatorType === 'quality-badge' ? 'text-orange-600 bg-orange-50' : 'text-gray-700'">🏅 Quality Badge</button>
                    </div>
                </div>

                {{-- Analisis Dokumen → halaman terpisah --}}
                <a href="{{ route('ai.analysis') }}"
                   class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition border bg-white text-gray-600 border-gray-300 hover:bg-gray-50">
                    📊 Analisis Dokumen →
                </a>

            </div>
        </div>
    </div>
