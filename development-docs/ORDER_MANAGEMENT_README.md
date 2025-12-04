# Order Management System Documentation

## Overview
Complete order management system for the Laravel ecommerce platform with comprehensive features for order processing, tracking, and management.

## Features

### Admin Features
- ✅ **Order Dashboard** - View statistics and recent orders
- ✅ **Order Listing** - Search, filter, and paginate orders
- ✅ **Order Details** - View complete order information
- ✅ **Status Management** - Update order status with history tracking
- ✅ **Order Editing** - Modify order details
- ✅ **Invoice Generation** - Print/download invoices
- ✅ **Order Cancellation** - Cancel orders with stock restoration
- ✅ **Real-time Search** - Livewire-powered instant search
- ✅ **Status History** - Track all status changes with timestamps
- ✅ **Customer Notifications** - SMS/Email notifications (ready for integration)

### Customer Features
- ✅ **Order History** - View all past orders
- ✅ **Order Details** - View order status and items
- ✅ **Order Tracking** - Track order status with timeline
- ✅ **Public Tracking** - Track orders without login (order number + email)
- ✅ **Invoice Download** - Download order invoices
- ✅ **Order Cancellation** - Cancel pending orders
- ✅ **Order Timeline** - Visual status progression

### Order Statuses
1. **Pending** - Order placed, awaiting processing
2. **Processing** - Order is being prepared
3. **Confirmed** - Order confirmed by admin
4. **Shipped** - Order shipped with tracking
5. **Delivered** - Order delivered to customer
6. **Cancelled** - Order cancelled (stock restored)
7. **Refunded** - Order refunded
8. **Failed** - Order failed (payment/processing)

### Payment Statuses
- **Pending** - Payment not received
- **Paid** - Payment completed
- **Failed** - Payment failed
- **Refunded** - Payment refunded

### Payment Methods Supported
- Cash on Delivery (COD)
- bKash
- Nagad
- Rocket
- Credit/Debit Card
- Bank Transfer

## Database Structure

### Tables Created
1. **orders** - Main order information
2. **order_items** - Order line items with product snapshots
3. **order_addresses** - Billing and shipping addresses
4. **order_status_histories** - Status change tracking
5. **order_payments** - Payment information and transactions

## File Structure

```
app/
├── Modules/Ecommerce/Order/
│   ├── Models/
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── OrderAddress.php
│   │   ├── OrderStatusHistory.php
│   │   └── OrderPayment.php
│   ├── Repositories/
│   │   ├── OrderRepository.php
│   │   ├── OrderItemRepository.php
│   │   └── OrderStatusHistoryRepository.php
│   ├── Services/
│   │   ├── OrderService.php
│   │   ├── OrderStatusService.php
│   │   └── OrderCalculationService.php
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   └── OrderController.php
│   │   └── Customer/
│   │       └── OrderController.php
│   └── Requests/
│       ├── UpdateOrderStatusRequest.php
│       └── UpdateOrderRequest.php
├── Livewire/Order/
│   ├── OrderStatusUpdater.php
│   ├── OrderSearch.php
│   └── OrderTracker.php
resources/views/
├── admin/orders/
│   ├── index.blade.php
│   ├── show.blade.php
│   ├── edit.blade.php
│   └── invoice.blade.php
├── customer/orders/
│   ├── index.blade.php
│   ├── show.blade.php
│   ├── track.blade.php
│   └── invoice.blade.php
└── livewire/order/
    ├── order-status-updater.blade.php
    ├── order-search.blade.php
    └── order-tracker.blade.php
```

## Installation & Setup

### Step 1: Run Migrations
```bash
php artisan migrate
```

This will create all necessary tables:
- orders
- order_items
- order_addresses
- order_status_histories
- order_payments

### Step 2: Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 3: Verify Routes
```bash
php artisan route:list --name=orders
```

## Usage Guide

### Admin Panel

#### Accessing Orders
Navigate to: `/admin/orders`

#### Viewing Order Details
Click on any order to view:
- Order items with product snapshots
- Customer information
- Shipping and billing addresses
- Status history timeline
- Payment information

#### Updating Order Status
1. Go to order details page
2. Use the "Update Order Status" form
3. Select new status
4. Add tracking information (if shipping)
5. Add notes (optional)
6. Choose whether to notify customer
7. Click "Update Status"

#### Editing Order
1. Click "Edit Order" button
2. Modify customer information
3. Update payment details
4. Add tracking information
5. Update shipping costs
6. Add admin notes
7. Save changes

#### Cancelling Order
1. Go to order details
2. Click "Cancel Order" button
3. Confirm cancellation
4. Stock will be automatically restored

#### Printing Invoice
1. Go to order details
2. Click "Print Invoice"
3. Print or save as PDF

### Customer Portal

#### Viewing Orders
Navigate to: `/my/orders`

#### Tracking Order
**Method 1: Logged In**
1. Go to "My Orders"
2. Click on order to view details

**Method 2: Public Tracking**
1. Go to `/track-order`
2. Enter order number
3. Enter email address
4. Click "Track Order"

#### Cancelling Order
1. View order details
2. Click "Cancel Order" (only for pending/processing orders)
3. Confirm cancellation

## API Integration Points

### Creating Orders (Cart Checkout)
```php
use App\Modules\Ecommerce\Order\Services\OrderService;

$orderService = app(OrderService::class);

$orderData = [
    'user_id' => auth()->id(),
    'customer_name' => 'John Doe',
    'customer_email' => 'john@example.com',
    'customer_phone' => '01712345678',
    'payment_method' => 'cod',
    'items' => [
        [
            'product' => $product,
            'variant' => $variant, // optional
            'quantity' => 2,
        ],
    ],
    'billing_address' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => '01712345678',
        'address_line_1' => '123 Main St',
        'city' => 'Dhaka',
        'postal_code' => '1200',
        'country' => 'Bangladesh',
    ],
    'shipping_address' => [...], // optional, uses billing if not provided
    'shipping_cost' => 60,
    'customer_notes' => 'Please deliver after 5 PM',
];

$order = $orderService->createOrder($orderData);
```

### Updating Order Status
```php
use App\Modules\Ecommerce\Order\Services\OrderStatusService;

$statusService = app(OrderStatusService::class);

$statusService->updateStatus(
    $order,
    'shipped',
    'Order shipped via Pathao',
    true // notify customer
);
```

### Getting Order Statistics
```php
use App\Modules\Ecommerce\Order\Services\OrderService;

$orderService = app(OrderService::class);

$stats = $orderService->getStatistics([
    'date_from' => '2024-01-01',
    'date_to' => '2024-01-31',
]);

// Returns:
// [
//     'total_orders' => 150,
//     'pending_orders' => 20,
//     'processing_orders' => 30,
//     'completed_orders' => 90,
//     'cancelled_orders' => 10,
//     'total_revenue' => 150000.00,
//     'pending_revenue' => 20000.00,
// ]
```

## Livewire Components

### OrderStatusUpdater
Real-time order status update component with tracking information.

**Usage:**
```blade
@livewire('order.order-status-updater', [
    'order' => $order,
    'availableStatuses' => $availableStatuses
])
```

### OrderSearch
Advanced order search and filtering component.

**Usage:**
```blade
@livewire('order.order-search')
```

### OrderTracker
Public order tracking component.

**Usage:**
```blade
@livewire('order.order-tracker')
```

## Customization

### Adding Custom Order Statuses
Edit `Order` model and migration to add new statuses.

### Modifying Shipping Calculation
Edit `OrderCalculationService::calculateShippingCost()` method.

### Adding Tax Calculation
Edit `OrderCalculationService::calculateTax()` method and set tax rate.

### Integrating SMS Notifications
Implement in `OrderStatusService::sendStatusNotification()` method.

### Integrating Email Notifications
Create notification classes and trigger in `OrderStatusService`.

## Security Features

- ✅ Authorization checks (admin/customer roles)
- ✅ CSRF protection on all forms
- ✅ Input validation on all requests
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade escaping)
- ✅ IP address logging for orders
- ✅ Soft deletes for order recovery

## Performance Optimizations

- ✅ Eager loading relationships
- ✅ Database indexing on frequently queried columns
- ✅ Pagination for large datasets
- ✅ Livewire debouncing for search
- ✅ Query result caching (ready for implementation)

## Testing Checklist

- [ ] Create order from cart
- [ ] View order in admin panel
- [ ] Update order status
- [ ] Track order status changes
- [ ] Cancel order and verify stock restoration
- [ ] Print invoice
- [ ] Customer view their orders
- [ ] Public order tracking
- [ ] Search and filter orders
- [ ] Edit order details
- [ ] Verify email/SMS notifications

## Future Enhancements

- [ ] Order export (CSV, Excel, PDF)
- [ ] Bulk order operations
- [ ] Advanced reporting and analytics
- [ ] Order notes/comments system
- [ ] Return/refund management
- [ ] Integration with shipping APIs
- [ ] Automated status updates
- [ ] Customer order reviews

## Support

For issues or questions:
1. Check this documentation
2. Review the code comments
3. Check Laravel and Livewire documentation
4. Contact development team

## Version History

- **v1.0.0** (2024-01-20) - Initial release
  - Complete order management system
  - Admin and customer interfaces
  - Status tracking and history
  - Invoice generation
  - Livewire components

---

**Last Updated:** 2024-01-20  
**Maintained By:** Development Team
