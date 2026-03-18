
<?php $__env->startSection('title', 'Langganan Saya'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6" x-data="{ cancelModal: false }">

    
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Langganan Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola paket dan kuota AI kamu</p>
        </div>
        <a href="<?php echo e(route('pricing')); ?>"
           class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition">
            Upgrade Paket
        </a>
    </div>

    <?php if(session('success')): ?>
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg text-sm text-green-800">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-sm text-red-800">
        <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?>

    <?php if($current && $current->isValid()): ?>
    
    <div class="bg-white rounded-2xl border-2 <?php echo e($current->package->is_featured ? 'border-red-400' : 'border-gray-200'); ?> p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-gray-900"><?php echo e($current->package->name); ?></h2>
                    <?php if($current->isOnTrial()): ?>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">TRIAL</span>
                    <?php else: ?>
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full">AKTIF</span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    <?php if($current->isOnTrial()): ?>
                        Trial berakhir <?php echo e($current->trial_ends_at->format('d M Y')); ?>

                    <?php else: ?>
                        Aktif hingga <?php echo e($current->ends_at->format('d M Y')); ?>

                    <?php endif; ?>
                    · <span class="font-medium text-gray-700"><?php echo e($current->days_remaining); ?> hari lagi</span>
                </p>
            </div>
            <div class="text-right">
                <?php if($current->isOnTrial()): ?>
                    <p class="text-2xl font-bold text-blue-600">Gratis</p>
                    <p class="text-xs text-gray-500">masa trial</p>
                <?php else: ?>
                    <p class="text-2xl font-bold text-gray-900">Rp <?php echo e(number_format($current->package->price, 0, ',', '.')); ?></p>
                    <p class="text-xs text-gray-500">/<?php echo e($current->billing_cycle === 'yearly' ? 'tahun' : 'bulan'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600 font-medium">Kuota AI Generate</span>
                <span class="font-semibold <?php echo e($current->quota_percentage >= 90 ? 'text-red-600' : 'text-gray-700'); ?>">
                    <?php echo e($current->ai_quota_used); ?> / <?php echo e($current->ai_quota_limit); ?> digunakan
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="h-3 rounded-full transition-all
                    <?php echo e($current->quota_percentage >= 90 ? 'bg-red-500' : ($current->quota_percentage >= 70 ? 'bg-yellow-500' : 'bg-green-500')); ?>"
                    style="width: <?php echo e($current->quota_percentage); ?>%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Sisa <?php echo e($current->remaining_quota); ?> generate
                <?php if($current->quota_reset_at): ?>
                    · Reset <?php echo e($current->quota_reset_at->format('d M Y')); ?>

                <?php endif; ?>
            </p>
        </div>

        
        <?php if($current->isOnTrial()): ?>
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600 font-medium">Progres Trial</span>
                <span class="text-gray-700"><?php echo e($current->trial_progress); ?>% terpakai</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="h-2 rounded-full bg-blue-400" style="width: <?php echo e($current->trial_progress); ?>%"></div>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="flex gap-3 mt-4">
            <a href="<?php echo e(route('pricing')); ?>"
               class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-xl transition">
                Upgrade Paket
            </a>
            <?php if($current->isActive()): ?>
            <button @click="cancelModal = true"
                    class="px-4 py-2 border border-gray-300 hover:border-red-400 hover:text-red-600 text-gray-600 text-sm font-medium rounded-xl transition">
                Batalkan Langganan
            </button>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
    
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 mb-6 text-center">
        <div class="text-4xl mb-3">⚡</div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada langganan aktif</h3>
        <p class="text-sm text-gray-600 mb-4">Mulai trial gratis 30 hari atau pilih paket yang sesuai kebutuhanmu.</p>
        <a href="<?php echo e(route('pricing')); ?>"
           class="inline-block px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition">
            Lihat Paket
        </a>
    </div>
    <?php endif; ?>

    
    <?php if($history->count() > 0): ?>
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Riwayat Langganan</h3>
        </div>
        <div class="divide-y divide-gray-100">
            <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900 text-sm"><?php echo e($sub->package->name ?? 'Paket Dihapus'); ?></p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <?php echo e($sub->created_at->format('d M Y')); ?>

                        <?php if($sub->billing_cycle): ?> · <?php echo e($sub->billing_cycle === 'yearly' ? 'Tahunan' : 'Bulanan'); ?> <?php endif; ?>
                    </p>
                </div>
                <div class="text-right">
                    <?php
                        $statusMap = [
                            'active'          => ['bg-green-100 text-green-700', 'Aktif'],
                            'trial'           => ['bg-blue-100 text-blue-700', 'Trial'],
                            'pending_payment' => ['bg-yellow-100 text-yellow-700', 'Menunggu Verifikasi'],
                            'cancelled'       => ['bg-gray-100 text-gray-600', 'Dibatalkan'],
                            'expired'         => ['bg-red-100 text-red-600', 'Kadaluarsa'],
                        ];
                        [$cls, $label] = $statusMap[$sub->status] ?? ['bg-gray-100 text-gray-600', $sub->status];
                    ?>
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full <?php echo e($cls); ?>"><?php echo e($label); ?></span>
                    <?php if($sub->status === 'pending_payment'): ?>
                    <p class="text-xs text-yellow-600 mt-1">Menunggu verifikasi admin</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div x-show="cancelModal" x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Batalkan Langganan?</h3>
            <p class="text-sm text-gray-600 mb-6">
                Akses tetap aktif hingga periode berakhir. Kamu bisa berlangganan kembali kapan saja.
            </p>
            <div class="flex gap-3">
                <form action="<?php echo e(route('subscription.cancel')); ?>" method="POST" class="flex-1">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            class="w-full py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition">
                        Ya, Batalkan
                    </button>
                </form>
                <button @click="cancelModal = false"
                        class="flex-1 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                    Tidak
                </button>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\subscription\index.blade.php ENDPATH**/ ?>