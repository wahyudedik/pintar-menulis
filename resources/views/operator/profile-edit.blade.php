@extends('layouts.operator')

@section('title', 'Edit Profile')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Profile Operator</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi profil Anda</p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-4">
        <ul class="list-disc list-inside text-sm text-red-700">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded mb-4 text-sm text-green-800">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('operator.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Foto Profil -->
        <div class="bg-white rounded-lg border border-gray-200 p-4" x-data="{ avatarPreview: null }">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Foto Profil</h3>
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                    </template>
                    <template x-if="!avatarPreview">
                        @if(auth()->user()->avatar)
                            @if(str_starts_with(auth()->user()->avatar, 'http'))
                                <img src="{{ auth()->user()->avatar }}" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                            @else
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                            @endif
                        @else
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center text-white text-2xl font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </template>
                </div>
                <div class="flex-1">
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer"
                           @change="const f=$event.target.files[0]; if(f){const r=new FileReader();r.onload=e=>avatarPreview=e.target.result;r.readAsDataURL(f);}">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP - maks 2MB</p>
                    @error('avatar')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Info Dasar -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 space-y-3">
            <h3 class="text-sm font-semibold text-gray-900">Informasi Dasar</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                       placeholder="+62 812 3456 7890"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', auth()->user()->location) }}"
                       placeholder="Jakarta, Indonesia"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
            </div>
        </div>

        <!-- Bio -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
            <textarea name="bio" required rows="4" 
                      placeholder="Ceritakan tentang diri Anda, pengalaman, dan keahlian..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">{{ old('bio', $profile->bio) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Minimal 50 karakter, maksimal 500 karakter</p>
        </div>

        <!-- Portfolio URL -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Portfolio URL</label>
            <input type="url" name="portfolio_url" 
                   value="{{ old('portfolio_url', $profile->portfolio_url) }}"
                   placeholder="https://portfolio.com"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
        </div>

        <!-- Specializations -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Spesialisasi</label>
            <div class="grid md:grid-cols-3 gap-2">
                @php
                $allSpecs = [
                    'Social Media', 'Ads', 'Website', 'Landing Page', 
                    'Marketplace', 'Email Marketing', 'Proposal', 
                    'Company Profile', 'Personal Branding', 'UX Writing',
                    'Product Description', 'SEO', 'Content Writing'
                ];
                $selectedSpecs = old('specializations', $profile->specializations ?? []);
                @endphp
                
                @foreach($allSpecs as $spec)
                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="specializations[]" value="{{ $spec }}"
                           {{ in_array($spec, $selectedSpecs) ? 'checked' : '' }}
                           class="rounded text-green-600 focus:ring-green-500">
                    <span class="text-sm">{{ $spec }}</span>
                </label>
                @endforeach
            </div>
            <p class="text-xs text-gray-500 mt-1">Pilih minimal 1 spesialisasi</p>
        </div>

        <!-- Base Price -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Dasar</label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                <input type="number" name="base_price" required min="50000" step="10000"
                       value="{{ old('base_price', $profile->base_price) }}"
                       class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
            </div>
            <p class="text-xs text-gray-500 mt-1">Minimal Rp 50.000</p>
        </div>

        <!-- Bank Account -->
        <div class="border-t border-gray-200 pt-4 mb-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Informasi Bank (untuk withdrawal)</h3>
            
            <div class="grid md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bank</label>
                    <select name="bank_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                        <option value="">Pilih Bank</option>
                        <option value="BCA" {{ old('bank_name', $profile->bank_name) == 'BCA' ? 'selected' : '' }}>BCA</option>
                        <option value="Mandiri" {{ old('bank_name', $profile->bank_name) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="BNI" {{ old('bank_name', $profile->bank_name) == 'BNI' ? 'selected' : '' }}>BNI</option>
                        <option value="BRI" {{ old('bank_name', $profile->bank_name) == 'BRI' ? 'selected' : '' }}>BRI</option>
                        <option value="CIMB Niaga" {{ old('bank_name', $profile->bank_name) == 'CIMB Niaga' ? 'selected' : '' }}>CIMB Niaga</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" 
                           value="{{ old('bank_account_number', $profile->bank_account_number) }}"
                           placeholder="1234567890"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemilik Rekening</label>
                <input type="text" name="bank_account_name" 
                       value="{{ old('bank_account_name', $profile->bank_account_name) }}"
                       placeholder="Sesuai dengan nama di rekening"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
            </div>
        </div>

        <!-- Availability -->
        <div class="mb-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_available" value="1"
                       {{ old('is_available', $profile->is_available) ? 'checked' : '' }}
                       class="rounded text-green-600 focus:ring-green-500">
                <span class="text-sm font-medium text-gray-700">Saya tersedia untuk menerima order</span>
            </label>
        </div>

        <!-- Submit -->
        <div class="flex gap-3">
            <button type="submit" 
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                Simpan Profile
            </button>
            <a href="{{ route('dashboard') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
