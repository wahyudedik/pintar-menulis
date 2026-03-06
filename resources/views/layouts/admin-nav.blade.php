<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Smart Copy SMK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">Smart Copy SMK</a>
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">Dashboard</a>
                        <a href="{{ route('admin.users') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md {{ request()->routeIs('admin.users*') ? 'bg-blue-50 text-blue-600' : '' }}">Users</a>
                        <a href="{{ route('admin.packages') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md {{ request()->routeIs('admin.packages*') ? 'bg-blue-50 text-blue-600' : '' }}">Packages</a>
                        <a href="{{ route('admin.reports') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md {{ request()->routeIs('admin.reports') ? 'bg-blue-50 text-blue-600' : '' }}">Reports</a>
                        <a href="{{ route('admin.payments') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md {{ request()->routeIs('admin.payments') ? 'bg-blue-50 text-blue-600' : '' }}">Payments</a>
                        <a href="{{ route('admin.payment-settings') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md {{ request()->routeIs('admin.payment-settings*') ? 'bg-blue-50 text-blue-600' : '' }}">Settings</a>
                        <a href="{{ route('admin.withdrawals') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md {{ request()->routeIs('admin.withdrawals*') ? 'bg-blue-50 text-blue-600' : '' }}">Withdrawals</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <x-notification-bell />
                    <span class="text-gray-700">{{ auth()->user()->name }}</span>
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">Admin</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-blue-600">Logout</button>
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
</body>
</html>
