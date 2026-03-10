@extends('layouts.client')

@section('title', 'Upload Foto Produk')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('image-caption.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>
        
        <h1 class="text-2xl font-semibold text-gray-900">Upload Foto Produk 🖼️</h1>
        <p class="text-sm text-gray-600 mt-1">AI akan analisis foto dan generate caption otomatis</p>
    </div>

    <form action="{{ route('image-caption.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf
        
        <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-6">
            <!-- Image Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Produk</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition" id="dropZone">
                    <input type="file" name="image" id="imageInput" accept="image/*" class="hidden" required>
                    
                    <div id="uploadPrompt">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-600 mb-2">Klik atau drag & drop foto di sini</p>
                        <p class="text-xs text-gray-500">JPG, PNG (Max 5MB)</p>
                    </div>

                    <div id="imagePreview" class="hidden">
                        <img src="" alt="Preview" class="max-h-64 mx-auto rounded-lg">
                        <button type="button" onclick="removeImage()" class="mt-3 text-sm text-red-600 hover:text-red-700">
                            Ganti Foto
                        </button>
                    </div>
                </div>
                @error('image')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Optional Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Bisnis (Opsional)</label>
                    <input type="text" name="business_type" placeholder="Contoh: Kuliner, Fashion, Kosmetik" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk (Opsional)</label>
                    <input type="text" name="product_name" placeholder="Contoh: Nasi Goreng Spesial" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">AI akan generate:</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-700">
                            <li>Caption untuk single post</li>
                            <li>Caption untuk carousel (3 slide)</li>
                            <li>Deteksi objek dalam foto</li>
                            <li>Tips editing & filter</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('image-caption.index') }}" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Generate Caption
                </button>
            </div>
        </div>
    </form>

    <!-- Loading Modal -->
    <div id="loadingModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg p-8 max-w-sm mx-4 text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Sedang Menganalisis Foto...</h3>
            <p class="text-sm text-gray-600">AI sedang membaca foto dan generate caption</p>
            <p class="text-xs text-gray-500 mt-2">Proses ini memakan waktu 10-30 detik</p>
        </div>
    </div>
</div>

<script>
const dropZone = document.getElementById('dropZone');
const imageInput = document.getElementById('imageInput');
const uploadPrompt = document.getElementById('uploadPrompt');
const imagePreview = document.getElementById('imagePreview');
const uploadForm = document.getElementById('uploadForm');
const loadingModal = document.getElementById('loadingModal');

// Click to upload
dropZone.addEventListener('click', () => imageInput.click());

// File input change
imageInput.addEventListener('change', handleFileSelect);

// Drag & drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-blue-400', 'bg-blue-50');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        imageInput.files = files;
        handleFileSelect();
    }
});

function handleFileSelect() {
    const file = imageInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.querySelector('img').src = e.target.result;
            uploadPrompt.classList.add('hidden');
            imagePreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    imageInput.value = '';
    uploadPrompt.classList.remove('hidden');
    imagePreview.classList.add('hidden');
}

// Show loading on submit
uploadForm.addEventListener('submit', () => {
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');
});
</script>
@endsection
