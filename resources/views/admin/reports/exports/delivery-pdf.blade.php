<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Zone Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            background-color: #f3f4f6;
            border: 1px solid #ddd;
            text-align: center;
        }
        .summary-card h3 {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }
        .summary-card p {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1e40af;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f3f4f6;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #10b981;
            color: #fff;
        }
        .badge-warning {
            background-color: #f59e0b;
            color: #fff;
        }
        .badge-danger {
            background-color: #ef4444;
            color: #fff;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .performance-bar {
            display: inline-block;
            height: 10px;
            background-color: #10b981;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Delivery Zone Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        <p>Generated: {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <h3>Total Zones</h3>
            <p>{{ number_format($deliveryZones->count()) }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Orders</h3>
            <p>{{ number_format($deliveryZones->sum('total_orders')) }}</p>
        </div>
        <div class="summary-card">
            <h3>Shipping Revenue</h3>
            <p>{{ currency_format($deliveryZones->sum('total_shipping_revenue')) }}</p>
        </div>
        <div class="summary-card">
            <h3>Avg Shipping Cost</h3>
            <p>{{ currency_format($deliveryZones->avg('avg_shipping_cost')) }}</p>
        </div>
    </div>

    <!-- Delivery Zone Performance -->
    <div class="section">
        <div class="section-title">Delivery Zone Performance</div>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th width="25%">Zone Name</th>
                    <th width="15%" class="text-right">Total Orders</th>
                    <th width="15%" class="text-right">Shipping Revenue</th>
                    <th width="15%" class="text-right">Avg Cost</th>
                    <th width="15%" class="text-right">Min Cost</th>
                    <th width="10%" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveryZones as $index => $zone)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $zone->zone_name }}</td>
                    <td class="text-right">{{ number_format($zone->total_orders) }}</td>
                    <td class="text-right">{{ currency_format($zone->total_shipping_revenue) }}</td>
                    <td class="text-right">{{ currency_format($zone->avg_shipping_cost) }}</td>
                    <td class="text-right">{{ currency_format($zone->min_shipping_cost) }}</td>
                    <td class="text-center">
                        @if($zone->total_orders > 100)
                            <span class="badge badge-success">High</span>
                        @elseif($zone->total_orders > 50)
                            <span class="badge badge-warning">Medium</span>
                        @else
                            <span class="badge badge-danger">Low</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No delivery zone data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Zone Rankings -->
    <div class="section">
        <div class="section-title">Top Performing Zones</div>
        <table>
            <thead>
                <tr>
                    <th width="10%" class="text-center">Rank</th>
                    <th width="35%">Zone Name</th>
                    <th width="20%" class="text-right">Total Orders</th>
                    <th width="35%" class="text-right">Shipping Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryZones->sortByDesc('total_orders')->take(10) as $index => $zone)
                <tr>
                    <td class="text-center">
                        @if($index == 0)
                            🥇 #1
                        @elseif($index == 1)
                            🥈 #2
                        @elseif($index == 2)
                            🥉 #3
                        @else
                            #{{ $index + 1 }}
                        @endif
                    </td>
                    <td>{{ $zone->zone_name }}</td>
                    <td class="text-right">{{ number_format($zone->total_orders) }}</td>
                    <td class="text-right">{{ currency_format($zone->total_shipping_revenue) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Performance Analysis -->
    <div class="section">
        <div class="section-title">Performance Analysis</div>
        <table>
            <thead>
                <tr>
                    <th width="30%">Metric</th>
                    <th width="70%">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Most Active Zone</td>
                    <td>{{ $deliveryZones->sortByDesc('total_orders')->first()->zone_name ?? 'N/A' }} 
                        ({{ number_format($deliveryZones->sortByDesc('total_orders')->first()->total_orders ?? 0) }} orders)</td>
                </tr>
                <tr>
                    <td>Highest Revenue Zone</td>
                    <td>{{ $deliveryZones->sortByDesc('total_shipping_revenue')->first()->zone_name ?? 'N/A' }} 
                        ({{ currency_format($deliveryZones->sortByDesc('total_shipping_revenue')->first()->total_shipping_revenue ?? 0) }})</td>
                </tr>
                <tr>
                    <td>Most Expensive Delivery</td>
                    <td>{{ $deliveryZones->sortByDesc('avg_shipping_cost')->first()->zone_name ?? 'N/A' }} 
                        ({{ currency_format($deliveryZones->sortByDesc('avg_shipping_cost')->first()->avg_shipping_cost ?? 0) }} avg)</td>
                </tr>
                <tr>
                    <td>Most Affordable Delivery</td>
                    <td>{{ $deliveryZones->sortBy('avg_shipping_cost')->first()->zone_name ?? 'N/A' }} 
                        ({{ currency_format($deliveryZones->sortBy('avg_shipping_cost')->first()->avg_shipping_cost ?? 0) }} avg)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This report is confidential and intended for internal use only.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
