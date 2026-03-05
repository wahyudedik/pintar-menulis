<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.png') }}">
    <title>Aplikasi Pembuat Caption Jualan Otomatis untuk UMKM Indonesia | Smart Copy SMK</title>
    <meta name="description" content="Bikin caption jualan yang bikin closing dalam 10 detik. Khusus UMKM Indonesia. Gratis 5 variasi caption per hari. Auto hashtag Indonesia.">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="w-10 h-10 rounded-lg object-cover">
                    <span class="text-lg font-semibold text-gray-900">Smart Copy SMK</span>
                </div>
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <!-- Pain Point Hook -->
                <div class="mb-6 space-y-2">
                    <p class="text-red-600 font-semibold text-lg">😰 Capek posting tapi sepi orderan?</p>
                    <p class="text-red-600 font-semibold text-lg">😰 Bingung bikin caption yang bikin orang beli?</p>
                    <p class="text-red-600 font-semibold text-lg">😰 Gak punya tim marketing tapi pengen closing naik?</p>
                </div>
                
                <h1 class="text-5xl font-bold text-gray-900 mb-6 mt-8">
                    AI Caption Generator Khusus UMKM Indonesia<br>
                    <span class="text-blue-600">yang Ingin Closing, Bukan Cuma Estetik</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Bikin caption jualan yang bikin customer langsung action dalam hitungan detik. Gak perlu pusing mikir kata-kata, AI kami yang kerja buat kamu!
                </p>
                <div class="flex justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-blue-600 text-white text-lg rounded-lg hover:bg-blue-700 transition font-semibold">
                            🚀 Mulai Generate Caption
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-blue-600 text-white text-lg rounded-lg hover:bg-blue-700 transition font-semibold">
                            🚀 Coba Gratis Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-4 border-2 border-gray-300 text-gray-700 text-lg rounded-lg hover:border-gray-400 transition font-semibold">
                            Login
                        </a>
                    @endauth
                </div>
                <p class="text-sm text-gray-500 mt-4">✨ Gratis 5 variasi caption per hari • Gak pakai kartu kredit • Langsung pakai!</p>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-blue-50 border-b border-gray-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-blue-600 mb-2">57+</div>
                    <div class="text-sm text-gray-600">Template Spesifik</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-blue-600 mb-2">5</div>
                    <div class="text-sm text-gray-600">Platform Sosmed</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-blue-600 mb-2">100%</div>
                    <div class="text-sm text-gray-600">Bahasa Indonesia</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-blue-600 mb-2">3 Detik</div>
                    <div class="text-sm text-gray-600">Hook Generator</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Why Different Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Kenapa UMKM Pilih Kami?</h2>
                <p class="text-lg text-gray-600">Karena kami paham banget perjuangan UMKM Indonesia</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6 bg-blue-50 rounded-lg">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">🎯 Fokus Closing, Bukan Estetik</h3>
                    <p class="text-gray-700">Setiap caption dirancang biar customer langsung action: beli, DM, atau klik link. Bukan cuma cantik tapi gak laku!</p>
                </div>
                <div class="text-center p-6 bg-green-50 rounded-lg">
                    <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">🗣️ Bahasa UMKM Indonesia</h3>
                    <p class="text-gray-700">Pakai bahasa yang relate: "Kak", "Bun", "Gaes". Bukan bahasa robot atau terlalu formal. Cocok buat jualan online!</p>
                </div>
                <div class="text-center p-6 bg-purple-50 rounded-lg">
                    <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">⚡ Hemat Waktu & Tenaga</h3>
                    <p class="text-gray-700">5 variasi caption dalam 10 detik. Auto hashtag Indonesia. Gak perlu pusing mikir kata-kata lagi!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Problem Solution Section -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Masalah UMKM yang Kami Solve</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <div class="bg-white p-6 rounded-lg border-2 border-red-200">
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">❌</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Masalah:</h4>
                            <p class="text-gray-700">"Udah posting tiap hari tapi kok sepi orderan? Caption kayaknya kurang menarik..."</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg border-2 border-green-200">
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">✅</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Solusi Kami:</h4>
                            <p class="text-gray-700">Caption yang fokus closing dengan hook kuat, CTA jelas, dan bahasa yang bikin orang pengen beli!</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg border-2 border-red-200">
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">❌</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Masalah:</h4>
                            <p class="text-gray-700">"Gak punya tim marketing, harus mikir sendiri caption tiap hari. Capek!"</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg border-2 border-green-200">
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">✅</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Solusi Kami:</h4>
                            <p class="text-gray-700">Generate 5 variasi caption dalam 10 detik. Tinggal pilih yang paling cocok, langsung posting!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Fitur Khusus UMKM</h2>
                <p class="text-gray-600">Semua yang kamu butuhkan untuk jualan online</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg p-6 border-2 border-blue-200">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🏭 12 Industry Presets</h3>
                    <p class="text-sm text-gray-600">Fashion, Makanan, Skincare, Printing, Fotografi, Catering, TikTok Shop, Shopee Affiliate, dan lainnya</p>
                </div>

                <div class="bg-white rounded-lg p-6 border-2 border-green-200">
                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🗣️ Bahasa Daerah</h3>
                    <p class="text-sm text-gray-600">Tambah sentuhan lokal dengan bahasa Jawa, Sunda, Betawi, Minang, atau Batak</p>
                </div>

                <div class="bg-white rounded-lg p-6 border-2 border-purple-200">
                    <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🏷️ Auto Hashtag</h3>
                    <p class="text-sm text-gray-600">Otomatis generate hashtag trending Indonesia yang relevan dengan produk kamu</p>
                </div>

                <div class="bg-white rounded-lg p-6 border-2 border-yellow-200">
                    <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">📊 Analytics Tracking</h3>
                    <p class="text-sm text-gray-600">Track performa caption kamu: likes, comments, shares, dan engagement rate</p>
                </div>

                <div class="bg-white rounded-lg p-6 border-2 border-red-200">
                    <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🔄 5-20 Variasi</h3>
                    <p class="text-sm text-gray-600">Generate 5 variasi gratis, atau 20 variasi sekaligus untuk premium users</p>
                </div>

                <div class="bg-white rounded-lg p-6 border-2 border-indigo-200">
                    <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🎯 Mode Simpel</h3>
                    <p class="text-sm text-gray-600">Gaptek? Tenang! Jawab 5 pertanyaan simpel, langsung jadi caption!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <div class="bg-white border-y border-gray-200 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Cara Pakai (Super Gampang!)</h2>
                <p class="text-gray-600">Cuma 3 langkah, caption langsung jadi</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 border-2 border-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-blue-600">1</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Pilih Mode</h3>
                    <p class="text-sm text-gray-600">Mode Simpel (5 pertanyaan) atau Mode Lengkap (lebih detail)</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 border-2 border-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-green-600">2</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Isi Brief</h3>
                    <p class="text-sm text-gray-600">Ceritain produk kamu, target market, dan tujuan jualan</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 border-2 border-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-purple-600">3</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Langsung Jadi!</h3>
                    <p class="text-sm text-gray-600">Dapat 5 variasi caption + hashtag. Tinggal copy & posting!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-3">Udah Siap Naikin Closing?</h2>
            <p class="text-xl text-blue-100 mb-8">Ribuan UMKM udah pakai. Sekarang giliran kamu!</p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition font-semibold text-lg">
                🚀 Mulai Gratis Sekarang
            </a>
            <p class="text-sm text-blue-100 mt-4">Gak pakai kartu kredit • Langsung bisa pakai • 5 variasi gratis per hari</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm text-gray-600">&copy; {{ date('Y') }} Smart Copy SMK. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
