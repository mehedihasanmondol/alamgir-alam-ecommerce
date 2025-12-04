<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Modules\Ecommerce\Product\Models\Product;

/**
 * ModuleName: Cart Sidebar Component
 * Purpose: Display slide-out cart sidebar with cart items
 * 
 * Key Methods:
 * - showCart(): Show the cart sidebar
 * - hideCart(): Hide the cart sidebar
 * - removeItem(): Remove item from cart
 * - updateItemQuantity(): Update item quantity
 * 
 * Dependencies:
 * - Session (for cart storage)
 * 
 * @category Livewire
 * @package  Cart
 * @author   Windsurf AI
 * @created  2025-11-09
 */
class CartSidebar extends Component
{
    public $isOpen = false;
    public $cartItems = [];
    public $subtotal = 0;
    public $frequentlyPurchased = [];

    /**
     * Mount component
     */
    public function mount()
    {
        $this->loadCart();
    }

    /**
     * Listen for cart-updated event and show sidebar
     */
    #[On('cart-updated')]
    public function showCart()
    {
        $this->loadCart();
        $this->isOpen = true;
    }

    /**
     * Listen for add-to-cart event from product cards
     */
    #[On('add-to-cart')]
    public function addToCart($productId, $variantId, $quantity = 1)
    {
        $product = Product::with(['variants', 'images', 'brand'])->find($productId);
        
        if (!$product) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Product not found'
            ]);
            return;
        }
        
        $variant = $product->variants->where('id', $variantId)->first();
        
        if (!$variant) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Product variant not found'
            ]);
            return;
        }
        
        // Check stock
        if ($variant->stock_quantity < $quantity) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Insufficient stock'
            ]);
            return;
        }
        
        $cart = session()->get('cart', []);
        $cartKey = 'variant_' . $variant->id;
        
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'product_name' => $product->name,
                'slug' => $product->slug,
                'brand' => $product->brand ? $product->brand->name : null,
                'price' => $variant->sale_price ?? $variant->price,
                'original_price' => $variant->price,
                'quantity' => $quantity,
                'image' => $product->getPrimaryThumbnailUrl(), // Use media library
                'sku' => $variant->sku,
                'stock_quantity' => $variant->stock_quantity,
            ];
        }
        
        session()->put('cart', $cart);
        
        $this->loadCart();
        $this->isOpen = true; // Show cart sidebar when item is added
        
        $this->dispatch('cart-updated');
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Product added to cart!'
        ]);
    }

    /**
     * Hide cart sidebar
     */
    public function hideCart()
    {
        $this->isOpen = false;
    }

    /**
     * Load cart from session
     */
    protected function loadCart()
    {
        $cart = session()->get('cart', []);
        $this->cartItems = $cart;
        $this->calculateSubtotal();
        $this->loadFrequentlyPurchased();
    }

    /**
     * Load frequently purchased together products
     * Uses same logic as product view - gets products from same categories as cart items
     */
    protected function loadFrequentlyPurchased()
    {
        if (empty($this->cartItems)) {
            $this->frequentlyPurchased = [];
            return;
        }

        // Get product IDs from cart
        $cartProductIds = collect($this->cartItems)->pluck('product_id')->unique()->toArray();

        // Get category IDs from cart products (many-to-many relationship)
        $cartProducts = Product::with('categories')->whereIn('id', $cartProductIds)->get();
        $categoryIds = $cartProducts->pluck('categories')->flatten()->pluck('id')->unique()->filter()->toArray();

        if (empty($categoryIds)) {
            $this->frequentlyPurchased = [];
            return;
        }

        // Get related products from same categories (same logic as product view)
        // This matches the frequently-purchased-together component logic
        $relatedProducts = Product::with(['variants', 'images', 'brand'])
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->whereNotIn('id', $cartProductIds)
            ->where('is_active', true)
            ->limit(3)
            ->get();

        $this->frequentlyPurchased = $relatedProducts->map(function($product) {
            $variant = $product->variants->first();
            
            return [
                'id' => $product->id,
                'product_id' => $product->id,
                'variant_id' => $variant->id ?? null,
                'name' => $product->name,
                'slug' => $product->slug,
                'brand' => $product->brand ? $product->brand->name : null,
                'price' => $variant->sale_price ?? $variant->price ?? 0,
                'original_price' => $variant->price ?? 0,
                'image' => $product->getPrimaryThumbnailUrl(), // Use media library
                'rating' => $product->average_rating ?? 0,
                'reviews' => $product->review_count ?? 0,
                'sku' => $variant->sku ?? null,
            ];
        })->toArray();
    }

    /**
     * Calculate cart subtotal
     */
    protected function calculateSubtotal()
    {
        $this->subtotal = 0;
        foreach ($this->cartItems as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem($cartKey)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
            
            $this->loadCart();
            $this->dispatch('cart-updated');
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Item removed from cart'
            ]);
        }
    }

    /**
     * Update item quantity
     */
    public function updateItemQuantity($cartKey, $quantity)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$cartKey]) && $quantity > 0) {
            $cart[$cartKey]['quantity'] = $quantity;
            session()->put('cart', $cart);
            
            $this->loadCart();
            $this->dispatch('cart-updated');
        }
    }

    /**
     * Add frequently purchased item to cart
     */
    public function addFrequentlyPurchasedToCart($productData)
    {
        $cart = session()->get('cart', []);
        $cartKey = 'variant_' . $productData['variant_id'];
        
        if (isset($cart[$cartKey])) {
            // Update quantity if already in cart
            $cart[$cartKey]['quantity'] += 1;
        } else {
            // Add new item
            $cart[$cartKey] = [
                'product_id' => $productData['product_id'],
                'variant_id' => $productData['variant_id'],
                'product_name' => $productData['name'],
                'variant_name' => null,
                'sku' => $productData['sku'],
                'price' => $productData['price'],
                'original_price' => $productData['original_price'],
                'quantity' => 1,
                'image' => $productData['image'],
                'brand' => $productData['brand'],
                'stock_quantity' => 999, // Default, would need to fetch actual stock
            ];
        }
        
        session()->put('cart', $cart);
        
        $this->loadCart();
        $this->dispatch('cart-updated');
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Product added to cart!'
        ]);
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.cart.cart-sidebar');
    }
}
