@extends('layouts.client')

@section('title', 'Edit Content - ' . $content->title)

@section('content')
<div class="p-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('projects.index') }}" class="hover:text-blue-600">Projects</a>
        <span>/</span>
        <a href="{{ route('projects.show', $project) }}" class="hover:text-blue-600">{{ $project->business_name }}</a>
        <span>/</span>
        <a href="{{ route('projects.content.index', $project) }}" class="hover:text-blue-600">Content</a>
        <span>/</span>
        <a href="{{ route('projects.content.show', [$project, $content]) }}" class="hover:text-blue-600">{{ Str::limit($content->title, 30) }}</a>
        <span>/</span>
        <span class="text-gray-900">Edit</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h1 class="text-xl font-bold text-gray-900 mb-6">Edit Content</h1>

                <form action="{{ route('projects.content.update', [$project, $content]) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @if($errors->any())
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $content->title) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konten <span class="text-red-500">*</span></label>
                        <textarea name="content" rows="12" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">{{ old('content', $content->content) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                        <select name="platform" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Platform</option>
                            @foreach(['instagram','tiktok','facebook','twitter','whatsapp','shopee','tokopedia','website'] as $p)
                            <option value="{{ $p }}" {{ old('platform', $content->platform) === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Perubahan</label>
                        <input type="text" name="change_notes" value="{{ old('change_notes') }}"
                               placeholder="Apa yang diubah? (opsional)"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-sm">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('projects.content.show', [$project, $content]) }}"
                           class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Info Content</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tipe</span>
                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $content->content_type)) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full">{{ ucfirst($content->status) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Versi</span>
                        <span class="font-medium">v{{ $content->version }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dibuat</span>
                        <span class="font-medium">{{ $content->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
