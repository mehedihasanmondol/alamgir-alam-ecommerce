<div>
    {{-- The whole world belongs to you. --}}
    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
        <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
        </svg>
        Delivery Options
    </h2>

    <!-- Hidden form fields for form submission -->
    <input type="hidden" name="delivery_zone_id" value="{{ $selectedZoneId }}">
    <input type="hidden" name="delivery_method_id" value="{{ $selectedMethodId }}">
    <input type="hidden" name="delivery_rate_id" value="{{ $selectedRateId }}">

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

</div>
