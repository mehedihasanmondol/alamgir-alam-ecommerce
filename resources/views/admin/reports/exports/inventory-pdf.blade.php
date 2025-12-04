<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventory Report</h1>
        <p>Generated: {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <!-- All Products Inventory -->
    <div class="section">
        <div class="section-title">Product Inventory</div>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th width="30%">Product Name</th>
                    <th width="20%">Category</th>
                    <th width="15%">Brand</th>
                    <th width="10%" class="text-right">Total Stock</th>
                    <th width="10%" class="text-right">Variants</th>
                    <th width="10%" class="text-right">Avg Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventory as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category_name ?? 'N/A' }}</td>
                    <td>{{ $item->brand_name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($item->total_stock) }}</td>
                    <td class="text-right">{{ number_format($item->variant_count) }}</td>
                    <td class="text-right">{{ currency_format($item->avg_price) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No inventory data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Low Stock Alert -->
    <div class="section">
        <div class="section-title">Low Stock Alert (≤10 units)</div>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th width="35%">Product Name</th>
                    <th width="20%">Variant SKU</th>
                    <th width="15%">Category</th>
                    <th width="15%" class="text-right">Stock Quantity</th>
                    <th width="10%" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStock as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->variant_sku }}</td>
                    <td>{{ $item->category_name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($item->stock_quantity) }}</td>
                    <td class="text-center">
                        <span class="badge badge-warning">Low Stock</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No low stock products</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Out of Stock -->
    <div class="section">
        <div class="section-title">Out of Stock Products</div>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th width="40%">Product Name</th>
                    <th width="25%">Variant SKU</th>
                    <th width="20%">Category</th>
                    <th width="10%" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($outOfStock as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->variant_sku }}</td>
                    <td>{{ $item->category_name ?? 'N/A' }}</td>
                    <td class="text-center">
                        <span class="badge badge-danger">Out of Stock</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No out of stock products</td>
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
