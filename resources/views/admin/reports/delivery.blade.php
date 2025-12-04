@extends('layouts.admin')

@section('title', 'Delivery Zone Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Delivery Zone Report</h1>
            <p class="text-gray-600 mt-1">Logistics performance and shipping analysis</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                ← Back to Dashboard
            </a>
            <a href="{{ route('admin.reports.export-delivery-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
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
        <form method="GET" action="{{ route('admin.reports.delivery') }}" class="flex items-end space-x-4">
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
            <a href="{{ route('admin.reports.delivery') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-blue-100 text-sm font-medium">Total Orders</p>
            <h3 class="text-3xl font-bold mt-2">{{ number_format($deliveryZones->sum('order_count')) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-green-100 text-sm font-medium">Total Revenue</p>
            <h3 class="text-3xl font-bold mt-2">{{ currency_format($deliveryZones->sum('total_revenue')) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-purple-100 text-sm font-medium">Shipping Revenue</p>
            <h3 class="text-3xl font-bold mt-2">{{ currency_format($deliveryZones->sum('shipping_revenue')) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-orange-100 text-sm font-medium">Avg Shipping Cost</p>
            <h3 class="text-3xl font-bold mt-2">{{ currency_format($deliveryZones->avg('avg_shipping_cost')) }}</h3>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Orders by Zone Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Orders by Delivery Zone</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="zoneOrdersChart"></canvas>
            </div>
        </div>

        <!-- Shipping Revenue Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Shipping Revenue by Zone</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="shippingRevenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Delivery Zone Performance Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Delivery Zone Performance</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Zone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Method</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Shipping Revenue</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Shipping</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Shipping %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($deliveryZones as $zone)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $zone->delivery_zone_name ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ $zone->delivery_method_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-blue-600">
                            {{ number_format($zone->order_count) }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-green-600">
                            {{ currency_format($zone->total_revenue) }}
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-purple-600">
                            {{ currency_format($zone->shipping_revenue) }}
                        </td>
                        <td class="px-6 py-4 text-right text-gray-900">
                            {{ currency_format($zone->avg_shipping_cost) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="px-2 py-1 text-xs font-bold rounded-full 
                                {{ ($zone->shipping_revenue / $zone->total_revenue * 100) > 15 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ number_format(($zone->shipping_revenue / $zone->total_revenue * 100), 1) }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            No delivery data available for the selected period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($deliveryZones->count() > 0)
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td class="px-6 py-4" colspan="2">TOTAL</td>
                        <td class="px-6 py-4 text-right text-blue-600">
                            {{ number_format($deliveryZones->sum('order_count')) }}
                        </td>
                        <td class="px-6 py-4 text-right text-green-600">
                            {{ currency_format($deliveryZones->sum('total_revenue')) }}
                        </td>
                        <td class="px-6 py-4 text-right text-purple-600">
                            {{ currency_format($deliveryZones->sum('shipping_revenue')) }}
                        </td>
                        <td class="px-6 py-4 text-right text-gray-900">
                            {{ currency_format($deliveryZones->avg('avg_shipping_cost')) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            {{ number_format(($deliveryZones->sum('shipping_revenue') / $deliveryZones->sum('total_revenue') * 100), 1) }}%
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Insights Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Most Popular Zone -->
        @if($deliveryZones->count() > 0)
        @php
            $topZone = $deliveryZones->sortByDesc('order_count')->first();
        @endphp
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
            <div class="flex items-center mb-3">
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                </svg>
                <h4 class="font-bold text-blue-900">Most Popular Zone</h4>
            </div>
            <p class="text-2xl font-bold text-blue-900">{{ $topZone->delivery_zone_name }}</p>
            <p class="text-sm text-blue-700 mt-1">{{ number_format($topZone->order_count) }} orders</p>
        </div>

        <!-- Highest Revenue Zone -->
        @php
            $revenueZone = $deliveryZones->sortByDesc('total_revenue')->first();
        @endphp
        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-6">
            <div class="flex items-center mb-3">
                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h4 class="font-bold text-green-900">Highest Revenue Zone</h4>
            </div>
            <p class="text-2xl font-bold text-green-900">{{ $revenueZone->delivery_zone_name }}</p>
            <p class="text-sm text-green-700 mt-1">{{ currency_format($revenueZone->total_revenue) }}</p>
        </div>

        <!-- Most Expensive Shipping -->
        @php
            $expensiveZone = $deliveryZones->sortByDesc('avg_shipping_cost')->first();
        @endphp
        <div class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-6">
            <div class="flex items-center mb-3">
                <svg class="w-6 h-6 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                </svg>
                <h4 class="font-bold text-orange-900">Highest Shipping Cost</h4>
            </div>
            <p class="text-2xl font-bold text-orange-900">{{ $expensiveZone->delivery_zone_name }}</p>
            <p class="text-sm text-orange-700 mt-1">{{ currency_format($expensiveZone->avg_shipping_cost) }} avg</p>
        </div>
        @endif
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Group data by zone
const zoneData = {};
@foreach($deliveryZones as $zone)
    (function() {
        const zoneName = '{{ $zone->delivery_zone_name ?? "N/A" }}';
        if (!zoneData[zoneName]) {
            zoneData[zoneName] = { orders: 0, shipping: 0 };
        }
        zoneData[zoneName].orders += {{ $zone->order_count }};
        zoneData[zoneName].shipping += {{ $zone->shipping_revenue }};
    })();
@endforeach

const zoneNames = Object.keys(zoneData);
const orderCounts = zoneNames.map(name => zoneData[name].orders);
const shippingRevenues = zoneNames.map(name => zoneData[name].shipping);

// Orders by Zone Chart
const ctx1 = document.getElementById('zoneOrdersChart').getContext('2d');
new Chart(ctx1, {
    type: 'doughnut',
    data: {
        labels: zoneNames,
        datasets: [{
            data: orderCounts,
            backgroundColor: [
                'rgba(59, 130, 246, 0.7)',
                'rgba(16, 185, 129, 0.7)',
                'rgba(245, 158, 11, 0.7)',
                'rgba(239, 68, 68, 0.7)',
                'rgba(139, 92, 246, 0.7)',
                'rgba(236, 72, 153, 0.7)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});

// Shipping Revenue Chart
const ctx2 = document.getElementById('shippingRevenueChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: zoneNames,
        datasets: [{
            label: 'Shipping Revenue',
            data: shippingRevenues,
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
