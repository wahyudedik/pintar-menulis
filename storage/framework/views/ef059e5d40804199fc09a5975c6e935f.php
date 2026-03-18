<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-9FYHV90H8P"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());   

        gtag('config', 'G-9FYHV90H8P');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" type="image/jpeg" href="<?php echo e(asset('favicon.png')); ?>">
    <title>Aplikasi Pembuat Caption Jualan Otomatis untuk UMKM Indonesia | Noteds</title>
    <meta name="description" content="Bikin caption jualan yang bikin closing dalam 10 detik. Khusus UMKM Indonesia. Gratis 5 variasi caption per hari. Auto hashtag Indonesia.">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 backdrop-blur-sm bg-white/95" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <img src="<?php echo e(asset('logo.png')); ?>" alt="Logo" class="w-10 h-10 rounded-lg object-cover">
                    <span class="text-lg font-semibold text-gray-900">Noteds</span>
                </div>
                <!-- Desktop Nav -->
                <div class="hidden sm:flex items-center space-x-3">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('dashboard')); ?>" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition">Dashboard</a>
                        <a href="<?php echo e(route('articles.index')); ?>" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition">Artikel</a>
                        <a href="<?php echo e(route('pricing')); ?>" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition">Pricing</a>
                    <?php else: ?>
                        <a href="<?php echo e(route('articles.index')); ?>" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition">Artikel</a>
                        <a href="<?php echo e(route('pricing')); ?>" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition">Pricing</a>
                        <a href="<?php echo e(route('login')); ?>" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition">Login</a>
                        <a href="<?php echo e(route('register')); ?>" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            Register
                        </a>
                    <?php endif; ?>
                </div>
                <!-- Mobile Hamburger -->
                <button @click="mobileOpen = !mobileOpen" class="sm:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition" aria-label="Toggle menu">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="sm:hidden border-t border-gray-200 bg-white px-4 py-3 space-y-1">
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">Dashboard</a>
                <a href="<?php echo e(route('articles.index')); ?>" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">Artikel</a>
                <a href="<?php echo e(route('pricing')); ?>" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">Pricing</a>
            <?php else: ?>
                <a href="<?php echo e(route('articles.index')); ?>" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">Artikel</a>
                <a href="<?php echo e(route('pricing')); ?>" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">Pricing</a>
                <a href="<?php echo e(route('login')); ?>" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">Login</a>
                <a href="<?php echo e(route('register')); ?>" class="block px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900">
        <!-- Circuit Lines SVG -->
        <svg class="absolute inset-0 w-full h-full opacity-20" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="circuit-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#06b6d4;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#3b82f6;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#06b6d4;stop-opacity:1" />
                </linearGradient>
            </defs>
            <!-- Horizontal Lines -->
            <line x1="0" y1="100" x2="400" y2="100" stroke="url(#circuit-gradient)" stroke-width="2" class="circuit-line" />
            <circle cx="400" cy="100" r="4" fill="#06b6d4" class="circuit-node" />
            <line x1="400" y1="100" x2="400" y2="200" stroke="url(#circuit-gradient)" stroke-width="2" class="circuit-line" />
            <circle cx="400" cy="200" r="4" fill="#3b82f6" class="circuit-node" />
            <line x1="400" y1="200" x2="600" y2="200" stroke="url(#circuit-gradient)" stroke-width="2" class="circuit-line" />
            
            <line x1="1440" y1="150" x2="1040" y2="150" stroke="url(#circuit-gradient)" stroke-width="2" class="circuit-line" />
            <circle cx="1040" cy="150" r="4" fill="#06b6d4" class="circuit-node" />
            <line x1="1040" y1="150" x2="1040" y2="250" stroke="url(#circuit-gradient)" stroke-width="2" class="circuit-line" />
            <circle cx="1040" cy="250" r="4" fill="#3b82f6" class="circuit-node" />
            
            <line x1="200" y1="400" x2="500" y2="400" stroke="url(#circuit-gradient)" stroke-width="2" class="circuit-line" />
            <circle cx="500" cy="400" r="4" fill="#06b6d4" class="circuit-node" />
            <line x1="500" y1="400" x2="500" y2="500" stroke="url(#circuit-gradient)" stroke-width="2" class="circuit-line" />
            
            <line x1="1200" y1="350" x2="900" y2="350" stroke="url(#circuit-gradient)" stroke-width="2" class="circuit-line" />
            <circle cx="900" cy="350" r="4" fill="#3b82f6" class="circuit-node" />
            <line x1="900" y1="350" x2="900" y2="450" stroke="url(#circuit-gradient)" stroke-width="2" class="circuit-line" />
            <circle cx="900" cy="450" r="4" fill="#06b6d4" class="circuit-node" />
        </svg>
        
        <!-- Animated Gradient Orbs -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-cyan-500 rounded-full mix-blend-screen filter blur-3xl animate-blob"></div>
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-blue-500 rounded-full mix-blend-screen filter blur-3xl animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-teal-500 rounded-full mix-blend-screen filter blur-3xl animate-blob animation-delay-4000"></div>
        </div>
        
        <!-- Tech Grid Pattern -->
        <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(6, 182, 212, 0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(6, 182, 212, 0.5) 1px, transparent 1px); background-size: 50px 50px;"></div>
        
        <!-- Floating Tech Elements -->
        <div class="absolute inset-0">
            <div class="tech-chip tech-chip-1"></div>
            <div class="tech-chip tech-chip-2"></div>
            <div class="tech-chip tech-chip-3"></div>
            <div class="tech-chip tech-chip-4"></div>
        </div>
        
        <!-- Content -->
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-24">
            <div class="text-center">
                <!-- Pain Point Hook -->
                <div class="mb-10 space-y-3">
                    <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800/60 backdrop-blur-sm border border-cyan-500/30 rounded-full">
                        <span class="text-2xl">😰</span>
                        <p class="text-cyan-100 font-medium text-sm">Capek posting tapi sepi orderan?</p>
                    </div>
                    <br>
                    <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800/60 backdrop-blur-sm border border-cyan-500/30 rounded-full">
                        <span class="text-2xl">😰</span>
                        <p class="text-cyan-100 font-medium text-sm">Bingung bikin caption yang bikin orang beli?</p>
                    </div>
                    <br>
                    <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800/60 backdrop-blur-sm border border-cyan-500/30 rounded-full">
                        <span class="text-2xl">😰</span>
                        <p class="text-cyan-100 font-medium text-sm">Gak punya tim marketing tapi pengen closing naik?</p>
                    </div>
                </div>
                
                <h1 class="text-3xl sm:text-5xl md:text-7xl font-black text-white mb-6 leading-tight tracking-tight">
                    AI Caption Generator<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-teal-400 animate-gradient-x">Khusus UMKM Indonesia</span>
                </h1>
                <p class="text-base sm:text-xl md:text-2xl text-slate-300 mb-10 max-w-3xl mx-auto leading-relaxed">
                    Bikin caption jualan yang bikin customer langsung action dalam hitungan detik. Gak perlu pusing mikir kata-kata, AI kami yang kerja buat kamu!
                </p>
                <div class="flex justify-center gap-4 flex-wrap">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('dashboard')); ?>" class="group relative px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-lg font-bold rounded-xl overflow-hidden transition-all duration-300 hover:scale-105 shadow-2xl shadow-cyan-500/50 hover:shadow-cyan-400/60">
                            <span class="relative z-10 flex items-center gap-2">
                                🚀 Mulai Generate Caption
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('register')); ?>" class="group relative px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-lg font-bold rounded-xl overflow-hidden transition-all duration-300 hover:scale-105 shadow-2xl shadow-cyan-500/50 hover:shadow-cyan-400/60">
                            <span class="relative z-10 flex items-center gap-2">
                                🚀 Coba Gratis Sekarang
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </a>
                        <a href="<?php echo e(route('login')); ?>" class="px-8 py-4 bg-slate-800/80 backdrop-blur-sm border-2 border-cyan-500/50 text-white text-lg font-bold rounded-xl hover:bg-slate-700/80 hover:border-cyan-400 transition-all duration-300 hover:scale-105">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-cyan-200/80 mt-6 flex items-center justify-center gap-2">
                    <span class="inline-block w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></span>
                    Gratis 5 variasi caption per hari • Gak pakai kartu kredit • Langsung pakai!
                </p>
            </div>
        </div>
    </div>

    <style>
        /* Circuit Animation */
        @keyframes circuit-pulse {
            0%, 100% {
                opacity: 0.3;
                stroke-width: 2;
            }
            50% {
                opacity: 1;
                stroke-width: 3;
            }
        }
        
        .circuit-line {
            animation: circuit-pulse 3s ease-in-out infinite;
        }
        
        .circuit-line:nth-child(2) {
            animation-delay: 0.5s;
        }
        
        .circuit-line:nth-child(4) {
            animation-delay: 1s;
        }
        
        .circuit-line:nth-child(6) {
            animation-delay: 1.5s;
        }
        
        @keyframes node-glow {
            0%, 100% {
                opacity: 0.5;
                r: 4;
            }
            50% {
                opacity: 1;
                r: 6;
                filter: drop-shadow(0 0 8px currentColor);
            }
        }
        
        .circuit-node {
            animation: node-glow 2s ease-in-out infinite;
        }
        
        /* Tech Chips */
        .tech-chip {
            position: absolute;
            width: 8px;
            height: 8px;
            background: linear-gradient(135deg, #06b6d4, #8b5cf6);
            border-radius: 2px;
            opacity: 0.4;
            animation: float-chip 8s ease-in-out infinite;
        }
        
        .tech-chip-1 {
            top: 15%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .tech-chip-2 {
            top: 25%;
            right: 15%;
            animation-delay: 2s;
        }
        
        .tech-chip-3 {
            bottom: 30%;
            left: 20%;
            animation-delay: 4s;
        }
        
        .tech-chip-4 {
            bottom: 20%;
            right: 25%;
            animation-delay: 6s;
        }
        
        @keyframes float-chip {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.4;
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 0.8;
            }
        }
        
        /* Blob Animation */
        @keyframes blob {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }
        
        .animate-blob {
            animation: blob 7s infinite;
        }
        
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        
        /* Gradient Text Animation */
        @keyframes gradient-x {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        .animate-gradient-x {
            background-size: 200% 200%;
            animation: gradient-x 3s ease infinite;
        }
    </style>

    <!-- Stats Section -->
    <div class="bg-gradient-to-r from-slate-900 to-blue-900 border-b border-cyan-500/20 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-cyan-400 mb-2">200+</div>
                    <div class="text-sm text-slate-300">Jenis Konten</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-cyan-400 mb-2">23</div>
                    <div class="text-sm text-slate-300">Platform Sosmed</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-cyan-400 mb-2">12</div>
                    <div class="text-sm text-slate-300">Industry Presets</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-cyan-400 mb-2">5</div>
                    <div class="text-sm text-slate-300">Bahasa Daerah</div>
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
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Fitur Lengkap untuk UMKM</h2>
                <p class="text-gray-600">Semua yang kamu butuhkan untuk jualan online & konten marketing</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-6 border-2 border-cyan-200 hover:border-cyan-400 transition-all">
                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🤖 AI Text Generator</h3>
                    <p class="text-sm text-gray-700 mb-3">Mode Simpel (6 pertanyaan) & Mode Advanced (200+ jenis konten) untuk semua kebutuhan marketing</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>✓ Quick Templates (15 jenis)</li>
                        <li>✓ Viral & Clickbait Content (20 jenis)</li>
                        <li>✓ Industry Presets (12 industri)</li>
                        <li>✓ Social Media (Instagram, TikTok, FB, LinkedIn)</li>
                        <li>✓ Marketplace (Shopee, Tokopedia, dll)</li>
                        <li>✓ Email & WhatsApp Marketing</li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border-2 border-purple-200 hover:border-purple-400 transition-all">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🖼️ Image Caption Generator</h3>
                    <p class="text-sm text-gray-700 mb-3">Upload foto produk, AI otomatis generate caption yang menarik!</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>✓ Upload foto produk</li>
                        <li>✓ AI detect objek & warna</li>
                        <li>✓ Caption single post</li>
                        <li>✓ Caption carousel (3 slide)</li>
                        <li>✓ Tips editing & filter</li>
                        <li>✓ ChatGPT gak bisa ini!</li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-xl p-6 border-2 border-green-200 hover:border-green-400 transition-all">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">📅 Bulk Content Generator</h3>
                    <p class="text-sm text-gray-700 mb-3">Generate 7-30 hari konten sekaligus dengan content calendar!</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>✓ Generate 7 atau 30 hari</li>
                        <li>✓ Auto-schedule posting time</li>
                        <li>✓ Smart daily themes</li>
                        <li>✓ Calendar view</li>
                        <li>✓ Export to CSV</li>
                        <li>✓ INSTANT! (< 1 detik)</li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 border-2 border-orange-200 hover:border-orange-400 transition-all">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🗣️ Bahasa UMKM + Daerah</h3>
                    <p class="text-sm text-gray-700 mb-3">AI otomatis pakai bahasa yang relate dengan target market kamu</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>✓ Auto "Kak", "Bun", "Gaes"</li>
                        <li>✓ Bahasa Jawa (Halus/Ngoko)</li>
                        <li>✓ Bahasa Sunda</li>
                        <li>✓ Bahasa Betawi</li>
                        <li>✓ Bahasa Minang</li>
                        <li>✓ Bahasa Bali</li>
                        <li>✓ Bahasa Batak</li>
                        <li>✓ Bahasa Madura</li>
                        <li>✓ Bahasa Bugis</li>
                        <li>✓ Bahasa Banjar</li>
                        <li>✓ Mix Bahasa (Indo + Daerah)</li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl p-6 border-2 border-pink-200 hover:border-pink-400 transition-all">
                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">📜 Caption History & ML</h3>
                    <p class="text-sm text-gray-700 mb-3">AI belajar dari caption kamu untuk hasil yang makin personal</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>✓ Semua caption tersimpan</li>
                        <li>✓ ML learning dari pattern</li>
                        <li>✓ Hasil makin personal</li>
                        <li>✓ Quality tracking</li>
                        <li>✓ Performance insights</li>
                        <li>✓ Makin sering pakai = makin bagus!</li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-6 border-2 border-indigo-200 hover:border-indigo-400 transition-all">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">💼 Brand Voice Saver</h3>
                    <p class="text-sm text-gray-700 mb-3">Save preferensi brand untuk generate lebih cepat & konsisten</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>✓ Save unlimited brand voices</li>
                        <li>✓ Set default brand voice</li>
                        <li>✓ Load dengan 1 klik</li>
                        <li>✓ Konsistensi brand</li>
                        <li>✓ Perfect untuk agency</li>
                        <li>✓ Multiple business support</li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-6 border-2 border-yellow-200 hover:border-yellow-400 transition-all">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">📊 Analytics & My Stats</h3>
                    <p class="text-sm text-gray-700 mb-3">Track performa caption & optimize strategy dengan data</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>✓ Track likes, comments, shares</li>
                        <li>✓ Auto-calculate engagement rate</li>
                        <li>✓ Platform comparison</li>
                        <li>✓ Category performance</li>
                        <li>✓ Export PDF & CSV</li>
                        <li>✓ Data-driven decisions</li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-6 border-2 border-teal-200 hover:border-teal-400 transition-all">
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🔄 Multiple Variations</h3>
                    <p class="text-sm text-gray-700 mb-3">Generate 1-20 variasi caption untuk A/B testing</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>✓ 1 caption: GRATIS</li>
                        <li>✓ 5 captions: Rp 5.000</li>
                        <li>✓ 10 captions: Rp 9.000</li>
                        <li>✓ 15 captions: Rp 12.000</li>
                        <li>✓ 20 captions: Rp 15.000</li>
                        <li>✓ Auto hashtag Indonesia</li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-xl p-6 border-2 border-violet-200 hover:border-violet-400 transition-all">
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">🎯 12 Industry Presets</h3>
                    <p class="text-sm text-gray-700 mb-3">AI yang paham industri spesifik dengan prompt khusus</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>✓ Fashion & Pakaian</li>
                        <li>✓ Makanan & Minuman</li>
                        <li>✓ Kecantikan & Skincare</li>
                        <li>✓ Jasa Printing & Fotografi</li>
                        <li>✓ Catering & TikTok Shop</li>
                        <li>✓ Dan 6 industri lainnya</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Open Clow Agent Section -->
    <div class="bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 py-20 relative overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-cyan-500 rounded-full mix-blend-screen filter blur-3xl animate-blob"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-500 rounded-full mix-blend-screen filter blur-3xl animate-blob animation-delay-2000"></div>
        </div>
        
        <!-- Tech Grid Pattern -->
        <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(6, 182, 212, 0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(6, 182, 212, 0.3) 1px, transparent 1px); background-size: 40px 40px;"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-3 px-6 py-3 bg-slate-800/60 backdrop-blur-sm border border-cyan-500/30 rounded-full mb-6">
                    <span class="text-3xl">🤖</span>
                    <span class="text-cyan-100 font-semibold">ENTERPRISE SOLUTION</span>
                </div>
                <h2 class="text-3xl sm:text-4xl md:text-6xl font-black text-white mb-6 leading-tight">
                    Open Clow Agent
                    <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-teal-400">AI Assistant Khusus Bisnis Kamu</span>
                </h2>
                <p class="text-xl md:text-2xl text-slate-300 mb-8 max-w-4xl mx-auto leading-relaxed">
                    Punya AI assistant pribadi yang paham 100% bisnis kamu. Dari customer service, content planning, sampai analisis kompetitor - semua otomatis!
                </p>
            </div>

            <!-- Pricing Cards -->
            <div class="grid md:grid-cols-2 gap-8 mb-16">
                <!-- Installation Package -->
                <div class="bg-slate-800/80 backdrop-blur-sm border-2 border-cyan-500/50 rounded-2xl p-8 hover:border-cyan-400 transition-all hover:scale-105">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">Paket Instalasi</h3>
                        <div class="text-4xl font-black text-cyan-400 mb-2">Rp 7.000.000</div>
                        <p class="text-cyan-200 text-sm">Sekali bayar + FREE maintenance selamanya</p>
                    </div>
                    <ul class="space-y-3 text-slate-300 mb-8">
                        <li class="flex items-center gap-3">
                            <span class="text-cyan-400">✓</span>
                            <span>Custom AI training sesuai bisnis kamu</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-cyan-400">✓</span>
                            <span>Integrasi dengan sistem existing</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-cyan-400">✓</span>
                            <span>Setup & konfigurasi lengkap</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-cyan-400">✓</span>
                            <span>Training team (2 sesi)</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-cyan-400">✓</span>
                            <span>Maintenance & update GRATIS</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-cyan-400">✓</span>
                            <span>24/7 technical support</span>
                        </li>
                    </ul>
                </div>

                <!-- Monthly Token Package -->
                <div class="bg-slate-800/80 backdrop-blur-sm border-2 border-blue-500/50 rounded-2xl p-8 hover:border-blue-400 transition-all hover:scale-105">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">Biaya Operasional</h3>
                        <div class="text-4xl font-black text-blue-400 mb-2">Rp 1.000.000</div>
                        <p class="text-blue-200 text-sm">Per bulan untuk 100.000 AI tokens</p>
                    </div>
                    <ul class="space-y-3 text-slate-300 mb-8">
                        <li class="flex items-center gap-3">
                            <span class="text-blue-400">✓</span>
                            <span>100.000 AI tokens per bulan</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-blue-400">✓</span>
                            <span>~3.000 interaksi AI per bulan</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-blue-400">✓</span>
                            <span>Unlimited users dalam tim</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-blue-400">✓</span>
                            <span>Real-time analytics & reporting</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-blue-400">✓</span>
                            <span>Auto-scaling sesuai kebutuhan</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-blue-400">✓</span>
                            <span>Priority support</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="mb-16">
                <h3 class="text-3xl font-bold text-white text-center mb-12">Fungsi Open Clow Agent untuk Bisnis Kamu</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="bg-slate-800/60 backdrop-blur-sm border border-cyan-500/30 rounded-xl p-6 hover:border-cyan-400 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-3">🤖 Customer Service AI</h4>
                        <p class="text-slate-300 text-sm">Auto-reply WhatsApp, Instagram DM, dan live chat. Jawab pertanyaan customer 24/7 dengan akurasi tinggi.</p>
                    </div>

                    <div class="bg-slate-800/60 backdrop-blur-sm border border-cyan-500/30 rounded-xl p-6 hover:border-cyan-400 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-3">📊 Sales Analytics AI</h4>
                        <p class="text-slate-300 text-sm">Analisis penjualan real-time, prediksi trend, dan rekomendasi strategi marketing berdasarkan data.</p>
                    </div>

                    <div class="bg-slate-800/60 backdrop-blur-sm border border-cyan-500/30 rounded-xl p-6 hover:border-cyan-400 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-3">✍️ Content Planning AI</h4>
                        <p class="text-slate-300 text-sm">Generate content calendar bulanan, ide konten viral, dan caption yang disesuaikan dengan performa terbaik.</p>
                    </div>

                    <div class="bg-slate-800/60 backdrop-blur-sm border border-cyan-500/30 rounded-xl p-6 hover:border-cyan-400 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-3">🎯 Competitor Monitor AI</h4>
                        <p class="text-slate-300 text-sm">Monitor kompetitor otomatis, analisis pricing strategy, dan alert ketika ada perubahan penting.</p>
                    </div>

                    <div class="bg-slate-800/60 backdrop-blur-sm border border-cyan-500/30 rounded-xl p-6 hover:border-cyan-400 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-3">💰 Lead Generation AI</h4>
                        <p class="text-slate-300 text-sm">Identifikasi prospek potensial, auto-follow up leads, dan nurturing customer journey otomatis.</p>
                    </div>

                    <div class="bg-slate-800/60 backdrop-blur-sm border border-cyan-500/30 rounded-xl p-6 hover:border-cyan-400 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-white mb-3">🔒 Business Intelligence AI</h4>
                        <p class="text-slate-300 text-sm">Dashboard eksekutif dengan insights mendalam, forecasting, dan rekomendasi strategis untuk growth.</p>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="text-center">
                <div class="bg-slate-800/80 backdrop-blur-sm border-2 border-cyan-500/50 rounded-2xl p-8 max-w-4xl mx-auto">
                    <h3 class="text-2xl font-bold text-white mb-4">Siap Punya AI Assistant Pribadi?</h3>
                    <p class="text-slate-300 mb-8">Konsultasi gratis untuk menentukan konfigurasi terbaik sesuai kebutuhan bisnis kamu</p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="https://wa.me/6281654932383?text=Halo,%20saya%20tertarik%20dengan%20Open%20Clow%20Agent.%20Bisa%20konsultasi%20gratis?" 
                           target="_blank"
                           class="group relative px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-lg font-bold rounded-xl overflow-hidden transition-all duration-300 hover:scale-105 shadow-2xl shadow-cyan-500/50 hover:shadow-cyan-400/60">
                            <span class="relative z-10 flex items-center justify-center gap-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                                🚀 Konsultasi Gratis via WhatsApp
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </a>
                    </div>
                    
                    <p class="text-sm text-slate-400 mt-6 flex items-center justify-center gap-2">
                        <span class="inline-block w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></span>
                        Konsultasi 100% gratis • No commitment • Response dalam 2 jam
                    </p>
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
            <a href="<?php echo e(route('register')); ?>" class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition font-semibold text-lg">
                🚀 Mulai Gratis Sekarang
            </a>
            <p class="text-sm text-blue-100 mt-4">Gak pakai kartu kredit • Langsung bisa pakai • 5 variasi gratis per hari</p>
        </div>
    </div>

    <!-- Latest Articles Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">📰 Artikel Terbaru</h2>
                <p class="text-lg text-gray-600">Inspirasi caption, quotes, dan tips terbaru untuk konten marketing kamu</p>
            </div>

            <?php
                $latestArticles = \App\Models\Article::latest('created_at')->take(5)->get();
            ?>

            <?php if($latestArticles->count() > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <?php $__currentLoopData = $latestArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('articles.show', $article->slug)); ?>" class="group bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg shadow-md hover:shadow-lg transition-all overflow-hidden border-2 border-blue-200 hover:border-blue-400">
                            <div class="p-6">
                                <div class="mb-3">
                                    <?php if($article->category): ?>
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                        <?php if($article->category === 'caption'): ?> bg-blue-100 text-blue-800
                                        <?php elseif($article->category === 'quote'): ?> bg-purple-100 text-purple-800
                                        <?php else: ?> bg-green-100 text-green-800
                                        <?php endif; ?>">
                                        <?php echo e(ucfirst($article->category)); ?>

                                    </span>
                                    <?php endif; ?>
                                    <?php if($article->industry): ?>
                                    <span class="inline-block ml-2 px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <?php echo e(ucfirst($article->industry)); ?>

                                    </span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition">
                                    <?php echo e($article->title); ?>

                                </h3>
                                <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                    <?php echo e(Str::limit(strip_tags($article->content), 120)); ?>

                                </p>
                                <div class="flex items-center justify-between">
                                    <span class="text-blue-600 font-semibold text-sm group-hover:translate-x-1 transition-transform inline-block">
                                        Baca Selengkapnya →
                                    </span>
                                    <span class="text-xs text-gray-400"><?php echo e($article->created_at->diffForHumans()); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="text-center">
                    <a href="<?php echo e(route('articles.index')); ?>" class="inline-block px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                        Lihat Semua Artikel →
                    </a>
                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <p class="text-gray-500 text-lg">Belum ada artikel tersedia. Cek kembali nanti!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="<?php echo e(asset('logo.png')); ?>" alt="Logo" class="w-8 h-8 rounded-lg">
                        <span class="text-white font-semibold">Noteds</span>
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
                        <li><a href="<?php echo e(route('register')); ?>" class="hover:text-white transition">Daftar Gratis</a></li>
                        <li><a href="<?php echo e(route('login')); ?>" class="hover:text-white transition">Login</a></li>
                        <?php if(auth()->guard()->check()): ?>
                            <li><a href="<?php echo e(route('dashboard')); ?>" class="hover:text-white transition">Dashboard</a></li>
                            <?php if(auth()->user()->role === 'client'): ?>
                                <li><a href="<?php echo e(route('ai.generator')); ?>" class="hover:text-white transition">AI Generator</a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo e(route('privacy-policy')); ?>" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="<?php echo e(route('terms-of-service')); ?>" class="hover:text-white transition">Terms of Service</a></li>
                        <li><a href="<?php echo e(route('refund-policy')); ?>" class="hover:text-white transition">Refund Policy</a></li>
                        <li><a href="<?php echo e(route('contact')); ?>" class="hover:text-white transition">Contact Us</a></li>
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
                        &copy; <?php echo e(date('Y')); ?> Noteds. All rights reserved.
                    </p>
                    <p class="text-sm text-gray-400">
                        Made with ❤️ for UMKM Indonesia
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Banner Popup -->
    <?php if (isset($component)) { $__componentOriginal411849fa56991479085a8247fc6f96f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal411849fa56991479085a8247fc6f96f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.banner-popup','data' => ['type' => 'landing']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banner-popup'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'landing']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal411849fa56991479085a8247fc6f96f5)): ?>
<?php $attributes = $__attributesOriginal411849fa56991479085a8247fc6f96f5; ?>
<?php unset($__attributesOriginal411849fa56991479085a8247fc6f96f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal411849fa56991479085a8247fc6f96f5)): ?>
<?php $component = $__componentOriginal411849fa56991479085a8247fc6f96f5; ?>
<?php unset($__componentOriginal411849fa56991479085a8247fc6f96f5); ?>
<?php endif; ?>

    <!-- 🤖 AI Assistant Widget -->
    <?php echo $__env->make('partials.ai-assistant-widget', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>

<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\welcome.blade.php ENDPATH**/ ?>