

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Subscription</h1>
        <p class="text-sm text-gray-600 mt-1">Verifikasi pembayaran dan kelola langganan user</p>
    </div>

    <?php if(session('success')): ?>
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg text-sm text-green-800">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?php
            $stats = [
                ['label' => 'Total Aktif', 'value' => \App\Models\UserSubscription::where('status', 'active')->count(), 'color' => 'green'],
                ['label' => 'Trial', 'value' => \App\Models\UserSubscription::where('status', 'trial')->count(), 'color' => 'blue'],
                ['label' => 'Pending Verifikasi', 'value' => \App\Models\UserSubscription::where('status', 'pending_payment')->count(), 'color' => 'yellow'],
                ['label' => 'Total', 'value' => \App\Models\UserSubscription::count(), 'color' => 'gray'],
            ];
        ?>
        <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-2xl font-bold text-gray-900"><?php echo e($stat['value']); ?></p>
            <p class="text-xs text-gray-500 mt-1"><?php echo e($stat['label']); ?></p>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="flex gap-2 mb-4 flex-wrap">
        <?php $__currentLoopData = ['all' => 'Semua', 'pending_payment' => 'Pending', 'active' => 'Aktif', 'trial' => 'Trial', 'cancelled' => 'Dibatalkan', 'expired' => 'Kadaluarsa']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(request()->fullUrlWithQuery(['status' => $val])); ?>"
           class="px-3 py-1.5 text-xs font-medium rounded-lg transition
               <?php echo e(request('status', 'all') === $val ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?>">
            <?php echo e($label); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Paket</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kuota</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pembayaran</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900"><?php echo e($sub->user->name); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($sub->user->email); ?></p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900"><?php echo e($sub->package->name ?? '-'); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($sub->billing_cycle === 'yearly' ? 'Tahunan' : 'Bulanan'); ?></p>
                        </td>
                        <td class="px-4 py-3">
                            <?php
                                $statusMap = [
                                    'active'          => 'bg-green-100 text-green-700',
                                    'trial'           => 'bg-blue-100 text-blue-700',
                                    'pending_payment' => 'bg-yellow-100 text-yellow-700',
                                    'cancelled'       => 'bg-gray-100 text-gray-600',
                                    'expired'         => 'bg-red-100 text-red-600',
                                ];
                                $statusLabel = [
                                    'active'          => 'Aktif',
                                    'trial'           => 'Trial',
                                    'pending_payment' => 'Pending',
                                    'cancelled'       => 'Dibatalkan',
                                    'expired'         => 'Kadaluarsa',
                                ];
                            ?>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full <?php echo e($statusMap[$sub->status] ?? 'bg-gray-100 text-gray-600'); ?>">
                                <?php echo e($statusLabel[$sub->status] ?? $sub->status); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            <?php if($sub->starts_at): ?>
                                <?php echo e($sub->starts_at->format('d M Y')); ?> –<br><?php echo e($sub->ends_at?->format('d M Y') ?? '-'); ?>

                            <?php elseif($sub->trial_starts_at): ?>
                                Trial: <?php echo e($sub->trial_starts_at->format('d M Y')); ?> –<br><?php echo e($sub->trial_ends_at?->format('d M Y') ?? '-'); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            <?php echo e($sub->ai_quota_used); ?> / <?php echo e($sub->ai_quota_limit); ?>

                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            <?php if($sub->payment_method): ?>
                                <p><?php echo e(str_replace('_', ' ', ucfirst($sub->payment_method))); ?></p>
                                <?php if($sub->payment_reference): ?>
                                    <a href="<?php echo e(asset('storage/' . $sub->payment_reference)); ?>" target="_blank"
                                       class="text-blue-600 hover:underline">Lihat Bukti</a>
                                <?php endif; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if($sub->status === 'pending_payment'): ?>
                            <div class="flex gap-2">
                                <form action="<?php echo e(route('admin.subscriptions.verify', $sub)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition">
                                        Verifikasi
                                    </button>
                                </form>
                                <form action="<?php echo e(route('admin.subscriptions.reject', $sub)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition"
                                            onclick="return confirm('Tolak pembayaran ini?')">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                                <span class="text-xs text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 text-sm">
                            Tidak ada data subscription
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($subscriptions instanceof \Illuminate\Pagination\LengthAwarePaginator): ?>
        <div class="px-4 py-3 border-t border-gray-100">
            <?php echo e($subscriptions->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\subscriptions.blade.php ENDPATH**/ ?>