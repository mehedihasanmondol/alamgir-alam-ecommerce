<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .header { text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #333; }
        .header h1 { font-size: 32px; margin-bottom: 10px; }
        .invoice-info { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .invoice-info div { flex: 1; }
        .invoice-info h3 { font-size: 16px; margin-bottom: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .totals { margin-left: auto; width: 300px; }
        .totals table { margin-bottom: 0; }
        .totals .grand-total { font-size: 18px; font-weight: bold; background-color: #f8f9fa; }
        .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print Button -->
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Print Invoice
            </button>
        </div>

        <!-- Header -->
        <div class="header">
            <h1>INVOICE</h1>
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>Your Business Address Here</p>
            <p>Phone: +880 XXX-XXXXXX | Email: info@example.com</p>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div>
                <h3>Invoice To:</h3>
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p>{{ $order->customer_email }}</p>
                <p>{{ $order->customer_phone }}</p>
                @if($order->shippingAddress)
                    <p style="margin-top: 10px;">{{ $order->shippingAddress->formatted_address }}</p>
                @endif
            </div>
            <div style="text-align: right;">
                <h3>Invoice Details:</h3>
                <p><strong>Invoice #:</strong> {{ $order->order_number }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                <p><strong>Payment:</strong> {{ ucfirst($order->payment_status) }}</p>
            </div>
        </div>

        <!-- Order Items -->
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>SKU</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->variant_name)
                                <br><small>{{ $item->variant_name }}</small>
                            @endif
                        </td>
                        <td>{{ $item->product_sku ?? '-' }}</td>
                        <td class="text-right">৳{{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">৳{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">৳{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->tax_amount > 0)
                    <tr>
                        <td>Tax:</td>
                        <td class="text-right">৳{{ number_format($order->tax_amount, 2) }}</td>
                    </tr>
                @endif
                @if($order->shipping_cost > 0)
                    <tr>
                        <td>Shipping:</td>
                        <td class="text-right">৳{{ number_format($order->shipping_cost, 2) }}</td>
                    </tr>
                @endif
                @if($order->discount_amount > 0)
                    <tr>
                        <td>Discount:</td>
                        <td class="text-right" style="color: #dc2626;">-৳{{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                @endif
                @if($order->coupon_discount > 0)
                    <tr>
                        <td>Coupon Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif:</td>
                        <td class="text-right" style="color: #dc2626;">-৳{{ number_format($order->coupon_discount, 2) }}</td>
                    </tr>
                @endif
                <tr class="grand-total">
                    <td><strong>Grand Total:</strong></td>
                    <td class="text-right"><strong>৳{{ number_format($order->total_amount, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p style="margin-top: 10px; font-size: 12px;">
                This is a computer-generated invoice and does not require a signature.
            </p>
        </div>
    </div>
</body>
</html>
