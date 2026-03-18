

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard Admin</h1>
        <p class="text-sm text-gray-500 mt-1">Platform overview & statistics</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Total Clients</p>
                    <p class="text-2xl font-semibold text-blue-600"><?php echo e($stats['total_clients']); ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Total Operators</p>
                    <p class="text-2xl font-semibold text-green-600"><?php echo e($stats['total_operators']); ?></p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Active Orders</p>
                    <p class="text-2xl font-semibold text-purple-600"><?php echo e($stats['active_orders']); ?></p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Total Revenue</p>
                    <p class="text-lg font-semibold text-gray-900">Rp <?php echo e(number_format($stats['total_revenue'], 0, ',', '.')); ?></p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Pending Requests</p>
            <p class="text-2xl font-semibold text-yellow-600"><?php echo e($stats['pending_requests']); ?></p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Completed Today</p>
            <p class="text-2xl font-semibold text-green-600"><?php echo e($stats['completed_today']); ?></p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Avg Rating</p>
            <p class="text-2xl font-semibold text-gray-900"><?php echo e(number_format($topOperators->avg('average_rating') ?? 0, 1)); ?></p>
        </div>
    </div>

    <!-- Top Operators -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Top Operators</h2>
        </div>
        <?php if($topOperators->count() > 0): ?>
        <div class="divide-y divide-gray-200">
            <?php $__currentLoopData = $topOperators->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $operator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            <?php echo e($index + 1); ?>

                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?php echo e($operator->name); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($operator->email); ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900"><?php echo e($operator->completed_count); ?> orders</p>
                        <p class="text-xs text-gray-500"><?php echo e(number_format($operator->average_rating ?? 0, 1)); ?> rating</p>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <div class="p-8 text-center">
            <p class="text-sm text-gray-500">Belum ada data operator</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
        </div>
        <div class="divide-y divide-gray-200">
            <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900">#<?php echo e($order->id); ?></span>
                            <span class="px-2 py-1 text-xs rounded-full
                                <?php if($order->status === 'active'): ?> bg-green-100 text-green-700
                                <?php elseif($order->status === 'expired'): ?> bg-red-100 text-red-700
                                <?php else: ?> bg-gray-100 text-gray-700
                                <?php endif; ?>">
                                <?php echo e(ucfirst($order->status)); ?>

                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            <?php echo e($order->user->name); ?> - <?php echo e($order->package ? $order->package->name : 'Marketplace Order'); ?>

                        </p>
                        <?php if($order->start_date && $order->end_date): ?>
                        <p class="text-xs text-gray-400 mt-1"><?php echo e($order->start_date->format('d M')); ?> - <?php echo e($order->end_date->format('d M Y')); ?></p>
                        <?php else: ?>
                        <p class="text-xs text-gray-400 mt-1"><?php echo e($order->created_at->format('d M Y')); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="text-right">
                        <?php if($order->package): ?>
                        <p class="text-sm font-semibold text-gray-900">Rp <?php echo e(number_format($order->package->price, 0, ',', '.')); ?></p>
                        <?php elseif($order->budget): ?>
                        <p class="text-sm font-semibold text-gray-900">Rp <?php echo e(number_format($order->budget, 0, ',', '.')); ?></p>
                        <?php else: ?>
                        <p class="text-sm font-semibold text-gray-900">-</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\dashboard\admin.blade.php ENDPATH**/ ?>