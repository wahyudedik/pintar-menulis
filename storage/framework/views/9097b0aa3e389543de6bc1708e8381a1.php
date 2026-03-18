

<?php $__env->startSection('title', 'ML Data Detail'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
            <a href="<?php echo e(route('admin.ml-data.index')); ?>" class="hover:text-gray-700">ML Data</a>
            <span>/</span>
            <span class="text-gray-900"><?php echo e($mlData->username); ?></span>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900"><?php echo e($mlData->username); ?></h1>
        <p class="text-sm text-gray-500 capitalize mt-1"><?php echo e($mlData->platform); ?></p>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Basic Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Informasi Cache</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Username</span>
                    <span class="text-sm font-medium text-gray-900"><?php echo e($mlData->username); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Platform</span>
                    <span class="text-sm font-medium text-gray-900 capitalize"><?php echo e($mlData->platform); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Quality Score</span>
                    <span class="text-sm font-medium text-gray-900"><?php echo e(number_format($mlData->data_quality_score ?? 0, 2)); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Cache Hits</span>
                    <span class="text-sm font-medium text-gray-900"><?php echo e($mlData->cache_hit_count ?? 0); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">API Calls Saved</span>
                    <span class="text-sm font-medium text-green-600"><?php echo e($mlData->api_calls_saved ?? 0); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Last Cache Hit</span>
                    <span class="text-sm font-medium text-gray-900"><?php echo e($mlData->last_cache_hit ? \Carbon\Carbon::parse($mlData->last_cache_hit)->diffForHumans() : '-'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Updated</span>
                    <span class="text-sm font-medium text-gray-900"><?php echo e($mlData->updated_at->format('d M Y H:i')); ?></span>
                </div>
            </div>
        </div>

        <!-- Raw Data Preview -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Data Preview</h2>
            <?php if($mlData->profile_data): ?>
            <pre class="text-xs text-gray-600 bg-gray-50 rounded-lg p-3 overflow-auto max-h-64"><?php echo e(json_encode($mlData->profile_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
            <?php else: ?>
            <p class="text-sm text-gray-400">Tidak ada data tersedia.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-6">
        <a href="<?php echo e(route('admin.ml-data.index')); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition">
            ← Kembali
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\ml-data\show.blade.php ENDPATH**/ ?>