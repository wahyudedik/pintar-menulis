@extends('layouts.admin')

@section('title', 'Package Management')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Package Management</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $packages->count() }} paket terdaftar · {{ $totalSubs }} subscriber aktif · {{ $pendingSubs }} pending verifikasi</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.packages.create') }}"
               class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition">
                + Tambah Paket
            </a>
            <a href="{{ route('admin.subscriptions') }}"
               class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                Kelola Subscription
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-5 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        @foreach($packages as $package)
        @php
            $activeCount  = $package->subscriptions()->whereIn('status', ['active', 'trial'])->count();
            $pendingCount = $package->subscriptions()->where('status', 'pending_payment')->count();
            $featureCount = count($package->getAllowedFeaturesList());
        @endphp
        <div class="bg-white rounded-xl border-2 overflow-hidden flex flex-col {{ $package->is_featured ? 'border-red-500' : 'border-gray-200' }} {{ !$package->is_active ? 'opacity-60' : '' }}">

            {{-- Badge --}}
            @if($package->badge_text)
            <div class="py-1.5 text-center text-xs font-bold text-white
                {{ $package->badge_color === 'red' ? 'bg-red-500' : ($package->badge_color === 'green' ? 'bg-green-500' : ($package->badge_color === 'purple' ? 'bg-purple-500' : 'bg-blue-500')) }}">
                {{ $package->badge_text }}
            </div>
            @endif

            <div class="p-5 flex flex-col flex-1">
                {{-- Header --}}
                <div class="flex items-start justify-between mb-1">
                    <h3 class="text-lg font-bold text-gray-900">{{ $package->name }}</h3>
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $package->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $package->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mb-3 leading-relaxed">{{ $package->description }}</p>

                {{-- Harga --}}
                <div class="mb-3 pb-3 border-b border-gray-100">
                    <p class="text-2xl font-bold text-gray-900">
                        @if($package->price == 0) Gratis
                        @else Rp {{ number_format($package->price, 0, ',', '.') }}
                        @endif
                    </p>
                    @if($package->price > 0)
                    <p class="text-xs text-gray-400">/bulan · Rp {{ number_format($package->yearly_price ?? $package->price * 10, 0, ',', '.') }}/tahun</p>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="space-y-1.5 mb-4 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <span>Kuota AI/bulan</span>
                        <span class="font-semibold text-gray-900">{{ number_format($package->ai_quota_monthly) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Fitur AI aktif</span>
                        <span class="font-semibold text-blue-700">{{ $featureCount }} fitur</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Trial</span>
                        <span class="font-semibold text-gray-900">{{ $package->has_trial ? $package->trial_days . ' hari' : 'Tidak' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Subscriber aktif</span>
                        <span class="font-semibold text-green-700">{{ $activeCount }}</span>
                    </div>
                    @if($pendingCount > 0)
                    <div class="flex justify-between">
                        <span>Pending</span>
                        <span class="font-semibold text-yellow-600">{{ $pendingCount }}</span>
                    </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="mt-auto space-y-2">
                    <a href="{{ route('admin.packages.edit', $package) }}"
                       class="block w-full text-center bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm font-medium">
                        ✏️ Edit Paket
                    </a>

                    <div class="grid grid-cols-2 gap-2">
                        {{-- Toggle aktif/nonaktif --}}
                        <form action="{{ route('admin.packages.toggle', $package) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="w-full text-center px-3 py-1.5 rounded-lg text-xs font-medium transition
                                    {{ $package->is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                {{ $package->is_active ? '⏸ Nonaktifkan' : '▶ Aktifkan' }}
                            </button>
                        </form>

                        {{-- Delete --}}
                        <form action="{{ route('admin.packages.destroy', $package) }}" method="POST"
                              onsubmit="return confirm('Hapus paket {{ addslashes($package->name) }}? Tindakan ini tidak bisa dibatalkan.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full text-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-medium transition
                                    {{ $activeCount > 0 ? 'opacity-40 cursor-not-allowed' : '' }}"
                                {{ $activeCount > 0 ? 'disabled title="Ada subscriber aktif"' : '' }}>
                                🗑 Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
