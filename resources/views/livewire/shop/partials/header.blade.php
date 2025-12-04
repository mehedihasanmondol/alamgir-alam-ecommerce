<div class="bg-white rounded-lg shadow-sm p-4 mb-6">
    <!-- Search Bar (Mobile & Desktop) -->
    <div class="mb-4">
        <div class="relative">
            <input 
                type="text" 
                wire:model.live.debounce.500ms="search"
                placeholder="Search products..." 
                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
            >
            <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center space-x-1">
                @if($search)
                    <button 
                        wire:click="$set('search', '')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
                <div class="bg-green-600 text-white p-2 rounded-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Results Count -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @if($search)
                    Search Results
                @elseif($category)
                    {{ $category->name }}
                @elseif($brand)
                    {{ $brand->name }}
                @else
                    All Products
                @endif
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                @if($search)
                    Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results for "{{ $search }}"
                @else
                    Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
                @endif
            </p>
        </div>

        <!-- Sort & View Options -->
        <div class="flex items-center space-x-4">
            <!-- Sort By -->
            <select wire:model.live="sortBy"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="latest">Latest</option>
                <option value="popular">Most Popular</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
                <option value="name_asc">Name: A to Z</option>
                <option value="name_desc">Name: Z to A</option>
                <option value="rating">Highest Rated</option>
            </select>

            <!-- Per Page -->
            <select wire:model.live="perPage"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="12">12 per page</option>
                <option value="24">24 per page</option>
                <option value="48">48 per page</option>
                <option value="96">96 per page</option>
            </select>

            <!-- View Toggle -->
            <div class="hidden md:flex border border-gray-300 rounded-lg overflow-hidden">
                <button wire:click="setViewMode('grid')" 
                        class="px-3 py-2 transition {{ $viewMode === 'grid' ? 'bg-green-600 text-white' : 'bg-white text-gray-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button wire:click="setViewMode('list')" 
                        class="px-3 py-2 border-l border-gray-300 transition {{ $viewMode === 'list' ? 'bg-green-600 text-white' : 'bg-white text-gray-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Active Filters -->
    @if($this->hasActiveFilters())
    <div class="mt-4 flex flex-wrap gap-2">
        <span class="text-sm font-medium text-gray-700">Active Filters:</span>
        
        @if($search)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                Search: <span class="ml-1 font-medium">{{ $search }}</span>
                <button wire:click="clearFilter('search')" class="ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
        @endif

        @if(count($selectedCategories) > 0)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                {{ count($selectedCategories) }} Categories
                <button wire:click="clearFilter('selectedCategories')" class="ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
        @endif

        @if(count($selectedBrands) > 0)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                {{ count($selectedBrands) }} Brands
                <button wire:click="clearFilter('selectedBrands')" class="ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
        @endif

        @if($minPrice || $maxPrice)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                Price: @currencySymbol{{ $minPrice ?: '0' }} - @currencySymbol{{ $maxPrice ?: '∞' }}
                <button wire:click="$set('minPrice', ''); $set('maxPrice', '')" class="ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
        @endif

        @if($minRating)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                {{ $minRating }}★ & Up
                <button wire:click="clearFilter('minRating')" class="ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
        @endif

        @if($inStock)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                In Stock
                <button wire:click="clearFilter('inStock')" class="ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
        @endif

        @if($onSale)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                On Sale
                <button wire:click="clearFilter('onSale')" class="ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
        @endif
    </div>
    @endif
</div>
