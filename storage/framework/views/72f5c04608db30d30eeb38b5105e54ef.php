

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Selamat datang, <?php echo e(auth()->user()->name); ?></p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Available Orders</p>
                    <p class="text-2xl font-semibold text-green-600"><?php echo e($stats['available_orders']); ?></p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">My Orders</p>
                    <p class="text-2xl font-semibold text-blue-600"><?php echo e($stats['my_orders']); ?></p>
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
                    <p class="text-xs text-gray-500 mb-1">Completed</p>
                    <p class="text-2xl font-semibold text-purple-600"><?php echo e($stats['completed']); ?></p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Earnings</p>
                    <p class="text-lg font-semibold text-gray-900">Rp <?php echo e(number_format($stats['total_earnings'], 0, ',', '.')); ?></p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <a href="<?php echo e(route('operator.queue')); ?>" class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white hover:from-green-600 hover:to-green-700 transition">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold mb-1">Order Queue</h3>
                    <p class="text-sm text-green-100"><?php echo e($stats['available_orders']); ?> orders tersedia</p>
                </div>
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </div>
        </a>

        <a href="<?php echo e(route('operator.earnings')); ?>" class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white hover:from-blue-600 hover:to-blue-700 transition">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold mb-1">Penghasilan</h3>
                    <p class="text-sm text-blue-100">Lihat detail earnings</p>
                </div>
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </div>
        </a>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Order Saya</h2>
        </div>
        <?php if($myOrders->count() > 0): ?>
        <div class="divide-y divide-gray-200">
            <?php $__currentLoopData = $myOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('operator.workspace', $order)); ?>" class="block p-4 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900">#<?php echo e($order->id); ?></span>
                            <span class="px-2 py-1 text-xs rounded-full
                                <?php if($order->status === 'accepted'): ?> bg-blue-100 text-blue-700
                                <?php elseif($order->status === 'in_progress'): ?> bg-purple-100 text-purple-700
                                <?php elseif($order->status === 'completed'): ?> bg-green-100 text-green-700
                                <?php endif; ?>">
                                <?php echo e(ucfirst($order->status)); ?>

                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1"><?php echo e($order->category); ?></p>
                        <p class="text-xs text-gray-400 mt-1">Deadline: <?php echo e($order->deadline->format('d M Y')); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">Rp <?php echo e(number_format($order->budget, 0, ',', '.')); ?></p>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <div class="p-8 text-center">
            <p class="text-sm text-gray-500 mb-3">Belum ada order aktif</p>
            <a href="<?php echo e(route('operator.queue')); ?>" class="text-sm text-green-600 hover:text-green-700">
                Lihat Order Queue
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Banner Popup -->
<?php if (isset($component)) { $__componentOriginal411849fa56991479085a8247fc6f96f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal411849fa56991479085a8247fc6f96f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.banner-popup','data' => ['type' => 'operator']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banner-popup'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'operator']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal411849fa56991479085a8247fc6f96f5)): ?>
<?php $attributes = $__attributesOriginal411849fa56991479085a8247fc6f96f5; ?>
<?php unset($__attributesOriginal411849fa56991479085a8247fc6f96f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal411849fa56991479085a8247fc6f96f5)): ?>
<?php $component = $__componentOriginal411849fa56991479085a8247fc6f96f5; ?>
<?php unset($__componentOriginal411849fa56991479085a8247fc6f96f5); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.operator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\dashboard\operator.blade.php ENDPATH**/ ?>