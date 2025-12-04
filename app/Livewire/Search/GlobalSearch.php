<?php

namespace App\Livewire\Search;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Modules\Blog\Models\Post as BlogPost;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\Tag as BlogTag;

/**
 * ModuleName: Global Search Component
 * Purpose: Comprehensive search across all content types with advanced UI/UX
 * 
 * @category Livewire
 * @package  Search
 * @created  2025-11-12
 */
class GlobalSearch extends Component
{
    #[Url(as: 'q', keep: true)]
    public $query = '';
    
    public $showResults = false;
    public $isLoading = false;
    public $selectedTab = 'all';
    public $searchHistory = [];
    
    protected $queryString = ['query' => ['as' => 'q', 'except' => '']];

    /**
     * Mount component
     */
    public function mount()
    {
        $this->searchHistory = session()->get('search_history', []);
    }

    /**
     * Updated query - trigger search
     */
    public function updatedQuery()
    {
        $this->isLoading = true;
        
        if (strlen($this->query) >= 2) {
            $this->showResults = true;
            $this->selectedTab = 'all';
        } else {
            $this->showResults = false;
        }
        
        $this->isLoading = false;
    }

    /**
     * Perform search and redirect
     */
    public function search()
    {
        if (trim($this->query)) {
            $this->addToSearchHistory(trim($this->query));
            return redirect()->route('search.results', ['q' => trim($this->query)]);
        }
    }

    /**
     * Search specific content type
     */
    public function searchType($type, $query = null)
    {
        $searchQuery = $query ?? $this->query;
        if (trim($searchQuery)) {
            $this->addToSearchHistory(trim($searchQuery));
            
            switch ($type) {
                case 'products':
                    return redirect()->route('shop', ['q' => trim($searchQuery)]);
                case 'categories':
                    return redirect()->route('categories.index', ['q' => trim($searchQuery)]);
                case 'brands':
                    return redirect()->route('brands.index', ['q' => trim($searchQuery)]);
                case 'blogs':
                    return redirect()->route('blog.index', ['q' => trim($searchQuery)]);
                default:
                    return redirect()->route('search.results', ['q' => trim($searchQuery)]);
            }
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
     * Set active tab
     */
    public function setTab($tab)
    {
        $this->selectedTab = $tab;
    }

    /**
     * Get product suggestions
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
     * Get blog post suggestions
     */
    public function getBlogSuggestionsProperty()
    {
        if (strlen($this->query) < 2) {
            return collect();
        }

        return BlogPost::where('status', 'published')
            ->where(function($q) {
                $q->where('title', 'like', "%{$this->query}%")
                  ->orWhere('content', 'like', "%{$this->query}%")
                  ->orWhere('excerpt', 'like', "%{$this->query}%");
            })
            ->with(['author', 'categories'])
            ->limit(4)
            ->get();
    }

    /**
     * Get blog category suggestions
     */
    public function getBlogCategorySuggestionsProperty()
    {
        if (strlen($this->query) < 2) {
            return collect();
        }

        return BlogCategory::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->withCount(['posts' => function($q) {
                $q->where('status', 'published');
            }])
            ->having('posts_count', '>', 0)
            ->limit(3)
            ->get();
    }

    /**
     * Get blog tag suggestions
     */
    public function getBlogTagSuggestionsProperty()
    {
        if (strlen($this->query) < 2) {
            return collect();
        }

        return BlogTag::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->withCount(['posts' => function($q) {
                $q->where('status', 'published');
            }])
            ->having('posts_count', '>', 0)
            ->limit(6)
            ->get();
    }

    /**
     * Get search suggestions (autocomplete)
     */
    public function getSearchSuggestionsProperty()
    {
        if (strlen($this->query) < 2) {
            return collect();
        }

        $suggestions = collect();

        // Product names
        $productNames = Product::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->limit(3)
            ->pluck('name');

        // Category names
        $categoryNames = Category::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->limit(2)
            ->pluck('name');

        // Brand names
        $brandNames = Brand::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->limit(2)
            ->pluck('name');

        // Blog titles
        $blogTitles = BlogPost::where('status', 'published')
            ->where('title', 'like', "%{$this->query}%")
            ->limit(2)
            ->pluck('title');

        return $suggestions
            ->concat($productNames)
            ->concat($categoryNames)
            ->concat($brandNames)
            ->concat($blogTitles)
            ->unique()
            ->take(8);
    }

    /**
     * Get search counts
     */
    public function getSearchCountsProperty()
    {
        if (strlen($this->query) < 2) {
            return [
                'products' => 0,
                'categories' => 0,
                'brands' => 0,
                'blogs' => 0,
                'blog_categories' => 0,
                'blog_tags' => 0,
                'total' => 0
            ];
        }

        $productCount = $this->productSuggestions->count();
        $categoryCount = $this->categorySuggestions->count();
        $brandCount = $this->brandSuggestions->count();
        $blogCount = $this->blogSuggestions->count();
        $blogCategoryCount = $this->blogCategorySuggestions->count();
        $blogTagCount = $this->blogTagSuggestions->count();

        return [
            'products' => $productCount,
            'categories' => $categoryCount,
            'brands' => $brandCount,
            'blogs' => $blogCount,
            'blog_categories' => $blogCategoryCount,
            'blog_tags' => $blogTagCount,
            'total' => $productCount + $categoryCount + $brandCount + $blogCount + $blogCategoryCount + $blogTagCount
        ];
    }

    /**
     * Get popular searches
     */
    public function getPopularSearchesProperty()
    {
        return [
            'Vitamins & Supplements',
            'Protein Powder',
            'Omega 3 Fish Oil',
            'Probiotics',
            'Multivitamin',
            'Collagen',
            'Magnesium',
            'Vitamin D3',
            'Health Tips',
            'Nutrition Guide'
        ];
    }

    /**
     * Navigate to specific item
     */
    public function goToProduct($slug)
    {
        $this->hideResults();
        return redirect()->route('products.show', $slug);
    }

    public function goToCategory($slug)
    {
        $this->hideResults();
        return redirect()->route('categories.show', $slug);
    }

    public function goToBrand($slug)
    {
        $this->hideResults();
        return redirect()->route('brands.show', $slug);
    }

    public function goToBlog($slug)
    {
        $this->hideResults();
        return redirect()->route('products.show', $slug);
    }

    public function goToBlogCategory($slug)
    {
        $this->hideResults();
        return redirect()->route('blog.category', $slug);
    }

    public function goToBlogTag($slug)
    {
        $this->hideResults();
        return redirect()->route('blog.tag', $slug);
    }

    /**
     * Search popular term
     */
    public function searchPopular($term)
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
        $this->dispatch('maintain-focus');
    }

    /**
     * Search from history
     */
    public function searchFromHistory($term)
    {
        $this->query = $term;
        $this->search();
    }

    /**
     * Clear search history
     */
    public function clearSearchHistory()
    {
        $this->searchHistory = [];
        session()->forget('search_history');
    }

    /**
     * Add to search history
     */
    protected function addToSearchHistory($query)
    {
        $history = session()->get('search_history', []);
        
        // Remove if already exists
        $history = array_filter($history, function($item) use ($query) {
            return strtolower($item) !== strtolower($query);
        });
        
        // Add to beginning
        array_unshift($history, $query);
        
        // Keep only last 10
        $history = array_slice($history, 0, 10);
        
        session()->put('search_history', $history);
        $this->searchHistory = $history;
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.search.global-search', [
            'productSuggestions' => $this->productSuggestions,
            'categorySuggestions' => $this->categorySuggestions,
            'brandSuggestions' => $this->brandSuggestions,
            'blogSuggestions' => $this->blogSuggestions,
            'blogCategorySuggestions' => $this->blogCategorySuggestions,
            'blogTagSuggestions' => $this->blogTagSuggestions,
            'searchSuggestions' => $this->searchSuggestions,
            'searchCounts' => $this->searchCounts,
            'popularSearches' => $this->popularSearches,
        ]);
    }
}
