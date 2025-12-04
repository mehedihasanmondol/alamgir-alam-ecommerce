@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    {{-- Header with Date Filter --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-sm text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}!</p>
            </div>
            
            {{-- Date Range Filter --}}
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">From:</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">To:</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
            </form>
        </div>
        <p class="text-xs text-gray-500 mt-3">Showing data from {{ $startDate }} to {{ $endDate }}</p>
    </div>

    {{-- Critical Alerts --}}
    @if(isset($criticalAlerts) && array_sum($criticalAlerts) > 0)
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @if(($criticalAlerts['pendingOrders'] ?? 0) > 0)
        <a href="{{ route('admin.orders.index') }}" class="bg-gradient-to-r from-orange-50 to-orange-100 border-l-4 border-orange-500 p-4 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-orange-600 uppercase">Pending Orders</p>
                    <p class="text-2xl font-bold text-orange-900 mt-1">{{ $criticalAlerts['pendingOrders'] }}</p>
                </div>
                <i class="fas fa-clock text-3xl text-orange-400"></i>
            </div>
        </a>
        @endif

        @if(($criticalAlerts['lowStock'] ?? 0) > 0)
        <a href="{{ route('admin.stock.index') }}" class="bg-gradient-to-r from-yellow-50 to-yellow-100 border-l-4 border-yellow-500 p-4 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-yellow-600 uppercase">Low Stock</p>
                    <p class="text-2xl font-bold text-yellow-900 mt-1">{{ $criticalAlerts['lowStock'] }}</p>
                </div>
                <i class="fas fa-exclamation-triangle text-3xl text-yellow-400"></i>
            </div>
        </a>
        @endif

        @if(($criticalAlerts['pendingReviews'] ?? 0) > 0)
        <a href="{{ route('admin.reviews.index') }}" class="bg-gradient-to-r from-purple-50 to-purple-100 border-l-4 border-purple-500 p-4 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-purple-600 uppercase">Pending Reviews</p>
                    <p class="text-2xl font-bold text-purple-900 mt-1">{{ $criticalAlerts['pendingReviews'] }}</p>
                </div>
                <i class="fas fa-star-half-alt text-3xl text-purple-400"></i>
            </div>
        </a>
        @endif

        @if(($criticalAlerts['activeStockAlerts'] ?? 0) > 0)
        <a href="{{ route('admin.stock.alerts') }}" class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 p-4 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-red-600 uppercase">Stock Alerts</p>
                    <p class="text-2xl font-bold text-red-900 mt-1">{{ $criticalAlerts['activeStockAlerts'] }}</p>
                </div>
                <i class="fas fa-bell text-3xl text-red-400"></i>
            </div>
        </a>
        @endif
    </div>
    @endif

    {{-- Main Statistics Cards (Simpler Design, Navigatable) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        {{-- Revenue Card --}}
        @isset($totalRevenue)
        <a href="{{ route('admin.orders.index') }}?status=delivered" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-5 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-xl text-emerald-600"></i>
                </div>
                <span class="text-xs font-medium text-gray-500">Revenue</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mb-1">৳{{ number_format($totalRevenue, 2) }}</p>
            <p class="text-xs text-gray-600">Delivered orders only</p>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-emerald-600 font-medium">Today: ৳{{ number_format($todayRevenue ?? 0, 2) }}</span>
            </div>
        </a>
        @endisset

        {{-- Orders Card --}}
        @isset($totalOrders)
        <a href="{{ route('admin.orders.index') }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-xl text-blue-600"></i>
                </div>
                <span class="text-xs font-medium text-gray-500">Orders</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($totalOrders) }}</p>
            <p class="text-xs text-gray-600">In selected date range</p>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-blue-600 font-medium">Delivered: {{ $completedOrders ?? 0 }}</span>
            </div>
        </a>
        @endisset

        {{-- Products Card --}}
        @isset($totalProducts)
        <a href="{{ route('admin.products.index') }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-5 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-xl text-purple-600"></i>
                </div>
                <span class="text-xs font-medium text-gray-500">Products</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($totalProducts) }}</p>
            <p class="text-xs text-gray-600">Total products</p>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-purple-600 font-medium">Published: {{ $activeProducts ?? 0 }}</span>
            </div>
        </a>
        @endisset

        {{-- Customers Card --}}
        @isset($totalCustomers)
        <a href="{{ route('admin.users.index') }}?role=customer" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-5 border-l-4 border-amber-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-xl text-amber-600"></i>
                </div>
                <span class="text-xs font-medium text-gray-500">Customers</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($totalCustomers) }}</p>
            <p class="text-xs text-gray-600">Total customers</p>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-amber-600 font-medium">Active: {{ $activeCustomers ?? 0 }}</span>
            </div>
        </a>
        @endisset
    </div>

    {{-- Secondary Mini Cards (Navigatable) --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        @isset($pendingOrders)
        <a href="{{ route('admin.orders.index') }}?status=pending" class="bg-white rounded-lg shadow hover:shadow-md transition p-4 border-l-4 border-orange-400">
            <p class="text-xs font-medium text-gray-600">Pending Orders</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $pendingOrders }}</p>
        </a>
        @endisset

        @isset($lowStockProducts)
        <a href="{{ route('admin.products.index') }}?stock=low" class="bg-white rounded-lg shadow hover:shadow-md transition p-4 border-l-4 border-yellow-400">
            <p class="text-xs font-medium text-gray-600">Low Stock</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $lowStockProducts }}</p>
        </a>
        @endisset

        @isset($totalPosts)
        <a href="{{ route('admin.blog.posts.index') }}" class="bg-white rounded-lg shadow hover:shadow-md transition p-4 border-l-4 border-indigo-400">
            <p class="text-xs font-medium text-gray-600">Blog Posts</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $totalPosts }}</p>
        </a>
        @endisset

        @isset($totalReviews)
        <a href="{{ route('admin.reviews.index') }}" class="bg-white rounded-lg shadow hover:shadow-md transition p-4 border-l-4 border-yellow-400">
            <p class="text-xs font-medium text-gray-600">Reviews</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $totalReviews }}</p>
        </a>
        @endisset

        @isset($totalCategories)
        <a href="{{ route('admin.categories.index') }}" class="bg-white rounded-lg shadow hover:shadow-md transition p-4 border-l-4 border-teal-400">
            <p class="text-xs font-medium text-gray-600">Categories</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $totalCategories }}</p>
        </a>
        @endisset

        @isset($totalBrands)
        <a href="{{ route('admin.brands.index') }}" class="bg-white rounded-lg shadow hover:shadow-md transition p-4 border-l-4 border-cyan-400">
            <p class="text-xs font-medium text-gray-600">Brands</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $totalBrands }}</p>
        </a>
        @endisset
    </div>

    {{-- Charts and Tables Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Order Status Chart --}}
        @isset($orderStatusChart)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Status Distribution</h3>
            <div class="space-y-3">
                @foreach($orderStatusChart as $status)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $status['status'] }}</span>
                        <span class="text-sm font-bold" style="color: {{ $status['color'] }}">{{ $status['count'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all" style="background-color: {{ $status['color'] }}; width: {{ $totalOrders > 0 ? ($status['count'] / $totalOrders * 100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endisset

        {{-- Recent Orders --}}
        @isset($recentOrders)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentOrders as $order)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">#{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-600">{{ $order->user->name ?? 'Guest' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">৳{{ number_format($order->total_amount, 2) }}</p>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded
                            @if($order->status === 'completed') bg-green-100 text-green-800
                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No recent orders</p>
                @endforelse
            </div>
        </div>
        @endisset
    </div>

    {{-- Sales Chart & Top Products --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Sales Chart --}}
        @isset($salesChart)
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Sales Overview
                <span class="text-xs font-normal text-gray-500">({{ $startDate }} to {{ $endDate }})</span>
            </h3>
            @if(count($salesChart) > 0)
            <div class="overflow-x-auto">
                <div class="flex gap-2 items-end min-w-full" style="height: 200px;">
                    @php
                        $maxRevenue = collect($salesChart)->max('revenue');
                        $maxRevenue = $maxRevenue > 0 ? $maxRevenue : 1;
                        $chartCount = count($salesChart);
                        $barWidth = $chartCount > 15 ? 'w-6' : ($chartCount > 7 ? 'w-10' : 'w-12');
                    @endphp
                    @foreach($salesChart as $day)
                    <div class="flex flex-col items-center {{ $barWidth }} flex-shrink-0">
                        <div class="w-full bg-gradient-to-t from-blue-600 to-blue-400 rounded-t-lg transition-all hover:from-blue-700 hover:to-blue-500 cursor-pointer" 
                             style="height: {{ ($day['revenue'] / $maxRevenue * 180) }}px; min-height: 20px;"
                             title="Date: {{ $day['date'] }}&#10;Revenue: ৳{{ number_format($day['revenue'], 2) }}&#10;Orders: {{ $day['orders'] }}">
                        </div>
                        <p class="text-xs text-gray-600 mt-2 truncate w-full text-center">{{ $day['date'] }}</p>
                        <p class="text-xs font-semibold text-gray-900">{{ $day['orders'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <p class="text-sm text-gray-500 text-center py-8">No sales data in this date range</p>
            @endif
        </div>
        @endisset

        {{-- Top Products --}}
        @isset($topProducts)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Selling Products</h3>
            <div class="space-y-3">
                @forelse($topProducts as $product)
                <div class="flex items-center space-x-3 p-2 rounded hover:bg-gray-50">
                    @php
                        $imageUrl = $product->getPrimaryThumbnailUrl();
                    @endphp
                    @if($imageUrl)
                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-10 h-10 rounded object-cover">
                    @else
                    <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                        <i class="fas fa-box text-gray-400"></i>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                        <p class="text-xs text-gray-600">{{ $product->sales_count }} sales</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No sales data in this date range</p>
                @endforelse
            </div>
        </div>
        @endisset
    </div>

    {{-- Customer Statistics Section --}}
    @isset($recentCustomers)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Customer Stats Summary --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Overview</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg">
                    <div>
                        <p class="text-xs text-gray-600">Total Customers</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCustomers ?? 0 }}</p>
                    </div>
                    <i class="fas fa-users text-2xl text-amber-600"></i>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div>
                        <p class="text-xs text-gray-600">Active Customers</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $activeCustomers ?? 0 }}</p>
                    </div>
                    <i class="fas fa-user-check text-2xl text-green-600"></i>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div>
                        <p class="text-xs text-gray-600">New in Range</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $newCustomersInRange ?? 0 }}</p>
                    </div>
                    <i class="fas fa-user-plus text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        {{-- Recent Customers List --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Customers</h3>
                <a href="{{ route('admin.users.index') }}?role=customer" class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th class="text-left text-xs font-semibold text-gray-600 pb-2">Customer</th>
                            <th class="text-left text-xs font-semibold text-gray-600 pb-2">Email</th>
                            <th class="text-left text-xs font-semibold text-gray-600 pb-2">Joined</th>
                            <th class="text-left text-xs font-semibold text-gray-600 pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCustomers as $customer)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-semibold text-amber-600">{{ substr($customer->name, 0, 1) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-gray-600">{{ $customer->email }}</td>
                            <td class="py-3 text-xs text-gray-500">{{ $customer->created_at->format('M d, Y') }}</td>
                            <td class="py-3">
                                @if($customer->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-sm text-gray-500">No customers yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endisset

    {{-- Quick Actions --}}
    <div class="bg-gray-50 rounded-lg shadow p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @if(auth()->user()->hasPermission('products.create'))
            <a href="{{ route('admin.products.create') }}" class="flex flex-col items-center p-3 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                <i class="fas fa-plus-circle text-2xl text-blue-600 mb-2"></i>
                <span class="text-xs font-medium text-gray-700 text-center">Add Product</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('orders.create'))
            <a href="{{ route('admin.orders.create') }}" class="flex flex-col items-center p-3 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                <i class="fas fa-shopping-bag text-2xl text-green-600 mb-2"></i>
                <span class="text-xs font-medium text-gray-700 text-center">New Order</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('posts.create'))
            <a href="{{ route('admin.blog.posts.create') }}" class="flex flex-col items-center p-3 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                <i class="fas fa-pen text-2xl text-purple-600 mb-2"></i>
                <span class="text-xs font-medium text-gray-700 text-center">Write Post</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('categories.create'))
            <a href="{{ route('admin.categories.create') }}" class="flex flex-col items-center p-3 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                <i class="fas fa-tags text-2xl text-orange-600 mb-2"></i>
                <span class="text-xs font-medium text-gray-700 text-center">Add Category</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('coupons.create'))
            <a href="{{ route('admin.coupons.create') }}" class="flex flex-col items-center p-3 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                <i class="fas fa-ticket-alt text-2xl text-pink-600 mb-2"></i>
                <span class="text-xs font-medium text-gray-700 text-center">Add Coupon</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('users.create'))
            <a href="{{ route('admin.users.create') }}" class="flex flex-col items-center p-3 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                <i class="fas fa-user-plus text-2xl text-teal-600 mb-2"></i>
                <span class="text-xs font-medium text-gray-700 text-center">Add User</span>
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
