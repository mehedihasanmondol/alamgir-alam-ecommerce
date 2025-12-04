<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Models\TrendingProduct;

class TrendingProductSelector extends Component
{
    public $search = '';
    public $selectedCategory = '';
    public $selectedBrand = '';

    protected $listeners = ['productAdded' => '$refresh'];

    public function selectProduct($productId)
    {
        // Check if product already exists in trending
        $exists = TrendingProduct::where('product_id', $productId)->exists();
        
        if ($exists) {
            session()->flash('error', 'Product already in trending list!');
            return;
        }

        // Get the highest sort order and add 1
        $maxOrder = TrendingProduct::max('sort_order') ?? 0;

        TrendingProduct::create([
            'product_id' => $productId,
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        $this->search = '';
        
        session()->flash('success', 'Product added to trending successfully!');
        
        return redirect()->route('admin.trending-products.index');
    }

    public function getProductsProperty()
    {
        $query = Product::with(['variants', 'categories', 'brand', 'images'])
            ->where('is_active', true)
            ->whereDoesntHave('trendingProduct');

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
        return view('livewire.admin.trending-product-selector', [
            'products' => $this->products,
        ]);
    }
}
