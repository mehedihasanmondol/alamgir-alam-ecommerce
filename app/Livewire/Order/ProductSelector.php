<?php

namespace App\Livewire\Order;

use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use Livewire\Component;

class ProductSelector extends Component
{
    public $search = '';
    public $selectedProducts = [];
    public $showDropdown = false;
    public $products = [];
    
    protected $listeners = ['productSelected'];

    public function mount()
    {
        $this->loadProducts();
        $this->showDropdown = false; // Don't show on mount
    }

    public function updatedSearch()
    {
        $this->loadProducts();
        $this->showDropdown = true; // Always show when typing
    }

    public function loadProducts()
    {
        if (empty($this->search)) {
            // Show recent/popular products when no search
            $this->products = Product::with(['variants', 'brand', 'categories'])
                ->where('is_active', true)
                ->where('status', 'published')
                ->orderBy('name')
                ->limit(10)
                ->get();
        } else {
            $this->products = Product::with(['variants', 'brand', 'categories'])
                ->where('is_active', true)
                ->where('status', 'published')
                ->where(function($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%');
                })
                ->orderBy('name')
                ->limit(20)
                ->get();
        }
    }
    
    public function showProducts()
    {
        $this->showDropdown = true;
        $this->loadProducts();
    }

    public function selectProduct($productId, $variantId = null)
    {
        $product = Product::with(['variants', 'brand', 'categories'])->find($productId);
        
        if (!$product) {
            return;
        }

        // Get the variant or default variant
        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
        } else {
            // For variable products, always select a variant
            if ($product->product_type === 'variable') {
                $variant = $product->variants->where('is_default', true)->first() 
                        ?? $product->variants->first();
                
                // If no variant exists for variable product, show error
                if (!$variant) {
                    session()->flash('error', 'This product has no variants configured.');
                    return;
                }
            }
        }
        
        // Ensure variant_id is never null for variable products
        if ($product->product_type === 'variable' && !$variant) {
            session()->flash('error', 'Please select a variant for this product.');
            return;
        }

        // Get image URL from media library
        $imageUrl = $product->getPrimaryThumbnailUrl();
        
        $productData = [
            'product_id' => $product->id,
            'variant_id' => $variant->id, // Always required per .windsurfrules
            'name' => $product->name,
            'variant_name' => $variant->name ?? null,
            'sku' => $variant->sku ?? 'N/A',
            'price' => $variant->price ?? $product->price ?? 0,
            'sale_price' => $variant->sale_price ?? null,
            'stock_quantity' => $variant->stock_quantity ?? 0,
            'quantity' => 1,
            'image' => $imageUrl,
        ];

        // Dispatch to browser window for Alpine.js to catch
        $this->dispatch('product-selected', productData: $productData);
        
        $this->search = '';
        $this->showDropdown = false;
        $this->loadProducts();
    }

    public function closeDropdown()
    {
        $this->showDropdown = false;
    }

    public function render()
    {
        return view('livewire.order.product-selector');
    }
}
