

<?php $__env->startSection('title', 'My Feedback'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">My Feedback</h1>
            <p class="text-sm text-gray-500 mt-1">Track feedback, bug reports, dan feature requests Anda</p>
        </div>
        <a href="<?php echo e(route('feedback.create')); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Submit Feedback
        </a>
    </div>

    <?php if($feedback->isEmpty()): ?>
    <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada feedback</h3>
        <p class="text-gray-600 mb-4">Punya bug report atau ide fitur? Submit sekarang!</p>
        <a href="<?php echo e(route('feedback.create')); ?>" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Submit Feedback
        </a>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php $__currentLoopData = $feedback; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="px-2 py-1 bg-<?php echo e($item->getTypeBadgeColor()); ?>-100 text-<?php echo e($item->getTypeBadgeColor()); ?>-700 text-xs rounded font-medium">
                            <?php echo e($item->getTypeLabel()); ?>

                        </span>
                        <span class="px-2 py-1 bg-<?php echo e($item->getStatusBadgeColor()); ?>-100 text-<?php echo e($item->getStatusBadgeColor()); ?>-700 text-xs rounded font-medium">
                            <?php echo e($item->getStatusLabel()); ?>

                        </span>
                        <span class="text-xs text-gray-500"><?php echo e($item->created_at->diffForHumans()); ?></span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo e($item->title); ?></h3>
                    <p class="text-sm text-gray-600 line-clamp-2"><?php echo e($item->description); ?></p>
                    
                    <?php if($item->admin_response): ?>
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-xs font-medium text-blue-900 mb-1">Response from Admin:</p>
                        <p class="text-sm text-blue-800"><?php echo e($item->admin_response); ?></p>
                        <p class="text-xs text-blue-600 mt-1"><?php echo e($item->responded_at->diffForHumans()); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <a href="<?php echo e(route('feedback.show', $item)); ?>" class="ml-4 text-blue-600 hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="mt-6">
        <?php echo e($feedback->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.' . auth()->user()->role, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\feedback\index.blade.php ENDPATH**/ ?>