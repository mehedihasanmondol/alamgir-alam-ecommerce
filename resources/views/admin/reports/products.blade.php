@extends('layouts.admin')

@section('title', 'Product Performance Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Product Performance Report</h1>
            <p class="text-gray-600 mt-1">Analyze product sales and performance</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                ← Back to Dashboard
            </a>
            <a href="{{ route('admin.reports.export-products-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.reports.products') }}" class="flex items-end space-x-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Apply Filter
            </button>
            <a href="{{ route('admin.reports.products') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-sm mb-6" x-data="{ tab: 'top' }">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="tab = 'top'" 
                        :class="tab === 'top' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    Top Sellers ({{ $topProducts->count() }})
                </button>
                <button @click="tab = 'performance'" 
                        :class="tab === 'performance' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    All Products ({{ $productPerformance->count() }})
                </button>
                <button @click="tab = 'category'" 
                        :class="tab === 'category' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    By Category ({{ $categoryPerformance->count() }})
                </button>
            </nav>
        </div>

        <!-- Top Sellers Tab -->
        <div x-show="tab === 'top'" class="p-6">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900">Top 50 Best Selling Products</h3>
                <p class="text-sm text-gray-600">Ranked by total revenue generated</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Units Sold</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Revenue</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Orders</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avg Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($topProducts as $index => $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                    {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : 
                                       ($index === 1 ? 'bg-gray-200 text-gray-700' : 
                                       ($index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700')) }}">
                                    {{ $index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $product->slug }}</p>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-blue-600">
                                {{ number_format($product->total_quantity) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-green-600">
                                {{ currency_format($product->total_revenue) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-900">
                                {{ number_format($product->order_count) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-900">
                                {{ currency_format($product->total_revenue / $product->total_quantity) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No products sold in this period</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- All Products Performance Tab -->
        <div x-show="tab === 'performance'" class="p-6">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900">All Product Performance</h3>
                <p class="text-sm text-gray-600">Complete sales data for all products</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Brand</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Units Sold</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Orders</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($productPerformance as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $product->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $product->category_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $product->brand_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right font-medium text-blue-600">
                                {{ number_format($product->units_sold) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-green-600">
                                {{ currency_format($product->revenue) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-900">
                                {{ number_format($product->order_count) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No products sold in this period</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Category Performance Tab -->
        <div x-show="tab === 'category'" class="p-6">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900">Performance by Category</h3>
                <p class="text-sm text-gray-600">Sales aggregated by product categories</p>
            </div>
            
            <!-- Category Chart -->
            <div class="mb-6 relative" style="height: 300px;">
                <canvas id="categoryChart"></canvas>
            </div>

            <!-- Category Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Products</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Units Sold</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Orders</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avg/Product</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($categoryPerformance as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $category->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-900">
                                {{ number_format($category->product_count) }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-blue-600">
                                {{ number_format($category->units_sold) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-green-600">
                                {{ currency_format($category->revenue) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-900">
                                {{ number_format($category->order_count) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-900">
                                {{ currency_format($category->revenue / $category->product_count) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No category data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js for tabs -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Category Performance Chart
const ctx = document.getElementById('categoryChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($categoryPerformance->pluck('name')) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($categoryPerformance->pluck('revenue')) !!},
            backgroundColor: 'rgba(139, 92, 246, 0.7)',
            borderColor: 'rgb(139, 92, 246)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '{{ currency_symbol() }}' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endsection
