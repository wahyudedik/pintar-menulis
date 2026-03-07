@extends('layouts.client')

@section('title', 'Caption History')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Caption History</h1>
                <p class="text-sm text-gray-500 mt-1">Semua caption yang pernah di-generate untuk machine learning AI</p>
            </div>
            <button onclick="showClearHistoryModal()" 
                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Clear History
            </button>
        </div>
        
        <!-- Info Box: How History Works -->
        <div class="mt-4 bg-blue-50 border-l-4 border-blue-600 p-4 rounded-r-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-blue-900 mb-1">🤖 Cara Kerja Caption History</h4>
                    <p class="text-sm text-blue-800 mb-2">
                        Setiap caption yang Anda generate akan tersimpan di sini. AI menggunakan data ini untuk:
                    </p>
                    <ul class="text-sm text-blue-800 space-y-1 ml-4 list-disc">
                        <li><strong>Avoid Repetition</strong>: AI tidak akan generate caption yang mirip dengan yang sudah pernah dibuat</li>
                        <li><strong>Learn Your Style</strong>: AI belajar dari caption yang sukses (dari Analytics)</li>
                        <li><strong>Dynamic Creativity</strong>: Semakin sering generate, AI semakin creative (temperature naik)</li>
                    </ul>
                    <div class="mt-3 p-3 bg-blue-100 rounded-lg">
                        <p class="text-sm text-blue-900 font-semibold">
                            📊 Current AI Status:
                        </p>
                        <p class="text-sm text-blue-800 mt-1">
                            • Generated in last 7 days: <strong>{{ $recentCount }} captions</strong><br>
                            • AI Temperature: <strong>{{ $aiTemperature }}</strong> 
                            @if($aiTemperature == 0.7)
                                (Balanced - Default)
                            @elseif($aiTemperature == 0.8)
                                (More Creative - Frequent User)
                            @else
                                (Very Creative - Power User)
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Generated</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_generated'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Unique Captions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_unique'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Repeated</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_repeated'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Last 7 Days</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['last_7_days'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('caption-history.index') }}" class="grid md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $cat)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                <select name="platform" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Platforms</option>
                    @foreach($platforms as $plat)
                        <option value="{{ $plat }}" {{ request('platform') == $plat ? 'selected' : '' }}>
                            {{ ucfirst($plat) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Apply Filters
                </button>
                <a href="{{ route('caption-history.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Caption History Table -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Caption History</h3>
            <p class="text-xs text-gray-500 mt-1">AI menggunakan data ini untuk avoid repetition dan learn your style</p>
        </div>
        <div class="overflow-x-auto">
            @if($histories->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Caption</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Platform</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Tone</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Times Generated</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Last Generated</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($histories as $history)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <div class="max-w-md">
                                <p class="line-clamp-2">{{ $history->caption_text }}</p>
                                @if($history->brief_summary)
                                    <p class="text-xs text-gray-500 mt-1">Brief: {{ Str::limit($history->brief_summary, 50) }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs">
                                {{ $history->category ? ucfirst(str_replace('_', ' ', $history->category)) : 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 bg-green-50 text-green-700 rounded text-xs">
                                {{ $history->platform ? ucfirst($history->platform) : 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $history->tone ? ucfirst($history->tone) : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($history->times_generated > 1)
                                <span class="px-2 py-1 bg-yellow-50 text-yellow-700 rounded text-xs font-semibold">
                                    {{ $history->times_generated }}x
                                </span>
                            @else
                                <span class="text-gray-500">1x</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $history->last_generated_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <button onclick="viewCaption({{ $history->id }})" 
                                    class="text-blue-600 hover:text-blue-700 mr-2">
                                View
                            </button>
                            <button onclick="deleteCaption({{ $history->id }})" 
                                    class="text-red-600 hover:text-red-700">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="p-4 border-t border-gray-200">
                {{ $histories->links() }}
            </div>
            @else
            <div class="p-8 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 mb-2">Belum ada caption history</p>
                <p class="text-sm text-gray-400 mb-4">Mulai generate caption di AI Generator untuk melihat history!</p>
                <a href="{{ route('ai.generator') }}" class="inline-block px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                    🚀 Generate Caption Sekarang
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- View Caption Modal -->
<div id="viewCaptionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Caption Detail</h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <div id="captionDetailContent" class="p-6">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<!-- Clear History Modal -->
<div id="clearHistoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Clear Caption History?</h3>
            <p class="text-sm text-gray-600 mb-4">
                Ini akan menghapus semua caption history dan <strong>reset AI learning</strong>. 
                AI akan mulai dari awal lagi tanpa memory caption sebelumnya.
            </p>
            <p class="text-sm text-red-600 mb-6">
                ⚠️ Warning: Action ini tidak bisa di-undo!
            </p>
            
            <div class="flex gap-3">
                <button onclick="confirmClearHistory()" 
                        class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                    Yes, Clear All
                </button>
                <button onclick="closeClearModal()"
                        class="px-6 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function viewCaption(id) {
        fetch(`/caption-history/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const history = data.history;
                    document.getElementById('captionDetailContent').innerHTML = `
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Caption Text:</label>
                                <div class="p-4 bg-gray-50 rounded-lg text-sm text-gray-900 whitespace-pre-wrap">
                                    ${history.caption_text}
                                </div>
                            </div>
                            
                            ${history.brief_summary ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Brief Summary:</label>
                                <p class="text-sm text-gray-600">${history.brief_summary}</p>
                            </div>
                            ` : ''}
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category:</label>
                                    <p class="text-sm text-gray-900">${history.category || 'N/A'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subcategory:</label>
                                    <p class="text-sm text-gray-900">${history.subcategory || 'N/A'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Platform:</label>
                                    <p class="text-sm text-gray-900">${history.platform || 'N/A'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tone:</label>
                                    <p class="text-sm text-gray-900">${history.tone || 'N/A'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Times Generated:</label>
                                    <p class="text-sm text-gray-900 font-semibold">${history.times_generated}x</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Generated:</label>
                                    <p class="text-sm text-gray-900">${new Date(history.last_generated_at).toLocaleString()}</p>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-900">
                                    <strong>Hash:</strong> <code class="text-xs">${history.hash}</code>
                                </p>
                                <p class="text-xs text-blue-700 mt-2">
                                    Hash digunakan untuk detect caption yang mirip/sama
                                </p>
                            </div>
                        </div>
                    `;
                    document.getElementById('viewCaptionModal').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load caption details');
            });
    }
    
    function closeViewModal() {
        document.getElementById('viewCaptionModal').classList.add('hidden');
    }
    
    function deleteCaption(id) {
        if (!confirm('Delete this caption from history? AI will no longer avoid this caption.')) {
            return;
        }
        
        fetch(`/caption-history/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✓ Caption deleted from history');
                location.reload();
            } else {
                alert('Failed to delete: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete caption');
        });
    }
    
    function showClearHistoryModal() {
        document.getElementById('clearHistoryModal').classList.remove('hidden');
    }
    
    function closeClearModal() {
        document.getElementById('clearHistoryModal').classList.add('hidden');
    }
    
    function confirmClearHistory() {
        fetch('/caption-history/clear-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✓ ' + data.message);
                location.reload();
            } else {
                alert('Failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to clear history');
        });
    }
</script>
@endsection
