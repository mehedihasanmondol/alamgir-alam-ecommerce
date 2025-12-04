<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Performance Report</title>
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
        .badge-gold {
            background-color: #fbbf24;
            color: #fff;
        }
        .badge-silver {
            background-color: #9ca3af;
            color: #fff;
        }
        .badge-bronze {
            background-color: #d97706;
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
        <h1>Product Performance Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        <p>Generated: {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <!-- Top Selling Products -->
    <div class="section">
        <div class="section-title">Top Selling Products</div>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">Rank</th>
                    <th width="40%">Product Name</th>
                    <th width="15%" class="text-right">Units Sold</th>
                    <th width="15%" class="text-right">Orders</th>
                    <th width="15%" class="text-right">Revenue</th>
                    <th width="10%" class="text-right">Avg Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $index => $product)
                <tr>
                    <td class="text-center">
                        @if($index == 0)
                            <span class="badge badge-gold">🥇 #1</span>
                        @elseif($index == 1)
                            <span class="badge badge-silver">🥈 #2</span>
                        @elseif($index == 2)
                            <span class="badge badge-bronze">🥉 #3</span>
                        @else
                            #{{ $index + 1 }}
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td class="text-right">{{ number_format($product->total_quantity) }}</td>
                    <td class="text-right">{{ number_format($product->order_count) }}</td>
                    <td class="text-right">{{ currency_format($product->total_revenue) }}</td>
                    <td class="text-right">{{ currency_format($product->avg_price) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No product data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Category Performance -->
    <div class="section">
        <div class="section-title">Category Performance</div>
        <table>
            <thead>
                <tr>
                    <th width="35%">Category</th>
                    <th width="15%" class="text-right">Products</th>
                    <th width="15%" class="text-right">Units Sold</th>
                    <th width="15%" class="text-right">Orders</th>
                    <th width="20%" class="text-right">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categoryPerformance as $category)
                <tr>
                    <td>{{ $category->category_name ?? 'Uncategorized' }}</td>
                    <td class="text-right">{{ number_format($category->product_count) }}</td>
                    <td class="text-right">{{ number_format($category->total_quantity) }}</td>
                    <td class="text-right">{{ number_format($category->order_count) }}</td>
                    <td class="text-right">{{ currency_format($category->total_revenue) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No category data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This report is confidential and intended for internal use only.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
