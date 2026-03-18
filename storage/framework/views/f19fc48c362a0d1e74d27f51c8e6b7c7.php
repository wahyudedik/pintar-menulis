

<?php $__env->startSection('title', 'Banner Information Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Banner Information Management</h1>
    </div>

    <?php if(session('success')): ?>
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-800"><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-6">
            <div class="mb-6">
                <h3 class="text-base font-semibold mb-2">Manage Banner Popups</h3>
                <p class="text-sm text-gray-600">
                    Configure banner information popups for different sections. Banners will only show once per user on their first visit.
                    If title or content is empty, the banner will not be displayed.
                </p>
            </div>

            <div class="space-y-6">
                <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border border-gray-200 rounded-lg p-6">
                        <form method="POST" action="<?php echo e(route('admin.banner-information.update', $banner)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-2xl
                                        <?php if($banner->type === 'landing'): ?> bg-blue-50
                                        <?php elseif($banner->type === 'client'): ?> bg-green-50
                                        <?php elseif($banner->type === 'operator'): ?> bg-purple-50
                                        <?php elseif($banner->type === 'guru'): ?> bg-orange-50
                                        <?php endif; ?>">
                                        <?php if($banner->type === 'landing'): ?>
                                            🏠
                                        <?php elseif($banner->type === 'client'): ?>
                                            👤
                                        <?php elseif($banner->type === 'operator'): ?>
                                            ⚙️
                                        <?php elseif($banner->type === 'guru'): ?>
                                            🎓
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900">
                                            <?php if($banner->type === 'landing'): ?>
                                                Landing Page Banner
                                            <?php elseif($banner->type === 'client'): ?>
                                                Client Dashboard Banner
                                            <?php elseif($banner->type === 'operator'): ?>
                                                Operator Dashboard Banner
                                            <?php elseif($banner->type === 'guru'): ?>
                                                Guru Dashboard Banner
                                            <?php endif; ?>
                                        </h4>
                                        <p class="text-xs text-gray-500">
                                            <?php if($banner->type === 'landing'): ?>
                                                Shown to visitors on the landing page
                                            <?php else: ?>
                                                Shown to <?php echo e($banner->type); ?> users on their dashboard
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>

                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" 
                                        <?php echo e($banner->is_active ? 'checked' : ''); ?>

                                        <?php echo e((!$banner->title || !$banner->content) ? 'disabled' : ''); ?>>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-700">Active</span>
                                </label>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="title_<?php echo e($banner->id); ?>" class="block text-sm font-medium text-gray-700 mb-1">
                                        Banner Title
                                    </label>
                                    <input type="text" 
                                        id="title_<?php echo e($banner->id); ?>" 
                                        name="title" 
                                        value="<?php echo e(old('title', $banner->title)); ?>"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Enter banner title (leave empty to disable)">
                                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div>
                                    <label for="content_<?php echo e($banner->id); ?>" class="block text-sm font-medium text-gray-700 mb-1">
                                        Banner Content
                                    </label>
                                    <textarea 
                                        id="content_<?php echo e($banner->id); ?>" 
                                        name="content" 
                                        rows="4"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Enter banner content (supports HTML)"><?php echo e(old('content', $banner->content)); ?></textarea>
                                    <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <p class="mt-1 text-xs text-gray-500">
                                        You can use HTML tags for formatting (e.g., &lt;strong&gt;, &lt;em&gt;, &lt;br&gt;, &lt;a&gt;)
                                    </p>
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                    <div class="text-xs text-gray-600">
                                        <?php if($banner->title && $banner->content): ?>
                                            <span class="text-green-600">✓ Banner configured</span>
                                        <?php else: ?>
                                            <span class="text-gray-400">⚠ Banner disabled (title or content empty)</span>
                                        <?php endif; ?>
                                    </div>
                                    <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\banner-information\index.blade.php ENDPATH**/ ?>