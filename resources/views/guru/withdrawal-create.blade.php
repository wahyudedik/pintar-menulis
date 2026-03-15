@extends('layouts.guru')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">

    <div class="mb-6">
        <a href="{{ route('guru.earnings') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Penghasilan</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tarik Saldo</h1>
        <p class="text-sm text-gray-500">Saldo tersedia: <span class="font-semibold text-green-600">Rp {{ number_format($availableBalance, 0, ',', '.') }}</span></p>
    </div>

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('guru.withdrawal.store') }}" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Penarikan (min. Rp 10.000)</label>
            <input type="number" name="amount" min="10000" max="{{ $availableBalance }}"
                   value="{{ old('amount') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="Contoh: 50000" required>
            @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
            <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                   placeholder="BCA / BRI / Mandiri / dll" required>
            @error('bank_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
            <input type="text" name="account_number" value="{{ old('account_number') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                   placeholder="1234567890" required>
            @error('account_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening</label>
            <input type="text" name="account_name" value="{{ old('account_name', $user->name) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                   required>
            @error('account_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
            <textarea name="notes" rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                      placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
        </div>

        <button type="submit"
                class="w-full py-2.5 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition text-sm">
            Ajukan Penarikan
        </button>
    </form>

</div>
@endsection
