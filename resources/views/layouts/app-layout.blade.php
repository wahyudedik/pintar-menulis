<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.png') }}">
    <title>@yield('title', 'Smart Copy SMK')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('head')
    <style>
        [x-cloak] { display: none !important; }
        
        /* Tooltip Styles - Simple & Reliable */
        .tooltip-container {
            position: fixed;
            left: 72px;
            padding: 8px 12px;
            background: #1f2937;
            color: white;
            border-radius: 6px;
            font-size: 13px;
            white-space: nowrap;
            z-index: 99999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease;
        }
        .tooltip-container.show {
            opacity: 1;
        }
        .tooltip-container::before {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 6px solid transparent;
            border-right-color: #1f2937;
        }
        
        /* Hide scrollbar but keep functionality */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;  /* Chrome, Safari and Opera */
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Menu Button -->
        <button @click="sidebarOpen = !sidebarOpen" 
                class="lg:hidden fixed top-4 left-4 z-50 w-10 h-10 bg-white rounded-lg shadow-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50">
            <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg x-show="sidebarOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-cloak
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40">
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:static inset-y-0 left-0 z-40 w-16 bg-white border-r border-gray-200 flex flex-col items-center py-4 transition-transform duration-300 ease-in-out">
            <!-- Logo -->
            <div class="mb-4 flex-shrink-0">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-10 h-10 rounded-lg object-cover">
            </div>

            <!-- Menu Items (Scrollable) -->
            <div class="flex-1 flex flex-col space-y-1 w-full px-2 overflow-y-auto scrollbar-hide">
                @yield('sidebar-menu')
            </div>

            <!-- Bottom Items (Fixed) -->
            <div class="flex-shrink-0 flex flex-col space-y-1 w-full px-2 pt-2 border-t border-gray-200">
                <!-- Notifications -->
                <div x-data="notificationBell()" x-init="init()" class="relative">
                    <a href="{{ route('notifications.index') }}" 
                       class="tooltip flex items-center justify-center w-12 h-12 rounded-lg text-gray-600 hover:bg-gray-100 transition relative"
                       data-tooltip="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span x-show="unreadCount > 0" 
                              x-text="unreadCount > 9 ? '9+' : unreadCount" 
                              class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-medium">
                        </span>
                    </a>
                </div>

                <!-- Profile/Logout -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="tooltip flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 text-white hover:opacity-90 transition"
                            data-tooltip="{{ auth()->user()->name }}">
                        <span class="font-semibold text-sm">{{ substr(auth()->user()->name, 0, 2) }}</span>
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         x-cloak
                         class="absolute bottom-full left-full ml-2 mb-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto lg:ml-0">
            <!-- Mobile Header Spacer -->
            <div class="lg:hidden h-16"></div>
            
            @if(session('success'))
            <div class="mx-6 mt-4">
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-6 mt-4">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
    function notificationBell() {
        return {
            unreadCount: 0,
            
            init() {
                this.fetchUnreadCount();
                setInterval(() => this.fetchUnreadCount(), 30000);
            },
            
            fetchUnreadCount() {
                fetch('{{ route('notifications.unread-count') }}')
                    .then(res => res.json())
                    .then(data => this.unreadCount = data.count)
                    .catch(() => {});
            }
        }
    }

    // Tooltip Handler
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipEl = document.createElement('div');
        tooltipEl.className = 'tooltip-container';
        document.body.appendChild(tooltipEl);

        document.querySelectorAll('.tooltip').forEach(el => {
            el.addEventListener('mouseenter', function(e) {
                const text = this.getAttribute('data-tooltip');
                if (!text) return;

                const rect = this.getBoundingClientRect();
                tooltipEl.textContent = text;
                
                // Position tooltip vertically centered with the icon
                const iconCenterY = rect.top + (rect.height / 2);
                tooltipEl.style.top = iconCenterY + 'px';
                tooltipEl.style.transform = 'translateY(-50%)';
                
                // Small delay for smooth appearance
                setTimeout(() => tooltipEl.classList.add('show'), 10);
            });

            el.addEventListener('mouseleave', function() {
                tooltipEl.classList.remove('show');
            });
        });
    });
    </script>

    <!-- 🤖 AI Assistant Widget -->
    @include('partials.ai-assistant-widget')
</body>
</html>
