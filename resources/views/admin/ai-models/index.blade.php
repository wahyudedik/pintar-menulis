@extends('layouts.admin')

@section('title', 'AI Model Management')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">AI Model Management</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola dan pantau penggunaan AI model</p>
    </div>

    <!-- Current Model -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Model Aktif</h2>
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $currentModel ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">AI</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Active</span>
        </div>
    </div>

    <!-- Usage Stats -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Usage Statistics</h2>
        @if(!empty($usageStats))
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 text-xs text-gray-500 font-medium">Model</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">RPM Used</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">RPM Limit</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">RPD Used</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">RPD Limit</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usageStats as $model => $stat)
                    @if(!str_starts_with($model, '_'))
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="py-3 font-mono text-xs text-gray-700">{{ $model }}</td>
                        <td class="py-3 text-right text-gray-600">{{ $stat['rpm_used'] ?? 0 }}</td>
                        <td class="py-3 text-right text-gray-600">{{ $stat['rpm_limit'] ?? 0 }}</td>
                        <td class="py-3 text-right text-gray-600">{{ $stat['rpd_used'] ?? 0 }}</td>
                        <td class="py-3 text-right text-gray-600">{{ $stat['rpd_limit'] ?? 0 }}</td>
                        <td class="py-3 text-right">
                            @php $available = ($stat['available'] ?? true); @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $available ? 'Available' : 'Unavailable' }}
                            </span>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-gray-400">Belum ada data usage.</p>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex space-x-3">
        <button onclick="resetStats()" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition">
            Reset Stats
        </button>
    </div>
</div>

<script>
function resetStats() {
    if (!confirm('Reset semua usage stats?')) return;
    fetch('{{ route("admin.ai-models.reset-stats") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json()).then(d => { alert(d.message); location.reload(); });
}
</script>
@endsection
