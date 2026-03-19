@extends('layouts.admin-nav')

@section('title', 'Detail Fitur - Feature Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.feature-analytics.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $info?->feature_label ?? $featureKey }}</h1>
            <p class="text-sm text-gray-500">{{ $info?->feature_category }} &middot; Analisis detail fitur</p>
        </div>
        <div class="ml-auto">
            <form method="GET">
                <select name="period" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    @foreach([7,14,30,60,90] as $d)
                    <option value="{{ $d }}" {{ $period == $d ? 'selected' : '' }}>{{ $d }} Hari</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Penggunaan</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalUses) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Unique Users</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $uniqueUsers }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Success Rate</p>
            <p class="text-3xl font-bold {{ $successRate >= 90 ? 'text-green-600' : ($successRate >= 70 ? 'text-yellow-600' : 'text-red-600') }} mt-1">{{ $successRate }}%</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Avg Response</p>
            <p class="text-3xl font-bold {{ $avgMs > 3000 ? 'text-red-600' : ($avgMs > 1500 ? 'text-yellow-600' : 'text-gray-900') }} mt-1">{{ $avgMs }}ms</p>
        </div>
    </div>

    {{-- Trend Chart --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Tren Penggunaan Harian</h2>
        <canvas id="trendChart" height="80"></canvas>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- By Package --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Penggunaan per Paket Langganan</h2>
            @forelse($byPackage as $pkg)
            @php $maxUses = $byPackage->max('uses'); @endphp
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-700">{{ $pkg->subscription_package ?? 'Tanpa Paket' }}</span>
                    <span class="font-semibold">{{ number_format($pkg->uses) }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $maxUses > 0 ? round($pkg->uses / $maxUses * 100) : 0 }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400">Belum ada data</p>
            @endforelse
        </div>

        {{-- Top Users --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Top Users Fitur Ini</h2>
            <div class="space-y-2">
                @forelse($topUsers as $i => $tu)
                <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400 w-4">{{ $i + 1 }}</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $tu->user?->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-400">{{ $tu->user?->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold">{{ $tu->uses }}x</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($tu->last_used)->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
new Chart(document.getElementById('trendChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: @json($trendDates),
        datasets: [
            {
                label: 'Penggunaan',
                data: @json($trendUses),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                tension: 0.4, fill: true, pointRadius: 3,
            },
            {
                label: 'Unique Users',
                data: @json($trendUsers),
                borderColor: '#10b981',
                backgroundColor: 'transparent',
                tension: 0.4, borderDash: [4,3], pointRadius: 3,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
        scales: { y: { beginAtZero: true }, x: { ticks: { maxTicksLimit: 10, font: { size: 10 } } } }
    }
});
</script>
@endpush

@endsection
