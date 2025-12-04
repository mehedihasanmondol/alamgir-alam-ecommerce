@extends('layouts.app')

@section('title', !empty($category->meta_title) ? $category->meta_title : $category->name . ' - ' . \App\Models\SiteSetting::get('site_name', config('app.name')))

@section('description', !empty($category->meta_description) ? $category->meta_description : $category->description)

@section('keywords', !empty($category->meta_keywords) ? $category->meta_keywords : '')

@section('og_type', 'website')
@section('og_title', !empty($category->og_title) ? $category->og_title : $category->name)
@section('og_description', !empty($category->og_description) ? $category->og_description : $category->description)
@section('og_image', !empty($category->og_image) ? $category->og_image : ($category->media ? $category->getMediumImageUrl() : asset('images/category-default.jpg')))

@section('canonical', $category->canonical_url ?? route('categories.show', $category->slug))

@section('content')
<div class="bg-gray-50 min-h-screen" x-data="categoryPage()">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="array_merge(
        [['label' => 'Home', 'url' => route('home')]],
        [['label' => 'Categories', 'url' => route('categories.index')]],
        $breadcrumb
    )" />

    <!-- Category Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Category Image -->
                @if($category->media)
                    <div class="w-24 h-24 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                        <img 
                            src="{{ $category->getThumbnailUrl() }}" 
                            alt="{{ $category->name }}"
                            class="w-full h-full object-cover"
                        >
                    </div>
                @endif

                <!-- Category Info -->
                <div class="flex-1">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                        {{ $category->name }}
                    </h1>
                    @if($category->description)
                        <p class="text-gray-600 max-w-3xl">
                            {{ $category->description }}
                        </p>
                    @endif
                    <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            {{ $products->total() }} {{ Str::plural('Product', $products->total()) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subcategories (if any) -->
    @if($category->activeChildren->isNotEmpty())
        <div class="bg-white border-b border-gray-200">
            <div class="container mx-auto px-4 py-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Subcategories</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($category->activeChildren as $child)
                        <a href="{{ route('categories.show', $child->slug) }}" 
                           class="group flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition">
                            @if($child->image)
                                <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100 mb-2">
                                    <img 
                                        src="{{ asset('storage/' . $child->image) }}" 
                                        alt="{{ $child->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform"
                                    >
                                </div>
                            @else
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-green-100 to-blue-100 flex items-center justify-center mb-2">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                            @endif
                            <span class="text-sm font-medium text-gray-900 text-center group-hover:text-green-600 transition-colors">
                                {{ $child->name }}
                            </span>
                            @if($child->products->count() > 0)
                                <span class="text-xs text-gray-500 mt-1">
                                    ({{ $child->products->count() }})
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar Filters -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Filters</h3>
                        <button @click="clearFilters()" class="text-sm text-green-600 hover:text-green-700">
                            Clear All
                        </button>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Price Range</h4>
                        <div class="space-y-2">
                            <input 
                                type="number" 
                                x-model="filters.min_price" 
                                placeholder="Min Price"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            >
                            <input 
                                type="number" 
                                x-model="filters.max_price" 
                                placeholder="Max Price"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            >
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Availability</h4>
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                x-model="filters.in_stock"
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">In Stock Only</span>
                        </label>
                    </div>

                    <!-- On Sale -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Special Offers</h4>
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                x-model="filters.on_sale"
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">On Sale</span>
                        </label>
                    </div>

                    <!-- Apply Filters Button -->
                    <button 
                        @click="applyFilters()"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition"
                    >
                        Apply Filters
                    </button>
                </div>
            </aside>

            <!-- Products Grid -->
            <main class="flex-1">
                <!-- Toolbar -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <!-- Results Count -->
                        <div class="text-sm text-gray-600">
                            Showing <span class="font-medium">{{ $products->firstItem() ?? 0 }}</span> 
                            to <span class="font-medium">{{ $products->lastItem() ?? 0 }}</span> 
                            of <span class="font-medium">{{ $products->total() }}</span> products
                        </div>

                        <!-- Sort & View Options -->
                        <div class="flex items-center gap-4">
                            <!-- Sort -->
                            <select 
                                x-model="filters.sort"
                                @change="applyFilters()"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                            >
                                <option value="latest">Latest</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="name_asc">Name: A to Z</option>
                                <option value="name_desc">Name: Z to A</option>
                            </select>

                            <!-- View Mode Toggle -->
                            <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                                <button 
                                    @click="viewMode = 'grid'"
                                    :class="viewMode === 'grid' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                    class="p-2 transition"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                    </svg>
                                </button>
                                <button 
                                    @click="viewMode = 'list'"
                                    :class="viewMode === 'list' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                    class="p-2 transition"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                @if($products->isEmpty())
                    <!-- Empty State -->
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Products Found</h3>
                        <p class="text-gray-600 mb-6">Try adjusting your filters or browse other categories.</p>
                        <a href="{{ route('categories.index') }}" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Browse All Categories
                        </a>
                    </div>
                @else
                    <!-- Grid View -->
                    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <x-frontend.product-card :product="$product" />
                        @endforeach
                    </div>

                    <!-- List View -->
                    <div x-show="viewMode === 'list'" class="space-y-4">
                        @foreach($products as $product)
                            @php
                                // Get default variant - handle both relationship and collection
                                $variant = null;
                                if (isset($product->defaultVariant) && !is_null($product->defaultVariant)) {
                                    $variant = is_object($product->defaultVariant) && method_exists($product->defaultVariant, 'first') 
                                        ? $product->defaultVariant->first() 
                                        : $product->defaultVariant;
                                }
                                
                                if (!$variant && isset($product->variants) && $product->variants->isNotEmpty()) {
                                    $variant = $product->variants->where('is_default', true)->first() ?? $product->variants->first();
                                }
                                
                                // Safely get variant properties
                                $price = 0;
                                $originalPrice = null;
                                $discount = 0;
                                $inStock = false;
                                $canAddToCart = false;
                                $showStockInfo = false;
                                
                                if ($variant && is_object($variant)) {
                                    $price = $variant->sale_price ?? $variant->price ?? 0;
                                    $originalPrice = ($variant->sale_price ?? null) ? ($variant->price ?? null) : null;
                                    $discount = $originalPrice ? round((($originalPrice - $price) / $originalPrice) * 100) : 0;
                                    $inStock = ($variant->stock_quantity ?? 0) > 0;
                                    
                                    // Use global stock restriction methods
                                    $canAddToCart = $variant->canAddToCart();
                                    $showStockInfo = $variant->shouldShowStock();
                                }
                                
                                $image = $product->getPrimaryThumbnailUrl();
                            @endphp
                            
                            <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition p-4">
                                <div class="flex gap-4">
                                    <!-- Product Image -->
                                    <a href="{{ route('products.show', $product->slug) }}" class="flex-shrink-0">
                                        @if($image)
                                            <img 
                                                src="{{ $image }}" 
                                                alt="{{ $product->name }}"
                                                class="w-32 h-32 object-cover rounded-lg"
                                            >
                                        @else
                                            <div class="w-32 h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </a>

                                    <!-- Product Info -->
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('products.show', $product->slug) }}" class="block">
                                            <h3 class="text-lg font-semibold text-gray-900 hover:text-green-600 transition mb-1">
                                                {{ $product->name }}
                                            </h3>
                                        </a>
                                        @if($product->brand)
                                            <p class="text-sm text-gray-500 mb-2">{{ $product->brand->name }}</p>
                                        @endif
                                        <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                            {{ Str::limit($product->short_description, 150) }}
                                        </p>
                                        <div class="flex items-center gap-4">
                                            <div>
                                                @if($originalPrice)
                                                    <span class="text-sm text-gray-400 line-through mr-2">৳{{ number_format($originalPrice, 2) }}</span>
                                                @endif
                                                <span class="text-xl font-bold text-green-600">৳{{ number_format($price, 2) }}</span>
                                            </div>
                                            @if($discount > 0)
                                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                                    -{{ $discount }}%
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex flex-col gap-2">
                                        @if($variant && is_object($variant))
                                            @if($canAddToCart)
                                                <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                                                    Add to Cart
                                                </button>
                                            @elseif($showStockInfo)
                                                <button disabled class="px-4 py-2 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed">
                                                    Out of Stock
                                                </button>
                                            @endif
                                        @else
                                            <button disabled class="px-4 py-2 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed">
                                                Unavailable
                                            </button>
                                        @endif
                                        <a href="{{ route('products.show', $product->slug) }}" class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition text-center">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

@push('scripts')
<script>
function categoryPage() {
    const urlParams = new URLSearchParams(window.location.search);
    
    return {
        viewMode: 'grid',
        filters: {
            min_price: urlParams.get('min_price') || '',
            max_price: urlParams.get('max_price') || '',
            in_stock: urlParams.get('in_stock') === '1',
            on_sale: urlParams.get('on_sale') === '1',
            sort: urlParams.get('sort') || 'latest'
        },
        
        applyFilters() {
            const params = new URLSearchParams();
            
            if (this.filters.min_price) params.set('min_price', this.filters.min_price);
            if (this.filters.max_price) params.set('max_price', this.filters.max_price);
            if (this.filters.in_stock) params.set('in_stock', '1');
            if (this.filters.on_sale) params.set('on_sale', '1');
            if (this.filters.sort) params.set('sort', this.filters.sort);
            
            window.location.href = window.location.pathname + '?' + params.toString();
        },
        
        clearFilters() {
            this.filters = {
                min_price: '',
                max_price: '',
                in_stock: false,
                on_sale: false,
                sort: 'latest'
            };
            window.location.href = window.location.pathname;
        }
    }
}
</script>
@endpush
@endsection
