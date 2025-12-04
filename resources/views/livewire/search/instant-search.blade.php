<div class="relative w-full max-w-2xl" x-data="{ 
    focused: false,
    showResults: @entangle('showResults'),
    query: @entangle('query')
}" x-on:click.away="$wire.hideResults()">
    
    <!-- Search Input -->
    <div class="relative">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="query"
            x-on:focus="focused = true"
            x-on:blur="setTimeout(() => focused = false, 200)"
            placeholder="Search all of iHerb - Products, Brands, Categories..." 
            class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
            autocomplete="off"
        >
        
        <!-- Search Button -->
        <button 
            wire:click="search"
            class="absolute right-2 top-1/2 -translate-y-1/2 bg-green-600 text-white p-2 rounded-md hover:bg-green-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </button>

        <!-- Clear Button -->
        <button 
            x-show="query.length > 0"
            wire:click="clearSearch"
            class="absolute right-12 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
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

    <!-- Search Results Dropdown -->
    <div 
        x-show="showResults && (query.length >= 2 || focused)"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-hidden"
        style="display: none;">
        
        <div class="max-h-96 overflow-y-auto">
            <!-- Loading State -->
            <div wire:loading wire:target="query" class="p-4 text-center">
                <div class="inline-flex items-center text-gray-600">
                    <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Searching...
                </div>
            </div>

            <div wire:loading.remove wire:target="query">
                <!-- When there's a query -->
                <div x-show="query.length >= 2">
                    <!-- Product Suggestions -->
                    @if($productSuggestions->count() > 0)
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-3 bg-gray-50">
                                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Products
                                </h3>
                            </div>
                            <div class="py-2">
                                @foreach($productSuggestions as $product)
                                    <button 
                                        wire:click="goToProduct('{{ $product->slug }}')"
                                        class="w-full px-4 py-3 hover:bg-gray-50 transition-colors text-left flex items-center space-x-3">
                                        
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
                                                    <span class="text-xs text-green-600 font-semibold">
                                                        {{ currency_format($product->defaultVariant->sale_price ?? $product->defaultVariant->price) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Arrow -->
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Category Suggestions -->
                    @if($categorySuggestions->count() > 0)
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-3 bg-gray-50">
                                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Categories
                                </h3>
                            </div>
                            <div class="py-2">
                                @foreach($categorySuggestions as $category)
                                    <button 
                                        wire:click="goToCategory('{{ $category->slug }}')"
                                        class="w-full px-4 py-2 hover:bg-gray-50 transition-colors text-left flex items-center justify-between">
                                        <span class="text-sm text-gray-900">{{ $category->name }}</span>
                                        <span class="text-xs text-gray-500">({{ $category->products_count }} items)</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Brand Suggestions -->
                    @if($brandSuggestions->count() > 0)
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-3 bg-gray-50">
                                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Brands
                                </h3>
                            </div>
                            <div class="py-2">
                                @foreach($brandSuggestions as $brand)
                                    <button 
                                        wire:click="goToBrand('{{ $brand->slug }}')"
                                        class="w-full px-4 py-2 hover:bg-gray-50 transition-colors text-left flex items-center justify-between">
                                        <span class="text-sm text-gray-900">{{ $brand->name }}</span>
                                        <span class="text-xs text-gray-500">({{ $brand->products_count }} items)</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- No Results -->
                    @if($productSuggestions->count() === 0 && $categorySuggestions->count() === 0 && $brandSuggestions->count() === 0)
                        <div class="px-4 py-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No results found</h3>
                            <p class="text-sm text-gray-600 mb-4">Try searching with different keywords</p>
                            <button 
                                wire:click="search"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search all products
                            </button>
                        </div>
                    @endif

                    <!-- View All Results -->
                    @if($productSuggestions->count() > 0 || $categorySuggestions->count() > 0 || $brandSuggestions->count() > 0)
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                            <button 
                                wire:click="search"
                                class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                View all results for "{{ $query }}"
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Popular Searches (when no query) -->
                <div x-show="query.length < 2 && focused">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Popular Searches
                        </h3>
                    </div>
                    <div class="py-2">
                        @foreach($popularSearches as $term)
                            <button 
                                wire:click="searchPopular('{{ $term }}')"
                                class="w-full px-4 py-2 hover:bg-gray-50 transition-colors text-left flex items-center">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-900">{{ $term }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
