<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
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
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        <p>Generated: {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <h3>Total Revenue</h3>
            <p>{{ currency_format($salesReport['summary']['total_revenue']) }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Orders</h3>
            <p>{{ number_format($salesReport['summary']['total_orders']) }}</p>
        </div>
        <div class="summary-card">
            <h3>Avg Order Value</h3>
            <p>{{ currency_format($salesReport['summary']['avg_order_value']) }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Discounts</h3>
            <p>{{ currency_format($salesReport['summary']['total_discounts']) }}</p>
        </div>
    </div>

    <!-- Sales Data Table -->
    <table>
        <thead>
            <tr>
                <th>Period</th>
                <th class="text-right">Orders</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Shipping</th>
                <th class="text-right">Discounts</th>
                <th class="text-right">Total Revenue</th>
                <th class="text-right">Avg Order</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesReport['period_data'] as $data)
            <tr>
                <td>{{ $data->period }}</td>
                <td class="text-right">{{ number_format($data->total_orders) }}</td>
                <td class="text-right">{{ currency_format($data->subtotal) }}</td>
                <td class="text-right">{{ currency_format($data->shipping_revenue) }}</td>
                <td class="text-right">{{ currency_format($data->total_discounts) }}</td>
                <td class="text-right">{{ currency_format($data->total_revenue) }}</td>
                <td class="text-right">{{ currency_format($data->avg_order_value) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No sales data available</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report is confidential and intended for internal use only.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
