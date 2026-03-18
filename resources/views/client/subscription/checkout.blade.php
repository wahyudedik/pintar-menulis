@extends('layouts.client')
@section('title', 'Checkout - ' . $package->name)

@section('content')
<div class="px-4 py-6 max-w-lg mx-auto" x-data="{
    gateway: '{{ in_array('manual_transfer', $enabledGateways) ? 'manual_transfer' : ($enabledGateways[0] ?? '') }}',
    bank: '',
    uploading: false
}">

    <a href="{{ route('pricing') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Pricing
    </a>

    <h1 class="text-xl font-bold text-gray-900 mb-5">Checkout</h1>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-sm text-red-800">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Order Summary --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5">
        <h2 class="font-semibold text-gray-900 mb-3 text-sm">Ringkasan Pesanan</h2>
        <div class="space-y-0 text-sm">
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-gray-500">Paket</span>
                <span class="font-medium">{{ $package->name }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-gray-500">Periode</span>
                <span class="font-medium">{{ $billingCycle === 'yearly' ? 'Tahunan (12 bulan)' : 'Bulanan' }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-gray-500">Kuota AI</span>
                <span class="font-medium">{{ number_format($package->ai_quota_monthly) }}/bulan</span>
            </div>
            <div class="flex justify-between pt-2">
                <span class="font-semibold text-gray-900">Total</span>
                <span class="text-lg font-bold text-red-600">Rp {{ number_format($price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    @if(empty($enabledGateways))
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-center">
        <p class="text-4xl mb-3">⚙️</p>
        <p class="font-semibold text-gray-900 mb-1">Metode pembayaran belum dikonfigurasi</p>
        <p class="text-sm text-gray-600">Hubungi admin untuk mengaktifkan metode pembayaran.</p>
    </div>
    @else

    <form action="{{ route('subscription.pay', $package) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="billing_cycle" value="{{ $billingCycle }}">
        <input type="hidden" name="gateway" x-bind:value="gateway">

        {{-- Gateway Selection --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5">
            <h2 class="font-semibold text-gray-900 mb-3 text-sm">Pilih Metode Pembayaran</h2>
            <div class="space-y-2">

                @if(in_array('midtrans', $enabledGateways))
                <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition"
                       :class="gateway === 'midtrans' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" name="_gateway_ui" value="midtrans" x-model="gateway" class="sr-only">
                    <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">💳</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm">Midtrans</p>
                        <p class="text-xs text-gray-500 truncate">GoPay, OVO, VA Bank, QRIS — otomatis</p>
                    </div>
                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-bold rounded-full flex-shrink-0">Otomatis</span>
                </label>
                @endif

                @if(in_array('xendit', $enabledGateways))
                <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition"
                       :class="gateway === 'xendit' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" name="_gateway_ui" value="xendit" x-model="gateway" class="sr-only">
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">⚡</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm">Xendit</p>
                        <p class="text-xs text-gray-500 truncate">VA Bank, E-Wallet, QRIS — otomatis</p>
                    </div>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full flex-shrink-0">Otomatis</span>
                </label>
                @endif

                @if(in_array('manual_transfer', $enabledGateways))
                <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition"
                       :class="gateway === 'manual_transfer' ? 'border-gray-700 bg-gray-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" name="_gateway_ui" value="manual_transfer" x-model="gateway" class="sr-only">
                    <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">🏦</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm">Transfer Manual</p>
                        <p class="text-xs text-gray-500">Transfer bank / e-wallet, verifikasi 1×24 jam</p>
                    </div>
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full flex-shrink-0">Manual</span>
                </label>
                @endif

            </div>
        </div>

        {{-- Midtrans Info --}}
        <div x-show="gateway === 'midtrans'" x-cloak
             class="bg-purple-50 border border-purple-200 rounded-2xl p-4 mb-5">
            <p class="text-sm font-semibold text-purple-900 mb-1">💳 Pembayaran via Midtrans</p>
            <p class="text-xs text-purple-700">
                Kamu akan diarahkan ke halaman pembayaran Midtrans. Tersedia: Credit Card, GoPay, OVO, DANA, VA Bank, QRIS.
                Pembayaran diproses otomatis — langganan aktif langsung setelah pembayaran berhasil.
            </p>
            <input type="hidden" name="payment_method" x-bind:value="gateway === 'midtrans' ? 'midtrans' : ''">
        </div>

        {{-- Xendit Info --}}
        <div x-show="gateway === 'xendit'" x-cloak
             class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-5">
            <p class="text-sm font-semibold text-blue-900 mb-1">⚡ Pembayaran via Xendit</p>
            <p class="text-xs text-blue-700">
                Kamu akan diarahkan ke halaman pembayaran Xendit. Tersedia: VA Bank, GoPay, OVO, DANA, QRIS, Kartu Kredit.
                Pembayaran diproses otomatis — langganan aktif langsung setelah pembayaran berhasil.
            </p>
            <input type="hidden" name="payment_method" x-bind:value="gateway === 'xendit' ? 'xendit' : ''">
        </div>

        {{-- Manual Transfer Detail --}}
        <div x-show="gateway === 'manual_transfer'" x-cloak class="mb-5 space-y-4">

            {{-- Bank Selection --}}
            @if($manualBanks->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-200 p-4">
                <h2 class="font-semibold text-gray-900 mb-3 text-sm">Pilih Rekening Tujuan</h2>
                <div class="space-y-2">
                    @foreach($manualBanks as $bankItem)
                    <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition"
                           :class="bank === '{{ $bankItem->bank_name }}' ? 'border-gray-700 bg-gray-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="payment_method" value="{{ $bankItem->bank_name }}"
                               x-model="bank" class="sr-only">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm">{{ $bankItem->bank_name }}</p>
                            <p class="text-xs text-gray-600 font-mono">{{ $bankItem->account_number }}</p>
                            <p class="text-xs text-gray-500">a.n. {{ $bankItem->account_name }}</p>
                        </div>
                        @if($bankItem->qr_code_path)
                        <img src="{{ asset('storage/' . $bankItem->qr_code_path) }}" alt="QR"
                             class="w-10 h-10 object-contain rounded-lg border border-gray-100 flex-shrink-0">
                        @endif
                    </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-3">
                    Transfer tepat <strong>Rp {{ number_format($price, 0, ',', '.') }}</strong> untuk mempercepat verifikasi.
                </p>
            </div>
            @endif

            {{-- Upload Proof --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-4">
                <h2 class="font-semibold text-gray-900 mb-1 text-sm">Upload Bukti Pembayaran</h2>
                <p class="text-xs text-gray-500 mb-3">Screenshot atau foto bukti transfer (JPG/PNG, maks 2MB)</p>
                <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-red-400 hover:bg-red-50 transition">
                    <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <span class="text-sm text-gray-500">Klik untuk upload</span>
                    <input type="file" name="payment_proof" accept="image/*" class="sr-only"
                           @change="uploading = $event.target.files.length > 0">
                </label>
                <p x-show="uploading" class="text-xs text-green-600 mt-2">✅ File dipilih</p>
                @error('payment_proof') <p class="text-red-600 text-xs mt-2">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit"
                class="w-full py-3.5 font-semibold rounded-xl transition text-sm text-white"
                :class="gateway === 'midtrans' ? 'bg-purple-600 hover:bg-purple-700' :
                        (gateway === 'xendit' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-red-500 hover:bg-red-600')">
            <span x-show="gateway === 'manual_transfer'">Kirim Bukti Pembayaran</span>
            <span x-show="gateway === 'midtrans'" x-cloak>Bayar via Midtrans →</span>
            <span x-show="gateway === 'xendit'" x-cloak>Bayar via Xendit →</span>
        </button>

        <p class="text-xs text-gray-500 text-center mt-3 pb-6">
            <span x-show="gateway === 'manual_transfer'">Verifikasi admin dalam 1×24 jam setelah bukti diterima.</span>
            <span x-show="gateway !== 'manual_transfer'" x-cloak>Langganan aktif otomatis setelah pembayaran berhasil.</span>
        </p>
    </form>
    @endif

</div>
@endsection
