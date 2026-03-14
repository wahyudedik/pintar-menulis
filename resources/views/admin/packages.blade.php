@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Package Management</h1>
            <p class="text-sm text-gray-600 mt-1">Kelola paket langganan platform</p>
        </div>
        <a href="{{ route('admin.subscriptions') }}"
           class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
            Kelola Subscription
        </a>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($packages as $package)
        @php
            $activeCount = $package->subscriptions()->whereIn('status', ['active', 'trial'])->count();
            $pendingCount = $package->subscriptions()->where('status', 'pending_payment')->count();
        @endphp
        <div class="bg-white rounded-xl border-2 overflow-hidden {{ $package->is_featured ? 'border-red-500' : 'border-gray-200' }}">
            @if($package->badge_text)
            <div class="py-2 text-center text-xs font-bold text-white
                {{ $package->badge_color === 'red' ? 'bg-red-500' : ($package->badge_color === 'green' ? 'bg-green-500' : ($package->badge_color === 'purple' ? 'bg-purple-500' : 'bg-blue-500')) }}">
                {{ $package->badge_text }}
            </div>
            @endif

            <div class="p-5">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-lg font-bold text-gray-900">{{ $package->name }}</h3>
                    @if($package->is_active)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Aktif</span>
                    @else
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Nonaktif</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mb-3">{{ $package->description }}</p>

                <div class="mb-3 pb-3 border-b border-gray-100">
                    <p class="text-2xl font-bold text-gray-900">
                        @if($package->price == 0) Gratis
                        @else Rp {{ number_format($package->price, 0, ',', '.') }}
                        @endif
                    </p>
                    @if($package->price > 0)
                    <p class="text-xs text-gray-500">/bulan · Rp {{ number_format($package->yearly_price ?? $package->price * 10, 0, ',', '.') }}/tahun</p>
                    @endif
                </div>

                <div class="space-y-1.5 mb-4 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <span>Kuota AI/bulan</span>
                        <span class="font-medium text-gray-900">{{ number_format($package->ai_quota_monthly) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Trial</span>
                        <span class="font-medium text-gray-900">{{ $package->has_trial ? $package->trial_days . ' hari' : 'Tidak' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Subscriber aktif</span>
                        <span class="font-medium text-green-700">{{ $activeCount }}</span>
                    </div>
                    @if($pendingCount > 0)
                    <div class="flex justify-between">
                        <span>Pending verifikasi</span>
                        <span class="font-medium text-yellow-600">{{ $pendingCount }}</span>
                    </div>
                    @endif
                </div>

                <a href="{{ route('admin.packages.edit', $package) }}"
                   class="block w-full text-center bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm font-medium">
                    Edit Paket
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
