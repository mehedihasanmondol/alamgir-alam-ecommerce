{{-- 
/**
 * ModuleName: Product Card Component
 * Purpose: Reusable product card for listing pages
 * 
 * Props:
 * - product: Product model instance
 * 
 * @category Frontend
 * @package  Components
 * @created  2025-01-06
 */
--}}

@props(['product'])

@php
    // Get default variant - handle both defaultVariant relationship and variants collection
    $variant = null;
    
    // Try to get the variant
    if (isset($product->defaultVariant) && !is_null($product->defaultVariant)) {
        $variant = $product->defaultVariant;
    } elseif (isset($product->variants) && is_object($product->variants) && method_exists($product->variants, 'isNotEmpty') && $product->variants->isNotEmpty()) {
        $defaultVariant = $product->variants->where('is_default', true)->first();
        $variant = $defaultVariant ?? $product->variants->first();
    }
    
    // Ensure $variant is an object, not a collection
    if ($variant && is_object($variant) && method_exists($variant, 'toArray')) {
        // It's still a collection, get the first item
        $variant = $variant->first();
    }
    
    // Now safely access properties
    $price = 0;
    $originalPrice = null;
    $discount = 0;
    $inStock = false;
    
    if ($variant && is_object($variant)) {
        $price = $variant->sale_price ?? $variant->price ?? 0;
        $originalPrice = ($variant->sale_price ?? null) ? ($variant->price ?? null) : null;
        $discount = $originalPrice ? round((($originalPrice - $price) / $originalPrice) * 100) : 0;
        $inStock = ($variant->stock_quantity ?? 0) > 0;
    }
    
    // Use new media library system for images
    $imageUrl = $product->getPrimaryThumbnailUrl();
@endphp

<div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition group">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <!-- Product Image -->
        <div class="relative overflow-hidden rounded-t-lg bg-gray-100">
            @if($imageUrl)
                <img 
                    src="{{ $imageUrl }}" 
                    alt="{{ $product->name }}" 
                    class="w-full h-64 object-cover group-hover:scale-105 transition duration-300"
                >
            @else
                <div class="w-full h-64 flex items-center justify-center">
                    <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif

            <!-- Badges -->
            <div class="absolute top-2 left-2 flex flex-col gap-2">
                @if($discount > 0)
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                        -{{ $discount }}%
                    </span>
                @endif
                @if($product->is_featured)
                    <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded">
                        Featured
                    </span>
                @endif
                @if(!$inStock)
                    <span class="bg-gray-500 text-white text-xs font-bold px-2 py-1 rounded">
                        Out of Stock
                    </span>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="absolute top-2 right-2 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition">
                <button class="bg-white hover:bg-green-600 hover:text-white text-gray-700 p-2 rounded-full shadow-md transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </button>
                <button class="bg-white hover:bg-green-600 hover:text-white text-gray-700 p-2 rounded-full shadow-md transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Product Info -->
        <div class="p-4">
            <!-- Brand -->
            @if($product->brand)
                <p class="text-xs text-gray-500 mb-1">{{ $product->brand->name }}</p>
            @endif

            <!-- Product Name -->
            <h3 class="font-medium text-gray-900 mb-2 line-clamp-2 group-hover:text-green-600 transition">
                {{ $product->name }}
            </h3>

            @if(\App\Models\SiteSetting::get('enable_product_reviews', '1') === '1')
            <!-- Rating (Placeholder) -->
            <div class="flex items-center mb-2">
                <div class="flex text-yellow-400">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-xs text-gray-500 ml-2">(0)</span>
            </div>
            @endif

            <!-- Price -->
            <div class="flex items-center justify-between">
                <div>
                    @if($originalPrice)
                        <span class="text-sm text-gray-400 line-through mr-2">৳{{ number_format($originalPrice, 2) }}</span>
                    @endif
                    <span class="text-lg font-bold text-green-600">৳{{ number_format($price, 2) }}</span>
                </div>
            </div>

            <!-- Add to Cart Button -->
            <button 
                class="w-full mt-3 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-medium transition {{ !$inStock ? 'opacity-50 cursor-not-allowed' : '' }}"
                {{ !$inStock ? 'disabled' : '' }}
            >
                @if($inStock)
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Add to Cart
                @else
                    Out of Stock
                @endif
            </button>
        </div>
    </a>
</div>
