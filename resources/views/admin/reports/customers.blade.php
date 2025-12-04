@extends('layouts.admin')

@section('title', 'Customer Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Customer Report</h1>
            <p class="text-gray-600 mt-1">Customer insights and lifetime value analysis</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                ← Back to Dashboard
            </a>
            <a href="{{ route('admin.reports.export-customers-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
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
        <form method="GET" action="{{ route('admin.reports.customers') }}" class="flex items-end space-x-4">
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
            <a href="{{ route('admin.reports.customers') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-blue-100 text-sm font-medium">Total Customers</p>
            <h3 class="text-3xl font-bold mt-2">{{ number_format($customers->count()) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-green-100 text-sm font-medium">Total Revenue</p>
            <h3 class="text-3xl font-bold mt-2">{{ currency_format($customers->sum('total_spent')) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-purple-100 text-sm font-medium">Avg Customer Value</p>
            <h3 class="text-3xl font-bold mt-2">{{ currency_format($customers->avg('total_spent')) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <p class="text-orange-100 text-sm font-medium">Avg Orders/Customer</p>
            <h3 class="text-3xl font-bold mt-2">{{ number_format($customers->avg('total_orders'), 1) }}</h3>
        </div>
    </div>

    <!-- Customer Segmentation -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Customers Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Top 10 Customers by Revenue</h3>
            <div class="relative" style="height: 350px;">
                <canvas id="topCustomersChart"></canvas>
            </div>
        </div>

        <!-- Customer Distribution -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Customer Segments</h3>
            <div class="space-y-4">
                @php
                    $vipCustomers = $customers->where('total_spent', '>=', 50000)->count();
                    $goldCustomers = $customers->whereBetween('total_spent', [20000, 49999])->count();
                    $silverCustomers = $customers->whereBetween('total_spent', [5000, 19999])->count();
                    $regularCustomers = $customers->where('total_spent', '<', 5000)->count();
                @endphp
                
                <!-- VIP Customers -->
                <div class="p-4 bg-purple-50 border-l-4 border-purple-500 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-bold text-purple-900">VIP Customers</p>
                        <span class="px-3 py-1 bg-purple-600 text-white text-sm font-bold rounded-full">
                            {{ $vipCustomers }}
                        </span>
                    </div>
                    <p class="text-sm text-purple-700">Spent ≥ {{ currency_format(50000) }}</p>
                </div>

                <!-- Gold Customers -->
                <div class="p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-bold text-yellow-900">Gold Customers</p>
                        <span class="px-3 py-1 bg-yellow-600 text-white text-sm font-bold rounded-full">
                            {{ $goldCustomers }}
                        </span>
                    </div>
                    <p class="text-sm text-yellow-700">Spent {{ currency_format(20000) }} - {{ currency_format(49999) }}</p>
                </div>

                <!-- Silver Customers -->
                <div class="p-4 bg-gray-50 border-l-4 border-gray-400 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-bold text-gray-900">Silver Customers</p>
                        <span class="px-3 py-1 bg-gray-600 text-white text-sm font-bold rounded-full">
                            {{ $silverCustomers }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-700">Spent {{ currency_format(5000) }} - {{ currency_format(19999) }}</p>
                </div>

                <!-- Regular Customers -->
                <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-bold text-blue-900">Regular Customers</p>
                        <span class="px-3 py-1 bg-blue-600 text-white text-sm font-bold rounded-full">
                            {{ $regularCustomers }}
                        </span>
                    </div>
                    <p class="text-sm text-blue-700">Spent < {{ currency_format(5000) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">All Customers</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Order Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Order</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Segment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $customer->name }}</p>
                            <p class="text-sm text-gray-500">{{ $customer->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-blue-600">
                            {{ number_format($customer->total_orders) }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-green-600">
                            {{ currency_format($customer->total_spent) }}
                        </td>
                        <td class="px-6 py-4 text-right text-gray-900">
                            {{ currency_format($customer->avg_order_value) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($customer->last_order_date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($customer->total_spent >= 50000)
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-800">VIP</span>
                            @elseif($customer->total_spent >= 20000)
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800">Gold</span>
                            @elseif($customer->total_spent >= 5000)
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-gray-200 text-gray-800">Silver</span>
                            @else
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">Regular</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No customer data available for the selected period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Top Customers Chart
const ctx = document.getElementById('topCustomersChart').getContext('2d');
const topCustomers = {!! json_encode($customers->take(10)) !!};

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: topCustomers.map(c => c.name),
        datasets: [{
            label: 'Total Spent',
            data: topCustomers.map(c => c.total_spent),
            backgroundColor: 'rgba(59, 130, 246, 0.7)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 2
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
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
