

<?php $__env->startSection('title', 'Edit Content - ' . $content->title); ?>

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
        <span class="text-gray-900">Edit</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h1 class="text-xl font-bold text-gray-900 mb-6">Edit Content</h1>

                <form action="<?php echo e(route('projects.content.update', [$project, $content])); ?>" method="POST" class="space-y-5">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <?php if($errors->any()): ?>
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="<?php echo e(old('title', $content->title)); ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konten <span class="text-red-500">*</span></label>
                        <textarea name="content" rows="12" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"><?php echo e(old('content', $content->content)); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                        <select name="platform" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Platform</option>
                            <?php $__currentLoopData = ['instagram','tiktok','facebook','twitter','whatsapp','shopee','tokopedia','website']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p); ?>" <?php echo e(old('platform', $content->platform) === $p ? 'selected' : ''); ?>><?php echo e(ucfirst($p)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Perubahan</label>
                        <input type="text" name="change_notes" value="<?php echo e(old('change_notes')); ?>"
                               placeholder="Apa yang diubah? (opsional)"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-sm">
                            Simpan Perubahan
                        </button>
                        <a href="<?php echo e(route('projects.content.show', [$project, $content])); ?>"
                           class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Info Content</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tipe</span>
                        <span class="font-medium"><?php echo e(ucfirst(str_replace('_', ' ', $content->content_type))); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full"><?php echo e(ucfirst($content->status)); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Versi</span>
                        <span class="font-medium">v<?php echo e($content->version); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dibuat</span>
                        <span class="font-medium"><?php echo e($content->created_at->format('d M Y')); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\projects\content\edit.blade.php ENDPATH**/ ?>