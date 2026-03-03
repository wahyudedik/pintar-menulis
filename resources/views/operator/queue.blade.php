@extends('layouts.operator')

@section('title', 'Order Queue')

@section('content')
<div class="p-6">
    <!-- My Active Orders -->
    <div class="mb-6">
        <div class="mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">Order Saya</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $myOrders->count() }} order aktif</p>
        </div>
        
        @if($myOrders->count() > 0)
        <div class="grid md:grid-cols-2 gap-4">
            @foreach($myOrders as $order)
            <div class="bg-white rounded-lg border border-gray-200 border-l-4 border-l-blue-500 p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ $order->category }}</h3>
                        <p class="text-xs text-gray-600">Client: {{ $order->user->name }}</p>
                    </div>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="bg-gray-50 rounded p-3 mb-3">
                    <p class="text-xs text-gray-600 font-medium mb-1">Brief:</p>
                    <p class="text-sm text-gray-800">{{ $order->brief }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <p class="text-xs text-gray-500">Budget</p>
                        <p class="text-base font-semibold text-green-600">Rp {{ number_format($order->budget, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Deadline</p>
                        <p class="text-sm font-medium text-red-600">{{ $order->deadline->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('operator.workspace', $order) }}" 
                       class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        Kerjakan
                    </a>
                    <form method="POST" action="{{ route('operator.reject', $order) }}" class="flex-1">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Yakin ingin reject order ini?')"
                                class="w-full bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition text-sm">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
            <p class="text-gray-600 text-sm">Anda belum memiliki order aktif</p>
        </div>
        @endif
    </div>

    <!-- Available Orders -->
    <div>
        <div class="mb-4">
            <h2 class="text-2xl font-semibold text-gray-900">Order Tersedia</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $orders->count() }} order menunggu</p>
        </div>
        
        @if($orders->count() > 0)
        <div class="grid md:grid-cols-2 gap-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:border-green-300 transition">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ $order->category }}</h3>
                        <p class="text-xs text-gray-600">Client: {{ $order->user->name }}</p>
                    </div>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">
                        Pending
                    </span>
                </div>

                <div class="bg-gray-50 rounded p-3 mb-3">
                    <p class="text-xs text-gray-600 font-medium mb-1">Brief:</p>
                    <p class="text-sm text-gray-800">{{ Str::limit($order->brief, 150) }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <p class="text-xs text-gray-500">Budget</p>
                        <p class="text-base font-semibold text-green-600">Rp {{ number_format($order->budget, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Deadline</p>
                        <p class="text-sm font-medium text-red-600">{{ $order->deadline->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $order->deadline->diffForHumans() }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('operator.accept', $order) }}">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                        Ambil Order
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Tidak ada order tersedia saat ini</p>
        </div>
        @endif
    </div>
</div>
@endsection
