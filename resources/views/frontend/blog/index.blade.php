@extends('layouts.app')

@section('title', $seoData['title'] ?? \App\Models\SiteSetting::get('blog_title', 'Blog'))

@section('description', $seoData['description'] ?? \App\Models\SiteSetting::get('blog_description', 'Discover the latest health and wellness tips'))

@section('keywords', $seoData['keywords'] ?? \App\Models\SiteSetting::get('blog_keywords', 'blog, articles, tips'))

@section('og_type', $seoData['og_type'] ?? 'website')
@section('og_title', $seoData['title'] ?? \App\Models\SiteSetting::get('blog_title', 'Blog'))
@section('og_description', $seoData['description'] ?? \App\Models\SiteSetting::get('blog_description', 'Discover the latest health and wellness tips'))
@section('og_image', $seoData['og_image'] ?? asset('images/og-default.jpg'))
@section('canonical', $seoData['canonical'] ?? route('blog.index'))

@section('twitter_card', 'summary_large_image')
@section('twitter_title', $seoData['title'] ?? \App\Models\SiteSetting::get('blog_title', 'Blog'))
@section('twitter_description', $seoData['description'] ?? \App\Models\SiteSetting::get('blog_description', 'Discover the latest health and wellness tips'))
@section('twitter_image', $seoData['og_image'] ?? asset('images/og-default.jpg'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Sidebar - Collapsible -->
            <x-blog.sidebar 
                title="{{ \App\Models\SiteSetting::get('blog_title', 'Wellness Hub') }}"
                subtitle="{{ \App\Models\SiteSetting::get('blog_tagline', 'Health & Lifestyle Blog') }}"
                :categories="$categories"
                categoryType="blog"
            />

            <!-- Main Content -->
            <div class="lg:col-span-9">
                <!-- Search, Filter & View Mode Bar -->
                <div class="  mb-6 " x-data="{ viewMode: 'grid' }">
                    <div class="bg-white p-6 rounded-lg shadow-sm flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
                        <!-- Search Form -->
                        <form action="{{ route('blog.search') }}" method="GET" class="flex-1 w-full lg:max-w-md">
                            <div class="relative flex gap-2">
                                <div class="relative flex-1">
                                    <input type="text" 
                                           name="q" 
                                           value="{{ request('q') }}"
                                           placeholder="Search all posts..." 
                                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg font-medium transition-colors whitespace-nowrap">
                                    Search
                                </button>
                            </div>
                        </form>

                        <div class="flex items-center gap-3 w-full lg:w-auto">
                            <!-- Sort Filter -->
                            <select name="sort" 
                                    onchange="window.location.href='{{ route('blog.index') }}?sort=' + this.value + '{{ request('q') ? '&q=' . request('q') : '' }}{{ request('filter') ? '&filter=' . request('filter') : '' }}'"
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title (A-Z)</option>
                            </select>

                            <!-- Per Page Filter -->
                            <select name="per_page" 
                                    onchange="window.location.href='{{ route('blog.index') }}?per_page=' + this.value + '{{ request('q') ? '&q=' . request('q') : '' }}{{ request('sort') ? '&sort=' . request('sort') : '' }}{{ request('filter') ? '&filter=' . request('filter') : '' }}'"
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per page</option>
                                <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20 per page</option>
                                <option value="30" {{ request('per_page', 10) == 30 ? 'selected' : '' }}>30 per page</option>
                                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50 per page</option>
                            </select>

                            <!-- Post Counter -->
                            <div class="px-4 py-2.5 bg-green-50 border border-green-200 rounded-lg text-sm font-medium text-green-700 whitespace-nowrap">
                                {{ $posts->total() }} {{ Str::plural('Post', $posts->total()) }}
                            </div>

                            <!-- View Mode Toggle -->
                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                <button type="button" 
                                        @click="viewMode = 'list'"
                                        :class="viewMode === 'list' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                        class="px-3 py-2.5 transition-colors"
                                        title="List View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                </button>
                                <button type="button" 
                                        @click="viewMode = 'grid'"
                                        :class="viewMode === 'grid' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                        class="px-3 py-2.5 border-l border-gray-300 transition-colors"
                                        title="Grid View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filters Display -->
                    @if(request('q') || request('sort') || request('filter'))
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="text-sm text-gray-600">Active filters:</span>
                        @if(request('q'))
                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                            Search: "{{ request('q') }}"
                            <a href="{{ route('blog.index') }}?{{ http_build_query(array_filter(request()->except('q'))) }}" 
                               class="hover:text-green-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </span>
                        @endif
                        @if(request('sort'))
                        <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                            Sort: {{ ucfirst(request('sort')) }}
                            <a href="{{ route('blog.index') }}?{{ http_build_query(array_filter(request()->except('sort'))) }}" 
                               class="hover:text-blue-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </span>
                        @endif
                        @if(request('filter'))
                        <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                            Filter: 
                            @if(request('filter') === 'popular')
                                Most Popular
                            @elseif(request('filter') === 'articles')
                                Articles Only
                            @elseif(request('filter') === 'videos')
                                Videos Only
                            @endif
                            <a href="{{ route('blog.index') }}?{{ http_build_query(array_filter(request()->except('filter'))) }}" 
                               class="hover:text-purple-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </span>
                        @endif
                        <a href="{{ route('blog.index') }}" class="text-sm text-red-600 hover:text-red-800 font-medium">
                            Clear all
                        </a>
                    </div>
                    @endif

                <!-- Posts List/Grid -->
                <div class="pt-6">
                    <!-- List View -->
                    <div x-show="viewMode === 'list'" class="space-y-6">
                        @forelse($posts as $post)
                        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                            <div class="md:flex">
                                @if($post->youtube_url || $post->media || $post->featured_image)
                                <div class="md:w-1/3 relative">
                                    @if($post->youtube_url)
                                        <!-- YouTube Video Thumbnail -->
                                        <div class="aspect-video overflow-hidden relative">
                                            <img src="https://img.youtube.com/vi/{{ $post->youtube_video_id }}/maxresdefault.jpg" 
                                                 alt="{{ $post->title }}"
                                                 class="w-full h-full object-cover"
                                                 onerror="this.src='https://img.youtube.com/vi/{{ $post->youtube_video_id }}/hqdefault.jpg'">
                                            
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                                <div class="bg-red-600 rounded-full p-3">
                                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            
                                            <div class="absolute top-3 right-3 bg-red-600 text-white px-2 py-1 rounded text-xs font-semibold flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                </svg>
                                                VIDEO
                                            </div>
                                        </div>
                                    @elseif($post->media)
                                        <img src="{{ $post->media->medium_url }}" 
                                             alt="{{ $post->featured_image_alt ?? $post->title }}" 
                                             class="w-full h-48 md:h-full object-cover">
                                    @elseif($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                             alt="{{ $post->featured_image_alt }}" 
                                             class="w-full h-48 md:h-full object-cover">
                                    @endif
                                </div>
                                @endif
                                <div class="p-6 {{ $post->youtube_url || $post->media || $post->featured_image ? 'md:w-2/3' : 'w-full' }}">
                                    @if($post->categories && $post->categories->count() > 0)
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        @foreach($post->categories as $category)
                                        <a href="{{ route('blog.category', $category->slug) }}" 
                                           class="inline-block bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full hover:bg-gray-200">
                                            {{ $category->name }}
                                        </a>
                                        @endforeach
                                    </div>
                                    @endif
                                    
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2 hover:text-blue-600">
                                        <a href="{{ route('products.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h3>
                                    
                                    <p class="text-gray-600 mb-4">{{ Str::limit($post->excerpt, 150) }}</p>
                                    
                                    <!-- Tick Marks -->
                                    @if($post->hasTickMarks())
                                    <div class="mb-3">
                                        <x-blog.tick-marks :post="$post" />
                                    </div>
                                    @endif
                                    
                                    <!-- Mobile-Optimized Metadata & Actions -->
                                    <div class="space-y-3">
                                        <!-- Author & Date Info -->
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-sm text-gray-500">
                                            @if(\App\Models\SiteSetting::get('blog_show_author', '1') === '1')
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                                    {{ substr($post->author->name, 0, 1) }}
                                                </div>
                                                <span class="font-medium">{{ $post->author->name }}</span>
                                            </div>
                                            @endif
                                            <div class="flex items-center gap-4 text-xs sm:text-sm">
                                                @if(\App\Models\SiteSetting::get('blog_show_date', '1') === '1')
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $post->published_at->format('M d, Y') }}
                                                </span>
                                                @endif
                                                @if(\App\Models\SiteSetting::get('blog_show_reading_time', '1') === '1')
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $post->reading_time_text }}
                                                </span>
                                                @endif
                                                @if(\App\Models\SiteSetting::get('blog_show_views', '1') === '1' && $post->views_count > 0)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    {{ number_format($post->views_count) }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Read More Button -->
                                        <div class="pt-2">
                                            <a href="{{ route('products.show', $post->slug) }}" 
                                               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                                                <span>Read More</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                        @empty
                        <div class="bg-white rounded-lg shadow-md p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-4 text-gray-500 text-lg">No posts found</p>
                        </div>
                        @endforelse
                    </div>

                    <!-- Grid View with Masonry Layout -->
                    <div x-show="viewMode === 'grid'" class="masonry-grid">
                        @forelse($posts as $post)
                            <x-blog.post-card :post="$post" />
                        @empty
                        <div class="bg-white rounded-lg shadow-md p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-4 text-gray-500 text-lg">No posts found</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Masonry Layout using CSS Columns */
    .masonry-grid {
        column-count: 1;
        column-gap: 1.5rem;
    }
    
    @media (min-width: 768px) {
        .masonry-grid {
            column-count: 2;
        }
    }
    
    @media (min-width: 1024px) {
        .masonry-grid {
            column-count: 3;
        }
    }
    
    /* Prevent items from breaking across columns */
    .masonry-grid > * {
        break-inside: avoid;
        margin-bottom: 1.5rem;
        display: inline-block;
        width: 100%;
    }
</style>
@endpush

@endsection
