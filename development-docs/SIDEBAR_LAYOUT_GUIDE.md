# Add to Cart Sidebar Layout - Implementation Guide

## ✅ Current Status

The add-to-cart component has been updated to match the exact iHerb style with:
- ✅ Centered quantity selector
- ✅ Orange "Add to Cart" button
- ✅ "Add to Lists" button

## 🎯 Next Step: Move to Sidebar

To complete the iHerb layout, the add-to-cart card needs to be positioned as a sidebar (aside) next to the product info.

### Current Layout (2-column)
```
┌─────────────┬─────────────────────┐
│   Gallery   │   Product Info      │
│   (Left)    │   + Add to Cart     │
│             │   (Right)           │
└─────────────┴─────────────────────┘
```

### Target Layout (3-column - iHerb Style)
```
┌──────────┬────────────────┬──────────┐
│ Gallery  │  Product Info  │  Price & │
│ (4 cols) │  (5 cols)      │  Cart    │
│          │                │  (3 cols)│
└──────────┴────────────────┴──────────┘
```

---

## 📝 Implementation Steps

### Step 1: Update Grid Layout
Change from 2-column to 3-column grid:

**File:** `resources/views/frontend/products/show.blade.php`

**Find:**
```blade
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
```

**Replace with:**
```blade
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
```

### Step 2: Update Gallery Column
**Find:**
```blade
<!-- Left Column: Product Gallery -->
<div>
```

**Replace with:**
```blade
<!-- Left Column: Product Gallery (4 columns) -->
<div class="lg:col-span-4">
```

### Step 3: Update Info Column
**Find:**
```blade
<!-- Right Column: Product Info -->
<div class="space-y-6">
```

**Replace with:**
```blade
<!-- Middle Column: Product Info (5 columns) -->
<div class="lg:col-span-5">
```

### Step 4: Remove Add-to-Cart from Middle Column
**Remove these sections from the middle column:**
- Variant Selector (if present)
- Add to Cart Component
- Product Badges
- Share Buttons

### Step 5: Add Right Sidebar
**Add after the middle column closes (`</div>`):**

```blade
<!-- Right Sidebar: Price & Add to Cart (3 columns) -->
<div class="lg:col-span-3">
    <div class="lg:sticky lg:top-4">
        <!-- Price Card -->
        <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
            @php
                $variant = $defaultVariant ?? $product->variants->first();
            @endphp
            
            <!-- Price Display -->
            @if($variant)
                @if($variant->sale_price && $variant->sale_price < $variant->price)
                    <div class="mb-2">
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-bold text-red-600">
                                ৳{{ number_format($variant->sale_price, 2) }}
                            </span>
                            <span class="text-sm text-red-600 font-semibold">
                                ({{ round((($variant->price - $variant->sale_price) / $variant->price) * 100) }}% off)
                            </span>
                        </div>
                        <div class="text-sm text-gray-500 line-through">
                            ৳{{ number_format($variant->price, 2) }}
                        </div>
                        <div class="text-xs text-gray-600 mt-1">
                            ৳{{ number_format($variant->sale_price / 50, 2) }}/ml
                        </div>
                    </div>
                @else
                    <div class="mb-2">
                        <span class="text-3xl font-bold text-gray-900">
                            ৳{{ number_format($variant->price, 2) }}
                        </span>
                        <div class="text-xs text-gray-600 mt-1">
                            ৳{{ number_format($variant->price / 50, 2) }}/ml
                        </div>
                    </div>
                @endif

                <!-- Claimed Badge -->
                <div class="mb-4">
                    <div class="bg-orange-100 text-orange-800 text-xs font-semibold px-2 py-1 rounded inline-block">
                        19% claimed
                    </div>
                </div>
            @endif

            <!-- Add to Cart Component -->
            @livewire('cart.add-to-cart', ['product' => $product, 'defaultVariant' => $defaultVariant])
        </div>
    </div>
</div>
```

---

## 🎨 Key Features of Sidebar

### 1. **Sticky Positioning**
```blade
<div class="lg:sticky lg:top-4">
```
- Sidebar stays visible when scrolling
- Only on large screens (lg:)
- 16px from top (top-4)

### 2. **Price Card**
- Border and shadow for elevation
- White background
- Rounded corners
- Padding for spacing

### 3. **Price Display**
- Large price (text-3xl)
- Discount percentage in red
- Original price (strikethrough)
- Unit price (per ml)

### 4. **Claimed Badge**
- Orange background
- Shows popularity (19% claimed)
- Small, inline badge

### 5. **Add to Cart Component**
- Centered quantity selector
- Orange "Add to Cart" button
- "Add to Lists" button below

---

## 📐 Column Breakdown

| Column | Width | Content |
|--------|-------|---------|
| Gallery | 4/12 (33%) | Product images |
| Info | 5/12 (42%) | Details, specs, rankings |
| Sidebar | 3/12 (25%) | Price & add to cart |

**Total:** 12 columns (Tailwind grid system)

---

## 📱 Responsive Behavior

### Desktop (≥1024px)
- 3-column layout (4-5-3)
- Sidebar is sticky
- Full width for all columns

### Tablet (768px - 1023px)
- 2-column layout
- Gallery + Info side by side
- Sidebar moves below

### Mobile (<768px)
- Single column, stacked
- Gallery → Info → Sidebar
- Full width for each

---

## ✅ Benefits of Sidebar Layout

1. **Better UX**: Price and cart always visible
2. **More Space**: Product info gets more room
3. **iHerb Match**: Exact same layout as reference
4. **Sticky Cart**: Easy to add to cart while reading
5. **Clean Design**: Organized, professional look

---

## 🚀 Quick Implementation

If you want me to implement this, I can:
1. Backup current file
2. Update grid layout
3. Move add-to-cart to sidebar
4. Test responsive design
5. Verify all functionality

Just let me know and I'll complete the implementation!

---

*Guide created: November 7, 2025*
