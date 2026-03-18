@extends('layouts.client')

@section('title', 'Keyword Research')

@section('content')
<div class="p-6" x-data="keywordResearch()">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">🔍 Keyword Research</h1>
        <p class="text-sm text-gray-500 mt-1">Riset keyword untuk optimasi konten & iklan Anda</p>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Cari Keyword</h2>
        
        <form @submit.prevent="searchKeyword" class="flex gap-3">
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Contoh: sepatu sneakers, nasi goreng, jual baju anak..."
                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   required>
            <button type="submit" 
                    :disabled="searching"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400">
                <span x-show="!searching">Cari</span>
                <span x-show="searching">Searching...</span>
            </button>
        </form>

        <!-- Search Result -->
        <div x-show="searchResult" x-cloak class="mt-6">
            <div class="p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
                <h3 class="font-semibold text-gray-900 mb-4">Hasil Riset:</h3>
                
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <!-- Main Keyword Info -->
                    <div class="bg-white p-4 rounded-lg">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="text-lg font-bold text-gray-900" x-text="searchResult?.keyword || ''"></div>
                                <div class="text-sm text-gray-600 mt-1">
                                    <span class="px-2 py-1 rounded-full text-xs"
                                          :class="{
                                              'bg-green-100 text-green-700': searchResult?.competition === 'LOW',
                                              'bg-yellow-100 text-yellow-700': searchResult?.competition === 'MEDIUM',
                                              'bg-red-100 text-red-700': searchResult?.competition === 'HIGH'
                                          }"
                                          x-text="searchResult?.competition === 'LOW' ? 'Kompetisi Rendah' : (searchResult?.competition === 'MEDIUM' ? 'Kompetisi Sedang' : 'Kompetisi Tinggi')">
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Volume Pencarian:</span>
                                <span class="text-sm font-semibold text-blue-600" x-text="searchResult?.search_volume ? searchResult.search_volume.toLocaleString('id-ID') + '/bulan' : '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">CPC Range:</span>
                                <span class="text-sm font-semibold text-green-600">
                                    Rp <span x-text="searchResult?.cpc_low ? searchResult.cpc_low.toLocaleString('id-ID') : '0'"></span> - 
                                    Rp <span x-text="searchResult?.cpc_high ? searchResult.cpc_high.toLocaleString('id-ID') : '0'"></span>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Trend:</span>
                                <span class="text-sm font-semibold" x-text="searchResult?.trend_direction || 'STABLE'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Recommendations -->
                    <div class="bg-white p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">💡 Rekomendasi:</h4>
                        <div class="space-y-2 text-sm">
                            <template x-if="searchResult?.competition === 'LOW' && searchResult?.search_volume > 1000">
                                <div class="flex items-start">
                                    <span class="text-green-500 mr-2">✓</span>
                                    <span class="text-gray-700">Keyword bagus! Volume tinggi & kompetisi rendah.</span>
                                </div>
                            </template>
                            <template x-if="searchResult?.competition === 'HIGH'">
                                <div class="flex items-start">
                                    <span class="text-yellow-500 mr-2">⚠</span>
                                    <span class="text-gray-700">Kompetisi tinggi. Pertimbangkan long-tail keywords.</span>
                                </div>
                            </template>
                            <template x-if="searchResult?.search_volume < 500">
                                <div class="flex items-start">
                                    <span class="text-blue-500 mr-2">ℹ</span>
                                    <span class="text-gray-700">Volume rendah. Cocok untuk niche market.</span>
                                </div>
                            </template>
                            <div class="flex items-start">
                                <span class="text-purple-500 mr-2">💰</span>
                                <span class="text-gray-700">
                                    Estimasi budget iklan: Rp <span x-text="searchResult?.cpc_low ? (searchResult.cpc_low * 100).toLocaleString('id-ID') : '0'"></span>/hari untuk 100 klik
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Keywords -->
                <template x-if="searchResult?.related_keywords && searchResult.related_keywords.length > 0">
                    <div class="bg-white p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">🔗 Related Keywords:</h4>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(related, index) in searchResult.related_keywords" :key="index">
                                <button @click="searchQuery = related.keyword; searchKeyword()"
                                        class="px-3 py-1 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 rounded-full text-sm transition"
                                        x-text="related.keyword">
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Recent Searches -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📜 Riwayat Pencarian</h2>
        
        <template x-if="recentKeywords.length === 0">
            <div class="text-center py-8 text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <p class="text-sm">Belum ada riwayat pencarian</p>
            </div>
        </template>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="keyword in recentKeywords" :key="keyword.id">
                <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-300 transition cursor-pointer"
                     @click="searchQuery = keyword.keyword; searchKeyword()">
                    <div class="flex justify-between items-start mb-2">
                        <div class="font-medium text-gray-900" x-text="keyword.keyword"></div>
                        <span class="text-xs px-2 py-1 rounded-full"
                              :class="{
                                  'bg-green-100 text-green-700': keyword.competition === 'LOW',
                                  'bg-yellow-100 text-yellow-700': keyword.competition === 'MEDIUM',
                                  'bg-red-100 text-red-700': keyword.competition === 'HIGH'
                              }"
                              x-text="keyword.competition">
                        </span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span x-text="keyword.search_volume ? keyword.search_volume.toLocaleString('id-ID') : '0'"></span> pencarian/bulan
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        CPC: Rp <span x-text="keyword.cpc_low ? keyword.cpc_low.toLocaleString('id-ID') : '0'"></span> - 
                        Rp <span x-text="keyword.cpc_high ? keyword.cpc_high.toLocaleString('id-ID') : '0'"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Trending Hashtags -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">🔥 Trending Hashtags</h2>
        
        <div class="grid md:grid-cols-3 gap-6">
            <div>
                <h3 class="font-medium text-gray-700 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    Instagram
                </h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="tag in trendingHashtags.instagram" :key="tag">
                        <span class="text-sm px-3 py-1 bg-pink-50 text-pink-700 rounded-full" x-text="tag"></span>
                    </template>
                </div>
            </div>

            <div>
                <h3 class="font-medium text-gray-700 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                    </svg>
                    TikTok
                </h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="tag in trendingHashtags.tiktok" :key="tag">
                        <span class="text-sm px-3 py-1 bg-gray-100 text-gray-700 rounded-full" x-text="tag"></span>
                    </template>
                </div>
            </div>

            <div>
                <h3 class="font-medium text-gray-700 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Facebook
                </h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="tag in trendingHashtags.facebook" :key="tag">
                        <span class="text-sm px-3 py-1 bg-blue-50 text-blue-700 rounded-full" x-text="tag"></span>
                    </template>
                </div>
            </div>
        </div>

        <div class="mt-4 p-3 bg-yellow-50 rounded-lg text-sm text-yellow-800">
            <strong>💡 Tips:</strong> Gunakan hashtag trending untuk meningkatkan reach konten Anda!
        </div>
    </div>
</div>

<script>
function keywordResearch() {
    return {
        searchQuery: '',
        searching: false,
        searchResult: null,
        recentKeywords: @json($recentKeywords),
        trendingHashtags: @json($trendingHashtags),

        async searchKeyword() {
            if (!this.searchQuery.trim()) return;

            this.searching = true;
            this.searchResult = null;

            try {
                const response = await fetch('/api/keyword-research/search', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        keyword: this.searchQuery
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.searchResult = data.data;
                    
                    // Add to recent keywords if not exists
                    const exists = this.recentKeywords.find(k => k.keyword === data.data.keyword);
                    if (!exists) {
                        this.recentKeywords.unshift(data.data);
                        if (this.recentKeywords.length > 20) {
                            this.recentKeywords.pop();
                        }
                    }
                } else {
                    alert('Error: ' + (data.message || 'Gagal melakukan keyword research'));
                }
            } catch (error) {
                console.error('Search error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.searching = false;
            }
        }
    }
}
</script>
@endsection
