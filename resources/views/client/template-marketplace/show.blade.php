@extends('layouts.client')

@section('title', $template->title . ' - Template Marketplace')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('templates.index') }}" class="hover:text-blue-600">Template Marketplace</a>
        <span>/</span>
        <span class="text-gray-900">{{ $template->title }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Template Header -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            @if($template->is_premium)
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">PREMIUM</span>
                            @else
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">GRATIS</span>
                            @endif
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">{{ ucfirst($template->category) }}</span>
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ ucfirst($template->platform) }}</span>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $template->title }}</h1>
                        <p class="text-gray-600 mt-2">{{ $template->description }}</p>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <!-- Favorite Button -->
                        <button onclick="toggleFavorite({{ $template->id }})"
                                id="fav-btn"
                                class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50 transition {{ $template->is_favorited ? 'text-red-500' : 'text-gray-400' }}">
                            <svg class="w-5 h-5" fill="{{ $template->is_favorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                        @if(auth()->id() === $template->user_id)
                        <a href="{{ route('templates.edit', $template->id) }}"
                           class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50 transition text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex items-center gap-6 text-sm text-gray-500 pt-4 border-t border-gray-100">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        {{ number_format($template->rating_average, 1) }} ({{ $template->total_ratings }} rating)
                    </span>
                    <span>{{ number_format($template->usage_count) }} kali digunakan</span>
                    <span>{{ number_format($template->favorite_count ?? 0) }} favorit</span>
                    <span>Oleh <strong>{{ $template->user->name }}</strong></span>
                </div>
            </div>

            <!-- Template Content Preview -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Preview Template</h2>
                <div class="bg-gray-50 rounded-lg p-4 font-mono text-sm text-gray-700 whitespace-pre-wrap border border-gray-200">{{ $template->template_content }}</div>
                @if($template->format_instructions)
                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-xs font-semibold text-blue-700 mb-1">Petunjuk Penggunaan:</p>
                    <p class="text-sm text-blue-600">{{ $template->format_instructions }}</p>
                </div>
                @endif
            </div>

            <!-- Ratings & Reviews -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Rating & Ulasan</h2>

                @if(auth()->id() !== $template->user_id)
                <!-- Rate Form -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-sm font-medium text-gray-700 mb-3">Beri Rating</p>
                    <div class="flex gap-1 mb-3" id="star-rating">
                        @for($i = 1; $i <= 5; $i++)
                        <button onclick="setRating({{ $i }})" class="star-btn text-2xl text-gray-300 hover:text-yellow-400 transition" data-value="{{ $i }}">★</button>
                        @endfor
                    </div>
                    <textarea id="review-text" rows="2" placeholder="Tulis ulasan (opsional)..."
                              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-2"></textarea>
                    <button onclick="submitRating({{ $template->id }})"
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                        Kirim Rating
                    </button>
                </div>
                @endif

                @forelse($template->ratings as $rating)
                <div class="flex gap-3 py-3 border-b border-gray-100 last:border-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm flex-shrink-0">
                        {{ substr($rating->user->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-medium text-gray-900">{{ $rating->user->name }}</span>
                            <span class="text-yellow-500 text-sm">{{ str_repeat('★', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}</span>
                        </div>
                        @if($rating->review)
                        <p class="text-sm text-gray-600">{{ $rating->review }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">Belum ada ulasan</p>
                @endforelse
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <!-- Use Template Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                @if($template->is_premium && $template->price > 0)
                    <p class="text-2xl font-bold text-gray-900 mb-1">Rp {{ number_format($template->price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mb-4">Bayar sekali, gunakan selamanya</p>
                @else
                    <p class="text-2xl font-bold text-green-600 mb-1">GRATIS</p>
                    <p class="text-xs text-gray-500 mb-4">Langsung gunakan tanpa biaya</p>
                @endif
                <button onclick="useTemplate({{ $template->id }})"
                        class="w-full py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-sm">
                    Gunakan Template
                </button>
                <a href="{{ route('templates.export', $template->id) }}"
                   class="w-full mt-2 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm text-center block">
                    Export Template
                </a>
            </div>

            <!-- Template Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Info Template</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tone</span>
                        <span class="font-medium text-gray-900">{{ ucfirst($template->tone) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Platform</span>
                        <span class="font-medium text-gray-900">{{ ucfirst($template->platform) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Lisensi</span>
                        <span class="font-medium text-gray-900">{{ ucfirst($template->license_type ?? 'free') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dibuat</span>
                        <span class="font-medium text-gray-900">{{ $template->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedRating = 0;

function setRating(value) {
    selectedRating = value;
    document.querySelectorAll('.star-btn').forEach((btn, i) => {
        btn.classList.toggle('text-yellow-400', i < value);
        btn.classList.toggle('text-gray-300', i >= value);
    });
}

function submitRating(templateId) {
    if (!selectedRating) { alert('Pilih rating terlebih dahulu'); return; }
    fetch(`/templates/${templateId}/rate`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ rating: selectedRating, review: document.getElementById('review-text').value })
    }).then(r => r.json()).then(d => {
        if (d.success) { alert('Rating berhasil disimpan!'); location.reload(); }
        else alert(d.message);
    });
}

function toggleFavorite(templateId) {
    fetch(`/templates/${templateId}/favorite`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload();
    });
}

function useTemplate(templateId) {
    fetch(`/templates/${templateId}/use`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(r => r.json()).then(d => {
        if (d.success) {
            const content = d.template?.template_content;
            if (content) {
                navigator.clipboard.writeText(content).then(() => alert('Template disalin ke clipboard!'));
            }
        } else alert(d.message);
    });
}
</script>
@endsection
