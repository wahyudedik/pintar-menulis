

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-4 py-8">

    
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Penghasilan Guru</h1>
            <p class="text-sm text-gray-500 mt-1">Rp <?php echo e(number_format($stats['earnings_per_entry'], 0, ',', '.')); ?> per isian training data</p>
        </div>
        <?php if($stats['available_balance'] >= 10000): ?>
            <a href="<?php echo e(route('guru.withdrawal.create')); ?>"
               class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition text-sm">
                Tarik Saldo
            </a>
        <?php endif; ?>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Total Isian</p>
            <p class="text-2xl font-semibold text-gray-900"><?php echo e(number_format($stats['total_entries'])); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Total Penghasilan</p>
            <p class="text-2xl font-semibold text-blue-600">Rp <?php echo e(number_format($stats['total_earnings'], 0, ',', '.')); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Pending Withdrawal</p>
            <p class="text-2xl font-semibold text-yellow-600">Rp <?php echo e(number_format($stats['pending_withdrawal'], 0, ',', '.')); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Saldo Tersedia</p>
            <p class="text-2xl font-semibold text-green-600">Rp <?php echo e(number_format($stats['available_balance'], 0, ',', '.')); ?></p>
        </div>
    </div>

    
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-5 mb-8 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-white mb-1">Tarik Penghasilan</h3>
            <p class="text-purple-100 text-sm">Saldo tersedia: Rp <?php echo e(number_format($stats['available_balance'], 0, ',', '.')); ?></p>
        </div>
        <div class="flex space-x-2">
            <a href="<?php echo e(route('guru.withdrawal.history')); ?>"
               class="px-4 py-2 bg-white/20 text-white rounded-lg font-medium hover:bg-white/30 transition text-sm">
                Riwayat
            </a>
            <?php if($stats['available_balance'] >= 10000): ?>
                <a href="<?php echo e(route('guru.withdrawal.create')); ?>"
                   class="px-4 py-2 bg-white text-purple-600 rounded-lg font-medium hover:bg-purple-50 transition text-sm">
                    Tarik Saldo
                </a>
            <?php else: ?>
                <span class="px-4 py-2 bg-white/10 text-white/60 rounded-lg text-sm cursor-not-allowed">
                    Min. Rp 10.000
                </span>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Riwayat Isian Training</h2>
        </div>
        <div class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $myEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-5 py-4 flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900 truncate"><?php echo e(Str::limit($entry->input_prompt, 80)); ?></p>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs text-gray-400"><?php echo e($entry->created_at->format('d M Y, H:i')); ?></span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                <?php echo e($entry->quality_rating === 'excellent' ? 'bg-green-100 text-green-700' :
                                   ($entry->quality_rating === 'good' ? 'bg-blue-100 text-blue-700' :
                                   ($entry->quality_rating === 'fair' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'))); ?>">
                                <?php echo e(ucfirst($entry->quality_rating)); ?>

                            </span>
                        </div>
                    </div>
                    <div class="ml-4 text-right">
                        <span class="text-sm font-semibold text-green-600">
                            +Rp <?php echo e(number_format($stats['earnings_per_entry'], 0, ',', '.')); ?>

                        </span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-5 py-10 text-center text-gray-400 text-sm">
                    Belum ada isian training data. Mulai review order untuk mendapat penghasilan!
                </div>
            <?php endif; ?>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            <?php echo e($myEntries->links()); ?>

        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\guru\earnings.blade.php ENDPATH**/ ?>