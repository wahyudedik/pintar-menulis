@extends('layouts.admin')

@section('title', 'Guru Monitor - ' . config('app.name'))

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Guru Monitor</h1>
        <p class="text-gray-500 text-sm mt-1">Pantau kinerja semua guru dalam sistem ML Training</p>
    </div>

    {{-- Global Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Guru</p>
            <p class="text-2xl font-bold text-gray-900">{{ $globalStats['total_gurus'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Entri</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($globalStats['total_entries']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Entri Excellent</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($globalStats['excellent_entries']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Bulan Ini</p>
            <p class="text-2xl font-bold text-purple-600">{{ number_format($globalStats['entries_this_month']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Dibayar</p>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($globalStats['total_paid_out'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Pending Payout</p>
            <p class="text-2xl font-bold text-orange-500">Rp {{ number_format($globalStats['pending_payout'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Guru Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Daftar Guru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Guru</th>
                        <th class="px-4 py-3 text-center">Total Entri</th>
                        <th class="px-4 py-3 text-center">Excellent</th>
                        <th class="px-4 py-3 text-center">Good</th>
                        <th class="px-4 py-3 text-center">Fair</th>
                        <th class="px-4 py-3 text-center">Poor</th>
                        <th class="px-4 py-3 text-center">Quality Score</th>
                        <th class="px-4 py-3 text-right">Total Earnings</th>
                        <th class="px-4 py-3 text-right">Saldo Tersedia</th>
                        <th class="px-4 py-3 text-center">Terakhir Submit</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($gurus as $guru)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ $guru['name'] }}</p>
                                <p class="text-xs text-gray-400">{{ $guru['email'] }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ number_format($guru['total_entries']) }}</td>
                        <td class="px-4 py-3 text-center text-green-600 font-medium">{{ $guru['excellent_entries'] }}</td>
                        <td class="px-4 py-3 text-center text-blue-600">{{ $guru['good_entries'] }}</td>
                        <td class="px-4 py-3 text-center text-yellow-600">{{ $guru['fair_entries'] }}</td>
                        <td class="px-4 py-3 text-center text-red-500">{{ $guru['poor_entries'] }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $score = $guru['quality_score'];
                                $color = $score >= 75 ? 'green' : ($score >= 50 ? 'yellow' : 'red');
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700">
                                {{ $score }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-800">Rp {{ number_format($guru['total_earnings'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-800">Rp {{ number_format($guru['available_balance'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center text-gray-500 text-xs">
                            {{ $guru['last_submission'] ? \Carbon\Carbon::parse($guru['last_submission'])->diffForHumans() : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.guru-monitor.show', $guru['id']) }}" 
                               class="text-xs text-red-600 hover:text-red-800 font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-gray-400">Belum ada guru terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
