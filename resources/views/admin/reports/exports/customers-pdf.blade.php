<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Report</title>
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
            width: 33.33%;
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
        .badge-vip {
            background-color: #7c3aed;
            color: #fff;
        }
        .badge-gold {
            background-color: #fbbf24;
            color: #fff;
        }
        .badge-silver {
            background-color: #9ca3af;
            color: #fff;
        }
        .badge-regular {
            background-color: #6b7280;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Customer Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        <p>Generated: {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <h3>Total Customers</h3>
            <p>{{ number_format($customers->count()) }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Revenue</h3>
            <p>{{ currency_format($customers->sum('total_spent')) }}</p>
        </div>
        <div class="summary-card">
            <h3>Avg Order Value</h3>
            <p>{{ currency_format($customers->avg('avg_order_value')) }}</p>
        </div>
    </div>

    <!-- Customer List -->
    <div class="section">
        <div class="section-title">Customer Details</div>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th width="25%">Customer Name</th>
                    <th width="20%">Email</th>
                    <th width="10%" class="text-center">Segment</th>
                    <th width="10%" class="text-right">Orders</th>
                    <th width="15%" class="text-right">Total Spent</th>
                    <th width="15%" class="text-right">Avg Order</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $index => $customer)
                @php
                    if($customer->total_spent >= 50000) {
                        $segment = 'VIP';
                        $badgeClass = 'badge-vip';
                    } elseif($customer->total_spent >= 20000) {
                        $segment = 'Gold';
                        $badgeClass = 'badge-gold';
                    } elseif($customer->total_spent >= 5000) {
                        $segment = 'Silver';
                        $badgeClass = 'badge-silver';
                    } else {
                        $segment = 'Regular';
                        $badgeClass = 'badge-regular';
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ $segment }}</span>
                    </td>
                    <td class="text-right">{{ number_format($customer->total_orders) }}</td>
                    <td class="text-right">{{ currency_format($customer->total_spent) }}</td>
                    <td class="text-right">{{ currency_format($customer->avg_order_value) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No customer data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Segmentation Summary -->
    <div class="section">
        <div class="section-title">Customer Segmentation Summary</div>
        <table>
            <thead>
                <tr>
                    <th width="30%">Segment</th>
                    <th width="25%" class="text-center">Criteria</th>
                    <th width="15%" class="text-right">Customers</th>
                    <th width="30%" class="text-right">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $vipCustomers = $customers->where('total_spent', '>=', 50000);
                    $goldCustomers = $customers->whereBetween('total_spent', [20000, 49999]);
                    $silverCustomers = $customers->whereBetween('total_spent', [5000, 19999]);
                    $regularCustomers = $customers->where('total_spent', '<', 5000);
                @endphp
                <tr>
                    <td><span class="badge badge-vip">VIP</span></td>
                    <td class="text-center">≥ {{ currency_format(50000) }}</td>
                    <td class="text-right">{{ number_format($vipCustomers->count()) }}</td>
                    <td class="text-right">{{ currency_format($vipCustomers->sum('total_spent')) }}</td>
                </tr>
                <tr>
                    <td><span class="badge badge-gold">Gold</span></td>
                    <td class="text-center">{{ currency_format(20000) }} - {{ currency_format(49999) }}</td>
                    <td class="text-right">{{ number_format($goldCustomers->count()) }}</td>
                    <td class="text-right">{{ currency_format($goldCustomers->sum('total_spent')) }}</td>
                </tr>
                <tr>
                    <td><span class="badge badge-silver">Silver</span></td>
                    <td class="text-center">{{ currency_format(5000) }} - {{ currency_format(19999) }}</td>
                    <td class="text-right">{{ number_format($silverCustomers->count()) }}</td>
                    <td class="text-right">{{ currency_format($silverCustomers->sum('total_spent')) }}</td>
                </tr>
                <tr>
                    <td><span class="badge badge-regular">Regular</span></td>
                    <td class="text-center">< {{ currency_format(5000) }}</td>
                    <td class="text-right">{{ number_format($regularCustomers->count()) }}</td>
                    <td class="text-right">{{ currency_format($regularCustomers->sum('total_spent')) }}</td>
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
