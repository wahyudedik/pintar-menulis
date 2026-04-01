@extends('layouts.client')

@section('title', 'Explore — Inspirasi Caption')

@section('content')
<div class="p-6" x-data="{ likedMap: {} }">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">🌟 Explore</h1>
            <p class="text-sm text-gray-500 mt-1">Inspirasi caption dari kreator lain — like, copy, dan adaptasi untuk bisnis kamu</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- Main content --}}
        <div class="lg:col-span-3">

            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-2 mb-5">
                <a href="{{ route('explore.index', ['sort' => 'latest', 'platform' => $platform, 'category' => $category]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-medium transition {{ $sort === 'latest' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    🕐 Terbaru
                </a>
                <a href="{{ route('explore.index', ['sort' => 'top_rated', 'platform' => $platform, 'category' => $category]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-medium transition {{ $sort === 'top_rated' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    ⭐ Rating Tertinggi
                </a>
                <a href="{{ route('explore.index', ['sort' => 'most_liked', 'platform' => $platform, 'category' => $category]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-medium transition {{ $sort === 'most_liked' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    ❤️ Paling Disukai
                </a>

                <span class="text-gray-300 mx-1">|</span>

                <a href="{{ route('explore.index', ['sort' => $sort, 'category' => $category]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-medium transition {{ !$platform ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua Platform
                </a>
                @foreach(['instagram' => '📱 Instagram', 'tiktok' => '🎵 TikTok', 'facebook' => '👥 Facebook', 'shopee' => '🛒 Shopee'] as $p => $label)
                <a href="{{ route('explore.index', ['sort' => $sort, 'platform' => $p, 'category' => $category]) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-medium transition {{ $platform === $p ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>

            {{-- Caption cards --}}
            @if($captions->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($captions as $caption)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition">
                    {{-- Author --}}
                    <div class="flex items-center gap-2.5 px-4 py-3 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            @if($caption->user?->avatar)
                                <img src="{{ $caption->user->avatar }}" class="w-8 h-8 rounded-full object-cover" alt="">
                            @else
                                {{ strtoupper(substr($caption->user?->name ?? '?', 0, 1)) }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $caption->user?->name ?? 'Anonim' }}</p>
                            <p class="text-xs text-gray-400">{{ $caption->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            @if($caption->platform)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $caption->platform === 'instagram' ? 'bg-pink-100 text-pink-700' : '' }}
                                {{ $caption->platform === 'tiktok' ? 'bg-gray-900 text-white' : '' }}
                                {{ $caption->platform === 'facebook' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $caption->platform === 'shopee' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ !in_array($caption->platform, ['instagram','tiktok','facebook','shopee']) ? 'bg-gray-100 text-gray-600' : '' }}
                            ">{{ ucfirst($caption->platform) }}</span>
                            @endif
                            @if($caption->rating)
                            <span class="text-xs text-yellow-600 font-medium">⭐ {{ $caption->rating }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Caption text --}}
                    <div class="px-4 py-3">
                        <p class="text-sm text-gray-800 leading-relaxed line-clamp-5">{{ $caption->caption_text }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between px-4 py-2.5 border-t border-gray-100 bg-gray-50">
                        <button @click="
                            fetch('{{ route('caption.like', $caption) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } })
                            .then(r => r.json()).then(d => { likedMap[{{ $caption->id }}] = d.liked; $el.querySelector('.like-count').textContent = d.likes_count; })
                        " class="flex items-center gap-1 text-xs font-medium transition"
                           :class="likedMap[{{ $caption->id }}] ? 'text-red-500' : 'text-gray-500 hover:text-red-500'">
                            <span x-text="likedMap[{{ $caption->id }}] ? '❤️' : '🤍'"></span>
                            <span class="like-count">{{ $caption->likes_count }}</span>
                        </button>

                        <button onclick="
                            const t = {{ json_encode($caption->caption_text) }};
                            navigator.clipboard ? navigator.clipboard.writeText(t).then(() => { this.textContent = '✓ Tersalin!'; setTimeout(() => { this.textContent = '📋 Copy'; }, 2000); }) : null;
                        " class="text-xs font-medium text-gray-500 hover:text-blue-600 transition">
                            📋 Copy
                        </button>

                        <a href="https://wa.me/?text={{ urlencode($caption->caption_text) }}" target="_blank"
                           class="text-xs font-medium text-gray-500 hover:text-green-600 transition">
                            💬 Share WA
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $captions->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-16">
                <div class="text-5xl mb-3">🌟</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum ada caption yang di-share</h3>
                <p class="text-sm text-gray-500 mb-4">Jadilah yang pertama! Generate caption lalu share ke Explore.</p>
                <a href="{{ route('ai.generator') }}" class="inline-block px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    🚀 Generate Caption
                </a>
            </div>
            @endif
        </div>

        {{-- Sidebar: Leaderboard --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 p-5 sticky top-6">
                <h3 class="font-bold text-gray-900 mb-4">🏆 Top Creator Bulan Ini</h3>

                @if($leaderboard->count() > 0)
                <div class="space-y-3">
                    @foreach($leaderboard as $idx => $creator)
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-bold w-6 text-center flex-shrink-0
                            {{ $idx === 0 ? 'text-yellow-500' : ($idx === 1 ? 'text-gray-400' : ($idx === 2 ? 'text-orange-500' : 'text-gray-300')) }}">
                            {{ $idx < 3 ? ['🥇','🥈','🥉'][$idx] : ($idx + 1) }}
                        </span>
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            @if($creator->avatar)
                                <img src="{{ $creator->avatar }}" class="w-8 h-8 rounded-full object-cover" alt="">
                            @else
                                {{ strtoupper(substr($creator->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $creator->name }}</p>
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <span>{{ $creator->shared_count }} shared</span>
                                <span>⭐ {{ $creator->avg_rating }}</span>
                                <span>❤️ {{ $creator->total_likes }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400 text-center py-4">Belum ada data bulan ini</p>
                @endif

                <div class="mt-5 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 leading-relaxed">
                        💡 Share caption terbaik kamu ke Explore untuk muncul di leaderboard dan menginspirasi kreator lain!
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
