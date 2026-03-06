@extends('layouts.operator')

@section('title', 'Tarik Saldo')

@section('content')
<div class="p-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('operator.earnings') }}" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
                ← Back to Earnings
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Tarik Saldo</h1>
            <p class="text-sm text-gray-500 mt-1">Request penarikan penghasilan</p>
        </div>

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
        @endif

        <!-- Balance Info -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 mb-6 text-white">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-green-100 text-sm mb-1">Saldo Tersedia</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-green-100 text-sm mb-1">Pending Withdrawal</p>
                    <p class="text-2xl font-semibold">Rp {{ number_format($pendingWithdrawals, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Withdrawal Form -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <form action="{{ route('operator.withdrawal.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Penarikan <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="number" name="amount" required min="50000" max="{{ $availableBalance }}"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="50000">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Minimum penarikan: Rp 50.000</p>
                    @error('amount')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Bank <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="bank_name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="BCA, Mandiri, BNI, dll">
                    @error('bank_name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Rekening <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="account_number" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="1234567890">
                    @error('account_number')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Pemilik Rekening <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="account_name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="Nama sesuai rekening">
                    @error('account_name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan (Optional)
                    </label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Catatan tambahan..."></textarea>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-sm text-yellow-800">
                            <p class="font-medium mb-1">Informasi Penting:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Proses withdrawal membutuhkan waktu 1-3 hari kerja</li>
                                <li>Pastikan data rekening sudah benar</li>
                                <li>Admin akan memverifikasi request Anda</li>
                                <li>Anda akan mendapat notifikasi setelah diproses</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('operator.earnings') }}" 
                       class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                        Batal
                    </a>
                    <button type="submit" 
                            class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
