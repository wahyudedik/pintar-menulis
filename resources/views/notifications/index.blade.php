@php
    $layout = match(auth()->user()->role) {
        'client' => 'layouts.client',
        'operator' => 'layouts.operator',
        'admin' => 'layouts.admin',
        'guru' => 'layouts.guru',
        default => 'layouts.app-layout'
    };
@endphp

@extends($layout)

@section('title', 'Notifikasi')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Notifikasi</h1>
            <p class="text-sm text-gray-500 mt-1">
                @if($unreadCount > 0)
                    {{ $unreadCount }} notifikasi belum dibaca
                @else
                    Semua notifikasi sudah dibaca
                @endif
            </p>
        </div>
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="text-sm text-blue-600 hover:text-blue-700">
                Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    <div class="bg-white rounded-lg border border-gray-200">
        @if($notifications->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($notifications as $notification)
            <div class="p-4 {{ !$notification->is_read ? 'bg-blue-50' : '' }} hover:bg-gray-50 transition">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="font-medium text-gray-900 text-sm">{{ $notification->title }}</h3>
                            @if(!$notification->is_read)
                            <span class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-700 mb-2">{{ $notification->message }}</p>
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                            @if($notification->action_url)
                            <a href="{{ $notification->action_url }}" class="text-blue-600 hover:text-blue-700">
                                Lihat Detail →
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if(!$notification->is_read)
                        <form method="POST" action="{{ route('notifications.mark-read', $notification) }}">
                            @csrf
                            <button type="submit" class="p-2 text-gray-400 hover:text-blue-600 transition" title="Tandai dibaca">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition" title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $notifications->links() }}
        </div>
        @endif
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Tidak ada notifikasi</p>
        </div>
        @endif
    </div>
</div>
@endsection
