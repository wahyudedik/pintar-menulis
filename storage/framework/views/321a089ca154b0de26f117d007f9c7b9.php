

<?php $__env->startSection('title', 'Feedback Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Feedback Management</h1>
        <p class="text-sm text-gray-500 mt-1">Manage user feedback, bug reports, dan feature requests</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Total</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total']); ?></p>
        </div>
        <div class="bg-yellow-50 rounded-lg border border-yellow-200 p-4">
            <p class="text-xs text-yellow-700 mb-1">Open</p>
            <p class="text-2xl font-bold text-yellow-900"><?php echo e($stats['open']); ?></p>
        </div>
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
            <p class="text-xs text-blue-700 mb-1">In Progress</p>
            <p class="text-2xl font-bold text-blue-900"><?php echo e($stats['in_progress']); ?></p>
        </div>
        <div class="bg-green-50 rounded-lg border border-green-200 p-4">
            <p class="text-xs text-green-700 mb-1">Resolved</p>
            <p class="text-2xl font-bold text-green-900"><?php echo e($stats['resolved']); ?></p>
        </div>
        <div class="bg-red-50 rounded-lg border border-red-200 p-4">
            <p class="text-xs text-red-700 mb-1">Bugs</p>
            <p class="text-2xl font-bold text-red-900"><?php echo e($stats['bugs']); ?></p>
        </div>
        <div class="bg-purple-50 rounded-lg border border-purple-200 p-4">
            <p class="text-xs text-purple-700 mb-1">Features</p>
            <p class="text-2xl font-bold text-purple-900"><?php echo e($stats['features']); ?></p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                <option value="">All Types</option>
                <option value="bug" <?php echo e(request('type') === 'bug' ? 'selected' : ''); ?>>Bug Report</option>
                <option value="feature" <?php echo e(request('type') === 'feature' ? 'selected' : ''); ?>>Feature Request</option>
                <option value="improvement" <?php echo e(request('type') === 'improvement' ? 'selected' : ''); ?>>Improvement</option>
                <option value="question" <?php echo e(request('type') === 'question' ? 'selected' : ''); ?>>Question</option>
            </select>

            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                <option value="">All Status</option>
                <option value="open" <?php echo e(request('status') === 'open' ? 'selected' : ''); ?>>Open</option>
                <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                <option value="resolved" <?php echo e(request('status') === 'resolved' ? 'selected' : ''); ?>>Resolved</option>
                <option value="closed" <?php echo e(request('status') === 'closed' ? 'selected' : ''); ?>>Closed</option>
            </select>

            <select name="priority" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                <option value="">All Priority</option>
                <option value="low" <?php echo e(request('priority') === 'low' ? 'selected' : ''); ?>>Low</option>
                <option value="medium" <?php echo e(request('priority') === 'medium' ? 'selected' : ''); ?>>Medium</option>
                <option value="high" <?php echo e(request('priority') === 'high' ? 'selected' : ''); ?>>High</option>
                <option value="critical" <?php echo e(request('priority') === 'critical' ? 'selected' : ''); ?>>Critical</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                Filter
            </button>

            <?php if(request()->hasAny(['type', 'status', 'priority'])): ?>
            <a href="<?php echo e(route('admin.feedback')); ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                Clear
            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Feedback List -->
    <?php if($feedback->isEmpty()): ?>
    <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No feedback found</h3>
        <p class="text-gray-600">Try adjusting your filters</p>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__currentLoopData = $feedback; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-pink-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                <?php echo e(substr($item->user->name, 0, 2)); ?>

                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900"><?php echo e($item->user->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($item->user->email); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-<?php echo e($item->getTypeBadgeColor()); ?>-100 text-<?php echo e($item->getTypeBadgeColor()); ?>-700 text-xs rounded font-medium">
                            <?php echo e($item->getTypeLabel()); ?>

                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-900"><?php echo e($item->title); ?></p>
                        <p class="text-xs text-gray-500 line-clamp-1"><?php echo e(Str::limit($item->description, 60)); ?></p>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-<?php echo e($item->getPriorityBadgeColor()); ?>-100 text-<?php echo e($item->getPriorityBadgeColor()); ?>-700 text-xs rounded font-medium">
                            <?php echo e(ucfirst($item->priority)); ?>

                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-<?php echo e($item->getStatusBadgeColor()); ?>-100 text-<?php echo e($item->getStatusBadgeColor()); ?>-700 text-xs rounded font-medium">
                            <?php echo e($item->getStatusLabel()); ?>

                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo e($item->created_at->format('d M Y')); ?>

                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="<?php echo e(route('admin.feedback.show', $item)); ?>" class="text-red-600 hover:text-red-900">
                            View
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <?php echo e($feedback->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\feedback\index.blade.php ENDPATH**/ ?>