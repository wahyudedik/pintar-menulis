

<?php $__env->startSection('title', 'Referral Monitor - ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Referral Monitor</h1>
        <p class="text-gray-500 text-sm mt-1">Pantau semua aktivitas referral dan komisi client</p>
    </div>

    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Referral</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total'])); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Menunggu Konversi</p>
            <p class="text-2xl font-bold text-yellow-500"><?php echo e(number_format($stats['pending'])); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Berhasil Konversi</p>
            <p class="text-2xl font-bold text-green-600"><?php echo e(number_format($stats['converted'])); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Komisi Dibagikan</p>
            <p class="text-xl font-bold text-gray-900">Rp <?php echo e(number_format($stats['total_commission'], 0, ',', '.')); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Top Referrer</h3>
            <?php $__empty_1 = true; $__currentLoopData = $stats['top_referrers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $referrer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between py-2 <?php echo e(!$loop->last ? 'border-b border-gray-100' : ''); ?>">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold"><?php echo e($i+1); ?></span>
                    <div>
                        <p class="text-sm font-medium text-gray-800"><?php echo e($referrer->name); ?></p>
                        <p class="text-xs text-gray-400"><?php echo e($referrer->email); ?></p>
                    </div>
                </div>
                <span class="text-sm font-semibold text-green-600">Rp <?php echo e(number_format($referrer->referral_earnings, 0, ',', '.')); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
            <?php endif; ?>
        </div>

        
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Semua Referral</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Referrer</th>
                            <th class="px-4 py-3 text-left">Referred</th>
                            <th class="px-4 py-3 text-center">Tipe</th>
                            <th class="px-4 py-3 text-right">Komisi</th>
                            <th class="px-4 py-3 text-center">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $referrals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $referral): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800"><?php echo e($referral->referrer->name); ?></p>
                                <p class="text-xs text-gray-400"><?php echo e($referral->referrer->email); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-gray-700"><?php echo e($referral->referredUser->name); ?></p>
                                <p class="text-xs text-gray-400"><?php echo e($referral->referredUser->email); ?></p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    <?php echo e($referral->type === 'subscription' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'); ?>">
                                    <?php echo e($referral->type === 'signup' ? 'Daftar' : 'Langganan'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium <?php echo e($referral->status === 'converted' ? 'text-green-600' : 'text-gray-400'); ?>">
                                Rp <?php echo e(number_format($referral->commission_amount, 0, ',', '.')); ?>

                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-500">
                                <?php echo e($referral->created_at->format('d M Y')); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada data referral.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($referrals->hasPages()): ?>
            <div class="px-5 py-4 border-t border-gray-100">
                <?php echo e($referrals->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\referrals\index.blade.php ENDPATH**/ ?>