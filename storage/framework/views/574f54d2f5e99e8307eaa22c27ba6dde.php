

<?php $__env->startSection('title', 'My Stats'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">📊 My Caption Stats</h1>
        <p class="text-sm text-gray-500 mt-1">Track your AI caption performance and get personalized insights</p>
    </div>

    <!-- Overall Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Generations</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($totalGenerations); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
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
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Best Category</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">
                        <?php if($bestCategory): ?>
                            <?php echo e(ucfirst(str_replace('_', ' ', $bestCategory->category))); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </p>
                    <?php if($bestCategory): ?>
                        <p class="text-xs text-gray-500"><?php echo e(number_format($bestCategory->avg_rating, 1)); ?> ⭐</p>
                    <?php endif; ?>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Best Platform</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">
                        <?php if($bestPlatform): ?>
                            <?php echo e(ucfirst($bestPlatform->platform)); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </p>
                    <?php if($bestPlatform): ?>
                        <p class="text-xs text-gray-500"><?php echo e(number_format($bestPlatform->avg_rating, 1)); ?> ⭐</p>
                    <?php endif; ?>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Suggestions -->
    <?php if(count($suggestions) > 0): ?>
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">💡 Suggestions for You</h2>
        <div class="space-y-3">
            <?php $__currentLoopData = $suggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suggestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-start p-4 rounded-lg <?php echo e($suggestion['type'] === 'warning' ? 'bg-yellow-50 border border-yellow-200' : ($suggestion['type'] === 'info' ? 'bg-blue-50 border border-blue-200' : 'bg-green-50 border border-green-200')); ?>">
                <div class="flex-shrink-0 mr-3">
                    <?php if($suggestion['type'] === 'warning'): ?>
                        <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    <?php elseif($suggestion['type'] === 'info'): ?>
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    <?php else: ?>
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-900"><?php echo e($suggestion['title']); ?></h3>
                    <p class="text-sm text-gray-700 mt-1"><?php echo e($suggestion['message']); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Rated Captions -->
    <?php if($recentRated->count() > 0): ?>
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📈 Recent Rated Captions</h2>
        <div class="space-y-4">
            <?php $__currentLoopData = $recentRated; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="text-xs font-medium text-gray-600"><?php echo e(ucfirst(str_replace('_', ' ', $caption->category))); ?></span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs text-gray-600"><?php echo e(ucfirst($caption->platform)); ?></span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs text-gray-500"><?php echo e($caption->created_at->diffForHumans()); ?></span>
                        </div>
                        <p class="text-sm text-gray-800 line-clamp-2"><?php echo e(Str::limit($caption->caption_text, 150)); ?></p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <div class="flex items-center">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <span class="text-lg <?php echo e($i <= $caption->rating ? 'text-yellow-400' : 'text-gray-300'); ?>">⭐</span>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="mt-4 text-center">
            <a href="<?php echo e(route('caption-history.index')); ?>" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                View All Caption History →
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Rated Captions Yet</h3>
        <p class="text-sm text-gray-600 mb-4">Start rating your generated captions to see insights here!</p>
        <a href="<?php echo e(route('ai.generator')); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            Generate Caption Now
        </a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/my-stats.blade.php ENDPATH**/ ?>