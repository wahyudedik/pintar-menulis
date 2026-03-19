@extends('layouts.client')

@section('title', 'Version History - ' . $content->title)

@section('content')
<div class="p-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('projects.index') }}" class="hover:text-blue-600">Projects</a>
        <span>/</span>
        <a href="{{ route('projects.show', $project) }}" class="hover:text-blue-600">{{ $project->business_name }}</a>
        <span>/</span>
        <a href="{{ route('projects.content.index', $project) }}" class="hover:text-blue-600">Content</a>
        <span>/</span>
        <a href="{{ route('projects.content.show', [$project, $content]) }}" class="hover:text-blue-600">{{ Str::limit($content->title, 30) }}</a>
        <span>/</span>
        <span class="text-gray-900">Version History</span>
    </div>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Version History</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $content->title }}</p>
        </div>
        <a href="{{ route('projects.content.show', [$project, $content]) }}"
           class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @forelse($versions as $version)
        <div class="flex items-start gap-4 p-4 border-b border-gray-100 last:border-0 hover:bg-gray-50">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                <span class="text-xs font-bold text-blue-600">v{{ $version->version_number }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    @php
                        $typeClasses = [
                            'created'              => 'bg-green-100 text-green-700',
                            'edited'               => 'bg-blue-100 text-blue-700',
                            'submitted'            => 'bg-yellow-100 text-yellow-700',
                            'submitted_for_review' => 'bg-yellow-100 text-yellow-700',
                            'approved'             => 'bg-green-100 text-green-700',
                            'rejected'             => 'bg-red-100 text-red-700',
                            'restored'             => 'bg-purple-100 text-purple-700',
                        ];
                        $typeClass = $typeClasses[$version->change_type] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <span class="px-2 py-0.5 {{ $typeClass }} text-xs font-medium rounded-full">
                        {{ ucfirst(str_replace('_', ' ', $version->change_type)) }}
                    </span>
                    @if($loop->first)
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Current</span>
                    @endif
                </div>
                @if($version->change_notes)
                <p class="text-sm text-gray-700">{{ $version->change_notes }}</p>
                @endif
                <p class="text-xs text-gray-500 mt-1">
                    oleh {{ $version->creator->name ?? 'System' }} · {{ $version->created_at->format('d M Y H:i') }}
                </p>
            </div>
            <div class="flex-shrink-0">
                @if(!$loop->first)
                <form action="{{ route('projects.content.restore-version', [$project, $content, $version]) }}" method="POST"
                      onsubmit="return confirm('Restore ke versi ini? Perubahan saat ini akan disimpan sebagai versi baru.')">
                    @csrf
                    <button type="submit"
                            class="px-3 py-1.5 border border-yellow-300 text-yellow-700 text-xs font-medium rounded-lg hover:bg-yellow-50 transition">
                        Restore
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <p class="text-gray-500 text-sm">Belum ada riwayat versi</p>
        </div>
        @endforelse
    </div>

    @if($versions->hasPages())
    <div class="mt-4">{{ $versions->links() }}</div>
    @endif
</div>
@endsection
