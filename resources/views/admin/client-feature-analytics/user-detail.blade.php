@extends('layouts.admin-nav')

@section('title', 'Detail User - Feature Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.feature-analytics.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex items-center gap-3">
            @if($user->avatar)
                <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : Storage::url($user->avatar) }}" class="w-10 h-10 rounded-full object-cover">
            @else
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>
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

    {{-- Summary --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Aksi</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalUses) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Fitur Digunakan</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $featuresUsed }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Terakhir Aktif</p>
            <p class="text-lg font-bold text-gray-900 mt-1">{{ $lastActive ? \Carbon\Carbon::parse($lastActive)->diffForHumans() : 'Belum ada' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Activity Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Aktivitas Harian</h2>
            <canvas id="actChart" height="100"></canvas>
        </div>
        {{-- Category Pie --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Distribusi Kategori</h2>
            <canvas id="catChart" height="200"></canvas>
        </div>
    </div>

    {{-- Feature breakdown --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Rincian Penggunaan Fitur</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-2 text-xs text-gray-500 font-medium">Fitur</th>
                        <th class="text-left py-2 text-xs text-gray-500 font-medium">Kategori</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Penggunaan</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Avg ms</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byFeature as $f)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2.5">
                            <a href="{{ route('admin.feature-analytics.feature', ['featureKey' => $f->feature_key, 'period' => $period]) }}"
                               class="font-medium text-blue-600 hover:underline">{{ $f->feature_label }}</a>
                        </td>
                        <td class="py-2.5"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $f->feature_category }}</span></td>
                        <td class="py-2.5 text-right font-semibold">{{ $f->uses }}</td>
                        <td class="py-2.5 text-right text-gray-400 text-xs">{{ $f->avg_ms ?? '-' }}</td>
                        <td class="py-2.5 text-right text-gray-400 text-xs">{{ \Carbon\Carbon::parse($f->last_used)->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-gray-400 text-sm">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Activity Log --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Log Aktivitas Terbaru</h2>
        <div class="space-y-2 max-h-80 overflow-y-auto">
            @forelse($recentLogs as $log)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full {{ $log->success ? 'bg-green-400' : 'bg-red-400' }} flex-shrink-0"></span>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $log->feature_label }}</p>
                        <p class="text-xs text-gray-400">{{ $log->feature_category }} &middot; {{ $log->http_method }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">{{ $log->duration_ms ? $log->duration_ms.'ms' : '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $log->created_at->format('d M H:i') }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 py-4 text-center">Belum ada log</p>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script>
new Chart(document.getElementById('actChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($actDates),
        datasets: [{
            label: 'Aktivitas',
            data: @json($actUses),
            backgroundColor: 'rgba(59,130,246,0.7)',
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { font: { size: 11 } } }, x: { ticks: { font: { size: 10 }, maxTicksLimit: 10 } } }
    }
});

new Chart(document.getElementById('catChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: @json($byCategory->pluck('feature_category')),
        datasets: [{
            data: @json($byCategory->pluck('uses')),
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316'],
            borderWidth: 2, borderColor: '#fff',
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 8 } } } }
});
</script>
@endpush

@endsection
