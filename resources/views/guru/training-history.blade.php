@extends('layouts.guru')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Training History</h1>
            <p class="text-sm text-gray-600 mt-1">Riwayat semua data training yang telah direview</p>
        </div>
        <a href="{{ route('guru.training') }}" class="text-purple-600 hover:text-purple-700 text-sm">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Excellent</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['excellent'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Good</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['good'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Fair</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['fair'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Poor</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['poor'] }}</p>
        </div>
    </div>

    <!-- Training Data List -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">All Training Data</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Input</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Quality</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Reviewed By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($trainingData as $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $data->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ Str::limit($data->input_prompt, 60) }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-medium
                                @if($data->quality_rating === 'excellent') bg-green-100 text-green-700
                                @elseif($data->quality_rating === 'good') bg-blue-100 text-blue-700
                                @elseif($data->quality_rating === 'fair') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700
                                @endif">
                                {{ ucfirst($data->quality_rating) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $data->guru->name }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $trainingData->links() }}
        </div>
    </div>
</div>
@endsection
