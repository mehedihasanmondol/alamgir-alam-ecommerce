@extends('layouts.app')

@section('title', 'All Categories - ' . \App\Models\SiteSetting::get('site_name', config('app.name')))

@section('description', 'Browse all product categories and find what you\'re looking for. Shop by category for health, wellness, and beauty products.')

@section('keywords', 'categories, shop, products, browse, product categories')

@section('og_type', 'website')
@section('og_image', asset('images/categories-banner.jpg'))
@section('canonical', route('categories.index'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Categories', 'url' => null]
    ]" />

    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                    Shop by Category
                </h1>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Explore our wide range of product categories and find exactly what you need
                </p>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="container mx-auto px-4 py-8">
        @if($categories->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Categories Available</h3>
                <p class="text-gray-600">Categories will be displayed here once they are added.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($categories as $category)
                    <!-- Category Card -->
                    <a href="{{ route('categories.show', $category->slug) }}" 
                       class="group bg-white rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <!-- Category Image -->
                        <div class="relative overflow-hidden bg-gradient-to-br from-green-50 to-blue-50 aspect-square">
                            @if($category->media)
                                <img 
                                    src="{{ $category->getThumbnailUrl() }}" 
                                    alt="{{ $category->name }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Product Count Badge -->
                            @if($category->products->count() > 0)
                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-medium text-gray-700">
                                    {{ $category->products->count() }} {{ Str::plural('Product', $category->products->count()) }}
                                </div>
                            @endif
                        </div>

                        <!-- Category Info -->
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-green-600 transition-colors">
                                {{ $category->name }}
                            </h3>
                            
                            @if($category->description)
                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                    {{ $category->description }}
                                </p>
                            @endif

                            <!-- Subcategories -->
                            @if($category->activeChildren->isNotEmpty())
                                <div class="border-t border-gray-100 pt-3 mt-3">
                                    <p class="text-xs font-medium text-gray-500 mb-2">Subcategories:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($category->activeChildren->take(3) as $child)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs text-gray-700 hover:bg-green-100 hover:text-green-700 transition-colors">
                                                {{ $child->name }}
                                            </span>
                                        @endforeach
                                        @if($category->activeChildren->count() > 3)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs text-gray-700">
                                                +{{ $category->activeChildren->count() - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- View Category Button -->
                            <div class="mt-4">
                                <span class="inline-flex items-center text-sm font-medium text-green-600 group-hover:text-green-700">
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
        @endif
    </div>

    <!-- Featured Categories Section (Optional) -->
    @if($categories->where('is_featured', true)->isNotEmpty())
        <div class="bg-white border-t border-gray-200 py-12 mt-8">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">
                    Featured Categories
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($categories->where('is_featured', true)->take(6) as $featured)
                        <a href="{{ route('categories.show', $featured->slug) }}" 
                           class="group text-center p-4 rounded-lg hover:bg-gray-50 transition">
                            @if($featured->image)
                                <div class="w-20 h-20 mx-auto mb-3 rounded-full overflow-hidden bg-gray-100">
                                    <img 
                                        src="{{ asset('storage/' . $featured->image) }}" 
                                        alt="{{ $featured->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform"
                                    >
                                </div>
                            @else
                                <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-gradient-to-br from-green-100 to-blue-100 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                            @endif
                            <h3 class="text-sm font-medium text-gray-900 group-hover:text-green-600 transition-colors">
                                {{ $featured->name }}
                            </h3>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-green-600 to-blue-600 py-12 mt-8">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
                Can't Find What You're Looking For?
            </h2>
            <p class="text-white/90 mb-6 max-w-2xl mx-auto">
                Browse all our products or use our search feature to find exactly what you need
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('shop') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-white text-green-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Browse All Products
                </a>
                <a href="{{ route('contact') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-green-600 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
