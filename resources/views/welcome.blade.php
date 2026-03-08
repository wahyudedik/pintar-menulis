<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.png') }}">
    <title>Aplikasi Pembuat Caption Jualan Otomatis untuk UMKM Indonesia | Smart Copy SMK</title>
    <meta name="description" content="Bikin caption jualan yang bikin closing dalam 10 detik. Khusus UMKM Indonesia. Gratis 5 variasi caption per hari. Auto hashtag Indonesia.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="w-8 h-8 rounded-lg">
                        <span class="text-white font-semibold">Smart Copy SMK</span>
                    </div>
                    <p class="text-sm text-gray-400 mb-4">
                        Platform AI Caption Generator khusus UMKM Indonesia. Bikin caption jualan yang bikin closing!
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar Gratis</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Login</a></li>
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">Dashboard</a></li>
                            @if(auth()->user()->role === 'client')
                                <li><a href="{{ route('ai.generator') }}" class="hover:text-white transition">AI Generator</a></li>
                            @endif
                        @endauth
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('privacy-policy') }}" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="{{ route('terms-of-service') }}" class="hover:text-white transition">Terms of Service</a></li>
                        <li><a href="{{ route('refund-policy') }}" class="hover:text-white transition">Refund Policy</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Jadi Operator -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Jadi Operator</h3>
                    <p class="text-sm text-gray-400 mb-4">
                        Punya skill copywriting? Gabung jadi operator caption freelance dan dapatkan penghasilan tambahan!
                    </p>
                    <a href="https://wa.me/6281654932383?text=Halo,%20saya%20ingin%20daftar%20jadi%20operator%20caption%20freelance" 
                       target="_blank"
                       class="inline-flex items-center space-x-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span>Daftar via WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 pt-8 mt-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <p class="text-sm text-gray-400">
                        &copy; {{ date('Y') }} Smart Copy SMK. All rights reserved.
                    </p>
                    <p class="text-sm text-gray-400">
                        Made with ❤️ for UMKM Indonesia
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
