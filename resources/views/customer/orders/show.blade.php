@extends('layouts.customer')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('customer.orders.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Orders
        </a>
    </div>

    <!-- Order Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-sm p-6 text-white">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold">Order #{{ $order->order_number }}</h1>
                <p class="text-blue-100 mt-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Placed on {{ $order->created_at->format('M d, Y h:i A') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'processing' => 'bg-blue-100 text-blue-800',
                        'shipped' => 'bg-indigo-100 text-indigo-800',
                        'delivered' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                    $statusClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $statusClass }}">
                    {{ ucfirst($order->status) }}
                </span>
                
                <!-- Download Invoice Button -->
                <a href="{{ route('customer.orders.invoice', $order) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition-colors shadow-sm border border-blue-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download
                </a>
                
                <!-- Print Button -->
                <button onclick="window.open('{{ route('customer.orders.invoice', $order) }}', '_blank').print()"
                        class="inline-flex items-center px-4 py-2 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm border border-gray-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
            </div>
        </div>

        <!-- Order Status Bar -->
        <div class="mt-6 pt-6 border-t border-blue-300">
            <div class="flex items-center justify-between">
                @php
                    $statuses = ['pending', 'processing', 'confirmed', 'shipped', 'delivered'];
                    $currentIndex = array_search($order->status, $statuses);
                @endphp
                @foreach($statuses as $index => $status)
                    <div class="flex-1 text-center">
                        <div class="relative">
                            @if($index < count($statuses) - 1)
                                <div class="absolute top-5 left-1/2 w-full h-1 {{ $index < $currentIndex ? 'bg-white' : 'bg-blue-300' }}"></div>
                            @endif
                            <div class="relative z-10 w-12 h-12 mx-auto rounded-full flex items-center justify-center shadow-lg {{ $index <= $currentIndex ? 'bg-white text-blue-600' : 'bg-blue-400 text-blue-100' }}">
                                @if($index <= $currentIndex)
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <span class="text-sm font-semibold">{{ $index + 1 }}</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-white mt-3 font-medium">{{ ucfirst($status) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        @if($order->tracking_number)
            <div class="mt-6 p-4 bg-blue-900 bg-opacity-30 rounded-lg backdrop-blur-sm border border-white border-opacity-20">
                <p class="text-sm text-white">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    <span class="font-semibold">Tracking Number:</span> {{ $order->tracking_number }}
                    @if($order->carrier)
                        <span class="ml-2">via {{ $order->carrier }}</span>
                    @endif
                </p>
            </div>
        @endif
    </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-0">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
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
                                            <a href="{{ $item->product ? route('products.show', $item->product->slug) : '#' }}" 
                                               class="block">
                                                <img src="{{ $imageUrl }}" 
                                                     alt="{{ $item->product_name }}"
                                                     class="w-24 h-24 object-cover rounded-lg border border-gray-200 hover:border-blue-500 transition-colors"
                                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center\'><svg class=\'w-12 h-12 text-gray-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>';">
                                            </a>
                                        @else
                                            <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <!-- Product Name with Link -->
                                        @if($item->product)
                                            <a href="{{ route('products.show', $item->product->slug) }}" 
                                               class="font-medium text-gray-900 hover:text-blue-600 transition-colors inline-block mb-1">
                                                {{ $item->product_name }}
                                            </a>
                                        @else
                                            <h3 class="font-medium text-gray-900 mb-1">{{ $item->product_name }}</h3>
                                        @endif
                                        
                                        <!-- Category and Brand -->
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            @if($item->product && $item->product->category)
                                                <span class="inline-flex items-center px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                    </svg>
                                                    {{ $item->product->category->name }}
                                                </span>
                                            @endif
                                            
                                            @if($item->product && $item->product->brand)
                                                <span class="inline-flex items-center px-2 py-0.5 bg-purple-50 text-purple-700 text-xs rounded">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                                    </svg>
                                                    {{ $item->product->brand->name }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <!-- SKU -->
                                        @if($item->product_sku)
                                            <p class="text-xs text-gray-500 mb-1">
                                                <span class="font-medium">SKU:</span> 
                                                <span class="font-mono">{{ $item->product_sku }}</span>
                                            </p>
                                        @endif
                                        
                                        <!-- Variant Info -->
                                        @if($item->variant_name)
                                            <div class="mb-1">
                                                <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-700 text-xs rounded">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                                    </svg>
                                                    {{ $item->variant_name }}
                                                </span>
                                            </div>
                                        @endif
                                        
                                        <!-- Variant Attributes -->
                                        @if($item->variant_attributes && is_array($item->variant_attributes))
                                            <div class="flex flex-wrap gap-1 mb-2">
                                                @foreach($item->variant_attributes as $key => $value)
                                                    <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded border border-gray-200">
                                                        <span class="font-semibold">{{ ucfirst($key) }}:</span>
                                                        <span class="ml-1">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Quantity:</span> {{ $item->quantity }}
                                        </p>
                                    </div>
                                    
                                    <!-- Price -->
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">৳{{ number_format($item->total, 2) }}</p>
                                        <p class="text-sm text-gray-500">৳{{ number_format($item->price, 2) }} each</p>
                                        @if($item->discount_amount > 0)
                                            <p class="text-xs text-green-600 font-medium mt-1">
                                                Saved ৳{{ number_format($item->discount_amount, 2) }}
                                            </p>
                                        @endif
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
                                @if($order->shipping_cost > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Shipping</span>
                                        <span class="text-gray-900">৳{{ number_format($order->shipping_cost, 2) }}</span>
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

                        <!-- Pay With -->
                        @if($order->payment_status !== 'paid' && in_array($order->payment_method, ['online', 'cod']))
                            @php
                                $paymentGateways = \App\Models\PaymentGateway::active()->get();
                            @endphp
                            
                            @if($paymentGateways->count() > 0)
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <span class="text-gray-700 font-medium">Pay with:</span>
                                        @foreach($paymentGateways as $gateway)
                                            <form action="{{ route('payment.initiate', $order) }}" method="POST" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="gateway" value="{{ $gateway->slug }}">
                                                <button type="submit" 
                                                        class="group relative px-4 py-3 bg-white border-2 border-gray-300 rounded-lg hover:border-green-500 hover:shadow-md transition-all duration-200 cursor-pointer"
                                                        title="Pay with {{ $gateway->name }}">
                                                    @if($gateway->logo)
                                                        <img src="{{ asset('storage/' . $gateway->logo) }}" 
                                                             alt="{{ $gateway->name }}" 
                                                             class="h-8 w-auto group-hover:scale-105 transition-transform">
                                                    @else
                                                        <span class="text-sm font-semibold text-gray-700 group-hover:text-green-600">{{ $gateway->name }}</span>
                                                    @endif
                                                    
                                                    <!-- Hover indicator -->
                                                    <div class="absolute inset-0 bg-green-500 opacity-0 group-hover:opacity-5 rounded-lg transition-opacity"></div>
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
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

                <!-- Payment Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Payment Method</p>
                            <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Payment Status</p>
                            <span class="inline-block mt-1 px-3 py-1 text-sm font-semibold rounded-full bg-{{ $order->payment_status_color }}-100 text-{{ $order->payment_status_color }}-800">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if($order->canBeCancelled())
                    <form id="cancel-customer-order-form" action="{{ route('customer.orders.cancel', $order) }}" method="POST">
                        @csrf
                        <button type="button" 
                                onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                    detail: { 
                                        title: 'Cancel Order', 
                                        message: 'Are you sure you want to cancel this order? This action cannot be undone.',
                                        onConfirm: () => document.getElementById('cancel-customer-order-form').submit()
                                    } 
                                }))"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Cancel Order
                        </button>
                    </form>
                @endif

                <!-- Need Help -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Need Help?</h3>
                    <p class="text-sm text-gray-600 mb-4">Contact our customer support for any questions about your order.</p>
                    <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Contact Support</a>
                </div>
            </div>
    </div>
</div>
@endsection
