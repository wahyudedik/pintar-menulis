@extends('layouts.client')

@section('title', 'Caption Result')

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('image-caption.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Gallery
        </a>
        
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Caption Result ✨</h1>
            <form action="{{ route('image-caption.destroy', $imageCaption) }}" method="POST" onsubmit="return confirm('Yakin hapus caption ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Image & Info -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <img src="{{ Storage::url($imageCaption->image_path) }}" alt="Product" class="w-full">
            </div>

            <!-- Detected Objects -->
            @if($imageCaption->detected_objects)
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">🔍 Objek Terdeteksi</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($imageCaption->detected_objects as $object)
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-700">
                        {{ $object }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Dominant Colors -->
            @if($imageCaption->dominant_colors)
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">🎨 Warna Dominan</h3>
                <div class="flex gap-2">
                    @foreach($imageCaption->dominant_colors as $color)
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg border border-gray-300" style="background-color: {{ $color }}"></div>
                        <span class="text-xs text-gray-600">{{ $color }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Editing Tips -->
            @if($imageCaption->editing_tips)
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">💡 Tips Editing</h3>
                <ul class="space-y-2">
                    @foreach($imageCaption->editing_tips as $tip)
                    <li class="flex gap-2 text-sm text-gray-700">
                        <span class="text-blue-600">•</span>
                        <span>{{ $tip }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <!-- Captions -->
        <div class="space-y-4">
            <!-- Single Post Caption -->
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">📝 Caption Single Post</h3>
                    <button onclick="copySingleCaption()" class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition">
                        Copy
                    </button>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap" id="singleCaption">{{ $imageCaption->caption_single }}</p>
                </div>
            </div>

            <!-- Carousel Captions -->
            @if($imageCaption->caption_carousel)
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">📱 Caption Carousel (3 Slide)</h3>
                    <button onclick="copyCarouselCaption()" class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition">
                        Copy All
                    </button>
                </div>
                <div class="space-y-3">
                    @foreach($imageCaption->caption_carousel as $index => $slide)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-xs font-medium text-gray-600">Slide {{ $index + 1 }}</span>
                        </div>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap carousel-slide">{{ $slide }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Info Box -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <div class="text-sm">
                        <p class="font-medium text-gray-900 mb-1">💡 Tips Penggunaan</p>
                        <ul class="text-gray-700 space-y-1 text-xs">
                            <li>• Edit caption sesuai brand voice kamu</li>
                            <li>• Tambahkan CTA yang spesifik</li>
                            <li>• Gunakan carousel untuk storytelling</li>
                            <li>• Terapkan tips editing untuk hasil maksimal</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copySingleCaption() {
    const text = document.getElementById('singleCaption').textContent;
    copyToClipboard(text, 'Caption single post berhasil dicopy! ✓');
}

function copyCarouselCaption() {
    const slides = document.querySelectorAll('.carousel-slide');
    let text = '';
    slides.forEach((slide, index) => {
        text += `Slide ${index + 1}:\n${slide.textContent}\n\n`;
    });
    copyToClipboard(text.trim(), 'Caption carousel berhasil dicopy! ✓');
}

function copyToClipboard(text, message) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        alert(message);
    } catch (err) {
        alert('Gagal copy caption');
    }
    
    document.body.removeChild(textarea);
}
</script>
@endsection
