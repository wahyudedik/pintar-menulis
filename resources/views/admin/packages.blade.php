@extends('layouts.admin')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Package Management</h1>
        <p class="text-sm text-gray-600 mt-1">Kelola paket langganan platform</p>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        @foreach($packages as $package)
        <div class="bg-white rounded-lg border-2 overflow-hidden {{ $package->name === 'Professional' ? 'border-red-600' : 'border-gray-200' }}">
            @if($package->name === 'Professional')
            <div class="bg-red-600 text-white text-center py-2 text-xs font-semibold">
                PALING POPULER
            </div>
            @endif
            
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $package->name }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ $package->description }}</p>
                
                <div class="mb-4 pb-4 border-b border-gray-200">
                    <span class="text-3xl font-bold text-gray-900">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                    <span class="text-gray-600">/bulan</span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-700">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ $package->caption_quota }} Caption Quota</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-700">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ $package->product_description_quota }} Product Description</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-700">
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ $package->orders_count }} Active Orders</span>
                    </div>
                </div>

                <div class="mb-4">
                    @if($package->is_active)
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-medium">Active</span>
                    @else
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium">Inactive</span>
                    @endif
                </div>

                <a href="{{ route('admin.packages.edit', $package) }}" 
                    class="block w-full text-center bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm">
                    Edit Package
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
