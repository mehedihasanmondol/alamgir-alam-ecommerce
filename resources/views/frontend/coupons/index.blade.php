@extends('layouts.app')

@section('title', 'Coupons & Special Offers - ' . \App\Models\SiteSetting::get('site_name', config('app.name')))

@section('description', 'Discover exclusive discount codes and special offers. Save more on your favorite health and wellness products with our active coupons.')

@section('keywords', 'coupons, discount codes, promo codes, special offers, deals, savings')

@section('og_type', 'website')
@section('og_image', asset('images/coupons-banner.jpg'))

@section('content')
<div class="bg-gradient-to-r from-blue-50 to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Special Offers & Coupons</h1>
            <p class="text-lg text-gray-600">Save more with our exclusive discount codes</p>
        </div>

        @if($coupons->isEmpty())
            <!-- No Coupons Available -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Active Coupons</h3>
                <p class="text-gray-600 mb-6">Check back soon for exciting offers and discounts!</p>
                <a href="{{ route('shop') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Continue Shopping
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @else
            <!-- Coupons Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($coupons as $coupon)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Coupon Header with Gradient -->
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium opacity-90">
                                    {{ $coupon->type === 'percentage' ? 'PERCENTAGE OFF' : 'FLAT DISCOUNT' }}
                                </span>
                                @if($coupon->free_shipping)
                                    <span class="px-2 py-1 bg-white/20 rounded text-xs font-semibold">
                                        FREE SHIPPING
                                    </span>
                                @endif
                            </div>
                            <div class="text-4xl font-bold mb-1">
                                {{ $coupon->formatted_value }}
                            </div>
                            <div class="text-sm opacity-90">{{ $coupon->name }}</div>
                        </div>

                        <!-- Coupon Body -->
                        <div class="p-6">
                            <!-- Coupon Code -->
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-500 mb-2">COUPON CODE</label>
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1 px-4 py-3 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg">
                                        <code class="text-lg font-bold text-gray-900 tracking-wider">{{ $coupon->code }}</code>
                                    </div>
                                    <button onclick="copyCoupon('{{ $coupon->code }}')" 
                                            class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                                            title="Copy code">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($coupon->description)
                                <p class="text-sm text-gray-600 mb-4">{{ $coupon->description }}</p>
                            @endif

                            <!-- Coupon Details -->
                            <div class="space-y-2 text-sm">
                                @if($coupon->min_purchase_amount)
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="text-gray-700">Min. purchase: <strong>৳{{ number_format($coupon->min_purchase_amount, 2) }}</strong></span>
                                    </div>
                                @endif

                                @if($coupon->max_discount_amount && $coupon->type === 'percentage')
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="text-gray-700">Max. discount: <strong>৳{{ number_format($coupon->max_discount_amount, 2) }}</strong></span>
                                    </div>
                                @endif

                                @if($coupon->usage_limit_per_user)
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="text-gray-700">{{ $coupon->usage_limit_per_user }} use(s) per customer</span>
                                    </div>
                                @endif

                                @if($coupon->first_order_only)
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                        </svg>
                                        <span class="text-gray-700">First order only</span>
                                    </div>
                                @endif

                                @if($coupon->expires_at)
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-orange-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-gray-700">Valid until: <strong>{{ $coupon->expires_at->format('M d, Y') }}</strong></span>
                                    </div>
                                @else
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="text-gray-700">No expiry date</span>
                                    </div>
                                @endif

                                @if($coupon->usage_limit)
                                    @php
                                        $remaining = max(0, $coupon->usage_limit - $coupon->total_used);
                                        $percentage = ($coupon->total_used / $coupon->usage_limit) * 100;
                                    @endphp
                                    <div class="mt-3">
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span>{{ $remaining }} remaining</span>
                                            <span>{{ $coupon->total_used }}/{{ $coupon->usage_limit }} used</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-2 rounded-full transition-all" 
                                                 style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Apply Button -->
                            <a href="{{ route('shop') }}" 
                               class="mt-6 block w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-center rounded-lg hover:from-blue-700 hover:to-purple-700 transition font-semibold">
                                Shop Now
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- How to Use Section -->
            <div class="mt-12 bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">How to Use Coupons</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">1. Shop</h3>
                        <p class="text-sm text-gray-600">Add products to your cart</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">2. Copy Code</h3>
                        <p class="text-sm text-gray-600">Click to copy your coupon code</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">3. Apply</h3>
                        <p class="text-sm text-gray-600">Paste code at checkout</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">4. Save</h3>
                        <p class="text-sm text-gray-600">Enjoy your discount!</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function copyCoupon(code) {
    navigator.clipboard.writeText(code).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2';
        toast.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Coupon code copied!</span>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }).catch(function(err) {
        console.error('Failed to copy:', err);
    });
}
</script>
@endsection
