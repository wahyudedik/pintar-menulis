<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak | <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="text-8xl font-black text-yellow-500 mb-4">403</div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Akses Ditolak</h1>
        <p class="text-gray-500 mb-8">Kamu tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="<?php echo e(url('/')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition">
            ← Kembali ke Beranda
        </a>
    </div>
</body>
</html>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\errors\403.blade.php ENDPATH**/ ?>