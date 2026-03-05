@extends('layouts.client')

@section('title', 'Caption Analytics')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Caption Analytics</h1>
                <p class="text-sm text-gray-500 mt-1">Track performa caption Anda dan dapatkan insights untuk AI yang lebih pintar</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('analytics.export.pdf') }}" 
                   class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('analytics.export.csv') }}" 
                   class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Captions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_captions'] }}</p>
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
                    <p class="text-sm text-gray-600">Avg Engagement</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['avg_engagement'], 1) }}%</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Reach</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_reach']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Successful</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['successful_captions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <!-- Platform Performance -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Platform Performance</h3>
            @if($platformPerformance->count() > 0)
            <canvas id="platformChart"></canvas>
            @else
            <p class="text-gray-500 text-center py-8 text-sm">Belum ada data platform</p>
            @endif
        </div>

        <!-- Category Performance -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Category Performance</h3>
            @if($categoryPerformance->count() > 0)
            <canvas id="categoryChart"></canvas>
            @else
            <p class="text-gray-500 text-center py-8 text-sm">Belum ada data kategori</p>
            @endif
        </div>
    </div>

    <!-- Engagement Over Time -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Engagement Over Time (Last 30 Days)</h3>
        @if($engagementOverTime->count() > 0)
        <canvas id="timeChart"></canvas>
        @else
        <p class="text-gray-500 text-center py-8 text-sm">Belum ada data engagement</p>
        @endif
    </div>

    <!-- Top Performing Captions -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top Performing Captions</h3>
        </div>
        <div class="p-4">
            @if($topCaptions->count() > 0)
            <div class="space-y-3">
                @foreach($topCaptions as $caption)
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 line-clamp-2">{{ Str::limit($caption->caption_text, 100) }}</p>
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-600">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded">{{ $caption->platform ?? 'N/A' }}</span>
                                <span>{{ $caption->category ?? 'N/A' }}</span>
                                <span>{{ $caption->posted_at ? $caption->posted_at->format('d M Y') : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-lg font-bold text-green-600">{{ number_format($caption->engagement_rate, 1) }}%</p>
                            <p class="text-xs text-gray-500">engagement</p>
                        </div>
                    </div>
                    <div class="flex gap-4 text-xs text-gray-600 mt-3 pt-3 border-t">
                        <span>❤️ {{ number_format($caption->likes) }}</span>
                        <span>💬 {{ number_format($caption->comments) }}</span>
                        <span>🔄 {{ number_format($caption->shares) }}</span>
                        <span>👁️ {{ number_format($caption->reach) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-8">Belum ada caption yang di-track. Mulai track caption Anda dari AI Generator!</p>
            @endif
        </div>
    </div>

    <!-- Recent Captions -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Recent Captions</h3>
            <button onclick="showAddCaptionModal()" class="text-sm text-blue-600 hover:text-blue-700">
                + Add Caption
            </button>
        </div>
        <div class="overflow-x-auto">
            @if($recentCaptions->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Caption</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Platform</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Engagement</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Posted</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentCaptions as $caption)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($caption->caption_text, 60) }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">{{ $caption->platform ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-green-600">{{ number_format($caption->engagement_rate, 1) }}%</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $caption->posted_at ? $caption->posted_at->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <button onclick="editMetrics({{ $caption->id }})" class="text-blue-600 hover:text-blue-700">
                                Edit Metrics
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-gray-500 text-center py-8">Belum ada caption</p>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($platformPerformance->count() > 0)
    const platformCtx = document.getElementById('platformChart').getContext('2d');
    new Chart(platformCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($platformPerformance->pluck('platform')) !!},
            datasets: [{
                label: 'Avg Engagement %',
                data: {!! json_encode($platformPerformance->pluck('avg_engagement')) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    @endif

    @if($categoryPerformance->count() > 0)
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryPerformance->pluck('category')) !!},
            datasets: [{
                data: {!! json_encode($categoryPerformance->pluck('avg_engagement')) !!},
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif

    @if($engagementOverTime->count() > 0)
    const timeCtx = document.getElementById('timeChart').getContext('2d');
    new Chart(timeCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($engagementOverTime->pluck('date')) !!},
            datasets: [{
                label: 'Engagement Rate %',
                data: {!! json_encode($engagementOverTime->pluck('avg_engagement')) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    @endif

    function editMetrics(id) {
        // TODO: Implement edit metrics modal
        alert('Edit metrics feature coming soon! ID: ' + id);
    }

    function showAddCaptionModal() {
        alert('Add caption feature - integrate with AI Generator');
    }
</script>

<!-- Edit Metrics Modal -->
<div id="editMetricsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Update Caption Metrics</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <form id="metricsForm" onsubmit="saveMetrics(event)" class="p-6">
            <input type="hidden" id="caption_id" name="caption_id">
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Likes</label>
                    <input type="number" id="likes" name="likes" min="0" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
                    <input type="number" id="comments" name="comments" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Shares</label>
                    <input type="number" id="shares" name="shares" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Saves</label>
                    <input type="number" id="saves" name="saves" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reach</label>
                    <input type="number" id="reach" name="reach" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Impressions</label>
                    <input type="number" id="impressions" name="impressions" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Clicks</label>
                    <input type="number" id="clicks" name="clicks" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Rating</label>
                    <select id="user_rating" name="user_rating"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">No rating</option>
                        <option value="1">⭐ 1 - Poor</option>
                        <option value="2">⭐⭐ 2 - Fair</option>
                        <option value="3">⭐⭐⭐ 3 - Good</option>
                        <option value="4">⭐⭐⭐⭐ 4 - Very Good</option>
                        <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea id="user_notes" name="user_notes" rows="3"
                          placeholder="What worked well? What didn't?"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" id="marked_as_successful" name="marked_as_successful"
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Mark as successful caption (for AI training)</span>
                </label>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" 
                        class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Save Metrics
                </button>
                <button type="button" onclick="closeEditModal()"
                        class="px-6 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function editMetrics(id) {
        // Fetch caption data
        fetch(`/analytics/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const caption = data.caption;
                    document.getElementById('caption_id').value = caption.id;
                    document.getElementById('likes').value = caption.likes || 0;
                    document.getElementById('comments').value = caption.comments || 0;
                    document.getElementById('shares').value = caption.shares || 0;
                    document.getElementById('saves').value = caption.saves || 0;
                    document.getElementById('reach').value = caption.reach || 0;
                    document.getElementById('impressions').value = caption.impressions || 0;
                    document.getElementById('clicks').value = caption.clicks || 0;
                    document.getElementById('user_rating').value = caption.user_rating || '';
                    document.getElementById('user_notes').value = caption.user_notes || '';
                    document.getElementById('marked_as_successful').checked = caption.marked_as_successful;
                    
                    document.getElementById('editMetricsModal').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load caption data');
            });
    }
    
    function closeEditModal() {
        document.getElementById('editMetricsModal').classList.add('hidden');
    }
    
    function saveMetrics(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const captionId = formData.get('caption_id');
        
        const data = {
            likes: parseInt(formData.get('likes')) || 0,
            comments: parseInt(formData.get('comments')) || 0,
            shares: parseInt(formData.get('shares')) || 0,
            saves: parseInt(formData.get('saves')) || 0,
            reach: parseInt(formData.get('reach')) || 0,
            impressions: parseInt(formData.get('impressions')) || 0,
            clicks: parseInt(formData.get('clicks')) || 0,
            user_rating: formData.get('user_rating') ? parseInt(formData.get('user_rating')) : null,
            user_notes: formData.get('user_notes'),
            marked_as_successful: formData.get('marked_as_successful') === 'on'
        };
        
        fetch(`/analytics/${captionId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✓ Metrics updated successfully!\nEngagement Rate: ' + data.engagement_rate + '%');
                closeEditModal();
                location.reload();
            } else {
                alert('Failed to update: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to save metrics');
        });
    }
</script>
@endsection
