

<?php $__env->startSection('title', 'ML Analytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">🤖 ML Analytics Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Machine Learning insights and auto-recommendations for AI improvement</p>
        </div>
        <a href="<?php echo e(route('admin.ml-analytics.export')); ?>" 
           class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            📥 Export Training Data
        </a>
    </div>

    <!-- Overall Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Captions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format($totalCaptions)); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Rated Captions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format($totalRated)); ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e(number_format($ratingRate, 1)); ?>% participation</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Average Rating</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        <?php if($avgRating): ?>
                            <?php echo e(number_format($avgRating, 1)); ?> ⭐
                        <?php else: ?>
                            - ⭐
                        <?php endif; ?>
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">ML Status</p>
                    <p class="text-lg font-semibold mt-1">
                        <?php if($totalRated >= 500): ?>
                            <span class="text-green-600">✓ Ready for Fine-tuning</span>
                        <?php elseif($totalRated >= 100): ?>
                            <span class="text-yellow-600">⏳ Collecting Data</span>
                        <?php else: ?>
                            <span class="text-gray-600">📊 Starting Phase</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-Recommendations -->
    <?php if(count($recommendations) > 0): ?>
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">💡 Auto-Recommendations</h2>
        <p class="text-sm text-gray-600 mb-4">System-generated insights based on user ratings and feedback</p>
        <div class="space-y-3">
            <?php $__currentLoopData = $recommendations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-start p-4 rounded-lg border <?php echo e($rec['priority'] === 'high' ? 'bg-red-50 border-red-200' : ($rec['priority'] === 'medium' ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200')); ?>">
                <div class="flex-shrink-0 mr-3 text-2xl">
                    <?php if($rec['priority'] === 'high'): ?>
                        🔴
                    <?php elseif($rec['priority'] === 'medium'): ?>
                        🟡
                    <?php else: ?>
                        🟢
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        <h3 class="text-sm font-semibold text-gray-900"><?php echo e($rec['title']); ?></h3>
                        <span class="px-2 py-0.5 text-xs font-medium rounded <?php echo e($rec['priority'] === 'high' ? 'bg-red-100 text-red-700' : ($rec['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')); ?>">
                            <?php echo e(strtoupper($rec['priority'])); ?>

                        </span>
                    </div>
                    <p class="text-sm text-gray-700 mt-1"><?php echo e($rec['message']); ?></p>
                    <div class="mt-2 flex items-center text-xs text-gray-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <strong>Recommended Action:</strong>&nbsp;<?php echo e($rec['action']); ?>

                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg border border-gray-200 p-12 text-center mb-6">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Recommendations Yet</h3>
        <p class="text-sm text-gray-600">Collect more ratings to get AI improvement recommendations</p>
    </div>
    <?php endif; ?>

    <!-- Performance Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Category Performance -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Category Performance</h2>
            <div class="space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $categoryPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900"><?php echo e(ucfirst(str_replace('_', ' ', $cat->category))); ?></p>
                        <p class="text-xs text-gray-600"><?php echo e($cat->total); ?> captions • <?php echo e($cat->high_rated); ?> high-rated (4-5⭐)</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold <?php echo e($cat->avg_rating >= 4 ? 'text-green-600' : ($cat->avg_rating >= 3 ? 'text-yellow-600' : 'text-red-600')); ?>">
                            <?php echo e(number_format($cat->avg_rating, 1)); ?> ⭐
                        </p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500">No rated captions yet</p>
                    <p class="text-xs text-gray-400 mt-1">Data will appear after users rate captions</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Platform Performance -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📱 Platform Performance</h2>
            <div class="space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $platformPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900"><?php echo e(ucfirst($plat->platform)); ?></p>
                        <p class="text-xs text-gray-600"><?php echo e($plat->total); ?> captions • <?php echo e($plat->high_rated); ?> high-rated (4-5⭐)</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold <?php echo e($plat->avg_rating >= 4 ? 'text-green-600' : ($plat->avg_rating >= 3 ? 'text-yellow-600' : 'text-red-600')); ?>">
                            <?php echo e(number_format($plat->avg_rating, 1)); ?> ⭐
                        </p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500">No rated captions yet</p>
                    <p class="text-xs text-gray-400 mt-1">Data will appear after users rate captions</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tone Performance -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🎭 Tone Performance</h2>
            <div class="space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $tonePerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900"><?php echo e(ucfirst($tone->tone)); ?></p>
                        <p class="text-xs text-gray-600"><?php echo e($tone->total); ?> captions • <?php echo e($tone->high_rated); ?> high-rated (4-5⭐)</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold <?php echo e($tone->avg_rating >= 4 ? 'text-green-600' : ($tone->avg_rating >= 3 ? 'text-yellow-600' : 'text-red-600')); ?>">
                            <?php echo e(number_format($tone->avg_rating, 1)); ?> ⭐
                        </p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500">No rated captions yet</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Local Language Performance -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🗣️ Local Language Performance</h2>
            <div class="space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $localLanguagePerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Bahasa <?php echo e(ucfirst($lang->local_language)); ?></p>
                        <p class="text-xs text-gray-600"><?php echo e($lang->total); ?> captions • <?php echo e($lang->high_rated); ?> high-rated (4-5⭐)</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold <?php echo e($lang->avg_rating >= 4 ? 'text-green-600' : ($lang->avg_rating >= 3 ? 'text-yellow-600' : 'text-red-600')); ?>">
                            <?php echo e(number_format($lang->avg_rating, 1)); ?> ⭐
                        </p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500">No local language captions rated yet</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top Words by Language -->
    <?php if(count($topWordsByLanguage) > 0): ?>
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-2">🔥 Top Performing Words</h2>
        <p class="text-sm text-gray-600 mb-4">Most frequently used words in high-rated captions (4-5⭐)</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $topWordsByLanguage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Bahasa <?php echo e(ucfirst($lang)); ?></h3>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">
                        <?php echo e($data['total_captions']); ?> captions
                    </span>
                </div>
                <div class="space-y-2">
                    <?php $__currentLoopData = array_slice($data['top_words'], 0, 5, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 font-medium">"<?php echo e($word); ?>"</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-900 text-xs font-bold rounded"><?php echo e($count); ?>x</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Rating Distribution -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Rating Distribution</h2>
        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $ratingDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center">
                <div class="w-20 text-sm font-medium text-gray-700">
                    <?php echo e($dist->rating); ?> ⭐
                </div>
                <div class="flex-1 mx-4">
                    <div class="w-full bg-gray-200 rounded-full h-6">
                        <div class="bg-blue-600 h-6 rounded-full flex items-center justify-end pr-2" 
                             style="width: <?php echo e($totalRated > 0 ? ($dist->count / $totalRated * 100) : 0); ?>%">
                            <span class="text-xs font-medium text-white"><?php echo e($dist->count); ?></span>
                        </div>
                    </div>
                </div>
                <div class="w-16 text-right text-sm text-gray-600">
                    <?php echo e($totalRated > 0 ? number_format($dist->count / $totalRated * 100, 1) : 0); ?>%
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-gray-500 text-center py-4">No ratings yet</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Feedback -->
    <?php if($recentFeedback->count() > 0): ?>
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">💬 Recent User Feedback</h2>
        <div class="space-y-3">
            <?php $__currentLoopData = $recentFeedback; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-medium rounded">User #<?php echo e($fb->user_id); ?></span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs text-gray-600"><?php echo e(ucfirst($fb->category)); ?></span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs text-gray-600"><?php echo e(ucfirst($fb->platform)); ?></span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs text-gray-500"><?php echo e($fb->created_at->diffForHumans()); ?></span>
                        </div>
                        <p class="text-sm text-gray-800 italic bg-gray-50 p-2 rounded">"<?php echo e($fb->feedback); ?>"</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <div class="flex items-center">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <span class="text-sm <?php echo e($i <= $fb->rating ? 'text-yellow-400' : 'text-gray-300'); ?>">⭐</span>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\ml-analytics\index.blade.php ENDPATH**/ ?>