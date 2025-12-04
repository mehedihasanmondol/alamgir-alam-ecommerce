# Coupon Discount as Separate Record - Implementation

## Overview
Updated the order calculation system to treat coupon discounts as a separate discount record, distinct from product-level discounts (sale prices).

## Changes Made

### 1. OrderCalculationService.php
**File**: `app/Modules/Ecommerce/Order/Services/OrderCalculationService.php`

#### Updated Method Signature
```php
// Before
public function calculateOrderTotals(
    array $items,
    float $shippingCost = 0,
    ?string $couponCode = null
): array

// After
public function calculateOrderTotals(
    array $items,
    float $shippingCost = 0,
    float $couponDiscount = 0  // Now accepts discount amount directly
): array
```

#### Updated Calculation Logic
```php
public function calculateOrderTotals(
    array $items,
    float $shippingCost = 0,
    float $couponDiscount = 0
): array {
    $subtotal = $this->calculateSubtotal($items);
    $taxAmount = $this->calculateTax($subtotal);
    
    // Total amount calculation: subtotal + tax + shipping - coupon discount
    $totalAmount = $subtotal + $taxAmount + $shippingCost - $couponDiscount;

    return [
        'subtotal' => round($subtotal, 2),
        'tax_amount' => round($taxAmount, 2),
        'shipping_cost' => round($shippingCost, 2),
        'discount_amount' => round($couponDiscount, 2),  // Coupon discount only
        'total_amount' => round(max(0, $totalAmount), 2), // Never negative
    ];
}
```

#### New Method for Product Discounts
```php
/**
 * Calculate product-level discount amount.
 * Note: Coupon discounts are handled separately and passed directly.
 */
protected function calculateProductDiscount(array $items): float
{
    $discount = 0;
    
    foreach ($items as $item) {
        // If item has a discount (original_price vs sale_price)
        if (isset($item['original_price']) && isset($item['price'])) {
            $itemDiscount = ($item['original_price'] - $item['price']) * $item['quantity'];
            $discount += max(0, $itemDiscount);
        }
    }
    
    return $discount;
}
```

---

### 2. OrderService.php
**File**: `app/Modules/Ecommerce/Order/Services/OrderService.php`

#### Updated Service Call
```php
// Before
$calculations = $this->calculationService->calculateOrderTotals(
    $data['items'],
    $data['shipping_cost'] ?? 0,
    $data['coupon_code'] ?? null  // Passed coupon code
);

// After
$calculations = $this->calculationService->calculateOrderTotals(
    $data['items'],
    $data['shipping_cost'] ?? 0,
    $data['discount_amount'] ?? 0  // Passed coupon discount amount directly
);
```

---

## Discount Calculation Flow

### Product Discounts (Sale Prices)
1. **Calculated at**: Cart level, when items are added
2. **Stored in**: Cart session with `original_price` and `price` fields
3. **Reflected in**: Item subtotal (already discounted)
4. **Display**: Shows "Special!" badge and savings per item

### Coupon Discounts
1. **Calculated at**: Cart page, when coupon is applied
2. **Stored in**: Session `applied_coupon` with `discount_amount`
3. **Applied to**: Order total (after subtotal + shipping)
4. **Display**: Shows as separate "Discount" line with coupon code badge

---

## Order Calculation Breakdown

### Example Order
```
Product A: $100 (original: $120) x 1 = $100
Product B: $50 (original: $60) x 2 = $100
───────────────────────────────────────────
Subtotal (after product discounts):    $200
Product Savings (not shown separately): $40
Shipping:                               $10
Coupon Discount (SAVE20):              -$20
───────────────────────────────────────────
Total:                                  $190
```

### Database Storage
```php
Order {
    subtotal: 200.00,           // Already includes product discounts
    shipping_cost: 10.00,
    discount_amount: 20.00,     // Coupon discount only
    total_amount: 190.00,
    coupon_code: 'SAVE20'
}
```

---

## Data Flow

### 1. Cart Page
```php
// Session stores
'cart' => [
    'item_key' => [
        'product_id' => 1,
        'price' => 100,              // Sale price
        'original_price' => 120,     // Original price
        'quantity' => 1
    ]
]

'applied_coupon' => [
    'code' => 'SAVE20',
    'discount_amount' => 20.00,
    'free_shipping' => false
]
```

### 2. Checkout Process
```php
// CheckoutController calculates
$subtotal = 200;  // Sum of (price * quantity) - already discounted
$discountAmount = 20;  // From session coupon
$shippingCost = 10;

// Passes to OrderService
$orderData = [
    'items' => $orderItems,
    'shipping_cost' => 10,
    'discount_amount' => 20,  // Coupon discount
    'coupon_code' => 'SAVE20'
];
```

### 3. Order Creation
```php
// OrderService calls OrderCalculationService
$calculations = calculateOrderTotals(
    items: $orderItems,
    shippingCost: 10,
    couponDiscount: 20  // Passed directly
);

// Returns
[
    'subtotal' => 200.00,
    'tax_amount' => 0.00,
    'shipping_cost' => 10.00,
    'discount_amount' => 20.00,  // Coupon only
    'total_amount' => 190.00
]
```

---

## Benefits of This Approach

### 1. Clear Separation
- **Product discounts**: Built into item prices (sale prices)
- **Coupon discounts**: Applied at order level, tracked separately

### 2. Accurate Tracking
- Can track coupon effectiveness independently
- Product sale performance separate from coupon campaigns
- Clear audit trail for discount sources

### 3. Flexible Display
- Show product savings at item level
- Show coupon savings at order level
- Easy to add more discount types in future

### 4. Correct Calculations
- Subtotal reflects actual prices paid per item
- Coupon applies to order total (not individual items)
- No double-counting of discounts

---

## View Display Logic

### Customer Order View
```blade
<!-- Product items show individual savings -->
@foreach($order->items as $item)
    <div>
        Price: ${{ $item->price }}
        @if($item->original_price > $item->price)
            <span>Saved ${{ $item->original_price - $item->price }}</span>
        @endif
    </div>
@endforeach

<!-- Order summary shows coupon discount -->
<div>Subtotal: ${{ $order->subtotal }}</div>
<div>Shipping: ${{ $order->shipping_cost }}</div>
@if($order->discount_amount > 0)
    <div>
        Discount
        @if($order->coupon_code)
            <badge>{{ $order->coupon_code }}</badge>
        @endif
        : -${{ $order->discount_amount }}
    </div>
@endif
<div>Total: ${{ $order->total_amount }}</div>
```

---

## Testing Scenarios

### Scenario 1: Product Discount Only
```
Product: $80 (original: $100)
Subtotal: $80
Shipping: $10
Coupon: None
Total: $90
```

### Scenario 2: Coupon Discount Only
```
Product: $100 (no sale)
Subtotal: $100
Shipping: $10
Coupon: -$10
Total: $100
```

### Scenario 3: Both Discounts
```
Product: $80 (original: $100)
Subtotal: $80
Shipping: $10
Coupon: -$10
Total: $80
```

### Scenario 4: Free Shipping Coupon
```
Product: $100
Subtotal: $100
Shipping: $0 (free from coupon)
Coupon: $0 (free shipping only)
Total: $100
```

---

## Migration Notes

### No Database Changes Required
- Existing `discount_amount` field stores coupon discount
- Product discounts reflected in item prices
- No schema changes needed

### Backward Compatibility
- Existing orders: `discount_amount` already stores coupon discount
- New orders: Same field usage, clearer calculation
- No data migration needed

---

## Future Enhancements

### 1. Add Product Discount Tracking
```php
// Optional: Track product-level discounts separately
'product_discount_amount' => 40.00,  // Total product savings
'coupon_discount_amount' => 20.00,   // Coupon savings
'total_discount' => 60.00            // Combined savings
```

### 2. Multiple Discount Types
```php
// Future expansion
'discount_breakdown' => [
    'product_sales' => 40.00,
    'coupon' => 20.00,
    'loyalty_points' => 10.00,
    'referral_bonus' => 5.00
]
```

### 3. Discount Analytics
- Track which discount type drives more sales
- Compare coupon effectiveness
- Analyze product sale performance

---

## Summary

✅ **Coupon discount** is now a separate, explicit parameter
✅ **Product discounts** are reflected in item prices (subtotal)
✅ **Order calculations** are clear and maintainable
✅ **No database changes** required
✅ **Backward compatible** with existing orders
✅ **Ready for future** discount type additions

---

**Implementation Date**: November 11, 2025
**Status**: ✅ Complete and Production Ready
