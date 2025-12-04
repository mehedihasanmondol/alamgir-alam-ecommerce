@if($products->count() > 0)
<div :class="viewMode === 'grid' ? 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4' : 'space-y-4'">
    @foreach($products as $product)
    @php
        $variant = $product->variants->first();
        $imageUrl = $product->getPrimaryThumbnailUrl();
        $price = $variant->sale_price ?? $variant->price ?? 0;
        $originalPrice = $variant->price ?? 0;
        $hasDiscount = $originalPrice > $price;
    @endphp

    @include('frontend.shop.partials.product-card', ['product' => $product, 'variant' => $variant, 'imageUrl' => $imageUrl, 'price' => $price, 'originalPrice' => $originalPrice, 'hasDiscount' => $hasDiscount])
    @endforeach
</div>

<!-- Pagination -->
<div class="mt-8">
    {{ $products->links() }}
</div>

@else
<!-- Empty State -->
<div class="bg-white rounded-lg shadow-sm p-12 text-center">
    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
    <p class="text-gray-600 mb-6">Try adjusting your filters or search terms</p>
    <button @click="clearFilters()" 
            class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
        Clear All Filters
    </button>
</div>
@endif
