@extends('layouts.operator')

@section('title', 'Riwayat Penarikan')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('operator.earnings') }}" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
            ← Back to Earnings
        </a>
        <h1 class="text-2xl font-semibold text-gray-900">Riwayat Penarikan</h1>
        <p class="text-sm text-gray-500 mt-1">History semua request penarikan saldo</p>
    </div>

    @if($withdrawals->count() > 0)
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diproses</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($withdrawals as $withdrawal)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $withdrawal->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                            </p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $withdrawal->bank_name }}</p>
                            <p class="text-xs text-gray-500">{{ $withdrawal->account_number }}</p>
                            <p class="text-xs text-gray-500">a.n. {{ $withdrawal->account_name }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($withdrawal->status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">
                                Pending
                            </span>
                            @elseif($withdrawal->status === 'processing')
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
                                Processing
                            </span>
                            @elseif($withdrawal->status === 'completed')
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                                Completed
                            </span>
                            @elseif($withdrawal->status === 'rejected')
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-medium">
                                Rejected
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($withdrawal->processed_at)
                                {{ $withdrawal->processed_at->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @if($withdrawal->admin_notes)
                    <tr class="bg-gray-50">
                        <td colspan="5" class="px-6 py-3">
                            <p class="text-xs text-gray-500 mb-1">Catatan Admin:</p>
                            <p class="text-sm text-gray-700">{{ $withdrawal->admin_notes }}</p>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Riwayat</h3>
        <p class="text-gray-600 mb-4">Anda belum pernah melakukan penarikan saldo</p>
        <a href="{{ route('operator.withdrawal.create') }}" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            Tarik Saldo Sekarang
        </a>
    </div>
    @endif
</div>
@endsection
