<?php

namespace App\Livewire\Stock;

use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use Livewire\Component;

class ProductSelector extends Component
{
    public $search = '';
    public $selectedProduct = null;
    public $selectedVariant = null;
    public $showDropdown = false;
    public $variants = [];
    
    // Form fields to emit
    public $productId;
    public $variantId;
    public $productName;
    public $variantName;
    public $currentStock = 0;
    public $sku = '';
    
    // Pre-selection parameters
    public $preSelectedProductId = null;
    public $preSelectedVariantId = null;

    protected $listeners = ['resetSelector'];
    
    public function mount($preSelectedProductId = null, $preSelectedVariantId = null)
    {
        $this->preSelectedProductId = $preSelectedProductId;
        $this->preSelectedVariantId = $preSelectedVariantId;
        
        // Pre-select if values exist (from validation errors)
        if ($this->preSelectedProductId) {
            $this->selectProduct($this->preSelectedProductId, $this->preSelectedVariantId);
        }
    }

    public function updatedSearch()
    {
        $this->showDropdown = true;
    }
    
    public function showProducts()
    {
        $this->showDropdown = true;
    }

    public function getProductsProperty()
    {
        if (empty($this->search)) {
            // Show recent products when no search
            return Product::with(['variants', 'brand', 'categories', 'images'])
                ->where('is_active', true)
                ->orderBy('name')
                ->limit(10)
                ->get();
        }
        
        $searchTerm = $this->search;
        return Product::with(['variants', 'brand', 'categories', 'images'])
            ->where('is_active', true)
            ->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('variants', function($q) use ($searchTerm) {
                          $q->where('sku', 'like', '%' . $searchTerm . '%');
                      });
            })
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    public function selectProduct($productId, $variantId = null)
    {
        $this->selectedProduct = Product::with(['variants', 'images'])->find($productId);
        
        if (!$this->selectedProduct) {
            return;
        }

        $this->productId = $this->selectedProduct->id;
        $this->productName = $this->selectedProduct->name;
        
        // Load variants for display
        $this->variants = $this->selectedProduct->variants()
            ->where('is_active', true)
            ->get();
        
        // Get the variant
        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
        } else {
            // Get default or first variant
            $variant = $this->selectedProduct->variants->where('is_default', true)->first() 
                    ?? $this->selectedProduct->variants->first();
        }
        
        if ($variant) {
            $this->selectVariant($variant->id);
        }
        
        $this->showDropdown = false;
        $this->search = '';
    }

    public function selectVariant($variantId)
    {
        $variant = ProductVariant::find($variantId);
        
        if ($variant) {
            $this->selectedVariant = $variant;
            $this->variantId = $variant->id;
            $this->variantName = $variant->name;
            $this->currentStock = $variant->stock_quantity ?? 0;
            $this->sku = $variant->sku;
            
            // Emit event to parent component
            $this->dispatch('productSelected', [
                'productId' => $this->productId,
                'variantId' => $this->variantId,
                'productName' => $this->productName,
                'variantName' => $this->variantName,
                'currentStock' => $this->currentStock,
                'sku' => $this->sku
            ]);
        }
    }

    public function resetSelector()
    {
        $this->reset([
            'search',
            'selectedProduct',
            'selectedVariant',
            'showDropdown',
            'variants',
            'productId',
            'variantId',
            'productName',
            'variantName',
            'currentStock',
            'sku'
        ]);
    }

    public function render()
    {
        return view('livewire.stock.product-selector');
    }
}
