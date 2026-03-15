<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Server Error | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="text-8xl font-black text-red-500 mb-4">500</div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Terjadi Kesalahan Server</h1>
        <p class="text-gray-500 mb-8">Maaf, terjadi kesalahan pada server kami. Tim kami sudah diberitahu.</p>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition">
            ← Kembali ke Beranda
        </a>
    </div>
</body>
</html>
