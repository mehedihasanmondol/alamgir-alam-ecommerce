<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <!-- Search Input -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search"
            placeholder="Search products by name or SKU..."
            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            @focus="open = true"
            @keydown.escape="open = false"
        >
        @if($search)
            <button 
                wire:click="$set('search', '')"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
                <svg class="h-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div wire:loading wire:target="search" class="absolute right-3 top-3">
        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- Search Results Dropdown -->
    <div 
        x-show="open"
        x-transition
        class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-xl border border-gray-200 max-h-96 overflow-y-auto"
    >
        @if($products->count() > 0)
            <div class="p-2">
                <p class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">
                    @if($search)
                        {{ $products->count() }} {{ $products->count() === 1 ? 'Product' : 'Products' }} Found
                    @else
                        Recent Products ({{ $products->count() }})
                    @endif
                </p>
                
                @foreach($products as $product)
                    <button
                        wire:click="selectProduct({{ $product->id }})"
                        @click="open = false"
                        class="w-full flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition text-left"
                    >
                        <!-- Product Image -->
                        @if($product->getPrimaryThumbnailUrl())
                            <img 
                                src="{{ $product->getPrimaryThumbnailUrl() }}" 
                                alt="{{ $product->name }}"
                                class="w-16 h-16 object-cover rounded-md flex-shrink-0"
                            >
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 truncate">{{ $product->name }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                @if($product->category)
                                    <span class="text-xs text-gray-500">{{ $product->category->name }}</span>
                                @endif
                                @if($product->brand)
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-500">{{ $product->brand->name }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                @if($product->sale_price)
                                    <span class="text-sm font-semibold text-green-600">
                                        {{ currency_format($product->sale_price) }}
                                    </span>
                                    <span class="text-xs text-gray-400 line-through">
                                        {{ currency_format($product->price) }}
                                    </span>
                                    <span class="text-xs font-medium text-red-600">
                                        -{{ $product->discount_percentage }}%
                                    </span>
                                @else
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ currency_format($product->price) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Add Icon -->
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </button>
                @endforeach
            </div>
        @elseif($search)
            <div class="p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-600 font-medium">No products found</p>
                <p class="text-sm text-gray-500 mt-1">Try searching with different keywords</p>
            </div>
        @endif
    </div>

    <!-- Help Text -->
    <p class="mt-2 text-sm text-gray-500">
        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        @if($search)
            Searching for "{{ $search }}"... Only products not in best sellers are shown.
        @else
            Click to see recent products or start typing to search by name/SKU.
        @endif
    </p>
</div>
