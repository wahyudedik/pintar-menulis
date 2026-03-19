@extends('layouts.client')

@section('title', $content->title . ' - ' . $project->business_name)

@section('content')
<div class="p-6" x-data="contentShow()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('projects.show', $project) }}" class="hover:text-gray-700">{{ $project->business_name }}</a>
                <span>/</span>
                <a href="{{ route('projects.content.index', $project) }}" class="hover:text-gray-700">Konten</a>
                <span>/</span>
                <span class="text-gray-700 truncate max-w-xs">{{ $content->title }}</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900">{{ $content->title }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('projects.content.index', $project) }}"
               class="text-sm text-gray-600 border border-gray-300 px-3 py-2 rounded-lg hover:bg-gray-50 transition">
                ← Daftar Konten
            </a>
            <a href="{{ route('projects.show', $project) }}"
               class="text-sm text-gray-600 border border-gray-300 px-3 py-2 rounded-lg hover:bg-gray-50 transition">
                ← Project
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Content Card --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $typeLabels = ['caption'=>'Caption','article'=>'Artikel','ad_copy'=>'Iklan','email'=>'Email','product_desc'=>'Deskripsi Produk'];
                            $statusColors = ['draft'=>'bg-gray-100 text-gray-700','review'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700','published'=>'bg-blue-100 text-blue-700'];
                            $statusLabels = ['draft'=>'Draft','review'=>'Menunggu Review','approved'=>'Disetujui','rejected'=>'Ditolak','published'=>'Dipublikasi'];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            {{ $typeLabels[$content->content_type] ?? ucfirst($content->content_type) }}
                        </span>
                        @if($content->platform)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            {{ ucfirst($content->platform) }}
                        </span>
                        @endif
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$content->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statusLabels[$content->status] ?? ucfirst($content->status) }}
                        </span>
                    </div>

                    {{-- Actions Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-1 text-sm text-gray-600 border border-gray-300 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition">
                            Aksi
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                             class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10 py-1">
                            @if($canEdit)
                            <a href="{{ route('projects.content.edit', [$project, $content]) }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                ✏️ Edit
                            </a>
                            @endif
                            @if($content->status === 'draft' && $canEdit)
                            <form action="{{ route('projects.content.submit-review', [$project, $content]) }}" method="POST"
                                  onsubmit="return confirm('Kirim konten ini untuk review?')">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    📤 Kirim untuk Review
                                </button>
                            </form>
                            @endif
                            @if($content->status === 'review' && $canApprove)
                            <div class="border-t border-gray-100 my-1"></div>
                            <button @click="showApproveModal = true; open = false"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                ✅ Setujui
                            </button>
                            <button @click="showRejectModal = true; open = false"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                ❌ Tolak
                            </button>
                            @endif
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="{{ route('projects.content.versions', [$project, $content]) }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                🕐 Riwayat Versi
                            </a>
                            <button @click="copyContent(); open = false"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                📋 Salin Konten
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-5">
                    {{-- Content Body --}}
                    <div class="bg-gray-50 rounded-lg p-4 mb-4 whitespace-pre-wrap text-gray-800 text-sm leading-relaxed" id="contentDisplay">{{ $content->content }}</div>
                    <p class="text-xs text-gray-400">{{ strlen($content->content) }} karakter</p>

                    {{-- Tags --}}
                    @if($content->tags)
                    <div class="mt-4">
                        <p class="text-xs font-medium text-gray-500 mb-2">Tags:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach(explode(',', $content->tags) as $tag)
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Notes --}}
                    @if($content->notes)
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                        <p class="text-xs font-medium text-blue-700 mb-1">Catatan:</p>
                        <p class="text-sm text-blue-800 whitespace-pre-wrap">{{ $content->notes }}</p>
                    </div>
                    @endif

                    {{-- Review Notes --}}
                    @if($content->review_notes)
                    <div class="mt-4 p-3 {{ $content->status === 'rejected' ? 'bg-red-50 border border-red-100' : 'bg-green-50 border border-green-100' }} rounded-lg">
                        <p class="text-xs font-medium {{ $content->status === 'rejected' ? 'text-red-700' : 'text-green-700' }} mb-1">
                            {{ $content->status === 'rejected' ? 'Alasan Penolakan:' : 'Catatan Reviewer:' }}
                        </p>
                        <p class="text-sm {{ $content->status === 'rejected' ? 'text-red-800' : 'text-green-800' }}">{{ $content->review_notes }}</p>
                        @if($content->reviewer)
                        <p class="text-xs text-gray-500 mt-1">— {{ $content->reviewer->name }}, {{ $content->reviewed_at?->format('d M Y H:i') }}</p>
                        @endif
                    </div>
                    @endif

                    {{-- Meta --}}
                    <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-4 text-xs text-gray-500">
                        <span>Dibuat oleh: <span class="font-medium text-gray-700">{{ $content->creator->name }}</span></span>
                        <span>{{ $content->created_at->format('d M Y \p\u\k\u\l H:i') }}</span>
                        @if($content->updated_at->ne($content->created_at))
                        <span>Diperbarui: {{ $content->updated_at->format('d M Y H:i') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Comments --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Komentar ({{ $content->comments->count() }})</h3>
                </div>
                <div class="p-5">
                    {{-- Add Comment --}}
                    <form action="{{ route('projects.content.comments.store', [$project, $content]) }}" method="POST" class="mb-6">
                        @csrf
                        <input type="hidden" name="type" value="comment">
                        <textarea name="comment" rows="3" required
                                  placeholder="Tambahkan komentar atau feedback..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('comment') border-red-400 @enderror">{{ old('comment') }}</textarea>
                        @error('comment')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        <button type="submit"
                                class="mt-2 bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Tambah Komentar
                        </button>
                    </form>

                    {{-- Comments List --}}
                    @forelse($content->comments->sortByDesc('created_at') as $comment)
                    <div class="flex gap-3 pb-4 mb-4 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-semibold shrink-0">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-gray-800">{{ $comment->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                @if($comment->is_resolved)
                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-xs">Selesai</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $comment->comment }}</p>
                            @if(!$comment->is_resolved && (auth()->id() === $comment->user_id || $canApprove))
                            <form action="{{ route('projects.content.comments.resolve', [$project, $content, $comment]) }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit" class="text-xs text-gray-400 hover:text-green-600 transition">
                                    ✓ Tandai selesai
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400 text-sm">
                        Belum ada komentar. Jadilah yang pertama memberi feedback!
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">

            {{-- Workflow Status --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Status Alur Kerja</h3>
                @php
                    $steps = [
                        ['key' => 'draft',     'label' => 'Draft',     'desc' => 'Konten dibuat'],
                        ['key' => 'review',    'label' => 'Review',    'desc' => 'Menunggu review'],
                        ['key' => 'approved',  'label' => 'Disetujui', 'desc' => 'Siap dipublikasi'],
                        ['key' => 'published', 'label' => 'Dipublikasi','desc' => 'Konten live'],
                    ];
                    $statusOrder = ['draft'=>0,'review'=>1,'approved'=>2,'rejected'=>2,'published'=>3];
                    $currentOrder = $statusOrder[$content->status] ?? 0;
                @endphp
                <div class="space-y-3">
                    @foreach($steps as $i => $step)
                    @php
                        $stepOrder = $statusOrder[$step['key']] ?? $i;
                        $isActive = $content->status === $step['key'] || ($step['key'] === 'approved' && $content->status === 'rejected');
                        $isDone = $stepOrder < $currentOrder;
                    @endphp
                    <div class="flex items-center gap-3 {{ (!$isActive && !$isDone) ? 'opacity-40' : '' }}">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-sm
                            {{ $isActive ? 'bg-blue-600 text-white' : ($isDone ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500') }}">
                            {{ $isDone ? '✓' : ($i + 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $step['label'] }}</p>
                            <p class="text-xs text-gray-500">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Version History --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Versi Terbaru</h3>
                    <a href="{{ route('projects.content.versions', [$project, $content]) }}"
                       class="text-xs text-blue-600 hover:underline">Lihat semua</a>
                </div>
                @forelse($versions->take(5) as $version)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-700">v{{ $version->version_number }}</p>
                        <p class="text-xs text-gray-400">{{ $version->created_at->format('d M, H:i') }}</p>
                        @if($version->change_notes)
                        <p class="text-xs text-gray-500 truncate max-w-32">{{ $version->change_notes }}</p>
                        @endif
                    </div>
                    @if($canEdit)
                    <form action="{{ route('projects.content.restore-version', [$project, $content, $version]) }}" method="POST"
                          onsubmit="return confirm('Restore versi ini? Konten saat ini akan disimpan sebagai versi baru.')">
                        @csrf
                        <button type="submit" class="text-xs text-blue-600 border border-blue-300 px-2 py-1 rounded hover:bg-blue-50 transition">
                            Restore
                        </button>
                    </form>
                    @endif
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-3">Belum ada riwayat versi</p>
                @endforelse
            </div>

        </div>
    </div>

    {{-- Approve Modal --}}
    <div x-show="showApproveModal" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showApproveModal = false">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Setujui Konten</h3>
            <form action="{{ route('projects.content.approve', [$project, $content]) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300"
                              placeholder="Tambahkan catatan approval..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                        ✅ Setujui Konten
                    </button>
                    <button type="button" @click="showApproveModal = false"
                            class="flex-1 border border-gray-300 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div x-show="showRejectModal" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showRejectModal = false">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tolak Konten</h3>
            <form action="{{ route('projects.content.reject', [$project, $content]) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="notes" rows="3" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                              placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition text-sm font-medium">
                        ❌ Tolak Konten
                    </button>
                    <button type="button" @click="showRejectModal = false"
                            class="flex-1 border border-gray-300 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Toast --}}
    <div x-show="toast" x-cloak x-transition
         class="fixed bottom-4 right-4 bg-gray-900 text-white text-sm px-4 py-2 rounded-lg shadow-lg z-50">
        <span x-text="toastMsg"></span>
    </div>
</div>

<script>
function contentShow() {
    return {
        showApproveModal: false,
        showRejectModal: false,
        toast: false,
        toastMsg: '',

        copyContent() {
            const text = document.getElementById('contentDisplay').innerText;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => this.showToast('Konten disalin!'));
            } else {
                const ta = document.createElement('textarea');
                ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
                document.body.appendChild(ta); ta.focus(); ta.select();
                document.execCommand('copy'); ta.remove();
                this.showToast('Konten disalin!');
            }
        },

        showToast(msg) {
            this.toastMsg = msg;
            this.toast = true;
            setTimeout(() => this.toast = false, 2500);
        }
    }
}
</script>
@endsection
