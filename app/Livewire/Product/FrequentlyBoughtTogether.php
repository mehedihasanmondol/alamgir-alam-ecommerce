<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;

/**
 * ModuleName: Frequently Bought Together
 * Purpose: Handle frequently bought together bundle functionality
 * 
 * @category Livewire
 * @package  Product
 * @created  2025-11-18
 */
class FrequentlyBoughtTogether extends Component
{
    public $product;
    public $relatedProducts;
    public $selectedItems = [];
    public $bundleItems = [];

    /**
     * Mount component
     */
    public function mount($product, $relatedProducts)
    {
        $this->product = $product;
        $this->relatedProducts = $relatedProducts;
        
        // Build bundle items first
        $this->buildBundleItems();
        
        // Check which items are already in cart and pre-select them
        $this->checkCartItems();
    }
    
    /**
     * Check which items are in cart and pre-select them
     */
    protected function checkCartItems()
    {
        $cart = session()->get('cart', []);
        $this->selectedItems = [];
        
        foreach ($this->bundleItems as $item) {
            $cartKey = 'variant_' . $item['variant_id'];
            
            // If item is in cart, select it
            if (isset($cart[$cartKey])) {
                $this->selectedItems[] = $item['id'];
            }
        }
        
        // Always select current product
        if (!in_array($this->product->id, $this->selectedItems)) {
            $this->selectedItems[] = $this->product->id;
        }
    }

    /**
     * Build bundle items array
     */
    protected function buildBundleItems()
    {
        $items = [];
        
        // Add current product
        $currentVariant = $this->product->variants->first();
        $currentPrice = $currentVariant->sale_price ?? $currentVariant->price ?? 0;
        
        $items[] = [
            'id' => $this->product->id,
            'variant_id' => $currentVariant->id,
            'name' => $this->product->name,
            'slug' => $this->product->slug,
            'price' => $currentPrice,
            'image' => $this->product->getPrimaryThumbnailUrl(), // Use media library
            'rating' => $this->product->average_rating ?? 4.5,
            'reviews' => $this->product->review_count ?? rand(1000, 50000),
            'isCurrent' => true,
            'canAddToCart' => $currentVariant->canAddToCart(),
        ];
        
        // Add related products (max 2)
        foreach($this->relatedProducts->take(2) as $related) {
            $variant = $related->variants->first();
            $price = $variant->sale_price ?? $variant->price ?? 0;
            
            $items[] = [
                'id' => $related->id,
                'variant_id' => $variant->id,
                'name' => $related->name,
                'slug' => $related->slug,
                'price' => $price,
                'image' => $related->getPrimaryThumbnailUrl(), // Use media library
                'rating' => $related->average_rating ?? 4.5,
                'reviews' => $related->review_count ?? rand(1000, 50000),
                'isCurrent' => false,
                'canAddToCart' => $variant->canAddToCart(),
            ];
        }
        
        $this->bundleItems = $items;
    }

    /**
     * Toggle item selection
     */
    public function toggleItem($productId)
    {
        // Don't allow deselecting current product
        if ($productId == $this->product->id) {
            return;
        }
        
        if (in_array($productId, $this->selectedItems)) {
            $this->selectedItems = array_values(array_filter($this->selectedItems, fn($id) => $id != $productId));
        } else {
            $this->selectedItems[] = $productId;
        }
    }

    /**
     * Get total price of selected items
     */
    public function getTotalPriceProperty()
    {
        $total = 0;
        foreach ($this->bundleItems as $item) {
            if (in_array($item['id'], $this->selectedItems)) {
                $total += $item['price'];
            }
        }
        return number_format($total, 2);
    }

    /**
     * Get selected count
     */
    public function getSelectedCountProperty()
    {
        return count($this->selectedItems);
    }
    
    /**
     * Check if all bundle items are already in cart
     */
    public function getAllInCartProperty()
    {
        $cart = session()->get('cart', []);
        
        foreach ($this->bundleItems as $item) {
            $cartKey = 'variant_' . $item['variant_id'];
            
            // If any item is not in cart, return false
            if (!isset($cart[$cartKey])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Add selected items to cart
     */
    public function addSelectedToCart()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Please select items to add to cart'
            ]);
            return;
        }

        $cart = session()->get('cart', []);
        $addedCount = 0;
        $errors = [];

        foreach ($this->bundleItems as $item) {
            if (!in_array($item['id'], $this->selectedItems)) {
                continue;
            }

            // Check if item can be added
            if (!$item['canAddToCart']) {
                $errors[] = $item['name'] . ' is out of stock';
                continue;
            }

            // Get full product and variant data
            $product = Product::with(['images', 'brand'])->find($item['id']);
            $variant = ProductVariant::find($item['variant_id']);

            if (!$product || !$variant) {
                continue;
            }

            // Check stock restriction
            if (!$variant->canAddToCart()) {
                $errors[] = $item['name'] . ' is currently out of stock';
                continue;
            }

            // Add to cart
            $cartKey = 'variant_' . $variant->id;
            $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();

            if (isset($cart[$cartKey])) {
                // Increment quantity if already in cart
                $cart[$cartKey]['quantity'] += 1;
            } else {
                // Add new item
                $cart[$cartKey] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'product_name' => $product->name,
                    'variant_name' => $variant->name ?? null,
                    'sku' => $variant->sku,
                    'price' => $variant->sale_price ?? $variant->price,
                    'original_price' => $variant->price,
                    'quantity' => 1,
                    'image' => $product->getPrimaryThumbnailUrl(), // Use media library
                    'brand' => $product->brand ? $product->brand->name : null,
                    'stock_quantity' => $variant->stock_quantity,
                ];
            }

            $addedCount++;
        }

        // Save cart
        session()->put('cart', $cart);

        // Show messages
        if ($addedCount > 0) {
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => $addedCount . ' item(s) added to cart successfully!'
            ]);

            // Update cart count
            $this->dispatch('cart-updated', ['count' => count($cart)]);
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => $error
                ]);
            }
        }

        if ($addedCount === 0 && empty($errors)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Unable to add items to cart'
            ]);
        }
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.product.frequently-bought-together');
    }
}
