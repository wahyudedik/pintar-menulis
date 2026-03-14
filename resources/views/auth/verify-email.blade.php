<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="w-16 h-16 rounded-lg object-cover mx-auto mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Verifikasi Email Kamu</h2>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600">
                    Terima kasih sudah mendaftar! Sebelum mulai, silakan verifikasi email kamu dengan mengklik link yang sudah kami kirim.
                    Cek folder <strong>Inbox</strong> atau <strong>Spam</strong>.
                </p>
            </div>

            @if(session('status') == 'verification-link-sent')
            <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-700 text-center">
                ✅ Link verifikasi baru sudah dikirim ke email kamu.
            </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                @csrf
                <button type="submit"
                        class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>
