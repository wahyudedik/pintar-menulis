@extends('layouts.client')

@section('title', 'Edit Proyek Bisnis')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Proyek Bisnis</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui informasi profil bisnis Anda</p>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <form action="{{ route('projects.update', $project) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bisnis / Proyek</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $project->business_name) }}" required
                           placeholder="Contoh: Kopi Susu Pak De"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('business_name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bidang Industri</label>
                    <input type="text" name="business_type" value="{{ old('business_type', $project->business_type) }}" required
                           placeholder="Contoh: Kuliner, Fashion, Teknologi"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('business_type')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Bisnis</label>
                    <textarea name="business_description" rows="4" required
                              placeholder="Jelaskan apa yang Anda jual, apa keunikan bisnis Anda, dll..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('business_description', $project->business_description) }}</textarea>
                    @error('business_description')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                    <input type="text" name="target_audience" value="{{ old('target_audience', $project->target_audience) }}"
                           placeholder="Contoh: Anak muda 18-25 tahun, pekerja kantoran"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('target_audience')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tone Merek (Opsional)</label>
                    <input type="text" name="brand_tone" value="{{ old('brand_tone', $project->brand_tone) }}"
                           placeholder="Contoh: Ceria, Berwibawa, Akrab"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('brand_tone')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('projects.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Update Proyek
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
