

<?php $__env->startSection('title', 'Referral & Komisi - ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Referral & Komisi</h1>
        <p class="text-gray-500 text-sm mt-1">Ajak teman bergabung — dapat Rp 500 saat daftar, Rp 1.000 saat pertama berlangganan</p>
    </div>

    <?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 text-white mb-6">
        <p class="text-blue-100 text-sm mb-2">Kode Referral Kamu</p>
        <div class="flex items-center gap-3">
            <span id="refCode" class="text-3xl font-bold tracking-widest"><?php echo e($stats['referral_code']); ?></span>
            <button onclick="copyCode()" class="bg-white/20 hover:bg-white/30 text-white text-xs px-3 py-1.5 rounded-lg transition">
                Salin
            </button>
        </div>
        <p class="text-blue-100 text-xs mt-3">Link referral:</p>
        <div class="flex items-center gap-2 mt-1">
            <span id="refLink" class="text-sm text-blue-100 truncate"><?php echo e(url('/register?ref=' . $stats['referral_code'])); ?></span>
            <button onclick="copyLink()" class="shrink-0 bg-white/20 hover:bg-white/30 text-white text-xs px-3 py-1.5 rounded-lg transition">
                Salin Link
            </button>
        </div>
    </div>

    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Referral</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total_referrals']); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Komisi Daftar</p>
            <p class="text-2xl font-bold text-blue-600"><?php echo e($stats['signup_commissions']); ?></p>
            <p class="text-xs text-gray-400">× Rp 500</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Komisi Langganan</p>
            <p class="text-2xl font-bold text-green-600"><?php echo e($stats['subscription_commissions']); ?></p>
            <p class="text-xs text-gray-400">× Rp 1.000</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Komisi</p>
            <p class="text-xl font-bold text-gray-900">Rp <?php echo e(number_format($stats['total_earnings'], 0, ',', '.')); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Saldo Tersedia</p>
            <p class="text-2xl font-bold text-blue-600">Rp <?php echo e(number_format($stats['available_balance'], 0, ',', '.')); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Pending Withdrawal</p>
            <p class="text-2xl font-bold text-orange-500">Rp <?php echo e(number_format($stats['pending_withdrawal'], 0, ',', '.')); ?></p>
        </div>
    </div>

    
    <?php if($stats['available_balance'] >= 10000): ?>
    <div class="mb-6">
        <a href="<?php echo e(route('client.referral.withdraw')); ?>" 
           class="inline-flex items-center gap-2 bg-green-600 text-white px-5 py-2.5 rounded-lg hover:bg-green-700 transition font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Tarik Komisi
        </a>
    </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">Cara Kerja Referral</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold shrink-0">1</div>
                <div>
                    <p class="font-medium text-gray-800 text-sm">Bagikan Kode</p>
                    <p class="text-xs text-gray-500 mt-0.5">Bagikan kode atau link referral kamu ke teman</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold shrink-0">2</div>
                <div>
                    <p class="font-medium text-gray-800 text-sm">Teman Daftar → Rp 500</p>
                    <p class="text-xs text-gray-500 mt-0.5">Kamu langsung dapat Rp 500 saat teman berhasil daftar</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold shrink-0">3</div>
                <div>
                    <p class="font-medium text-gray-800 text-sm">Teman Berlangganan → Rp 1.000</p>
                    <p class="text-xs text-gray-500 mt-0.5">Dapat tambahan Rp 1.000 saat teman pertama kali berlangganan berbayar (sekali saja)</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Riwayat Komisi</h3>
        </div>
        <div class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $stats['referrals']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $referral): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-800"><?php echo e($referral->referredUser->name); ?></p>
                    <p class="text-xs text-gray-400">
                        <?php echo e($referral->type === 'signup' ? 'Daftar' : 'Berlangganan'); ?> ·
                        <?php echo e($referral->created_at->format('d M Y')); ?>

                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-green-600">+Rp <?php echo e(number_format($referral->commission_amount, 0, ',', '.')); ?></span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        <?php echo e($referral->type === 'signup' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'); ?>">
                        <?php echo e($referral->type === 'signup' ? 'Daftar' : 'Langganan'); ?>

                    </span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada komisi. Mulai bagikan kode kamu!</div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if($withdrawalHistory->count() > 0): ?>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Riwayat Penarikan</h3>
        </div>
        <div class="divide-y divide-gray-100">
            <?php $__currentLoopData = $withdrawalHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-800">Rp <?php echo e(number_format($wd->amount, 0, ',', '.')); ?></p>
                    <p class="text-xs text-gray-400"><?php echo e($wd->bank_name); ?> · <?php echo e($wd->created_at->format('d M Y')); ?></p>
                </div>
                <?php $wdColors = ['pending'=>'yellow','processing'=>'blue','completed'=>'green','rejected'=>'red']; ?>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-<?php echo e($wdColors[$wd->status] ?? 'gray'); ?>-100 text-<?php echo e($wdColors[$wd->status] ?? 'gray'); ?>-700">
                    <?php echo e(ucfirst($wd->status)); ?>

                </span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function copyCode() {
    navigator.clipboard.writeText('<?php echo e($stats['referral_code']); ?>');
    alert('Kode referral disalin!');
}
function copyLink() {
    navigator.clipboard.writeText('<?php echo e(url('/register?ref=' . $stats['referral_code'])); ?>');
    alert('Link referral disalin!');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\referral\index.blade.php ENDPATH**/ ?>