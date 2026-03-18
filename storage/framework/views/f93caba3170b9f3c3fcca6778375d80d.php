

<?php $__env->startSection('title', 'Edit Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Profile Operator</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi profil Anda</p>
    </div>

    <?php if($errors->any()): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-4">
        <ul class="list-disc list-inside text-sm text-red-700">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('operator.profile.update')); ?>" class="bg-white rounded-lg border border-gray-200 p-4">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- Bio -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
            <textarea name="bio" required rows="4" 
                      placeholder="Ceritakan tentang diri Anda, pengalaman, dan keahlian..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"><?php echo e(old('bio', $profile->bio)); ?></textarea>
            <p class="text-xs text-gray-500 mt-1">Minimal 50 karakter, maksimal 500 karakter</p>
        </div>

        <!-- Portfolio URL -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Portfolio URL</label>
            <input type="url" name="portfolio_url" 
                   value="<?php echo e(old('portfolio_url', $profile->portfolio_url)); ?>"
                   placeholder="https://portfolio.com"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
        </div>

        <!-- Specializations -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Spesialisasi</label>
            <div class="grid md:grid-cols-3 gap-2">
                <?php
                $allSpecs = [
                    'Social Media', 'Ads', 'Website', 'Landing Page', 
                    'Marketplace', 'Email Marketing', 'Proposal', 
                    'Company Profile', 'Personal Branding', 'UX Writing',
                    'Product Description', 'SEO', 'Content Writing'
                ];
                $selectedSpecs = old('specializations', $profile->specializations ?? []);
                ?>
                
                <?php $__currentLoopData = $allSpecs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="specializations[]" value="<?php echo e($spec); ?>"
                           <?php echo e(in_array($spec, $selectedSpecs) ? 'checked' : ''); ?>

                           class="rounded text-green-600 focus:ring-green-500">
                    <span class="text-sm"><?php echo e($spec); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <p class="text-xs text-gray-500 mt-1">Pilih minimal 1 spesialisasi</p>
        </div>

        <!-- Base Price -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Dasar</label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                <input type="number" name="base_price" required min="50000" step="10000"
                       value="<?php echo e(old('base_price', $profile->base_price)); ?>"
                       class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
            </div>
            <p class="text-xs text-gray-500 mt-1">Minimal Rp 50.000</p>
        </div>

        <!-- Bank Account -->
        <div class="border-t border-gray-200 pt-4 mb-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Informasi Bank (untuk withdrawal)</h3>
            
            <div class="grid md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bank</label>
                    <select name="bank_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                        <option value="">Pilih Bank</option>
                        <option value="BCA" <?php echo e(old('bank_name', $profile->bank_name) == 'BCA' ? 'selected' : ''); ?>>BCA</option>
                        <option value="Mandiri" <?php echo e(old('bank_name', $profile->bank_name) == 'Mandiri' ? 'selected' : ''); ?>>Mandiri</option>
                        <option value="BNI" <?php echo e(old('bank_name', $profile->bank_name) == 'BNI' ? 'selected' : ''); ?>>BNI</option>
                        <option value="BRI" <?php echo e(old('bank_name', $profile->bank_name) == 'BRI' ? 'selected' : ''); ?>>BRI</option>
                        <option value="CIMB Niaga" <?php echo e(old('bank_name', $profile->bank_name) == 'CIMB Niaga' ? 'selected' : ''); ?>>CIMB Niaga</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" 
                           value="<?php echo e(old('bank_account_number', $profile->bank_account_number)); ?>"
                           placeholder="1234567890"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemilik Rekening</label>
                <input type="text" name="bank_account_name" 
                       value="<?php echo e(old('bank_account_name', $profile->bank_account_name)); ?>"
                       placeholder="Sesuai dengan nama di rekening"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
            </div>
        </div>

        <!-- Availability -->
        <div class="mb-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_available" value="1"
                       <?php echo e(old('is_available', $profile->is_available) ? 'checked' : ''); ?>

                       class="rounded text-green-600 focus:ring-green-500">
                <span class="text-sm font-medium text-gray-700">Saya tersedia untuk menerima order</span>
            </label>
        </div>

        <!-- Submit -->
        <div class="flex gap-3">
            <button type="submit" 
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                Simpan Profile
            </button>
            <a href="<?php echo e(route('dashboard')); ?>" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                Batal
            </a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.operator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\operator\profile-edit.blade.php ENDPATH**/ ?>