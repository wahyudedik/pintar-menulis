@extends('layouts.client')

@section('title', 'Payment')

@section('content')
<div class="p-6" x-data="paymentPage()">
    <div class="mb-6">
        <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-700 text-sm">
            ← Kembali ke Order Detail
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Pembayaran Order</h1>
        <p class="text-sm text-gray-500 mt-1">Selesaikan pembayaran untuk order Anda</p>
    </div>

    <!-- Order Summary -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Order ID</span>
                <span class="font-semibold">#{{ $order->id }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Category</span>
                <span class="font-semibold">{{ $order->category }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Operator</span>
                <span class="font-semibold">{{ $order->operator->name }}</span>
            </div>
            <div class="border-t pt-3 flex justify-between text-lg">
                <span class="font-bold text-gray-900">Total Pembayaran</span>
                <span class="font-bold text-blue-600">Rp {{ number_format($order->budget, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pilih Metode Pembayaran</h3>

        @if($paymentSettings->count() > 0)
        <!-- Manual Transfer Options -->
        <div class="space-y-4 mb-6">
            @foreach($paymentSettings as $setting)
            <div class="border rounded-lg p-4 cursor-pointer hover:border-blue-500 transition"
                 :class="selectedMethod === '{{ $setting->id }}' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                 @click="selectMethod('{{ $setting->id }}', '{{ $setting->bank_name }}')">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <input type="radio" 
                               name="payment_method" 
                               value="{{ $setting->id }}"
                               x-model="selectedMethod"
                               class="w-4 h-4 text-blue-600">
                        <div>
                            <div class="font-semibold text-gray-900">Transfer Bank - {{ $setting->bank_name }}</div>
                            <div class="text-sm text-gray-600">{{ $setting->account_number }} - {{ $setting->account_name }}</div>
                        </div>
                    </div>
                    <span class="text-2xl">🏦</span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Selected Payment Details -->
        <div x-show="selectedMethod" x-transition class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h4 class="font-bold text-gray-900 mb-4">Detail Pembayaran</h4>
            
            @foreach($paymentSettings as $setting)
            <div x-show="selectedMethod === '{{ $setting->id }}'" class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Transfer ke rekening:</p>
                    <div class="bg-white rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-gray-900">{{ $setting->bank_name }}</span>
                            <button @click="copyToClipboard('{{ $setting->account_number }}')" 
                                    class="text-blue-600 hover:text-blue-700 text-sm">
                                📋 Copy
                            </button>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 mb-1">{{ $setting->account_number }}</div>
                        <div class="text-gray-600">a.n. {{ $setting->account_name }}</div>
                    </div>
                </div>

                @if($setting->qr_code_path)
                <div>
                    <p class="text-sm text-gray-600 mb-2">Atau scan QR Code:</p>
                    <div class="bg-white rounded-lg p-4 inline-block">
                        <img src="{{ asset('storage/' . $setting->qr_code_path) }}" 
                             alt="QR Code" 
                             class="w-48 h-48 object-contain">
                    </div>
                </div>
                @endif

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        ⚠️ Transfer tepat sebesar <strong>Rp {{ number_format($order->budget, 0, ',', '.') }}</strong> 
                        untuk mempermudah verifikasi
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Upload Proof Form -->
        <form action="{{ route('payment.submit-proof', $order) }}" method="POST" enctype="multipart/form-data" 
              x-show="selectedMethod" x-transition>
            @csrf
            <input type="hidden" name="payment_method" x-model="selectedBankName">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    ID Transaksi / Nomor Referensi (Opsional)
                </label>
                <input type="text" name="transaction_id" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: TRX123456789">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Bukti Transfer <span class="text-red-600">*</span>
                </label>
                <input type="file" name="proof_image" accept="image/*" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG. Max 2MB</p>
                @error('proof_image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" 
                    class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                Submit Bukti Pembayaran
            </button>
        </form>

        @else
        <div class="text-center py-8">
            <p class="text-gray-600">Belum ada metode pembayaran yang tersedia.</p>
            <p class="text-sm text-gray-500 mt-2">Silakan hubungi admin.</p>
        </div>
        @endif
    </div>
</div>

<script>
function paymentPage() {
    return {
        selectedMethod: null,
        selectedBankName: '',
        
        selectMethod(id, bankName) {
            this.selectedMethod = id;
            this.selectedBankName = bankName;
        },
        
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Nomor rekening berhasil dicopy!');
            });
        }
    }
}
</script>
@endsection
