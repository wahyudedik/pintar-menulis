@extends('layouts.guru')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Guru</h1>
        <p class="text-sm text-gray-600 mt-1">Kelola data training dan model ML</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Data Training</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_training_data'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Excellent Quality</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['excellent_data'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Good Quality</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['good_data'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Model Versions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['model_versions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Training Data -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Data Training Terbaru</h2>
            </div>
            <div class="p-4">
                @if($recentTraining->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentTraining as $data)
                        <div class="flex items-start justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ Str::limit($data->input_prompt, 50) }}</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    Quality: {{ ucfirst($data->quality_rating) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $data->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded 
                                {{ $data->quality_rating === 'excellent' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $data->quality_rating === 'good' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $data->quality_rating === 'fair' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $data->quality_rating === 'poor' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($data->quality_rating) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('guru.training') }}" class="text-sm text-purple-600 hover:text-purple-700">
                            Lihat Semua →
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-8">Belum ada data training</p>
                @endif
            </div>
        </div>

        <!-- Model Versions -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Versi Model</h2>
            </div>
            <div class="p-4">
                @if($modelVersions->count() > 0)
                    <div class="space-y-3">
                        @foreach($modelVersions as $version)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-900">{{ $version->version_name }}</p>
                                <span class="px-2 py-1 text-xs rounded {{ $version->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $version->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600">
                                Accuracy: {{ number_format($version->accuracy_score * 100, 2) }}%
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $version->created_at->format('d M Y') }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('guru.analytics') }}" class="text-sm text-purple-600 hover:text-purple-700">
                            Lihat Analytics →
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-8">Belum ada model version</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('guru.training') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Tambah Training Data</p>
                    <p class="text-xs text-gray-600">Input data baru</p>
                </div>
            </a>

            <a href="{{ route('guru.training.history') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Training History</p>
                    <p class="text-xs text-gray-600">Lihat riwayat</p>
                </div>
            </a>

            <a href="{{ route('guru.analytics') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Analytics</p>
                    <p class="text-xs text-gray-600">Lihat statistik</p>
                </div>
            </a>
        </div>
    </div>
</div>

@endsection
