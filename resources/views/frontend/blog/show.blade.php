@extends('layouts.app')

@section('title', $seoData['title'] ?? $post->title)

@section('description', $seoData['description'] ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 160))

@section('keywords', $seoData['keywords'] ?? 'blog, article')

@section('og_type', $seoData['og_type'] ?? 'article')
@section('og_title', $seoData['title'] ?? $post->title)
@section('og_description', $seoData['description'] ?? $post->excerpt)
@section('og_image', $seoData['og_image'] ?? asset('images/og-default.jpg'))
@section('canonical', $seoData['canonical'] ?? url($post->slug))

@section('twitter_card', 'summary_large_image')
@section('twitter_title', $seoData['title'] ?? $post->title)
@section('twitter_description', $seoData['description'] ?? $post->excerpt)
@section('twitter_image', $seoData['og_image'] ?? asset('images/og-default.jpg'))

@if(isset($seoData['author_name']))
@section('author', $seoData['author_name'])
@endif

@push('meta_tags')
    <!-- Article Specific Meta -->
    <meta property="article:published_time" content="{{ $seoData['published_at']->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $seoData['updated_at']->toIso8601String() }}">
    @if($post->author)
    <meta property="article:author" content="{{ $post->author->name }}">
    @endif
    @if($post->categories && $post->categories->count() > 0)
        @foreach($post->categories as $category)
    <meta property="article:section" content="{{ $category->name }}">
        @endforeach
    @endif
    @foreach($post->tags as $tag)
    <meta property="article:tag" content="{{ $tag->name }}">
    @endforeach
@endpush

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Sidebar - Collapsible -->
            <x-blog.sidebar 
                title="{{ \App\Models\SiteSetting::get('blog_title', 'Wellness Hub') }}"
                subtitle="{{ \App\Models\SiteSetting::get('blog_tagline', 'Health & Lifestyle Blog') }}"
                :categories="$categories"
                :currentCategory="$post->categories->first()"
                categoryType="blog"
            />

            <!-- Main Content -->
            <article class="lg:col-span-9">
                <div class="bg-white rounded-lg shadow-sm">
                    <!-- Breadcrumb -->
                    <div class="px-8 pt-6 pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <a href="{{ route('home') }}" class="hover:text-green-600 transition-colors">{{ \App\Models\SiteSetting::get('site_name', 'Wellness Hub') }}</a>
                            <span>/</span>
                            @if($post->categories && $post->categories->count() > 0)
                                @foreach($post->categories as $index => $category)
                                    <a href="{{ route('blog.category', $category->slug) }}" class="hover:text-green-600 transition-colors">
                                        {{ $category->name }}
                                    </a>
                                    @if(!$loop->last)
                                        <span>/</span>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Header -->
                    <div class="px-8 pt-6 pb-8">
                        <!-- Title -->
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">{{ $post->title }}</h1>

                        <!-- Meta Info -->
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-6">
                            @if(\App\Models\SiteSetting::get('blog_show_views', '1') === '1')
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>{{ number_format($post->views_count) }} views</span>
                            </div>
                            @endif
                            <!-- Tick Marks -->
                            <x-blog.tick-marks :post="$post" />
                        </div>

                        <!-- Author Info -->
                        @if(\App\Models\SiteSetting::get('blog_show_author', '1') === '1')
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if($post->author->authorProfile?->media)
                                    <img src="{{ $post->author->authorProfile->media->small_url }}" 
                                         alt="{{ $post->author->name }}"
                                         class="w-12 h-12 rounded-full object-cover">
                                @elseif($post->author->authorProfile?->avatar)
                                    <img src="{{ asset('storage/' . $post->author->authorProfile->avatar) }}" 
                                         alt="{{ $post->author->name }}"
                                         class="w-12 h-12 rounded-full object-cover">
                                @elseif($post->author->media)
                                    <img src="{{ $post->author->media->small_url }}" 
                                         alt="{{ $post->author->name }}"
                                         class="w-12 h-12 rounded-full object-cover">
                                @elseif($post->author->avatar)
                                    <img src="{{ asset('storage/' . $post->author->avatar) }}" 
                                         alt="{{ $post->author->name }}"
                                         class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center text-white text-lg font-bold">
                                        {{ substr($post->author->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('blog.author', $post->author->authorProfile->slug) }}" class="text-blue-600 hover:text-blue-800 font-semibold">{{ $post->author->name }}</a>
                                    @if(\App\Models\SiteSetting::get('blog_show_date', '1') === '1')
                                    <p class="text-sm text-gray-500">Posted on {{ $post->published_at->format('F j, Y') }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Shop & Share Icon Buttons -->
                            <div class="flex items-center gap-2" x-data="{ shopModalOpen: false, shareOpen: false }">
                                @if($post->products && $post->products->count() > 0)
                                <!-- Shop Icon Button -->
                                <div class="relative group">
                                    <button @click="shopModalOpen = true" 
                                            class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </button>
                                    <!-- Tooltip -->
                                    <div class="absolute right-0 top-full mt-1 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                                        Shop ({{ $post->products->count() }})
                                    </div>
                                </div>

                                <!-- Shop Modal (Delete Modal Style) -->
                                <div x-show="shopModalOpen" 
                                     x-cloak
                                     @keydown.escape.window="shopModalOpen = false"
                                     class="fixed inset-0 z-50 overflow-y-auto" 
                                     style="display: none;">
                                    <div class="flex items-center justify-center min-h-screen px-4">
                                        <!-- Backdrop -->
                                        <div class="fixed inset-0 transition-opacity" 
                                             style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                                             @click="shopModalOpen = false"></div>
                                        
                                        <!-- Modal -->
                                        <div class="relative rounded-lg shadow-xl max-w-6xl w-full border border-gray-200"
                                             style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                                            
                                            <!-- Header -->
                                            <div class="p-6 border-b border-gray-200">
                                                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                    </svg>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Shop This Article</h3>
                                                <p class="text-sm text-gray-500 text-center">
                                                    {{ $post->products->count() }} product{{ $post->products->count() > 1 ? 's' : '' }} featured in this article
                                                </p>
                                            </div>
                                            
                                            <!-- Product Grid -->
                                            <div class="p-6 overflow-y-auto" style="max-height: 70vh;">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                                    @foreach($post->products as $product)
                                                        <x-product-card-unified :product="$product" size="default" />
                                                    @endforeach
                                                </div>
                                            </div>
                                            
                                            <!-- Footer -->
                                            <div class="p-6 border-t border-gray-200 flex justify-center">
                                                <button @click="shopModalOpen = false" 
                                                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Share Dropdown -->
                                <div class="relative">
                                    <button @click="shareOpen = !shareOpen" 
                                            @click.away="shareOpen = false"
                                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="shareOpen" 
                                         x-transition
                                         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                        <div class="p-2">
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                                               target="_blank"
                                               class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 rounded-lg transition">
                                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                                <span class="text-gray-700">Facebook</span>
                                            </a>
                                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" 
                                               target="_blank"
                                               class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 rounded-lg transition">
                                                <svg class="w-5 h-5 text-sky-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                                </svg>
                                                <span class="text-gray-700">Twitter</span>
                                            </a>
                                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($post->title) }}" 
                                               target="_blank"
                                               class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 rounded-lg transition">
                                                <svg class="w-5 h-5 text-blue-700" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                                </svg>
                                                <span class="text-gray-700">LinkedIn</span>
                                            </a>
                                            <button onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Link copied!');"
                                                    class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 rounded-lg transition w-full">
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                                </svg>
                                                <span class="text-gray-700">Copy Link</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Featured Image -->
                    @if($post->media || $post->featured_image)
                    <div class="px-8 pb-8">
                        @if($post->media)
                            <img src="{{ $post->media->large_url }}" 
                                 alt="{{ $post->featured_image_alt ?? $post->title }}" 
                                 class="w-full rounded-xl">
                        @elseif($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                 alt="{{ $post->featured_image_alt }}" 
                                 class="w-full rounded-xl">
                        @endif
                    </div>
                    @endif

                    <!-- YouTube Video -->
                    @if($post->youtube_url && $post->youtube_video_id)
                    <div class="px-8 pb-8">
                        <div class="relative rounded-xl overflow-hidden shadow-lg" style="padding-bottom: 56.25%; height: 0;">
                            <iframe 
                                src="{{ $post->youtube_embed_url }}" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen
                                class="absolute top-0 left-0 w-full h-full">
                            </iframe>
                        </div>
                    </div>
                    @endif

                    <!-- Content -->
                    <div class="px-8 pb-8">
                        <div class="prose prose-lg max-w-none">
                            {!! $post->content !!}
                        </div>
                    </div>

                    <!-- Tags -->
                    @if(\App\Models\SiteSetting::get('blog_show_tags', '1') === '1' && $post->tags->count() > 0)
                    <div class="px-8 py-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Tags:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}" 
                               class="inline-block bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-4 py-2 rounded-full transition duration-150">
                                #{{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Shop This Article -->
                    @if($post->products && $post->products->count() > 0)
                    <div id="shop-this-article" class="px-8 py-8 border-t border-gray-200 bg-gradient-to-br from-green-50 to-blue-50">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    Shop This Article
                                </h2>
                                <p class="text-gray-600 mt-1">Products featured or recommended in this article</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($post->products as $product)
                                <x-product-card-unified :product="$product" size="default" />
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Share -->
                    <div class="px-8 py-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Share this post:</h3>
                        <div class="flex space-x-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('products.show', $post->slug)) }}" 
                               target="_blank"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('products.show', $post->slug)) }}&text={{ urlencode($post->title) }}" 
                               target="_blank"
                               class="bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg">
                                Twitter
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('products.show', $post->slug)) }}" 
                               target="_blank"
                               class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg">
                                LinkedIn
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Author Bio -->
                @if(\App\Models\SiteSetting::get('blog_show_author', '1') === '1')
                <div class="bg-white rounded-lg shadow-sm mt-8 p-8">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            @if($post->author->authorProfile?->media)
                                <img src="{{ $post->author->authorProfile->media->medium_url }}" 
                                     alt="{{ $post->author->name }}"
                                     class="w-16 h-16 rounded-full object-cover">
                            @elseif($post->author->authorProfile?->avatar)
                                <img src="{{ asset('storage/' . $post->author->authorProfile->avatar) }}" 
                                     alt="{{ $post->author->name }}"
                                     class="w-16 h-16 rounded-full object-cover">
                            @elseif($post->author->media)
                                <img src="{{ $post->author->media->medium_url }}" 
                                     alt="{{ $post->author->name }}"
                                     class="w-16 h-16 rounded-full object-cover">
                            @elseif($post->author->avatar)
                                <img src="{{ asset('storage/' . $post->author->avatar) }}" 
                                     alt="{{ $post->author->name }}"
                                     class="w-16 h-16 rounded-full object-cover">
                            @else
                                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    {{ substr($post->author->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-1">
                                <a href="{{ route('blog.author', $post->author->authorProfile->slug) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $post->author->name }}
                                </a>
                            </h3>
                            @if($post->author->authorProfile?->job_title)
                                <p class="text-sm text-gray-500 mb-2">{{ $post->author->authorProfile->job_title }}</p>
                            @endif
                            @if($post->author->authorProfile?->bio)
                                <p class="text-gray-600 mb-3">{{ \Illuminate\Support\Str::limit($post->author->authorProfile->bio, 200) }}</p>
                            @else
                                <p class="text-gray-600 mb-3">Content writer and blogger passionate about sharing knowledge.</p>
                            @endif
                            <a href="{{ route('blog.author', $post->author->authorProfile->slug) }}" 
                               class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium text-sm">
                                View author profile
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Related Posts -->
                @if($relatedPosts->count() > 0)
                <div class="bg-white rounded-lg shadow-sm mt-8 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Posts</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $related)
                    <article class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
                        @if($related->media || $related->featured_image)
                            @if($related->media)
                                <img src="{{ $related->media->medium_url }}" 
                                     alt="{{ $related->title }}" 
                                     class="w-full h-40 object-cover">
                            @elseif($related->featured_image)
                                <img src="{{ asset('storage/' . $related->featured_image) }}" 
                                     alt="{{ $related->title }}" 
                                     class="w-full h-40 object-cover">
                            @endif
                        @endif
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 mb-2 hover:text-blue-600">
                                <a href="{{ route('products.show', $related->slug) }}">
                                    {{ Str::limit($related->title, 50) }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500">{{ $related->published_at->format('M d, Y') }}</p>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
            @endif

                <!-- Comments Section -->
                @if(\App\Models\SiteSetting::get('blog_show_comments', '1') === '1' && $post->allow_comments)
                @livewire('blog.comment-section', ['post' => $post])
                @endif

                <!-- Old Comment Section (Backup) -->
                @if(false && $post->allow_comments)
                <div class="bg-white rounded-lg shadow-sm mt-8 p-8" x-data="{ replyingTo: null }">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <svg class="w-6 h-6 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Comments ({{ $post->approvedComments->count() }})
                        </h2>
                    </div>

                    @if(session('comment_success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('comment_success') }}
                    </div>
                    @endif

                    <!-- Comment Form -->
                    <div class="mb-8 bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave a Comment</h3>
                        <form action="{{ route('blog.comments.store', $post->id) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                @guest
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                        <input type="text" 
                                               name="guest_name" 
                                               value="{{ old('guest_name') }}"
                                               placeholder="Your Name" 
                                               required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('guest_name') border-red-500 @enderror">
                                        @error('guest_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                        <input type="email" 
                                               name="guest_email" 
                                               value="{{ old('guest_email') }}"
                                               placeholder="your@email.com" 
                                               required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('guest_email') border-red-500 @enderror">
                                        @error('guest_email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                @else
                                <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>
                                @endguest

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Comment *</label>
                                    <textarea name="content" 
                                              rows="4" 
                                              placeholder="Share your thoughts..." 
                                              required
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                                    @error('content')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Your comment will be reviewed before being published.</p>
                                </div>

                                <div class="flex gap-2 items-center justify-between">
                                    <button type="submit" 
                                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        Post Comment
                                    </button>
                                    @guest
                                    <p class="text-sm text-gray-600">
                                        Have an account? 
                                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Sign in</a>
                                    </p>
                                    @endguest
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Comments List -->
                    @if($post->approvedComments->count() > 0)
                    <div class="space-y-6">
                        @foreach($post->approvedComments as $comment)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-start gap-4">
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        {{ substr($comment->commenter_name, 0, 1) }}
                                    </div>
                                </div>

                                <!-- Comment Content -->
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $comment->commenter_name }}</h4>
                                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $comment->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-gray-700 leading-relaxed">{{ $comment->content }}</p>

                                    <!-- Replies -->
                                    @if($comment->approvedReplies->count() > 0)
                                    <div class="mt-4 space-y-4">
                                        @foreach($comment->approvedReplies as $reply)
                                        <div class="flex items-start gap-3 pl-4 border-l-2 border-blue-200">
                                            <!-- Reply Avatar -->
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                                                    {{ substr($reply->commenter_name, 0, 1) }}
                                                </div>
                                            </div>

                                            <!-- Reply Content -->
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h5 class="font-semibold text-gray-900 text-sm">{{ $reply->commenter_name }}</h5>
                                                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-gray-700 text-sm">{{ $reply->content }}</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">No comments yet</p>
                        <p class="text-gray-400 text-sm mt-1">Be the first to share your thoughts!</p>
                    </div>
                    @endif
                </div>
                @endif
            </article>
        </div>
    </div>
</div>
@endsection
