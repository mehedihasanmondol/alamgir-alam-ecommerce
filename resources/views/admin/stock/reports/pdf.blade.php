<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333;}
        .header {text-align: center; padding: 20px 0; border-bottom: 3px solid #2563eb; margin-bottom: 20px;}
        .header h1 {font-size: 24px; color: #1e40af; margin-bottom: 5px;}
        .header p {font-size: 11px; color: #666;}
        .filters {background: #f3f4f6; padding: 10px; margin-bottom: 20px; border-radius: 5px;}
        .filters h3 {font-size: 12px; margin-bottom: 8px; color: #374151;}
        .filters table {width: 100%; font-size: 10px;}
        .filters td {padding: 3px 5px;}
        .filters td:first-child {font-weight: bold; width: 100px;}
        .summary {display: table; width: 100%; margin-bottom: 20px;}
        .summary-card {display: table-cell; width: 16.66%; padding: 10px; text-align: center; background: #eff6ff; border: 1px solid #bfdbfe;}
        .summary-card .label {font-size: 9px; color: #1e40af; text-transform: uppercase; margin-bottom: 5px;}
        .summary-card .value {font-size: 18px; font-weight: bold; color: #1e3a8a;}
        table {width: 100%; border-collapse: collapse; margin-top: 15px;}
        thead {background: #2563eb; color: white;}
        th {padding: 8px 5px; text-align: left; font-size: 10px; font-weight: 600;}
        td {padding: 6px 5px; border-bottom: 1px solid #e5e7eb; font-size: 9px;}
        tr:nth-child(even) {background-color: #f9fafb;}
        .badge {padding: 3px 8px; border-radius: 12px; font-size: 8px; font-weight: 600; display: inline-block;}
        .badge-green {background: #d1fae5; color: #065f46;}
        .badge-yellow {background: #fef3c7; color: #92400e;}
        .badge-red {background: #fee2e2; color: #991b1b;}
        .text-right {text-align: right;}
        .footer {position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #666; padding: 10px 0; border-top: 1px solid #e5e7eb;}
        .page-break {page-break-after: always;}
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📊 Stock Report</h1>
        <p>Generated on {{ now()->format('F d, Y \a\t H:i:s') }}</p>
    </div>

    <!-- Filters Applied -->
    <div class="filters">
        <h3>📋 Filters Applied:</h3>
        <table>
            <tr>
                <td>Category:</td>
                <td>{{ $filters['category'] }}</td>
                <td>Brand:</td>
                <td>{{ $filters['brand'] }}</td>
            </tr>
            <tr>
                <td>Stock Status:</td>
                <td>{{ $filters['stock_status'] }}</td>
                <td>Search:</td>
                <td>{{ $filters['search'] }}</td>
            </tr>
        </table>
    </div>

    <!-- Summary Statistics -->
    <div class="summary">
        <div class="summary-card">
            <div class="label">Total Products</div>
            <div class="value">{{ number_format($summary['total_products']) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Quantity</div>
            <div class="value">{{ number_format($summary['total_quantity']) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">In Stock</div>
            <div class="value">{{ number_format($summary['in_stock_count']) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Low Stock</div>
            <div class="value">{{ number_format($summary['low_stock_count']) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Out of Stock</div>
            <div class="value">{{ number_format($summary['out_of_stock_count']) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Stock Value</div>
            <div class="value">${{ number_format($summary['total_stock_value'], 2) }}</div>
        </div>
    </div>

    <!-- Stock Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Product</th>
                <th style="width: 10%;">SKU</th>
                <th style="width: 15%;">Variation</th>
                <th style="width: 15%;">Category</th>
                <th style="width: 10%;">Brand</th>
                <th style="width: 8%;" class="text-right">Stock</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 12%;" class="text-right">Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $stock)
                <tr>
                    <td>{{ $stock->product_name }}</td>
                    <td>{{ $stock->sku }}</td>
                    <td>{{ $stock->variation_name ?? '-' }}</td>
                    <td>{{ $stock->category_names ?? '-' }}</td>
                    <td>{{ $stock->brand_name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($stock->stock_quantity) }}</td>
                    <td>
                        @php
                            $statusClass = match(true) {
                                $stock->stock_quantity <= 0 => 'badge-red',
                                $stock->stock_quantity <= $stock->low_stock_alert => 'badge-yellow',
                                default => 'badge-green'
                            };
                            $statusText = match(true) {
                                $stock->stock_quantity <= 0 => 'Out of Stock',
                                $stock->stock_quantity <= $stock->low_stock_alert => 'Low Stock',
                                default => 'In Stock'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                    <td class="text-right">${{ number_format($stock->stock_value, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Stock Report | Generated by {{ config('app.name') }} | Page <span class="pagenum"></span></p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $size = 9;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
