

<?php $__env->startSection('title', 'Guru Monitor - ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Guru Monitor</h1>
        <p class="text-gray-500 text-sm mt-1">Pantau kinerja semua guru dalam sistem ML Training</p>
    </div>

    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Guru</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e($globalStats['total_gurus']); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Entri</p>
            <p class="text-2xl font-bold text-blue-600"><?php echo e(number_format($globalStats['total_entries'])); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Entri Excellent</p>
            <p class="text-2xl font-bold text-green-600"><?php echo e(number_format($globalStats['excellent_entries'])); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Bulan Ini</p>
            <p class="text-2xl font-bold text-purple-600"><?php echo e(number_format($globalStats['entries_this_month'])); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Dibayar</p>
            <p class="text-2xl font-bold text-gray-900">Rp <?php echo e(number_format($globalStats['total_paid_out'], 0, ',', '.')); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Pending Payout</p>
            <p class="text-2xl font-bold text-orange-500">Rp <?php echo e(number_format($globalStats['pending_payout'], 0, ',', '.')); ?></p>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Daftar Guru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Guru</th>
                        <th class="px-4 py-3 text-center">Total Entri</th>
                        <th class="px-4 py-3 text-center">Excellent</th>
                        <th class="px-4 py-3 text-center">Good</th>
                        <th class="px-4 py-3 text-center">Fair</th>
                        <th class="px-4 py-3 text-center">Poor</th>
                        <th class="px-4 py-3 text-center">Quality Score</th>
                        <th class="px-4 py-3 text-right">Total Earnings</th>
                        <th class="px-4 py-3 text-right">Saldo Tersedia</th>
                        <th class="px-4 py-3 text-center">Terakhir Submit</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-900"><?php echo e($guru['name']); ?></p>
                                <p class="text-xs text-gray-400"><?php echo e($guru['email']); ?></p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-800"><?php echo e(number_format($guru['total_entries'])); ?></td>
                        <td class="px-4 py-3 text-center text-green-600 font-medium"><?php echo e($guru['excellent_entries']); ?></td>
                        <td class="px-4 py-3 text-center text-blue-600"><?php echo e($guru['good_entries']); ?></td>
                        <td class="px-4 py-3 text-center text-yellow-600"><?php echo e($guru['fair_entries']); ?></td>
                        <td class="px-4 py-3 text-center text-red-500"><?php echo e($guru['poor_entries']); ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php
                                $score = $guru['quality_score'];
                                $color = $score >= 75 ? 'green' : ($score >= 50 ? 'yellow' : 'red');
                            ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-<?php echo e($color); ?>-100 text-<?php echo e($color); ?>-700">
                                <?php echo e($score); ?>%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-800">Rp <?php echo e(number_format($guru['total_earnings'], 0, ',', '.')); ?></td>
                        <td class="px-4 py-3 text-right text-gray-800">Rp <?php echo e(number_format($guru['available_balance'], 0, ',', '.')); ?></td>
                        <td class="px-4 py-3 text-center text-gray-500 text-xs">
                            <?php echo e($guru['last_submission'] ? \Carbon\Carbon::parse($guru['last_submission'])->diffForHumans() : '-'); ?>

                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="<?php echo e(route('admin.guru-monitor.show', $guru['id'])); ?>" 
                               class="text-xs text-red-600 hover:text-red-800 font-medium">Detail</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-gray-400">Belum ada guru terdaftar.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\guru-monitor\index.blade.php ENDPATH**/ ?>