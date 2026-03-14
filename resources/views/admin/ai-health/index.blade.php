@extends('layouts.admin')

@section('title', 'AI Health Monitor')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">AI Health Monitor</h1>
        <p class="text-sm text-gray-500 mt-1">Status dan performa AI model secara real-time</p>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Health Status</p>
            <p class="text-lg font-semibold capitalize
                {{ $healthStatus === 'healthy' ? 'text-green-600' : ($healthStatus === 'degraded' ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $healthStatus }}
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Connectivity</p>
            <p class="text-lg font-semibold capitalize
                {{ $connectivityStatus === 'connected' ? 'text-green-600' : 'text-red-600' }}">
                {{ $connectivityStatus }}
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Success Rate</p>
            <p class="text-lg font-semibold text-gray-900">{{ $successRate }}%</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Response Time</p>
            <p class="text-lg font-semibold text-gray-900">{{ $responseTime }}ms</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Today's Stats -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Statistik Hari Ini</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Sukses</span>
                    <span class="text-sm font-medium text-green-600">{{ $successCount }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Gagal</span>
                    <span class="text-sm font-medium text-red-600">{{ $failureCount }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Consecutive Failures</span>
                    <span class="text-sm font-medium {{ $consecutiveFailures > 3 ? 'text-red-600' : 'text-gray-900' }}">{{ $consecutiveFailures }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Last Success</span>
                    <span class="text-sm font-medium text-gray-900">{{ $lastSuccess }}</span>
                </div>
                @if($lastError)
                <div class="mt-3 p-3 bg-red-50 rounded-lg">
                    <p class="text-xs text-red-600 font-medium mb-1">Last Error</p>
                    <p class="text-xs text-red-500">{{ $lastError }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Model Stats -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Model Usage Stats</h2>
            @if(!empty($modelStats))
            <div class="space-y-2">
                @foreach($modelStats as $model => $stat)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                    <span class="text-xs font-mono text-gray-700">{{ $model }}</span>
                    <div class="flex items-center space-x-3 text-xs text-gray-500">
                        <span>RPM: {{ $stat['rpm_used'] ?? 0 }}/{{ $stat['rpm_limit'] ?? 0 }}</span>
                        <span>RPD: {{ $stat['rpd_used'] ?? 0 }}/{{ $stat['rpd_limit'] ?? 0 }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400">Belum ada data model.</p>
            @endif
        </div>
    </div>

    <!-- Notifications -->
    @if(!empty($notifications))
    <div class="mt-6 bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Notifikasi Terbaru</h2>
        <div class="space-y-2">
            @foreach(array_slice($notifications, 0, 10) as $notif)
            <div class="flex items-start space-x-3 py-2 border-b border-gray-100 last:border-0">
                <span class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0
                    {{ ($notif['type'] ?? '') === 'error' ? 'bg-red-500' : (($notif['type'] ?? '') === 'warning' ? 'bg-yellow-500' : 'bg-green-500') }}">
                </span>
                <div>
                    <p class="text-sm text-gray-700">{{ $notif['message'] ?? '' }}</p>
                    <p class="text-xs text-gray-400">{{ $notif['time'] ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="mt-6 flex space-x-3">
        <button onclick="forceCheck()" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
            Force Health Check
        </button>
        <button onclick="clearData()" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition">
            Clear Data
        </button>
    </div>
</div>

<script>
function forceCheck() {
    fetch('{{ route("admin.ai-health.force-check") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json()).then(d => alert(d.message));
}
function clearData() {
    if (!confirm('Yakin ingin menghapus semua health data?')) return;
    fetch('{{ route("admin.ai-health.clear-data") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json()).then(d => { alert(d.message); location.reload(); });
}
</script>
@endsection
