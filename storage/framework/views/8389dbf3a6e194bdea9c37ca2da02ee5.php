

<?php $__env->startSection('title', 'Caption Analytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Caption Analytics</h1>
                <p class="text-sm text-gray-500 mt-1">Track performa caption Anda dan dapatkan insights untuk AI yang lebih pintar</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('analytics.export.pdf')); ?>" 
                   class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </a>
                <a href="<?php echo e(route('analytics.export.csv')); ?>" 
                   class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>
        
        <!-- How It Works Info Box -->
        <div class="mt-4 bg-blue-50 border-l-4 border-blue-600 p-4 rounded-r-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-blue-900 mb-1">📊 Cara Kerja Analytics</h4>
                    <p class="text-sm text-blue-800 mb-2">
                        Fitur ini menggunakan <strong>manual input</strong> dari Anda. Berikut cara kerjanya:
                    </p>
                    <ol class="text-sm text-blue-800 space-y-1 ml-4 list-decimal">
                        <li>Anda generate caption di AI Generator</li>
                        <li>Anda posting caption tersebut di sosial media (Instagram, Facebook, TikTok, dll)</li>
                        <li>Setelah beberapa hari, lihat performa di sosmed Anda (likes, comments, shares, reach)</li>
                        <li>Input data performa ke platform ini dengan klik <strong>"Edit Metrics"</strong></li>
                        <li>AI kami akan belajar dari caption yang sukses untuk generate caption yang lebih baik!</li>
                    </ol>
                    <p class="text-sm text-blue-800 mt-2">
                        💡 <strong>Tip:</strong> Semakin banyak data yang Anda input, semakin pintar AI dalam membuat caption yang cocok untuk bisnis Anda!
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Captions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($stats['total_captions']); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Avg Engagement</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format($stats['avg_engagement'], 1)); ?>%</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Reach</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format($stats['total_reach'])); ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Successful</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($stats['successful_captions']); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- 📊 Enhanced Analytics Dashboard -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">📊 Advanced Analytics</h2>
            <div class="flex gap-2">
                <button onclick="refreshAnalytics()" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                    🔄 Refresh Data
                </button>
                <button onclick="toggleAnalyticsView()" class="px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition">
                    📈 Toggle View
                </button>
            </div>
        </div>

        <!-- Performance Analysis Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Best Performing Analysis -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">🏆 Best Performing Analysis</h3>
                    <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">Top 10%</span>
                </div>
                
                <div class="space-y-4">
                    <!-- Best Caption -->
                    <div class="border-l-4 border-green-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-2">🥇 Champion Caption</h4>
                        <div class="bg-green-50 rounded-lg p-3 mb-3">
                            <p class="text-sm text-gray-800" id="bestCaption"><?php echo e($bestCaption->caption_text ?? 'Belum ada data caption terbaik'); ?></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Engagement Rate:</span>
                                <span class="font-semibold text-green-600" id="bestEngagement"><?php echo e($bestCaption->engagement_rate ?? 0); ?>%</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Total Reach:</span>
                                <span class="font-semibold text-green-600" id="bestReach"><?php echo e(number_format($bestCaption->reach ?? 0)); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Success Patterns -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">📈 Success Patterns</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Best Platform:</span>
                                <span class="font-medium" id="bestPlatform"><?php echo e($analytics['best_platform'] ?? 'Instagram'); ?></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Best Category:</span>
                                <span class="font-medium" id="bestCategory"><?php echo e($analytics['best_category'] ?? 'Social Media'); ?></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Avg Caption Length:</span>
                                <span class="font-medium" id="bestLength"><?php echo e($analytics['best_length'] ?? '150'); ?> chars</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Best Hashtag Count:</span>
                                <span class="font-medium" id="bestHashtags"><?php echo e($analytics['best_hashtags'] ?? '5-7'); ?> tags</span>
                            </div>
                        </div>
                    </div>

                    <!-- Success Keywords -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">🔑 High-Impact Keywords</h4>
                        <div class="flex flex-wrap gap-1">
                            <?php
                                $successKeywords = $analytics['success_keywords'] ?? ['promo', 'diskon', 'terbatas', 'eksklusif', 'gratis'];
                            ?>
                            <?php $__currentLoopData = $successKeywords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keyword): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded"><?php echo e($keyword); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Worst Performing Analysis -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">📉 Areas for Improvement</h3>
                    <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded">Bottom 10%</span>
                </div>
                
                <div class="space-y-4">
                    <!-- Worst Caption -->
                    <div class="border-l-4 border-red-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-2">⚠️ Needs Improvement</h4>
                        <div class="bg-red-50 rounded-lg p-3 mb-3">
                            <p class="text-sm text-gray-800" id="worstCaption"><?php echo e($worstCaption->caption_text ?? 'Belum ada data caption terburuk'); ?></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Engagement Rate:</span>
                                <span class="font-semibold text-red-600" id="worstEngagement"><?php echo e($worstCaption->engagement_rate ?? 0); ?>%</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Total Reach:</span>
                                <span class="font-semibold text-red-600" id="worstReach"><?php echo e(number_format($worstCaption->reach ?? 0)); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Problem Patterns -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">🚫 Problem Patterns</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Worst Platform:</span>
                                <span class="font-medium" id="worstPlatform"><?php echo e($analytics['worst_platform'] ?? 'Twitter'); ?></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Worst Category:</span>
                                <span class="font-medium" id="worstCategory"><?php echo e($analytics['worst_category'] ?? 'Generic'); ?></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Problem Length:</span>
                                <span class="font-medium" id="worstLength"><?php echo e($analytics['worst_length'] ?? '300+'); ?> chars</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Hashtag Overuse:</span>
                                <span class="font-medium" id="worstHashtags"><?php echo e($analytics['worst_hashtags'] ?? '15+'); ?> tags</span>
                            </div>
                        </div>
                    </div>

                    <!-- Improvement Suggestions -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">💡 Improvement Tips</h4>
                        <div class="space-y-1 text-sm text-gray-600">
                            <div class="flex items-start">
                                <span class="text-blue-600 mr-2">•</span>
                                <span>Fokus pada platform dengan engagement terbaik</span>
                            </div>
                            <div class="flex items-start">
                                <span class="text-blue-600 mr-2">•</span>
                                <span>Gunakan 5-7 hashtag yang relevan</span>
                            </div>
                            <div class="flex items-start">
                                <span class="text-blue-600 mr-2">•</span>
                                <span>Optimalkan panjang caption 100-200 karakter</span>
                            </div>
                            <div class="flex items-start">
                                <span class="text-blue-600 mr-2">•</span>
                                <span>Gunakan kata kunci yang terbukti efektif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Optimal Posting Time & Audience Patterns -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Optimal Posting Time -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">⏰ Optimal Posting Time</h3>
                
                <!-- Time Heatmap -->
                <div class="mb-4">
                    <h4 class="font-medium text-gray-900 mb-3">📅 Best Days & Hours</h4>
                    <div class="grid grid-cols-7 gap-1 text-xs mb-2">
                        <div class="text-center font-medium text-gray-600">Sen</div>
                        <div class="text-center font-medium text-gray-600">Sel</div>
                        <div class="text-center font-medium text-gray-600">Rab</div>
                        <div class="text-center font-medium text-gray-600">Kam</div>
                        <div class="text-center font-medium text-gray-600">Jum</div>
                        <div class="text-center font-medium text-gray-600">Sab</div>
                        <div class="text-center font-medium text-gray-600">Min</div>
                    </div>
                    <div class="grid grid-cols-7 gap-1" id="timeHeatmap">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Peak Hours -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 mb-2">🔥 Peak Performance Hours</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-blue-800">Morning Peak:</span>
                            <span class="font-semibold text-blue-900" id="morningPeak">08:00 - 10:00</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-blue-800">Afternoon Peak:</span>
                            <span class="font-semibold text-blue-900" id="afternoonPeak">13:00 - 15:00</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-blue-800">Evening Peak:</span>
                            <span class="font-semibold text-blue-900" id="eveningPeak">19:00 - 21:00</span>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="mt-4 p-3 bg-green-50 rounded-lg">
                    <h4 class="font-medium text-green-900 mb-2">📈 Posting Recommendations</h4>
                    <div class="text-sm text-green-800 space-y-1">
                        <div>• Post pada <strong>Selasa-Kamis</strong> untuk engagement terbaik</div>
                        <div>• Waktu optimal: <strong>19:00-21:00 WIB</strong></div>
                        <div>• Hindari posting pada <strong>Minggu pagi</strong></div>
                    </div>
                </div>
            </div>

            <!-- Audience Engagement Patterns -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">👥 Audience Engagement Patterns</h3>
                
                <!-- Engagement Types -->
                <div class="mb-4">
                    <h4 class="font-medium text-gray-900 mb-3">💬 Engagement Breakdown</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-red-500 mr-2">❤️</span>
                                <span class="text-sm text-gray-600">Likes</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: <?php echo e($engagementBreakdown['likes_percentage'] ?? 70); ?>%"></div>
                                </div>
                                <span class="text-sm font-medium"><?php echo e($engagementBreakdown['likes_percentage'] ?? 70); ?>%</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-blue-500 mr-2">💬</span>
                                <span class="text-sm text-gray-600">Comments</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo e($engagementBreakdown['comments_percentage'] ?? 20); ?>%"></div>
                                </div>
                                <span class="text-sm font-medium"><?php echo e($engagementBreakdown['comments_percentage'] ?? 20); ?>%</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-green-500 mr-2">🔄</span>
                                <span class="text-sm text-gray-600">Shares</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo e($engagementBreakdown['shares_percentage'] ?? 10); ?>%"></div>
                                </div>
                                <span class="text-sm font-medium"><?php echo e($engagementBreakdown['shares_percentage'] ?? 10); ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Audience Behavior -->
                <div class="mb-4">
                    <h4 class="font-medium text-gray-900 mb-3">🎯 Audience Behavior</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-purple-50 rounded-lg p-3">
                            <div class="text-purple-900 font-medium">Avg Response Time</div>
                            <div class="text-purple-700 text-lg font-bold" id="avgResponseTime">2.5 hours</div>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-3">
                            <div class="text-orange-900 font-medium">Peak Activity</div>
                            <div class="text-orange-700 text-lg font-bold" id="peakActivity">Evening</div>
                        </div>
                    </div>
                </div>

                <!-- Content Preferences -->
                <div class="bg-indigo-50 rounded-lg p-4">
                    <h4 class="font-medium text-indigo-900 mb-2">🎨 Content Preferences</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-indigo-800">Visual Content:</span>
                            <span class="font-semibold text-indigo-900">+45% engagement</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-indigo-800">Question Posts:</span>
                            <span class="font-semibold text-indigo-900">+32% comments</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-indigo-800">Behind-the-Scenes:</span>
                            <span class="font-semibold text-indigo-900">+28% shares</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROI Calculator & Competitor Comparison -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- ROI Calculator -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 ROI Calculator</h3>
                
                <!-- ROI Input Form -->
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sales dari Caption (Rp)</label>
                        <input type="number" id="salesAmount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="1000000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Marketing Cost (Rp)</label>
                        <input type="number" id="marketingCost" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="100000">
                    </div>
                    <button onclick="calculateROI()" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition">
                        Calculate ROI
                    </button>
                </div>

                <!-- ROI Results -->
                <div id="roiResults" class="hidden">
                    <div class="bg-green-50 rounded-lg p-4 mb-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600" id="roiPercentage">0%</div>
                            <div class="text-sm text-green-800">Return on Investment</div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-blue-50 rounded-lg p-3">
                            <div class="text-blue-900 font-medium">Net Profit</div>
                            <div class="text-blue-700 text-lg font-bold" id="netProfit">Rp 0</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3">
                            <div class="text-purple-900 font-medium">Profit Margin</div>
                            <div class="text-purple-700 text-lg font-bold" id="profitMargin">0%</div>
                        </div>
                    </div>
                </div>

                <!-- ROI Insights -->
                <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                    <h4 class="font-medium text-yellow-900 mb-2">📊 ROI Insights</h4>
                    <div class="text-sm text-yellow-800 space-y-1">
                        <div>• ROI > 300% = Excellent performance</div>
                        <div>• ROI 100-300% = Good performance</div>
                        <div>• ROI < 100% = Needs improvement</div>
                    </div>
                </div>

                <!-- Top ROI Captions -->
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">🏆 Top ROI Captions</h4>
                    <div class="space-y-2" id="topROICaptions">
                        <!-- Will be populated dynamically -->
                    </div>
                </div>
            </div>

            <!-- Competitor Comparison -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🥊 Competitor Comparison</h3>
                
                <!-- Competitor Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Competitor</label>
                    <select id="competitorSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="loadCompetitorData()">
                        <option value="">Choose competitor...</option>
                        <?php if(isset($competitors)): ?>
                            <?php $__currentLoopData = $competitors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $competitor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($competitor->id); ?>"><?php echo e($competitor->username); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Comparison Results -->
                <div id="competitorComparison" class="hidden">
                    <div class="space-y-4">
                        <!-- Performance Comparison -->
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">📊 Performance vs Competitor</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Engagement Rate</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-blue-600" id="myEngagement">5.2%</span>
                                        <span class="text-xs text-gray-500">vs</span>
                                        <span class="text-sm font-medium text-red-600" id="competitorEngagement">3.8%</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Avg Reach</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-blue-600" id="myReach">2.5K</span>
                                        <span class="text-xs text-gray-500">vs</span>
                                        <span class="text-sm font-medium text-red-600" id="competitorReach">3.2K</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Posting Frequency</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-blue-600" id="myFrequency">5/week</span>
                                        <span class="text-xs text-gray-500">vs</span>
                                        <span class="text-sm font-medium text-red-600" id="competitorFrequency">7/week</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Competitive Advantages -->
                        <div class="bg-green-50 rounded-lg p-4">
                            <h4 class="font-medium text-green-900 mb-2">✅ Your Advantages</h4>
                            <div class="space-y-1 text-sm text-green-800" id="myAdvantages">
                                <div>• Higher engagement rate (+1.4%)</div>
                                <div>• Better content quality score</div>
                                <div>• More consistent posting schedule</div>
                            </div>
                        </div>

                        <!-- Areas to Improve -->
                        <div class="bg-orange-50 rounded-lg p-4">
                            <h4 class="font-medium text-orange-900 mb-2">📈 Areas to Improve</h4>
                            <div class="space-y-1 text-sm text-orange-800" id="improvementAreas">
                                <div>• Increase posting frequency</div>
                                <div>• Improve reach optimization</div>
                                <div>• Use trending hashtags more effectively</div>
                            </div>
                        </div>

                        <!-- Competitive Insights -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-2">🎯 Competitive Insights</h4>
                            <div class="space-y-1 text-sm text-blue-800">
                                <div>• Competitor posts more during weekends</div>
                                <div>• They use 8-10 hashtags per post</div>
                                <div>• Strong in video content format</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Competitor Selected -->
                <div id="noCompetitorSelected" class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">Select a competitor to see comparison</p>
                    <a href="<?php echo e(route('competitor-analysis.index')); ?>" class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-block">
                        Add competitors in Competitor Analysis →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <!-- Platform Performance -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Platform Performance</h3>
            <?php if($platformPerformance->count() > 0): ?>
            <canvas id="platformChart"></canvas>
            <?php else: ?>
            <p class="text-gray-500 text-center py-8 text-sm">Belum ada data platform</p>
            <?php endif; ?>
        </div>

        <!-- Category Performance -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Category Performance</h3>
            <?php if($categoryPerformance->count() > 0): ?>
            <canvas id="categoryChart"></canvas>
            <?php else: ?>
            <p class="text-gray-500 text-center py-8 text-sm">Belum ada data kategori</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Engagement Over Time -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Engagement Over Time (Last 30 Days)</h3>
        <?php if($engagementOverTime->count() > 0): ?>
        <canvas id="timeChart"></canvas>
        <?php else: ?>
        <p class="text-gray-500 text-center py-8 text-sm">Belum ada data engagement</p>
        <?php endif; ?>
    </div>

    <!-- Top Performing Captions -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top Performing Captions</h3>
        </div>
        <div class="p-4">
            <?php if($topCaptions->count() > 0): ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $topCaptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 line-clamp-2"><?php echo e(Str::limit($caption->caption_text, 100)); ?></p>
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-600">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded"><?php echo e($caption->platform ?? 'N/A'); ?></span>
                                <span><?php echo e($caption->category ?? 'N/A'); ?></span>
                                <span><?php echo e($caption->posted_at ? $caption->posted_at->format('d M Y') : 'N/A'); ?></span>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-lg font-bold text-green-600"><?php echo e(number_format($caption->engagement_rate, 1)); ?>%</p>
                            <p class="text-xs text-gray-500">engagement</p>
                        </div>
                    </div>
                    <div class="flex gap-4 text-xs text-gray-600 mt-3 pt-3 border-t">
                        <span>❤️ <?php echo e(number_format($caption->likes)); ?></span>
                        <span>💬 <?php echo e(number_format($caption->comments)); ?></span>
                        <span>🔄 <?php echo e(number_format($caption->shares)); ?></span>
                        <span>👁️ <?php echo e(number_format($caption->reach)); ?></span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-center py-8">Belum ada caption yang di-track. Mulai track caption Anda dari AI Generator!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Captions -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Recent Captions</h3>
                <p class="text-xs text-gray-500 mt-1">Klik "Edit Metrics" untuk input data performa dari sosial media Anda</p>
            </div>
            <button onclick="showAddCaptionModal()" class="text-sm text-blue-600 hover:text-blue-700">
                + Add Caption
            </button>
        </div>
        <div class="overflow-x-auto">
            <?php if($recentCaptions->count() > 0): ?>
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Caption</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Platform</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Engagement</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Posted</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $recentCaptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo e(Str::limit($caption->caption_text, 60)); ?></td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs"><?php echo e($caption->platform ?? 'N/A'); ?></span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <?php if($caption->engagement_rate > 0): ?>
                                <span class="font-semibold text-green-600"><?php echo e(number_format($caption->engagement_rate, 1)); ?>%</span>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">Belum diinput</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($caption->posted_at ? $caption->posted_at->format('d M Y') : '-'); ?></td>
                        <td class="px-4 py-3 text-sm">
                            <button onclick="editMetrics(<?php echo e($caption->id); ?>)" class="text-blue-600 hover:text-blue-700 font-medium">
                                📊 Edit Metrics
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="p-8 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p class="text-gray-500 mb-2">Belum ada caption yang di-track</p>
                <p class="text-sm text-gray-400 mb-4">Mulai generate caption di AI Generator, lalu track performanya di sini!</p>
                <a href="<?php echo e(route('ai.generator')); ?>" class="inline-block px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                    🚀 Generate Caption Sekarang
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    <?php if($platformPerformance->count() > 0): ?>
    const platformCtx = document.getElementById('platformChart').getContext('2d');
    new Chart(platformCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($platformPerformance->pluck('platform')); ?>,
            datasets: [{
                label: 'Avg Engagement %',
                data: <?php echo json_encode($platformPerformance->pluck('avg_engagement')); ?>,
                backgroundColor: '#3b82f6',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { y: { beginAtZero: true } }
        }
    });
    <?php endif; ?>

    <?php if($categoryPerformance->count() > 0): ?>
    const categoryLabels = <?php echo json_encode($categoryPerformance->pluck('category')); ?>;
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoryLabels.map(c => formatCategoryLabel(c)),
            datasets: [{
                data: <?php echo json_encode($categoryPerformance->pluck('avg_engagement')); ?>,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#84cc16'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    <?php endif; ?>

    <?php if($engagementOverTime->count() > 0): ?>
    const timeCtx = document.getElementById('timeChart').getContext('2d');
    new Chart(timeCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($engagementOverTime->pluck('date')); ?>,
            datasets: [{
                label: 'Engagement Rate %',
                data: <?php echo json_encode($engagementOverTime->pluck('avg_engagement')); ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { y: { beginAtZero: true } }
        }
    });
    <?php endif; ?>

    function showAddCaptionModal() {
        showNotification('Gunakan AI Generator untuk membuat caption, lalu track di sini.', 'info');
    }
</script>

<!-- Edit Metrics Modal -->
<div id="editMetricsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Update Caption Metrics</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <form id="metricsForm" onsubmit="saveMetrics(event)" class="p-6">
            <input type="hidden" id="caption_id" name="caption_id">
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Likes</label>
                    <input type="number" id="likes" name="likes" min="0" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
                    <input type="number" id="comments" name="comments" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Shares</label>
                    <input type="number" id="shares" name="shares" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Saves</label>
                    <input type="number" id="saves" name="saves" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reach</label>
                    <input type="number" id="reach" name="reach" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Impressions</label>
                    <input type="number" id="impressions" name="impressions" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Clicks</label>
                    <input type="number" id="clicks" name="clicks" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Rating</label>
                    <select id="user_rating" name="user_rating"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">No rating</option>
                        <option value="1">⭐ 1 - Poor</option>
                        <option value="2">⭐⭐ 2 - Fair</option>
                        <option value="3">⭐⭐⭐ 3 - Good</option>
                        <option value="4">⭐⭐⭐⭐ 4 - Very Good</option>
                        <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea id="user_notes" name="user_notes" rows="3"
                          placeholder="What worked well? What didn't?"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" id="marked_as_successful" name="marked_as_successful"
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Mark as successful caption (for AI training)</span>
                </label>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" 
                        class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Save Metrics
                </button>
                <button type="button" onclick="closeEditModal()"
                        class="px-6 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const categoryLabelMap = {
        'caption': 'Caption',
        'social_media': 'Social Media',
        'google_ads': 'Google Ads',
        'email_marketing': 'Email Marketing',
        'product_description': 'Deskripsi Produk',
        'blog_article': 'Artikel Blog',
        'video_script': 'Script Video',
        'whatsapp': 'WhatsApp',
        'seo_content': 'Konten SEO',
        'brand_voice': 'Brand Voice',
        'image_analysis': 'Analisis Gambar',
        'design_analysis': 'Analisis Desain',
        'financial_analysis': 'Analisis Keuangan',
        'ebook_analysis': 'Analisis Ebook',
        'reader_trend': 'Tren Pembaca',
        'multiple_captions': 'Multiple Captions',
    };

    function formatCategoryLabel(cat) {
        if (!cat) return 'Lainnya';
        return categoryLabelMap[cat] || cat.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    function editMetrics(id) {
        fetch(`/analytics/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const caption = data.caption;
                    document.getElementById('caption_id').value = caption.id;
                    document.getElementById('likes').value = caption.likes || 0;
                    document.getElementById('comments').value = caption.comments || 0;
                    document.getElementById('shares').value = caption.shares || 0;
                    document.getElementById('saves').value = caption.saves || 0;
                    document.getElementById('reach').value = caption.reach || 0;
                    document.getElementById('impressions').value = caption.impressions || 0;
                    document.getElementById('clicks').value = caption.clicks || 0;
                    document.getElementById('user_rating').value = caption.user_rating || '';
                    document.getElementById('user_notes').value = caption.user_notes || '';
                    document.getElementById('marked_as_successful').checked = caption.marked_as_successful;
                    document.getElementById('editMetricsModal').classList.remove('hidden');
                } else {
                    showNotification('Gagal memuat data caption.', 'error');
                }
            })
            .catch(() => showNotification('Gagal memuat data caption.', 'error'));
    }

    function closeEditModal() {
        document.getElementById('editMetricsModal').classList.add('hidden');
    }

    function saveMetrics(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const captionId = formData.get('caption_id');
        const data = {
            likes: parseInt(formData.get('likes')) || 0,
            comments: parseInt(formData.get('comments')) || 0,
            shares: parseInt(formData.get('shares')) || 0,
            saves: parseInt(formData.get('saves')) || 0,
            reach: parseInt(formData.get('reach')) || 0,
            impressions: parseInt(formData.get('impressions')) || 0,
            clicks: parseInt(formData.get('clicks')) || 0,
            user_rating: formData.get('user_rating') ? parseInt(formData.get('user_rating')) : null,
            user_notes: formData.get('user_notes'),
            marked_as_successful: formData.get('marked_as_successful') === 'on'
        };
        fetch(`/analytics/${captionId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Metrics berhasil disimpan! Engagement Rate: ' + data.engagement_rate + '%', 'success');
                closeEditModal();
                location.reload();
            } else {
                showNotification(data.message || 'Gagal menyimpan metrics.', 'error');
            }
        })
        .catch(() => showNotification('Gagal menyimpan metrics.', 'error'));
    }

    function refreshAnalytics() {
        const refreshBtn = document.querySelector('button[onclick="refreshAnalytics()"]');
        refreshBtn.innerHTML = '⏳ Loading...';
        refreshBtn.disabled = true;
        setTimeout(() => location.reload(), 1000);
    }

    function toggleAnalyticsView() {
        document.querySelectorAll('.analytics-section').forEach(s => s.classList.toggle('hidden'));
    }

    function initializeTimeHeatmap() {
        const heatmapContainer = document.getElementById('timeHeatmap');
        if (!heatmapContainer) return;
        const heatmapData = [
            [2, 1, 1, 2, 3, 4, 2],
            [1, 2, 3, 4, 5, 3, 1],
            [3, 4, 5, 4, 3, 2, 1],
            [2, 3, 4, 5, 4, 3, 2]
        ];
        const colors = ['bg-gray-200','bg-blue-200','bg-blue-400','bg-blue-600','bg-blue-800','bg-blue-900'];
        let html = '';
        for (let i = 0; i < 4; i++) {
            for (let j = 0; j < 7; j++) {
                const v = heatmapData[i][j];
                html += `<div class="w-8 h-6 rounded ${colors[v]} flex items-center justify-center text-xs text-white font-medium">${v}</div>`;
            }
        }
        heatmapContainer.innerHTML = html;
    }

    function calculateROI() {
        const salesAmount = parseFloat(document.getElementById('salesAmount').value) || 0;
        const marketingCost = parseFloat(document.getElementById('marketingCost').value) || 0;
        if (salesAmount <= 0 || marketingCost <= 0) {
            showNotification('Masukkan nilai penjualan dan biaya marketing yang valid.', 'error');
            return;
        }
        const netProfit = salesAmount - marketingCost;
        const roiPercentage = ((netProfit / marketingCost) * 100).toFixed(1);
        const profitMarginPercentage = ((netProfit / salesAmount) * 100).toFixed(1);
        document.getElementById('roiPercentage').textContent = roiPercentage + '%';
        document.getElementById('netProfit').textContent = 'Rp ' + netProfit.toLocaleString('id-ID');
        document.getElementById('profitMargin').textContent = profitMarginPercentage + '%';
        document.getElementById('roiResults').classList.remove('hidden');
        const roiEl = document.getElementById('roiPercentage');
        roiEl.className = 'text-3xl font-bold ' + (roiPercentage >= 300 ? 'text-green-600' : roiPercentage >= 100 ? 'text-blue-600' : 'text-red-600');
        const topROIContainer = document.getElementById('topROICaptions');
        const samples = [
            { text: '🔥 Flash Sale 50% OFF! Limited time only...', roi: 450 },
            { text: '✨ New product launch with exclusive discount...', roi: 320 },
            { text: '💎 Premium quality at affordable price...', roi: 280 }
        ];
        topROIContainer.innerHTML = samples.map(c => {
            const color = c.roi >= 300 ? 'text-green-600' : c.roi >= 100 ? 'text-blue-600' : 'text-red-600';
            return `<div class="flex justify-between items-center p-2 bg-gray-50 rounded text-sm"><span class="flex-1 truncate">${c.text}</span><span class="font-bold ${color} ml-2">${c.roi}%</span></div>`;
        }).join('');
    }

    function loadCompetitorData() {
        const competitorId = document.getElementById('competitorSelect').value;
        if (!competitorId) {
            document.getElementById('competitorComparison').classList.add('hidden');
            document.getElementById('noCompetitorSelected').classList.remove('hidden');
            return;
        }
        document.getElementById('noCompetitorSelected').classList.add('hidden');
        document.getElementById('competitorComparison').classList.remove('hidden');
        updateCompetitorComparison({
            engagement_rate: 3.8, avg_reach: 3200, posting_frequency: 7,
            advantages: ['Higher engagement rate (+1.4%)', 'Better content quality score', 'More consistent posting schedule'],
            improvements: ['Increase posting frequency', 'Improve reach optimization', 'Use trending hashtags more effectively']
        });
    }

    function updateCompetitorComparison(data) {
        document.getElementById('competitorEngagement').textContent = data.engagement_rate + '%';
        document.getElementById('competitorReach').textContent = (data.avg_reach / 1000).toFixed(1) + 'K';
        document.getElementById('competitorFrequency').textContent = data.posting_frequency + '/week';
        document.getElementById('myAdvantages').innerHTML = data.advantages.map(a => `<div>• ${a}</div>`).join('');
        document.getElementById('improvementAreas').innerHTML = data.improvements.map(i => `<div>• ${i}</div>`).join('');
    }

    document.addEventListener('DOMContentLoaded', function() {
        initializeTimeHeatmap();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/analytics.blade.php ENDPATH**/ ?>