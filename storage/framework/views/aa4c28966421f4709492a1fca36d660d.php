

<?php $__env->startSection('title', 'Content Gaps - ' . $competitor->username); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
        <a href="<?php echo e(route('competitor-analysis.index')); ?>" class="hover:text-purple-600">Competitor Analysis</a>
        <span>/</span>
        <a href="<?php echo e(route('competitor-analysis.show', $competitor)); ?>" class="hover:text-purple-600"><?php echo e($competitor->username); ?></a>
        <span>/</span>
        <span class="text-gray-900">Content Gaps</span>
    </div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Content Gaps</h1>
            <p class="text-sm text-gray-500 mt-1">Peluang konten yang belum dimanfaatkan dari <span class="font-medium text-gray-700"><?php echo e($competitor->username); ?></span></p>
        </div>
        <a href="<?php echo e(route('competitor-analysis.show', $competitor)); ?>"
           class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition">
            ← Kembali
        </a>
    </div>

    <?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Content Gaps List -->
    <?php if($contentGaps->isEmpty()): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Semua Content Gap Sudah Diimplementasi</h3>
        <p class="text-sm text-gray-500">Tidak ada content gap yang tersisa. Lakukan analisis ulang untuk menemukan peluang baru.</p>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php $__currentLoopData = $contentGaps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $priorityColors = [
                'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'border' => 'border-red-200'],
                'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'border' => 'border-orange-200'],
                'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200'],
                'gray' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
            ];
            $colors = $priorityColors[$gap->priority_color] ?? $priorityColors['gray'];
        ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full <?php echo e($colors['bg']); ?> <?php echo e($colors['text']); ?>">
                        <?php echo e($gap->priority_label); ?>

                    </span>
                    <span class="px-2.5 py-1 text-xs bg-purple-100 text-purple-700 rounded-full">
                        <?php echo e($gap->gap_type_label); ?>

                    </span>
                </div>
                <form action="<?php echo e(route('competitor-analysis.gap.implement', $gap->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            class="text-xs px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-lg hover:bg-green-100 transition">
                        ✓ Tandai Selesai
                    </button>
                </form>
            </div>

            <h3 class="text-base font-semibold text-gray-900 mb-2"><?php echo e($gap->gap_title); ?></h3>
            <p class="text-sm text-gray-600 mb-3"><?php echo e($gap->gap_description); ?></p>

            <?php if($gap->opportunity): ?>
            <div class="p-3 bg-blue-50 rounded-lg mb-3">
                <p class="text-xs font-medium text-blue-700 mb-1">💡 Peluang</p>
                <p class="text-sm text-blue-600"><?php echo e($gap->opportunity); ?></p>
            </div>
            <?php endif; ?>

            <?php if(!empty($gap->suggested_content)): ?>
            <div class="mt-3">
                <p class="text-xs font-medium text-gray-500 mb-2">Saran Konten:</p>
                <ul class="space-y-1">
                    <?php $__currentLoopData = $gap->suggested_content; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suggestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-start space-x-2 text-sm text-gray-600">
                        <span class="text-purple-400 mt-0.5">•</span>
                        <span><?php echo e($suggestion); ?></span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if($gap->identified_date): ?>
            <p class="text-xs text-gray-400 mt-3">Ditemukan: <?php echo e($gap->identified_date->format('d M Y')); ?></p>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\competitor-analysis\content-gaps.blade.php ENDPATH**/ ?>