# Manual Discount Display - Complete Implementation

## Overview
Added manual discount (`discount_amount`) display to all order views alongside coupon discount (`coupon_discount`), ensuring both discount types are visible to customers and admins.

## Problem
Manual discounts were not being displayed in order views, only coupon discounts were shown. This caused confusion when admins applied manual adjustments.

## Solution
Updated all order views to display both discount types separately with clear labels.

---

## Files Updated

### 1. Customer Order Details
**File**: `resources/views/customer/orders/show.blade.php`

```blade
@if($order->shipping_cost > 0)
    <div class="flex justify-between text-sm">
        <span class="text-gray-600">Shipping</span>
        <span class="text-gray-900">৳{{ number_format($order->shipping_cost, 2) }}</span>
    </div>
@endif

@if($order->discount_amount > 0)
    <div class="flex justify-between text-sm">
        <span class="text-gray-600">Discount</span>
        <span class="text-red-600">-৳{{ number_format($order->discount_amount, 2) }}</span>
    </div>
@endif

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

### 2. Admin Order Details
**File**: `resources/views/admin/orders/show.blade.php`

```blade
@if($order->discount_amount > 0)
    <div class="flex justify-between text-sm">
        <span class="text-gray-600">Discount</span>
        <span class="text-red-600">-৳{{ number_format($order->discount_amount, 2) }}</span>
    </div>
@endif

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

### 3. Customer Invoice
**File**: `resources/views/customer/orders/invoice.blade.php`

```blade
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
```

---

### 4. Admin Invoice
**File**: `resources/views/admin/orders/invoice.blade.php`

Same structure as customer invoice.

---

### 5. Admin Order Edit Summary
**File**: `resources/views/admin/orders/edit-livewire.blade.php`

```blade
<div>
    <p class="text-indigo-100 text-sm font-medium">Shipping</p>
    <p class="text-2xl font-bold mt-1">৳{{ number_format($order->shipping_cost, 2) }}</p>
</div>

@if($order->discount_amount > 0)
    <div>
        <p class="text-indigo-100 text-sm font-medium">Discount</p>
        <p class="text-2xl font-bold mt-1">৳{{ number_format($order->discount_amount, 2) }}</p>
    </div>
@endif

@if($order->coupon_discount > 0)
    <div>
        <p class="text-indigo-100 text-sm font-medium">Coupon</p>
        <p class="text-2xl font-bold mt-1">৳{{ number_format($order->coupon_discount, 2) }}</p>
    </div>
@endif

<div class="border-l border-indigo-400 pl-6">
    <p class="text-indigo-100 text-sm font-medium">Total Amount</p>
    <p class="text-3xl font-bold mt-1">৳{{ number_format($order->total_amount, 2) }}</p>
</div>
```

---

## Display Examples

### Customer Order View
```
Order Summary
─────────────────────────────────
Subtotal:                  $200.00
Shipping:                   $10.00
Discount:                   -$5.00   ← Manual discount
Coupon Discount [SAVE20]:  -$20.00  ← Coupon discount
─────────────────────────────────
Total:                     $185.00
```

### Admin Order View
```
Order Summary
─────────────────────────────────
Subtotal:                  $200.00
Shipping:                   $10.00
Discount:                   -$5.00   ← Manual discount
Coupon Discount [SAVE20]:  -$20.00  ← Coupon discount with badge
─────────────────────────────────
Total:                     $185.00
```

### Invoice (Print)
```
Subtotal:                  $200.00
Shipping:                   $10.00
Discount:                   -$5.00
Coupon Discount (SAVE20):  -$20.00
─────────────────────────────────
Grand Total:               $185.00
```

### Admin Edit Summary (Top Banner)
```
┌──────────┬──────────┬──────────┬──────────┬──────────────┐
│ Subtotal │ Shipping │ Discount │  Coupon  │ Total Amount │
│  $200.00 │  $10.00  │   $5.00  │  $20.00  │   $185.00    │
└──────────┴──────────┴──────────┴──────────┴──────────────┘
```

---

## Discount Type Comparison

| Feature | Manual Discount | Coupon Discount |
|---------|----------------|-----------------|
| **Field** | `discount_amount` | `coupon_discount` |
| **Label** | "Discount" | "Coupon Discount" |
| **Badge** | No | Yes (coupon code) |
| **Editable** | Yes (admin only) | No (system only) |
| **Source** | Admin adjustment | Customer coupon |
| **Color** | Red text | Red text + green badge |

---

## Conditional Display Logic

### Both Discounts Present
```blade
Subtotal:                  $200.00
Shipping:                   $10.00
Discount:                   -$5.00   ← Shows
Coupon Discount (CODE):    -$20.00  ← Shows
Total:                     $185.00
```

### Only Manual Discount
```blade
Subtotal:                  $200.00
Shipping:                   $10.00
Discount:                   -$5.00   ← Shows
Total:                     $205.00
```

### Only Coupon Discount
```blade
Subtotal:                  $200.00
Shipping:                   $10.00
Coupon Discount (CODE):    -$20.00  ← Shows
Total:                     $190.00
```

### No Discounts
```blade
Subtotal:                  $200.00
Shipping:                   $10.00
Total:                     $210.00
```

---

## Benefits

### 1. Complete Transparency
- Customers see all discounts applied
- Clear breakdown of savings
- No hidden adjustments

### 2. Better Admin Visibility
- Admins can see manual adjustments
- Coupon discounts clearly identified
- Easy to audit and verify

### 3. Accurate Reporting
- Track manual discounts separately
- Measure coupon effectiveness
- Analyze discount patterns

### 4. Improved Trust
- Customers see exactly what they're paying
- Clear explanation of all charges/discounts
- Professional presentation

---

## Use Cases

### Scenario 1: Customer Service Adjustment
**Situation**: Customer received damaged item
**Action**: Admin applies $5 manual discount
**Display**:
```
Discount: -$5.00
```

### Scenario 2: Coupon Applied at Checkout
**Situation**: Customer uses SAVE20 coupon
**Action**: System applies coupon discount
**Display**:
```
Coupon Discount (SAVE20): -$20.00
```

### Scenario 3: Both Discounts
**Situation**: Customer uses coupon + admin adds goodwill discount
**Action**: Both discounts applied
**Display**:
```
Discount: -$5.00
Coupon Discount (SAVE20): -$20.00
```

---

## Testing Checklist

- [x] Manual discount shows in customer order view
- [x] Manual discount shows in admin order view
- [x] Manual discount shows in customer invoice
- [x] Manual discount shows in admin invoice
- [x] Manual discount shows in admin edit summary
- [x] Coupon discount shows with badge
- [x] Both discounts can appear together
- [x] Conditional display works (only shows if > 0)
- [x] Total calculation is correct
- [x] Print/PDF invoices display correctly

---

## Summary

### What Changed
✅ Added `discount_amount` display to all order views
✅ Maintained `coupon_discount` display with badge
✅ Both discounts now visible side-by-side
✅ Conditional display (only shows if > 0)
✅ Consistent across all views

### Views Updated
1. Customer Order Details
2. Admin Order Details
3. Customer Invoice
4. Admin Invoice
5. Admin Edit Summary Banner

### Display Format
- **Manual Discount**: Simple "Discount: -$X.XX"
- **Coupon Discount**: "Coupon Discount [CODE]: -$X.XX"

---

**Implementation Date**: November 11, 2025
**Status**: ✅ Complete - All Views Updated
**Impact**: Both discount types now visible in all order views
