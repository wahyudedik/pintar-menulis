@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Subscription</h1>
        <p class="text-sm text-gray-600 mt-1">Verifikasi pembayaran dan kelola langganan user</p>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg text-sm text-green-800">
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
            $stats = [
                ['label' => 'Total Aktif', 'value' => \App\Models\UserSubscription::where('status', 'active')->count(), 'color' => 'green'],
                ['label' => 'Trial', 'value' => \App\Models\UserSubscription::where('status', 'trial')->count(), 'color' => 'blue'],
                ['label' => 'Pending Verifikasi', 'value' => \App\Models\UserSubscription::where('status', 'pending_payment')->count(), 'color' => 'yellow'],
                ['label' => 'Total', 'value' => \App\Models\UserSubscription::count(), 'color' => 'gray'],
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-2xl font-bold text-gray-900">{{ $stat['value'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-4 flex-wrap">
        @foreach(['all' => 'Semua', 'pending_payment' => 'Pending', 'active' => 'Aktif', 'trial' => 'Trial', 'cancelled' => 'Dibatalkan', 'expired' => 'Kadaluarsa'] as $val => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}"
           class="px-3 py-1.5 text-xs font-medium rounded-lg transition
               {{ request('status', 'all') === $val ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Paket</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kuota</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pembayaran</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subscriptions as $sub)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $sub->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $sub->user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $sub->package->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $sub->billing_cycle === 'yearly' ? 'Tahunan' : 'Bulanan' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusMap = [
                                    'active'          => 'bg-green-100 text-green-700',
                                    'trial'           => 'bg-blue-100 text-blue-700',
                                    'pending_payment' => 'bg-yellow-100 text-yellow-700',
                                    'cancelled'       => 'bg-gray-100 text-gray-600',
                                    'expired'         => 'bg-red-100 text-red-600',
                                ];
                                $statusLabel = [
                                    'active'          => 'Aktif',
                                    'trial'           => 'Trial',
                                    'pending_payment' => 'Pending',
                                    'cancelled'       => 'Dibatalkan',
                                    'expired'         => 'Kadaluarsa',
                                ];
                            @endphp
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusMap[$sub->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusLabel[$sub->status] ?? $sub->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            @if($sub->starts_at)
                                {{ $sub->starts_at->format('d M Y') }} –<br>{{ $sub->ends_at?->format('d M Y') ?? '-' }}
                            @elseif($sub->trial_starts_at)
                                Trial: {{ $sub->trial_starts_at->format('d M Y') }} –<br>{{ $sub->trial_ends_at?->format('d M Y') ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            {{ $sub->ai_quota_used }} / {{ $sub->ai_quota_limit }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            @if($sub->payment_method)
                                <p>{{ str_replace('_', ' ', ucfirst($sub->payment_method)) }}</p>
                                @if($sub->payment_reference)
                                    <a href="{{ asset('storage/' . $sub->payment_reference) }}" target="_blank"
                                       class="text-blue-600 hover:underline">Lihat Bukti</a>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($sub->status === 'pending_payment')
                            <div class="flex gap-2">
                                <form action="{{ route('admin.subscriptions.verify', $sub) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition">
                                        Verifikasi
                                    </button>
                                </form>
                                <form action="{{ route('admin.subscriptions.reject', $sub) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition"
                                            onclick="return confirm('Tolak pembayaran ini?')">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 text-sm">
                            Tidak ada data subscription
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscriptions instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
