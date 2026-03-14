@extends('layouts.client')

@section('title', $project->business_name)

@section('content')
<div class="p-6" x-data="projectWorkspace()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <h1 class="text-2xl font-semibold text-gray-900">{{ $project->business_name }}</h1>
                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ ucfirst($userRole) }}</span>
            </div>
            <p class="text-sm text-gray-500">{{ $project->business_type }} • {{ $stats['team_members'] }} anggota tim</p>
        </div>
        <div class="flex items-center space-x-3">
            @if($canEdit)
            <a href="{{ route('projects.content.create', $project) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Content
            </a>
            @endif
            <a href="{{ route('projects.collaboration.workspace', $project) }}" 
               class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.196-2.121M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Team Workspace
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Content</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['total_content'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Drafts</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['drafts'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Pending Review</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['pending_review'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Approved</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['approved'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.196-2.121M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Team Members</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['team_members'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Content</h3>
                        <a href="{{ route('projects.content.index', $project) }}" 
                           class="text-sm text-blue-600 hover:text-blue-700">View All</a>
                    </div>
                </div>
                <div class="p-6">
                    @if($recentContent->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 mb-4">Belum ada content yang dibuat</p>
                        @if($canEdit)
                        <a href="{{ route('projects.content.create', $project) }}" 
                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                            Buat content pertama
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        @endif
                    </div>
                    @else
                    <div class="space-y-4">
                        @foreach($recentContent as $content)
                        <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $content->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : ($content->status === 'review' ? 'bg-orange-100 text-orange-800' : ($content->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($content->status) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('projects.content.show', [$project, $content]) }}" 
                                       class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                        {{ $content->title }}
                                    </a>
                                    <span class="text-xs text-gray-500">{{ $content->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    By {{ $content->creator->name }} • {{ ucfirst($content->content_type) }}
                                    @if($content->platform)
                                    • {{ ucfirst($content->platform) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Team Members & Quick Actions -->
        <div class="space-y-6">
            <!-- Team Members -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Team Members</h3>
                        <a href="{{ route('projects.collaboration.index', $project) }}" 
                           class="text-sm text-blue-600 hover:text-blue-700">Manage</a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <!-- Project Owner -->
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600">{{ substr($project->user->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $project->user->name }}</p>
                                <p class="text-xs text-gray-500">Owner</p>
                            </div>
                        </div>

                        <!-- Team Members -->
                        @foreach($teamMembers as $member)
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-600">{{ substr($member->user->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $member->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($member->role) }}</p>
                            </div>
                        </div>
                        @endforeach

                        @if($teamMembers->isEmpty())
                        <p class="text-sm text-gray-500 text-center py-4">Belum ada anggota tim</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($canEdit)
                    <a href="{{ route('projects.content.create', $project) }}" 
                       class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create New Content
                    </a>
                    @endif

                    <a href="{{ route('projects.content.index', $project) }}" 
                       class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View All Content
                    </a>

                    @if($canApprove && $stats['pending_review'] > 0)
                    <a href="{{ route('projects.content.index', $project) }}?status=review" 
                       class="flex items-center p-3 text-sm text-orange-700 bg-orange-50 hover:bg-orange-100 rounded-lg transition">
                        <svg class="w-5 h-5 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Review Pending Content ({{ $stats['pending_review'] }})
                    </a>
                    @endif

                    <a href="{{ route('projects.collaboration.index', $project) }}" 
                       class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.196-2.121M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Manage Team
                    </a>

                    @if($project->isOwner(auth()->user()))
                    <a href="{{ route('projects.edit', $project) }}" 
                       class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Project Settings
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function projectWorkspace() {
    return {
        // Add any JavaScript functionality here
    }
}
</script>
@endsection