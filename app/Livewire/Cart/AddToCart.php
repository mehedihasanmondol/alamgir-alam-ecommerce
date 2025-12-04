<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;

/**
 * ModuleName: Cart Management
 * Purpose: Handle add to cart functionality with quantity management
 * 
 * Key Methods:
 * - increment(): Increase quantity
 * - decrement(): Decrease quantity
 * - addToCart(): Add product to cart
 * - updateQuantity(): Update quantity value
 * 
 * Dependencies:
 * - Product Model
 * - ProductVariant Model
 * - Session (for cart storage)
 * 
 * @category Livewire
 * @package  Cart
 * @author   Windsurf AI
 * @created  2025-11-07
 */
class AddToCart extends Component
{
    public $product;
    public $defaultVariant;
    public $quantity = 1;
    public $selectedVariantId;
    public $maxQuantity;
    public $showSuccess = false;

    protected $listeners = [
        'variant-changed' => 'handleVariantChange',
        'cart-updated' => 'refreshCartQuantity'
    ];

    /**
     * Mount component with product data
     */
    public function mount($product, $defaultVariant = null)
    {
        $this->product = $product;
        $this->defaultVariant = $defaultVariant;
        
        // Set initial variant
        if ($this->product->product_type === 'variable') {
            $this->selectedVariantId = $this->product->variants->first()->id ?? null;
        } else {
            $this->selectedVariantId = $this->defaultVariant->id ?? null;
        }
        
        $this->updateMaxQuantity();
        
        // Check if product is already in cart and set quantity
        $this->loadCartQuantity();
    }

    /**
     * Handle variant change from variant selector
     */
    public function handleVariantChange($variantData = null)
    {
        if ($variantData && isset($variantData['id'])) {
            $this->selectedVariantId = $variantData['id'];
            $this->updateMaxQuantity();
            
            // Load cart quantity for new variant
            $this->loadCartQuantity();
            
            // Reset quantity if it exceeds new max
            if ($this->quantity > $this->maxQuantity) {
                $this->quantity = $this->maxQuantity;
            }
        }
    }

    /**
     * Update maximum quantity based on selected variant
     */
    protected function updateMaxQuantity()
    {
        // If stock validation is disabled, set a high limit
        if (!ProductVariant::isStockRestrictionEnabled()) {
            $this->maxQuantity = 9999; // High limit when stock validation is disabled
            return;
        }
        
        if ($this->selectedVariantId) {
            $variant = ProductVariant::find($this->selectedVariantId);
            $this->maxQuantity = $variant ? $variant->stock_quantity : 0;
        } else {
            $this->maxQuantity = 0;
        }
    }

    /**
     * Load quantity from cart if product already exists
     */
    protected function loadCartQuantity()
    {
        if ($this->selectedVariantId) {
            $cart = session()->get('cart', []);
            $cartKey = 'variant_' . $this->selectedVariantId;
            
            if (isset($cart[$cartKey])) {
                $this->quantity = $cart[$cartKey]['quantity'];
            } else {
                // Product removed from cart, reset to 1
                $this->quantity = 1;
            }
        }
    }

    /**
     * Refresh cart quantity when cart is updated from sidebar
     */
    public function refreshCartQuantity()
    {
        $this->loadCartQuantity();
    }

    /**
     * Increment quantity
     */
    public function increment()
    {
        if ($this->quantity < $this->maxQuantity) {
            $this->quantity++;
        }
    }

    /**
     * Decrement quantity
     */
    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    /**
     * Update quantity directly
     */
    public function updateQuantity($value)
    {
        $value = (int) $value;
        
        if ($value < 1) {
            $this->quantity = 1;
        } elseif ($value > $this->maxQuantity) {
            $this->quantity = $this->maxQuantity;
        } else {
            $this->quantity = $value;
        }
    }

    /**
     * Add product to cart
     */
    public function addToCart()
    {
        // Validate variant selection for variable products
        if ($this->product->product_type === 'variable' && !$this->selectedVariantId) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Please select product options'
            ]);
            return;
        }

        // Validate stock availability
        $variant = ProductVariant::find($this->selectedVariantId);
        
        if (!$variant) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Product variant not found'
            ]);
            return;
        }

        // Check if variant can be added to cart (considers global stock restriction setting)
        if (!$variant->canAddToCart()) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'This product is currently out of stock'
            ]);
            return;
        }

        // Only check stock quantity if restriction is enabled
        if (ProductVariant::isStockRestrictionEnabled() && $variant->stock_quantity < $this->quantity) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Insufficient stock available'
            ]);
            return;
        }

        // Get cart from session
        $cart = session()->get('cart', []);

        // Check if variant already in cart
        $cartKey = 'variant_' . $variant->id;
        
        // Always update/set the quantity (don't add to existing)
        $cart[$cartKey] = [
            'product_id' => $this->product->id,
            'variant_id' => $variant->id,
            'product_name' => $this->product->name,
            'variant_name' => $variant->name ?? null,
            'sku' => $variant->sku,
            'price' => $variant->sale_price ?? $variant->price,
            'original_price' => $variant->price,
            'quantity' => $this->quantity, // Set to selected quantity, not add
            'image' => $this->product->getPrimaryThumbnailUrl(), // Use media library
            'brand' => $this->product->brand ? $this->product->brand->name : null,
            'stock_quantity' => $variant->stock_quantity,
        ];

        // Save cart to session
        session()->put('cart', $cart);

        // Show success message
        $this->showSuccess = true;
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Cart updated successfully!'
        ]);

        // Update cart count in header
        $this->dispatch('cart-updated', ['count' => count($cart)]);

        // Keep the quantity as is (don't reset to 1)

        // Hide success message after 3 seconds
        $this->dispatch('hide-success-after', ['delay' => 3000]);
    }

    /**
     * Get claimed percentage based on sales and current cart quantity
     */
    public function getClaimedPercentageProperty()
    {
        if (!$this->selectedVariantId) {
            return 0;
        }

        $variant = ProductVariant::find($this->selectedVariantId);
        if (!$variant) {
            return 0;
        }

        // Get total sales count
        $salesCount = $this->product->sales_count ?? 0;
        
        // Add current cart quantity to simulate claimed amount
        $totalClaimed = $salesCount + $this->quantity;
        
        // Calculate percentage
        $totalAvailable = $totalClaimed + $variant->stock_quantity;
        $percentage = $totalAvailable > 0 
            ? min(round(($totalClaimed / $totalAvailable) * 100), 95)
            : rand(5, 25);
        
        return $percentage;
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.cart.add-to-cart');
    }
}
