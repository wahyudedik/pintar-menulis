<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.png') }}">
    <title>{{ config('app.name', 'Smart Copy SMK') }}</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Smart Copy SMK - AI Caption Generator untuk UMKM Indonesia">
    <meta name="keywords" content="caption generator, AI, UMKM, digital marketing">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="{{ config('app.name', 'Smart Copy SMK') }}">
    <meta property="og:description" content="Smart Copy SMK - AI Caption Generator untuk UMKM Indonesia">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    
    <!-- Additional head content from child views -->
    @yield('head')
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="w-10 h-10 rounded-lg object-cover">
                    <a href="/" class="text-lg font-semibold text-gray-900">Smart Copy SMK</a>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('articles.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition">Artikel</a>
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

    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Brand Section -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="w-8 h-8 rounded-lg object-cover">
                        <span class="font-semibold text-white">Smart Copy SMK</span>
                    </div>
                    <p class="text-sm text-gray-400">Platform AI Caption Generator khusus UMKM Indonesia. Buat caption jualan yang bikin closing!</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-semibold text-white mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('articles.index') }}" class="text-gray-400 hover:text-white transition">Daftar Gratis</a></li>
                        <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition">Login</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Fitur</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Pricing</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="font-semibold text-white mb-4">Legal</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Refund Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Jadi Operator -->
                <div>
                    <h3 class="font-semibold text-white mb-4">Jadi Operator</h3>
                    <p class="text-sm text-gray-400 mb-4">Punya skill copywriting? Gabung jadi operator caption freelance dan dapatkan penghasilan tambahan!</p>
                    <a href="https://wa.me/62" target="_blank" class="inline-flex items-center space-x-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421-7.403h-.004a9.87 9.87 0 00-9.746 9.798c0 2.734.732 5.41 2.124 7.738L.929 23.5l8.272-2.737c2.194 1.156 4.653 1.77 7.297 1.77 9.933 0 18.018-8.087 18.018-18.018 0-4.709-1.827-9.145-5.14-12.458-3.312-3.312-7.748-5.139-12.458-5.139"/></svg>
                        <span>Daftar via WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} Smart Copy SMK. All rights reserved.</p>
                <p class="mt-2">Made with ❤️ for UMKM Indonesia</p>
            </div>
        </div>
    </footer>
</body>
</html>