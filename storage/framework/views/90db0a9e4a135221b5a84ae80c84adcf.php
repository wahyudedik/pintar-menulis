

<?php $__env->startSection('title', 'Edit Template - ' . $template->title); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?php echo e(route('templates.index')); ?>" class="hover:text-blue-600">Template Marketplace</a>
        <span>/</span>
        <a href="<?php echo e(route('templates.show', $template->id)); ?>" class="hover:text-blue-600"><?php echo e($template->title); ?></a>
        <span>/</span>
        <span class="text-gray-900">Edit</span>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h1 class="text-xl font-bold text-gray-900 mb-6">Edit Template</h1>

            <form id="edit-form">
                <?php echo csrf_field(); ?>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Template</label>
                        <input type="text" name="title" value="<?php echo e($template->title); ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"><?php echo e($template->description); ?></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <?php $__currentLoopData = ['viral_clickbait','trend_fresh_ideas','event_promo','hr_recruitment','branding_tagline','education','monetization','video_monetization','freelance','digital_products']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat); ?>" <?php echo e($template->category === $cat ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('_', ' ', $cat))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                            <select name="platform" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <?php $__currentLoopData = ['instagram','tiktok','facebook','twitter','youtube','linkedin','whatsapp','marketplace','all']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p); ?>" <?php echo e($template->platform === $p ? 'selected' : ''); ?>><?php echo e(ucfirst($p)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tone</label>
                            <select name="tone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <?php $__currentLoopData = ['casual','professional','friendly','exciting','persuasive','educational','humorous']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($t); ?>" <?php echo e($template->tone === $t ? 'selected' : ''); ?>><?php echo e(ucfirst($t)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konten Template</label>
                        <textarea name="template_content" rows="8" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-mono"><?php echo e($template->template_content); ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">Gunakan [VARIABEL] untuk bagian yang perlu diisi pengguna</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Petunjuk Penggunaan (opsional)</label>
                        <textarea name="format_instructions" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"><?php echo e($template->format_instructions); ?></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lisensi</label>
                            <select name="license_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <?php $__currentLoopData = ['free','personal','commercial','extended']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($l); ?>" <?php echo e($template->license_type === $l ? 'selected' : ''); ?>><?php echo e(ucfirst($l)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (jika premium)</label>
                            <input type="number" name="price" value="<?php echo e($template->price ?? 0); ?>" min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_public" value="1" <?php echo e($template->is_public ? 'checked' : ''); ?>

                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Publik (tampil di marketplace)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_premium" value="1" <?php echo e($template->is_premium ? 'checked' : ''); ?>

                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Template Premium</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3 mt-6 pt-6 border-t border-gray-100">
                    <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-sm">
                        Simpan Perubahan
                    </button>
                    <a href="<?php echo e(route('templates.show', $template->id)); ?>"
                       class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('edit-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    data.is_public = formData.has('is_public') ? true : false;
    data.is_premium = formData.has('is_premium') ? true : false;

    fetch(`/templates/<?php echo e($template->id); ?>`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'X-HTTP-Method-Override': 'PUT' },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(d => {
        if (d.success) {
            window.location.href = '<?php echo e(route("templates.show", $template->id)); ?>';
        } else {
            alert(d.message || 'Gagal menyimpan perubahan');
        }
    }).catch(() => alert('Terjadi kesalahan'));
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\template-marketplace\edit.blade.php ENDPATH**/ ?>