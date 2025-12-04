@extends('layouts.app')

@section('title', $product->meta_title ?? $product->name)
@section('meta_description', $product->meta_description ?? $product->short_description)
@section('meta_keywords', $product->meta_keywords ?? '')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b">
    <div class="container mx-auto px-4 py-2">
        <nav class="flex items-center space-x-2 text-xs text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-orange-600 transition">Home</a>
            <span>›</span>
            @if($product->category)
                @if($product->category->parent)
                    <a href="{{ route('shop') }}?category={{ $product->category->parent->slug }}" class="hover:text-orange-600 transition">
                        {{ $product->category->parent->name }}
                    </a>
                    <span>›</span>
                @endif
                <a href="{{ route('shop') }}?category={{ $product->category->slug }}" class="hover:text-orange-600 transition">
                    {{ $product->category->name }}
                </a>
                <span>›</span>
            @endif
            <span class="text-gray-900">{{ Str::limit($product->name, 50) }}</span>
        </nav>
    </div>
</div>

<!-- Main Product Section -->
<div class="bg-white">
    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6 lg:p-8">
                <!-- Left Column: Product Gallery (4 columns) -->
                <div class="lg:col-span-5">
                    <x-product-gallery :product="$product" />
                </div>

                <!-- Right Column: Product Info (7 columns) -->
                <div class="lg:col-span-7 space-y-4">
                    <!-- Brand -->
                    @if($product->brand)
                    <div>
                        <a href="{{ route('shop') }}?brand={{ $product->brand->slug }}" class="inline-flex items-center text-sm text-gray-600 hover:text-green-600 transition">
                            @if($product->brand->logo_path)
                                <img src="{{ asset('storage/' . $product->brand->logo_path) }}" alt="{{ $product->brand->name }}" class="h-6 mr-2">
                            @endif
                            <span class="font-medium">{{ $product->brand->name }}</span>
                        </a>
                    </div>
                    @endif

                    <!-- Product Name -->
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">
                            {{ $product->name }}
                        </h1>
                    </div>

                    <!-- Rating & Reviews -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($averageRating))
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @elseif($i - 0.5 <= $averageRating)
                                    <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <defs>
                                            <linearGradient id="half-{{ $i }}">
                                                <stop offset="50%" stop-color="currentColor"/>
                                                <stop offset="50%" stop-color="#e5e7eb"/>
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
                        <a href="#reviews" class="text-sm text-gray-600 hover:text-green-600 transition">
                            {{ $totalReviews }} {{ Str::plural('Review', $totalReviews) }}
                        </a>
                    </div>

                    <!-- Short Description -->
                    @if($product->short_description)
                    <div class="text-gray-600 leading-relaxed">
                        {{ $product->short_description }}
                    </div>
                    @endif

                    <!-- Price -->
                    <div class="border-t border-b border-gray-200 py-4">
                        @if($product->product_type === 'variable')
                            <!-- Variable Product Price Range -->
                            @php
                                $minPrice = $product->variants->min('price');
                                $maxPrice = $product->variants->max('price');
                            @endphp
                            <div class="flex items-baseline space-x-2">
                                <span class="text-3xl font-bold text-green-600">
                                    ৳{{ number_format($minPrice, 2) }}
                                </span>
                                @if($minPrice != $maxPrice)
                                    <span class="text-xl text-gray-500">- ৳{{ number_format($maxPrice, 2) }}</span>
                                @endif
                            </div>
                        @else
                            <!-- Simple/Grouped/Affiliate Product Price -->
                            @if($defaultVariant)
                                <div class="flex items-baseline space-x-3">
                                    @if($defaultVariant->sale_price && $defaultVariant->sale_price < $defaultVariant->price)
                                        <span class="text-3xl font-bold text-green-600">
                                            ৳{{ number_format($defaultVariant->sale_price, 2) }}
                                        </span>
                                        <span class="text-xl text-gray-400 line-through">
                                            ৳{{ number_format($defaultVariant->price, 2) }}
                                        </span>
                                        @php
                                            $discount = round((($defaultVariant->price - $defaultVariant->sale_price) / $defaultVariant->price) * 100);
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Save {{ $discount }}%
                                        </span>
                                    @else
                                        <span class="text-3xl font-bold text-green-600">
                                            ৳{{ number_format($defaultVariant->price, 2) }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Stock Status -->
                    @if($product->product_type !== 'affiliate')
                        <div class="flex items-center space-x-2">
                            @if($defaultVariant && $defaultVariant->stock_quantity > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    In Stock ({{ $defaultVariant->stock_quantity }} available)
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Out of Stock
                                </span>
                            @endif
                        </div>
                    @endif

                    <!-- Variant Selector (for variable products) -->
                    @if($product->product_type === 'variable')
                        <div class="space-y-4">
                            <x-variant-selector :product="$product" />
                        </div>
                    @endif

                    <!-- Add to Cart Component -->
                    @if($product->product_type === 'affiliate')
                        <!-- Affiliate Product Button -->
                        <div class="space-y-3">
                            <a href="{{ $product->affiliate_url }}" target="_blank" rel="nofollow noopener" 
                               class="block w-full bg-green-600 hover:bg-green-700 text-white text-center font-semibold py-4 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                                {{ $product->affiliate_button_text ?? 'View Product' }}
                                <svg class="inline-block w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            <p class="text-xs text-gray-500 text-center">
                                You will be redirected to the seller's website
                            </p>
                        </div>
                    @else
                        <!-- Regular Add to Cart -->
                        @livewire('cart.add-to-cart', [
                            'product' => $product,
                            'defaultVariant' => $defaultVariant
                        ])
                    @endif

                    <!-- Product Badges -->
                    <div class="flex flex-wrap gap-2">
                        @if($product->is_featured)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Featured
                            </span>
                        @endif
                        @if($product->created_at->gt(now()->subDays(30)))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                New Arrival
                            </span>
                        @endif
                    </div>

                    <!-- Share Buttons -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-medium text-gray-700">Share:</span>
                            <div class="flex space-x-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($product->name) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-sky-500 text-white hover:bg-sky-600 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . request()->url()) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white hover:bg-green-600 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="mt-8">
            <x-product-tabs :product="$product" :averageRating="$averageRating" :totalReviews="$totalReviews" />
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <x-related-products :products="$relatedProducts" title="Related Products" />
        </div>
        @endif

        <!-- Recently Viewed -->
        @if($recentlyViewed->count() > 0)
        <div class="mt-12">
            <x-related-products :products="$recentlyViewed" title="Recently Viewed" />
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Product detail page interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll to tabs
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
</script>
@endpush
