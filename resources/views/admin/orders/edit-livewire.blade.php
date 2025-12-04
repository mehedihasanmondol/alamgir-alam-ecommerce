@extends('layouts.admin')

@section('title', 'Edit Order - ' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ showNotification: false, notificationType: '', notificationMessage: '' }" 
     @notify.window="showNotification = true; notificationType = $event.detail.type; notificationMessage = $event.detail.message; setTimeout(() => showNotification = false, 5000)">
    
    <!-- Notification Toast -->
    <div x-show="showNotification" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-4 right-4 z-50 max-w-sm">
        <div :class="notificationType === 'success' ? 'bg-green-500' : 'bg-red-500'" 
             class="rounded-lg shadow-lg p-4 text-white flex items-center space-x-3">
            <svg x-show="notificationType === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <svg x-show="notificationType === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span x-text="notificationMessage"></span>
        </div>
    </div>

    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.orders.show', $order) }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Order #{{ $order->order_number }}</h1>
                    <p class="text-gray-600 mt-1">Update order information by category • Last updated: {{ $order->updated_at->diffForHumans() }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                    {{ ucfirst($order->status) }}
                </span>
                <a href="{{ route('admin.orders.show', $order) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    View Order
                </a>
            </div>
        </div>
    </div>

    <!-- Order Summary Bar -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-indigo-100 text-sm font-medium">Subtotal</p>
                <p class="text-2xl font-bold mt-1">৳{{ number_format($order->subtotal, 2) }}</p>
            </div>
            <div>
                <p class="text-indigo-100 text-sm font-medium">Shipping</p>
                <p class="text-2xl font-bold mt-1">৳{{ number_format($order->shipping_cost, 2) }}</p>
            </div>
            @if($order->discount_amount > 0)
                <div>
                    <p class="text-indigo-100 text-sm font-medium">Discount</p>
                    <p class="text-2xl font-bold mt-1">৳{{ number_format($order->discount_amount, 2) }}</p>
                </div>
            @endif
            @if($order->coupon_discount > 0)
                <div>
                    <p class="text-indigo-100 text-sm font-medium">Coupon</p>
                    <p class="text-2xl font-bold mt-1">৳{{ number_format($order->coupon_discount, 2) }}</p>
                </div>
            @endif
            <div class="border-l border-indigo-400 pl-6">
                <p class="text-indigo-100 text-sm font-medium">Total Amount</p>
                <p class="text-3xl font-bold mt-1">৳{{ number_format($order->total_amount, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Livewire Components Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column -->
        <div class="space-y-6">
            <!-- Shipping & Billing Addresses -->
            @livewire('admin.order.edit-addresses', ['order' => $order], key('addresses-'.$order->id))

            <!-- Payment Information -->
            @livewire('admin.order.edit-payment-info', ['order' => $order], key('payment-'.$order->id))

            <!-- Delivery Information -->
            @livewire('admin.order.edit-delivery-info', ['order' => $order], key('delivery-'.$order->id))
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Costs & Discounts -->
            @livewire('admin.order.edit-costs-discounts', ['order' => $order], key('costs-'.$order->id))

            <!-- Status & Notes -->
            @livewire('admin.order.edit-status-notes', ['order' => $order], key('status-'.$order->id))
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Invoice
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Export Order
            </button>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Back to Orders
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Reload page when order is updated to refresh all components
    window.addEventListener('orderUpdated', event => {
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    });
</script>
@endpush
@endsection
