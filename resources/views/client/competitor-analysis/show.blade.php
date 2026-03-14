@extends('layouts.client')

@section('title', 'Detail Kompetitor - ' . $competitor->username)

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('competitor-analysis.index') }}" class="hover:text-purple-600">Competitor Analysis</a>
            <span>/</span>
            <span class="text-gray-900">{{ $competitor->username }}</span>
        </div>
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-2xl">
                    {{ strtoupper(substr($competitor->username, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">{{ $competitor->username }}</h1>
                    <div class="flex items-center space-x-3 mt-1">
                        <span class="text-sm text-gray-500 capitalize">{{ $competitor->platform }}</span>
                        @if($competitor->category)
                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded">{{ $competitor->category }}</span>
                        @endif
                        <span class="px-2 py-1 text-xs rounded-full {{ $competitor->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $competitor->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <form action="{{ route('competitor-analysis.refresh', $competitor) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </form>
                <form action="{{ route('competitor-analysis.toggle-active', $competitor) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        {{ $competitor->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <form action="{{ route('competitor-analysis.destroy', $competitor) }}" method="POST" class="inline" 
                      onsubmit="return confirm('Yakin ingin menghapus kompetitor ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-sm text-green-800">{!! session('success') !!}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-800">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Analysis Status -->
    @if(!$latestSummary && $competitor->created_at->diffInMinutes(now()) < 15)
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <div>
                <h3 class="text-lg font-semibold text-blue-900">🤖 AI sedang menganalisis kompetitor...</h3>
                <p class="text-sm text-blue-700 mt-1">Analisis lengkap berjalan di background. Refresh halaman dalam 2-3 menit untuk melihat hasil.</p>
                <div class="mt-3 flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                        <span class="text-xs text-blue-600">Menganalisis konten</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
                        <span class="text-xs text-blue-600">Mencari pola</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-blue-300 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                        <span class="text-xs text-blue-600">Mengidentifikasi peluang</span>
                    </div>
                </div>
                <div class="mt-3">
                    <button onclick="location.reload()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        🔄 Refresh Halaman
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto refresh every 60 seconds if analysis is still running
        setTimeout(function() {
            if (!document.querySelector('[data-analysis-complete]')) {
                location.reload();
            }
        }, 60000);
    </script>
    @endif

    <!-- Stats Overview -->
    @if($latestSummary)
    <div data-analysis-complete class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600">Total Posts</p>
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $latestSummary->total_posts }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600">Avg Engagement</p>
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($latestSummary->avg_engagement_rate, 1) }}%</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600">Posting Frequency</p>
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $latestSummary->posting_frequency ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">posts/week</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600">Best Time</p>
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $latestSummary->best_posting_time ?? 'N/A' }}</p>
        </div>
    </div>
    @else
    <!-- Empty State - No Analysis Yet -->
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center mb-6">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Data Analisis</h3>
        <p class="text-gray-600 mb-4">Kompetitor baru ditambahkan. Analisis AI sedang berjalan atau belum dimulai.</p>
        <div class="flex items-center justify-center space-x-3">
            <form action="{{ route('competitor-analysis.refresh', $competitor) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Mulai Analisis</span>
                </button>
            </form>
            <a href="{{ route('competitor-analysis.index') }}" 
               class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Kembali ke Daftar
            </a>
        </div>
    </div>
    @endif

    <!-- Content Tabs -->
    @if($latestSummary || $competitor->patterns->isNotEmpty() || $recentPosts->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200" x-data="{ tab: 'patterns' }">
        <!-- Tab Headers -->
        <div class="border-b border-gray-200">
            <div class="flex space-x-1 p-2">
                <button @click="tab = 'patterns'" 
                        :class="tab === 'patterns' ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    📊 Patterns
                </button>
                <button @click="tab = 'top-content'" 
                        :class="tab === 'top-content' ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    🔥 Top Content
                </button>
                <button @click="tab = 'content-gaps'" 
                        :class="tab === 'content-gaps' ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    💡 Content Ideas
                </button>
                <button @click="tab = 'recent-posts'" 
                        :class="tab === 'recent-posts' ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    📝 Recent Posts
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Patterns Tab -->
            <div x-show="tab === 'patterns'" x-cloak>
                @if($competitor->patterns->isEmpty())
                <p class="text-center text-gray-500 py-8">Belum ada data pattern</p>
                @else
                <div class="space-y-4">
                    @foreach($competitor->patterns->take(10) as $pattern)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded font-medium">
                                {{ ucfirst(str_replace('_', ' ', $pattern->pattern_type)) }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $pattern->analysis_date->format('d M Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-700">{{ $pattern->insights ?? 'No insights available' }}</p>
                        @if($pattern->pattern_data)
                        <div class="mt-2 text-xs text-gray-600">
                            <pre class="bg-white p-2 rounded border border-gray-200 overflow-x-auto">{{ json_encode($pattern->pattern_data, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Top Content Tab -->
            <div x-show="tab === 'top-content'" x-cloak>
                @if($topPosts->isEmpty())
                <p class="text-center text-gray-500 py-8">Belum ada data top content</p>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($topPosts as $topContent)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-start justify-between mb-2">
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded font-medium">
                                #{{ $topContent->rank }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $topContent->post->posted_at->format('d M Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-900 mb-2 line-clamp-3">{{ $topContent->post->caption }}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-600">
                            <span>❤️ {{ number_format($topContent->post->likes_count) }}</span>
                            <span>💬 {{ number_format($topContent->post->comments_count) }}</span>
                            <span>📊 {{ number_format($topContent->post->engagement_rate, 1) }}%</span>
                        </div>
                        @if($topContent->success_factors)
                        <div class="mt-2 p-2 bg-white rounded border border-gray-200">
                            <p class="text-xs text-gray-600">{{ $topContent->success_factors }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Content Gaps Tab -->
            <div x-show="tab === 'content-gaps'" x-cloak>
                @if($competitor->contentGaps->isEmpty())
                <p class="text-center text-gray-500 py-8">Belum ada content gap teridentifikasi</p>
                @else
                <div class="space-y-4">
                    @foreach($competitor->contentGaps as $gap)
                    <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-purple-600 text-white text-xs rounded font-medium">
                                    Priority: {{ $gap->priority }}
                                </span>
                                <span class="px-2 py-1 bg-white text-gray-700 text-xs rounded">
                                    {{ ucfirst($gap->gap_type) }}
                                </span>
                            </div>
                            @if(!$gap->is_implemented)
                            <form action="{{ route('competitor-analysis.gap.implement', $gap) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-purple-600 hover:text-purple-700 font-medium">
                                    ✓ Mark as Used
                                </button>
                            </form>
                            @endif
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-1">{{ $gap->gap_title }}</h4>
                        <p class="text-sm text-gray-700 mb-2">{{ $gap->gap_description }}</p>
                        @if($gap->suggested_content)
                        <div class="mt-2 p-3 bg-white rounded border border-purple-200">
                            <p class="text-xs text-purple-700 font-medium mb-1">💡 Suggested Content:</p>
                            @if(is_array($gap->suggested_content))
                                @foreach($gap->suggested_content as $suggestion)
                                <p class="text-sm text-gray-700 mb-1">• {{ $suggestion }}</p>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-700">{{ $gap->suggested_content }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Recent Posts Tab -->
            <div x-show="tab === 'recent-posts'" x-cloak>
                @if($recentPosts->isEmpty())
                <p class="text-center text-gray-500 py-8">Belum ada data posting</p>
                @else
                <div class="space-y-4">
                    @foreach($recentPosts as $post)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-500">{{ $post->posted_at->format('d M Y H:i') }}</span>
                            <div class="flex items-center space-x-3 text-xs text-gray-600">
                                <span>❤️ {{ number_format($post->likes_count) }}</span>
                                <span>💬 {{ number_format($post->comments_count) }}</span>
                                <span>📊 {{ number_format($post->engagement_rate, 1) }}%</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-900 mb-2">{{ $post->caption }}</p>
                        @if($post->hashtags)
                        <div class="flex flex-wrap gap-1">
                            @foreach($post->hashtags as $hashtag)
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">{{ $hashtag }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

@push('head')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endsection
