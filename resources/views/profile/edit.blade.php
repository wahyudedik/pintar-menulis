@extends('layouts.app-layout')

@section('title', 'Edit Profil')

@section('sidebar-menu')
    @include('layouts.' . auth()->user()->role)
@endsection

@section('content')
<div class="p-6 max-w-3xl mx-auto" x-data="{ avatarPreview: null }">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Profil</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi akun dan tampilan profil kamu</p>
    </div>

    @if(session('status') === 'profile-updated')
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg text-sm text-green-800">Profil berhasil diperbarui.</div>
    @endif
    @if(session('status') === 'google-disconnected')
    <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg text-sm text-blue-800">Akun Google berhasil diputus.</div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PATCH')

        {{-- Foto Profil --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Foto Profil</h2>
            <div class="flex items-center gap-6">
                <div class="relative flex-shrink-0">
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                    </template>
                    <template x-if="!avatarPreview">
                        @if($user->avatar)
                            @if(str_starts_with($user->avatar, 'http'))
                                <img src="{{ $user->avatar }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                            @else
                                <img src="{{ Storage::url($user->avatar) }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                            @endif
                        @else
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold border-4 border-white shadow-md">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </template>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload foto baru</label>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                           @change="const f=$event.target.files[0]; if(f){const r=new FileReader();r.onload=e=>avatarPreview=e.target.result;r.readAsDataURL(f);}">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP - maks 2MB</p>
                    @error('avatar')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Info Dasar --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-900">Informasi Dasar</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                <textarea name="bio" rows="3" placeholder="Ceritakan sedikit tentang diri kamu..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 resize-none">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           placeholder="+62 812 3456 7890"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @error('phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $user->location) }}"
                           placeholder="Jakarta, Indonesia"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @error('location')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                <input type="url" name="website" value="{{ old('website', $user->website) }}"
                       placeholder="https://yourwebsite.com"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                @error('website')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Operator Section --}}
        @if($user->role === 'operator')
        @php $op = $user->operatorProfile; @endphp
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-900">Profil Operator</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bio Operator</label>
                <textarea name="operator[bio]" rows="4" required
                          placeholder="Ceritakan pengalaman dan keahlian kamu sebagai operator..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 resize-none">{{ old('operator.bio', $op->bio ?? '') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Minimal 50 karakter</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Portfolio URL</label>
                <input type="url" name="operator[portfolio_url]"
                       value="{{ old('operator.portfolio_url', $op->portfolio_url ?? '') }}"
                       placeholder="https://portfolio.com"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Spesialisasi</label>
                @php
                $allSpecs = ['Social Media','Ads','Website','Landing Page','Marketplace','Email Marketing','Proposal','Company Profile','Personal Branding','UX Writing','Product Description','SEO','Content Writing'];
                $selectedSpecs = old('operator.specializations', $op->specializations ?? []);
                @endphp
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($allSpecs as $spec)
                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer text-sm">
                        <input type="checkbox" name="operator[specializations][]" value="{{ $spec }}"
                               {{ in_array($spec, (array)$selectedSpecs) ? 'checked' : '' }}
                               class="rounded text-blue-600 focus:ring-blue-500">
                        {{ $spec }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Dasar</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                    <input type="number" name="operator[base_price]" min="50000" step="10000"
                           value="{{ old('operator.base_price', $op->base_price ?? 50000) }}"
                           class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="text-xs text-gray-400 mt-1">Minimal Rp 50.000</p>
            </div>
            <div class="border-t border-gray-100 pt-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Informasi Bank (untuk withdrawal)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                        <select name="operator[bank_name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Bank</option>
                            @foreach(['BCA','Mandiri','BNI','BRI','CIMB Niaga','Danamon','Permata','BTN'] as $bank)
                            <option value="{{ $bank }}" {{ old('operator.bank_name', $op->bank_name ?? '') == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                        <input type="text" name="operator[bank_account_number]"
                               value="{{ old('operator.bank_account_number', $op->bank_account_number ?? '') }}"
                               placeholder="1234567890"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening</label>
                    <input type="text" name="operator[bank_account_name]"
                           value="{{ old('operator.bank_account_name', $op->bank_account_name ?? '') }}"
                           placeholder="Sesuai nama di rekening"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="operator[is_available]" value="1"
                           {{ old('operator.is_available', $op->is_available ?? true) ? 'checked' : '' }}
                           class="rounded text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Saya tersedia untuk menerima order</span>
                </label>
            </div>
        </div>
        @endif

        {{-- Google Account --}}
        @if($user->google_id)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-2">Akun Google</h2>
            <p class="text-sm text-gray-500 mb-3">Akun kamu terhubung dengan Google.</p>
            <form method="POST" action="{{ route('profile.disconnect-google') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">Putus koneksi Google</button>
            </form>
        </div>
        @endif

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                Simpan Perubahan
            </button>
            <a href="{{ route('dashboard') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm text-gray-700">
                Batal
            </a>
        </div>
    </form>

    {{-- Delete Account --}}
    <div class="mt-8 bg-white rounded-xl border border-red-200 p-6">
        <h2 class="text-base font-semibold text-red-700 mb-2">Hapus Akun</h2>
        <p class="text-sm text-gray-500 mb-4">Setelah dihapus, semua data tidak dapat dipulihkan.</p>
        <button onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                class="text-sm text-red-600 hover:text-red-700 font-medium border border-red-300 px-4 py-2 rounded-lg hover:bg-red-50 transition">
            Hapus Akun Saya
        </button>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl p-6 max-w-md w-full">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi Hapus Akun</h3>
            <p class="text-sm text-gray-500 mb-4">Masukkan password untuk mengkonfirmasi penghapusan akun.</p>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <input type="password" name="password" placeholder="Password kamu"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-4 focus:ring-2 focus:ring-red-500">
                @error('password', 'userDeletion')
                <p class="text-xs text-red-600 mb-3">{{ $message }}</p>
                @enderror
                <div class="flex gap-3">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">Hapus Akun</button>
                    <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Batal</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
