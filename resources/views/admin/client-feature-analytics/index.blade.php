@extends('layouts.admin-nav')

@section('title', 'Client Feature Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Client Feature Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">Monitoring & analisis penggunaan fitur oleh client</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="period" onchange="this.form.submit()"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="7"  {{ $period == 7  ? 'selected' : '' }}>7 Hari</option>
                <option value="14" {{ $period == 14 ? 'selected' : '' }}>14 Hari</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>30 Hari</option>
                <option value="60" {{ $period == 60 ? 'selected' : '' }}>60 Hari</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>90 Hari</option>
            </select>
            <select name="category" onchange="this.form.submit()"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="all">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Aktivitas</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalUsage) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $period }} hari terakhir</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">User Aktif</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ number_format($activeUsers) }}</p>
            <p class="text-xs text-gray-400 mt-1">unique users</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Fitur Digunakan</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalFeatures }}</p>
            <p class="text-xs text-gray-400 mt-1">dari total fitur</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Avg Response</p>
            <p class="text-3xl font-bold {{ $avgResponseMs > 3000 ? 'text-red-600' : ($avgResponseMs > 1500 ? 'text-yellow-600' : 'text-gray-900') }} mt-1">{{ $avgResponseMs }}ms</p>
            <p class="text-xs text-gray-400 mt-1">rata-rata</p>
        </div>
    </div>

    {{-- Optimization Insights --}}
    @if(count($insights) > 0)
    <div class="mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Rekomendasi Optimasi</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($insights as $insight)
            @php
            $colors = [
                'success' => 'bg-green-50 border-green-200 text-green-800',
                'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                'info'    => 'bg-blue-50 border-blue-200 text-blue-800',
            ];
            $cls = $colors[$insight['type']] ?? $colors['info'];
            @endphp
            <div class="border rounded-xl p-4 {{ $cls }}">
                <div class="flex items-start gap-3">
                    <span class="text-xl flex-shrink-0">{{ $insight['icon'] }}</span>
                    <div>
                        <p class="font-semibold text-sm">{{ $insight['title'] }}</p>
                        <p class="text-sm mt-0.5 opacity-90">{{ $insight['body'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Daily Trend Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Tren Aktivitas Harian</h2>
            <canvas id="trendChart" height="100"></canvas>
        </div>

        {{-- Category Breakdown --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Penggunaan per Kategori</h2>
            <canvas id="categoryChart" height="200"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Top Features Table --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Top Fitur Berdasarkan Penggunaan</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-2 text-xs text-gray-500 font-medium">#</th>
                            <th class="text-left py-2 text-xs text-gray-500 font-medium">Fitur</th>
                            <th class="text-left py-2 text-xs text-gray-500 font-medium">Kategori</th>
                            <th class="text-right py-2 text-xs text-gray-500 font-medium">Penggunaan</th>
                            <th class="text-right py-2 text-xs text-gray-500 font-medium">Users</th>
                            <th class="text-right py-2 text-xs text-gray-500 font-medium">Success</th>
                            <th class="text-right py-2 text-xs text-gray-500 font-medium">Avg ms</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topFeatures as $i => $f)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="py-2.5 text-gray-400 text-xs">{{ $i + 1 }}</td>
                            <td class="py-2.5">
                                <a href="{{ route('admin.feature-analytics.feature', ['featureKey' => $f->feature_key, 'period' => $period]) }}"
                                   class="font-medium text-blue-600 hover:underline">{{ $f->feature_label }}</a>
                                <div class="w-full bg-gray-100 rounded-full h-1 mt-1">
                                    <div class="bg-blue-500 h-1 rounded-full" style="width: {{ $f->usage_pct }}%"></div>
                                </div>
                            </td>
                            <td class="py-2.5">
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $f->feature_category }}</span>
                            </td>
                            <td class="py-2.5 text-right font-semibold">{{ number_format($f->total_uses) }}</td>
                            <td class="py-2.5 text-right text-gray-600">{{ $f->unique_users }}</td>
                            <td class="py-2.5 text-right">
                                <span class="{{ $f->success_rate >= 90 ? 'text-green-600' : ($f->success_rate >= 70 ? 'text-yellow-600' : 'text-red-600') }} font-medium">
                                    {{ $f->success_rate }}%
                                </span>
                            </td>
                            <td class="py-2.5 text-right text-gray-500 text-xs">{{ $f->avg_ms ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="py-8 text-center text-gray-400 text-sm">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">
            {{-- By Package --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-base font-semibold text-gray-900 mb-3">Penggunaan per Paket</h2>
                @forelse($byPackage as $pkg)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <span class="text-sm text-gray-700">{{ $pkg->subscription_package ?? 'Tanpa Paket' }}</span>
                    <div class="text-right">
                        <span class="text-sm font-semibold">{{ number_format($pkg->total_uses) }}</span>
                        <span class="text-xs text-gray-400 ml-1">/ {{ $pkg->unique_users }} users</span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">Belum ada data</p>
                @endforelse
            </div>

            {{-- Underused Features --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-base font-semibold text-gray-900 mb-3">
                    Fitur Tidak Aktif
                    <span class="ml-1 px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">{{ $underused->count() }}</span>
                </h2>
                <p class="text-xs text-gray-400 mb-3">Fitur yang belum digunakan dalam {{ $period }} hari</p>
                <div class="space-y-1 max-h-48 overflow-y-auto">
                    @forelse($underused as $key => $feat)
                    <div class="flex items-center justify-between py-1">
                        <span class="text-xs text-gray-600">{{ $feat['label'] }}</span>
                        <span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded">{{ $feat['category'] }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-green-600">Semua fitur aktif digunakan!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Power Users --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Power Users (Top 10)</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-2 text-xs text-gray-500 font-medium">#</th>
                        <th class="text-left py-2 text-xs text-gray-500 font-medium">User</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Total Aksi</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Fitur Digunakan</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Terakhir Aktif</th>
                        <th class="text-right py-2 text-xs text-gray-500 font-medium">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($powerUsers as $i => $pu)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2.5 text-gray-400 text-xs">{{ $i + 1 }}</td>
                        <td class="py-2.5">
                            <div class="flex items-center gap-2">
                                @if($pu->user?->avatar)
                                    <img src="{{ str_starts_with($pu->user->avatar, 'http') ? $pu->user->avatar : Storage::url($pu->user->avatar) }}"
                                         class="w-7 h-7 rounded-full object-cover">
                                @else
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($pu->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $pu->user?->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-400">{{ $pu->user?->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-2.5 text-right font-semibold">{{ number_format($pu->total_uses) }}</td>
                        <td class="py-2.5 text-right text-gray-600">{{ $pu->features_used }}</td>
                        <td class="py-2.5 text-right text-gray-400 text-xs">{{ \Carbon\Carbon::parse($pu->last_active)->diffForHumans() }}</td>
                        <td class="py-2.5 text-right">
                            @if($pu->user)
                            <a href="{{ route('admin.feature-analytics.user', ['user' => $pu->user_id, 'period' => $period]) }}"
                               class="text-xs text-blue-600 hover:underline">Lihat</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400 text-sm">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: @json($trendDates),
        datasets: [
            {
                label: 'Total Aktivitas',
                data: @json($trendUses),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                tension: 0.4,
                fill: true,
                pointRadius: 3,
            },
            {
                label: 'User Aktif',
                data: @json($trendUsers),
                borderColor: '#10b981',
                backgroundColor: 'transparent',
                tension: 0.4,
                borderDash: [4,3],
                pointRadius: 3,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
        scales: { y: { beginAtZero: true, ticks: { font: { size: 11 } } }, x: { ticks: { font: { size: 10 }, maxTicksLimit: 10 } } }
    }
});

const catCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(catCtx, {
    type: 'doughnut',
    data: {
        labels: @json($byCategory->pluck('feature_category')),
        datasets: [{
            data: @json($byCategory->pluck('total_uses')),
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#84cc16'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 8 } }
        }
    }
});
</script>
@endpush

@endsection
