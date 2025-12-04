@extends('layouts.customer')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-sm p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}!</h1>
        <p class="text-blue-100">Here's what's happening with your account today.</p>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">All time</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_orders'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Awaiting processing</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Completed</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['completed_orders'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Successfully delivered</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Wishlist Items -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-pink-500 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Wishlist</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['wishlist_count'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Saved items</p>
                </div>
                <div class="bg-pink-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Quick Actions Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Orders</h2>
                        <a href="{{ route('customer.orders.index') }}" 
                           class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center">
                            View All
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($recentOrders as $order)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="font-semibold text-gray-900">Order #{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    @if($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <span>{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
                                    <span>•</span>
                                    <span class="font-semibold text-gray-900">৳{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('customer.orders.show', $order) }}" 
                                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View Details
                                    </a>
                                    @if($order->status === 'delivered')
                                        <a href="{{ route('customer.orders.invoice', $order) }}" 
                                           class="text-gray-600 hover:text-gray-700 text-sm font-medium">
                                            Invoice
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <p class="text-gray-500 mb-4">No orders yet</p>
                            <a href="{{ route('shop') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                Start Shopping
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions & Account Info -->
        <div class="space-y-6">
            <!-- Quick Actions Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('customer.orders.index') }}" 
                       class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="bg-blue-100 rounded-lg p-2 group-hover:bg-blue-200 transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Track Order</p>
                            <p class="text-xs text-gray-500">Check order status</p>
                        </div>
                    </a>

                    <a href="{{ route('wishlist.index') }}" 
                       class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="bg-pink-100 rounded-lg p-2 group-hover:bg-pink-200 transition-colors">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">My Wishlist</p>
                            <p class="text-xs text-gray-500">View saved items</p>
                        </div>
                    </a>

                    <a href="{{ route('customer.addresses.index') }}" 
                       class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="bg-green-100 rounded-lg p-2 group-hover:bg-green-200 transition-colors">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Manage Addresses</p>
                            <p class="text-xs text-gray-500">Add or edit addresses</p>
                        </div>
                    </a>

                    <a href="{{ route('customer.settings') }}" 
                       class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="bg-purple-100 rounded-lg p-2 group-hover:bg-purple-200 transition-colors">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Account Settings</p>
                            <p class="text-xs text-gray-500">Update preferences</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Account Info Card -->
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg shadow-sm p-6 border border-blue-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Member Since</p>
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->created_at->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Email</p>
                        <p class="text-sm font-medium text-gray-900 break-all">{{ auth()->user()->email }}</p>
                    </div>
                    @if(auth()->user()->mobile)
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Mobile</p>
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->mobile }}</p>
                    </div>
                    @endif
                </div>
                <a href="{{ route('customer.profile') }}" 
                   class="mt-4 block w-full text-center px-4 py-2 bg-white border border-blue-200 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 transition-colors">
                    Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
