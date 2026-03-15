<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.png') }}">
    <title>@yield('title') | Noteds</title>
    <meta name="description" content="@yield('description', 'Noteds - AI Caption Generator untuk UMKM Indonesia')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('welcome') }}" class="flex items-center space-x-3">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="w-10 h-10 rounded-lg object-cover">
                        <span class="text-lg font-semibold text-gray-900">Noteds</span>
                    </a>
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

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg border border-gray-200 p-8">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="flex space-x-6 text-sm">
                    <a href="{{ route('privacy-policy') }}" class="hover:text-white transition">Privacy Policy</a>
                    <a href="{{ route('terms-of-service') }}" class="hover:text-white transition">Terms of Service</a>
                    <a href="{{ route('refund-policy') }}" class="hover:text-white transition">Refund Policy</a>
                    <a href="{{ route('contact') }}" class="hover:text-white transition">Contact</a>
                </div>
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} Noteds. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>

