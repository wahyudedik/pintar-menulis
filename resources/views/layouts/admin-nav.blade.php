<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Noteds') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-6">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600 whitespace-nowrap">{{ config('app.name', 'Noteds') }}</a>
                    <div class="hidden lg:flex items-center space-x-1">
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">Dashboard</a>
                        <a href="{{ route('admin.users') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm {{ request()->routeIs('admin.users*') ? 'bg-blue-50 text-blue-600' : '' }}">Users</a>

                        {{-- Packages & Subscriptions --}}
                        <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                            <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm flex items-center gap-1 {{ request()->routeIs('admin.packages*') || request()->routeIs('admin.subscriptions*') ? 'bg-blue-50 text-blue-600' : '' }}">
                                Paket <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                                <a href="{{ route('admin.packages') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Kelola Paket</a>
                                <a href="{{ route('admin.subscriptions') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Subscriptions</a>
                            </div>
                        </div>

                        {{-- Finance --}}
                        <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                            <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm flex items-center gap-1 {{ request()->routeIs('admin.payments*') || request()->routeIs('admin.reports*') || request()->routeIs('admin.withdrawals*') || request()->routeIs('admin.referrals*') ? 'bg-blue-50 text-blue-600' : '' }}">
                                Keuangan <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                                <a href="{{ route('admin.payments') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Payments</a>
                                <a href="{{ route('admin.reports') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Reports</a>
                                <a href="{{ route('admin.withdrawals') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Withdrawals</a>
                                <a href="{{ route('admin.referrals.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Referrals</a>
                            </div>
                        </div>

                        {{-- AI & ML --}}
                        <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                            <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm flex items-center gap-1 {{ request()->routeIs('admin.ai*') || request()->routeIs('admin.ml*') ? 'bg-blue-50 text-blue-600' : '' }}">
                                AI & ML <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                                <a href="{{ route('admin.ai-usage.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">AI Usage</a>
                                <a href="{{ route('admin.ai-health.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">AI Health</a>
                                <a href="{{ route('admin.ai-models.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">AI Models</a>
                                <a href="{{ route('admin.ml-analytics.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">ML Analytics</a>
                                <a href="{{ route('admin.ml-data.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">ML Data</a>
                            </div>
                        </div>
                        {{-- Analytics --}}
                        <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                            <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm flex items-center gap-1 {{ request()->routeIs('admin.feature-analytics*') ? 'bg-blue-50 text-blue-600' : '' }}">
                                Analytics <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                                <a href="{{ route('admin.feature-analytics.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">📊 Client Feature Analytics</a>
                            </div>
                        </div>
                        <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                            <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm flex items-center gap-1 {{ request()->routeIs('admin.banner*') || request()->routeIs('admin.ad-placements*') || request()->routeIs('admin.feedback*') || request()->routeIs('admin.guru-monitor*') || request()->routeIs('admin.whatsapp*') || request()->routeIs('admin.error-logs*') ? 'bg-blue-50 text-blue-600' : '' }}">
                                Lainnya <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                                <a href="{{ route('admin.banner-information.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Banners</a>
                                <a href="{{ route('admin.ad-placements.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Ad Placements</a>
                                <a href="{{ route('admin.feedback') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Feedback</a>
                                <a href="{{ route('admin.guru-monitor.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Guru Monitor</a>
                                <a href="{{ route('admin.whatsapp-analytics.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">WhatsApp Analytics</a>
                                <a href="{{ route('admin.payment-settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Settings</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('admin.error-logs.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 {{ request()->routeIs('admin.error-logs*') ? 'bg-red-50 text-red-600' : '' }}">🪵 Error Logs</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <x-notification-bell />
                    <span class="text-gray-700 text-sm hidden sm:block">{{ auth()->user()->name }}</span>
                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Admin</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-blue-600 text-sm">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    </div>
    @endif

    <main>
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
