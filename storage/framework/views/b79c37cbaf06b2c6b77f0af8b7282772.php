

<?php $__env->startSection('title', 'Version History - ' . $content->title); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?php echo e(route('projects.index')); ?>" class="hover:text-blue-600">Projects</a>
        <span>/</span>
        <a href="<?php echo e(route('projects.show', $project)); ?>" class="hover:text-blue-600"><?php echo e($project->business_name); ?></a>
        <span>/</span>
        <a href="<?php echo e(route('projects.content.index', $project)); ?>" class="hover:text-blue-600">Content</a>
        <span>/</span>
        <a href="<?php echo e(route('projects.content.show', [$project, $content])); ?>" class="hover:text-blue-600"><?php echo e(Str::limit($content->title, 30)); ?></a>
        <span>/</span>
        <span class="text-gray-900">Version History</span>
    </div>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Version History</h1>
            <p class="text-sm text-gray-500 mt-1"><?php echo e($content->title); ?></p>
        </div>
        <a href="<?php echo e(route('projects.content.show', [$project, $content])); ?>"
           class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <?php $__empty_1 = true; $__currentLoopData = $versions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-start gap-4 p-4 border-b border-gray-100 last:border-0 hover:bg-gray-50">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                <span class="text-xs font-bold text-blue-600">v<?php echo e($version->version_number); ?></span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <?php
                        $typeColors = ['created'=>'green','edited'=>'blue','submitted'=>'yellow','approved'=>'green','rejected'=>'red','restored'=>'purple'];
                        $color = $typeColors[$version->change_type] ?? 'gray';
                    ?>
                    <span class="px-2 py-0.5 bg-<?php echo e($color); ?>-100 text-<?php echo e($color); ?>-700 text-xs font-medium rounded-full">
                        <?php echo e(ucfirst(str_replace('_', ' ', $version->change_type))); ?>

                    </span>
                    <?php if($loop->first): ?>
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Current</span>
                    <?php endif; ?>
                </div>
                <?php if($version->change_notes): ?>
                <p class="text-sm text-gray-700"><?php echo e($version->change_notes); ?></p>
                <?php endif; ?>
                <p class="text-xs text-gray-500 mt-1">
                    oleh <?php echo e($version->creator->name ?? 'System'); ?> · <?php echo e($version->created_at->format('d M Y H:i')); ?>

                </p>
            </div>
            <div class="flex-shrink-0">
                <?php if(!$loop->first): ?>
                <form action="<?php echo e(route('projects.content.restore-version', [$project, $content, $version])); ?>" method="POST"
                      onsubmit="return confirm('Restore ke versi ini? Perubahan saat ini akan disimpan sebagai versi baru.')">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            class="px-3 py-1.5 border border-yellow-300 text-yellow-700 text-xs font-medium rounded-lg hover:bg-yellow-50 transition">
                        Restore
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-12">
            <p class="text-gray-500 text-sm">Belum ada riwayat versi</p>
        </div>
        <?php endif; ?>
    </div>

    <?php if($versions->hasPages()): ?>
    <div class="mt-4"><?php echo e($versions->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\projects\content\versions.blade.php ENDPATH**/ ?>