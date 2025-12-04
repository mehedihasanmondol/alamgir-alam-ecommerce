# Admin Panel - Coupon Discount Read-Only Implementation

## Overview
Updated the admin order edit panel to clearly separate manual discounts from coupon discounts, making coupon discounts read-only since they should only be modified through the coupon system.

## Problem
Previously, the admin panel showed a single "Discount" field that could be edited, which was confusing because:
1. Coupon discounts should not be manually edited
2. Manual discounts and coupon discounts were not clearly separated
3. Editing could break the coupon tracking system

## Solution
Separated the two discount types and made coupon discount read-only in the admin edit interface.

---

## Changes Made

### 1. EditCostsDiscounts Component
**File**: `app/Livewire/Admin/Order/EditCostsDiscounts.php`

#### Added Property
```php
public $coupon_discount;  // Read-only, from coupon
```

#### Updated mount()
```php
public function mount(Order $order)
{
    $this->order = $order;
    $this->shipping_cost = $order->shipping_cost ?? 0;
    $this->discount_amount = $order->discount_amount ?? 0;
    $this->coupon_discount = $order->coupon_discount ?? 0;  // Read-only
    $this->coupon_code = $order->coupon_code;
    $this->calculateTotal();
}
```

#### Updated calculateTotal()
```php
public function calculateTotal()
{
    // Total calculation: subtotal + tax + shipping - discount - coupon_discount
    $this->calculatedTotal = $this->order->subtotal + 
                            ($this->shipping_cost ?? 0) + 
                            ($this->order->tax_amount ?? 0) - 
                            ($this->discount_amount ?? 0) - 
                            ($this->coupon_discount ?? 0);
}
```

#### Updated save()
```php
$this->order->update([
    'shipping_cost' => $this->shipping_cost,
    'discount_amount' => $this->discount_amount,
    // Note: coupon_discount and coupon_code are NOT updated here
    // They should only be changed through the coupon system
    'total_amount' => $this->calculatedTotal,
]);
```

---

### 2. Edit View
**File**: `resources/views/livewire/admin/order/edit-costs-discounts.blade.php`

#### Edit Mode - Manual Discount (Editable)
```blade
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Discount Amount (Manual)
    </label>
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">৳</span>
        <input type="number" step="0.01" wire:model.live="discount_amount" 
               class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all" 
               placeholder="0.00">
    </div>
    <p class="text-xs text-gray-500 mt-1">Manual discount (not from coupon)</p>
    @error('discount_amount') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
</div>
```

#### Edit Mode - Coupon Discount (Read-only)
```blade
@if($coupon_discount > 0)
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Coupon Discount (Read-only)
        </label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">৳</span>
            <input type="text" value="{{ number_format($coupon_discount, 2) }}" 
                   disabled 
                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
        </div>
        @if($coupon_code)
            <p class="text-xs text-green-600 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                </svg>
                Coupon: <code class="ml-1 font-semibold">{{ $coupon_code }}</code>
            </p>
        @endif
        <p class="text-xs text-gray-500 mt-1">This discount is from a coupon and cannot be edited manually</p>
    </div>
@endif
```

#### View Mode - Manual Discount
```blade
@if($order->discount_amount > 0)
    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <span class="text-sm font-medium text-gray-700">Manual Discount</span>
        </div>
        <span class="text-lg font-bold text-red-600">৳{{ number_format($order->discount_amount ?? 0, 2) }}</span>
    </div>
@endif
```

#### View Mode - Coupon Discount
```blade
@if($order->coupon_discount > 0)
    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
            <span class="text-sm font-medium text-gray-700">Coupon Discount</span>
            @if($order->coupon_code)
                <code class="ml-2 text-xs bg-white px-2 py-0.5 rounded border border-green-300 font-mono font-semibold text-green-700">{{ $order->coupon_code }}</code>
            @endif
        </div>
        <span class="text-lg font-bold text-green-600">৳{{ number_format($order->coupon_discount ?? 0, 2) }}</span>
    </div>
@endif
```

---

## Visual Design

### Color Coding
- **Blue** - Shipping Cost (editable)
- **Red** - Manual Discount (editable)
- **Green** - Coupon Discount (read-only)

### Edit Mode Layout
```
┌─────────────────────────────────────┐
│ Shipping Cost                       │
│ ৳ [___________] (editable)          │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ Discount Amount (Manual)            │
│ ৳ [___________] (editable)          │
│ Manual discount (not from coupon)   │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ Coupon Discount (Read-only)         │
│ ৳ [___20.00___] (disabled)          │
│ ✓ Coupon: SAVE20                    │
│ Cannot be edited manually           │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ New Order Total: ৳190.00            │
└─────────────────────────────────────┘
```

### View Mode Layout
```
┌─────────────────────────────────────┐
│ 🚚 Shipping Cost        ৳10.00      │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ 🏷️  Manual Discount    ৳5.00       │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ 🎟️  Coupon Discount    ৳20.00      │
│     [SAVE20]                        │
└─────────────────────────────────────┘
```

---

## Benefits

### 1. Clear Separation
- **Manual Discount**: For admin-applied discounts (returns, adjustments, etc.)
- **Coupon Discount**: From customer-applied coupons (read-only)

### 2. Data Integrity
- Coupon discounts cannot be accidentally modified
- Coupon tracking remains accurate
- Prevents breaking coupon usage records

### 3. Better UX
- Clear labels indicate purpose of each field
- Visual distinction (colors) helps identify field types
- Helpful hints explain why coupon discount is read-only

### 4. Accurate Calculations
- Total includes both discount types
- Each discount tracked separately
- Easy to audit and report

---

## Use Cases

### Manual Discount
**When to use:**
- Customer service adjustments
- Damaged item compensation
- Goodwill discounts
- Price matching
- Special circumstances

**How to apply:**
1. Click "Edit" button
2. Enter amount in "Discount Amount (Manual)" field
3. Total updates automatically
4. Click "Save Changes"

### Coupon Discount
**When applied:**
- Customer enters coupon code at checkout
- Automatically calculated by coupon system
- Recorded in `coupon_discount` field

**Cannot be edited because:**
- Tied to coupon usage tracking
- Affects coupon statistics
- Must match coupon rules
- Recorded in `coupon_user` table

---

## Order Total Calculation

```
Subtotal:                    $200.00
Tax:                          $0.00
Shipping:                    $10.00
Manual Discount:             -$5.00   ← Editable by admin
Coupon Discount (SAVE20):   -$20.00   ← Read-only, from coupon
─────────────────────────────────────
Total:                       $185.00
```

---

## Database Fields

```php
Order {
    subtotal: 200.00,
    tax_amount: 0.00,
    shipping_cost: 10.00,
    discount_amount: 5.00,      // Manual discount (editable)
    coupon_discount: 20.00,     // Coupon discount (read-only in admin)
    coupon_code: 'SAVE20',      // Coupon used
    total_amount: 185.00
}
```

---

## Admin Workflow

### Scenario 1: Order with Coupon
1. Customer places order with coupon "SAVE20"
2. `coupon_discount` = 20.00 (automatic)
3. Admin views order - sees green "Coupon Discount" box
4. Admin can add manual discount if needed
5. Both discounts apply to total

### Scenario 2: Manual Adjustment
1. Customer requests price adjustment
2. Admin clicks "Edit"
3. Admin enters manual discount amount
4. Coupon discount remains unchanged (if exists)
5. Total recalculates with both discounts

### Scenario 3: No Discounts
1. Order has no coupon or manual discount
2. Only "Shipping Cost" field shows
3. Admin can add manual discount if needed

---

## Security & Validation

### Protected Fields
- `coupon_discount` - Not included in update array
- `coupon_code` - Not included in update array

### Editable Fields
- `shipping_cost` - Validated: numeric, min:0
- `discount_amount` - Validated: numeric, min:0
- `total_amount` - Calculated automatically

### Validation Rules
```php
protected $rules = [
    'shipping_cost' => 'nullable|numeric|min:0',
    'discount_amount' => 'nullable|numeric|min:0',
    // coupon_discount not in rules (read-only)
];
```

---

## Testing Checklist

- [x] Manual discount field is editable
- [x] Coupon discount field is read-only (disabled)
- [x] Coupon code displays with coupon discount
- [x] Total calculates correctly with both discounts
- [x] Saving updates manual discount only
- [x] Coupon discount remains unchanged after save
- [x] View mode shows both discounts separately
- [x] Color coding is clear and consistent
- [x] Helper text explains field purposes
- [x] Validation works for editable fields

---

## Summary

✅ **Manual Discount** - Red, editable, for admin adjustments
✅ **Coupon Discount** - Green, read-only, from coupon system
✅ **Clear Separation** - Different colors and labels
✅ **Data Integrity** - Coupon tracking protected
✅ **Better UX** - Clear purpose for each field

---

**Implementation Date**: November 11, 2025
**Status**: ✅ Complete and Production Ready
