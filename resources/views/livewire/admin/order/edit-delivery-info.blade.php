<div class="bg-white rounded-lg shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
    <!-- Header -->
    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-purple-100">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Delivery & Shipping
            </h3>
            @if(!$isEditing)
                <button wire:click="toggleEdit" class="px-3 py-1.5 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </button>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        @if($isEditing)
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Delivery Zone
                    </label>
                    <select wire:model.live="delivery_zone_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                        <option value="">Select Zone</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }} @if($zone->code)({{ $zone->code }})@endif</option>
                        @endforeach
                    </select>
                    @error('delivery_zone_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Delivery Method
                    </label>
                    <select wire:model.live="delivery_method_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" @if(!$delivery_zone_id) disabled @endif>
                        <option value="">Select Method</option>
                        @foreach($availableMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->name }} @if($method->estimated_days)- {{ $method->estimated_days }}@endif</option>
                        @endforeach
                    </select>
                    @if(!$delivery_zone_id)
                        <p class="text-xs text-gray-500 mt-1">Please select a zone first</p>
                    @endif
                    @error('delivery_method_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                @if($shippingCostPreview !== null)
                    <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200 animate-pulse">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Shipping Cost Preview:</p>
                                <p class="text-xs text-indigo-600 mt-0.5">This will update the order total</p>
                            </div>
                            <p class="text-2xl font-bold text-indigo-700">৳{{ number_format($shippingCostPreview, 2) }}</p>
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Delivery Status
                    </label>
                    <select wire:model="delivery_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                        <option value="">Not Set</option>
                        <option value="pending">Pending</option>
                        <option value="assigned">Assigned</option>
                        <option value="picked_up">Picked Up</option>
                        <option value="in_transit">In Transit</option>
                        <option value="out_for_delivery">Out for Delivery</option>
                        <option value="delivered">Delivered</option>
                        <option value="failed">Failed</option>
                        <option value="returned">Returned</option>
                    </select>
                    @error('delivery_status') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Estimated Delivery Date
                    </label>
                    <input type="date" wire:model="estimated_delivery" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                    @error('estimated_delivery') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tracking Information -->
                <div class="pt-4 border-t border-purple-200">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Shipping & Tracking
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tracking Number</label>
                            <input type="text" wire:model="tracking_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" placeholder="TRK123456789">
                            @error('tracking_number') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Carrier</label>
                            <input type="text" wire:model="carrier" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" placeholder="Pathao, Sundarban, etc.">
                            @error('carrier') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Shipped At</label>
                            <input type="datetime-local" wire:model="shipped_at" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                            @error('shipped_at') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Delivered At</label>
                            <input type="datetime-local" wire:model="delivered_at" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                            @error('delivered_at') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" wire:click="toggleEdit" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg wire:loading.remove wire:target="save" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span wire:loading.remove wire:target="save">Save Changes</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        @else
            <div class="space-y-3">
                @if($order->deliveryZone)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-32">
                            <span class="text-sm font-medium text-gray-500">Zone:</span>
                        </div>
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-900">{{ $order->deliveryZone->name }}</span>
                            @if($order->deliveryZone->code)
                                <span class="ml-2 text-xs text-gray-500 font-mono">({{ $order->deliveryZone->code }})</span>
                            @endif
                        </div>
                    </div>
                @endif

                @if($order->deliveryMethod)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-32">
                            <span class="text-sm font-medium text-gray-500">Method:</span>
                        </div>
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-900">{{ $order->deliveryMethod->name }}</span>
                            @if($order->deliveryMethod->estimated_days)
                                <span class="ml-2 text-xs text-gray-500">({{ $order->deliveryMethod->estimated_days }})</span>
                            @endif
                        </div>
                    </div>
                @endif

                @if($order->delivery_status)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-32">
                            <span class="text-sm font-medium text-gray-500">Status:</span>
                        </div>
                        <div class="flex-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                            </span>
                        </div>
                    </div>
                @endif

                @if($order->estimated_delivery)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-32">
                            <span class="text-sm font-medium text-gray-500">Est. Delivery:</span>
                        </div>
                        <div class="flex-1">
                            <span class="text-sm text-gray-700">{{ $order->estimated_delivery->format('M d, Y') }}</span>
                            <span class="ml-2 text-xs text-gray-500">({{ $order->estimated_delivery->diffForHumans() }})</span>
                        </div>
                    </div>
                @endif

                <!-- Tracking Information -->
                @if($order->tracking_number || $order->carrier || $order->shipped_at || $order->delivered_at)
                    <div class="pt-3 mt-3 border-t border-purple-100">
                        <p class="text-xs font-semibold text-purple-700 mb-2 flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Shipping & Tracking
                        </p>
                        
                        @if($order->tracking_number)
                            <div class="flex items-start mb-2">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Tracking:</span>
                                </div>
                                <div class="flex-1">
                                    <code class="text-sm bg-purple-50 px-2 py-1 rounded font-mono text-purple-700">{{ $order->tracking_number }}</code>
                                </div>
                            </div>
                        @endif

                        @if($order->carrier)
                            <div class="flex items-start mb-2">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Carrier:</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-900">🚚 {{ $order->carrier }}</span>
                                </div>
                            </div>
                        @endif

                        @if($order->shipped_at)
                            <div class="flex items-start mb-2">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Shipped:</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-sm text-gray-700">{{ $order->shipped_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                        @endif

                        @if($order->delivered_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-medium text-gray-500">Delivered:</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-sm text-green-700 font-medium">✓ {{ $order->delivered_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
