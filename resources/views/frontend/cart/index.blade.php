@extends('layouts.app')

@section('title', 'Shopping Cart - ' . \App\Models\SiteSetting::get('site_name', config('app.name')))

@section('description', 'Review your cart items and proceed to checkout. Free shipping on orders over $40.')

@section('keywords', 'shopping cart, cart, checkout, buy products')

@section('robots', 'noindex, follow')

@section('content')
<div class="bg-gray-50 min-h-screen py-8" x-data="cartPage()" @shipping-updated.window="shippingCost = $event.detail.shippingCost || 0">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Cart Items -->
            <div class="lg:col-span-2">
                <!-- Combined Cart Card -->
                <div class="bg-white rounded-lg shadow-sm">
                    <!-- Cart Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h1 class="text-2xl font-bold text-gray-900">
                                Cart (<span x-text="cartItemsCount">{{ count($cart) }}</span>)
                            </h1>
                            <!-- Delivery Information Inline -->
                            @livewire('cart.delivery-selector-inline')
                        </div>

                        <!-- Select All & Actions -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       x-model="selectAll" 
                                       @change="toggleSelectAll()"
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Select all</span>
                            </label>

                            <div class="flex items-center space-x-4">
                                <button @click="removeSelected()" 
                                        x-show="selectedItems.length > 0"
                                        class="flex items-center text-sm text-red-600 hover:text-red-700">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Remove all
                                </button>
                                <button class="flex items-center text-sm text-gray-600 hover:text-gray-700">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                    </svg>
                                    Share
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Items -->
                    @forelse($cart as $key => $item)
                    <div class="p-6 {{ !$loop->last ? 'border-b border-gray-200' : '' }}" x-data="{ quantity: {{ $item['quantity'] }} }">
                    <div class="flex items-start space-x-4">
                        <!-- Checkbox -->
                        <input type="checkbox" 
                               :value="'{{ $key }}'"
                               x-model="selectedItems"
                               class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 mt-2">

                        <!-- Product Image -->
                        <div class="flex-shrink-0">
                            <img src="{{ $item['image'] ?? asset('images/placeholder.png') }}" 
                                 alt="{{ $item['product_name'] }}"
                                 class="w-24 h-24 object-cover rounded-lg">
                            @if(isset($item['original_price']) && $item['original_price'] > $item['price'])
                            <span class="inline-block mt-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                                Special!
                            </span>
                            @endif
                        </div>

                        <!-- Product Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    @if($item['brand'])
                                    <p class="text-sm text-gray-600 mb-1">{{ $item['brand'] }}</p>
                                    @endif
                                    <h3 class="text-base font-medium text-gray-900 mb-1">
                                        <a href="{{ route('products.show', ['slug' => $item['slug'] ?? '#']) }}" 
                                           class="hover:text-green-600">
                                            {{ $item['product_name'] }}
                                        </a>
                                    </h3>
                                    @if(isset($item['sku']))
                                    <p class="text-xs text-gray-500">Product code: {{ $item['sku'] }}</p>
                                    @endif
                                </div>

                                <!-- Price -->
                                <div class="text-right ml-4">
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ currency_format($item['price']) }}
                                    </p>
                                    @if(isset($item['original_price']) && $item['original_price'] > $item['price'])
                                    <p class="text-sm text-gray-500 line-through">
                                        {{ currency_format($item['original_price']) }}
                                    </p>
                                    <p class="text-xs text-green-600 font-medium">
                                        Special! -{{ currency_format($item['original_price'] - $item['price']) }}
                                    </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Quantity Controls & Actions -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 mt-4">
                                <!-- Quantity Selector -->
                                <div class="flex items-center border border-gray-300 rounded-lg flex-shrink-0">
                                    <button @click="updateQuantity('{{ $key }}', quantity - 1)" 
                                            :disabled="quantity <= 1"
                                            class="px-2 sm:px-3 py-2 text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <input type="number" 
                                           x-model="quantity"
                                           @change="updateQuantity('{{ $key }}', quantity)"
                                           min="1"
                                           class="w-12 sm:w-16 text-center border-0 focus:ring-0 py-2 text-sm">
                                    <button @click="updateQuantity('{{ $key }}', quantity + 1)"
                                            class="px-2 sm:px-3 py-2 text-gray-600 hover:bg-gray-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Action Buttons Container -->
                                <div class="flex items-center gap-3 sm:gap-4">
                                    <!-- Remove Button -->
                                    <button @click="removeItem('{{ $key }}')"
                                            class="p-2 text-gray-400 hover:text-red-600 transition flex-shrink-0"
                                            title="Remove from cart">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>

                                    <!-- Save for Later / Add to Wishlist Button -->
                                    <button @click="saveForLater('{{ $key }}', {{ json_encode($item) }})"
                                            class="flex items-center text-sm text-gray-600 hover:text-green-600 transition flex-shrink-0"
                                            title="Save to wishlist">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        <span class="hidden xs:inline sm:inline">Save for later</span>
                                        <span class="xs:hidden sm:hidden">Save</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Your cart is empty</h3>
                        <p class="text-gray-600 mb-6">Add items to get started</p>
                        <a href="{{ route('shop') }}" 
                           class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                            Continue Shopping
                        </a>
                    </div>
                    @endforelse
                </div>

                <!-- Recommended Products -->
                @if($recommendedProducts->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Recommended for you</h2>
                    
                    <!-- Products Carousel -->
                    <div class="relative">
                        <!-- Navigation Buttons -->
                        <button 
                            onclick="scrollCarousel('cart-recommended', 'left')" 
                            class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 transition-all border border-gray-200"
                            aria-label="Previous products"
                        >
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        
                        <button 
                            onclick="scrollCarousel('cart-recommended', 'right')" 
                            class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 transition-all border border-gray-200"
                            aria-label="Next products"
                        >
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>

                        <!-- Products Slider -->
                        <div 
                            id="cart-recommended" 
                            class="flex gap-4 overflow-x-auto scroll-smooth pb-4 scrollbar-hide"
                            style="scrollbar-width: none; -ms-overflow-style: none;"
                        >
                            @foreach($recommendedProducts as $product)
                                <div class="flex-none w-[calc(75%-0.75rem)] sm:w-[calc(50%-0.5rem)] md:w-[calc(33.333%-0.667rem)] lg:w-[calc(25%-0.75rem)] ">
                                    <x-product-card-unified :product="$product" size="default" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <!-- Order Summary -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order summary</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Items total (<span x-text="selectedItems.length">0</span>)</span>
                                <span class="font-medium text-gray-900">$<span x-text="calculateItemsTotal().toFixed(2)">0.00</span></span>
                            </div>
                            
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Total weight: <span x-text="calculateTotalWeight().toFixed(2)">0.00</span> kg</span>
                            </div>
                            
                            <div class="flex justify-between" x-show="calculateDiscounts() > 0">
                                <button class="flex items-center text-gray-600 hover:text-gray-900">
                                    <span>Discounts</span>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <span class="font-medium text-red-600">-$<span x-text="calculateDiscounts().toFixed(2)">0.00</span></span>
                            </div>
                            
                            <div class="pl-4 text-xs text-gray-600" x-show="calculateDiscounts() > 0">
                                <div class="flex justify-between">
                                    <span>Product discounts</span>
                                    <span class="text-red-600">-$<span x-text="calculateDiscounts().toFixed(2)">0.00</span></span>
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium text-gray-900">$<span x-text="calculateSubtotal().toFixed(2)">0.00</span></span>
                                </div>
                            </div>
                            
                            <!-- Coupon Discount -->
                            <div class="flex justify-between" x-show="couponDiscount > 0">
                                <span class="text-gray-600">Coupon Discount</span>
                                <span class="font-medium text-green-600">-$<span x-text="couponDiscount.toFixed(2)">0.00</span></span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <div class="flex items-center text-gray-600">
                                    <span>Shipping</span>
                                    <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium" :class="freeShipping ? 'line-through text-gray-400 text-xs' : 'text-gray-900'">
                                        $<span x-text="shippingCost.toFixed(2)">0.00</span>
                                    </span>
                                    <span x-show="freeShipping" class="font-medium text-green-600">FREE</span>
                                </div>
                            </div>
                            
                        </div>
                        
                        <!-- Total -->
                        <div class="border-t border-gray-200 mt-4 pt-4">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-xl font-bold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-gray-900">
                                    $<span x-text="calculateGrandTotal().toFixed(2)">0.00</span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Checkout Button -->
                        <a href="{{ route('checkout.index') }}" 
                           class="block w-full px-6 py-3 bg-green-600 text-white text-center font-bold rounded-lg hover:bg-green-700 transition mb-4">
                            Proceed to Checkout
                        </a>
                        
                        <!-- Payment Methods -->
                        @php
                            $paymentGateways = \App\Models\PaymentGateway::active()->get();
                        @endphp
                        
                        @if($paymentGateways->count() > 0)
                            <div class="border-t border-gray-200 pt-4 mb-4">
                                <p class="text-xs text-gray-600 mb-2">Accepted payment methods</p>
                                <div class="flex flex-wrap items-center gap-2">
                                    @foreach($paymentGateways as $gateway)
                                        @if($gateway->logo)
                                            <img src="{{ asset('storage/' . $gateway->logo) }}" 
                                                 alt="{{ $gateway->name }}" 
                                                 class="h-6 w-auto"
                                                 title="{{ $gateway->name }}">
                                        @else
                                            <span class="text-xs px-2 py-1 bg-gray-100 rounded">{{ $gateway->name }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Coupon Code -->
                        <div class="border-t border-gray-200 pt-4">
                            @livewire('cart.coupon-applier')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delivery Selector Modal -->
    <div x-data="{ open: false }" 
         @open-modal.window="if ($event.detail.modalId === 'delivery-selector-modal') open = true"
         @close-modal.window="open = false"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <!-- Backdrop with blur effect -->
        <div class="fixed inset-0 transition-opacity" 
             style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
             @click="open = false"
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative rounded-lg shadow-xl max-w-2xl w-full p-6 border border-gray-200"
                 style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
                 @click.away="open = false"
                 x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Close Button -->
                <button type="button" 
                        @click="open = false"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <!-- Modal Header with Icon -->
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-blue-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
                
                <h3 class="text-xl font-medium text-gray-900 text-center mb-2">Select Delivery Method</h3>
                <p class="text-sm text-gray-500 text-center mb-6">Choose your preferred delivery zone and shipping method</p>

                <!-- Modal Body -->
                <div class="mb-6 max-h-[60vh] overflow-y-auto px-2 -mx-2">
                    @livewire('cart.delivery-selector', ['cartTotal' => $itemsTotal, 'cartWeight' => $totalWeight, 'itemCount' => count($cart)])
                </div>

                <!-- Modal Footer -->
                <div class="flex gap-3">
                    <button @click="open = false" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button @click="open = false" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Confirm Selection
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cartPage() {
    return {
        selectAll: false,
        selectedItems: [],
        promoCode: '',
        cartItemsCount: {{ count($cart) }},
        shippingCost: {{ session('shipping_cost', 0) }},
        totalWeight: {{ $totalWeight }},
        cartData: @json($cart->toArray()),
        couponDiscount: {{ session('applied_coupon.discount_amount', 0) }},
        freeShipping: {{ session('applied_coupon.free_shipping', false) ? 'true' : 'false' }},
        
        init() {
            // Initialize any required data
            this.$watch('selectedItems', () => {
                this.updateSelectAllState();
                // Shipping will be updated by Livewire component
            });
            
            // Select all items by default
            this.selectedItems = Object.keys(this.cartData);
            this.selectAll = true;
            
            // Listen for shipping updates from Livewire component
            window.addEventListener('shipping-updated', (event) => {
                this.shippingCost = event.detail.shippingCost || 0;
            });
            
            // Listen for Livewire coupon events
            Livewire.on('couponApplied', (event) => {
                this.couponDiscount = event[0]?.discount || event.discount || 0;
                this.freeShipping = event[0]?.freeShipping || event.freeShipping || false;
            });
            
            Livewire.on('couponRemoved', () => {
                this.couponDiscount = 0;
                this.freeShipping = false;
            });
        },
        
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedItems = Object.keys(this.cartData);
            } else {
                this.selectedItems = [];
            }
        },
        
        updateSelectAllState() {
            // Update selectAll checkbox state based on selected items
            const totalItems = Object.keys(this.cartData).length;
            this.selectAll = totalItems > 0 && this.selectedItems.length === totalItems;
        },
        
        // Calculate items total for selected items
        calculateItemsTotal() {
            let total = 0;
            this.selectedItems.forEach(key => {
                if (this.cartData[key]) {
                    const item = this.cartData[key];
                    total += item.price * item.quantity;
                }
            });
            return total;
        },
        
        // Calculate total weight for selected items
        calculateTotalWeight() {
            let weight = 0;
            this.selectedItems.forEach(key => {
                if (this.cartData[key]) {
                    const item = this.cartData[key];
                    // Assuming 0.1 kg per item as default weight
                    weight += 0.1 * item.quantity;
                }
            });
            return weight;
        },
        
        // Calculate discounts for selected items (for display purposes only)
        calculateDiscounts() {
            let discounts = 0;
            this.selectedItems.forEach(key => {
                if (this.cartData[key]) {
                    const item = this.cartData[key];
                    if (item.original_price && item.original_price > item.price) {
                        discounts += (item.original_price - item.price) * item.quantity;
                    }
                }
            });
            return discounts;
        },
        
        // Calculate subtotal (item.price is already the sale price, so no need to subtract discounts)
        calculateSubtotal() {
            return this.calculateItemsTotal();
        },
        
        // Get shipping cost from Livewire component
        getShippingCost() {
            // Free shipping from coupon
            if (this.freeShipping) {
                return 0;
            }
            // The Livewire component will update this via events
            return this.shippingCost || 0;
        },
        
        // Calculate grand total
        calculateGrandTotal() {
            let total = this.calculateSubtotal() - this.couponDiscount + this.getShippingCost();
            return Math.max(0, total); // Ensure total is never negative
        },
        
        updateQuantity(cartKey, quantity) {
            if (quantity < 1) return;
            
            fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    cart_key: cartKey,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        },
        
        removeItem(cartKey) {
            // Show confirmation modal
            window.dispatchEvent(new CustomEvent('confirm-modal', {
                detail: {
                    title: 'Remove Item',
                    message: 'Are you sure you want to remove this item from your cart?',
                    onConfirm: () => {
                        fetch('/cart/remove', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                cart_key: cartKey
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            }
                        });
                    }
                }
            }));
        },
        
        removeSelected() {
            if (this.selectedItems.length === 0) return;
            
            // Show confirmation modal
            window.dispatchEvent(new CustomEvent('confirm-modal', {
                detail: {
                    title: 'Remove Items',
                    message: `Are you sure you want to remove ${this.selectedItems.length} item(s) from your cart?`,
                    onConfirm: () => {
                        fetch('/cart/remove-multiple', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                cart_keys: this.selectedItems
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            }
                        });
                    }
                }
            }));
        },
        
        saveForLater(cartKey, item) {
            // Add to wishlist and remove from cart
            fetch('/wishlist/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: item.product_id,
                    variant_id: item.variant_id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove from cart
                    return fetch('/cart/remove', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            cart_key: cartKey
                        })
                    });
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            type: 'success',
                            message: 'Item saved to wishlist!'
                        }
                    }));
                    
                    // Dispatch wishlist-updated event for counter
                    Livewire.dispatch('wishlist-updated');
                    
                    // Reload page
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: {
                        type: 'error',
                        message: 'Failed to save item. Please try again.'
                    }
                }));
            });
        },
        
        applyPromoCode() {
            if (!this.promoCode) return;
            
            // Implement promo code logic
            alert('Promo code functionality will be implemented');
        },
        
        addToCart(product) {
            // Use Livewire to add to cart
            Livewire.dispatch('add-to-cart', {
                productId: product.product_id,
                variantId: product.variant_id,
                quantity: 1
            });
        }
    }
}

// Carousel scroll function for recommended products
function scrollCarousel(carouselId, direction) {
    const carousel = document.getElementById(carouselId);
    const scrollAmount = 220; // Card width + gap
    
    if (direction === 'left') {
        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}

// Hide scrollbar for webkit browsers
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endpush
@endsection
