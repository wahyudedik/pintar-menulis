@extends('layouts.admin')

@section('title', 'ML Data Detail')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.ml-data.index') }}" class="hover:text-gray-700">ML Data</a>
            <span>/</span>
            <span class="text-gray-900">{{ $mlData->username }}</span>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900">{{ $mlData->username }}</h1>
        <p class="text-sm text-gray-500 capitalize mt-1">{{ $mlData->platform }}</p>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Basic Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Informasi Cache</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Username</span>
                    <span class="text-sm font-medium text-gray-900">{{ $mlData->username }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Platform</span>
                    <span class="text-sm font-medium text-gray-900 capitalize">{{ $mlData->platform }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Quality Score</span>
                    <span class="text-sm font-medium text-gray-900">{{ number_format($mlData->data_quality_score ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Cache Hits</span>
                    <span class="text-sm font-medium text-gray-900">{{ $mlData->cache_hit_count ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">API Calls Saved</span>
                    <span class="text-sm font-medium text-green-600">{{ $mlData->api_calls_saved ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Last Cache Hit</span>
                    <span class="text-sm font-medium text-gray-900">{{ $mlData->last_cache_hit ? \Carbon\Carbon::parse($mlData->last_cache_hit)->diffForHumans() : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Updated</span>
                    <span class="text-sm font-medium text-gray-900">{{ $mlData->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Raw Data Preview -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Data Preview</h2>
            @if($mlData->profile_data)
            <pre class="text-xs text-gray-600 bg-gray-50 rounded-lg p-3 overflow-auto max-h-64">{{ json_encode($mlData->profile_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
            <p class="text-sm text-gray-400">Tidak ada data tersedia.</p>
            @endif
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.ml-data.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition">
            ← Kembali
        </a>
    </div>
</div>
@endsection
