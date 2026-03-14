{{-- 📈 Caption Performance Predictor Modal --}}
<div x-show="showPerformancePredictor" 
     x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
     @click.self="showPerformancePredictor = false">
    
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 bg-gradient-to-r from-purple-600 to-blue-600 text-white p-6 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold flex items-center">
                        📈 Caption Performance Predictor
                    </h2>
                    <p class="text-purple-100 text-sm mt-1">Predict engagement & get improvement suggestions</p>
                </div>
                <button @click="showPerformancePredictor = false" 
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6">
            {{-- Input Caption --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Caption yang mau dianalisis
                </label>
                <textarea x-model="performanceCaption" 
                          rows="6"
                          placeholder="Paste caption kamu di sini..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
            </div>

            {{-- Platform & Industry --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                    <select x-model="performancePlatform" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="instagram">Instagram</option>
                        <option value="facebook">Facebook</option>
                        <option value="tiktok">TikTok</option>
                        <option value="linkedin">LinkedIn</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                    <select x-model="performanceIndustry" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="fashion">Fashion</option>
                        <option value="food">Food & Beverage</option>
                        <option value="beauty">Beauty</option>
                        <option value="tech">Technology</option>
                        <option value="general">General</option>
                    </select>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3 mb-6">
                <button @click="analyzeCaptionPerformance()" 
                        :disabled="!performanceCaption || performanceLoading"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-blue-600 text-white py-3 rounded-lg hover:shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                    <span x-show="!performanceLoading">🎯 Analyze Performance</span>
                    <span x-show="performanceLoading" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Analyzing...
                    </span>
                </button>
                
                <button @click="generat