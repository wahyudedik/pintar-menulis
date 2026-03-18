

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Training History</h1>
            <p class="text-sm text-gray-600 mt-1">Riwayat semua data training yang telah direview</p>
        </div>
        <a href="<?php echo e(route('guru.training')); ?>" class="text-purple-600 hover:text-purple-700 text-sm">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Total</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total']); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Excellent</p>
            <p class="text-2xl font-bold text-green-600"><?php echo e($stats['excellent']); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Good</p>
            <p class="text-2xl font-bold text-blue-600"><?php echo e($stats['good']); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Fair</p>
            <p class="text-2xl font-bold text-yellow-600"><?php echo e($stats['fair']); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Poor</p>
            <p class="text-2xl font-bold text-red-600"><?php echo e($stats['poor']); ?></p>
        </div>
    </div>

    <!-- Training Data List -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">All Training Data</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Input</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Quality</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Reviewed By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $trainingData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <?php echo e($data->created_at->format('d M Y')); ?>

                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <?php echo e(Str::limit($data->input_prompt, 60)); ?>

                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-medium
                                <?php if($data->quality_rating === 'excellent'): ?> bg-green-100 text-green-700
                                <?php elseif($data->quality_rating === 'good'): ?> bg-blue-100 text-blue-700
                                <?php elseif($data->quality_rating === 'fair'): ?> bg-yellow-100 text-yellow-700
                                <?php else: ?> bg-red-100 text-red-700
                                <?php endif; ?>">
                                <?php echo e(ucfirst($data->quality_rating)); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <?php echo e($data->guru->name); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            <?php echo e($trainingData->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\guru\training-history.blade.php ENDPATH**/ ?>