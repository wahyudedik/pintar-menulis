<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Password</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <img src="<?php echo e(asset('logo.png')); ?>" alt="Logo" class="w-16 h-16 rounded-lg object-cover mx-auto mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Konfirmasi Password</h2>
            <p class="text-sm text-gray-600 mt-2">Area aman. Konfirmasi password kamu untuk melanjutkan.</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-8">
            <form method="POST" action="<?php echo e(route('password.confirm')); ?>">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" type="password" name="password" required autofocus
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit"
                        class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    Konfirmasi
                </button>
            </form>
        </div>
    </div>
</body>
</html>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\auth\confirm-password.blade.php ENDPATH**/ ?>