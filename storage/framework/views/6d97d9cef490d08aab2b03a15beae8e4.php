
<?php $__env->startSection('title', 'Pilih Paket'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6" x-data="{ billing: 'monthly' }">

    
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Pilih Paket yang Tepat</h1>
        <p class="text-gray-500 mt-2">Mulai gratis 30 hari, tidak perlu kartu kredit</p>

        
        <div class="inline-flex items-center mt-6 bg-gray-100 rounded-full p-1">
            <button @click="billing='monthly'"
                    :class="billing==='monthly' ? 'bg-white shadow text-gray-900' : 'text-gray-500'"
                    class="px-5 py-2 rounded-full text-sm font-medium transition">Bulanan</button>
            <button @click="billing='yearly'"
                    :class="billing==='yearly' ? 'bg-white shadow text-gray-900' : 'text-gray-500'"
                    class="px-5 py-2 rounded-full text-sm font-medium transition">
                Tahunan
                <span class="ml-1 px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Hemat 2 bulan</span>
            </button>
        </div>
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

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl mx-auto">
        <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $isCurrent = $current && $current->package_id === $package->id && $current->isValid();
            $monthlyPrice = $package->price;
            $yearlyPrice  = $package->yearly_price ?? $package->price * 10;
            $yearlyPerMonth = $package->yearly_price ? round($package->yearly_price / 12) : round($package->price * 10 / 12);
            $savings = ($package->price * 12) - ($package->yearly_price ?? $package->price * 10);
        ?>
        <div class="relative bg-white rounded-2xl border-2 flex flex-col
            <?php echo e($package->is_featured ? 'border-red-500 shadow-xl sm:scale-105' : 'border-gray-200'); ?>">

            
            <?php if($package->badge_text): ?>
            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                <span class="px-4 py-1 text-xs font-bold text-white rounded-full
                    <?php echo e($package->badge_color === 'red' ? 'bg-red-500' :
                       ($package->badge_color === 'green' ? 'bg-green-500' :
                       ($package->badge_color === 'purple' ? 'bg-purple-500' : 'bg-blue-500'))); ?>">
                    <?php echo e($package->badge_text); ?>

                </span>
            </div>
            <?php endif; ?>

            <div class="p-6 flex flex-col flex-1">
                
                <h3 class="text-xl font-bold text-gray-900"><?php echo e($package->name); ?></h3>
                <p class="text-sm text-gray-500 mt-1 mb-4"><?php echo e($package->description); ?></p>

                
                <div class="mb-5">
                    <div x-show="billing==='monthly'">
                        <?php if($package->price == 0): ?>
                            <span class="text-4xl font-extrabold text-gray-900">Gratis</span>
                        <?php else: ?>
                            <span class="text-4xl font-extrabold text-gray-900">Rp <?php echo e(number_format($monthlyPrice, 0, ',', '.')); ?></span>
                            <span class="text-gray-500 text-sm">/bulan</span>
                        <?php endif; ?>
                    </div>
                    <div x-show="billing==='yearly'" x-cloak>
                        <?php if($package->price == 0): ?>
                            <span class="text-4xl font-extrabold text-gray-900">Gratis</span>
                        <?php else: ?>
                            <span class="text-4xl font-extrabold text-gray-900">Rp <?php echo e(number_format($yearlyPerMonth, 0, ',', '.')); ?></span>
                            <span class="text-gray-500 text-sm">/bulan</span>
                            <div class="text-xs text-green-600 font-medium mt-1">
                                Rp <?php echo e(number_format($yearlyPrice, 0, ',', '.')); ?>/tahun · Hemat Rp <?php echo e(number_format($savings, 0, ',', '.')); ?>

                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if($package->has_trial && $package->trial_days > 0): ?>
                    <div class="mt-2 text-xs text-blue-600 font-medium">
                        ✨ Coba gratis <?php echo e($package->trial_days); ?> hari
                    </div>
                    <?php endif; ?>
                </div>

                
                <ul class="space-y-2 mb-3 flex-1">
                    <?php
                        $features = $package->features;
                        if (is_string($features)) {
                            $features = json_decode($features, true) ?? [];
                        }
                        $features = is_array($features) ? $features : [];
                    ?>
                    <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-start gap-2 text-sm text-gray-700">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <?php echo e($feature); ?>

                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>

                
                <?php $aiCount = count($package->getAllowedFeaturesList()); ?>
                <?php if($aiCount > 0): ?>
                <div class="mb-4 px-3 py-2 bg-blue-50 rounded-lg text-xs text-blue-700 font-medium">
                    🤖 <?php echo e($aiCount); ?> fitur AI tersedia
                </div>
                <?php endif; ?>

                
                <?php if($isCurrent): ?>
                    <div class="w-full text-center py-3 rounded-xl bg-gray-100 text-gray-600 text-sm font-medium">
                        ✅ Paket Aktif
                    </div>
                <?php elseif(!Auth::check()): ?>
                    <a href="<?php echo e(route('login')); ?>"
                       class="w-full text-center py-3 rounded-xl text-sm font-semibold transition
                           <?php echo e($package->is_featured ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-gray-900 hover:bg-gray-800 text-white'); ?>">
                        Mulai Sekarang
                    </a>
                <?php elseif($package->price == 0): ?>
                    
                    <?php if(!auth()->user()->hasUsedTrial()): ?>
                    <form action="<?php echo e(route('subscription.trial', $package)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                class="w-full py-3 rounded-xl text-sm font-semibold transition bg-gray-900 hover:bg-gray-800 text-white">
                            Mulai Gratis
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="w-full text-center py-3 rounded-xl bg-gray-100 text-gray-500 text-sm font-medium">
                        Trial sudah digunakan
                    </div>
                    <?php endif; ?>
                <?php elseif($package->has_trial && $package->trial_days > 0 && !auth()->user()->hasUsedTrial()): ?>
                    
                    <form action="<?php echo e(route('subscription.trial', $package)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                class="w-full py-3 rounded-xl text-sm font-semibold transition
                                    <?php echo e($package->is_featured ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-gray-900 hover:bg-gray-800 text-white'); ?>">
                            Coba Gratis <?php echo e($package->trial_days); ?> Hari
                        </button>
                    </form>
                <?php else: ?>
                    
                    <div x-data>
                        <a :href="billing==='yearly'
                                ? '<?php echo e(route('subscription.checkout', $package)); ?>?billing=yearly'
                                : '<?php echo e(route('subscription.checkout', $package)); ?>'"
                           class="block w-full text-center py-3 rounded-xl text-sm font-semibold transition
                               <?php echo e($package->is_featured ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-gray-900 hover:bg-gray-800 text-white'); ?>">
                            Berlangganan
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="max-w-2xl mx-auto mt-16" x-data="{ open: null }">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Pertanyaan Umum</h2>
        <?php $__currentLoopData = [
            ['q' => 'Apakah trial benar-benar gratis?', 'a' => 'Ya, 100% gratis selama 30 hari. Tidak perlu kartu kredit atau data pembayaran apapun.'],
            ['q' => 'Bagaimana cara bayar?', 'a' => 'Kami menerima transfer bank (BCA, Mandiri, BNI, BRI) dan dompet digital (GoPay, OVO, DANA, QRIS). Pembayaran diverifikasi manual dalam 1x24 jam.'],
            ['q' => 'Bisa ganti paket kapan saja?', 'a' => 'Bisa. Upgrade atau downgrade bisa dilakukan kapan saja. Sisa kuota akan disesuaikan.'],
            ['q' => 'Apa yang terjadi setelah trial habis?', 'a' => 'Akses AI akan dibatasi. Anda bisa upgrade ke paket berbayar untuk melanjutkan.'],
            ['q' => 'Apakah ada refund?', 'a' => 'Ada garansi uang kembali 7 hari jika tidak puas. Hubungi support kami.'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="border-b border-gray-200">
            <button @click="open = open === <?php echo e($i); ?> ? null : <?php echo e($i); ?>"
                    class="w-full flex justify-between items-center py-4 text-left text-sm font-medium text-gray-900">
                <?php echo e($faq['q']); ?>

                <svg class="w-5 h-5 text-gray-400 transition" :class="open === <?php echo e($i); ?> ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open === <?php echo e($i); ?>" x-cloak class="pb-4 text-sm text-gray-600">
                <?php echo e($faq['a']); ?>

            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\subscription\pricing.blade.php ENDPATH**/ ?>