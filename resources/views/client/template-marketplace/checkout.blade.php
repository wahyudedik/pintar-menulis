@extends('layouts.client')

@section('title', 'Checkout Template — ' . $purchase->template->title)

@section('content')
<div class="p-6 max-w-2xl mx-auto">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('templates.index') }}" class="hover:text-blue-600">Template Marketplace</a>
        <span>/</span>
        <a href="{{ route('templates.show', $purchase->template_id) }}" class="hover:text-blue-600">{{ Str::limit($purchase->template->title, 30) }}</a>
        <span>/</span>
        <span class="text-gray-900">Checkout</span>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <!-- Order Summary -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pembelian</h2>

        <div class="flex items-start gap-4 pb-4 border-b border-gray-100 mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-xl flex-shrink-0">
                📄
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900">{{ $purchase->template->title }}</p>
                <p class="text-sm text-gray-500 mt-0.5">{{ ucfirst($purchase->template->category) }} · {{ ucfirst($purchase->template->platform) }}</p>
                <p class="text-xs text-gray-400 mt-1">Penjual: {{ $purchase->seller->name }}</p>
            </div>
        </div>

        <div class="space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Harga Template</span>
                <span>Rp {{ number_format($purchase->price_paid, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-500 text-xs">
                <span>Komisi Platform ({{ $commissionRate }}%)</span>
                <span>Rp {{ number_format($purchase->price_paid * $commissionRate / 100, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-500 text-xs">
                <span>Diterima Penjual</span>
                <span>Rp {{ number_format($sellerEarnings, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-gray-100 text-base">
                <span>Total Bayar</span>
                <span>Rp {{ number_format($purchase->price_paid, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Payment Instructions -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Cara Pembayaran</h2>

        @php
            $paymentSettings = \App\Models\PaymentSetting::where('is_active', true)
                ->where('gateway_type', 'manual_transfer')
                ->get();
        @endphp

        @if($paymentSettings->count() > 0)
        <div class="space-y-3 mb-4">
            @foreach($paymentSettings as $setting)
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="w-10 h-10 bg-white rounded-lg border border-gray-200 flex items-center justify-center text-lg flex-shrink-0">
                    @if(str_contains(strtolower($setting->bank_name ?? ''), 'bca')) 🏦
                    @elseif(str_contains(strtolower($setting->bank_name ?? ''), 'mandiri')) 🏦
                    @elseif(str_contains(strtolower($setting->gateway_type ?? ''), 'qris')) 📱
                    @else 💳
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">{{ $setting->bank_name ?? ucfirst(str_replace('_', ' ', $setting->gateway_type)) }}</p>
                    <p class="text-sm text-gray-700 font-mono">{{ $setting->account_number }}</p>
                    @if($setting->account_name)
                    <p class="text-xs text-gray-500">a.n. {{ $setting->account_name }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700 mb-4">
            Hubungi admin untuk informasi rekening pembayaran.
        </div>
        @endif

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-700">
            <p class="font-semibold mb-1">Penting:</p>
            <ul class="space-y-1 list-disc list-inside">
                <li>Transfer tepat sesuai nominal: <strong>Rp {{ number_format($purchase->price_paid, 0, ',', '.') }}</strong></li>
                <li>Upload bukti transfer di bawah ini</li>
                <li>Admin akan memverifikasi dalam 1×24 jam</li>
                <li>Template akan aktif setelah pembayaran dikonfirmasi</li>
            </ul>
        </div>
    </div>

    <!-- Upload Proof -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload Bukti Transfer</h2>

        <form action="{{ route('templates.checkout.confirm', $purchase->id) }}" method="POST" enctype="multipart/form-data"
              x-data="{ fileName: '', preview: null }">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti Transfer</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition cursor-pointer"
                     @click="$refs.fileInput.click()">
                    <template x-if="!preview">
                        <div>
                            <p class="text-3xl mb-2">📎</p>
                            <p class="text-sm text-gray-600">Klik untuk pilih gambar</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, max 2MB</p>
                        </div>
                    </template>
                    <template x-if="preview">
                        <div>
                            <img :src="preview" class="max-h-48 mx-auto rounded-lg mb-2">
                            <p class="text-xs text-gray-500" x-text="fileName"></p>
                        </div>
                    </template>
                </div>
                <input type="file" name="proof_image" accept="image/*" required x-ref="fileInput" class="hidden"
                       @change="
                           const file = $event.target.files[0];
                           if (file) {
                               fileName = file.name;
                               const reader = new FileReader();
                               reader.onload = e => preview = e.target.result;
                               reader.readAsDataURL(file);
                           }
                       ">
                @error('proof_image')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-sm">
                    Kirim Bukti Pembayaran
                </button>
                <a href="{{ route('templates.show', $purchase->template_id) }}"
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
