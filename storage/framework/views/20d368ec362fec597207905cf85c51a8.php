<!-- Caption Analysis Modal - Full Screen -->
<div x-show="showAnalysis" 
     x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     @click.self="showAnalysis = false"
     style="display: none;">
    
    <div class="bg-white rounded-lg w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">🔍 Caption Analysis</h3>
                    <p class="text-sm text-gray-600">Insights mendalam untuk caption Anda</p>
                </div>
            </div>
            <button @click="showAnalysis = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-6">

            <!-- Loading State - Enhanced -->
            <div x-show="analysisLoading" class="text-center py-16">
                <div class="inline-block relative">
                    <!-- Animated spinner -->
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-200 border-t-blue-600"></div>
                    <!-- Pulse effect -->
                    <div class="absolute inset-0 rounded-full border-4 border-blue-300 animate-ping opacity-20"></div>
                </div>
                <div class="mt-6 space-y-3">
                    <p class="text-gray-700 font-medium text-lg">🤖 Analyzing Caption...</p>
                    <p class="text-gray-500">Mohon tunggu, sedang menganalisis kualitas caption Anda</p>
                    <!-- Progress dots -->
                    <div class="flex justify-center space-x-2 mt-4">
                        <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>

            <!-- Analysis Tabs -->
            <div x-show="!analysisLoading && analysisResult" class="space-y-6">
                <!-- Tab Navigation - Full Width -->
                <div class="flex flex-wrap gap-2 border-b border-gray-200">
                    <button @click="analysisTab = 'quality'" 
                            :class="analysisTab === 'quality' ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800'"
                            class="px-6 py-3 text-base font-medium transition rounded-t-lg">
                        📊 Quality Score
                    </button>
                    <button @click="analysisTab = 'sentiment'" 
                            :class="analysisTab === 'sentiment' ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800'"
                            class="px-6 py-3 text-base font-medium transition rounded-t-lg">
                        😊 Sentiment
                    </button>
                    <button @click="analysisTab = 'recommendations'" 
                            :class="analysisTab === 'recommendations' ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800'"
                            class="px-6 py-3 text-base font-medium transition rounded-t-lg">
                        💡 Tips & Recommendations
                    </button>
                </div>

                <!-- Quality Score Tab - Full Screen Layout -->
                <div x-show="analysisTab === 'quality' && analysisResult?.quality" class="space-y-6">
                    <!-- Score Grid - Larger Layout -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <!-- Overall Score -->
                        <div class="bg-white rounded-xl p-6 text-center shadow-lg border border-gray-100">
                            <div class="text-3xl font-bold text-blue-600 mb-2" x-text="(analysisResult?.quality?.overall_score || 0).toFixed(1)"></div>
                            <p class="text-sm text-gray-600 font-medium">Overall Score</p>
                        </div>
                        <!-- Engagement -->
                        <div class="bg-white rounded-xl p-6 text-center shadow-lg border border-gray-100">
                            <div class="text-3xl font-bold text-green-600 mb-2" x-text="(analysisResult?.quality?.engagement_score || 0).toFixed(1)"></div>
                            <p class="text-sm text-gray-600 font-medium">Engagement</p>
                        </div>
                        <!-- Clarity -->
                        <div class="bg-white rounded-xl p-6 text-center shadow-lg border border-gray-100">
                            <div class="text-3xl font-bold text-yellow-600 mb-2" x-text="(analysisResult?.quality?.clarity_score || 0).toFixed(1)"></div>
                            <p class="text-sm text-gray-600 font-medium">Clarity</p>
                        </div>
                        <!-- CTA -->
                        <div class="bg-white rounded-xl p-6 text-center shadow-lg border border-gray-100">
                            <div class="text-3xl font-bold text-purple-600 mb-2" x-text="(analysisResult?.quality?.call_to_action_score || 0).toFixed(1)"></div>
                            <p class="text-sm text-gray-600 font-medium">Call to Action</p>
                        </div>
                        <!-- Emoji -->
                        <div class="bg-white rounded-xl p-6 text-center shadow-lg border border-gray-100">
                            <div class="text-3xl font-bold text-pink-600 mb-2" x-text="(analysisResult?.quality?.emoji_usage_score || 0).toFixed(1)"></div>
                            <p class="text-sm text-gray-600 font-medium">Emoji Usage</p>
                        </div>
                    </div>

                    <!-- Strengths & Weaknesses - Side by Side -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <h4 class="font-bold text-green-700 text-lg mb-4 flex items-center">
                                <span class="text-2xl mr-2">✅</span> Strengths
                            </h4>
                            <ul class="space-y-2">
                                <template x-for="strength in (analysisResult?.quality?.strengths || [])" :key="strength">
                                    <li class="text-gray-700 flex items-start">
                                        <span class="text-green-500 mr-2 mt-1">•</span>
                                        <span x-text="strength"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <h4 class="font-bold text-red-700 text-lg mb-4 flex items-center">
                                <span class="text-2xl mr-2">⚠️</span> Areas for Improvement
                            </h4>
                            <ul class="space-y-2">
                                <template x-for="weakness in (analysisResult?.quality?.weaknesses || [])" :key="weakness">
                                    <li class="text-gray-700 flex items-start">
                                        <span class="text-red-500 mr-2 mt-1">•</span>
                                        <span x-text="weakness"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <!-- Improved Caption -->
                    <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                        <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                            <span class="text-2xl mr-2">💡</span> Improved Version
                        </h4>
                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                            <p class="text-gray-700 leading-relaxed" x-text="analysisResult?.quality?.improved_caption || 'No suggestion available'"></p>
                        </div>
                        <button @click="useImprovedCaption()" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            ✨ Use This Caption
                        </button>
                    </div>
                </div>

                <!-- Sentiment Tab - Full Screen Layout -->
                <div x-show="analysisTab === 'sentiment' && analysisResult?.sentiment" class="space-y-6">
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="bg-white rounded-xl p-8 text-center shadow-lg border border-gray-100">
                            <div class="text-4xl font-bold mb-3" 
                                 :class="(analysisResult?.sentiment?.sentiment || 'neutral') === 'positive' ? 'text-green-600' : (analysisResult?.sentiment?.sentiment || 'neutral') === 'negative' ? 'text-red-600' : 'text-yellow-600'"
                                 x-text="(analysisResult?.sentiment?.sentiment || 'neutral').toUpperCase()"></div>
                            <p class="text-gray-600 font-medium">Overall Sentiment</p>
                        </div>
                        <div class="bg-white rounded-xl p-8 text-center shadow-lg border border-gray-100">
                            <div class="text-4xl font-bold text-blue-600 mb-3" x-text="((analysisResult?.sentiment?.score || 0) * 100).toFixed(0) + '%'"></div>
                            <p class="text-gray-600 font-medium">Confidence Level</p>
                        </div>
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <p class="text-gray-600 font-medium mb-3">Key Sentiment Keywords</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="keyword in (analysisResult?.sentiment?.keywords || [])" :key="keyword">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full" x-text="keyword"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                        <h4 class="font-bold text-gray-900 text-lg mb-3">📝 Analysis Explanation</h4>
                        <p class="text-gray-700 leading-relaxed" x-text="analysisResult?.sentiment?.explanation || 'No explanation available'"></p>
                    </div>
                </div>

                <!-- Recommendations Tab - Full Screen Layout -->
                <div x-show="analysisTab === 'recommendations' && analysisResult?.recommendations" class="space-y-6">
                    <!-- Alternative Versions -->
                    <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                        <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                            <span class="text-2xl mr-2">📝</span> Alternative Caption Versions
                        </h4>
                        <div class="grid gap-4">
                            <template x-for="(alt, index) in (analysisResult?.recommendations?.alternative_versions || [])" :key="index">
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-sm font-medium text-blue-600" x-text="'Version ' + (index + 1)"></span>
                                        <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded" x-text="alt.focus"></span>
                                    </div>
                                    <p class="text-gray-700 mb-3 leading-relaxed" x-text="alt.version"></p>
                                    <button @click="copyToClipboard(alt.version)" 
                                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                        📋 Copy This Version
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Hashtag & Emoji Suggestions -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                                <span class="text-2xl mr-2">🏷️</span> Hashtag Suggestions
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="hashtag in (analysisResult?.recommendations?.hashtag_suggestions || [])" :key="hashtag">
                                    <button @click="addHashtag(hashtag)" 
                                            class="px-3 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition"
                                            x-text="hashtag"></button>
                                </template>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                                <span class="text-2xl mr-2">😊</span> Emoji Suggestions
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="emoji in (analysisResult?.recommendations?.emoji_suggestions || [])" :key="emoji">
                                    <button @click="addEmoji(emoji)" 
                                            class="px-3 py-2 bg-yellow-100 text-2xl rounded-lg hover:bg-yellow-200 transition"
                                            x-text="emoji"></button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Estimated Improvement -->
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-6 border border-green-200 shadow-lg">
                        <div class="flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-lg text-gray-700 mb-2">
                                    <span class="font-bold">🚀 Estimated Performance Improvement:</span>
                                </p>
                                <p class="text-3xl font-bold text-green-600" x-text="analysisResult?.recommendations?.estimated_improvement || '0%'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div x-show="analysisError" class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                <div class="text-red-500 text-4xl mb-3">⚠️</div>
                <p class="text-red-700 mb-4" x-text="analysisError"></p>
                <button @click="analyzeCaption()" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                    🔄 Try Again
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/partials/caption-analysis.blade.php ENDPATH**/ ?>