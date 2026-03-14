@extends('layouts.client')

@section('title', 'Competitor Comparison')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('competitor-analysis.index') }}" class="hover:text-purple-600">Competitor Analysis</a>
            <span>/</span>
            <span class="text-gray-900">Comparison</span>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900">📊 Competitor Comparison</h1>
        <p class="text-sm text-gray-500 mt-1">Bandingkan performa {{ $competitors->count() }} kompetitor untuk strategi yang lebih baik</p>
    </div>

    <!-- AI Insights Summary -->
    @if(isset($comparisonInsights) && is_array($comparisonInsights))
    <div class="mb-6 bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-xl p-6">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-purple-900 mb-2">🤖 AI Analysis Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-purple-800 mb-1">🏆 Top Performer</p>
                        <p class="text-lg font-bold text-purple-900">@{{ $comparisonInsights['winner'] ?? 'N/A' }}</p>
                        @if(isset($comparisonInsights['winner_reasons']) && is_array($comparisonInsights['winner_reasons']))
                        <ul class="text-xs text-purple-700 mt-1">
                            @foreach($comparisonInsights['winner_reasons'] as $reason)
                            <li>• {{ $reason }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-purple-800 mb-1">💡 Key Opportunities</p>
                        @if(isset($comparisonInsights['opportunities']) && is_array($comparisonInsights['opportunities']))
                        <ul class="text-sm text-purple-700">
                            @foreach(array_slice($comparisonInsights['opportunities'], 0, 2) as $opportunity)
                            <li>• {{ $opportunity }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-sm text-purple-700">Analyzing opportunities...</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Fallback when no AI insights available -->
    <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <div class="flex items-center space-x-3">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm font-medium text-yellow-800">AI Analysis in Progress</p>
                <p class="text-xs text-yellow-700">Generating comparison insights... Please refresh in a moment.</p>
            </div>
        </div>
    </div>
    @endif
    <!-- Comparison Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">📈 Performance Comparison</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Competitor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Followers</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posts</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Engagement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($competitors as $competitor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($competitor->username, 0, 1)) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $competitor->username }}</p>
                                    <p class="text-xs text-gray-500">{{ $competitor->profile_name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 capitalize">
                                {{ $competitor->platform }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($competitor->followers_count) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $competitor->analysisSummary->first()->total_posts ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $latestSummary = $competitor->analysisSummary->first();
                                $engagementRate = $latestSummary->avg_engagement_rate ?? 0;
                                $engagementColor = $engagementRate >= 5 ? 'text-green-600' : ($engagementRate >= 3 ? 'text-yellow-600' : 'text-red-600');
                            @endphp
                            <span class="text-sm font-medium {{ $engagementColor }}">
                                {{ number_format($engagementRate, 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                {{ $competitor->category ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $competitor->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $competitor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Detailed Insights -->
    @if(isset($comparisonInsights) && is_array($comparisonInsights))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Key Insights -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Key Insights
            </h3>
            @if(isset($comparisonInsights['key_insights']) && is_array($comparisonInsights['key_insights']))
            <ul class="space-y-3">
                @foreach($comparisonInsights['key_insights'] as $insight)
                <li class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-2"></div>
                    <p class="text-sm text-gray-700">{{ $insight }}</p>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-sm text-gray-500">Generating insights...</p>
            @endif
        </div>

        <!-- Recommendations -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Strategic Recommendations
            </h3>
            @if(isset($comparisonInsights['recommendations']) && is_array($comparisonInsights['recommendations']))
            <ul class="space-y-3">
                @foreach($comparisonInsights['recommendations'] as $recommendation)
                <li class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-2 h-2 bg-green-600 rounded-full mt-2"></div>
                    <p class="text-sm text-gray-700">{{ $recommendation }}</p>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-sm text-gray-500">Generating recommendations...</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Performance Charts -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Performance Visualization</h3>
        
        <!-- Engagement Rate Chart -->
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Engagement Rate Comparison</h4>
            <div class="space-y-3">
                @foreach($competitors as $competitor)
                @php
                    $latestSummary = $competitor->analysisSummary->first();
                    $engagementRate = $latestSummary->avg_engagement_rate ?? 0;
                    $maxEngagement = $competitors->max(function($c) { 
                        $summary = $c->analysisSummary->first();
                        return $summary->avg_engagement_rate ?? 0; 
                    });
                    $barWidth = $maxEngagement > 0 ? ($engagementRate / $maxEngagement) * 100 : 0;
                @endphp
                <div class="flex items-center space-x-4">
                    <div class="w-24 text-sm text-gray-600 truncate">{{ $competitor->username }}</div>
                    <div class="flex-1 bg-gray-200 rounded-full h-4 relative">
                        <div class="bg-gradient-to-r from-purple-500 to-blue-500 h-4 rounded-full transition-all duration-500" 
                             style="width: {{ $barWidth }}%"></div>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-medium text-white">
                            {{ number_format($engagementRate, 1) }}%
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Posts Count Chart -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">Posts Count Comparison</h4>
            <div class="space-y-3">
                @foreach($competitors as $competitor)
                @php
                    $latestSummary = $competitor->analysisSummary->first();
                    $postsCount = $latestSummary->total_posts ?? 0;
                    $maxPosts = $competitors->max(function($c) { 
                        $summary = $c->analysisSummary->first();
                        return $summary->total_posts ?? 0; 
                    });
                    $barWidth = $maxPosts > 0 ? ($postsCount / $maxPosts) * 100 : 0;
                @endphp
                <div class="flex items-center space-x-4">
                    <div class="w-24 text-sm text-gray-600 truncate">{{ $competitor->username }}</div>
                    <div class="flex-1 bg-gray-200 rounded-full h-4 relative">
                        <div class="bg-gradient-to-r from-green-500 to-teal-500 h-4 rounded-full transition-all duration-500" 
                             style="width: {{ $barWidth }}%"></div>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-medium text-white">
                            {{ $postsCount }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Individual Competitor Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        @foreach($competitors as $competitor)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg">
                    {{ strtoupper(substr($competitor->username, 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $competitor->username }}</h3>
                    <p class="text-xs text-gray-500 capitalize">{{ $competitor->platform }} • {{ $competitor->category }}</p>
                </div>
            </div>

            @php $latestSummary = $competitor->analysisSummary->first(); @endphp
            @if($latestSummary)
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Followers</span>
                    <span class="text-sm font-medium text-gray-900">{{ number_format($competitor->followers_count) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Posts</span>
                    <span class="text-sm font-medium text-gray-900">{{ $latestSummary->total_posts }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Avg Engagement</span>
                    <span class="text-sm font-medium text-gray-900">{{ number_format($latestSummary->avg_engagement_rate, 1) }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Avg Likes</span>
                    <span class="text-sm font-medium text-gray-900">{{ number_format($latestSummary->avg_likes) }}</span>
                </div>
                
                <!-- Content Gaps -->
                @if($competitor->contentGaps->isNotEmpty())
                <div class="pt-3 border-t border-gray-200">
                    <p class="text-xs text-gray-600 mb-2">Content Opportunities</p>
                    <div class="space-y-1">
                        @foreach($competitor->contentGaps->take(2) as $gap)
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                            <span class="text-xs text-gray-700">{{ $gap->gap_title }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="text-center py-4">
                <p class="text-sm text-gray-500">No analysis data available</p>
            </div>
            @endif

            <div class="mt-4 pt-4 border-t border-gray-200">
                <a href="{{ route('competitor-analysis.show', $competitor) }}" 
                   class="w-full px-4 py-2 bg-purple-600 text-white text-sm text-center rounded-lg hover:bg-purple-700 transition block">
                    View Details
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('competitor-analysis.index') }}" 
           class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
            ← Back to List
        </a>
        
        <div class="flex items-center space-x-3">
            <button onclick="printReport()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Print Report</span>
            </button>
            
            <button onclick="exportComparison()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Export Data</span>
            </button>
        </div>
    </div>
</div>

<script>
function exportComparison() {
    try {
        // Simple CSV export using clean data from controller
        const competitors = @json($exportData ?? []);
        
        if (!competitors || competitors.length === 0) {
            alert('No data available for export');
            return;
        }
        
        let csv = 'Username,Platform,Category,Followers,Posts,Engagement Rate,Avg Likes\n';
        competitors.forEach(competitor => {
            csv += `${competitor.username},${competitor.platform},${competitor.category},${competitor.followers},${competitor.posts},${competitor.engagement_rate}%,${competitor.avg_likes}\n`;
        });
        
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'competitor-comparison-' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        // Show success message
        showNotification('✅ Data exported successfully!', 'success');
    } catch (error) {
        console.error('Export failed:', error);
        alert('Export failed. Please try again.');
    }
}

function printReport() {
    try {
        // Add print-specific styles
        const printStyles = `
            <style>
                @media print {
                    .no-print { display: none !important; }
                    .print-only { display: block !important; }
                    body { font-size: 12px; }
                    .bg-gradient-to-r { background: #f8f9fa !important; }
                    .text-purple-600 { color: #6b46c1 !important; }
                    .border { border: 1px solid #e5e7eb !important; }
                }
            </style>
        `;
        
        // Add print styles to head
        const head = document.head || document.getElementsByTagName('head')[0];
        const style = document.createElement('style');
        style.innerHTML = printStyles;
        head.appendChild(style);
        
        // Add no-print class to action buttons
        const actionButtons = document.querySelector('.flex.items-center.justify-between');
        if (actionButtons) {
            actionButtons.classList.add('no-print');
        }
        
        // Print
        window.print();
        
        // Clean up
        setTimeout(() => {
            head.removeChild(style);
            if (actionButtons) {
                actionButtons.classList.remove('no-print');
            }
        }, 1000);
        
    } catch (error) {
        console.error('Print failed:', error);
        alert('Print failed. Please try again.');
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
        type === 'success' ? 'bg-green-600' : 
        type === 'error' ? 'bg-red-600' : 'bg-blue-600'
    }`;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Update print button to use custom function
document.addEventListener('DOMContentLoaded', function() {
    const printButton = document.querySelector('button[onclick="window.print()"]');
    if (printButton) {
        printButton.setAttribute('onclick', 'printReport()');
    }
});
</script>
@endsection