@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Google AdSense Placements</h1>
            <p class="text-gray-600 mt-2">Manage ad placements across article pages</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Ad Placements Grid -->
        <div class="space-y-6">
            @foreach($placements as $placement)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ str_replace('_', ' ', ucfirst($placement->location)) }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                @switch($placement->location)
                                    @case('article_list_top')
                                        Top of article list page
                                        @break
                                    @case('article_list_bottom')
                                        Bottom of article list page
                                        @break
                                    @case('article_detail_top')
                                        Top of article detail page
                                        @break
                                    @case('article_detail_middle')
                                        Middle of article detail page
                                        @break
                                    @case('article_detail_bottom')
                                        Bottom of article detail page
                                        @break
                                @endswitch
                            </p>
                        </div>
                        <form action="{{ route('admin.ad-placements.toggle', $placement) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 rounded-lg font-semibold transition-colors
                                @if($placement->is_active)
                                    bg-green-100 text-green-800 hover:bg-green-200
                                @else
                                    bg-gray-100 text-gray-800 hover:bg-gray-200
                                @endif">
                                {{ $placement->is_active ? '✓ Active' : '○ Inactive' }}
                            </button>
                        </form>
                    </div>

                    <!-- Ad Code Form -->
                    <form action="{{ route('admin.ad-placements.update', $placement) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Google AdSense Code
                            </label>
                            <textarea name="ad_code" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm" placeholder="Paste your Google AdSense code here...">{{ $placement->ad_code }}</textarea>
                            <p class="text-xs text-gray-500 mt-2">
                                Paste your complete Google AdSense ad code (including &lt;script&gt; tags)
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                Save Changes
                            </button>
                            @if($placement->ad_code)
                                <button type="button" onclick="previewAd('{{ $placement->location }}')" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                                    Preview
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            @endforeach
        </div>

        <!-- Info Box -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h4 class="font-semibold text-blue-900 mb-2">How to add Google AdSense</h4>
            <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside">
                <li>Go to your Google AdSense account</li>
                <li>Create a new ad unit</li>
                <li>Copy the complete ad code</li>
                <li>Paste it in the appropriate placement above</li>
                <li>Toggle the placement to Active</li>
                <li>Save changes</li>
            </ol>
        </div>
    </div>
</div>

<script>
function previewAd(location) {
    alert('Preview functionality coming soon. Ad will display at: ' + location);
}
</script>
@endsection
