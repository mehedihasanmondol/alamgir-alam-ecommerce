@props([
    'product',
    'viewMode' => 'grid', // 'grid' or 'list'
    'showViewToggle' => false, // Whether to show both grid and list views
    'size' => 'default', // 'default', 'small', 'large'
    'showDescription' => false // Whether to show description in grid mode
])

@php
    // Calculate product data exactly like Shop Page
    // Ensure we get a proper ProductVariant model instance
    $variant = null;
    if ($product->variants && $product->variants->count() > 0) {
        $firstVariant = $product->variants->first();
        // If it's a stdClass, get the proper model by ID
        if ($firstVariant instanceof stdClass && isset($firstVariant->id)) {
            $variant = \App\Modules\Ecommerce\Product\Models\ProductVariant::find($firstVariant->id);
        } elseif ($firstVariant instanceof \App\Modules\Ecommerce\Product\Models\ProductVariant) {
            $variant = $firstVariant;
        }
    }
    
    // Use new media library system - handle both Product models and stdClass objects
    if (method_exists($product, 'getPrimaryThumbnailUrl')) {
        $imageUrl = $product->getPrimaryThumbnailUrl();
    } elseif ($product->images && $product->images->count() > 0) {
        $firstImage = $product->images->first();
        if (is_object($firstImage) && isset($firstImage->image_path)) {
            $imageUrl = asset('storage/' . $firstImage->image_path);
        } else {
            $imageUrl = asset('images/placeholder.png');
        }
    } else {
        $imageUrl = asset('images/placeholder.png');
    }
    $price = $variant ? ($variant->sale_price ?? $variant->price ?? 0) : 0;
    $originalPrice = $variant ? ($variant->price ?? 0) : 0;
    $hasDiscount = $originalPrice > $price;
    
    // Stock restriction setting - use model methods only if we have a proper model
    $canAddToCart = ($variant && method_exists($variant, 'canAddToCart')) ? $variant->canAddToCart() : false;
    $showStockInfo = ($variant && method_exists($variant, 'shouldShowStock')) ? $variant->shouldShowStock() : false;
    $stockText = ($variant && method_exists($variant, 'getStockDisplayText')) ? $variant->getStockDisplayText() : null;
    
    // Size-based classes
    $sizeClasses = [
        'small' => [
            'container' => 'w-48',
            'image' => 'aspect-square',
            'padding' => 'p-3',
            'title' => 'text-xs',
            'price' => 'text-sm',
            'button' => 'px-3 py-1.5 text-xs'
        ],
        'default' => [
            'container' => '',
            'image' => 'aspect-square',
            'padding' => 'p-4',
            'title' => 'text-sm',
            'price' => 'text-lg',
            'button' => 'px-4 py-2 text-sm'
        ],
        'large' => [
            'container' => '',
            'image' => 'aspect-square',
            'padding' => 'p-6',
            'title' => 'text-base',
            'price' => 'text-xl',
            'button' => 'px-6 py-3 text-base'
        ]
    ];
    
    $classes = $sizeClasses[$size] ?? $sizeClasses['default'];
@endphp

<!-- Grid View -->
<div x-show="!@js($showViewToggle) || viewMode === 'grid'" 
     class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition {{ $classes['container'] }}">
    <div class="relative {{ $classes['image'] }} bg-gray-100">
        <a href="{{ route('products.show', $product->slug) }}">
            <img src="{{ $imageUrl ?? asset('images/placeholder.png') }}" 
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                 loading="lazy">
        </a>
        
        <!-- Wishlist Button -->
        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
            @livewire('wishlist.add-to-wishlist', ['productId' => $product->id, 'variantId' => $variant->id ?? null, 'size' => 'md'], key('wishlist-grid-'.$product->id.'-'.uniqid()))
        </div>

        @if($showStockInfo && $variant && $variant->stock_quantity <= 0)
        <div class="absolute top-2 left-2 bg-gray-800 text-white text-xs font-bold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">
            Out of Stock
        </div>
        @elseif($hasDiscount)
        <div class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
            SALE
        </div>
        @endif
    </div>

    <div class="{{ $classes['padding'] }}">
        @if($product->brand)
        <p class="text-xs text-gray-600 mb-1">{{ $product->brand->name }}</p>
        @endif
        
        <h3 class="{{ $classes['title'] }} font-medium text-gray-900 mb-2 line-clamp-2 min-h-[40px]">
            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-green-600">
                {{ $product->name }}
            </a>
        </h3>

        <!-- Rating -->
        @if(\App\Models\SiteSetting::get('enable_product_reviews', '1') === '1' && $product->average_rating > 0)
        <div class="flex items-center mb-2">
            <div class="flex items-center">
                @for($i = 1; $i <= 5; $i++)
                <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                @endfor
            </div>
            <span class="ml-1 text-xs text-gray-600">({{ $product->review_count ?? 0 }})</span>
        </div>
        @endif

        <!-- Description (optional) -->
        @if($showDescription && $product->description)
        <p class="text-xs text-gray-600 mb-3 line-clamp-2">
            {{ Str::limit(strip_tags($product->description), 100) }}
        </p>
        @endif

        <!-- Price -->
        <div class="mb-3">
            <div class="flex items-baseline space-x-2">
                <span class="{{ $classes['price'] }} font-bold text-gray-900">{{ currency_format($price) }}</span>
                @if($hasDiscount)
                <span class="text-sm text-gray-500 line-through">{{ currency_format($originalPrice) }}</span>
                @endif
            </div>
        </div>

        <!-- Add to Cart Button -->
        @if($variant && $canAddToCart)
            @php
                $cart = session()->get('cart', []);
                $cartKey = 'variant_' . $variant->id;
                $isInCart = isset($cart[$cartKey]);
                $cartQuantity = $isInCart ? $cart[$cartKey]['quantity'] : 0;
            @endphp
            <button onclick="addToCartAndUpdate(this, {{ $product->id }}, {{ $variant->id ?? 'null' }}, 1, {{ $cartQuantity }})"
                    class="w-full {{ $classes['button'] }} {{ $isInCart ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-lg transition flex items-center justify-center"
                    data-product-id="{{ $product->id }}" 
                    data-variant-id="{{ $variant->id ?? 'null' }}"
                    data-is-in-cart="{{ $isInCart ? 'true' : 'false' }}"
                    data-cart-quantity="{{ $cartQuantity }}">
                @if($isInCart)
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add More ({{ $cartQuantity }})
                @else
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Add to Cart
                @endif
            </button>
        @elseif($showStockInfo && $variant && !$canAddToCart)
        <button disabled class="w-full {{ $classes['button'] }} bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed flex items-center justify-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Out of Stock
        </button>
        @elseif(!$variant)
        <button disabled class="w-full {{ $classes['button'] }} bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed flex items-center justify-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            No Variant Available
        </button>
        @endif
    </div>
</div>

<!-- List View (only shown if showViewToggle is true) -->
@if($showViewToggle)
<div x-show="viewMode === 'list'" class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
    <div class="flex flex-col md:flex-row">
        <div class="relative md:w-48 aspect-square bg-gray-100 flex-shrink-0">
            <a href="{{ route('products.show', $product->slug) }}">
                <img src="{{ $imageUrl ?? asset('images/placeholder.png') }}" 
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover"
                     loading="lazy">
            </a>
            
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

                    @if(\App\Models\SiteSetting::get('enable_product_reviews', '1') === '1' && $product->average_rating > 0)
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
                    @livewire('wishlist.add-to-wishlist', ['productId' => $product->id, 'variantId' => $variant->id ?? null, 'size' => 'lg'], key('wishlist-list-'.$product->id.'-'.uniqid()))
                    
                    @if($variant && $canAddToCart)
                        @php
                            $cart = session()->get('cart', []);
                            $cartKey = 'variant_' . $variant->id;
                            $isInCart = isset($cart[$cartKey]);
                            $cartQuantity = $isInCart ? $cart[$cartKey]['quantity'] : 0;
                        @endphp
                        <button onclick="addToCartAndUpdate(this, {{ $product->id }}, {{ $variant->id ?? 'null' }}, 1, {{ $cartQuantity }})"
                                class="px-6 py-3 {{ $isInCart ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-lg transition flex items-center justify-center"
                                data-product-id="{{ $product->id }}" 
                                data-variant-id="{{ $variant->id ?? 'null' }}"
                                data-is-in-cart="{{ $isInCart ? 'true' : 'false' }}"
                                data-cart-quantity="{{ $cartQuantity }}">
                            @if($isInCart)
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add More ({{ $cartQuantity }})
                            @else
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Add to Cart
                            @endif
                        </button>
                    @elseif($showStockInfo && $variant && !$canAddToCart)
                    <button disabled class="px-6 py-3 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Out of Stock
                    </button>
                    @elseif(!$variant)
                    <button disabled class="px-6 py-3 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        No Variant Available
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
@endif

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

