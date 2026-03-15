@extends('layouts.guru')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('guru.earnings') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Penghasilan</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Riwayat Withdrawal</h1>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200">
        <div class="divide-y divide-gray-100">
            @forelse($withdrawals as $w)
                <div class="px-5 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Rp {{ number_format($w->amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $w->bank_name }} — {{ $w->account_number }}</p>
                        <p class="text-xs text-gray-400">{{ $w->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium
                        {{ $w->status === 'completed' ? 'bg-green-100 text-green-700' :
                           ($w->status === 'approved' ? 'bg-blue-100 text-blue-700' :
                           ($w->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                        {{ ucfirst($w->status) }}
                    </span>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-gray-400 text-sm">
                    Belum ada riwayat withdrawal.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
