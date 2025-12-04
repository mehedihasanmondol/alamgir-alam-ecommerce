# Coupon Discount - Dedicated Database Field

## Overview
Added a dedicated `coupon_discount` field to the `orders` table to specifically track coupon discounts separately from other discount types.

## Database Changes

### Migration Created
**File**: `database/migrations/2025_11_11_123801_add_coupon_discount_to_orders_table.php`

```php
Schema::table('orders', function (Blueprint $table) {
    $table->decimal('coupon_discount', 10, 2)->default(0)->after('discount_amount')
        ->comment('Discount amount from coupon code');
});
```

### Field Details
- **Name**: `coupon_discount`
- **Type**: `DECIMAL(10, 2)`
- **Default**: `0.00`
- **Position**: After `discount_amount`
- **Purpose**: Store coupon-specific discount amount

---

## Updated Files

### 1. Order Model
**File**: `app/Modules/Ecommerce/Order/Models/Order.php`

#### Added to Fillable
```php
protected $fillable = [
    // ... existing fields
    'discount_amount',
    'coupon_discount',  // NEW
    'total_amount',
    'coupon_code',
    // ... rest of fields
];
```

#### Added to Casts
```php
protected $casts = [
    // ... existing casts
    'discount_amount' => 'decimal:2',
    'coupon_discount' => 'decimal:2',  // NEW
    'total_amount' => 'decimal:2',
    // ... rest of casts
];
```

---

### 2. Order Service
**File**: `app/Modules/Ecommerce/Order/Services/OrderService.php`

#### Updated Order Creation
```php
$order = $this->orderRepository->create([
    // ... existing fields
    'discount_amount' => $calculations['discount_amount'],
    'coupon_discount' => $data['discount_amount'] ?? 0,  // NEW - Coupon discount
    'total_amount' => $calculations['total_amount'],
    'coupon_code' => $data['coupon_code'] ?? null,
    // ... rest of fields
]);
```

---

### 3. Customer Order View
**File**: `resources/views/customer/orders/show.blade.php`

#### Updated Display
```blade
@if($order->coupon_discount > 0)
    <div class="flex justify-between text-sm">
        <span class="text-gray-600">
            Coupon Discount
            @if($order->coupon_code)
                <span class="inline-flex items-center px-2 py-0.5 ml-2 rounded text-xs font-bold bg-green-100 text-green-800">
                    {{ $order->coupon_code }}
                </span>
            @endif
        </span>
        <span class="text-red-600">-৳{{ number_format($order->coupon_discount, 2) }}</span>
    </div>
@endif
```

---

### 4. Admin Order View
**File**: `resources/views/admin/orders/show.blade.php`

Same update as customer view - now uses `coupon_discount` field.

---

### 5. Customer Invoice
**File**: `resources/views/customer/orders/invoice.blade.php`

```blade
@if($order->coupon_discount > 0)
    <tr>
        <td>Coupon Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif:</td>
        <td class="text-right" style="color: #dc2626;">-৳{{ number_format($order->coupon_discount, 2) }}</td>
    </tr>
@endif
```

---

### 6. Admin Invoice
**File**: `resources/views/admin/orders/invoice.blade.php`

Same update as customer invoice.

---

## Field Usage Breakdown

### `discount_amount` (Existing)
- **Purpose**: General discount field (can be used for product-level discounts or other discount types)
- **Current Use**: Stores coupon discount (for backward compatibility)
- **Future Use**: Can track product sale discounts separately

### `coupon_discount` (New)
- **Purpose**: Specifically for coupon code discounts
- **Current Use**: Stores the exact discount amount from applied coupon
- **Benefit**: Clear separation and tracking of coupon effectiveness

---

## Order Structure Example

### Database Record
```php
Order {
    id: 1,
    order_number: 'ORD-2025-001',
    subtotal: 200.00,              // After product discounts
    tax_amount: 0.00,
    shipping_cost: 10.00,
    discount_amount: 20.00,        // General discount (currently same as coupon)
    coupon_discount: 20.00,        // NEW - Specific coupon discount
    total_amount: 190.00,          // subtotal + tax + shipping - coupon_discount
    coupon_code: 'SAVE20',
}
```

### Display Breakdown
```
Order Summary
─────────────────────────────────
Subtotal:                  $200.00
Shipping:                   $10.00
Coupon Discount (SAVE20):  -$20.00  ← Uses coupon_discount field
─────────────────────────────────
Total:                     $190.00
```

---

## Benefits

### 1. Clear Separation
- Coupon discounts tracked independently
- Easy to identify coupon-driven sales
- Separate from product-level discounts

### 2. Better Analytics
- Track total coupon discount given
- Measure coupon ROI accurately
- Compare coupon vs product discounts

### 3. Flexible Expansion
- `discount_amount` available for other discount types
- Can add more discount fields in future
- Clear data structure for reporting

### 4. Backward Compatible
- Existing orders still work (coupon_discount defaults to 0)
- No data migration needed for old orders
- New orders populate both fields

---

## Future Enhancements

### Potential Additional Fields
```php
// Future expansion possibilities
'product_discount' => 40.00,      // Product sale discounts
'coupon_discount' => 20.00,       // Coupon discounts
'loyalty_discount' => 10.00,      // Loyalty points
'referral_discount' => 5.00,      // Referral bonuses
'total_discount' => 75.00,        // Sum of all discounts
```

### Enhanced Reporting
- Discount breakdown by type
- Coupon effectiveness reports
- Customer discount preferences
- Seasonal discount analysis

---

## Migration Status

✅ **Migration Created**: `2025_11_11_123801_add_coupon_discount_to_orders_table.php`
✅ **Migration Run**: Successfully executed
✅ **Field Added**: `coupon_discount` column added to orders table
✅ **Model Updated**: Order model includes new field
✅ **Service Updated**: OrderService saves to new field
✅ **Views Updated**: All order views display new field

---

## Testing Checklist

- [x] Migration runs successfully
- [x] Field appears in database
- [x] Order model recognizes field
- [x] New orders save coupon_discount
- [x] Customer view displays coupon_discount
- [x] Admin view displays coupon_discount
- [x] Invoices show coupon_discount
- [x] Existing orders still work (default 0)
- [x] Coupon code badge displays correctly
- [x] Discount calculations are accurate

---

## Summary

### What Changed
1. ✅ Added `coupon_discount` column to orders table
2. ✅ Updated Order model (fillable + casts)
3. ✅ Updated OrderService to save coupon discount
4. ✅ Updated all order views to display coupon_discount
5. ✅ Updated all invoices to show coupon_discount

### Why This Matters
- **Clear Data**: Coupon discounts are now explicitly tracked
- **Better Reports**: Can analyze coupon effectiveness separately
- **Future Ready**: Structure supports multiple discount types
- **Maintainable**: Clear field naming and purpose

### Database Schema
```sql
-- Orders table now has:
discount_amount DECIMAL(10,2) DEFAULT 0    -- General discounts
coupon_discount DECIMAL(10,2) DEFAULT 0    -- Coupon-specific (NEW)
coupon_code VARCHAR(255) NULL              -- Coupon code used
```

---

**Implementation Date**: November 11, 2025
**Migration File**: `2025_11_11_123801_add_coupon_discount_to_orders_table.php`
**Status**: ✅ Complete and Production Ready
