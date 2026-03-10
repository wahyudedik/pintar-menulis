@extends('layouts.client')

@section('title', 'Generate Bulk Content')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('bulk-content.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Calendars
        </a>
        <h1 class="text-2xl font-semibold text-gray-900">Generate Bulk Content</h1>
        <p class="text-sm text-gray-500 mt-1">Buat konten untuk 7 hari atau 30 hari sekaligus dengan AI</p>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <form id="bulkContentForm" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Calendar <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                    id="title" 
                    name="title" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Contoh: Konten Instagram Februari 2026">
            </div>

            <!-- Duration -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Durasi <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="duration" value="7_days" required class="sr-only peer">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center peer-checked:bg-blue-600">
                                    <span class="text-lg font-bold peer-checked:text-white">7</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">7 Hari</p>
                                    <p class="text-xs text-gray-500">1 Minggu Konten</p>
                                </div>
                            </div>
                        </div>
                        <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-blue-600 peer-checked:bg-blue-600 flex items-center justify-center">
                            <div class="w-2 h-2 bg-white rounded-full hidden peer-checked:block"></div>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="duration" value="30_days" required class="sr-only peer">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center peer-checked:bg-purple-600">
                                    <span class="text-lg font-bold peer-checked:text-white">30</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">30 Hari</p>
                                    <p class="text-xs text-gray-500">1 Bulan Konten</p>
                                </div>
                            </div>
                        </div>
                        <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-purple-600 peer-checked:bg-purple-600 flex items-center justify-center">
                            <div class="w-2 h-2 bg-white rounded-full hidden peer-checked:block"></div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Start Date -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Mulai <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                    id="start_date" 
                    name="start_date" 
                    required
                    min="{{ date('Y-m-d') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                    Kategori Bisnis <span class="text-red-500">*</span>
                </label>
                <select id="category" 
                    name="category" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Kategori</option>
                    <option value="fashion">Fashion & Clothing</option>
                    <option value="food">Food & Beverage</option>
                    <option value="beauty">Beauty & Skincare</option>
                    <option value="electronics">Electronics</option>
                    <option value="home">Home & Living</option>
                    <option value="health">Health & Wellness</option>
                    <option value="education">Education</option>
                    <option value="services">Services</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>

            <!-- Platform -->
            <div>
                <label for="platform" class="block text-sm font-medium text-gray-700 mb-1">
                    Platform Utama <span class="text-red-500">*</span>
                </label>
                <select id="platform" 
                    name="platform" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Platform</option>
                    <option value="instagram">Instagram</option>
                    <option value="tiktok">TikTok</option>
                    <option value="facebook">Facebook</option>
                    <option value="twitter">Twitter/X</option>
                    <option value="shopee">Shopee</option>
                    <option value="tokopedia">Tokopedia</option>
                    <option value="whatsapp">WhatsApp Status</option>
                </select>
            </div>

            <!-- Tone -->
            <div>
                <label for="tone" class="block text-sm font-medium text-gray-700 mb-1">
                    Tone of Voice <span class="text-red-500">*</span>
                </label>
                <select id="tone" 
                    name="tone" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Tone</option>
                    <option value="casual">Casual & Friendly</option>
                    <option value="professional">Professional</option>
                    <option value="funny">Funny & Humorous</option>
                    <option value="inspirational">Inspirational</option>
                    <option value="educational">Educational</option>
                    <option value="promotional">Promotional</option>
                </select>
            </div>

            <!-- Brief -->
            <div>
                <label for="brief" class="block text-sm font-medium text-gray-700 mb-1">
                    Brief Produk/Bisnis <span class="text-red-500">*</span>
                </label>
                <textarea id="brief" 
                    name="brief" 
                    rows="4"
                    required
                    maxlength="1000"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Jelaskan produk/bisnis Anda, target audience, unique selling point, dll. Semakin detail, semakin bagus hasilnya!"></textarea>
                <p class="text-xs text-gray-500 mt-1">Maksimal 1000 karakter</p>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Yang akan di-generate:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Caption untuk setiap hari dengan tema berbeda</li>
                            <li>Jadwal posting otomatis (pagi, siang, malam)</li>
                            <li>Hashtag relevan untuk setiap caption</li>
                            <li>Variasi konten (promo, edukasi, testimoni, dll)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('bulk-content.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Cancel
                </a>
                <button type="submit" 
                    id="generateBtn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Generate Konten</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Generating Content...</h3>
            <p class="text-sm text-gray-600 mb-4">AI sedang membuat konten untuk Anda. Mohon tunggu sebentar.</p>
            <div class="bg-gray-100 rounded-lg p-3">
                <p class="text-xs text-gray-600" id="loadingStatus">Initializing...</p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('bulkContentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Show loading modal
    const modal = document.getElementById('loadingModal');
    const statusEl = document.getElementById('loadingStatus');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    const duration = data.duration === '7_days' ? 7 : 30;
    statusEl.textContent = `Generating ${duration} days of content...`;
    
    try {
        const response = await fetch('{{ route("bulk-content.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            statusEl.textContent = 'Success! Redirecting...';
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1000);
        } else {
            throw new Error(result.message || 'Generation failed');
        }
    } catch (error) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        alert('Error: ' + error.message);
    }
});
</script>
@endsection
