<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Models\SaleOffer;

/**
 * ModuleName: Sale Offer Product Selector
 * Purpose: Search and select products for sale offers
 * 
 * Key Features:
 * - Real-time product search
 * - Filter by category/brand
 * - Display product details
 * - Add to sale offers
 * 
 * @category Livewire
 * @package  App\Livewire\Admin
 * @author   Admin
 * @created  2025-11-06
 */
class SaleOfferProductSelector extends Component
{
    public $search = '';
    public $selectedCategory = '';
    public $selectedBrand = '';

    protected $listeners = ['productAdded' => '$refresh'];

    public function selectProduct($productId)
    {
        // Check if product already exists in sale offers
        $exists = SaleOffer::where('product_id', $productId)->exists();
        
        if ($exists) {
            // Product already exists, just return without action
            return;
        }

        // Get the highest display order and add 1
        $maxOrder = SaleOffer::max('display_order') ?? 0;

        SaleOffer::create([
            'product_id' => $productId,
            'display_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        $this->search = '';
        
        // Set success message and reload the page
        session()->flash('success', 'Product added to sale offers successfully!');
        
        return redirect()->route('admin.sale-offers.index');
    }

    public function getProductsProperty()
    {
        $query = Product::with(['variants', 'categories', 'brand', 'images'])
            ->where('is_active', true)
            ->whereDoesntHave('saleOffer');

        // If search is provided, filter by search term
        if (strlen($this->search) > 0) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('variants', function($variantQuery) {
                      $variantQuery->where('sku', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply category filter
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        // Apply brand filter
        if ($this->selectedBrand) {
            $query->where('brand_id', $this->selectedBrand);
        }

        // If no search, show recent products (default load)
        if (strlen($this->search) < 1) {
            return $query->latest()
                ->limit(10)
                ->get();
        }

        return $query->limit(10)->get();
    }

    public function render()
    {
        return view('livewire.admin.sale-offer-product-selector', [
            'products' => $this->products,
        ]);
    }
}
