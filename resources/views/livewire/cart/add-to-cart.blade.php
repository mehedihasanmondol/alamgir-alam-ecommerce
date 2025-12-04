<div class="space-y-3">
    <!-- Progress Bar & Claimed Text (Only show if stock validation is enabled) -->
    @php
        $stockValidationEnabled = \App\Modules\Ecommerce\Product\Models\ProductVariant::isStockRestrictionEnabled();
    @endphp
    
    @if($stockValidationEnabled && $maxQuantity > 0)
    <div class="mb-1">
        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: {{ $this->claimedPercentage }}%"></div>
        </div>
        <!-- Claimed Text -->
        <div class="text-sm text-gray-700 text-center">
            {{ $this->claimedPercentage }}% claimed
        </div>
    </div>
    @endif

    <!-- Quantity Selector (Exact iHerb Style) -->
    <div class="flex items-center justify-center bg-gray-100 rounded-full p-1 w-40 mx-auto">
        <!-- Decrement Button -->
        <button 
            wire:click="decrement" 
            type="button"
            :disabled="$wire.quantity <= 1"
            class="w-10 h-10 flex items-center justify-center bg-white rounded-full hover:bg-gray-50 text-gray-600 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
            </svg>
        </button>

        <!-- Quantity Display -->
        <div class="flex-1 h-10 flex items-center justify-center">
            <span class="text-lg font-semibold text-gray-900">{{ $quantity }}</span>
        </div>

        <!-- Increment Button -->
        <button 
            wire:click="increment" 
            type="button"
            :disabled="$wire.quantity >= $wire.maxQuantity"
            class="w-10 h-10 flex items-center justify-center bg-white rounded-full hover:bg-gray-50 text-gray-600 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </button>
    </div>

    <!-- Add to Cart Button (Exact iHerb Orange Style) -->
    @php
        $variant = $selectedVariantId ? \App\Modules\Ecommerce\Product\Models\ProductVariant::find($selectedVariantId) : null;
        $canAddToCart = $variant && $variant->canAddToCart();
        $showStockInfo = $variant && $variant->shouldShowStock();
    @endphp

    @if($variant && $canAddToCart)
    <button 
        wire:click="addToCart" 
        wire:loading.attr="disabled"
        class="w-full bg-orange-500 hover:bg-orange-600 disabled:bg-orange-400 text-white font-bold text-base py-3.5 px-6 rounded-xl transition duration-200 shadow-sm hover:shadow-md">
        <span wire:loading.remove wire:target="addToCart">
            Add to Cart
        </span>
        <span wire:loading wire:target="addToCart" class="flex items-center justify-center">
            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Adding...
        </span>
    </button>
    @elseif($showStockInfo)
    <button 
        type="button"
        disabled
        class="w-full bg-gray-400 text-white font-bold py-3.5 px-6 rounded-xl cursor-not-allowed">
        Out of Stock
    </button>
    @endif

    <!-- Success Message -->
    @if($showSuccess)
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium">Added to cart successfully!</span>
    </div>
    @endif

    <!-- Buy Now Button (Optional) -->
    {{-- Commented out until checkout route is created
    @if($maxQuantity > 0)
        <div class="pt-4 border-t border-gray-200">
            <a href="{{ route('checkout') }}" 
               class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center font-semibold py-3 px-6 rounded-lg transition duration-200">
                Buy Now
            </a>
        </div>
    @endif
    --}}
</div>

@push('scripts')
<script>
    // Listen for cart update events
    window.addEventListener('cart-updated', event => {
        // Update cart count in header
        const cartCount = document.querySelector('[data-cart-count]');
        if (cartCount && event.detail.count) {
            cartCount.textContent = event.detail.count;
        }
    });

    // Listen for toast messages
    window.addEventListener('show-toast', event => {
        // You can integrate with your toast notification system here
        console.log(event.detail.type, event.detail.message);
    });
</script>
@endpush
