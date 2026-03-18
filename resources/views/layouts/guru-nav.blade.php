<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">
                        Noteds
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                        Dashboard
                    </a>
                    <a href="{{ route('guru.training') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('guru.training*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                        ML Training
                    </a>
                    <a href="{{ route('guru.training.history') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('guru.training.history') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                        History
                    </a>
                    <a href="{{ route('guru.analytics') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('guru.analytics') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                        Analytics
                    </a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <x-notification-bell />
                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">Guru</span>
                <span class="text-gray-700">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-700">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
