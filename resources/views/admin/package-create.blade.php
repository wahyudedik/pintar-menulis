@extends('layouts.admin')

@section('title', 'Tambah Paket Baru')

@section('content')
<div class="p-6 max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('admin.packages') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Packages</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tambah Paket Baru</h1>
    </div>

    @if($errors->any())
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-sm text-red-800">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form action="{{ route('admin.packages.store') }}" method="POST">
            @csrf

            {{-- Basic Info --}}
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Informasi Dasar</h2>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Contoh: Enterprise">
                    @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Untuk siapa paket ini cocok...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Bulanan (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" required min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('price') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Tahunan (Rp)</label>
                    <input type="number" name="yearly_price" value="{{ old('yearly_price') }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <p class="text-xs text-gray-400 mt-1">Kosongkan untuk otomatis (10× bulanan)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 99) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
            </div>

            {{-- AI Quota --}}
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Kuota AI</h2>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kuota AI Generate/Bulan</label>
                    <input type="number" name="ai_quota_monthly" value="{{ old('ai_quota_monthly', 0) }}" required min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('ai_quota_monthly') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Caption Quota</label>
                    <input type="number" name="caption_quota" value="{{ old('caption_quota', 0) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Description Quota</label>
                    <input type="number" name="product_description_quota" value="{{ old('product_description_quota', 0) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
            </div>

            {{-- Trial & Badge --}}
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Trial & Badge</h2>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teks Badge</label>
                    <input type="text" name="badge_text" value="{{ old('badge_text') }}" placeholder="Contoh: PALING POPULER"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warna Badge</label>
                    <select name="badge_color"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        @foreach(['red' => 'Merah', 'green' => 'Hijau', 'blue' => 'Biru', 'purple' => 'Ungu'] as $val => $label)
                        <option value="{{ $val }}" {{ old('badge_color') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Trial (hari)</label>
                    <input type="number" name="trial_days" value="{{ old('trial_days', 30) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
            </div>

            {{-- Features --}}
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Fitur (satu per baris)</h2>
            <div class="mb-6">
                <textarea name="features_text" rows="6"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono"
                    placeholder="500 generate AI per bulan&#10;Akses semua fitur premium&#10;Priority support&#10;Custom branding">{{ old('features_text') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Setiap baris = satu fitur yang ditampilkan di pricing page</p>
            </div>

            {{-- Toggles --}}
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Pengaturan</h2>
            <div class="space-y-3 mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="has_trial" value="1" {{ old('has_trial') ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700">Paket ini memiliki masa trial</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700">Tampilkan sebagai paket unggulan (highlighted)</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked
                        class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700">Aktif (tampil di halaman pricing)</span>
                </label>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                    Buat Paket
                </button>
                <a href="{{ route('admin.packages') }}"
                    class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
