<div class="bg-white rounded-lg shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-orange-100">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Shipping & Tracking
            </h3>
            @if(!$isEditing)
                <button wire:click="toggleEdit" class="px-3 py-1.5 text-sm bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </button>
            @endif
        </div>
    </div>

    <div class="p-6">
        @if($isEditing)
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tracking Number</label>
                    <input type="text" wire:model="tracking_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" placeholder="TRK123456789">
                    @error('tracking_number') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Carrier</label>
                    <input type="text" wire:model="carrier" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" placeholder="Pathao, Sundarban, etc.">
                    @error('carrier') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Shipped At</label>
                    <input type="datetime-local" wire:model="shipped_at" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    @error('shipped_at') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivered At</label>
                    <input type="datetime-local" wire:model="delivered_at" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    @error('delivered_at') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" wire:click="toggleEdit" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center disabled:opacity-50">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Save Changes</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        @else
            <div class="space-y-3">
                @if($order->tracking_number || $order->carrier || $order->shipped_at || $order->delivered_at)
                    @if($order->tracking_number)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-32"><span class="text-sm font-medium text-gray-500">Tracking:</span></div>
                            <div class="flex-1"><code class="text-sm bg-gray-100 px-2 py-1 rounded font-mono">{{ $order->tracking_number }}</code></div>
                        </div>
                    @endif
                    @if($order->carrier)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-32"><span class="text-sm font-medium text-gray-500">Carrier:</span></div>
                            <div class="flex-1"><span class="text-sm font-medium text-gray-900">{{ $order->carrier }}</span></div>
                        </div>
                    @endif
                    @if($order->shipped_at)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-32"><span class="text-sm font-medium text-gray-500">Shipped:</span></div>
                            <div class="flex-1"><span class="text-sm text-gray-700">{{ $order->shipped_at->format('M d, Y h:i A') }}</span></div>
                        </div>
                    @endif
                    @if($order->delivered_at)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-32"><span class="text-sm font-medium text-gray-500">Delivered:</span></div>
                            <div class="flex-1"><span class="text-sm text-gray-700">{{ $order->delivered_at->format('M d, Y h:i A') }}</span></div>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-500 text-sm font-medium mb-1">No shipping information yet</p>
                        <p class="text-gray-400 text-xs">Click "Edit" to add tracking details</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
