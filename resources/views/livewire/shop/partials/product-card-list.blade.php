<div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition" wire:key="list-{{ $product->id }}">
    <div class="flex flex-col md:flex-row">
        <div class="relative md:w-48 aspect-square bg-gray-100 flex-shrink-0">
            <a href="{{ route('products.show', $product->slug) }}">
                <img src="{{ $imageUrl ?? asset('images/placeholder.svg') }}" 
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover"
                     loading="lazy">
            </a>
            
            @php
                $showStockInfo = $variant && $variant->shouldShowStock();
                $canAddToCart = $variant && $variant->canAddToCart();
            @endphp

            @if($showStockInfo && $variant && $variant->stock_quantity <= 0)
            <div class="absolute top-2 left-2 bg-gray-800 text-white text-xs font-bold px-2 py-1 rounded">
                Out of Stock
            </div>
            @elseif($hasDiscount)
            <div class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                SALE
            </div>
            @endif
        </div>

        <div class="flex-1 p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    @if($product->brand)
                    <p class="text-sm text-gray-600 mb-1">{{ $product->brand->name }}</p>
                    @endif
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        <a href="{{ route('products.show', $product->slug) }}" class="hover:text-green-600">
                            {{ $product->name }}
                        </a>
                    </h3>

                    @if($product->average_rating > 0)
                    <div class="flex items-center mb-3">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endfor
                        </div>
                        <span class="ml-2 text-sm text-gray-600">({{ $product->review_count ?? 0 }} reviews)</span>
                    </div>
                    @endif

                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                        {{ Str::limit(strip_tags($product->description), 150) }}
                    </p>

                    <div class="flex items-center space-x-4">
                        <div>
                            <div class="flex items-baseline space-x-2">
                                <span class="text-2xl font-bold text-gray-900">{{ currency_format($price) }}</span>
                                @if($hasDiscount)
                                <span class="text-lg text-gray-500 line-through">{{ currency_format($originalPrice) }}</span>
                                @endif
                            </div>
                            @if($hasDiscount)
                            <p class="text-sm text-green-600 font-medium">
                                Save {{ currency_format($originalPrice - $price) }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="ml-6 flex flex-col items-end space-y-2">
                    @php
                        $wishlist = session()->get('wishlist', []);
                        $wishlistKey = 'variant_' . ($variant->id ?? $product->id);
                        $isInWishlist = isset($wishlist[$wishlistKey]);
                    @endphp
                    <button onclick="this.querySelector('svg').classList.toggle('fill-red-500'); this.querySelector('svg').classList.toggle('text-red-500'); Livewire.dispatch('toggle-wishlist', { productId: {{ $product->id }}, variantId: {{ $variant->id ?? 'null' }} })"
                            class="p-2 hover:scale-110 transition-all duration-200"
                            title="{{ $isInWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
                        @if($isInWishlist)
                            <!-- Filled Heart (In Wishlist) -->
                            <svg class="w-6 h-6 text-red-500 fill-red-500" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        @else
                            <!-- Outline Heart (Not in Wishlist) -->
                            <svg class="w-6 h-6 text-gray-400 hover:text-red-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        @endif
                    </button>
                    
                    @if($variant && $canAddToCart)
                        @php
                            $cart = session()->get('cart', []);
                            $cartKey = 'variant_' . $variant->id;
                            $isInCart = isset($cart[$cartKey]);
                            $cartQuantity = $isInCart ? $cart[$cartKey]['quantity'] : 0;
                        @endphp
                        <button wire:click="addToCart({{ $product->id }}, {{ $variant->id }}, 1)"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50"
                                class="px-6 py-3 {{ $isInCart ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-lg transition disabled:opacity-50 flex items-center justify-center">
                            @if($isInCart)
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add More ({{ $cartQuantity }})
                            @else
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Add to Cart
                            @endif
                        </button>
                    @elseif($showStockInfo)
                    <button disabled class="px-6 py-3 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Out of Stock
                    </button>
                    @endif

                    <a href="{{ route('products.show', $product->slug) }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                        View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
