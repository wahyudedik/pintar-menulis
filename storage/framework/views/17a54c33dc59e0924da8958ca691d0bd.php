

<?php $__env->startSection('title', 'Detail Guru: ' . $guru->name . ' - ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    
    <div class="flex items-center gap-4 mb-6">
        <a href="<?php echo e(route('admin.guru-monitor.index')); ?>" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?php echo e($guru->name); ?></h1>
            <p class="text-gray-500 text-sm"><?php echo e($guru->email); ?></p>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Entri</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total_entries'])); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Excellent</p>
            <p class="text-2xl font-bold text-green-600"><?php echo e(number_format($stats['excellent_entries'])); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Earnings</p>
            <p class="text-xl font-bold text-gray-900">Rp <?php echo e(number_format($stats['total_earnings'], 0, ',', '.')); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Saldo Tersedia</p>
            <p class="text-xl font-bold text-blue-600">Rp <?php echo e(number_format($stats['available_balance'], 0, ',', '.')); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Pending WD</p>
            <p class="text-xl font-bold text-orange-500">Rp <?php echo e(number_format($stats['pending_withdrawal'], 0, ',', '.')); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Dibayar</p>
            <p class="text-xl font-bold text-gray-700">Rp <?php echo e(number_format($stats['total_paid_out'], 0, ',', '.')); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Distribusi Kualitas</h3>
            <?php $__currentLoopData = ['excellent' => ['label' => 'Excellent', 'color' => 'green'], 'good' => ['label' => 'Good', 'color' => 'blue'], 'fair' => ['label' => 'Fair', 'color' => 'yellow'], 'poor' => ['label' => 'Poor', 'color' => 'red']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $count = $qualityDist[$rating] ?? 0; $pct = $stats['total_entries'] > 0 ? round($count / $stats['total_entries'] * 100) : 0; ?>
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600"><?php echo e($meta['label']); ?></span>
                    <span class="font-medium text-gray-800"><?php echo e($count); ?> (<?php echo e($pct); ?>%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-<?php echo e($meta['color']); ?>-500 h-2 rounded-full" style="width: <?php echo e($pct); ?>%"></div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Koreksi Earnings</h3>
            <form action="<?php echo e(route('admin.guru-monitor.adjust-earnings', $guru)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Jumlah (positif = tambah, negatif = kurang)</label>
                    <input type="number" name="amount" step="0.01" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                           placeholder="Contoh: 50 atau -25">
                </div>
                <div class="mb-4">
                    <label class="block text-xs text-gray-500 mb-1">Alasan</label>
                    <input type="text" name="reason" required maxlength="255"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                           placeholder="Alasan koreksi...">
                </div>
                <button type="submit" class="w-full bg-red-600 text-white text-sm font-medium py-2 rounded-lg hover:bg-red-700 transition">
                    Simpan Koreksi
                </button>
            </form>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Aktivitas 30 Hari Terakhir</h3>
            <?php if($activityChart->isEmpty()): ?>
                <p class="text-sm text-gray-400 text-center py-6">Tidak ada aktivitas dalam 30 hari terakhir.</p>
            <?php else: ?>
                <div class="space-y-1 max-h-48 overflow-y-auto">
                    <?php $__currentLoopData = $activityChart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex justify-between text-xs text-gray-600">
                        <span><?php echo e(\Carbon\Carbon::parse($date)->format('d M')); ?></span>
                        <span class="font-medium"><?php echo e($count); ?> entri</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Riwayat Entri Training</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-left">Platform</th>
                        <th class="px-4 py-3 text-center">Rating</th>
                        <th class="px-4 py-3 text-left">Input (preview)</th>
                        <th class="px-4 py-3 text-center">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700"><?php echo e($entry->category ?? '-'); ?></td>
                        <td class="px-4 py-3 text-gray-600"><?php echo e($entry->platform ?? '-'); ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php
                                $ratingColors = ['excellent' => 'green', 'good' => 'blue', 'fair' => 'yellow', 'poor' => 'red'];
                                $rc = $ratingColors[$entry->quality_rating] ?? 'gray';
                            ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-<?php echo e($rc); ?>-100 text-<?php echo e($rc); ?>-700">
                                <?php echo e(ucfirst($entry->quality_rating ?? '-')); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate"><?php echo e(Str::limit($entry->input_text, 80)); ?></td>
                        <td class="px-4 py-3 text-center text-gray-500 text-xs"><?php echo e($entry->created_at->format('d M Y')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada entri training.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($entries->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-100">
            <?php echo e($entries->links()); ?>

        </div>
        <?php endif; ?>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Riwayat Penarikan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-right">Jumlah</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $withdrawals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $wdColors = ['pending' => 'yellow', 'processing' => 'blue', 'completed' => 'green', 'rejected' => 'red'];
                        $wc = $wdColors[$wd->status] ?? 'gray';
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600"><?php echo e($wd->created_at->format('d M Y')); ?></td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800">Rp <?php echo e(number_format($wd->amount, 0, ',', '.')); ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-<?php echo e($wc); ?>-100 text-<?php echo e($wc); ?>-700">
                                <?php echo e(ucfirst($wd->status)); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs"><?php echo e($wd->notes ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada riwayat penarikan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\guru-monitor\show.blade.php ENDPATH**/ ?>