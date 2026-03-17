@extends('layouts.admin')

@section('title', 'Edit Package')

@section('content')
<div class="p-6 max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('admin.packages') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Packages</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Paket: {{ $package->name }}</h1>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg text-sm text-green-800">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form action="{{ route('admin.packages.update', $package) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Basic Info --}}
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Informasi Dasar</h2>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket</label>
                    <input type="text" name="name" value="{{ old('name', $package->name) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">{{ old('description', $package->description) }}</textarea>
                    @error('description') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Bulanan (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', $package->price) }}" required min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('price') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Tahunan (Rp)</label>
                    <input type="number" name="yearly_price" value="{{ old('yearly_price', $package->yearly_price) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <p class="text-xs text-gray-400 mt-1">Kosongkan untuk otomatis (10× bulanan)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $package->sort_order ?? 0) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
            </div>

            {{-- AI Quota --}}
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Kuota AI</h2>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kuota AI Generate/Bulan</label>
                    <input type="number" name="ai_quota_monthly" value="{{ old('ai_quota_monthly', $package->ai_quota_monthly) }}" required min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('ai_quota_monthly') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Caption Quota (lama)</label>
                    <input type="number" name="caption_quota" value="{{ old('caption_quota', $package->caption_quota) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Description Quota</label>
                    <input type="number" name="product_description_quota" value="{{ old('product_description_quota', $package->product_description_quota) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
            </div>

            {{-- Trial & Badge --}}
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Trial & Badge</h2>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teks Badge</label>
                    <input type="text" name="badge_text" value="{{ old('badge_text', $package->badge_text) }}" placeholder="Contoh: PALING POPULER"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warna Badge</label>
                    <select name="badge_color"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        @foreach(['red' => 'Merah', 'green' => 'Hijau', 'blue' => 'Biru', 'purple' => 'Ungu'] as $val => $label)
                        <option value="{{ $val }}" {{ old('badge_color', $package->badge_color) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Trial (hari)</label>
                    <input type="number" name="trial_days" value="{{ old('trial_days', $package->trial_days ?? 30) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
            </div>

            {{-- AI Features Access --}}
            @php $activeFeatures = old('allowed_features', $package->getAllowedFeaturesList()); @endphp
            @include('admin.partials.package-features-checkboxes')

            {{-- Features --}}
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Fitur (satu per baris)</h2>
            <div class="mb-6">
                <textarea name="features_text" rows="6"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono"
                    placeholder="30 generate AI per bulan&#10;Akses semua template&#10;Support via email">{{ old('features_text', is_array($package->features) ? implode("\n", $package->features) : '') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Setiap baris = satu fitur yang ditampilkan di pricing page</p>
            </div>

            {{-- Toggles --}}
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 pt-4 border-t border-gray-100">Pengaturan</h2>
            <div class="space-y-3 mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="has_trial" value="1" {{ old('has_trial', $package->has_trial) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700">Paket ini memiliki masa trial</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $package->is_featured) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700">Tampilkan sebagai paket unggulan (highlighted)</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700">Aktif (tampil di halaman pricing)</span>
                </label>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                    Simpan Perubahan
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
