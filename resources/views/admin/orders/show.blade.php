@extends('layouts.admin')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                    <p class="text-gray-600 mt-1">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Print Invoice
            </a>
            <a href="{{ route('admin.orders.edit', $order) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Order
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center space-x-4 pb-4 border-b border-gray-200 last:border-0">
                                @php
                                    $imageUrl = null;
                                    
                                    // Priority 1: Use stored product_image from order item (historical data)
                                    if ($item->product_image) {
                                        $imageUrl = asset('storage/' . $item->product_image);
                                    }
                                    // Priority 2: Use variant image if available (for old data)
                                    elseif ($item->variant && $item->variant->image) {
                                        $imageUrl = asset('storage/' . $item->variant->image);
                                    }
                                    // Priority 3: Use product's primary thumbnail (NEW MEDIA SYSTEM)
                                    elseif ($item->product) {
                                        $imageUrl = $item->product->getPrimaryThumbnailUrl();
                                    }
                                @endphp
                                
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $item->product_name }}"
                                         class="w-20 h-20 object-cover rounded-lg border border-gray-200"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center\'><svg class=\'w-10 h-10 text-gray-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>';">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ $item->product_name }}</h3>
                                    @if($item->product_sku)
                                        <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                                    @endif
                                    @if($item->variant_name)
                                        <p class="text-sm text-gray-600">{{ $item->variant_name }}</p>
                                    @endif
                                    {{-- Variant attributes hidden as per user request --}}
                                    {{-- @if($item->formatted_variant_attributes)
                                        <p class="text-sm text-gray-500">{{ $item->formatted_variant_attributes }}</p>
                                    @endif --}}
                                </div>
                                
                                <div class="text-center">
                                    <p class="text-sm text-gray-500">Quantity</p>
                                    <p class="font-medium text-gray-900">{{ $item->quantity }}</p>
                                </div>
                                
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Price</p>
                                    <p class="font-medium text-gray-900">৳{{ number_format($item->price, 2) }}</p>
                                </div>
                                
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Total</p>
                                    <p class="font-medium text-gray-900">৳{{ number_format($item->total, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Totals -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900">৳{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->tax_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tax</span>
                                    <span class="text-gray-900">৳{{ number_format($order->tax_amount, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($order->shipping_cost > 0)
                                <!-- Shipping Summary -->
                                <div class="py-3 px-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                                    <div class="flex justify-between text-sm font-semibold text-blue-900 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                            </svg>
                                            Shipping Summary
                                        </span>
                                        <span class="text-blue-800 text-base">৳{{ number_format($order->shipping_cost, 2) }}</span>
                                    </div>
                                    
                                    <!-- Delivery Details -->
                                    @if($order->deliveryZone || $order->deliveryMethod)
                                        <div class="mb-2">
                                            <div class="grid grid-cols-2 gap-2 text-xs">
                                                @if($order->deliveryZone)
                                                    <div>
                                                        <span class="text-gray-500">Zone:</span>
                                                        <span class="font-medium text-gray-700 ml-1">{{ $order->deliveryZone->name }}</span>
                                                    </div>
                                                @endif
                                                @if($order->deliveryMethod)
                                                    <div>
                                                        <span class="text-gray-500">Method:</span>
                                                        <span class="font-medium text-gray-700 ml-1">{{ $order->deliveryMethod->name }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($order->deliveryMethod && $order->deliveryMethod->carrier_name)
                                                <div class="mt-1.5 text-xs">
                                                    <span class="text-gray-500">Carrier:</span>
                                                    <span class="font-semibold text-orange-700 ml-1">🚚 {{ $order->deliveryMethod->carrier_name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <!-- Cost Breakdown -->
                                    @if($order->base_shipping_cost || $order->handling_fee || $order->insurance_fee || $order->cod_fee)
                                        <div class="space-y-1 text-xs">
                                            @if($order->base_shipping_cost > 0)
                                                <div class="flex justify-between text-gray-600 pl-3">
                                                    <span>• Base Rate</span>
                                                    <span class="font-medium">৳{{ number_format($order->base_shipping_cost, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($order->handling_fee > 0)
                                                <div class="flex justify-between text-gray-600 pl-3">
                                                    <span>• Handling Fee</span>
                                                    <span class="font-medium">৳{{ number_format($order->handling_fee, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($order->insurance_fee > 0)
                                                <div class="flex justify-between text-gray-600 pl-3">
                                                    <span>• Insurance Fee</span>
                                                    <span class="font-medium">৳{{ number_format($order->insurance_fee, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($order->cod_fee > 0)
                                                <div class="flex justify-between text-gray-600 pl-3">
                                                    <span>• COD Fee</span>
                                                    <span class="font-medium text-orange-600">৳{{ number_format($order->cod_fee, 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @elseif($order->shipping_cost == 0 && ($order->deliveryZone || $order->deliveryMethod))
                                <!-- Free Shipping -->
                                <div class="py-2 px-3 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="flex items-center text-green-700 font-medium">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            FREE SHIPPING
                                        </span>
                                        <span class="text-green-800 font-semibold">৳0.00</span>
                                    </div>
                                    @if($order->deliveryZone || $order->deliveryMethod)
                                        <div class="mt-1 text-xs text-gray-600">
                                            @if($order->deliveryZone)
                                                <span>{{ $order->deliveryZone->name }}</span>
                                            @endif
                                            @if($order->deliveryMethod)
                                                <span class="ml-1">• {{ $order->deliveryMethod->name }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Discount</span>
                                    <span class="text-red-600">-৳{{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($order->coupon_discount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">
                                        Coupon Discount
                                        @if($order->coupon_code)
                                            <span class="inline-flex items-center px-2 py-0.5 ml-2 rounded text-xs font-bold bg-green-100 text-green-800">
                                                {{ $order->coupon_code }}
                                            </span>
                                        @endif
                                    </span>
                                    <span class="text-red-600">-৳{{ number_format($order->coupon_discount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                                <span class="text-gray-900">Total</span>
                                <span class="text-gray-900">৳{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status History -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Status History</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($order->statusHistories as $history)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="font-medium text-gray-900">{{ $history->description }}</p>
                                        <span class="text-sm text-gray-500">{{ $history->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($history->notes)
                                        <p class="text-sm text-gray-600 mt-1">{{ $history->notes }}</p>
                                    @endif
                                    @if($history->user)
                                        <p class="text-xs text-gray-500 mt-1">By {{ $history->user->name }}</p>
                                    @endif
                                    @if($history->customer_notified)
                                        <p class="text-xs text-green-600 mt-1">✓ Customer notified</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Status -->
            <div class="bg-white rounded-lg shadow-sm p-6 relative overflow-visible">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Status</h3>
                
                <!-- Status Update Section -->
                <div class="mb-6">
                    @livewire('order.order-status-updater', ['order' => $order, 'availableStatuses' => $availableStatuses])
                </div>

                <!-- Additional Status Information -->
                <div class="space-y-3 pt-6 border-t border-gray-200">
                    <div>
                        <p class="text-sm text-gray-600">Payment Status</p>
                        <span class="inline-block mt-1 px-3 py-1 text-sm font-semibold rounded-full bg-{{ $order->payment_status_color }}-100 text-{{ $order->payment_status_color }}-800">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Payment Method</p>
                        <p class="font-medium mt-1">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                    </div>
                    <div class="pt-3 border-t border-gray-200">
                        <div class="mb-2">
                            <p class="text-sm text-gray-600">Shipping Carrier</p>
                            @if($order->carrier)
                                <p class="font-medium mt-1 text-green-600">✓ {{ $order->carrier }}</p>
                            @else
                                <p class="font-medium mt-1 text-orange-600">⚠ Not Assigned</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tracking Number</p>
                            @if($order->tracking_number)
                                <p class="font-medium mt-1 font-mono text-blue-600">{{ $order->tracking_number }}</p>
                            @else
                                <p class="font-medium mt-1 text-gray-400">Not Available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Information (Customer Selected) -->
            @if($order->deliveryZone || $order->deliveryMethod || $order->delivery_status || $order->estimated_delivery)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                </svg>
                                <h3 class="font-semibold text-gray-900">Delivery Info</h3>
                            </div>
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded">
                                Selected
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-4 space-y-3">
                        @php
                            $deliveryRate = $order->appliedDeliveryRate;
                        @endphp
                        
                        <!-- Carrier Information - Highlighted -->
                        @if($order->deliveryMethod && $order->deliveryMethod->carrier_name)
                            <div class="p-3 bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg border-2 border-orange-300 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="p-1.5 bg-orange-500 rounded-full">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-orange-700 uppercase tracking-wide">Carrier</p>
                                            <p class="text-base font-bold text-orange-900">{{ $order->deliveryMethod->carrier_name }}</p>
                                        </div>
                                    </div>
                                    @if($order->deliveryMethod->estimated_days)
                                        <div class="text-right">
                                            <p class="text-xs text-orange-600">Delivery Time</p>
                                            <p class="text-sm font-semibold text-orange-900">{{ $order->deliveryMethod->estimated_days }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Zone & Method - Compact -->
                        @if($order->deliveryZone || $order->deliveryMethod)
                            <div class="grid grid-cols-2 gap-2">
                                @if($order->deliveryZone)
                                    <div class="p-2 bg-blue-50 rounded border-l-2 border-blue-500">
                                        <p class="text-xs text-gray-500 mb-0.5">Zone</p>
                                        <p class="font-semibold text-sm text-gray-900">{{ $order->deliveryZone->name }}</p>
                                        @if($order->deliveryZone->code)
                                            <span class="text-xs font-mono text-blue-600">{{ $order->deliveryZone->code }}</span>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($order->deliveryMethod)
                                    <div class="p-2 bg-purple-50 rounded border-l-2 border-purple-500">
                                        <p class="text-xs text-gray-500 mb-0.5">Method</p>
                                        <div class="flex items-center">
                                            @if($order->deliveryMethod->icon)
                                                <span class="text-sm mr-1">{{ $order->deliveryMethod->icon }}</span>
                                            @endif
                                            <p class="font-semibold text-sm text-gray-900">{{ $order->deliveryMethod->name }}</p>
                                        </div>
                                        @if($order->deliveryMethod->code)
                                            <span class="text-xs font-mono text-purple-600">{{ $order->deliveryMethod->code }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Rate Configuration & Shipping Cost Combined -->
                        @if($deliveryRate && $order->deliveryZone && $order->deliveryMethod)
                            <div class="p-2 bg-gradient-to-br from-indigo-50 to-purple-50 rounded border border-indigo-200">
                                <!-- Header with Calculation Type -->
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs font-semibold text-gray-700">Rate Configuration</p>
                                    <span class="text-xs px-1.5 py-0.5 bg-indigo-600 text-white rounded font-medium">
                                        {{ ucfirst(str_replace('_', ' ', $order->deliveryMethod->calculation_type ?? 'flat_rate')) }}
                                    </span>
                                </div>
                                
                                <div class="space-y-1 text-xs">
                                    <!-- Base Rate -->
                                    <div class="flex justify-between text-gray-700 bg-white bg-opacity-50 px-2 py-1 rounded">
                                        <span class="font-medium">Base Rate:</span>
                                        <span class="font-semibold">৳{{ number_format($deliveryRate->base_rate, 2) }}</span>
                                    </div>
                                    
                                    <!-- Calculation Rules (Condensed) -->
                                    @if($order->deliveryMethod->calculation_type === 'weight_based' && $deliveryRate->rate_per_kg > 0)
                                        <div class="flex justify-between text-gray-600 px-2">
                                            <span>+ Weight ({{ $deliveryRate->weight_from ?? '0' }}-{{ $deliveryRate->weight_to ?? '∞' }}kg)</span>
                                            <span class="font-medium">৳{{ number_format($deliveryRate->rate_per_kg, 2) }}/kg</span>
                                        </div>
                                    @endif
                                    
                                    @if($order->deliveryMethod->calculation_type === 'price_based' && $deliveryRate->rate_percentage > 0)
                                        <div class="flex justify-between text-gray-600 px-2">
                                            <span>+ Price-based</span>
                                            <span class="font-medium">{{ number_format($deliveryRate->rate_percentage, 2) }}%</span>
                                        </div>
                                    @endif
                                    
                                    @if($order->deliveryMethod->calculation_type === 'item_based' && $deliveryRate->rate_per_item > 0)
                                        <div class="flex justify-between text-gray-600 px-2">
                                            <span>+ Per Item ({{ $deliveryRate->item_from ?? '0' }}-{{ $deliveryRate->item_to ?? '∞' }})</span>
                                            <span class="font-medium">৳{{ number_format($deliveryRate->rate_per_item, 2) }}/item</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Additional Fees -->
                                    @if($deliveryRate->handling_fee > 0)
                                        <div class="flex justify-between text-gray-600 px-2">
                                            <span>+ Handling</span>
                                            <span>৳{{ number_format($deliveryRate->handling_fee, 2) }}</span>
                                        </div>
                                    @endif
                                    @if($deliveryRate->insurance_fee > 0)
                                        <div class="flex justify-between text-gray-600 px-2">
                                            <span>+ Insurance</span>
                                            <span>৳{{ number_format($deliveryRate->insurance_fee, 2) }}</span>
                                        </div>
                                    @endif
                                    @if($deliveryRate->cod_fee > 0)
                                        <div class="flex justify-between text-gray-600 px-2">
                                            <span>+ COD Fee</span>
                                            <span>৳{{ number_format($deliveryRate->cod_fee, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Final Shipping Cost Applied -->
                        @if($order->shipping_cost > 0)
                            <div class="p-2 bg-green-50 rounded border border-green-200">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold text-gray-700">💰 Final Shipping Cost</span>
                                    <span class="text-base font-bold text-green-700">৳{{ number_format($order->shipping_cost, 2) }}</span>
                                </div>
                                
                                <!-- Cost Breakdown (What was actually charged) -->
                                @if($order->base_shipping_cost || $order->handling_fee || $order->insurance_fee || $order->cod_fee)
                                    <div class="mt-1.5">
                                        <div class="space-y-0.5 text-xs text-gray-600">
                                            @if($order->base_shipping_cost > 0)
                                                <div class="flex justify-between">
                                                    <span>• Base Shipping</span>
                                                    <span class="font-medium">৳{{ number_format($order->base_shipping_cost, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($order->handling_fee > 0)
                                                <div class="flex justify-between">
                                                    <span>• Handling Fee</span>
                                                    <span class="font-medium">৳{{ number_format($order->handling_fee, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($order->insurance_fee > 0)
                                                <div class="flex justify-between">
                                                    <span>• Insurance Fee</span>
                                                    <span class="font-medium">৳{{ number_format($order->insurance_fee, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($order->cod_fee > 0)
                                                <div class="flex justify-between">
                                                    <span>• COD Fee</span>
                                                    <span class="font-medium">৳{{ number_format($order->cod_fee, 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @elseif($order->shipping_cost == 0 && ($order->deliveryZone || $order->deliveryMethod))
                            <div class="p-2 bg-green-50 rounded border border-green-200">
                                <div class="text-center mb-1">
                                    <span class="text-sm font-bold text-green-700">✓ FREE SHIPPING</span>
                                </div>
                                @if($order->deliveryMethod && $order->deliveryMethod->free_shipping_threshold)
                                    <p class="text-xs text-center text-gray-600">
                                        Qualified at ৳{{ number_format($order->deliveryMethod->free_shipping_threshold, 2) }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        <!-- Status & Estimated Delivery - Compact Row -->
                        @if($order->delivery_status || $order->estimated_delivery)
                            <div class="grid grid-cols-2 gap-2">
                                @if($order->delivery_status)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Status</p>
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded 
                                            @if($order->delivery_status === 'delivered') bg-green-100 text-green-800
                                            @elseif($order->delivery_status === 'out_for_delivery') bg-blue-100 text-blue-800
                                            @elseif($order->delivery_status === 'in_transit') bg-yellow-100 text-yellow-800
                                            @elseif($order->delivery_status === 'picked_up') bg-indigo-100 text-indigo-800
                                            @elseif($order->delivery_status === 'failed' || $order->delivery_status === 'returned') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                                        </span>
                                    </div>
                                @endif

                                @if($order->estimated_delivery)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Est. Delivery</p>
                                        <p class="text-xs font-semibold text-gray-900">
                                            📅 {{ $order->estimated_delivery->format('M d, Y') }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $order->estimated_delivery->diffForHumans() }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(!$order->deliveryZone && !$order->deliveryMethod)
                            <div class="text-center py-3">
                                <svg class="w-8 h-8 mx-auto text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-xs text-gray-500 mb-1">No delivery info</p>
                                <a href="{{ route('admin.orders.edit', $order) }}" class="text-xs text-blue-600 hover:text-blue-700">
                                    Add details →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-medium">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">{{ $order->customer_email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-medium">{{ $order->customer_phone }}</p>
                    </div>
                    @if($order->customer_notes)
                        <div>
                            <p class="text-sm text-gray-600">Customer Notes</p>
                            <p class="text-sm text-gray-700 mt-1">{{ $order->customer_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Address -->
            @if($order->shippingAddress)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h3>
                    <div class="text-sm text-gray-700">
                        <p class="font-medium">{{ $order->shippingAddress->full_name }}</p>
                        <p class="mt-1">{{ $order->shippingAddress->phone }}</p>
                        <p class="mt-2">{{ $order->shippingAddress->formatted_address }}</p>
                    </div>
                </div>
            @endif

            <!-- Billing Address -->
            @if($order->billingAddress)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Billing Address</h3>
                    <div class="text-sm text-gray-700">
                        <p class="font-medium">{{ $order->billingAddress->full_name }}</p>
                        <p class="mt-1">{{ $order->billingAddress->phone }}</p>
                        <p class="mt-2">{{ $order->billingAddress->formatted_address }}</p>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            @if($order->canBeCancelled())
                <form id="cancel-order-form" action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                    @csrf
                    <button type="button" 
                            onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                detail: { 
                                    title: 'Cancel Order', 
                                    message: 'Are you sure you want to cancel this order? This action cannot be undone.',
                                    onConfirm: () => document.getElementById('cancel-order-form').submit()
                                } 
                            }))"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Cancel Order
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
