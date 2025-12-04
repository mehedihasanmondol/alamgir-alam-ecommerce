@section('title', $seoData['title'] ?? 'Shop')

@section('description', $seoData['description'] ?? 'Shop our products')

@section('keywords', $seoData['keywords'] ?? 'shop, products')

@section('og_type', $seoData['og_type'] ?? 'website')
@section('og_title', $seoData['title'] ?? 'Shop')
@section('og_description', $seoData['description'] ?? 'Shop our products')
@section('og_image', $seoData['og_image'] ?? asset('images/og-default.jpg'))
@section('canonical', $seoData['canonical'] ?? url()->current())

@section('twitter_card', 'summary_large_image')
@section('twitter_title', $seoData['title'] ?? 'Shop')
@section('twitter_description', $seoData['description'] ?? 'Shop our products')
@section('twitter_image', $seoData['og_image'] ?? asset('images/og-default.jpg'))

<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    @if($breadcrumb)
        <x-breadcrumb :items="$breadcrumb" />
    @endif

    <!-- Brand Header (for brand pages) - Compact Version -->
    @if($brand)
        <div class="bg-white border-b border-gray-200">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center gap-4">
                    <!-- Brand Logo -->
                    @if($brand->media || $brand->logo)
                        <div class="w-16 h-16 md:w-20 md:h-20 flex-shrink-0 rounded-lg overflow-hidden bg-white shadow-md border border-gray-200 p-2">
                            <img 
                                src="{{ $brand->getThumbnailUrl() }}" 
                                alt="{{ $brand->name }}"
                                class="w-full h-full object-contain"
                            >
                        </div>
                    @else
                        <div class="w-16 h-16 md:w-20 md:h-20 flex-shrink-0 rounded-lg bg-gradient-to-br from-purple-100 to-pink-100 shadow-md flex items-center justify-center">
                            <svg class="w-8 h-8 md:w-10 md:h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                    @endif

                    <!-- Brand Info -->
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900 truncate">
                            {{ $brand->name }}
                        </h1>
                        @if($brand->description)
                            <p class="text-sm text-gray-600 line-clamp-1 mt-1">
                                {{ $brand->description }}
                            </p>
                        @endif
                    </div>
                    
                    <!-- Stats (Inline) -->
                    @php
                        $totalProducts = $brand->products->count();
                    @endphp
                    <div class="hidden sm:flex items-center gap-4 text-sm">
                        @if($totalProducts > 0)
                            <div class="flex items-center gap-1 text-gray-600">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <span class="font-semibold text-gray-900">{{ $totalProducts }}</span>
                            </div>
                        @endif
                        
                        <!-- Brand Website Link -->
                        @if($brand->website)
                            <a href="{{ $brand->getWebsiteUrl() }}" target="_blank" rel="noopener" class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                </svg>
                                <span class="text-sm font-medium">Website</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Category Header (for category pages) - Compact Version with Dropdown -->
    @if($category)
        <div class="bg-white border-b border-gray-200" x-data="{ subcategoriesOpen: false }">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center gap-4">
                    <!-- Category Image -->
                    @if($category->media)
                        <div class="w-16 h-16 md:w-20 md:h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 shadow-md">
                            <img 
                                src="{{ $category->getThumbnailUrl() }}" 
                                alt="{{ $category->name }}"
                                class="w-full h-full object-cover"
                            >
                        </div>
                    @else
                        <div class="w-16 h-16 md:w-20 md:h-20 flex-shrink-0 rounded-lg bg-gradient-to-br from-green-100 to-blue-100 shadow-md flex items-center justify-center">
                            <svg class="w-8 h-8 md:w-10 md:h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                    @endif

                    <!-- Category Info -->
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900 truncate">
                            {{ $category->name }}
                        </h1>
                        @if($category->description)
                            <p class="text-sm text-gray-600 line-clamp-1 mt-1">
                                {{ $category->description }}
                            </p>
                        @endif
                    </div>
                    
                    <!-- Stats (Inline) -->
                    @php
                        $totalProducts = $category->products->count();
                        $subcategoriesCount = $category->activeChildren->count();
                    @endphp
                    <div class="hidden sm:flex items-center gap-4 text-sm">
                        @if($totalProducts > 0)
                            <div class="flex items-center gap-1 text-gray-600">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <span class="font-semibold text-gray-900">{{ $totalProducts }}</span>
                            </div>
                        @endif
                        
                        <!-- Subcategories Dropdown -->
                        @if($subcategoriesCount > 0)
                            <div class="relative">
                                <button 
                                    @click="subcategoriesOpen = !subcategoriesOpen"
                                    @click.away="subcategoriesOpen = false"
                                    class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition-colors cursor-pointer">
                                    <!-- Main Category Image -->
                                    @if($category->media)
                                        <div class="w-6 h-6 rounded-full overflow-hidden bg-white ring-2 ring-blue-200 flex-shrink-0">
                                            <img 
                                                src="{{ $category->getThumbnailUrl() }}" 
                                                alt="{{ $category->name }}"
                                                class="w-full h-full object-cover"
                                            >
                                        </div>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 ring-2 ring-blue-200 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="font-semibold">{{ $subcategoriesCount }}</span>
                                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': subcategoriesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div 
                                    x-show="subcategoriesOpen"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform scale-100"
                                    x-transition:leave-end="opacity-0 transform scale-95"
                                    class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto"
                                    style="display: none;">
                                    
                                    <!-- Dropdown Header -->
                                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 rounded-t-lg">
                                        <h3 class="text-sm font-semibold text-gray-900">Subcategories</h3>
                                        <p class="text-xs text-gray-500 mt-0.5">Browse {{ $subcategoriesCount }} subcategories</p>
                                    </div>

                                    <!-- Subcategories List -->
                                    <div class="py-2">
                                        @foreach($category->activeChildren as $child)
                                            @php
                                                $productCount = $child->products->count();
                                            @endphp
                                            <a href="{{ route('categories.show', $child->slug) }}" 
                                               class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 transition-colors group">
                                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                                    <!-- Category Icon/Image -->
                                                    @if($child->image)
                                                        <div class="w-10 h-10 flex-shrink-0 rounded-md overflow-hidden bg-gray-100">
                                                            <img 
                                                                src="{{ asset('storage/' . $child->image) }}" 
                                                                alt="{{ $child->name }}"
                                                                class="w-full h-full object-cover"
                                                            >
                                                        </div>
                                                    @else
                                                        <div class="w-10 h-10 flex-shrink-0 rounded-md bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Category Name -->
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 group-hover:text-green-600 transition-colors truncate">
                                                            {{ $child->name }}
                                                        </p>
                                                        @if($productCount > 0)
                                                            <p class="text-xs text-gray-500">
                                                                {{ $productCount }} {{ Str::plural('item', $productCount) }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Arrow Icon -->
                                                <svg class="w-4 h-4 text-gray-400 group-hover:text-green-600 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar Filters -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <!-- Mobile Filter Toggle -->
                    <button wire:click="$toggle('showFilters')" 
                            class="lg:hidden w-full mb-4 px-4 py-2 bg-green-600 text-white rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filters
                    </button>

                    <div class="space-y-6 {{ $showFilters ? '' : 'hidden lg:block' }}">
                        
                        <!-- Categories (hide on category pages) -->
                        @if($pageType !== 'category')
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">
                                Categories 
                                <span class="text-xs text-gray-500">(Selected: {{ count($selectedCategories) }})</span>
                            </h3>
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach($categories as $cat)
                                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                    <input type="checkbox" 
                                           value="{{ $cat->id }}"
                                           wire:model.live="selectedCategories"
                                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $cat->name }}</span>
                                    <span class="ml-auto text-xs text-gray-500">({{ $cat->products_count }})</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Brands (hide on brand pages) -->
                        @if($pageType !== 'brand')
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Brands</h3>
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach($brands as $brand)
                                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                    <input type="checkbox" 
                                           value="{{ $brand->id }}"
                                           wire:model.live="selectedBrands"
                                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $brand->name }}</span>
                                    <span class="ml-auto text-xs text-gray-500">({{ $brand->products_count }})</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Price Range -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Price Range</h3>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <input type="number" 
                                           wire:model.live.debounce.500ms="minPrice"
                                           placeholder="Min"
                                           min="0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <span class="text-gray-500">-</span>
                                    <input type="number" 
                                           wire:model.live.debounce.500ms="maxPrice"
                                           placeholder="Max"
                                           min="0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                </div>
                                @if($priceRange)
                                <p class="text-xs text-gray-500">
                                    Range: {{ currency_format($priceRange->min_price) }} - {{ currency_format($priceRange->max_price) }}
                                </p>
                                @endif
                            </div>
                        </div>

                        <!-- Rating -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Rating</h3>
                            <div class="space-y-2">
                                @for($i = 5; $i >= 1; $i--)
                                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                    <input type="radio" 
                                           name="rating"
                                           value="{{ $i }}"
                                           wire:model.live="minRating"
                                           class="border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 flex items-center">
                                        @for($j = 1; $j <= 5; $j++)
                                        <svg class="w-4 h-4 {{ $j <= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        @endfor
                                        <span class="ml-1 text-sm text-gray-600">& Up</span>
                                    </span>
                                </label>
                                @endfor
                            </div>
                        </div>

                        <!-- Availability -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Availability</h3>
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="checkbox" 
                                       wire:model.live="inStock"
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700">In Stock Only</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="checkbox" 
                                       wire:model.live="onSale"
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700">On Sale</span>
                            </label>
                        </div>

                        <!-- Clear Filters -->
                        <button wire:click="clearFilters" 
                                class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Clear All Filters
                        </button>
                    </div>

                    <!-- Loading Indicator -->
                    <div wire:loading class="mt-4 text-center">
                        <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg">
                            <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading...
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 relative">
                @include('livewire.shop.partials.header')
                
                <!-- Products Container with Loading State -->
                <div class="relative">
                    <!-- Loading Overlay with Blur (Only for filter changes) -->
                    <div wire:loading.delay.longest
                         wire:target="selectedCategories,selectedBrands,minPrice,maxPrice,minRating,inStock,onSale,sortBy,perPage"
                         class="fixed inset-0 z-50 overflow-y-auto"
                         style="display: none;"
                         x-show="true"
                         x-cloak>
                        
                        <!-- Backdrop with blur -->
                        <div class="fixed inset-0 transition-all duration-300" 
                             style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"></div>
                        
                        <!-- Modal Content -->
                        <div class="flex items-center justify-center min-h-screen p-4">
                            <div class="relative rounded-lg shadow-2xl max-w-md w-full p-6 border border-gray-200"
                                 style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                                
                                <!-- Loading Icon -->
                                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                                    <svg class="animate-spin w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                
                                <!-- Title -->
                                <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Loading Products</h3>
                                
                                <!-- Message -->
                                <p class="text-sm text-gray-600 text-center mb-4">Please wait while we update the results...</p>
                                
                                <!-- Progress Bar -->
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-green-600 h-2 rounded-full animate-progress"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Products -->
                    @include('livewire.shop.partials.products')
                </div>
            </main>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes progress {
        0% {
            width: 0%;
        }
        50% {
            width: 70%;
        }
        100% {
            width: 100%;
        }
    }
    
    .animate-progress {
        animation: progress 1.5s ease-in-out infinite;
    }

    /* Hide scrollbar for subcategories */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    
    .scrollbar-hide::-webkit-scrollbar {
        display: none;  /* Chrome, Safari and Opera */
    }
</style>
@endpush
