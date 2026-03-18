

<?php $__env->startSection('title', 'Tambah Paket Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-3xl">
    <div class="mb-6">
        <a href="<?php echo e(route('admin.packages')); ?>" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Packages</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tambah Paket Baru</h1>
    </div>

    <?php if($errors->any()): ?>
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-sm text-red-800">
        <ul class="list-disc list-inside space-y-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form action="<?php echo e(route('admin.packages.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Informasi Dasar</h2>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket</label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Contoh: Enterprise">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Untuk siapa paket ini cocok..."><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Bulanan (Rp)</label>
                    <input type="number" name="price" value="<?php echo e(old('price', 0)); ?>" required min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Tahunan (Rp)</label>
                    <input type="number" name="yearly_price" value="<?php echo e(old('yearly_price')); ?>" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <p class="text-xs text-gray-400 mt-1">Kosongkan untuk otomatis (10× bulanan)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="<?php echo e(old('sort_order', 99)); ?>" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
            </div>

            
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Kuota AI</h2>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kuota AI Generate/Bulan</label>
                    <input type="number" name="ai_quota_monthly" value="<?php echo e(old('ai_quota_monthly', 0)); ?>" required min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <?php $__errorArgs = ['ai_quota_monthly'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Caption Quota</label>
                    <input type="number" name="caption_quota" value="<?php echo e(old('caption_quota', 0)); ?>" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Description Quota</label>
                    <input type="number" name="product_description_quota" value="<?php echo e(old('product_description_quota', 0)); ?>" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
            </div>

            
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Trial & Badge</h2>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teks Badge</label>
                    <input type="text" name="badge_text" value="<?php echo e(old('badge_text')); ?>" placeholder="Contoh: PALING POPULER"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warna Badge</label>
                    <select name="badge_color"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <?php $__currentLoopData = ['red' => 'Merah', 'green' => 'Hijau', 'blue' => 'Biru', 'purple' => 'Ungu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php echo e(old('badge_color') === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Trial (hari)</label>
                    <input type="number" name="trial_days" value="<?php echo e(old('trial_days', 30)); ?>" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
            </div>

            
            <?php $activeFeatures = old('allowed_features', []); ?>
            <?php echo $__env->make('admin.partials.package-features-checkboxes', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Fitur (satu per baris)</h2>
            <div class="mb-6">
                <textarea name="features_text" rows="6"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono"
                    placeholder="500 generate AI per bulan&#10;Akses semua fitur premium&#10;Priority support&#10;Custom branding"><?php echo e(old('features_text')); ?></textarea>
                <p class="text-xs text-gray-400 mt-1">Setiap baris = satu fitur yang ditampilkan di pricing page</p>
            </div>

            
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Pengaturan</h2>
            <div class="space-y-3 mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="has_trial" value="1" <?php echo e(old('has_trial') ? 'checked' : ''); ?>

                        class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700">Paket ini memiliki masa trial</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" <?php echo e(old('is_featured') ? 'checked' : ''); ?>

                        class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700">Tampilkan sebagai paket unggulan (highlighted)</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked
                        class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700">Aktif (tampil di halaman pricing)</span>
                </label>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                    Buat Paket
                </button>
                <a href="<?php echo e(route('admin.packages')); ?>"
                    class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\package-create.blade.php ENDPATH**/ ?>