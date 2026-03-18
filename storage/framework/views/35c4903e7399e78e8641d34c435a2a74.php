

<?php $__env->startSection('title', 'Feedback Detail'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="<?php echo e(route('feedback.index')); ?>" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
                ← Back to My Feedback
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Feedback Detail</h1>
        </div>

        <!-- Feedback Card -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 bg-<?php echo e($feedback->getTypeBadgeColor()); ?>-100 text-<?php echo e($feedback->getTypeBadgeColor()); ?>-700 text-sm rounded font-medium">
                        <?php echo e($feedback->getTypeLabel()); ?>

                    </span>
                    <span class="px-3 py-1 bg-<?php echo e($feedback->getPriorityBadgeColor()); ?>-100 text-<?php echo e($feedback->getPriorityBadgeColor()); ?>-700 text-sm rounded font-medium">
                        <?php echo e(ucfirst($feedback->priority)); ?>

                    </span>
                </div>
                <span class="px-3 py-1 bg-<?php echo e($feedback->getStatusBadgeColor()); ?>-100 text-<?php echo e($feedback->getStatusBadgeColor()); ?>-700 text-sm rounded font-medium">
                    <?php echo e($feedback->getStatusLabel()); ?>

                </span>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 mb-4"><?php echo e($feedback->title); ?></h2>

            <div class="prose max-w-none mb-6">
                <p class="text-gray-700 whitespace-pre-wrap"><?php echo e($feedback->description); ?></p>
            </div>

            <?php if($feedback->screenshot): ?>
            <div class="mb-6">
                <p class="text-sm font-medium text-gray-700 mb-2">Screenshot:</p>
                <img src="<?php echo e($feedback->screenshot); ?>" alt="Screenshot" class="max-w-full h-auto rounded-lg border border-gray-200">
            </div>
            <?php endif; ?>

            <div class="pt-6 border-t border-gray-200 grid grid-cols-2 gap-4 text-sm">
                <?php if($feedback->page_url): ?>
                <div>
                    <p class="text-gray-500 mb-1">Page URL:</p>
                    <a href="<?php echo e($feedback->page_url); ?>" target="_blank" class="text-blue-600 hover:underline break-all text-xs">
                        <?php echo e($feedback->page_url); ?>

                    </a>
                </div>
                <?php endif; ?>

                <div>
                    <p class="text-gray-500 mb-1">Submitted:</p>
                    <p class="text-gray-900"><?php echo e($feedback->created_at->format('d M Y H:i')); ?></p>
                </div>
            </div>
        </div>

        <!-- Admin Response -->
        <?php if($feedback->admin_response): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start space-x-3 mb-3">
                <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Response from Admin</h3>
                    <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo e($feedback->admin_response); ?></p>
                    <p class="text-xs text-gray-500 mt-3">
                        <?php echo e($feedback->responded_at->format('d M Y H:i')); ?>

                    </p>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-yellow-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-sm font-medium text-yellow-900 mb-1">Menunggu Response</h3>
            <p class="text-sm text-yellow-700">Tim kami sedang review feedback kamu. Kami akan segera merespons.</p>
        </div>
        <?php endif; ?>

        <!-- Status Timeline -->
        <div class="mt-6 bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-sm font-medium text-gray-900 mb-4">Status Timeline</h3>
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Submitted</p>
                        <p class="text-xs text-gray-500"><?php echo e($feedback->created_at->format('d M Y H:i')); ?></p>
                    </div>
                </div>

                <?php if($feedback->status === 'in_progress' || $feedback->status === 'resolved' || $feedback->status === 'closed'): ?>
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">In Progress</p>
                        <p class="text-xs text-gray-500">Tim sedang mengerjakan</p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($feedback->status === 'resolved' || $feedback->status === 'closed'): ?>
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Resolved</p>
                        <p class="text-xs text-gray-500"><?php echo e($feedback->updated_at->format('d M Y H:i')); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.' . auth()->user()->role, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\feedback\show.blade.php ENDPATH**/ ?>