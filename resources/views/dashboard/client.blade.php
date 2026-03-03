@extends('layouts.client')

@section('title', 'Dashboard')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ auth()->user()->name }}</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Total Orders</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Active</p>
                    <p class="text-2xl font-semibold text-green-600">{{ $stats['active_orders'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Pending</p>
                    <p class="text-2xl font-semibold text-yellow-600">{{ $stats['pending_requests'] }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Completed</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ $stats['completed_requests'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Packages -->
    @if($activeOrders->count() > 0)
    <div class="bg-white rounded-lg border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Paket Aktif</h2>
        </div>
        <div class="p-4 space-y-4">
            @foreach($activeOrders as $order)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $order->package->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Berlaku hingga {{ $order->end_date->format('d M Y') }}</p>
                    </div>
                    <a href="{{ route('copywriting.create', $order) }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        Request
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Caption Quota</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $order->remaining_caption_quota }} / {{ $order->package->caption_quota }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Product Desc Quota</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $order->remaining_product_description_quota }} / {{ $order->package->product_description_quota }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Requests -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Request Terbaru</h2>
        </div>
        @if($recentRequests->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($recentRequests as $request)
            <a href="{{ route('copywriting.show', $request) }}" class="block p-4 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($request->type) }}</span>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($request->status === 'completed') bg-green-100 text-green-700
                                @elseif($request->status === 'in_progress') bg-blue-100 text-blue-700
                                @elseif($request->status === 'pending') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $request->platform }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $request->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="p-4 border-t border-gray-200 text-center">
            <a href="{{ route('copywriting.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                Lihat Semua
            </a>
        </div>
        @else
        <div class="p-8 text-center">
            <p class="text-sm text-gray-500">Belum ada request copywriting</p>
        </div>
        @endif
    </div>
</div>
@endsection
