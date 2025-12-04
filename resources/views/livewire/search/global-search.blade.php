<div class="relative w-full max-w-3xl" x-data="{ 
    focused: false,
    showResults: @entangle('showResults'),
    query: @entangle('query'),
    selectedTab: @entangle('selectedTab'),
    maintainFocus: false
}" 
x-on:click.away="if (!maintainFocus) $wire.hideResults()"
@maintain-focus.window="
    maintainFocus = true;
    $nextTick(() => {
        $refs.searchInput.focus();
        focused = true;
        setTimeout(() => { maintainFocus = false; }, 300);
    });
">
    
    <!-- Search Input Container -->
    <div class="relative">
        <!-- Main Search Input -->
        <div class="relative bg-white rounded-full border border-gray-300 focus-within:border-gray-400 lg:focus-within:border-gray-400 transition-all duration-200">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="query"
                x-ref="searchInput"
                x-on:focus="focused = true"
                x-on:blur="setTimeout(() => { if (!maintainFocus) focused = false; }, 200)"
                placeholder="Search products, brands, categories..." 
                class="w-full px-4 py-2 lg:py-3 pr-10 lg:pr-16 text-sm lg:text-lg border-0 rounded-full focus:outline-none focus:ring-0 bg-transparent"
                autocomplete="off"
            >
            
            <!-- Search Button -->
            <button 
                wire:click="search"
                class="absolute right-1 lg:right-2 top-1/2 -translate-y-1/2 bg-transparent hover:bg-gray-100 text-gray-500 hover:text-gray-600 p-1.5 rounded-full transition-colors flex items-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>

            <!-- Clear Button (Desktop only) -->
            <button 
                x-show="query.length > 0"
                wire:click="clearSearch"
                class="hidden lg:block absolute right-16 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-75"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-75">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Desktop Search Suggestions Dropdown -->
        <div 
            x-show="query.length >= 1 && (focused || maintainFocus)"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="hidden lg:block absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-2xl border border-gray-200 z-50 overflow-hidden max-w-3xl"
            style="display: none;"
            
            <!-- Close Button (Desktop) -->
            <div class="absolute top-2 right-2 z-10">
                <button 
                    @click="focused = false; $wire.hideResults()"
                    class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Search Suggestions List (Left Side) -->
            <div class="flex">
                <!-- Suggestions Column -->
                <div class="w-1/3 bg-gray-50 border-r border-gray-200">
                    <div class="p-3 border-b border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900">Search Suggestions</h4>
                    </div>
                    
                    <!-- Loading State -->
                    <div wire:loading wire:target="query" class="p-4 text-center">
                        <div class="inline-flex items-center text-gray-600">
                            <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm">Searching...</span>
                        </div>
                    </div>

                    <div wire:loading.remove wire:target="query" class="max-h-80 overflow-y-auto">
                        <!-- Auto-complete Suggestions -->
                        @if($searchSuggestions->count() > 0)
                            @foreach($searchSuggestions as $suggestion)
                                <button 
                                    wire:click="updateQuery('{{ $suggestion }}')"
                                    class="w-full px-4 py-2 text-left hover:bg-gray-100 transition-colors flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span class="text-gray-900">{{ $suggestion }}</span>
                                </button>
                            @endforeach
                        @endif

                        <!-- Popular Searches (when no suggestions) -->
                        @if($searchSuggestions->count() === 0 && strlen($query) < 2)
                            <div class="p-3 border-b border-gray-200 bg-white">
                                <h5 class="text-xs font-medium text-gray-700 mb-2">Popular Searches</h5>
                            </div>
                            @foreach(array_slice($popularSearches, 0, 6) as $popular)
                                <button 
                                    wire:click="updateQuery('{{ $popular }}')"
                                    class="w-full px-4 py-2 text-left hover:bg-gray-100 transition-colors flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span class="text-gray-900">{{ $popular }}</span>
                                </button>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Results Column -->
                <div class="flex-1">
                    <div wire:loading.remove wire:target="query">
                        <!-- When there's a query -->
                        <div x-show="query.length >= 2">
                            <!-- Results Tabs -->
                            <div class="flex border-b border-gray-200 bg-gray-50">
                                <button 
                                    wire:click="setTab('all')"
                                    class="px-4 py-3 text-sm font-medium transition-colors border-b-2 text-orange-600 border-orange-500 bg-white">
                                    All Results ({{ $searchCounts['total'] }})
                                </button>
                            </div>

                            <!-- Results Content -->
                            <div class="max-h-96 overflow-y-auto">
                                <!-- All Results Tab -->
                                <div x-show="selectedTab === 'all'">
                                    <!-- Products Section -->
                                    @if($productSuggestions->count() > 0)
                                        <div class="border-b border-gray-100">
                                            <div class="px-4 py-3 bg-gray-50 flex items-center justify-between">
                                                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                    Products
                                                </h3>
                                            </div>
                                            <div class="py-2">
                                                @foreach($productSuggestions->take(3) as $product)
                                                    <a 
                                                        href="{{ route('products.show', $product->slug) }}"
                                                        class="block w-full px-4 py-3 hover:bg-gray-50 transition-colors text-left flex items-center space-x-3">
                                                        
                                                        <!-- Product Image -->
                                                        <div class="w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                                            @if($product->getPrimaryThumbnailUrl())
                                                                <img 
                                                                    src="{{ $product->getPrimaryThumbnailUrl() }}" 
                                                                    alt="{{ $product->name }}"
                                                                    class="w-full h-full object-cover"
                                                                >
                                                            @else
                                                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Product Info -->
                                                        <div class="flex-1 min-w-0">
                                                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</h4>
                                                            <div class="flex items-center space-x-2 mt-1">
                                                                @if($product->brand)
                                                                    <span class="text-xs text-gray-500">{{ $product->brand->name }}</span>
                                                                @endif
                                                                @if($product->defaultVariant)
                                                                    <span class="text-sm text-orange-600 font-semibold">
                                                                        {{ currency_format($product->defaultVariant->sale_price ?? $product->defaultVariant->price) }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Blog Posts Section -->
                                    @if($blogSuggestions->count() > 0)
                                        <div class="border-b border-gray-100">
                                            <div class="px-4 py-3 bg-gray-50 flex items-center justify-between">
                                                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                                    </svg>
                                                    Blog Posts
                                                </h3>
                                            </div>
                                            <div class="py-2">
                                                @foreach($blogSuggestions->take(3) as $blog)
                                                    <a 
                                                        href="{{ route('products.show', $blog->slug) }}"
                                                        class="block w-full px-4 py-3 hover:bg-gray-50 transition-colors text-left">
                                                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $blog->title }}</h4>
                                                        <div class="flex items-center space-x-2 mt-1">
                                                            <span class="text-xs text-gray-500">{{ $blog->created_at->format('M d, Y') }}</span>
                                                            @if($blog->categories->count() > 0)
                                                                <span class="text-xs text-blue-600">{{ $blog->categories->first()->name }}</span>
                                                            @endif
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Categories & Brands -->
                                    @if($categorySuggestions->count() > 0 || $brandSuggestions->count() > 0)
                                        <div class="px-4 py-3">
                                            <div class="grid grid-cols-2 gap-4">
                                                <!-- Categories -->
                                                @if($categorySuggestions->count() > 0)
                                                    <div>
                                                        <h4 class="text-xs font-medium text-gray-700 mb-2">Categories</h4>
                                                        @foreach($categorySuggestions->take(2) as $category)
                                                            <a 
                                                                href="{{ route('categories.show', $category->slug) }}"
                                                                class="block w-full text-left px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                                                                <span class="text-sm text-gray-900">{{ $category->name }}</span>
                                                                <span class="text-xs text-gray-500 ml-1">({{ $category->products_count }})</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <!-- Brands -->
                                                @if($brandSuggestions->count() > 0)
                                                    <div>
                                                        <h4 class="text-xs font-medium text-gray-700 mb-2">Brands</h4>
                                                        @foreach($brandSuggestions->take(2) as $brand)
                                                            <a 
                                                                href="{{ route('brands.show', $brand->slug) }}"
                                                                class="block w-full text-left px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                                                                <span class="text-sm text-gray-900">{{ $brand->name }}</span>
                                                                <span class="text-xs text-gray-500 ml-1">({{ $brand->products_count }})</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Products Only Tab -->
                                <div x-show="selectedTab === 'products'">
                                    @if($productSuggestions->count() > 0)
                                        <div class="py-2">
                                            @foreach($productSuggestions as $product)
                                                <a 
                                                    href="{{ route('products.show', $product->slug) }}"
                                                    class="block w-full px-4 py-3 hover:bg-gray-50 transition-colors text-left flex items-center space-x-3">
                                                    
                                                    <div class="w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                                        @if($product->getPrimaryThumbnailUrl())
                                                            <img 
                                                                src="{{ $product->getPrimaryThumbnailUrl() }}" 
                                                                alt="{{ $product->name }}"
                                                                class="w-full h-full object-cover"
                                                            >
                                                        @else
                                                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</h4>
                                                        <div class="flex items-center space-x-2 mt-1">
                                                            @if($product->brand)
                                                                <span class="text-xs text-gray-500">{{ $product->brand->name }}</span>
                                                            @endif
                                                            @if($product->defaultVariant)
                                                                <span class="text-sm text-orange-600 font-semibold">
                                                                    {{ currency_format($product->defaultVariant->sale_price ?? $product->defaultVariant->price) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Blogs Only Tab -->
                                <div x-show="selectedTab === 'blogs'">
                                    @if($blogSuggestions->count() > 0)
                                        <div class="py-2">
                                            @foreach($blogSuggestions as $blog)
                                                <a 
                                                    href="{{ route('products.show', $blog->slug) }}"
                                                    class="block w-full px-4 py-3 hover:bg-gray-50 transition-colors text-left">
                                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ $blog->title }}</h4>
                                                    @if($blog->excerpt)
                                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ Str::limit($blog->excerpt, 100) }}</p>
                                                    @endif
                                                    <div class="flex items-center space-x-2 mt-2">
                                                        <span class="text-xs text-gray-500">{{ $blog->created_at->format('M d, Y') }}</span>
                                                        @if($blog->categories->count() > 0)
                                                            <span class="text-xs text-blue-600">{{ $blog->categories->first()->name }}</span>
                                                        @endif
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <!-- No Results -->
                        @if($searchCounts['total'] === 0 && strlen($query) >= 2)
                            <div class="px-6 py-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No results found</h3>
                                <p class="text-sm text-gray-600 mb-4">Try different keywords or browse our categories</p>
                                <div class="flex justify-center space-x-3">
                                    <a href="{{ route('shop') }}" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                        Browse Products
                                    </a>
                                    <a href="{{ route('blog.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                        Browse Blog
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Search Suggestions Dropdown -->
        <div 
            x-show="query.length >= 1 && (focused || maintainFocus)"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="lg:hidden absolute top-full left-0 mt-1 bg-white rounded-lg shadow-2xl border border-gray-200 z-50 overflow-hidden w-screen"
            style="display: none; margin-left: calc(-61vw + 50%);"
            
            <!-- Close Button (Mobile) -->
            <div class="absolute top-2 right-2 z-10">
                <button 
                    @click="focused = false; $wire.hideResults()"
                    class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Search Suggestions List (Left Side) -->
            <div class="flex">
                <!-- Suggestions Column -->
                <div class="w-1/3 bg-gray-50 border-r border-gray-200">
                    <div class="p-3 border-b border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900">Search Suggestions</h4>
                    </div>
                    
                    <!-- Loading State -->
                    <div wire:loading wire:target="query" class="p-4 text-center">
                        <div class="inline-flex items-center text-gray-600">
                            <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm">Searching...</span>
                        </div>
                    </div>

                    <div wire:loading.remove wire:target="query" class="max-h-80 overflow-y-auto">
                        <!-- Auto-complete Suggestions -->
                        @if($searchSuggestions->count() > 0)
                            @foreach($searchSuggestions as $suggestion)
                                <button 
                                    wire:click="updateQuery('{{ $suggestion }}')"
                                    class="w-full px-4 py-2 text-left hover:bg-gray-100 transition-colors flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span class="text-gray-900">{{ $suggestion }}</span>
                                </button>
                            @endforeach
                        @endif

                        <!-- Popular Searches (when no suggestions) -->
                        @if($searchSuggestions->count() === 0 && strlen($query) < 2)
                            <div class="p-3 border-b border-gray-200 bg-white">
                                <h5 class="text-xs font-medium text-gray-700 mb-2">Popular Searches</h5>
                            </div>
                            @foreach(array_slice($popularSearches, 0, 6) as $popular)
                                <button 
                                    wire:click="updateQuery('{{ $popular }}')"
                                    class="w-full px-4 py-2 text-left hover:bg-gray-100 transition-colors flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span class="text-gray-900">{{ $popular }}</span>
                                </button>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Results Column -->
                <div class="flex-1">
                    <div wire:loading.remove wire:target="query">
                        <!-- When there's a query -->
                        <div x-show="query.length >= 2">
                            <!-- Results Tabs -->
                            <div class="flex border-b border-gray-200 bg-gray-50">
                                <button 
                                    wire:click="setTab('all')"
                                    class="px-4 py-3 text-sm font-medium transition-colors border-b-2 text-orange-600 border-orange-500 bg-white">
                                    All Results ({{ $searchCounts['total'] }})
                                </button>
                            </div>

                            <!-- Results Content -->
                            <div class="max-h-96 overflow-y-auto">
                                <!-- All Results Tab -->
                                <div x-show="selectedTab === 'all'">
                                    <!-- Products Section -->
                                    @if($productSuggestions->count() > 0)
                                        <div class="border-b border-gray-100">
                                            <div class="px-4 py-3 bg-gray-50 flex items-center justify-between">
                                                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                    Products
                                                </h3>
                                            </div>
                                            <div class="py-2">
                                                @foreach($productSuggestions->take(3) as $product)
                                                    <a 
                                                        href="{{ route('products.show', $product->slug) }}"
                                                        class="block w-full px-4 py-3 hover:bg-gray-50 transition-colors text-left flex items-center space-x-3">
                                                        
                                                        <!-- Product Image -->
                                                        <div class="w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                                            @if($product->getPrimaryThumbnailUrl())
                                                                <img 
                                                                    src="{{ $product->getPrimaryThumbnailUrl() }}" 
                                                                    alt="{{ $product->name }}"
                                                                    class="w-full h-full object-cover"
                                                                >
                                                            @else
                                                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Product Info -->
                                                        <div class="flex-1 min-w-0">
                                                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</h4>
                                                            <div class="flex items-center space-x-2 mt-1">
                                                                @if($product->brand)
                                                                    <span class="text-xs text-gray-500">{{ $product->brand->name }}</span>
                                                                @endif
                                                                @if($product->defaultVariant)
                                                                    <span class="text-sm text-orange-600 font-semibold">
                                                                        {{ currency_format($product->defaultVariant->sale_price ?? $product->defaultVariant->price) }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Blog Posts Section -->
                                    @if($blogSuggestions->count() > 0)
                                        <div class="border-b border-gray-100">
                                            <div class="px-4 py-3 bg-gray-50 flex items-center justify-between">
                                                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                                    </svg>
                                                    Blog Posts
                                                </h3>
                                            </div>
                                            <div class="py-2">
                                                @foreach($blogSuggestions->take(3) as $blog)
                                                    <a 
                                                        href="{{ route('products.show', $blog->slug) }}"
                                                        class="block w-full px-4 py-3 hover:bg-gray-50 transition-colors text-left">
                                                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $blog->title }}</h4>
                                                        <div class="flex items-center space-x-2 mt-1">
                                                            <span class="text-xs text-gray-500">{{ $blog->created_at->format('M d, Y') }}</span>
                                                            @if($blog->categories->count() > 0)
                                                                <span class="text-xs text-blue-600">{{ $blog->categories->first()->name }}</span>
                                                            @endif
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Categories & Brands -->
                                    @if($categorySuggestions->count() > 0 || $brandSuggestions->count() > 0)
                                        <div class="px-4 py-3">
                                            <div class="grid grid-cols-2 gap-4">
                                                <!-- Categories -->
                                                @if($categorySuggestions->count() > 0)
                                                    <div>
                                                        <h4 class="text-xs font-medium text-gray-700 mb-2">Categories</h4>
                                                        @foreach($categorySuggestions->take(2) as $category)
                                                            <a 
                                                                href="{{ route('categories.show', $category->slug) }}"
                                                                class="block w-full text-left px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                                                                <span class="text-sm text-gray-900">{{ $category->name }}</span>
                                                                <span class="text-xs text-gray-500 ml-1">({{ $category->products_count }})</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <!-- Brands -->
                                                @if($brandSuggestions->count() > 0)
                                                    <div>
                                                        <h4 class="text-xs font-medium text-gray-700 mb-2">Brands</h4>
                                                        @foreach($brandSuggestions->take(2) as $brand)
                                                            <a 
                                                                href="{{ route('brands.show', $brand->slug) }}"
                                                                class="block w-full text-left px-2 py-1 rounded hover:bg-gray-100 transition-colors">
                                                                <span class="text-sm text-gray-900">{{ $brand->name }}</span>
                                                                <span class="text-xs text-gray-500 ml-1">({{ $brand->products_count }})</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Products Only Tab -->
                                <div x-show="selectedTab === 'products'">
                                    @if($productSuggestions->count() > 0)
                                        <div class="py-2">
                                            @foreach($productSuggestions as $product)
                                                <a 
                                                    href="{{ route('products.show', $product->slug) }}"
                                                    class="block w-full px-4 py-3 hover:bg-gray-50 transition-colors text-left flex items-center space-x-3">
                                                    
                                                    <div class="w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                                        @if($product->getPrimaryThumbnailUrl())
                                                            <img 
                                                                src="{{ $product->getPrimaryThumbnailUrl() }}" 
                                                                alt="{{ $product->name }}"
                                                                class="w-full h-full object-cover"
                                                            >
                                                        @else
                                                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</h4>
                                                        <div class="flex items-center space-x-2 mt-1">
                                                            @if($product->brand)
                                                                <span class="text-xs text-gray-500">{{ $product->brand->name }}</span>
                                                            @endif
                                                            @if($product->defaultVariant)
                                                                <span class="text-sm text-orange-600 font-semibold">
                                                                    {{ currency_format($product->defaultVariant->sale_price ?? $product->defaultVariant->price) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Blogs Only Tab -->
                                <div x-show="selectedTab === 'blogs'">
                                    @if($blogSuggestions->count() > 0)
                                        <div class="py-2">
                                            @foreach($blogSuggestions as $blog)
                                                <a 
                                                    href="{{ route('products.show', $blog->slug) }}"
                                                    class="block w-full px-4 py-3 hover:bg-gray-50 transition-colors text-left">
                                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ $blog->title }}</h4>
                                                    @if($blog->excerpt)
                                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ Str::limit($blog->excerpt, 100) }}</p>
                                                    @endif
                                                    <div class="flex items-center space-x-2 mt-2">
                                                        <span class="text-xs text-gray-500">{{ $blog->created_at->format('M d, Y') }}</span>
                                                        @if($blog->categories->count() > 0)
                                                            <span class="text-xs text-blue-600">{{ $blog->categories->first()->name }}</span>
                                                        @endif
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <!-- No Results -->
                        @if($searchCounts['total'] === 0 && strlen($query) >= 2)
                            <div class="px-6 py-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No results found</h3>
                                <p class="text-sm text-gray-600 mb-4">Try different keywords or browse our categories</p>
                                <div class="flex justify-center space-x-3">
                                    <a href="{{ route('shop') }}" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                        Browse Products
                                    </a>
                                    <a href="{{ route('blog.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                        Browse Blog
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
