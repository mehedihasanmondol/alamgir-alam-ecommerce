<?php

namespace App\Livewire\Search;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Brand\Models\Brand;

/**
 * ModuleName: Mobile Search Component
 * Purpose: Provide mobile-optimized search interface
 * 
 * @category Livewire
 * @package  Search
 * @created  2025-11-12
 */
class MobileSearch extends Component
{
    #[Url(as: 'q', keep: true)]
    public $query = '';
    
    public $isOpen = false;
    public $recentSearches = [];
    
    protected $queryString = ['query' => ['as' => 'q', 'except' => '']];

    /**
     * Mount component
     */
    public function mount()
    {
        $this->recentSearches = session()->get('recent_searches', []);
    }

    /**
     * Open mobile search
     */
    public function openSearch()
    {
        $this->isOpen = true;
        $this->dispatch('mobile-search-opened');
    }

    /**
     * Close mobile search
     */
    public function closeSearch()
    {
        $this->isOpen = false;
        $this->dispatch('mobile-search-closed');
    }

    /**
     * Perform search
     */
    public function search()
    {
        if (trim($this->query)) {
            $this->addToRecentSearches(trim($this->query));
            $this->closeSearch();
            return redirect()->route('search.results', ['q' => trim($this->query)]);
        }
    }

    /**
     * Search from recent or popular
     */
    public function searchTerm($term)
    {
        $this->query = $term;
        $this->search();
    }

    /**
     * Update search query with suggestion
     */
    public function updateQuery($term)
    {
        $this->query = $term;
        // Don't perform search, just update the input
        // Dispatch event to maintain focus
        $this->dispatch('maintain-focus-mobile');
    }

    /**
     * Clear search
     */
    public function clearSearch()
    {
        $this->query = '';
    }

    /**
     * Clear recent searches
     */
    public function clearRecentSearches()
    {
        $this->recentSearches = [];
        session()->forget('recent_searches');
    }

    /**
     * Add to recent searches
     */
    protected function addToRecentSearches($query)
    {
        $recentSearches = session()->get('recent_searches', []);
        
        // Remove if already exists
        $recentSearches = array_filter($recentSearches, function($search) use ($query) {
            return strtolower($search) !== strtolower($query);
        });
        
        // Add to beginning
        array_unshift($recentSearches, $query);
        
        // Keep only last 10
        $recentSearches = array_slice($recentSearches, 0, 10);
        
        session()->put('recent_searches', $recentSearches);
        $this->recentSearches = $recentSearches;
    }

    /**
     * Get quick suggestions
     */
    public function getQuickSuggestionsProperty()
    {
        if (strlen($this->query) < 2) {
            return collect();
        }

        return Product::where('is_active', true)
            ->where(function($q) {
                $q->where('name', 'like', "%{$this->query}%")
                  ->orWhere('description', 'like', "%{$this->query}%");
            })
            ->limit(5)
            ->pluck('name')
            ->map(function($name) {
                return trim($name);
            })
            ->unique()
            ->values();
    }

    /**
     * Get popular searches
     */
    public function getPopularSearchesProperty()
    {
        return [
            'Vitamins',
            'Protein',
            'Omega 3',
            'Probiotics',
            'Multivitamin',
            'Collagen'
        ];
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.search.mobile-search', [
            'quickSuggestions' => $this->quickSuggestions,
            'popularSearches' => $this->popularSearches,
        ]);
    }
}
