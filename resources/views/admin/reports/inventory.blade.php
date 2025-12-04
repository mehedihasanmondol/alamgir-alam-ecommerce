@extends('layouts.admin')

@section('title', 'Inventory Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Inventory Report</h1>
            <p class="text-gray-600 mt-1">Stock levels and inventory management</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                ← Back to Dashboard
            </a>
            <a href="{{ route('admin.reports.export-inventory-pdf') }}" 
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    <!-- Stock Alerts Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Products</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($inventory->count()) }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Low Stock</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($lowStock->count()) }}</h3>
                    <p class="text-yellow-100 text-xs mt-1">≤ 10 units</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Out of Stock</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($outOfStock->count()) }}</h3>
                    <p class="text-red-100 text-xs mt-1">0 units</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-sm mb-6" x-data="{ tab: 'all' }">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="tab = 'all'" 
                        :class="tab === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    All Products ({{ $inventory->count() }})
                </button>
                <button @click="tab = 'low'" 
                        :class="tab === 'low' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    Low Stock ({{ $lowStock->count() }})
                </button>
                <button @click="tab = 'out'" 
                        :class="tab === 'out' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    Out of Stock ({{ $outOfStock->count() }})
                </button>
            </nav>
        </div>

        <!-- All Products Tab -->
        <div x-show="tab === 'all'" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Brand</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Variants</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avg Price</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($inventory as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $item->name }}</p>
                            <p class="text-xs text-gray-500">ID: #{{ $item->id }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->category_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->brand_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-right">
                            <span class="font-bold {{ $item->total_stock <= 0 ? 'text-red-600' : ($item->total_stock <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ number_format($item->total_stock) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">{{ $item->variant_count }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">{{ currency_format($item->avg_price) }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($item->total_stock <= 0)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Out of Stock</span>
                            @elseif($item->total_stock <= 10)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No products found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Low Stock Tab -->
        <div x-show="tab === 'low'" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-yellow-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($lowStock as $item)
                    <tr class="hover:bg-yellow-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $item->name }}</p>
                            <p class="text-xs text-gray-500">ID: #{{ $item->id }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->variant_sku }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->category_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-right">
                            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 font-bold text-sm">
                                {{ $item->stock_quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">{{ currency_format($item->price) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No low stock products</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Out of Stock Tab -->
        <div x-show="tab === 'out'" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($outOfStock as $item)
                    <tr class="hover:bg-red-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $item->name }}</p>
                            <p class="text-xs text-gray-500">ID: #{{ $item->id }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->variant_sku }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->category_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">{{ currency_format($item->price) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No out of stock products</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
