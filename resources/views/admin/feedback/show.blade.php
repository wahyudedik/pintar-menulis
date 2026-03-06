@extends('layouts.admin')

@section('title', 'Feedback Detail')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.feedback') }}" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
                    ← Back to Feedback List
                </a>
                <h1 class="text-2xl font-semibold text-gray-900">Feedback Detail</h1>
            </div>
            <form action="{{ route('admin.feedback.destroy', $feedback) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus feedback ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                    Delete
                </button>
            </form>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Feedback Info -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <span class="px-3 py-1 bg-{{ $feedback->getTypeBadgeColor() }}-100 text-{{ $feedback->getTypeBadgeColor() }}-700 text-sm rounded font-medium">
                                {{ $feedback->getTypeLabel() }}
                            </span>
                            <span class="px-3 py-1 bg-{{ $feedback->getPriorityBadgeColor() }}-100 text-{{ $feedback->getPriorityBadgeColor() }}-700 text-sm rounded font-medium">
                                {{ ucfirst($feedback->priority) }}
                            </span>
                        </div>
                        <span class="px-3 py-1 bg-{{ $feedback->getStatusBadgeColor() }}-100 text-{{ $feedback->getStatusBadgeColor() }}-700 text-sm rounded font-medium">
                            {{ $feedback->getStatusLabel() }}
                        </span>
                    </div>

                    <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $feedback->title }}</h2>

                    <div class="prose max-w-none">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $feedback->description }}</p>
                    </div>

                    @if($feedback->screenshot)
                    <div class="mt-6">
                        <p class="text-sm font-medium text-gray-700 mb-2">Screenshot:</p>
                        <img src="{{ $feedback->screenshot }}" alt="Screenshot" class="max-w-full h-auto rounded-lg border border-gray-200">
                    </div>
                    @endif

                    <div class="mt-6 pt-6 border-t border-gray-200 grid grid-cols-2 gap-4 text-sm">
                        @if($feedback->page_url)
                        <div>
                            <p class="text-gray-500 mb-1">Page URL:</p>
                            <a href="{{ $feedback->page_url }}" target="_blank" class="text-blue-600 hover:underline break-all">
                                {{ $feedback->page_url }}
                            </a>
                        </div>
                        @endif

                        @if($feedback->browser)
                        <div>
                            <p class="text-gray-500 mb-1">Browser:</p>
                            <p class="text-gray-900 text-xs">{{ Str::limit($feedback->browser, 60) }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Admin Response Section -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Response</h3>

                    @if($feedback->admin_response)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $feedback->admin_response }}</p>
                        <p class="text-xs text-gray-500 mt-2">
                            Responded at: {{ $feedback->responded_at->format('d M Y H:i') }}
                        </p>
                    </div>
                    @endif

                    <form action="{{ route('admin.feedback.update', $feedback) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Response</label>
                            <textarea name="admin_response" rows="4" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                      placeholder="Tulis response untuk user...">{{ old('admin_response', $feedback->admin_response) }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                    <option value="open" {{ $feedback->status === 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $feedback->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ $feedback->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $feedback->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                                <select name="priority" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                    <option value="low" {{ $feedback->priority === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ $feedback->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ $feedback->priority === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="critical" {{ $feedback->priority === 'critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                            Update Feedback
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- User Info -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Submitted By</h3>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr($feedback->user->name, 0, 2) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $feedback->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $feedback->user->email }}</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-200 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Role:</span>
                            <span class="font-medium text-gray-900">{{ ucfirst($feedback->user->role) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Submitted:</span>
                            <span class="font-medium text-gray-900">{{ $feedback->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Created</p>
                                <p class="text-xs text-gray-500">{{ $feedback->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>

                        @if($feedback->responded_at)
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Responded</p>
                                <p class="text-xs text-gray-500">{{ $feedback->responded_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-gray-300 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Last Updated</p>
                                <p class="text-xs text-gray-500">{{ $feedback->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
