<div>
    @if(count($bundleItems) > 1 && !$this->allInCart)
    <div class="bg-white py-8 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <!-- Section Title -->
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Frequently purchased together</h2>
            
            <!-- Bundle Container -->
            <div class="bg-white p-6">
                <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                    
                    <!-- Left Side: Product Images with Plus Signs -->
                    <div class="flex-1">
                        <div class="flex items-center justify-start flex-wrap gap-6 lg:gap-8">
                            @foreach($bundleItems as $index => $item)
                                <!-- Product Image -->
                                <div class="flex flex-col items-center group">
                                    <!-- Image Container with Hover Effect -->
                                    <a href="{{ route('products.show', $item['slug']) }}" 
                                       class="block w-32 h-32 lg:w-40 lg:h-40 bg-white border-2 border-gray-200 rounded-xl overflow-hidden hover:border-orange-400 hover:shadow-lg transition-all duration-300 p-3">
                                        <img src="{{ $item['image'] }}" 
                                             alt="{{ $item['name'] }}"
                                             class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-300">
                                    </a>
                                    
                                    @if(\App\Models\SiteSetting::get('enable_product_reviews', '1') === '1')
                                    <!-- Star Rating & Reviews -->
                                    <div class="flex items-center mt-3">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($item['rating']))
                                                <svg class="w-4 h-4 text-orange-400 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                        <span class="text-sm text-gray-700 ml-1.5 font-medium">{{ number_format($item['reviews']) }}</span>
                                    </div>
                                    @endif
                                    
                                    <!-- Product Name (Truncated) -->
                                    <p class="text-xs text-gray-600 text-center mt-2 max-w-[140px] line-clamp-2 leading-tight">
                                        {{ Str::limit($item['name'], 40) }}
                                    </p>
                                </div>
                                
                                <!-- Plus Sign (except for last item) -->
                                @if(!$loop->last)
                                <div class="text-gray-400 text-3xl font-light self-start mt-12 lg:mt-16">+</div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Right Side: Product List & Total -->
                    <div class="lg:w-96 space-y-4">
                        <!-- Product Checkboxes -->
                        <div class="space-y-3">
                            @foreach($bundleItems as $item)
                            <label class="flex items-start space-x-3 cursor-pointer group">
                                <!-- Checkbox -->
                                <input type="checkbox" 
                                       wire:model.live="selectedItems"
                                       value="{{ $item['id'] }}"
                                       {{ $item['isCurrent'] ? 'disabled' : '' }}
                                       {{ !$item['canAddToCart'] ? 'disabled' : '' }}
                                       class="mt-1 w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 {{ ($item['isCurrent'] || !$item['canAddToCart']) ? 'opacity-50' : '' }}">
                                
                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            @php
                                                $cart = session()->get('cart', []);
                                                $cartKey = 'variant_' . $item['variant_id'];
                                                $isInCart = isset($cart[$cartKey]);
                                            @endphp
                                            
                                            @if($item['isCurrent'])
                                                <span class="text-xs font-semibold text-green-600 mb-1 block">Current Item</span>
                                            @endif
                                            @if($isInCart)
                                                <span class="text-xs font-semibold text-blue-600 mb-1 block">✓ In Cart</span>
                                            @endif
                                            @if(!$item['canAddToCart'])
                                                <span class="text-xs font-semibold text-red-600 mb-1 block">Out of Stock</span>
                                            @endif
                                            <a href="{{ route('products.show', $item['slug']) }}" 
                                               class="text-sm text-blue-600 hover:text-blue-800 hover:underline line-clamp-2 group-hover:text-blue-800">
                                                {{ $item['name'] }}
                                            </a>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900 whitespace-nowrap ml-2">
                                            {{ currency_format($item['price']) }}
                                        </span>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        
                        <!-- Total Price -->
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-base font-semibold text-gray-900">Total:</span>
                                <span class="text-2xl font-bold text-gray-900">{{ currency_format($this->totalPrice) }}</span>
                            </div>
                            
                            <!-- Add Selected to Cart Button -->
                            <button wire:click="addSelectedToCart"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50"
                                    :disabled="$wire.selectedCount === 0"
                                    class="w-full bg-orange-500 hover:bg-orange-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 px-6 rounded-xl transition duration-200 shadow-sm hover:shadow-md">
                                <span wire:loading.remove wire:target="addSelectedToCart">
                                    Add Selected to Cart ({{ $this->selectedCount }})
                                </span>
                                <span wire:loading wire:target="addSelectedToCart" class="flex items-center justify-center">
                                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Adding...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
