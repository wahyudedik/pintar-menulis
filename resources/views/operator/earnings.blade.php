@extends('layouts.operator')

@section('title', 'Earnings')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Penghasilan Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola penghasilan dan withdrawal</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Total Penghasilan</p>
                    <p class="text-2xl font-semibold text-green-600">
                        Rp {{ number_format($stats['total_earnings'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Order Selesai</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed_orders'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Rating Rata-rata</p>
                    <p class="text-2xl font-semibold text-yellow-600">{{ number_format($stats['average_rating'], 1) }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Pending Withdrawal</p>
                    <p class="text-2xl font-semibold text-purple-600">
                        Rp {{ number_format($stats['pending_withdrawal'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Button -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 mb-6 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold mb-1">Tarik Penghasilan</h3>
                <p class="text-green-100 text-sm">Saldo tersedia: Rp {{ number_format($stats['pending_withdrawal'], 0, ',', '.') }}</p>
            </div>
            <button class="bg-white text-green-600 px-4 py-2 rounded-lg font-medium hover:bg-green-50 transition text-sm">
                Tarik Saldo
            </button>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Transaksi</h3>
        </div>
        <div class="p-4">
            @if($completedOrders->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($completedOrders as $order)
                <div class="py-3 flex items-center justify-between hover:bg-gray-50 transition">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $order->user->name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $order->category }} • 
                            @if($order->completed_at)
                                {{ $order->completed_at->format('d M Y') }}
                            @else
                                {{ $order->updated_at->format('d M Y') }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-green-600">
                            Rp {{ number_format($order->budget, 0, ',', '.') }}
                        </p>
                        <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                            Completed
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 text-sm">Belum ada transaksi</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
