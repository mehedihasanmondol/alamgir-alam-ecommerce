# Order Management System - Quick Setup Guide

## 🚀 Quick Start (5 Minutes)

### Step 1: Run Migrations
```bash
php artisan migrate
```

This creates 5 new tables:
- `orders`
- `order_items`
- `order_addresses`
- `order_status_histories`
- `order_payments`

### Step 2: Clear Application Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### Step 3: Verify Installation
```bash
# Check if routes are registered
php artisan route:list --name=orders

# You should see routes like:
# - admin.orders.index
# - admin.orders.show
# - customer.orders.index
# - orders.track
```

## ✅ System is Ready!

### Admin Access
Navigate to: `http://your-domain.com/admin/orders`

**Features Available:**
- View all orders with statistics
- Search and filter orders
- Update order status
- Edit order details
- Print invoices
- Cancel orders

### Customer Access
Navigate to: `http://your-domain.com/my/orders`

**Features Available:**
- View order history
- Track order status
- Download invoices
- Cancel pending orders

### Public Order Tracking
Navigate to: `http://your-domain.com/track-order`

**Features:**
- Track orders without login
- Requires order number + email

## 📊 Testing the System

### Create a Test Order (Manual)
```php
use App\Modules\Ecommerce\Order\Services\OrderService;
use App\Modules\Ecommerce\Product\Models\Product;

$orderService = app(OrderService::class);

// Get a test product
$product = Product::first();

$orderData = [
    'user_id' => 1, // Your test user ID
    'customer_name' => 'Test Customer',
    'customer_email' => 'test@example.com',
    'customer_phone' => '01712345678',
    'payment_method' => 'cod',
    'items' => [
        [
            'product' => $product,
            'variant' => null,
            'quantity' => 1,
        ],
    ],
    'billing_address' => [
        'first_name' => 'Test',
        'last_name' => 'Customer',
        'phone' => '01712345678',
        'address_line_1' => '123 Test Street',
        'city' => 'Dhaka',
        'postal_code' => '1200',
        'country' => 'Bangladesh',
    ],
    'shipping_cost' => 60,
];

$order = $orderService->createOrder($orderData);

echo "Order created: " . $order->order_number;
```

### Update Order Status
1. Go to `/admin/orders`
2. Click on an order
3. Use "Update Order Status" form
4. Select new status
5. Click "Update Status"

## 🔧 Configuration

### Shipping Costs
Edit: `app/Modules/Ecommerce/Order/Services/OrderCalculationService.php`

```php
public function calculateShippingCost(string $city, float $weight = 0): float
{
    // Inside Dhaka: 60 BDT
    // Outside Dhaka: 120 BDT
    
    $insideDhaka = ['dhaka', 'ঢাকা'];
    
    if (in_array(strtolower($city), $insideDhaka)) {
        return 60;
    }

    return 120;
}
```

### Tax Rate
Edit: `app/Modules/Ecommerce/Order/Services/OrderCalculationService.php`

```php
protected float $taxRate = 0; // Change to your tax rate (e.g., 5 for 5%)
```

### SMS/Email Notifications
Edit: `app/Modules/Ecommerce/Order/Services/OrderStatusService.php`

```php
protected function sendStatusNotification(Order $order, string $status): void
{
    // Implement your SMS/Email service here
    // Example:
    // app(SmsService::class)->send($order->customer_phone, $message);
}
```

## 📱 Navigation

### Admin Navigation
The "Orders" menu item is now active in:
- Desktop sidebar
- Mobile sidebar

### Customer Navigation
Add to your customer navigation:
```blade
<a href="{{ route('customer.orders.index') }}">My Orders</a>
```

## 🎯 Key Routes

### Admin Routes
- `/admin/orders` - Order list
- `/admin/orders/{id}` - Order details
- `/admin/orders/{id}/edit` - Edit order
- `/admin/orders/{id}/invoice` - Print invoice

### Customer Routes
- `/my/orders` - Order history
- `/my/orders/{id}` - Order details
- `/my/orders/{id}/invoice` - Download invoice
- `/track-order` - Public tracking

## 🔐 Permissions

### Admin Access
Requires:
- Authenticated user
- Role: `admin`

### Customer Access
Requires:
- Authenticated user
- Can only view own orders

### Public Access
- Order tracking (with order number + email)

## 📈 Statistics Available

The order dashboard shows:
- Total orders
- Pending orders
- Completed orders
- Total revenue
- Pending revenue
- Order growth (7 days)
- Status distribution

## 🐛 Troubleshooting

### Orders not showing?
```bash
# Clear cache
php artisan cache:clear
php artisan view:clear

# Check database
php artisan tinker
>>> App\Modules\Ecommerce\Order\Models\Order::count()
```

### Routes not working?
```bash
# Clear route cache
php artisan route:clear

# Verify routes
php artisan route:list --name=orders
```

### Livewire not working?
```bash
# Publish Livewire assets
php artisan livewire:publish --assets

# Clear view cache
php artisan view:clear
```

## 📚 Next Steps

1. **Integrate with Cart** - Connect checkout to order creation
2. **Setup Notifications** - Configure SMS/Email services
3. **Test Workflow** - Create test orders and update statuses
4. **Customize Views** - Adjust design to match your theme
5. **Add Reporting** - Create custom reports as needed

## 🎉 You're All Set!

The Order Management System is now fully functional and ready to use.

For detailed documentation, see: `ORDER_MANAGEMENT_README.md`

---

**Need Help?**
- Check the comprehensive README
- Review code comments
- Test with sample data
- Contact development team

**Version:** 1.0.0  
**Last Updated:** 2024-01-20
