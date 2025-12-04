<div class="relative" x-data="{ showDropdown: false }" @click.away="showDropdown = false">
    <!-- Search Input -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search"
            @focus="showDropdown = true; $wire.showProducts()"
            @keydown="showDropdown = true"
            placeholder="Search products by name or SKU..."
            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
        >
        @if($search)
        <button 
            type="button"
            wire:click="$set('search', '')"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        @endif
    </div>

    <!-- Dropdown Results -->
    <div 
        x-show="showDropdown"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute z-50 mt-2 w-full bg-white rounded-lg shadow-xl border border-gray-200 max-h-96 overflow-y-auto"
        style="display: none;"
    >
        <!-- Loading State -->
        <div wire:loading class="p-8 text-center">
            <svg class="animate-spin h-8 w-8 mx-auto text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm text-gray-500 mt-2">Loading products...</p>
        </div>

        <!-- Products List -->
        <div wire:loading.remove class="p-2">
            @forelse($this->products as $product)
                @php
                    $defaultVariant = $product->variants->where('is_default', true)->first() 
                                   ?? $product->variants->first();
                    $price = $defaultVariant?->price ?? 0;
                    $salePrice = $defaultVariant?->sale_price;
                    $stock = $defaultVariant?->stock_quantity ?? 0;
                @endphp
                
                <div class="hover:bg-gray-50 rounded-lg p-3 cursor-pointer transition-colors border-b border-gray-100 last:border-b-0">
                    <!-- Simple Product or Default Variant -->
                    <div 
                        wire:click="selectProduct({{ $product->id }}, {{ $defaultVariant?->id ?? 'null' }})"
                        @click="showDropdown = false"
                        class="flex items-center space-x-3"
                    >
                        <!-- Product Image -->
                        <div class="flex-shrink-0">
                            @php
                                // Use media library system
                                $imageUrl = $product->getPrimaryThumbnailUrl();
                                
                                // Fallback to variant image (for old data)
                                if (!$imageUrl && $defaultVariant && $defaultVariant->image) {
                                    $imageUrl = asset('storage/' . $defaultVariant->image);
                                }
                            @endphp
                            
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-12 h-12 object-cover rounded-lg border border-gray-200"
                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center\'><svg class=\'w-6 h-6 text-gray-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $product->name }}</h4>
                                    <div class="flex items-center space-x-2 mt-1">
                                        @if($product->brand)
                                            <span class="text-xs text-gray-500">{{ $product->brand->name }}</span>
                                        @endif
                                        @if($product->category)
                                            <span class="text-xs text-gray-400">• {{ $product->category->name }}</span>
                                        @endif
                                    </div>
                                    @if($defaultVariant && $defaultVariant->sku)
                                        <p class="text-xs text-gray-500 mt-0.5">SKU: {{ $defaultVariant->sku }}</p>
                                    @endif
                                </div>
                                
                                <!-- Price & Stock -->
                                <div class="text-right ml-3">
                                    <div class="flex items-center space-x-1">
                                        @if($salePrice)
                                            <span class="text-sm font-bold text-green-600">৳{{ number_format($salePrice, 2) }}</span>
                                            <span class="text-xs text-gray-400 line-through">৳{{ number_format($price, 2) }}</span>
                                        @else
                                            <span class="text-sm font-bold text-gray-900">৳{{ number_format($price, 2) }}</span>
                                        @endif
                                    </div>
                                    @if($stock > 0)
                                        <span class="text-xs {{ $stock > 10 ? 'text-green-600' : 'text-yellow-600' }}">Stock: {{ $stock }}</span>
                                    @else
                                        <span class="text-xs text-red-600">Out of Stock</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Variable Product - Show Variants -->
                    @if($product->product_type === 'variable' && $product->variants->count() > 1)
                        <div class="mt-2 ml-15 space-y-1">
                            <p class="text-xs font-medium text-gray-600 mb-1">Available Variants:</p>
                            @foreach($product->variants as $variant)
                                @if($variant->id !== $defaultVariant?->id)
                                    <div 
                                        wire:click.stop="selectProduct({{ $product->id }}, {{ $variant->id }})"
                                        @click="showDropdown = false"
                                        class="flex items-center justify-between p-2 hover:bg-blue-50 rounded border border-gray-200 hover:border-blue-300 transition-colors"
                                    >
                                        <div class="flex-1">
                                            <span class="text-xs font-medium text-gray-700">{{ $variant->name }}</span>
                                            @if($variant->sku)
                                                <span class="text-xs text-gray-500 ml-2">SKU: {{ $variant->sku }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($variant->sale_price)
                                                <span class="text-xs font-semibold text-green-600">৳{{ number_format($variant->sale_price, 2) }}</span>
                                                <span class="text-xs text-gray-400 line-through">৳{{ number_format($variant->price, 2) }}</span>
                                            @else
                                                <span class="text-xs font-semibold text-gray-900">৳{{ number_format($variant->price, 2) }}</span>
                                            @endif
                                            @if($variant->stock_quantity > 0)
                                                <span class="text-xs text-green-600">({{ $variant->stock_quantity }})</span>
                                            @else
                                                <span class="text-xs text-red-600">(Out)</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm">No products found</p>
                    @if($search)
                        <p class="text-xs text-gray-400 mt-1">Try a different search term</p>
                    @endif
                </div>
            @endforelse
        </div>
        <!-- End Products List -->
    </div>

    <!-- Loading Indicator in Search Box -->
    <div wire:loading class="absolute right-3 top-3">
        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- Hidden inputs for form submission -->
    @if($selectedProduct)
        <input type="hidden" name="product_id" value="{{ $productId }}">
        @if($variantId)
            <input type="hidden" name="variant_id" value="{{ $variantId }}">
        @endif
        
        <!-- Selected Product Display -->
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @php
                        // Use media library system
                        $selectedImageUrl = $selectedProduct->getPrimaryThumbnailUrl();
                    @endphp
                    
                    @if($selectedImageUrl)
                        <img src="{{ $selectedImageUrl }}" alt="{{ $selectedProduct->name }}" class="w-12 h-12 object-cover rounded-lg border border-blue-300">
                    @else
                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">{{ $productName }}</h4>
                        @if($variantName)
                            <p class="text-xs text-gray-600">{{ $variantName }}</p>
                        @endif
                        @if($sku)
                            <p class="text-xs text-gray-500">SKU: {{ $sku }}</p>
                        @endif
                        <p class="text-xs font-medium {{ $currentStock > 10 ? 'text-green-600' : ($currentStock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                            Current Stock: {{ $currentStock }} units
                        </p>
                    </div>
                </div>
                
                <button 
                    type="button"
                    wire:click="resetSelector"
                    class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif
</div>
