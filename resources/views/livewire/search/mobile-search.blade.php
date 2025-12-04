<div>
    <!-- Inline Mobile Search Field -->
    <div class="relative w-full">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="query"
            placeholder="Search"
            class="w-full h-9 pl-4 pr-10 text-sm border border-gray-300 rounded-full bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
        >
        <button 
            wire:click="search"
            class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-gray-500 hover:text-green-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile Search Overlay -->
<div 
    x-data="{ isOpen: @entangle('isOpen'), maintainFocus: false }"
    x-show="isOpen"
    @maintain-focus-mobile.window="
        maintainFocus = true;
        $nextTick(() => {
            $refs.mobileSearchInput.focus();
            setTimeout(() => { maintainFocus = false; }, 300);
        });
    "
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 lg:hidden"
    style="display: none;">
    
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    
    <!-- Search Panel -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="-translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="-translate-y-full"
        class="relative bg-white w-full h-full overflow-y-auto">
        
        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-4 py-3">
            <div class="flex items-center space-x-3">
                <!-- Back Button -->
                <button 
                    wire:click="closeSearch"
                    class="flex items-center justify-center w-10 h-10 text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <!-- Search Input -->
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="query"
                        x-ref="mobileSearchInput"
                        placeholder="Search products, brands, categories..."
                        class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        autofocus
                    >
                    
                    <!-- Clear Button -->
                    @if($query)
                        <button 
                            wire:click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>
                
                <!-- Search Button -->
                <button 
                    wire:click="search"
                    class="flex items-center justify-center w-12 h-12 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-4">
            <!-- Loading State -->
            <div wire:loading wire:target="query" class="text-center py-8">
                <div class="inline-flex items-center text-gray-600">
                    <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Searching...
                </div>
            </div>

            <div wire:loading.remove wire:target="query">
                <!-- Trending Now Section (when no search query) -->
                @if(!$query)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Trending now</h3>
                        <div class="flex flex-wrap gap-2">
                            @forelse(\App\Models\TrendingProduct::with('product')->where('is_active', true)->orderBy('sort_order')->limit(8)->get() as $trendingProduct)
                                @if($trendingProduct->product && $trendingProduct->product->is_active)
                                    <a href="{{ route('products.show', $trendingProduct->product->slug) }}" 
                                       class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full text-sm text-gray-800 transition-colors">
                                        {{ $trendingProduct->product->name }}
                                    </a>
                                @endif
                            @empty
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-4 py-2 bg-gray-100 rounded-full text-sm text-gray-800">Vitamins</span>
                                    <span class="px-4 py-2 bg-gray-100 rounded-full text-sm text-gray-800">Supplements</span>
                                    <span class="px-4 py-2 bg-gray-100 rounded-full text-sm text-gray-800">Protein Powder</span>
                                    <span class="px-4 py-2 bg-gray-100 rounded-full text-sm text-gray-800">Omega-3</span>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Browse Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Browse</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('shop', ['on_sale' => 1]) }}" 
                               class="p-4 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-center">
                                <div class="text-2xl mb-1">🏷️</div>
                                <span class="text-sm font-medium text-gray-800">Sale Offers!</span>
                            </a>
                            <a href="{{ route('brands.index') }}" 
                               class="p-4 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-center">
                                <div class="text-2xl mb-1">⭐</div>
                                <span class="text-sm font-medium text-gray-800">Brands of the week</span>
                            </a>
                            <a href="{{ route('shop', ['on_sale' => 1]) }}" 
                               class="p-4 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-center">
                                <div class="text-2xl mb-1">💰</div>
                                <span class="text-sm font-medium text-gray-800">Sales & Offers</span>
                            </a>
                            <a href="{{ route('shop', ['sort' => 'latest']) }}" 
                               class="p-4 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-center">
                                <div class="text-2xl mb-1">✨</div>
                                <span class="text-sm font-medium text-gray-800">Try</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Quick Suggestions -->
                @if($query && $quickSuggestions->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Suggestions
                        </h3>
                        <div class="space-y-2">
                            @foreach($quickSuggestions as $suggestion)
                                <button 
                                    wire:click="updateQuery('{{ $suggestion }}')"
                                    class="w-full text-left px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span class="text-gray-900">{{ $suggestion }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Recent Searches -->
                @if(!$query && count($recentSearches) > 0)
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Recent Searches
                            </h3>
                            <button 
                                wire:click="clearRecentSearches"
                                class="text-sm text-red-600 hover:text-red-700 transition-colors">
                                Clear All
                            </button>
                        </div>
                        <div class="space-y-2">
                            @foreach($recentSearches as $recent)
                                <button 
                                    wire:click="updateQuery('{{ $recent }}')"
                                    class="w-full text-left px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-gray-900">{{ $recent }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Popular Searches -->
                @if(!$query)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Popular Searches
                        </h3>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($popularSearches as $popular)
                                <button 
                                    wire:click="updateQuery('{{ $popular }}')"
                                    class="px-4 py-3 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg hover:from-green-100 hover:to-blue-100 transition-colors text-center">
                                    <span class="text-gray-900 font-medium">{{ $popular }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="space-y-3">
                    <a href="{{ route('shop') }}" 
                       class="w-full flex items-center px-4 py-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="text-green-700 font-medium">Browse All Products</span>
                    </a>
                    
                    <a href="{{ route('categories.index') }}" 
                       class="w-full flex items-center px-4 py-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span class="text-blue-700 font-medium">Browse Categories</span>
                    </a>
                    
                    <a href="{{ route('brands.index') }}" 
                       class="w-full flex items-center px-4 py-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span class="text-purple-700 font-medium">Browse Brands</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
