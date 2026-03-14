@extends('layouts.client')
@section('title', 'Langganan Saya')

@section('content')
<div class="p-6" x-data="{ cancelModal: false }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Langganan Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola paket dan kuota AI kamu</p>
        </div>
        <a href="{{ route('pricing') }}"
           class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition">
            Upgrade Paket
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg text-sm text-green-800">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-sm text-red-800">
        {{ session('error') }}
    </div>
    @endif

    @if($current && $current->isValid())
    {{-- Active Subscription Card --}}
    <div class="bg-white rounded-2xl border-2 {{ $current->package->is_featured ? 'border-red-400' : 'border-gray-200' }} p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-gray-900">{{ $current->package->name }}</h2>
                    @if($current->isOnTrial())
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">TRIAL</span>
                    @else
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full">AKTIF</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    @if($current->isOnTrial())
                        Trial berakhir {{ $current->trial_ends_at->format('d M Y') }}
                    @else
                        Aktif hingga {{ $current->ends_at->format('d M Y') }}
                    @endif
                    · <span class="font-medium text-gray-700">{{ $current->days_remaining }} hari lagi</span>
                </p>
            </div>
            <div class="text-right">
                @if($current->isOnTrial())
                    <p class="text-2xl font-bold text-blue-600">Gratis</p>
                    <p class="text-xs text-gray-500">masa trial</p>
                @else
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($current->package->price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">/{{ $current->billing_cycle === 'yearly' ? 'tahun' : 'bulan' }}</p>
                @endif
            </div>
        </div>

        {{-- Quota Bar --}}
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600 font-medium">Kuota AI Generate</span>
                <span class="font-semibold {{ $current->quota_percentage >= 90 ? 'text-red-600' : 'text-gray-700' }}">
                    {{ $current->ai_quota_used }} / {{ $current->ai_quota_limit }} digunakan
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="h-3 rounded-full transition-all
                    {{ $current->quota_percentage >= 90 ? 'bg-red-500' : ($current->quota_percentage >= 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                    style="width: {{ $current->quota_percentage }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Sisa {{ $current->remaining_quota }} generate
                @if($current->quota_reset_at)
                    · Reset {{ $current->quota_reset_at->format('d M Y') }}
                @endif
            </p>
        </div>

        {{-- Trial Progress --}}
        @if($current->isOnTrial())
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600 font-medium">Progres Trial</span>
                <span class="text-gray-700">{{ $current->trial_progress }}% terpakai</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="h-2 rounded-full bg-blue-400" style="width: {{ $current->trial_progress }}%"></div>
            </div>
        </div>
        @endif

        {{-- Actions --}}
        <div class="flex gap-3 mt-4">
            <a href="{{ route('pricing') }}"
               class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-xl transition">
                Upgrade Paket
            </a>
            @if($current->isActive())
            <button @click="cancelModal = true"
                    class="px-4 py-2 border border-gray-300 hover:border-red-400 hover:text-red-600 text-gray-600 text-sm font-medium rounded-xl transition">
                Batalkan Langganan
            </button>
            @endif
        </div>
    </div>

    @else
    {{-- No Active Subscription --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 mb-6 text-center">
        <div class="text-4xl mb-3">⚡</div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada langganan aktif</h3>
        <p class="text-sm text-gray-600 mb-4">Mulai trial gratis 30 hari atau pilih paket yang sesuai kebutuhanmu.</p>
        <a href="{{ route('pricing') }}"
           class="inline-block px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition">
            Lihat Paket
        </a>
    </div>
    @endif

    {{-- Subscription History --}}
    @if($history->count() > 0)
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Riwayat Langganan</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($history as $sub)
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900 text-sm">{{ $sub->package->name ?? 'Paket Dihapus' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $sub->created_at->format('d M Y') }}
                        @if($sub->billing_cycle) · {{ $sub->billing_cycle === 'yearly' ? 'Tahunan' : 'Bulanan' }} @endif
                    </p>
                </div>
                <div class="text-right">
                    @php
                        $statusMap = [
                            'active'          => ['bg-green-100 text-green-700', 'Aktif'],
                            'trial'           => ['bg-blue-100 text-blue-700', 'Trial'],
                            'pending_payment' => ['bg-yellow-100 text-yellow-700', 'Menunggu Verifikasi'],
                            'cancelled'       => ['bg-gray-100 text-gray-600', 'Dibatalkan'],
                            'expired'         => ['bg-red-100 text-red-600', 'Kadaluarsa'],
                        ];
                        [$cls, $label] = $statusMap[$sub->status] ?? ['bg-gray-100 text-gray-600', $sub->status];
                    @endphp
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $cls }}">{{ $label }}</span>
                    @if($sub->status === 'pending_payment')
                    <p class="text-xs text-yellow-600 mt-1">Menunggu verifikasi admin</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Cancel Modal --}}
    <div x-show="cancelModal" x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Batalkan Langganan?</h3>
            <p class="text-sm text-gray-600 mb-6">
                Akses tetap aktif hingga periode berakhir. Kamu bisa berlangganan kembali kapan saja.
            </p>
            <div class="flex gap-3">
                <form action="{{ route('subscription.cancel') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition">
                        Ya, Batalkan
                    </button>
                </form>
                <button @click="cancelModal = false"
                        class="flex-1 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                    Tidak
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
