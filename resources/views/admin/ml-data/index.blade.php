@extends('layouts.admin')

@section('title', 'ML Data Manager')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">ML Data Manager</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola cache data ML untuk analisis kompetitor</p>
        </div>
        <button onclick="cleanupData()" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
            Cleanup Old Data
        </button>
    </div>

    <!-- Stats Cards -->
    @if(!empty($stats))
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Total Profiles</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_profiles'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Cache Hits</p>
            <p class="text-2xl font-semibold text-green-600">{{ $stats['total_cache_hits'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">API Calls Saved</p>
            <p class="text-2xl font-semibold text-blue-600">{{ $stats['total_api_calls_saved'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Avg Quality Score</p>
            <p class="text-2xl font-semibold text-purple-600">{{ number_format($stats['avg_quality_score'] ?? 0, 1) }}</p>
        </div>
    </div>
    @endif

    <!-- Platform Performance -->
    @if($platformPerformance->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Platform Performance</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 text-xs text-gray-500 font-medium">Platform</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Profiles</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Avg Quality</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">API Saved</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Avg Hits</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($platformPerformance as $row)
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="py-3 capitalize font-medium text-gray-700">{{ $row->platform }}</td>
                        <td class="py-3 text-right text-gray-600">{{ $row->total_profiles }}</td>
                        <td class="py-3 text-right text-gray-600">{{ number_format($row->avg_quality, 1) }}</td>
                        <td class="py-3 text-right text-gray-600">{{ $row->total_savings }}</td>
                        <td class="py-3 text-right text-gray-600">{{ number_format($row->avg_hits, 1) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Recent Entries -->
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Recent Cache Entries</h2>
        @if($recentEntries->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 text-xs text-gray-500 font-medium">Username</th>
                        <th class="text-left py-2 text-xs text-gray-500 font-medium">Platform</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Quality</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Cache Hits</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Updated</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentEntries as $entry)
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="py-3 font-medium text-gray-700">{{ $entry->username }}</td>
                        <td class="py-3 capitalize text-gray-600">{{ $entry->platform }}</td>
                        <td class="py-3 text-right text-gray-600">{{ number_format($entry->data_quality_score ?? 0, 1) }}</td>
                        <td class="py-3 text-right text-gray-600">{{ $entry->cache_hit_count ?? 0 }}</td>
                        <td class="py-3 text-right text-gray-500 text-xs">{{ $entry->updated_at->diffForHumans() }}</td>
                        <td class="py-3 text-right">
                            <a href="{{ route('admin.ml-data.show', $entry) }}" class="text-xs text-purple-600 hover:underline">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-gray-400">Belum ada cache data.</p>
        @endif
    </div>
</div>

<script>
function cleanupData() {
    if (!confirm('Hapus data ML yang sudah lama?')) return;
    fetch('{{ route("admin.ml-data.cleanup") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json()).then(d => { alert(d.message); location.reload(); });
}
</script>
@endsection
