@extends('layouts.app')

@section('title', 'Search Results: ' . $query . ' - ' . \App\Models\SiteSetting::get('blog_title', 'Blog'))

@section('description', 'Search results for "' . $query . '" in our blog. Find health and wellness articles, tips, and advice.')

@section('keywords', $query . ', blog search, health articles, wellness tips')

@section('robots', 'noindex, follow')
@section('canonical', route('blog.search', ['q' => $query]))

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
                <!-- Breadcrumb Header -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="px-8 py-4">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors">{{ \App\Models\SiteSetting::get('blog_title', 'Blog') }}</a>
                            <span>/</span>
                            <a href="{{ route('blog.index') }}" class="hover:text-indigo-600 transition-colors">Blog</a>
                            <span>/</span>
                            <span class="text-gray-900 font-medium">Search Results</span>
                        </div>
                    </div>
                </div>

                <!-- Search, Filter & View Mode Bar -->
                <div class="mb-6" x-data="{ viewMode: 'grid' }">
                    <div class="bg-white rounded-lg shadow-sm p-6 flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
                        <!-- Search Form -->
                        <form action="{{ route('blog.search') }}" method="GET" class="flex-1 w-full lg:max-w-md">
                            <div class="relative flex gap-2">
                                <div class="relative flex-1">
                                    <input type="text" 
                                           name="q" 
                                           value="{{ $query }}"
                                           placeholder="Search posts..." 
                                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg font-medium transition-colors whitespace-nowrap">
                                    Search
                                </button>
                            </div>
                        </form>

                        <div class="flex items-center gap-3 w-full lg:w-auto">
                            <!-- Sort Filter -->
                            <select name="sort" 
                                    onchange="window.location.href='{{ route('blog.search') }}?q={{ $query }}&sort=' + this.value + '{{ request('per_page') ? '&per_page=' . request('per_page') : '' }}'"
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title (A-Z)</option>
                            </select>

                            <!-- Per Page Filter -->
                            <select name="per_page" 
                                    onchange="window.location.href='{{ route('blog.search') }}?q={{ $query }}&per_page=' + this.value + '{{ request('sort') ? '&sort=' . request('sort') : '' }}'"
                                    class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per page</option>
                                <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20 per page</option>
                                <option value="30" {{ request('per_page', 10) == 30 ? 'selected' : '' }}>30 per page</option>
                                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50 per page</option>
                            </select>

                            <!-- View Mode Toggle -->
                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                <button type="button" 
                                        @click="viewMode = 'list'"
                                        :class="viewMode === 'list' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                        class="px-3 py-2.5 transition-colors"
                                        title="List View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                </button>
                                <button type="button" 
                                        @click="viewMode = 'grid'"
                                        :class="viewMode === 'grid' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
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
                    @if($query || request('sort'))
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="text-sm text-gray-600">Active filters:</span>
                        
                        @if($query)
                        <span class="inline-flex items-center gap-1 bg-indigo-100 text-indigo-800 text-sm px-3 py-1 rounded-full">
                            Search: "{{ $query }}"
                            <a href="{{ route('blog.index') }}" 
                               class="hover:text-indigo-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </span>
                        @endif
                        
                        @if(request('sort'))
                        <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                            Sort: {{ ucfirst(request('sort')) }}
                            <a href="{{ route('blog.search') }}?q={{ $query }}" 
                               class="hover:text-blue-900">
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
                                    <img src="https://img.youtube.com/vi/{{ $post->youtube_video_id }}/maxresdefault.jpg" 
                                         alt="{{ $post->title }}"
                                         class="w-full h-48 md:h-full object-cover"
                                         onerror="this.src='https://img.youtube.com/vi/{{ $post->youtube_video_id }}/hqdefault.jpg'">
                                    
                                    <!-- Video Play Badge -->
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                        <div class="bg-red-600 rounded-full p-3 shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- YouTube Badge -->
                                    <div class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 rounded text-xs font-semibold flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        VIDEO
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
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    @if($post->categories && $post->categories->count() > 0)
                                        @foreach($post->categories as $category)
                                        <a href="{{ route('blog.category', $category->slug) }}" 
                                           class="inline-block bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full hover:bg-gray-200">
                                            {{ $category->name }}
                                        </a>
                                        @endforeach
                                    @endif
                                    @if($post->is_featured)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full">
                                        Featured
                                    </span>
                                    @endif
                                </div>
                                
                                <h3 class="text-2xl font-bold text-gray-900 mb-2 hover:text-blue-600">
                                    <a href="{{ route('products.show', $post->slug) }}">{{ $post->title }}</a>
                                </h3>
                                
                                @if($post->excerpt)
                                <p class="text-gray-600 mb-4">{{ Str::limit($post->excerpt, 150) }}</p>
                                @endif
                                
                                <!-- Mobile-Optimized Metadata & Actions -->
                                <div class="space-y-3">
                                        <!-- Author & Date Info -->
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-sm text-gray-500">
                                            @if(\App\Models\SiteSetting::get('blog_show_author', '1') === '1')
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
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
                                           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors">
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
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">No results found</h3>
                        <p class="mt-2 text-gray-500">We couldn't find any posts matching "{{ $query }}"</p>
                        <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('blog.index') }}" 
                               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                                Browse All Posts
                            </a>
                            <button onclick="document.querySelector('input[name=q]').focus()" 
                                    class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium">
                                Try Another Search
                            </button>
                        </div>
                    </div>
                    @endforelse
                    </div>

                    <!-- Grid View with Masonry Layout -->
                    <div x-show="viewMode === 'grid'" class="masonry-grid">
                        @forelse($posts as $post)
                            <x-blog.post-card :post="$post" />
                        @empty
                            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="mt-4 text-xl font-semibold text-gray-900">No results found</h3>
                                <p class="mt-2 text-gray-500">We couldn't find any posts matching "{{ $query }}"</p>
                                <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                                    <a href="{{ route('blog.index') }}" 
                                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                                        Browse All Posts
                                    </a>
                                    <button onclick="document.querySelector('input[name=q]').focus()" 
                                            class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium">
                                        Try Another Search
                                    </button>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                <div class="mt-8">
                    {{ $posts->appends(['q' => $query])->links() }}
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
