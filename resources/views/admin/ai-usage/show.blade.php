@extends('layouts.admin')

@section('title', 'AI Usage - ' . $user->name)

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.ai-usage.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">AI Usage Analytics</h1>
        </div>
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-lg font-semibold text-blue-600">{{ substr($user->name, 0, 1) }}</span>
            </div>
            <div>
                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Total Generations</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_generations']) }}</p>
            <p class="text-xs text-gray-500 mt-2">
                Unique: {{ $stats['unique_captions'] }} | Repeated: {{ $stats['repeated_captions'] }}
            </p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-600">AI Temperature</p>
            <p class="text-2xl font-bold mt-1
                {{ $stats['ai_temperature'] == 0.9 ? 'text-red-600' : 
                   ($stats['ai_temperature'] == 0.8 ? 'text-orange-600' : 'text-green-600') }}">
                {{ $stats['ai_temperature'] }}
            </p>
            <p class="text-xs text-gray-500 mt-2">{{ $stats['temperature_level'] }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Recent Activity</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['generations_7d'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Last 7 days</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Avg per Day</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['avg_per_day'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Since {{ $stats['days_since_first'] }} days ago</p>
        </div>
    </div>

    <!-- Timeline & Usage Info -->
    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Generation Timeline (Last 30 Days)</h3>
            @if($timeline->count() > 0)
            <canvas id="timelineChart"></canvas>
            @else
            <p class="text-gray-500 text-center py-8 text-sm">No data in last 30 days</p>
            @endif
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Usage Info</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-600">First Generation</p>
                    <p class="text-sm font-medium text-gray-900">
                        {{ $stats['first_generation'] ? \Carbon\Carbon::parse($stats['first_generation'])->format('d M Y') : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Last Generation</p>
                    <p class="text-sm font-medium text-gray-900">
                        {{ $stats['last_generation'] ? \Carbon\Carbon::parse($stats['last_generation'])->format('d M Y, H:i') : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Last 30 Days</p>
                    <p class="text-sm font-medium text-gray-900">{{ $stats['generations_30d'] }} generations</p>
                </div>
                <div class="pt-3 border-t border-gray-200">
                    <p class="text-xs text-gray-600 mb-2">Analytics Integration</p>
                    <p class="text-sm text-gray-900">
                        Tracked: <span class="font-semibold">{{ $analyticsStats['tracked_captions'] }}</span>
                    </p>
                    <p class="text-sm text-gray-900">
                        Avg Engagement: <span class="font-semibold">{{ number_format($analyticsStats['avg_engagement'], 1) }}%</span>
                    </p>
                    <p class="text-sm text-gray-900">
                        Successful: <span class="font-semibold">{{ $analyticsStats['successful_captions'] }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Distribution -->
    <div class="grid md:grid-cols-3 gap-6 mb-6">
        <!-- Platform Usage -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Platform Usage</h3>
            @if($platformUsage->count() > 0)
            <div class="space-y-2">
                @foreach($platformUsage as $platform)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">{{ ucfirst($platform->platform) }}</span>
                    <span class="text-sm font-semibold text-blue-600">{{ $platform->count }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" 
                         style="width: {{ ($platform->count / $stats['total_generations']) * 100 }}%"></div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm text-center py-4">No platform data</p>
            @endif
        </div>

        <!-- Category Usage -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Category Usage</h3>
            @if($categoryUsage->count() > 0)
            <div class="space-y-2">
                @foreach($categoryUsage->take(5) as $category)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $category->category)) }}</span>
                    <span class="text-sm font-semibold text-green-600">{{ $category->count }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" 
                         style="width: {{ ($category->count / $stats['total_generations']) * 100 }}%"></div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm text-center py-4">No category data</p>
            @endif
        </div>

        <!-- Tone Usage -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Tone Usage</h3>
            @if($toneUsage->count() > 0)
            <div class="space-y-2">
                @foreach($toneUsage as $tone)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">{{ ucfirst($tone->tone) }}</span>
                    <span class="text-sm font-semibold text-purple-600">{{ $tone->count }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" 
                         style="width: {{ ($tone->count / $stats['total_generations']) * 100 }}%"></div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm text-center py-4">No tone data</p>
            @endif
        </div>
    </div>

    <!-- Recent Generations -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Generations</h3>
        </div>
        <div class="overflow-x-auto">
            @if($recentGenerations->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Caption</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Platform</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Times Generated</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Last Generated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentGenerations as $gen)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900 max-w-md">
                            <p class="line-clamp-2">{{ $gen->caption_text }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs">
                                {{ $gen->category ? ucfirst(str_replace('_', ' ', $gen->category)) : 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 bg-green-50 text-green-700 rounded text-xs">
                                {{ $gen->platform ? ucfirst($gen->platform) : 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($gen->times_generated > 1)
                                <span class="px-2 py-1 bg-yellow-50 text-yellow-700 rounded text-xs font-semibold">
                                    {{ $gen->times_generated }}x
                                </span>
                            @else
                                <span class="text-gray-500">1x</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $gen->last_generated_at->format('d M Y, H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center">
                <p class="text-gray-500">No recent generations</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($timeline->count() > 0)
    const timelineCtx = document.getElementById('timelineChart').getContext('2d');
    new Chart(timelineCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($timeline->pluck('date')) !!},
            datasets: [{
                label: 'Generations',
                data: {!! json_encode($timeline->pluck('count')) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    @endif
</script>
@endsection
