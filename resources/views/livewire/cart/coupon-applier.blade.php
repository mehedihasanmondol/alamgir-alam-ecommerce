<div x-data="{ expanded: @entangle('showCouponSection') }">
    @if($appliedCoupon)
        <!-- Compact Applied Coupon Display -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-3 mb-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 flex-1 min-w-0">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-green-900 text-sm">{{ $appliedCoupon->code }}</span>
                            @if($freeShipping)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                    </svg>
                                    Free Ship
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-green-700 truncate">{{ $appliedCoupon->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 ml-2">
                    <span class="font-bold text-green-900 text-sm whitespace-nowrap">-{{ currency_format($discountAmount) }}</span>
                    <button wire:click="removeCoupon" 
                            type="button"
                            class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded transition"
                            title="Remove coupon">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @else
        <!-- Collapsible Coupon Section -->
        <button @click="expanded = !expanded" 
                type="button"
                class="w-full flex items-center justify-between p-3 bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-lg hover:from-orange-100 hover:to-amber-100 transition-all group mb-3">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center group-hover:bg-orange-200 transition">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div class="text-left">
                    <p class="text-sm font-semibold text-gray-900">Have a coupon?</p>
                    <p class="text-xs text-gray-600">Click to apply discount</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" 
                 :class="{ 'rotate-180': expanded }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- Expanded Coupon Content -->
        <div x-show="expanded" 
             x-collapse
             class="mb-3">
            <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-4">
                <!-- Coupon Input Form -->
                <form wire:submit.prevent="applyCoupon">
                    <div class="flex space-x-2">
                        <input type="text" 
                               wire:model="couponCode"
                               placeholder="Enter code"
                               class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent uppercase"
                               maxlength="50">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition whitespace-nowrap disabled:opacity-50">
                            <span wire:loading.remove>Apply</span>
                            <span wire:loading>
                                <svg class="animate-spin h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>

                    @if($message)
                        <div class="mt-2 rounded-lg p-2.5 text-sm {{ $messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800' }}">
                            <div class="flex items-start">
                                @if($messageType === 'success')
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                                <span class="text-xs">{{ $message }}</span>
                            </div>
                        </div>
                    @endif
                </form>

                <!-- Available Coupons -->
                @if(count($availableCoupons) > 0)
                    <div class="border-t border-gray-200 pt-3">
                        <p class="text-xs font-semibold text-gray-700 mb-2 flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Available Offers
                        </p>
                        <div class="space-y-2">
                            @foreach($availableCoupons as $coupon)
                                <div class="relative bg-gradient-to-r from-gray-50 to-gray-100 border border-dashed border-gray-300 rounded-lg p-2.5 hover:border-green-400 hover:shadow-sm transition group">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0 pr-2">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-600 text-white">
                                                    {{ $coupon['code'] }}
                                                </span>
                                                @if($coupon['free_shipping'])
                                                    <span class="inline-flex items-center text-xs text-blue-600">
                                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                                        </svg>
                                                        Free Ship
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-700 font-medium mb-0.5">{{ $coupon['name'] }}</p>
                                            @if($coupon['description'])
                                                <p class="text-xs text-gray-500 line-clamp-1">{{ $coupon['description'] }}</p>
                                            @endif
                                            @if($coupon['min_purchase_amount'])
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Min: {{ currency_format($coupon['min_purchase_amount']) }}
                                                </p>
                                            @endif
                                            @if($coupon['expires_at'])
                                                <p class="text-xs text-orange-600 mt-0.5">
                                                    Expires: {{ \Carbon\Carbon::parse($coupon['expires_at'])->format('M d, Y') }}
                                                </p>
                                            @endif
                                        </div>
                                        <button wire:click="quickApply('{{ $coupon['code'] }}')"
                                                type="button"
                                                @if(!$coupon['can_apply']) disabled @endif
                                                class="flex-shrink-0 px-3 py-1.5 text-xs font-semibold rounded-md transition {{ $coupon['can_apply'] ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-200 text-gray-500 cursor-not-allowed' }}">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
