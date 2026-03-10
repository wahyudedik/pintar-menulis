@extends('layouts.public')

@section('head')
    <!-- SEO Meta Tags -->
    <meta name="description" content="Discover daily articles, tips, quotes, and inspiration for content creators and entrepreneurs.">
    <meta name="keywords" content="articles, tips, inspiration, content creation, digital marketing">
    <meta name="canonical" href="{{ route('articles.index') }}">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="Articles & Inspiration">
    <meta property="og:description" content="Discover daily articles, tips, quotes, and inspiration for content creators">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('articles.index') }}">
    <meta property="og:image" content="{{ asset('images/article-default.jpg') }}">
    
    <!-- JSON-LD Organization Schema -->
    <script type="application/ld+json">
        {!! json_encode(\App\Helpers\SeoHelper::generateOrganizationSchema()) !!}
    </script>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Articles & Inspiration</h1>
            <p class="text-lg text-gray-600">Discover daily articles, quotes, tips, and inspiration for content creators</p>
        </div>

        <!-- Ad Placement: Top -->
        @if($adTopCode = \App\Models\AdPlacement::getAdCode('article_list_top'))
            <div class="mb-8 p-4 bg-gray-100 rounded-lg border border-gray-300">
                <div class="text-xs text-gray-500 mb-2">Advertisement</div>
                {!! $adTopCode !!}
            </div>
        @endif

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @forelse($articles as $article)
                <article class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                    <div class="p-6">
                        <!-- Category Badge -->
                        <div class="mb-3">
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
                                @if($article->category === 'industry') bg-blue-100 text-blue-800
                                @elseif($article->category === 'tips') bg-green-100 text-green-800
                                @elseif($article->category === 'quote') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($article->category) }}
                            </span>
                            <span class="inline-block ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($article->industry) }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">
                            {{ $article->title }}
                        </h2>

                        <!-- Content Preview -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ Str::limit($article->content, 150) }}
                        </p>

                        <!-- Meta -->
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            <span>{{ $article->created_at->format('M d, Y') }}</span>
                        </div>

                        <!-- Read More Link -->
                        <a href="{{ route('articles.show', $article->slug) }}" class="inline-block text-blue-600 hover:text-blue-800 font-semibold text-sm">
                            Read More →
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No articles found yet. Check back soon!</p>
                </div>
            @endforelse
        </div>

        <!-- Ad Placement: Bottom -->
        @if($adBottomCode = \App\Models\AdPlacement::getAdCode('article_list_bottom'))
            <div class="mb-8 p-4 bg-gray-100 rounded-lg border border-gray-300">
                <div class="text-xs text-gray-500 mb-2">Advertisement</div>
                {!! $adBottomCode !!}
            </div>
        @endif

        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="flex justify-center">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
