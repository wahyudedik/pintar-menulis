

<?php $__env->startSection('content'); ?>
<div class="max-w-lg mx-auto px-4 py-8">

    <div class="mb-6">
        <a href="<?php echo e(route('guru.earnings')); ?>" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Penghasilan</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tarik Saldo</h1>
        <p class="text-sm text-gray-500">Saldo tersedia: <span class="font-semibold text-green-600">Rp <?php echo e(number_format($availableBalance, 0, ',', '.')); ?></span></p>
    </div>

    <?php if(session('error')): ?>
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('guru.withdrawal.store')); ?>" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
        <?php echo csrf_field(); ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Penarikan (min. Rp 10.000)</label>
            <input type="number" name="amount" min="10000" max="<?php echo e($availableBalance); ?>"
                   value="<?php echo e(old('amount')); ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="Contoh: 50000" required>
            <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
            <input type="text" name="bank_name" value="<?php echo e(old('bank_name')); ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                   placeholder="BCA / BRI / Mandiri / dll" required>
            <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
            <input type="text" name="account_number" value="<?php echo e(old('account_number')); ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                   placeholder="1234567890" required>
            <?php $__errorArgs = ['account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening</label>
            <input type="text" name="account_name" value="<?php echo e(old('account_name', $user->name)); ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                   required>
            <?php $__errorArgs = ['account_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
            <textarea name="notes" rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                      placeholder="Catatan tambahan..."><?php echo e(old('notes')); ?></textarea>
        </div>

        <button type="submit"
                class="w-full py-2.5 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition text-sm">
            Ajukan Penarikan
        </button>
    </form>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\guru\withdrawal-create.blade.php ENDPATH**/ ?>