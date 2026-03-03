@extends('layouts.admin')

@section('content')
<div class="p-6" x-data="{ showAddModal: false, showMidtransModal: false }">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Payment Settings</h1>
        <p class="text-sm text-gray-600 mt-1">Kelola metode pembayaran platform</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    <!-- Midtrans Configuration -->
    <div class="bg-purple-600 rounded-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-2">Midtrans Integration</h3>
                <p class="text-purple-100 text-sm">Configure Midtrans payment gateway for automatic payment processing</p>
            </div>
            <button @click="showMidtransModal = true" 
                    class="px-4 py-2 bg-white text-purple-600 rounded-lg hover:bg-purple-50 transition text-sm font-medium">
                Configure
            </button>
        </div>
    </div>

    <!-- Manual Transfer Methods -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Manual Transfer Methods</h3>
            <button @click="showAddModal = true" 
                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                + Add Bank Account
            </button>
        </div>

        @if($settings->count() > 0)
        <div class="p-4 grid md:grid-cols-2 gap-4">
            @foreach($settings as $setting)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">{{ $setting->bank_name }}</h4>
                        <p class="text-sm text-gray-600">{{ $setting->account_number }}</p>
                        <p class="text-xs text-gray-500">a.n. {{ $setting->account_name }}</p>
                    </div>
                    <span class="px-2 py-1 rounded text-xs font-medium
                        {{ $setting->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $setting->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                @if($setting->qr_code_path)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $setting->qr_code_path) }}" 
                         alt="QR Code" 
                         class="w-32 h-32 object-contain border border-gray-200 rounded-lg">
                </div>
                @endif

                <div class="flex gap-2">
                    <form action="{{ route('admin.payment-settings.destroy', $setting) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('Yakin hapus metode pembayaran ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            <p class="text-gray-500 mb-4">Belum ada metode pembayaran</p>
            <button @click="showAddModal = true" 
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                Add First Bank Account
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Add Bank Account Modal -->
<div x-show="showAddModal" 
     x-transition
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     @click.self="showAddModal = false"
     style="display: none;">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Add Bank Account</h3>
        
        <form action="{{ route('admin.payment-settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name *</label>
                <input type="text" name="bank_name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                       placeholder="BCA, Mandiri, BNI, etc.">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Account Number *</label>
                <input type="text" name="account_number" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                       placeholder="1234567890">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Account Name *</label>
                <input type="text" name="account_name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                       placeholder="PT Smart Copy SMK">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">QR Code (Optional)</label>
                <input type="file" name="qr_code" accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Upload QR code untuk pembayaran. Max 2MB</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Add Bank Account
                </button>
                <button type="button" @click="showAddModal = false" 
                        class="flex-1 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Midtrans Configuration Modal -->
<div x-show="showMidtransModal" 
     x-transition
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     @click.self="showMidtransModal = false"
     style="display: none;">
    <div class="bg-white rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Midtrans Configuration</h3>
        
        <form action="{{ route('admin.payment-settings.midtrans-update') }}" method="POST">
            @csrf
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Info:</span> Midtrans adalah payment gateway untuk menerima pembayaran otomatis 
                    (Credit Card, E-Wallet, VA, dll). Dapatkan API keys dari 
                    <a href="https://dashboard.midtrans.com" target="_blank" class="underline">Midtrans Dashboard</a>
                </p>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="midtrans_enabled" value="1" 
                           {{ env('MIDTRANS_ENABLED', false) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Enable Midtrans Payment Gateway</span>
                </label>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                <select name="midtrans_environment" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <option value="sandbox" {{ env('MIDTRANS_ENVIRONMENT', 'sandbox') === 'sandbox' ? 'selected' : '' }}>
                        Sandbox (Testing)
                    </option>
                    <option value="production" {{ env('MIDTRANS_ENVIRONMENT') === 'production' ? 'selected' : '' }}>
                        Production (Live)
                    </option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Merchant ID</label>
                <input type="text" name="midtrans_merchant_id" 
                       value="{{ env('MIDTRANS_MERCHANT_ID', '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                       placeholder="G123456789">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Client Key</label>
                <input type="text" name="midtrans_client_key" 
                       value="{{ env('MIDTRANS_CLIENT_KEY', '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                       placeholder="SB-Mid-client-xxxxx">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Server Key</label>
                <input type="password" name="midtrans_server_key" 
                       value="{{ env('MIDTRANS_SERVER_KEY', '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                       placeholder="SB-Mid-server-xxxxx">
                <p class="text-xs text-gray-500 mt-1">Keep this secret! Never share publicly</p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-800">
                    <span class="font-semibold">Important:</span> Setelah save, aplikasi akan restart untuk apply perubahan .env
                </p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                    Save Configuration
                </button>
                <button type="button" @click="showMidtransModal = false" 
                        class="flex-1 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
