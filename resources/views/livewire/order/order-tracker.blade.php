<div class="max-w-4xl mx-auto">
    @if(!$searched || !$order)
        <!-- Track Order Form -->
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Track Your Order</h2>

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="trackOrder" class="space-y-6">
                <div>
                    <label for="orderNumber" class="block text-sm font-medium text-gray-700 mb-2">
                        Order Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="orderNumber" id="orderNumber"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="e.g., ORD-20240120-ABC123">
                    @error('orderNumber') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" wire:model="email" id="email"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="your@email.com">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <button type="submit" 
                        class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>Track Order</span>
                    <span wire:loading>Searching...</span>
                </button>
            </form>
        </div>
    @else
        <!-- Order Tracking Results -->
        <div class="space-y-6">
            <!-- Order Header -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h2>
                    <button wire:click="resetSearch" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Track Another Order
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Order Date</p>
                        <p class="font-medium">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Amount</p>
                        <p class="font-medium text-lg">৳{{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>

                @if($order->tracking_number)
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">Tracking Number:</span> {{ $order->tracking_number }}
                            @if($order->carrier)
                                <span class="ml-2">via {{ $order->carrier }}</span>
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            <!-- Order Timeline -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Timeline</h3>
                
                <div class="space-y-4">
                    @foreach($order->statusHistories as $history)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="font-medium text-gray-900">{{ ucfirst($history->new_status) }}</p>
                                @if($history->notes)
                                    <p class="text-sm text-gray-600 mt-1">{{ $history->notes }}</p>
                                @endif
                                <p class="text-sm text-gray-500 mt-1">{{ $history->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center space-x-4 pb-4 border-b border-gray-200 last:border-0">
                            @if($item->product_image)
                                <img src="{{ Storage::url($item->product_image) }}" 
                                     alt="{{ $item->product_name }}"
                                     class="w-16 h-16 object-cover rounded">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                    <span class="text-gray-400 text-xs">No Image</span>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $item->product_name }}</h4>
                                @if($item->variant_name)
                                    <p class="text-sm text-gray-600">{{ $item->variant_name }}</p>
                                @endif
                                <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                            </div>
                            
                            <div class="text-right">
                                <p class="font-medium text-gray-900">৳{{ number_format($item->total, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
