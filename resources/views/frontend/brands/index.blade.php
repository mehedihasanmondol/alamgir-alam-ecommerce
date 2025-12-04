@extends('layouts.app')

@section('title', 'All Brands - ' . \App\Models\SiteSetting::get('site_name', config('app.name')))

@section('description', 'Browse all product brands and shop from your favorite manufacturers. Quality brands for health, wellness, and beauty products.')

@section('keywords', 'brands, manufacturers, shop by brand, product brands, trusted brands')

@section('og_type', 'website')
@section('og_image', asset('images/brands-banner.jpg'))
@section('canonical', route('brands.index'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Brands', 'url' => null]
    ]" />

    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                    Shop by Brand
                </h1>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Explore products from trusted brands and manufacturers
                </p>
            </div>
        </div>
    </div>

    <!-- A-Z Letter Navigation -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10 shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-wrap items-center justify-center gap-2">
                <a href="{{ route('brands.index') }}" 
                   class="px-3 py-1.5 rounded-md text-sm font-medium {{ !request('letter') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition">
                    All
                </a>
                @foreach(range('A', 'Z') as $letter)
                    <a href="{{ route('brands.index', ['letter' => $letter]) }}" 
                       class="px-3 py-1.5 rounded-md text-sm font-medium {{ request('letter') === $letter ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition">
                        {{ $letter }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Brands Grid -->
    <div class="container mx-auto px-4 py-8">
        @if($brands->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    @if(request('letter'))
                        No Brands Found Starting with "{{ request('letter') }}"
                    @else
                        No Brands Available
                    @endif
                </h3>
                <p class="text-gray-600 mb-6">
                    @if(request('letter'))
                        Try browsing other letters or view all brands
                    @else
                        Brands will be displayed here once they are added
                    @endif
                </p>
                @if(request('letter'))
                    <a href="{{ route('brands.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        View All Brands
                    </a>
                @endif
            </div>
        @else
            <!-- Current Filter Info -->
            @if(request('letter'))
                <div class="mb-6 flex items-center justify-between bg-white rounded-lg shadow-sm p-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Brands starting with "{{ request('letter') }}"
                        </h2>
                        <p class="text-sm text-gray-600">{{ $brands->total() }} {{ Str::plural('brand', $brands->total()) }} found</p>
                    </div>
                    <a href="{{ route('brands.index') }}" 
                       class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Clear Filter
                    </a>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($brands as $brand)
                    <!-- Brand Card -->
                    <a href="{{ route('brands.show', $brand->slug) }}" 
                       class="group bg-white rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <!-- Brand Logo -->
                        <div class="relative overflow-hidden bg-white aspect-video flex items-center justify-center p-6 border-b border-gray-100">
                            @if($brand->media || $brand->logo)
                                <img 
                                    src="{{ $brand->getThumbnailUrl() }}" 
                                    alt="{{ $brand->name }}"
                                    class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300"
                                >
                            @else
                                <div class="text-center">
                                    <div class="w-20 h-20 mx-auto mb-2 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <span class="text-lg font-bold text-gray-900">{{ $brand->name }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Brand Info -->
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                {{ $brand->name }}
                            </h3>
                            
                            @if($brand->description)
                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                    {{ $brand->description }}
                                </p>
                            @endif

                            <!-- Product Count -->
                            @if($brand->products_count > 0)
                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    {{ $brand->products_count }} {{ Str::plural('Product', $brand->products_count) }}
                                </div>
                            @endif

                            <!-- View Products Button -->
                            <div class="mt-4">
                                <span class="inline-flex items-center text-sm font-medium text-blue-600 group-hover:text-blue-700">
                                    View Products
                                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($brands->hasPages())
                <div class="mt-8">
                    {{ $brands->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Available Letters Info -->
    @if($brandsByLetter->isNotEmpty() && !request('letter'))
        <div class="bg-white border-t border-gray-200 py-8">
            <div class="container mx-auto px-4">
                <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">
                    Quick Browse by Letter
                </h2>
                <div class="flex flex-wrap justify-center gap-2 max-w-4xl mx-auto">
                    @foreach($brandsByLetter as $letter => $letterBrands)
                        <a href="{{ route('brands.index', ['letter' => $letter]) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 rounded-lg transition">
                            <span class="font-bold text-lg">{{ $letter }}</span>
                            <span class="text-sm">({{ $letterBrands->count() }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 py-12 mt-8">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
                Looking for Something Specific?
            </h2>
            <p class="text-white/90 mb-6 max-w-2xl mx-auto">
                Browse all our products or use our search feature to find exactly what you need
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('shop') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Browse All Products
                </a>
                <a href="{{ route('categories.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Browse by Category
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
