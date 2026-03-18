<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="text-8xl font-black text-blue-600 mb-4">404</div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Halaman Tidak Ditemukan</h1>
        <p class="text-gray-500 mb-8">Halaman yang kamu cari tidak ada atau sudah dipindahkan.</p>
        <a href="<?php echo e(url('/')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition">
            ← Kembali ke Beranda
        </a>
    </div>
</body>
</html>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\errors\404.blade.php ENDPATH**/ ?>