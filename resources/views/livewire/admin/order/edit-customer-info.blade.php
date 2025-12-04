<div class="bg-white rounded-lg shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
    <!-- Header -->
    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Customer Information
            </h3>
            @if(!$isEditing)
                <button wire:click="toggleEdit" class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
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
            <!-- Edit Mode -->
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Customer Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="customer_name" id="customer_name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           placeholder="Enter customer name">
                    @error('customer_name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Customer Email
                    </label>
                    <input type="email" wire:model="customer_email" id="customer_email"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           placeholder="customer@example.com">
                    @error('customer_email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Customer Phone <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="customer_phone" id="customer_phone"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           placeholder="+880 1XXX-XXXXXX">
                    @error('customer_phone') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="customer_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Customer Notes
                    </label>
                    <textarea wire:model="customer_notes" id="customer_notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                              placeholder="Any special instructions or notes from customer"></textarea>
                    @error('customer_notes') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" wire:click="toggleEdit" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
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
            <!-- View Mode -->
            <div class="space-y-3">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-32">
                        <span class="text-sm font-medium text-gray-500">Name:</span>
                    </div>
                    <div class="flex-1">
                        <span class="text-sm text-gray-900 font-medium">{{ $order->customer_name }}</span>
                    </div>
                </div>

                @if($order->customer_email)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-32">
                            <span class="text-sm font-medium text-gray-500">Email:</span>
                        </div>
                        <div class="flex-1">
                            <a href="mailto:{{ $order->customer_email }}" class="text-sm text-blue-600 hover:text-blue-700">
                                {{ $order->customer_email }}
                            </a>
                        </div>
                    </div>
                @endif

                <div class="flex items-start">
                    <div class="flex-shrink-0 w-32">
                        <span class="text-sm font-medium text-gray-500">Phone:</span>
                    </div>
                    <div class="flex-1">
                        <a href="tel:{{ $order->customer_phone }}" class="text-sm text-blue-600 hover:text-blue-700">
                            {{ $order->customer_phone }}
                        </a>
                    </div>
                </div>

                @if($order->customer_notes)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-32">
                            <span class="text-sm font-medium text-gray-500">Notes:</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700 italic bg-gray-50 p-2 rounded">{{ $order->customer_notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
