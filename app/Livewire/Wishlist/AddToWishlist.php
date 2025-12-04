<?php

namespace App\Livewire\Wishlist;

use Livewire\Component;
use App\Modules\Ecommerce\Product\Models\Product;

/**
 * ModuleName: Add to Wishlist Component
 * Purpose: Toggle wishlist button for products
 * 
 * @category Livewire
 * @package  Wishlist
 * @created  2025-11-09
 */
class AddToWishlist extends Component
{
    public $productId;
    public $variantId;
    public $isInWishlist = false;
    public $showIcon = true; // Show only icon or icon + text
    public $size = 'md'; // sm, md, lg

    /**
     * Mount component
     */
    public function mount($productId, $variantId = null, $showIcon = true, $size = 'md')
    {
        $this->productId = $productId;
        $this->variantId = $variantId;
        $this->showIcon = $showIcon;
        $this->size = $size;
        $this->checkWishlist();
    }

    /**
     * Check if product is in wishlist
     */
    public function checkWishlist()
    {
        $wishlist = session()->get('wishlist', []);
        
        $key = 'product_' . $this->productId;
        if ($this->variantId) {
            $key = 'variant_' . $this->variantId;
        }
        
        $this->isInWishlist = isset($wishlist[$key]);
    }

    /**
     * Toggle wishlist
     */
    public function toggleWishlist()
    {
        $wishlist = session()->get('wishlist', []);
        
        $key = 'product_' . $this->productId;
        if ($this->variantId) {
            $key = 'variant_' . $this->variantId;
        }
        
        if (isset($wishlist[$key])) {
            // Remove from wishlist
            unset($wishlist[$key]);
            session()->put('wishlist', $wishlist);
            
            $this->isInWishlist = false;
            
            $this->dispatch('show-toast', [
                'type' => 'info',
                'message' => 'Removed from wishlist'
            ]);
        } else {
            // Add to wishlist
            $product = Product::with(['variants', 'images', 'brand'])->find($this->productId);
            
            if (!$product) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => 'Product not found'
                ]);
                return;
            }
            
            $variant = null;
            if ($this->variantId) {
                $variant = $product->variants->where('id', $this->variantId)->first();
            } else {
                $variant = $product->variants->first();
            }
            
            $wishlist[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variant->id ?? null,
                'product_name' => $product->name,
                'slug' => $product->slug,
                'brand' => $product->brand ? $product->brand->name : null,
                'price' => $variant->sale_price ?? $variant->price ?? 0,
                'original_price' => $variant->price ?? 0,
                'image' => $product->getPrimaryThumbnailUrl(), // Use media library
                'sku' => $variant->sku ?? null,
                'added_at' => now()->toDateTimeString(),
            ];
            
            session()->put('wishlist', $wishlist);
            
            $this->isInWishlist = true;
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Added to wishlist!'
            ]);
        }
        
        // Dispatch event to update wishlist counter
        $this->dispatch('wishlist-updated');
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.wishlist.add-to-wishlist');
    }
}
