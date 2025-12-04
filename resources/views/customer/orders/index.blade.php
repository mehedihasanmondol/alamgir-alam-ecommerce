@extends('layouts.customer')

@section('title', 'My Orders')

@section('content')
<div class="space-y-6">
    <!-- Page Header with Stats -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-sm p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">My Orders</h1>
                <p class="text-blue-100 mt-1">Track and manage all your orders</p>
            </div>
            <div class="hidden md:flex items-center space-x-6">
                <div class="text-center">
                    <p class="text-3xl font-bold">{{ $orders->total() }}</p>
                    <p class="text-sm text-blue-100">Total Orders</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="GET" action="{{ route('customer.orders.index') }}" class="space-y-4">
            <!-- Search Bar -->
            <div class="relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search by order number, product name..."
                       class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Filter Tabs -->
            <div class="flex items-center space-x-2 overflow-x-auto pb-2">
                <a href="{{ route('customer.orders.index') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All Orders
                </a>
                <a href="{{ route('customer.orders.index', ['status' => 'pending']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap {{ request('status') === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Pending
                </a>
                <a href="{{ route('customer.orders.index', ['status' => 'processing']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap {{ request('status') === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Processing
                </a>
                <a href="{{ route('customer.orders.index', ['status' => 'shipped']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap {{ request('status') === 'shipped' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Shipped
                </a>
                <a href="{{ route('customer.orders.index', ['status' => 'delivered']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap {{ request('status') === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Delivered
                </a>
                <a href="{{ route('customer.orders.index', ['status' => 'cancelled']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap {{ request('status') === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Cancelled
                </a>
            </div>
        </form>
    </div>

    <!-- Orders List -->
    @if($orders->count() > 0)
        <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
                        <!-- Order Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-5 border-b border-gray-200">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <!-- Order Info -->
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4 flex-1">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-blue-100 rounded-lg p-3">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Order Number</p>
                                            <p class="font-bold text-gray-900 text-lg">#{{ $order->order_number }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="hidden sm:block w-px h-12 bg-gray-300"></div>
                                    
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Order Date</p>
                                        <p class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</p>
                                    </div>
                                    
                                    <div class="hidden sm:block w-px h-12 bg-gray-300"></div>
                                    
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Total Amount</p>
                                        <p class="font-bold text-blue-600 text-xl">৳{{ number_format($order->total_amount, 2) }}</p>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div>
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
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $statusClass }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items Preview -->
                        <div class="px-6 py-5">
                            <div class="flex flex-wrap items-center gap-4 mb-4">
                                @foreach($order->items->take(4) as $item)
                                    <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition-colors">
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
                                                 class="w-16 h-16 object-cover rounded-lg border-2 border-white shadow-sm"
                                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg flex items-center justify-center border-2 border-white shadow-sm\'><svg class=\'w-8 h-8 text-gray-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>';">
                                        @else
                                            <div class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg flex items-center justify-center border-2 border-white shadow-sm">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <p class="font-medium text-gray-900 text-sm truncate">{{ $item->product_name }}</p>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-xs text-gray-500">Qty: {{ $item->quantity }}</span>
                                                <span class="text-xs text-gray-400">•</span>
                                                <span class="text-xs font-semibold text-blue-600">৳{{ number_format($item->price * $item->quantity, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($order->items->count() > 4)
                                    <div class="bg-blue-50 rounded-lg px-4 py-3 text-center">
                                        <p class="text-sm font-semibold text-blue-700">+{{ $order->items->count() - 4 }}</p>
                                        <p class="text-xs text-blue-600">More Items</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-200">
                                <a href="{{ route('customer.orders.show', $order) }}" 
                                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Details
                                </a>
                                
                                @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                                    <!-- <a href="{{ route('orders.track') }}?order_number={{ $order->order_number }}" 
                                       class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 border-2 border-blue-600 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Track Order
                                    </a> -->
                                @endif
                                
                                @if($order->status === 'delivered')
                                    <a href="{{ route('customer.orders.invoice', $order) }}" 
                                       class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 border-2 border-green-600 text-green-600 font-medium rounded-lg hover:bg-green-50 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download Invoice
                                    </a>
                                @endif
                                
                                @if(in_array($order->status, ['pending', 'processing']))
                                    <form method="POST" action="{{ route('customer.orders.cancel', $order) }}" class="flex-1 sm:flex-none" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-6 py-3 border-2 border-red-600 text-red-600 font-medium rounded-lg hover:bg-red-50 transition-colors">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Cancel Order
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        <!-- Pagination -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            {{ $orders->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-full w-32 h-32 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-16 h-16 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">
                    @if(request('status'))
                        No {{ ucfirst(request('status')) }} Orders
                    @elseif(request('search'))
                        No Orders Found
                    @else
                        No Orders Yet
                    @endif
                </h3>
                <p class="text-gray-600 mb-8">
                    @if(request('search'))
                        We couldn't find any orders matching "{{ request('search') }}". Try a different search term.
                    @elseif(request('status'))
                        You don't have any {{ request('status') }} orders at the moment.
                    @else
                        You haven't placed any orders yet. Start shopping to see your orders here!
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if(request('search') || request('status'))
                        <a href="{{ route('customer.orders.index') }}" 
                           class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            View All Orders
                        </a>
                    @endif
                    <a href="{{ route('shop') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Start Shopping
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
