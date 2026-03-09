@extends('layouts.admin')

@section('title', 'Banner Information Management')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Banner Information Management</h1>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-6">
            <div class="mb-6">
                <h3 class="text-base font-semibold mb-2">Manage Banner Popups</h3>
                <p class="text-sm text-gray-600">
                    Configure banner information popups for different sections. Banners will only show once per user on their first visit.
                    If title or content is empty, the banner will not be displayed.
                </p>
            </div>

            <div class="space-y-6">
                @foreach($banners as $banner)
                    <div class="border border-gray-200 rounded-lg p-6">
                        <form method="POST" action="{{ route('admin.banner-information.update', $banner) }}">
                            @csrf
                            @method('PUT')

                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-2xl
                                        @if($banner->type === 'landing') bg-blue-50
                                        @elseif($banner->type === 'client') bg-green-50
                                        @elseif($banner->type === 'operator') bg-purple-50
                                        @elseif($banner->type === 'guru') bg-orange-50
                                        @endif">
                                        @if($banner->type === 'landing')
                                            🏠
                                        @elseif($banner->type === 'client')
                                            👤
                                        @elseif($banner->type === 'operator')
                                            ⚙️
                                        @elseif($banner->type === 'guru')
                                            🎓
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900">
                                            @if($banner->type === 'landing')
                                                Landing Page Banner
                                            @elseif($banner->type === 'client')
                                                Client Dashboard Banner
                                            @elseif($banner->type === 'operator')
                                                Operator Dashboard Banner
                                            @elseif($banner->type === 'guru')
                                                Guru Dashboard Banner
                                            @endif
                                        </h4>
                                        <p class="text-xs text-gray-500">
                                            @if($banner->type === 'landing')
                                                Shown to visitors on the landing page
                                            @else
                                                Shown to {{ $banner->type }} users on their dashboard
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" 
                                        {{ $banner->is_active ? 'checked' : '' }}
                                        {{ (!$banner->title || !$banner->content) ? 'disabled' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-700">Active</span>
                                </label>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="title_{{ $banner->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                        Banner Title
                                    </label>
                                    <input type="text" 
                                        id="title_{{ $banner->id }}" 
                                        name="title" 
                                        value="{{ old('title', $banner->title) }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Enter banner title (leave empty to disable)">
                                    @error('title')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="content_{{ $banner->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                        Banner Content
                                    </label>
                                    <textarea 
                                        id="content_{{ $banner->id }}" 
                                        name="content" 
                                        rows="4"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Enter banner content (supports HTML)">{{ old('content', $banner->content) }}</textarea>
                                    @error('content')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        You can use HTML tags for formatting (e.g., &lt;strong&gt;, &lt;em&gt;, &lt;br&gt;, &lt;a&gt;)
                                    </p>
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                    <div class="text-xs text-gray-600">
                                        @if($banner->title && $banner->content)
                                            <span class="text-green-600">✓ Banner configured</span>
                                        @else
                                            <span class="text-gray-400">⚠ Banner disabled (title or content empty)</span>
                                        @endif
                                    </div>
                                    <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
