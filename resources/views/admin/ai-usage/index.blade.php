@extends('layouts.admin')

@section('title', 'AI Usage Analytics')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">AI Usage Analytics</h1>
        <p class="text-sm text-gray-500 mt-1">Monitor AI generator usage across all users</p>
    </div>

    <!-- Overall Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Generations</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_generations']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                Today: {{ number_format($stats['generations_today']) }} | 
                This Week: {{ number_format($stats['generations_this_week']) }}
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_users']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                Active (30d): {{ number_format($stats['active_users_30d']) }}
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Avg per User</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['avg_per_user'], 1) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                Generations per user
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['generations_this_month']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                Monthly generations
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <!-- Daily Generations Chart -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Daily Generations (Last 30 Days)</h3>
            @if($dailyGenerations->count() > 0)
            <canvas id="dailyChart"></canvas>
            @else
            <p class="text-gray-500 text-center py-8 text-sm">No data available</p>
            @endif
        </div>

        <!-- Platform Distribution -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Platform Distribution</h3>
            @if($platformStats->count() > 0)
            <canvas id="platformChart"></canvas>
            @else
            <p class="text-gray-500 text-center py-8 text-sm">No data available</p>
            @endif
        </div>
    </div>

    <!-- Top Users -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top Users by Generation Count</h3>
        </div>
        <div class="overflow-x-auto">
            @if($topUsers->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Rank</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Total Generations</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Last Generated</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($topUsers as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                {{ $index == 0 ? 'bg-yellow-100 text-yellow-800' : 
                                   ($index == 1 ? 'bg-gray-100 text-gray-800' : 
                                   ($index == 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-50 text-blue-800')) }} 
                                font-semibold">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div>
                                <p class="font-medium text-gray-900">{{ $item['user']->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item['user']->email }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full font-semibold">
                                {{ number_format($item['total_generations']) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $item['last_generated'] ? \Carbon\Carbon::parse($item['last_generated'])->format('d M Y, H:i') : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('admin.ai-usage.show', $item['user']->id) }}" 
                               class="text-blue-600 hover:text-blue-700 font-medium">
                                View Details →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center">
                <p class="text-gray-500">No users have generated captions yet</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Category Distribution</h3>
        </div>
        <div class="p-4">
            @if($categoryStats->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($categoryStats as $cat)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-900">
                        {{ ucfirst(str_replace('_', ' ', $cat->category)) }}
                    </span>
                    <span class="text-sm font-semibold text-blue-600">
                        {{ number_format($cat->count) }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4 text-sm">No category data available</p>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($dailyGenerations->count() > 0)
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyGenerations->pluck('date')) !!},
            datasets: [{
                label: 'Generations',
                data: {!! json_encode($dailyGenerations->pluck('count')) !!},
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

    @if($platformStats->count() > 0)
    const platformCtx = document.getElementById('platformChart').getContext('2d');
    new Chart(platformCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($platformStats->pluck('platform')->map(function($p) { return ucfirst($p); })) !!},
            datasets: [{
                data: {!! json_encode($platformStats->pluck('count')) !!},
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
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
</script>
@endsection
