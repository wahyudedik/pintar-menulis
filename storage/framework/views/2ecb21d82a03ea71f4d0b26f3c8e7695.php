

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">ML Training Dashboard</h1>
        <p class="text-sm text-gray-600 mt-1">Review dan kelola data training untuk model ML</p>
    </div>

    <?php if(session('success')): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Training Data</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($stats['total_training_data']); ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending Reviews</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($stats['pending_reviews']); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Excellent Quality</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($stats['excellent_quality']); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Average Quality</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($stats['average_quality']); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Model Info -->
    <?php if($latestModel): ?>
    <div class="bg-purple-600 rounded-lg p-6 mb-6 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold mb-2">Current Model: <?php echo e($latestModel->version); ?></h3>
                <p class="text-purple-100 text-sm"><?php echo e($latestModel->description); ?></p>
                <div class="mt-2 text-sm text-purple-100">
                    Training Data: <?php echo e($latestModel->training_count); ?> • 
                    Accuracy: <?php echo e($latestModel->accuracy_score); ?>%
                </div>
            </div>
            <div>
                <span class="px-4 py-2 bg-white text-purple-600 rounded-lg text-sm font-semibold">
                    <?php echo e($latestModel->is_active ? 'Active' : 'Inactive'); ?>

                </span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Successful Captions for Training -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Successful Captions (High Engagement)</h2>
            <p class="text-sm text-gray-600 mt-1">Caption dengan performa tinggi untuk melatih AI</p>
        </div>
        <div class="p-4">
            <?php if($successfulCaptions->count() > 0): ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $successfulCaptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 mb-2"><?php echo e(Str::limit($caption->caption_text, 150)); ?></p>
                            <div class="flex items-center gap-3 text-xs text-gray-600">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded"><?php echo e($caption->platform ?? 'N/A'); ?></span>
                                <span><?php echo e($caption->category); ?></span>
                                <span>Tone: <?php echo e($caption->tone); ?></span>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-lg font-bold text-green-600"><?php echo e(number_format($caption->engagement_rate, 1)); ?>%</p>
                            <p class="text-xs text-gray-500">engagement</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <div class="flex gap-4 text-xs text-gray-600">
                            <span>❤️ <?php echo e(number_format($caption->likes)); ?></span>
                            <span>💬 <?php echo e(number_format($caption->comments)); ?></span>
                            <span>🔄 <?php echo e(number_format($caption->shares)); ?></span>
                        </div>
                        <?php if(!$caption->used_for_training): ?>
                        <button onclick="trainFromCaption(<?php echo e($caption->id); ?>)" 
                                class="px-3 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700 transition">
                            Use for Training
                        </button>
                        <?php else: ?>
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                            ✓ Already Trained
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-center py-8">Belum ada caption dengan performa tinggi</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Reviews -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Pending Reviews</h2>
            <div class="flex gap-3 text-sm">
                <a href="<?php echo e(route('guru.training.history')); ?>" class="text-purple-600 hover:text-purple-700">
                    Training History →
                </a>
                <span class="text-gray-300">|</span>
                <a href="<?php echo e(route('guru.analytics')); ?>" class="text-purple-600 hover:text-purple-700">
                    Analytics →
                </a>
            </div>
        </div>
        <div class="p-4">
            <?php if($pendingReviews->count() > 0): ?>
            <div class="space-y-4">
                <?php $__currentLoopData = $pendingReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h4 class="text-base font-semibold text-gray-900 mb-2"><?php echo e($order->category); ?></h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><span class="font-medium">Client:</span> <?php echo e($order->user->name); ?></p>
                                <?php if($order->operator): ?>
                                <p><span class="font-medium">Operator:</span> <?php echo e($order->operator->name); ?></p>
                                <?php endif; ?>
                                <?php if($order->completed_at): ?>
                                <p><span class="font-medium">Completed:</span> <?php echo e($order->completed_at->format('d M Y H:i')); ?></p>
                                <?php else: ?>
                                <p><span class="font-medium">Updated:</span> <?php echo e($order->updated_at->format('d M Y H:i')); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="<?php echo e(route('guru.training.show', $order)); ?>" 
                           class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition">
                            Review
                        </a>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3 mb-3">
                        <p class="text-xs font-semibold text-gray-700 mb-1">Brief:</p>
                        <p class="text-sm text-gray-800"><?php echo e(Str::limit($order->brief, 150)); ?></p>
                    </div>

                    <div class="bg-green-50 rounded-lg p-3">
                        <p class="text-xs font-semibold text-gray-700 mb-1">AI Output:</p>
                        <p class="text-sm text-gray-800"><?php echo e(Str::limit($order->result, 150)); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="mt-4">
                <?php echo e($pendingReviews->links()); ?>

            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500">Tidak ada order yang perlu direview</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function trainFromCaption(captionId) {
        if (!confirm('Use this caption for AI training?')) return;
        
        // Create a simple form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("guru.training.caption")); ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrfInput);
        
        const captionInput = document.createElement('input');
        captionInput.type = 'hidden';
        captionInput.name = 'caption_id';
        captionInput.value = captionId;
        form.appendChild(captionInput);
        
        const qualityInput = document.createElement('input');
        qualityInput.type = 'hidden';
        qualityInput.name = 'quality_rating';
        qualityInput.value = 'excellent'; // Auto-mark as excellent since it has high engagement
        form.appendChild(qualityInput);
        
        const notesInput = document.createElement('input');
        notesInput.type = 'hidden';
        notesInput.name = 'feedback_notes';
        notesInput.value = 'High engagement caption from analytics';
        form.appendChild(notesInput);
        
        document.body.appendChild(form);
        form.submit();
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\guru\training-dashboard.blade.php ENDPATH**/ ?>