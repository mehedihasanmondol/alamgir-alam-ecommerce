# Cart Sidebar Implementation

## Overview
Implemented an iHerb-style slide-out cart sidebar that appears when items are added to the cart.

## Files Created/Modified

### 1. New Files Created

#### `app/Livewire/Cart/CartSidebar.php`
- Livewire component for cart sidebar functionality
- Listens for `cart-updated` event
- Manages cart items display
- Handles item removal and quantity updates
- Calculates subtotal

#### `resources/views/livewire/cart/cart-sidebar.blade.php`
- Slide-out sidebar UI
- Product list with images
- Quantity controls
- Remove item functionality
- Subtotal display
- Action buttons (View Cart, Continue Shopping)

#### `app/Livewire/Cart/CartCounter.php`
- Header cart counter component
- Updates dynamically when cart changes
- Listens for `cart-updated` event

#### `resources/views/livewire/cart/cart-counter.blade.php`
- Cart icon with badge counter
- Shows item count

### 2. Modified Files

#### `resources/views/components/frontend/header.blade.php`
- Replaced static cart counter with Livewire component
- Line 118: `@livewire('cart.cart-counter')`

#### `resources/views/layouts/app.blade.php`
- Added cart sidebar component
- Line 40: `@livewire('cart.cart-sidebar')`

## Features

### Cart Sidebar Features:
1. **Slide-out Animation**: Smooth transition from right side
2. **Product Display**: 
   - Product image
   - Product name
   - Variant name
   - Price
   - Quantity controls
3. **Quantity Management**:
   - Increment/decrement buttons
   - Real-time price updates
4. **Remove Items**: One-click item removal
5. **Subtotal Calculation**: Auto-calculated total
6. **Action Buttons**:
   - View Cart (navigates to cart page)
   - Continue Shopping (closes sidebar)
7. **Empty State**: Shows message when cart is empty
8. **Backdrop**: Click outside to close

### Cart Counter Features:
1. **Dynamic Updates**: Updates without page reload
2. **Visual Indicator**: Green badge for items, gray for empty
3. **Item Count**: Shows total number of items

## How It Works

### Event Flow:
1. User clicks "Add to Cart" button
2. `AddToCart` component adds item to session
3. Dispatches `cart-updated` event
4. `CartSidebar` listens and opens automatically
5. `CartCounter` listens and updates count
6. Both components refresh their data from session

### Session Storage:
Cart items stored in session with structure:
```php
[
    'variant_123' => [
        'product_id' => 1,
        'variant_id' => 123,
        'product_name' => 'Product Name',
        'variant_name' => 'Variant Name',
        'sku' => 'SKU123',
        'price' => 29.99,
        'quantity' => 2,
        'image' => 'path/to/image.jpg'
    ]
]
```

## Usage

### Opening Cart Sidebar:
The sidebar automatically opens when:
- User adds item to cart
- `cart-updated` event is dispatched

### Manual Control:
```blade
// Show sidebar
$this->dispatch('cart-updated');

// Hide sidebar
wire:click="hideCart"
```

## Styling
- Uses Tailwind CSS
- Green theme matching iHerb design
- Responsive (full width on mobile, 384px on desktop)
- Smooth transitions and animations
- Alpine.js for show/hide animations

## Dependencies
- Livewire 3.x
- Alpine.js (for transitions)
- Tailwind CSS
- Session storage

## Future Enhancements
- Add "Frequently Purchased Together" products
- Add shipping calculator
- Add coupon code input
- Add mini product recommendations
- Persist cart to database for logged-in users
