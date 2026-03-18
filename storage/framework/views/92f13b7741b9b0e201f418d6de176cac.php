<!-- Caption Optimizer Buttons - Add to Result Section -->
<div x-show="result && !loading" x-cloak class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        ⚡ Caption Optimizer Tools
    </h4>
    <p class="text-xs text-gray-600 mb-3">Tingkatkan kualitas caption dengan tools optimizer AI</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <!-- Grammar Checker Button -->
        <button @click="openGrammarChecker()" 
                class="flex items-center justify-center px-4 py-3 bg-white border-2 border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">📝 Grammar Checker</span>
        </button>
        
        <!-- Caption Shortener Button -->
        <button @click="openCaptionShortener()" 
                class="flex items-center justify-center px-4 py-3 bg-white border-2 border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 transition shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">✂️ Caption Shortener</span>
        </button>
        
        <!-- Caption Expander Button -->
        <button @click="openCaptionExpander()" 
                class="flex items-center justify-center px-4 py-3 bg-white border-2 border-green-300 text-green-700 rounded-lg hover:bg-green-50 transition shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">📈 Caption Expander</span>
        </button>
    </div>
</div>

<!-- Grammar Checker Modal -->
<div x-show="showGrammarChecker" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     @click.self="showGrammarChecker = false">
    <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">📝 Grammar Checker</h3>
                    <p class="text-sm text-gray-600">Periksa dan perbaiki grammar, spelling, dan struktur kalimat</p>
                </div>
            </div>
            <button @click="showGrammarChecker = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Loading State -->
            <div x-show="grammarLoading" class="text-center py-16">
                <div class="inline-block relative">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-purple-200 border-t-purple-600"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-purple-300 animate-ping opacity-20"></div>
                </div>
                <p class="text-gray-700 font-medium text-lg mt-6">🤖 Checking Grammar...</p>
                <p class="text-gray-500">Mohon tunggu, sedang menganalisis teks Anda</p>
            </div>

            <!-- Grammar Results -->
            <div x-show="!grammarLoading && grammarResult" class="space-y-6">
                <!-- Overall Score -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg mb-1">Overall Grammar Score</h4>
                            <p class="text-sm text-gray-600">Skor keseluruhan kualitas grammar</p>
                        </div>
                        <div class="text-center">
                            <div class="text-5xl font-bold text-purple-600" x-text="(grammarResult?.overall_score || 0) + '/10'"></div>
                            <p class="text-sm text-gray-600 mt-1">Score</p>
                        </div>
                    </div>
                </div>

                <!-- Errors Found -->
                <div x-show="grammarResult?.errors && grammarResult.errors.length > 0" class="bg-white rounded-xl p-6 border border-gray-200">
                    <h4 class="font-bold text-red-700 text-lg mb-4 flex items-center">
                        <span class="text-2xl mr-2">⚠️</span> Errors Found (<span x-text="grammarResult?.errors?.length || 0"></span>)
                    </h4>
                    <div class="space-y-3">
                        <template x-for="(error, index) in (grammarResult?.errors || [])" :key="index">
                            <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                                <div class="flex items-start justify-between mb-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded"
                                          :class="{
                                              'bg-red-100 text-red-700': error.severity === 'high',
                                              'bg-yellow-100 text-yellow-700': error.severity === 'medium',
                                              'bg-blue-100 text-blue-700': error.severity === 'low'
                                          }"
                                          x-text="error.type"></span>
                                    <span class="text-xs text-gray-500" x-text="'Position: ' + error.position"></span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-sm text-gray-700">Original: </span>
                                    <span class="text-sm font-medium text-red-600" x-text="error.original"></span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-sm text-gray-700">Suggestion: </span>
                                    <span class="text-sm font-medium text-green-600" x-text="error.suggestion"></span>
                                </div>
                                <p class="text-xs text-gray-600 mt-2" x-text="error.explanation"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Corrected Text -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                        <span class="text-2xl mr-2">✅</span> Corrected Text
                    </h4>
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap" x-text="grammarResult?.corrected_text"></p>
                    </div>
                    <button @click="useCorrectedText()" 
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                        ✨ Use Corrected Text
                    </button>
                </div>

                <!-- Quick Fix Button -->
                <div class="text-center">
                    <button @click="quickGrammarFix()" 
                            :disabled="grammarFixing"
                            class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition font-medium disabled:bg-gray-400">
                        <span x-show="!grammarFixing">⚡ Quick Fix All Errors</span>
                        <span x-show="grammarFixing">Fixing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Caption Shortener Modal -->
<div x-show="showCaptionShortener" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     @click.self="showCaptionShortener = false">
    <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-cyan-50">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">✂️ Caption Shortener</h3>
                    <p class="text-sm text-gray-600">Perpendek caption sambil mempertahankan pesan utama</p>
                </div>
            </div>
            <button @click="showCaptionShortener = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Settings -->
            <div x-show="!shortenerLoading && !shortenerResult" class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Length (characters)</label>
                    <input type="number" x-model="shortenerTargetLength" min="50" max="500"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Current: <span x-text="result?.length || 0"></span> characters</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" x-model="shortenerPreserveHashtags" class="mr-2 text-blue-600">
                        <span class="text-sm">Preserve Hashtags</span>
                    </label>
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" x-model="shortenerPreserveEmojis" class="mr-2 text-blue-600">
                        <span class="text-sm">Preserve Emojis</span>
                    </label>
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" x-model="shortenerPreserveCTA" class="mr-2 text-blue-600">
                        <span class="text-sm">Preserve CTA</span>
                    </label>
                </div>

                <button @click="shortenCaption()" 
                        class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    ✂️ Shorten Caption
                </button>
            </div>

            <!-- Loading State -->
            <div x-show="shortenerLoading" class="text-center py-16">
                <div class="inline-block relative">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-200 border-t-blue-600"></div>
                </div>
                <p class="text-gray-700 font-medium text-lg mt-6">✂️ Shortening Caption...</p>
            </div>

            <!-- Shortener Results -->
            <div x-show="!shortenerLoading && shortenerResult" class="space-y-6">
                <!-- Shortened Versions -->
                <div class="space-y-4">
                    <h4 class="font-bold text-gray-900 text-lg">📝 Shortened Versions</h4>
                    <template x-for="(version, index) in (shortenerResult?.shortened_versions || [])" :key="index">
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm font-medium text-blue-600" x-text="'Version ' + (index + 1)"></span>
                                <div class="text-right">
                                    <span class="text-xs text-gray-500" x-text="version.length + ' chars'"></span>
                                    <span class="ml-2 text-xs px-2 py-1 bg-white rounded" x-text="version.strategy"></span>
                                </div>
                            </div>
                            <p class="text-gray-700 mb-3 leading-relaxed whitespace-pre-wrap" x-text="version.version"></p>
                            <div class="flex items-center justify-between">
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="element in version.preserved_elements" :key="element">
                                        <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded" x-text="element"></span>
                                    </template>
                                </div>
                                <button @click="useShortened(version.version)" 
                                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                    ✨ Use This
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Back Button -->
                <button @click="shortenerResult = null" 
                        class="w-full px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    ← Back to Settings
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Caption Expander Modal -->
<div x-show="showCaptionExpander" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     @click.self="showCaptionExpander = false">
    <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">📈 Caption Expander</h3>
                    <p class="text-sm text-gray-600">Perluas caption dengan konten yang relevan dan menarik</p>
                </div>
            </div>
            <button @click="showCaptionExpander = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Settings -->
            <div x-show="!expanderLoading && !expanderResult" class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Length (characters)</label>
                    <input type="number" x-model="expanderTargetLength" min="100" max="2000"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">Current: <span x-text="result?.length || 0"></span> characters</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expansion Type</label>
                    <select x-model="expanderType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="detailed">Detailed - Add more details & specifications</option>
                        <option value="storytelling">Storytelling - Add personal story & emotion</option>
                        <option value="educational">Educational - Add tips & how-to</option>
                        <option value="promotional">Promotional - Add benefits & testimonials</option>
                        <option value="engaging">Engaging - Add questions & interactions</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" x-model="expanderAddHashtags" class="mr-2 text-green-600">
                        <span class="text-sm">Add Hashtags</span>
                    </label>
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" x-model="expanderAddEmojis" class="mr-2 text-green-600">
                        <span class="text-sm">Add Emojis</span>
                    </label>
                </div>

                <button @click="expandCaption()" 
                        class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    📈 Expand Caption
                </button>
            </div>

            <!-- Loading State -->
            <div x-show="expanderLoading" class="text-center py-16">
                <div class="inline-block relative">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-green-200 border-t-green-600"></div>
                </div>
                <p class="text-gray-700 font-medium text-lg mt-6">📈 Expanding Caption...</p>
            </div>

            <!-- Expander Results -->
            <div x-show="!expanderLoading && expanderResult" class="space-y-6">
                <!-- Expanded Versions -->
                <div class="space-y-4">
                    <h4 class="font-bold text-gray-900 text-lg">📝 Expanded Versions</h4>
                    <template x-for="(version, index) in (expanderResult?.expanded_versions || [])" :key="index">
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm font-medium text-green-600" x-text="'Version ' + (index + 1)"></span>
                                <div class="text-right">
                                    <span class="text-xs text-gray-500" x-text="version.length + ' chars'"></span>
                                    <span class="ml-2 text-xs px-2 py-1 bg-white rounded" x-text="version.expansion_method"></span>
                                </div>
                            </div>
                            <p class="text-gray-700 mb-3 leading-relaxed whitespace-pre-wrap" x-text="version.version"></p>
                            <div class="flex items-center justify-between">
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="element in version.added_elements" :key="element">
                                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded" x-text="element"></span>
                                    </template>
                                </div>
                                <button @click="useExpanded(version.version)" 
                                        class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                                    ✨ Use This
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Back Button -->
                <button @click="expanderResult = null" 
                        class="w-full px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    ← Back to Settings
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\partials\caption-optimizer.blade.php ENDPATH**/ ?>