<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <img src="<?php echo e(asset('logo.png')); ?>" alt="Logo" class="w-16 h-16 rounded-lg object-cover mx-auto mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Lupa Password?</h2>
            <p class="text-sm text-gray-600 mt-2">Masukkan email kamu dan kami akan kirim link reset password.</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-8">
            <?php if(session('status')): ?>
            <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-700">
                <?php echo e(session('status')); ?>

            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('password.email')); ?>">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <?php $__errorArgs = ['email'];
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
                    Kirim Link Reset Password
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="<?php echo e(route('login')); ?>" class="text-sm text-blue-600 hover:underline">← Kembali ke Login</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\auth\forgot-password.blade.php ENDPATH**/ ?>