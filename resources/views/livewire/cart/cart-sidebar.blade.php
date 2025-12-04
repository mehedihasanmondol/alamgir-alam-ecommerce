<div>
    <!-- Backdrop with blur -->
    <div 
        x-show="$wire.isOpen"
        @click="$wire.hideCart()"
        x-transition:enter="transition-all duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-all duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40"
        style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: none;"
    ></div>

    <!-- Sidebar -->
    <div 
        x-show="$wire.isOpen"
        x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 bottom-0 w-full md:w-96 bg-white shadow-2xl z-50 flex flex-col"
        style="display: none;"
    >
        <!-- Header -->
        <div class="bg-green-600 text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-lg font-bold">Added to cart!</h2>
            </div>
            <button wire:click="hideCart" class="text-white hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto px-6 py-4">
            @if(count($cartItems) > 0)
                <div class="space-y-4">
                    @foreach($cartItems as $key => $item)
                        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                @if(isset($item['image']) && $item['image'])
                                    <img src="{{ $item['image'] }}" alt="{{ $item['product_name'] }}" class="w-24 h-24 object-cover rounded-lg border border-gray-200">
                                @else
                                    <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1 min-w-0">
                                <!-- Product Name -->
                                <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 mb-1">{{ $item['product_name'] }}</h3>
                                
                                <!-- Brand -->
                                @if(isset($item['brand']) && $item['brand'])
                                    <p class="text-xs text-gray-600 mb-1">By {{ $item['brand'] }}</p>
                                @endif
                                
                                <!-- Variant Info -->
                                @if(isset($item['variant_name']) && $item['variant_name'])
                                    <p class="text-xs text-gray-500 mb-1">
                                        <span class="font-medium">Variant:</span> {{ $item['variant_name'] }}
                                    </p>
                                @endif
                                
                                <!-- SKU -->
                                @if(isset($item['sku']) && $item['sku'])
                                    <p class="text-xs text-gray-500 mb-2">
                                        <span class="font-medium">SKU:</span> {{ $item['sku'] }}
                                    </p>
                                @endif
                                
                                <!-- Price Info -->
                                <div class="flex items-center space-x-2 mb-2">
                                    @if(isset($item['original_price']) && $item['original_price'] > $item['price'])
                                        <span class="text-xs text-gray-400 line-through">{{ currency_format($item['original_price']) }}</span>
                                        <span class="text-sm font-bold text-red-600">{{ currency_format($item['price']) }}</span>
                                        <span class="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-medium">
                                            {{ round((($item['original_price'] - $item['price']) / $item['original_price']) * 100) }}% OFF
                                        </span>
                                    @else
                                        <span class="text-sm font-bold text-gray-900">{{ currency_format($item['price']) }}</span>
                                    @endif
                                </div>
                                
                                <!-- Quantity Controls and Total -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <button 
                                            wire:click="updateItemQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                                            class="w-7 h-7 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                            @if($item['quantity'] <= 1) disabled @endif
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <span class="text-sm font-semibold w-10 text-center">{{ $item['quantity'] }}</span>
                                        <button 
                                            wire:click="updateItemQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                                            class="w-7 h-7 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-100 transition"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-base font-bold text-gray-900">{{ currency_format($item['price'] * $item['quantity']) }}</p>
                                    </div>
                                </div>

                                <!-- Stock Info and Remove Button -->
                                <div class="flex items-center justify-between mt-2">
                                    @php
                                        $showStockInfo = \App\Modules\Ecommerce\Product\Models\ProductVariant::isStockRestrictionEnabled();
                                    @endphp
                                    
                                    @if($showStockInfo && isset($item['stock_quantity']) && $item['stock_quantity'] <= 10)
                                        <p class="text-xs text-orange-600 font-medium">Only {{ $item['stock_quantity'] }} left</p>
                                    @else
                                        <div></div>
                                    @endif
                                    <button 
                                        wire:click="removeItem('{{ $key }}')"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium transition flex items-center"
                                    >
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Frequently Purchased Together Section -->
                @if(count($frequentlyPurchased) > 0)
                <div class="mt-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Frequently purchased together</h3>
                    <div class="space-y-3">
                        @foreach($frequentlyPurchased as $product)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <!-- Product Image -->
                            <a href="{{ route('products.show', $product['slug']) }}" class="flex-shrink-0">
                                @if($product['image'])
                                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-16 h-16 object-cover rounded border border-gray-200">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </a>
                            
                            <!-- Product Info -->
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('products.show', $product['slug']) }}" class="text-xs font-medium text-gray-900 hover:text-blue-600 line-clamp-2 block mb-1">
                                    {{ $product['name'] }}
                                </a>
                                
                                @if($product['brand'])
                                    <p class="text-xs text-gray-500 mb-1">{{ $product['brand'] }}</p>
                                @endif
                                
                                @if(\App\Models\SiteSetting::get('enable_product_reviews', '1') === '1')
                                <!-- Rating -->
                                @if($product['rating'] > 0)
                                <div class="flex items-center mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($product['rating']))
                                            <svg class="w-3 h-3 text-orange-400 fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                    <span class="text-xs text-gray-600 ml-1">({{ number_format($product['reviews']) }})</span>
                                </div>
                                @endif
                                @endif
                                
                                <!-- Price -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-1">
                                        @if($product['original_price'] > $product['price'])
                                            <span class="text-xs text-gray-400 line-through">{{ currency_format($product['original_price']) }}</span>
                                            <span class="text-sm font-bold text-red-600">{{ currency_format($product['price']) }}</span>
                                        @else
                                            <span class="text-sm font-bold text-gray-900">{{ currency_format($product['price']) }}</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Add to Cart Button -->
                                    <button 
                                        wire:click="addFrequentlyPurchasedToCart({{ json_encode($product) }})"
                                        class="text-xs bg-green-600 text-white px-3 py-1.5 rounded hover:bg-green-700 transition font-medium"
                                    >
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500">Your cart is empty</p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        @if(count($cartItems) > 0)
        <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
            <!-- Subtotal -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-bold text-gray-900">Subtotal ({{ count($cartItems) }} Items)</p>
                    <p class="text-xs text-gray-500">Includes all discounts</p>
                </div>
                <p class="text-xl font-bold text-gray-900">{{ currency_format($subtotal) }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-2">
                <a href="{{ route('cart.index') }}" class="block w-full bg-green-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                    View Cart
                </a>
                <button 
                    wire:click="hideCart"
                    class="block w-full bg-white border-2 border-gray-300 text-gray-700 text-center py-3 rounded-lg font-semibold hover:bg-gray-50 transition"
                >
                    Continue Shopping
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
