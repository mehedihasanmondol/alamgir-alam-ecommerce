<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
            </svg>
            Delivery Information
        </h3>
        @auth
            @if($selectedZoneId && $selectedMethodId)
                <button wire:click="saveAsDefault" 
                        class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save as Default
                </button>
            @endif
        @endauth
    </div>

    <!-- Delivery Zone Selection -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Delivery Zone <span class="text-red-500">*</span>
        </label>
        <select wire:model.live="selectedZoneId" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">Select delivery zone</option>
            @foreach($deliveryZones as $zone)
                <option value="{{ $zone->id }}">
                    {{ $zone->name }}
                    @if($zone->description)
                        - {{ $zone->description }}
                    @endif
                </option>
            @endforeach
        </select>
        @if($deliveryZones->isEmpty())
            <p class="text-sm text-red-600 mt-1">No delivery zones available</p>
        @endif
    </div>

    <!-- Delivery Method Selection -->
    @if($selectedZoneId)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Delivery Method <span class="text-red-500">*</span>
                <span class="text-xs text-gray-500 font-normal">({{ $deliveryMethods ? $deliveryMethods->count() : 0 }} available)</span>
            </label>
            
            @if($deliveryMethods && $deliveryMethods->count() > 0)
                <div class="space-y-2">
                    @foreach($deliveryMethods as $method)
                        <label class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition
                                    {{ $selectedMethodId == $method->id ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300' }}">
                            <input type="radio" 
                                   wire:model.live="selectedMethodId" 
                                   value="{{ $method->id }}"
                                   class="mt-1 text-green-600 focus:ring-green-500">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        @if($method->icon)
                                            <span class="text-2xl mr-2">{{ $method->icon }}</span>
                                        @endif
                                        <span class="font-medium text-gray-900">{{ $method->name }}</span>
                                    </div>
                                    @php
                                        $rate = $method->rates->where('delivery_zone_id', $selectedZoneId)->first();
                                        $cost = $rate ? ($rate->base_rate + $rate->handling_fee + $rate->insurance_fee + $rate->cod_fee) : 0;
                                        $isFree = $method->qualifiesForFreeShipping($cartTotal);
                                    @endphp
                                    <span class="font-bold {{ $isFree ? 'text-green-600' : 'text-gray-900' }}">
                                        @if($isFree)
                                            FREE
                                        @else
                                            ৳{{ number_format($cost, 2) }}
                                        @endif
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $method->getEstimatedDeliveryText() }}
                                </p>
                                @if($method->description)
                                    <p class="text-xs text-gray-500 mt-1">{{ $method->description }}</p>
                                @endif
                                @if($method->free_shipping_threshold && !$isFree)
                                    <p class="text-xs text-green-600 mt-1">
                                        💡 Free shipping on orders over ৳{{ number_format($method->free_shipping_threshold, 2) }}
                                    </p>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            @else
                <div class="text-sm text-gray-500 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="font-medium text-gray-700 mb-2">⚠️ No delivery methods available for this zone</p>
                    <p class="text-xs">This could be because:</p>
                    <ul class="text-xs mt-1 ml-4 space-y-1">
                        <li>• No active delivery methods configured for this zone</li>
                        <li>• Cart total (৳{{ number_format($cartTotal, 2) }}) doesn't meet method requirements</li>
                        <li>• Cart weight ({{ $cartWeight }}kg) exceeds method limits</li>
                        <li>• Item count ({{ $itemCount }}) exceeds method limits</li>
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <!-- Shipping Cost Summary -->
    @if($selectedZoneId && $selectedMethodId && $deliveryRate)
        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="font-medium text-gray-900">Shipping Cost</span>
                <span class="text-xl font-bold text-green-600">
                    @if($shippingCost > 0)
                        ৳{{ number_format($shippingCost, 2) }}
                    @else
                        FREE
                    @endif
                </span>
            </div>
            
            @if($shippingCost > 0)
                <div class="space-y-1 text-xs text-gray-600 pl-4">
                    @if($deliveryRate->base_rate > 0)
                        <div class="flex justify-between">
                            <span>• Base Rate</span>
                            <span>৳{{ number_format($deliveryRate->base_rate, 2) }}</span>
                        </div>
                    @endif
                    @if($deliveryRate->handling_fee > 0)
                        <div class="flex justify-between">
                            <span>• Handling Fee</span>
                            <span>৳{{ number_format($deliveryRate->handling_fee, 2) }}</span>
                        </div>
                    @endif
                    @if($deliveryRate->insurance_fee > 0)
                        <div class="flex justify-between">
                            <span>• Insurance</span>
                            <span>৳{{ number_format($deliveryRate->insurance_fee, 2) }}</span>
                        </div>
                    @endif
                    @if($deliveryRate->cod_fee > 0)
                        <div class="flex justify-between">
                            <span>• COD Fee</span>
                            <span>৳{{ number_format($deliveryRate->cod_fee, 2) }}</span>
                        </div>
                    @endif
                </div>
            @endif
            
            <!-- Apply Button (for modal) -->
            <button onclick="window.dispatchEvent(new CustomEvent('close-modal'))"
                    class="w-full mt-4 px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                Apply Delivery Method
            </button>
        </div>
    @endif

    @guest
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <a href="{{ route('login') }}" class="font-medium underline">Login</a> to save your delivery preferences
            </p>
        </div>
    @endguest
</div>
