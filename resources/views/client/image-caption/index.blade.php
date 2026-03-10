@extends('layouts.client')

@section('title', 'Image Caption Generator')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Image Caption Generator 🖼️</h1>
            <p class="text-sm text-gray-600 mt-1">Upload foto produk, AI generate caption otomatis!</p>
        </div>
        <a href="{{ route('image-caption.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Upload Foto
        </a>
    </div>

    @if($captions->isEmpty())
    <div class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada foto yang di-upload</h3>
        <p class="text-gray-600 mb-4">Upload foto produk pertama kamu dan biarkan AI generate caption!</p>
        <a href="{{ route('image-caption.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Upload Foto Sekarang
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($captions as $caption)
        <a href="{{ route('image-caption.show', $caption) }}" class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition group">
            <div class="aspect-square bg-gray-100 overflow-hidden">
                <img src="{{ Storage::url($caption->image_path) }}" alt="Product" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
            </div>
            <div class="p-4">
                <div class="flex items-center gap-2 mb-2">
                    @foreach($caption->detected_objects ?? [] as $object)
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                        {{ $object }}
                    </span>
                    @endforeach
                </div>
                <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($caption->caption_single, 100) }}</p>
                <p class="text-xs text-gray-400 mt-2">{{ $caption->created_at->diffForHumans() }}</p>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $captions->links() }}
    </div>
    @endif
</div>
@endsection
