

<?php $__env->startSection('title', 'My Orders'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">My Orders</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola semua order Anda</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Total Orders</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo e($stats['total']); ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Pending</p>
                    <p class="text-2xl font-semibold text-yellow-600"><?php echo e($stats['pending']); ?></p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">In Progress</p>
                    <p class="text-2xl font-semibold text-blue-600"><?php echo e($stats['in_progress']); ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Completed</p>
                    <p class="text-2xl font-semibold text-green-600"><?php echo e($stats['completed']); ?></p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Order History</h2>
        </div>
        <div class="p-4">
            <?php if($orders->count() > 0): ?>
            <div class="space-y-4">
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-base font-semibold text-gray-900"><?php echo e($order->category); ?></h3>
                                <span class="px-2 py-1 rounded-full text-xs
                                    <?php if($order->status === 'pending'): ?> bg-yellow-100 text-yellow-700
                                    <?php elseif($order->status === 'accepted' || $order->status === 'in_progress'): ?> bg-blue-100 text-blue-700
                                    <?php elseif($order->status === 'completed'): ?> bg-green-100 text-green-700
                                    <?php elseif($order->status === 'revision'): ?> bg-orange-100 text-orange-700
                                    <?php endif; ?>">
                                    <?php echo e(ucfirst($order->status)); ?>

                                </span>
                            </div>
                            
                            <?php if($order->operator): ?>
                            <p class="text-sm text-gray-600 mb-1">
                                Operator: <span class="font-medium"><?php echo e($order->operator->name); ?></span>
                            </p>
                            <?php else: ?>
                            <p class="text-sm text-gray-500 mb-1">Menunggu operator...</p>
                            <?php endif; ?>

                            <p class="text-xs text-gray-500">
                                #<?php echo e($order->id); ?> • <?php echo e($order->created_at->format('d M Y')); ?>

                            </p>
                        </div>

                        <div class="text-right">
                            <p class="text-xs text-gray-500">Budget</p>
                            <p class="text-lg font-semibold text-green-600">Rp <?php echo e(number_format($order->budget, 0, ',', '.')); ?></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded p-3 mb-3">
                        <p class="text-sm text-gray-700"><?php echo e(Str::limit($order->brief ?? 'No description', 150)); ?></p>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-xs text-gray-600">
                            <?php if($order->deadline): ?>
                            <span class="font-medium">Deadline:</span> <?php echo e($order->deadline->format('d M Y')); ?>

                            <?php endif; ?>
                            <?php if($order->status === 'completed' && $order->completed_at): ?>
                            <span class="ml-3 font-medium">Selesai:</span> <?php echo e($order->completed_at->format('d M Y')); ?>

                            <?php endif; ?>
                        </div>

                        <a href="<?php echo e(route('orders.show', $order)); ?>" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <p class="text-gray-600 mb-4">Anda belum memiliki order</p>
                <a href="<?php echo e(route('browse.operators')); ?>" class="text-blue-600 hover:text-blue-700 text-sm">
                    Browse Operators →
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\orders.blade.php ENDPATH**/ ?>