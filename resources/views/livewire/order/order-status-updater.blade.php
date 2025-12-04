<div>
    <!-- Status Selection Dropdown -->
    <div class="@if($showNoteForm) invisible @endif">
        <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-2">
            Order Status
        </label>
        <select wire:model.live="newStatus" id="newStatus" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @if(empty($availableStatuses)) disabled @endif>
            <option value="{{ $order->status }}">{{ ucfirst($order->status) }} (Current)</option>
            @if(!empty($availableStatuses))
                @foreach($availableStatuses as $status)
                    @if($status !== $order->status)
                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                    @endif
                @endforeach
            @endif
        </select>
        @error('newStatus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        
        @if(empty($availableStatuses))
            <p class="text-sm text-gray-500 mt-2">No status transitions available for this order.</p>
        @endif
    </div>

    <!-- Inline Note Form (overlays over the Order Status card) -->
    @if($showNoteForm)
        <div class="absolute top-0 left-0 right-0 bg-white rounded-lg shadow-2xl p-6 z-50 -mx-6 -mt-6 border-2 border-blue-400">
            <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-blue-200">
                <div>
                    <h4 class="text-xl font-bold text-gray-900">
                        Status Change
                    </h4>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ ucfirst($order->status) }} → {{ ucfirst($pendingStatus) }}
                    </p>
                </div>
                <button wire:click="cancelStatusChange" type="button" 
                        class="text-gray-400 hover:text-red-500 transition-colors p-2 hover:bg-red-50 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="updateStatus" class="space-y-4">
                <!-- Tracking Information (show when status is shipped) -->
                @if($newStatus === 'shipped')
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                        <h5 class="text-sm font-semibold text-gray-900 mb-3">Shipping Information</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="trackingNumber" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tracking Number
                                </label>
                                <input type="text" wire:model="trackingNumber" id="trackingNumber"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter tracking number">
                                @error('trackingNumber') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="carrier" class="block text-sm font-medium text-gray-700 mb-2">
                                    Carrier
                                </label>
                                <input type="text" wire:model="carrier" id="carrier"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="e.g., Pathao, Sundarban">
                                @error('carrier') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-900 mb-2">
                        Add Note (Optional)
                    </label>
                    <textarea wire:model="notes" id="notes" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Add any notes about this status change..."></textarea>
                    @error('notes') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Notify Customer Checkbox -->
                <div class="flex items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <input type="checkbox" wire:model="notifyCustomer" id="notifyCustomer"
                           class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="notifyCustomer" class="ml-3 text-sm font-medium text-gray-700">
                        Notify customer via SMS/Email
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" wire:click="skipNote"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                            wire:loading.attr="disabled">
                        Skip & Update
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors shadow-lg"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Update Status</span>
                        <span wire:loading>Updating...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
