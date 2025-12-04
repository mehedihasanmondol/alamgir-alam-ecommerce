@extends('layouts.app')

@section('title', $seoData['title'] ?? $brand->name)

@section('description', $seoData['description'] ?? 'Shop ' . $brand->name . ' products')

@section('keywords', $seoData['keywords'] ?? $brand->name . ' products')

@section('og_type', $seoData['og_type'] ?? 'website')
@section('og_title', $seoData['title'] ?? $brand->name)
@section('og_description', $seoData['description'] ?? 'Shop ' . $brand->name . ' products')
@section('og_image', $seoData['og_image'] ?? asset('images/og-default.jpg'))
@section('canonical', $seoData['canonical'] ?? route('brands.show', $brand->slug))

@section('twitter_card', 'summary_large_image')
@section('twitter_title', $seoData['title'] ?? $brand->name)
@section('twitter_description', $seoData['description'] ?? 'Shop ' . $brand->name . ' products')
@section('twitter_image', $seoData['og_image'] ?? asset('images/og-default.jpg'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Brands', 'url' => route('brands.index')],
        ['label' => $brand->name, 'url' => null]
    ]" />

    <!-- Brand Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <!-- Brand Logo -->
                <div class="w-32 h-32 flex-shrink-0 bg-white border-2 border-gray-100 rounded-lg p-4 flex items-center justify-center">
                    @if($brand->media || $brand->logo)
                        <img 
                            src="{{ $brand->getMediumLogoUrl() }}" 
                            alt="{{ $brand->name }}"
                            class="max-w-full max-h-full object-contain"
                        >
                    @else
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Brand Info -->
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                        {{ $brand->name }}
                    </h1>
                    @if($brand->description)
                        <p class="text-gray-600 text-lg mb-4 max-w-3xl">
                            {{ $brand->description }}
                        </p>
                    @endif
                    <div class="flex items-center justify-center md:justify-start gap-4 text-sm text-gray-600">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            {{ $products->total() }} {{ Str::plural('Product', $products->total()) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="container mx-auto px-4 py-8">
        @if($products->isEmpty())
            <!-- No Products State -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Products Available</h3>
                <p class="text-gray-600 mb-6">
                    {{ $brand->name }} products will be displayed here once they are added.
                </p>
                <a href="{{ route('brands.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Browse Other Brands
                </a>
            </div>
        @else
            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <x-product-card-unified :product="$product" layout="grid" />
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Related Brands Section -->
    @php
        $relatedBrands = \App\Modules\Ecommerce\Brand\Models\Brand::where('is_active', true)
            ->where('id', '!=', $brand->id)
            ->withCount('products')
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->take(8)
            ->get();
    @endphp

    @if($relatedBrands->isNotEmpty())
        <div class="bg-white border-t border-gray-200 py-12 mt-8">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">
                    Explore Other Brands
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                    @foreach($relatedBrands as $relatedBrand)
                        <a href="{{ route('brands.show', $relatedBrand->slug) }}" 
                           class="group text-center p-4 bg-gray-50 rounded-lg hover:bg-white hover:shadow-md transition">
                            @if($relatedBrand->media || $relatedBrand->logo)
                                <div class="w-16 h-16 mx-auto mb-2 flex items-center justify-center">
                                    <img 
                                        src="{{ $relatedBrand->getThumbnailUrl() }}" 
                                        alt="{{ $relatedBrand->name }}"
                                        class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform"
                                    >
                                </div>
                            @else
                                <div class="w-16 h-16 mx-auto mb-2 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            @endif
                            <h3 class="text-xs font-medium text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                {{ $relatedBrand->name }}
                            </h3>
                        </a>
                    @endforeach
                </div>
                <div class="text-center mt-6">
                    <a href="{{ route('brands.index') }}" 
                       class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                        View All Brands
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
