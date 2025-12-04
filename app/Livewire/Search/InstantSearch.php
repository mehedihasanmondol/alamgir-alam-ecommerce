<?php

namespace App\Livewire\Search;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Brand\Models\Brand;

/**
 * ModuleName: Instant Search Component
 * Purpose: Provide instant search results with suggestions and categories
 * 
 * @category Livewire
 * @package  Search
 * @created  2025-11-12
 */
class InstantSearch extends Component
{
    #[Url(as: 'q', keep: true)]
    public $query = '';
    
    public $showResults = false;
    public $isLoading = false;
    
    protected $queryString = ['query' => ['as' => 'q', 'except' => '']];

    /**
     * Updated query - trigger search
     */
    public function updatedQuery()
    {
        $this->isLoading = true;
        
        if (strlen($this->query) >= 2) {
            $this->showResults = true;
        } else {
            $this->showResults = false;
        }
        
        $this->isLoading = false;
    }

    /**
     * Perform search and redirect to shop page
     */
    public function search()
    {
        if (trim($this->query)) {
            return redirect()->route('shop', ['q' => trim($this->query)]);
        }
    }

    /**
     * Clear search
     */
    public function clearSearch()
    {
        $this->query = '';
        $this->showResults = false;
    }

    /**
     * Hide results
     */
    public function hideResults()
    {
        $this->showResults = false;
    }

    /**
     * Get search suggestions - products
     */
    public function getProductSuggestionsProperty()
    {
        if (strlen($this->query) < 2) {
            return collect();
        }

        return Product::with(['images', 'brand', 'defaultVariant'])
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('name', 'like', "%{$this->query}%")
                  ->orWhere('description', 'like', "%{$this->query}%")
                  ->orWhereHas('variants', function($variantQuery) {
                      $variantQuery->where('sku', 'like', "%{$this->query}%");
                  });
            })
            ->limit(6)
            ->get();
    }

    /**
     * Get category suggestions
     */
    public function getCategorySuggestionsProperty()
    {
        if (strlen($this->query) < 2) {
            return collect();
        }

        return Category::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->withCount(['products' => function($q) {
                $q->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->limit(4)
            ->get();
    }

    /**
     * Get brand suggestions
     */
    public function getBrandSuggestionsProperty()
    {
        if (strlen($this->query) < 2) {
            return collect();
        }

        return Brand::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->withCount(['products' => function($q) {
                $q->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->limit(4)
            ->get();
    }

    /**
     * Get popular search terms (you can implement this based on search logs)
     */
    public function getPopularSearchesProperty()
    {
        // For now, return static popular searches
        // In production, you might want to track search queries and return popular ones
        return [
            'Vitamins',
            'Protein Powder',
            'Omega 3',
            'Probiotics',
            'Multivitamin',
            'Collagen',
            'Magnesium',
            'Vitamin D'
        ];
    }

    /**
     * Navigate to category
     */
    public function goToCategory($slug)
    {
        $this->hideResults();
        return redirect()->route('categories.show', $slug);
    }

    /**
     * Navigate to brand
     */
    public function goToBrand($slug)
    {
        $this->hideResults();
        return redirect()->route('brands.show', $slug);
    }

    /**
     * Navigate to product
     */
    public function goToProduct($slug)
    {
        $this->hideResults();
        return redirect()->route('products.show', $slug);
    }

    /**
     * Search for popular term
     */
    public function searchPopular($term)
    {
        $this->query = $term;
        $this->search();
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.search.instant-search', [
            'productSuggestions' => $this->productSuggestions,
            'categorySuggestions' => $this->categorySuggestions,
            'brandSuggestions' => $this->brandSuggestions,
            'popularSearches' => $this->popularSearches,
        ]);
    }
}
