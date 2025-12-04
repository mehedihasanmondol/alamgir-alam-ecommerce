@extends('layouts.app')

@section('title', !empty($product->meta_title) ? $product->meta_title : $product->name . ' - ' . \App\Models\SiteSetting::get('site_name', config('app.name')))

@section('description', !empty($product->meta_description) ? $product->meta_description : $product->short_description)

@section('keywords', !empty($product->meta_keywords) ? $product->meta_keywords : (($product->brand ? $product->brand->name . ', ' : '') . ($product->category ? $product->category->name . ', ' : '') . 'buy online, shop'))

@php
    // Define variant early for use throughout the view (including meta tags)
    $variant = $defaultVariant ?? $product->variants->first();
@endphp

@section('og_type', 'product')
@section('og_title', !empty($product->meta_title) ? $product->meta_title : $product->name)
@section('og_description', !empty($product->meta_description) ? $product->meta_description : $product->short_description)
@section('og_image', $product->getPrimaryImageUrl() ?? asset('images/placeholder.png'))
@section('canonical', route('products.show', $product->slug))

@section('twitter_title', !empty($product->meta_title) ? $product->meta_title : $product->name)
@section('twitter_description', !empty($product->meta_description) ? $product->meta_description : $product->short_description)
@section('twitter_image', $product->getPrimaryImageUrl() ?? asset('images/placeholder.png'))

@push('meta_tags')
    <!-- Product Specific Meta -->
    <meta property="product:price:amount" content="{{ $variant->sale_price ?? $variant->price }}">
    <meta property="product:price:currency" content="BDT">
    @if($variant->stock_quantity > 0)
    <meta property="product:availability" content="in stock">
    @else
    <meta property="product:availability" content="out of stock">
    @endif
    @if($product->brand)
    <meta property="product:brand" content="{{ $product->brand->name }}">
    @endif
@endpush

@section('content')

<!-- Breadcrumb -->
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')]
    ];
    
    // Add parent category if exists
    if($product->category && $product->category->parent) {
        $breadcrumbs[] = [
            'label' => $product->category->parent->name,
            'url' => route('categories.show', $product->category->parent->slug)
        ];
    }
    
    // Add category if exists
    if($product->category) {
        $breadcrumbs[] = [
            'label' => $product->category->name,
            'url' => route('categories.show', $product->category->slug)
        ];
    }
    
    // Add brand if exists (optional)
    if($product->brand) {
        $breadcrumbs[] = [
            'label' => $product->brand->name,
            'url' => route('brands.show', $product->brand->slug)
        ];
    }
    
    // Add current product (no link)
    $breadcrumbs[] = [
        'label' => $product->name,
        'url' => null
    ];
@endphp

<x-breadcrumb :items="$breadcrumbs" />

<!-- Main Product Section -->
<div class="bg-white">
    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Column: Product Gallery (4 columns) -->
            <div class="lg:col-span-4">
                <x-product-gallery :product="$product" />
            </div>

            <!-- Middle Column: Product Info (5 columns) -->
            <div class="lg:col-span-5">
                <!-- Badges Row -->
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($variant && $variant->sale_price && $variant->sale_price < $variant->price)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-red-600 text-white">
                            Special!
                        </span>
                    @endif
                    @if($product->brand && $product->brand->is_featured)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-teal-600 text-white">
                            iHerb Brands
                        </span>
                    @endif
                </div>

                <!-- Product Title -->
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">
                    {{ $product->name }}
                </h1>

                <!-- Brand -->
                @if($product->brand)
                <div class="mb-3">
                    <span class="text-sm text-gray-600">By </span>
                    <a href="{{ route('brands.show', $product->brand->slug) }}" class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-800 transition font-medium group">
                        <span>{{ $product->brand->name }}</span>
                        <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                @endif

                <!-- Short Description -->
                @if($product->short_description)
                <div class="mb-4 pb-4 border-b border-gray-200">
                    <div class="text-sm text-gray-700 leading-relaxed prose prose-sm max-w-none">
                        {!! $product->short_description !!}
                    </div>
                </div>
                @endif

                <!-- Rating & Reviews Summary -->
                @if(\App\Models\SiteSetting::get('enable_product_reviews', '1') === '1' || \App\Models\SiteSetting::get('enable_product_qna', '1') === '1' || $product->views_count > 0)
                <div class="mb-4 pb-4 border-b border-gray-200">
                @if(\App\Models\SiteSetting::get('enable_product_reviews', '1') === '1')
                    <!-- Rating Stars and Score with Hover Tooltip -->
                    <div class="flex items-center space-x-2 mb-2 relative group">
                        <div class="flex items-center cursor-pointer">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($averageRating))
                                    <svg class="w-5 h-5 text-orange-400 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @elseif($i - 0.5 <= $averageRating)
                                    <svg class="w-5 h-5 text-orange-400" viewBox="0 0 20 20">
                                        <defs>
                                            <linearGradient id="half-{{ $i }}">
                                                <stop offset="50%" stop-color="currentColor"/>
                                                <stop offset="50%" stop-color="#D1D5DB"/>
                                            </linearGradient>
                                        </defs>
                                        <path fill="url(#half-{{ $i }})" d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xl font-bold text-gray-900">{{ number_format($averageRating, 1) }}</span>
                        <span class="text-sm text-gray-500">out of 5</span>
                        
                        @php
                            // Calculate rating distribution
                            $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                            $reviews = $product->approvedReviews;
                            foreach($reviews as $review) {
                                $rating = (int) $review->rating;
                                if(isset($ratingCounts[$rating])) {
                                    $ratingCounts[$rating]++;
                                }
                            }
                        @endphp
                        
                        <!-- Hover Tooltip -->
                        <div class="absolute left-0 top-full mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-xl p-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <!-- Average Rating Header -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-3xl font-bold text-gray-900">{{ number_format($averageRating, 1) }}</span>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($averageRating))
                                                <svg class="w-4 h-4 text-orange-400 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 mb-3">Based on {{ number_format($totalReviews) }} ratings</p>
                            
                            <!-- Rating Bars -->
                            <div class="space-y-1">
                                @foreach([5, 4, 3, 2, 1] as $star)
                                    @php
                                        $count = $ratingCounts[$star];
                                        $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                    @endphp
                                    <div class="flex items-center space-x-2 text-xs">
                                        <span class="text-gray-700 w-6">{{ $star }}</span>
                                        <div class="flex items-center flex-1">
                                            @for($i = 1; $i <= $star; $i++)
                                                <svg class="w-3 h-3 text-orange-400 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="bg-orange-400 h-full rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-gray-700 w-10 text-right">{{ $percentage }}%</span>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- See Reviews Link -->
                            <a href="#reviews-section" class="block mt-4 text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                                See customer reviews →
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Review and Q&A Links -->
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        @if(\App\Models\SiteSetting::get('enable_product_reviews', '1') === '1')
                        <a href="#reviews-section" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <span class="font-medium">{{ number_format($totalReviews) }}</span>
                            <span class="ml-1 text-gray-600">{{ Str::plural('Review', $totalReviews) }}</span>
                        </a>
                        @endif
                        
                        @if(\App\Models\SiteSetting::get('enable_product_reviews', '1') === '1' && \App\Models\SiteSetting::get('enable_product_qna', '1') === '1')
                        <span class="text-gray-300">|</span>
                        @endif
                        
                        @if(\App\Models\SiteSetting::get('enable_product_qna', '1') === '1')
                        <a href="#questions-section" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">{{ number_format($totalQuestions) }}</span>
                            <span class="ml-1 text-gray-600">{{ Str::plural('Question', $totalQuestions) }}</span>
                            @if($totalAnswers > 0)
                                <span class="ml-1 text-gray-500">({{ number_format($totalAnswers) }} answered)</span>
                            @endif
                        </a>
                        @endif
                        
                        @if((\App\Models\SiteSetting::get('enable_product_reviews', '1') === '1' || \App\Models\SiteSetting::get('enable_product_qna', '1') === '1') && $product->views_count > 0)
                        <span class="text-gray-300">|</span>
                        @endif
                        
                        @if($product->views_count > 0)
                        <div class="flex items-center text-gray-600">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span>{{ number_format($product->views_count) }} {{ Str::plural('view', $product->views_count) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Stock Status (only shown when restriction is enabled) -->
                @php
                    $showStockInfo = $variant && $variant->shouldShowStock();
                @endphp
                
                @if($showStockInfo)
                    @if($variant->stock_quantity > 0)
                    <div class="mb-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-green-700 font-semibold">In stock</span>
                        </div>
                        @if($variant->stock_quantity <= $variant->low_stock_alert)
                            <div class="mt-1 flex items-center text-sm text-orange-600">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium">Only {{ $variant->stock_quantity }} left - Order soon!</span>
                            </div>
                        @endif
                    </div>
                    @else
                    <div class="mb-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-red-700 font-semibold">Out of Stock</span>
                        </div>
                    </div>
                    @endif
                @endif

                @if(\App\Models\SiteSetting::get('enable_product_specifications', '1') === '1')
                <!-- Product Information List (Specifications) -->
                <div class="mb-6">
                    <div class="space-y-2 text-sm">
                        <!-- Best By Date - Removed as expires_at field doesn't exist in database -->

                        <!-- Category -->
                        @if($product->categories->isNotEmpty() || $product->category)
                        <div class="flex items-start space-x-2">
                            <span class="text-gray-700 font-medium min-w-[100px]">Category:</span>
                            <div class="flex flex-wrap gap-2">
                                @if($product->categories->isNotEmpty())
                                    @foreach($product->categories as $category)
                                        <a href="{{ route('categories.show', $category->slug) }}" 
                                           class="text-blue-600 hover:text-blue-800 hover:underline transition">
                                            {{ $category->name }}
                                        </a>@if(!$loop->last)<span class="text-gray-400">,</span>@endif
                                    @endforeach
                                @elseif($product->category)
                                    <a href="{{ route('categories.show', $product->category->slug) }}" 
                                       class="text-blue-600 hover:text-blue-800 hover:underline transition">
                                        {{ $product->category->name }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- First Available -->
                        <div class="flex items-start space-x-2">
                            <span class="text-gray-700 font-medium min-w-[100px]">First available:</span>
                            <span class="text-gray-900">{{ $product->created_at->format('m/Y') }}</span>
                        </div>

                        <!-- Shipping Weight -->
                        @if($variant && $variant->weight)
                        <div class="flex items-start space-x-2">
                            <span class="text-gray-700 font-medium min-w-[100px]">Shipping weight:</span>
                            <span class="text-gray-900">{{ number_format($variant->weight, 2) }} kg</span>
                            <button class="text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                        @endif

                        <!-- Product Code (SKU) -->
                        @if($variant && $variant->sku)
                        <div class="flex items-start space-x-2">
                            <span class="text-gray-700 font-medium min-w-[100px]">Product code:</span>
                            <span class="text-gray-900">{{ $variant->sku }}</span>
                        </div>
                        @endif

                        <!-- UPC Code - Removed as barcode field doesn't exist in database -->

                        <!-- Package Quantity - Removed as dimensions field doesn't exist in database -->

                        <!-- Dimensions -->
                        @if($variant && ($variant->length || $variant->width || $variant->height))
                        <div class="flex items-start space-x-2">
                            <span class="text-gray-700 font-medium min-w-[100px]">Dimensions:</span>
                            <span class="text-gray-900">
                                {{ number_format($variant->length ?? 0, 2) }} x {{ number_format($variant->width ?? 0, 2) }} x {{ number_format($variant->height ?? 0, 2) }} cm
                            </span>
                        </div>
                        @endif

                    </div>
                </div>
                @endif

            </div>

            <!-- Right Column: Price & Cart Sidebar (3 columns) -->
            <div class="lg:col-span-3">
                <div class="lg:sticky lg:top-[180px]">
                    <!-- Price & Cart Section (iHerb Style) -->
                    <div class="bg-white border border-gray-300 rounded-xl p-5 shadow-sm">
                        @if($product->product_type === 'variable' && $product->variants->count() > 1)
                            @php
                                $minPrice = $product->variants->min('sale_price') ?? $product->variants->min('price');
                                $maxPrice = $product->variants->max('sale_price') ?? $product->variants->max('price');
                            @endphp
                            <!-- Price Display -->
                            <div class="mb-3">
                                <div class="flex items-baseline space-x-2">
                                    <span class="text-3xl font-bold text-red-600">
                                        {{ currency_format($minPrice) }}
                                    </span>
                                    @if($minPrice != $maxPrice)
                                        <span class="text-lg text-gray-600">
                                            - {{ currency_format($maxPrice) }}
                                        </span>
                                    @endif
                                </div>
                                @if($product->variants->first() && $product->variants->first()->weight)
                                <div class="text-sm text-gray-600">
                                    ${{ number_format($minPrice / $product->variants->first()->weight, 2) }}/unit
                                </div>
                                @endif
                            </div>
                        @else
                            @if($variant)
                                <!-- Price Display -->
                                <div class="mb-3">
                                    @if($variant->sale_price && $variant->sale_price < $variant->price)
                                        <div class="flex items-baseline space-x-2 mb-1">
                                            <span class="text-3xl font-bold text-red-600">
                                                {{ currency_format($variant->sale_price) }}
                                            </span>
                                            <span class="text-sm font-semibold text-red-600">
                                                ({{ round((($variant->price - $variant->sale_price) / $variant->price) * 100) }}% off)
                                            </span>
                                        </div>
                                        <div class="flex items-baseline space-x-2">
                                            <span class="text-sm text-gray-500 line-through">
                                                {{ currency_format($variant->price) }}
                                            </span>
                                            @if($variant->weight)
                                            <span class="text-sm text-gray-600">
                                                {{ currency_format($variant->sale_price / $variant->weight) }}/unit
                                            </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="mb-1">
                                            <span class="text-3xl font-bold text-red-600">
                                                {{ currency_format($variant->price) }}
                                            </span>
                                        </div>
                                        @if($variant->weight)
                                        <div class="text-sm text-gray-600">
                                            {{ currency_format($variant->price / $variant->weight) }}/unit
                                        </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        @endif
                        
                        <!-- Variant Selector (for variable products) -->
                        @if($product->product_type === 'variable' && $product->variants->count() > 1)
                        <div class="mb-4">
                            <x-variant-selector :product="$product" />
                        </div>
                        @endif
                        
                        <!-- Add to Cart Component -->
                        <div>
                            @livewire('cart.add-to-cart', ['product' => $product, 'defaultVariant' => $defaultVariant])
                        </div>
                    </div>
                    
                    <!-- Add to Lists Button (Separate) -->
                    <div class="mt-3">
                        <button class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-white border-2 border-gray-300 rounded-xl text-green-600 hover:border-green-500 hover:bg-green-50 transition font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span>Add to Lists</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Frequently Purchased Together -->
@livewire('product.frequently-bought-together', ['product' => $product, 'relatedProducts' => $relatedProducts])

<!-- Inspired by Browsing -->

<!-- Recently Viewed Products -->
@if($recentlyViewed->count() > 0)
<div class=" py-8">
    <div class="container mx-auto px-4">
        <x-inspired-by-browsing :products="$recentlyViewed" />
    </div>
</div>
@endif

<!-- Product Overview Section -->
@if($product->description)
<div class="bg-white py-8">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Product overview</h2>
        
        <div class="bg-white rounded-lg border border-gray-200 p-6 lg:p-8">
            <div class="prose max-w-none">
                {!! $product->description !!}
            </div>
        </div>
    </div>
</div>
@endif

@if(\App\Models\SiteSetting::get('enable_product_qna', '1') === '1')
<!-- Questions and Answers Section -->
<div class="bg-gray-50 py-8" id="questions-section">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg border border-gray-200 p-6 lg:p-8">
            <!-- Success Message -->
            @if(session('question_success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-green-800 font-medium">{{ session('question_success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Header -->
            <div class="mb-6 flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Questions and answers</h2>
                    <p class="text-sm text-gray-600">
                        Answers posted solely reflect the views and opinions expressed by the contributors and not those of our store.
                    </p>
                </div>
                <div>
                    @livewire('product.ask-question', ['productId' => $product->id])
                </div>
            </div>

            <!-- Livewire Question List Component -->
            @livewire('product.question-list', ['productId' => $product->id])
        </div>
    </div>
</div>
@endif

@if(\App\Models\SiteSetting::get('enable_product_reviews', '1') === '1')
<!-- Customer Reviews Section -->
<div class="bg-gray-50 py-8" id="reviews-section">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg border border-gray-200 p-6 lg:p-8">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Customer Reviews</h2>
                <p class="text-sm text-gray-600">
                    Share your experience with this product and help other customers make informed decisions.
                </p>
            </div>

            <!-- Review List Component -->
            @livewire('product.review-list', ['productId' => $product->id])

            <!-- Review Form Component -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                @livewire('product.review-form', ['productId' => $product->id])
            </div>
        </div>
    </div>
</div>
@endif

<!-- Most Popular / Featured Products -->
@if($popularProducts->count() > 0)
<div class="bg-white py-8">
    <div class="container mx-auto px-4">
        <x-popular-products-slider :products="$popularProducts" />
    </div>
</div>
@endif

@endsection
