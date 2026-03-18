

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="<?php echo e(route('guru.earnings')); ?>" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Penghasilan</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Riwayat Withdrawal</h1>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200">
        <div class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $withdrawals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-5 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Rp <?php echo e(number_format($w->amount, 0, ',', '.')); ?></p>
                        <p class="text-xs text-gray-500 mt-0.5"><?php echo e($w->bank_name); ?> — <?php echo e($w->account_number); ?></p>
                        <p class="text-xs text-gray-400"><?php echo e($w->created_at->format('d M Y, H:i')); ?></p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium
                        <?php echo e($w->status === 'completed' ? 'bg-green-100 text-green-700' :
                           ($w->status === 'approved' ? 'bg-blue-100 text-blue-700' :
                           ($w->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'))); ?>">
                        <?php echo e(ucfirst($w->status)); ?>

                    </span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-5 py-10 text-center text-gray-400 text-sm">
                    Belum ada riwayat withdrawal.
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\guru\withdrawal-history.blade.php ENDPATH**/ ?>