<?php

namespace App\Livewire\Search;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Modules\Blog\Models\Post as BlogPost;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\Tag as BlogTag;

/**
 * ModuleName: Search Results Component
 * Purpose: Display comprehensive search results with filters
 * 
 * @category Livewire
 * @package  Search
 * @created  2025-11-12
 */
class SearchResults extends Component
{
    use WithPagination;

    #[Url(as: 'q', keep: true)]
    public $query = '';
    
    #[Url(as: 'type')]
    public $searchType = 'all'; // all, products, categories, brands
    
    #[Url(as: 'sort')]
    public $sortBy = 'relevance';
    
    public $showFilters = false;

    protected $queryString = [
        'query' => ['as' => 'q', 'except' => ''],
        'searchType' => ['as' => 'type', 'except' => 'all'],
        'sortBy' => ['as' => 'sort', 'except' => 'relevance']
    ];

    /**
     * Mount component
     */
    public function mount($query = '')
    {
        $this->query = $query ?: request('q', '');
    }

    /**
     * Updated query - reset pagination
     */
    public function updatedQuery()
    {
        $this->resetPage();
    }

    /**
     * Updated search type - reset pagination
     */
    public function updatedSearchType()
    {
        $this->resetPage();
    }

    /**
     * Updated sort - reset pagination
     */
    public function updatedSortBy()
    {
        $this->resetPage();
    }

    /**
     * Get search results based on type
     */
    public function getResultsProperty()
    {
        if (!$this->query || strlen($this->query) < 2) {
            return collect();
        }

        switch ($this->searchType) {
            case 'products':
                return $this->getProductResults();
            case 'categories':
                return $this->getCategoryResults();
            case 'brands':
                return $this->getBrandResults();
            case 'blogs':
                return $this->getBlogResults();
            case 'blog_categories':
                return $this->getBlogCategoryResults();
            case 'blog_tags':
                return $this->getBlogTagResults();
            default:
                return $this->getAllResults();
        }
    }

    /**
     * Get product search results
     */
    protected function getProductResults()
    {
        $query = Product::with(['images', 'brand', 'defaultVariant', 'categories'])
            ->where('is_active', true);

        // Search logic
        $searchTerms = explode(' ', $this->query);
        $query->where(function($q) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $term = trim($term);
                if (strlen($term) >= 2) {
                    $q->where(function($subQuery) use ($term) {
                        $subQuery->where('name', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%")
                                ->orWhereHas('variants', function($variantQuery) use ($term) {
                                    $variantQuery->where('sku', 'like', "%{$term}%");
                                })
                                ->orWhereHas('brand', function($brandQuery) use ($term) {
                                    $brandQuery->where('name', 'like', "%{$term}%");
                                })
                                ->orWhereHas('categories', function($catQuery) use ($term) {
                                    $catQuery->where('name', 'like', "%{$term}%");
                                });
                    });
                }
            }
        });

        // Sorting
        switch ($this->sortBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_low':
                $query->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) DESC');
                break;
            case 'latest':
                $query->latest();
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'relevance':
            default:
                // Simple relevance: exact matches first, then partial matches
                $query->orderByRaw("CASE 
                    WHEN name LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    ELSE 3
                END", [
                    $this->query,
                    "%{$this->query}%"
                ]);
                break;
        }

        return $query->paginate(24);
    }

    /**
     * Get category search results
     */
    protected function getCategoryResults()
    {
        return Category::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->withCount(['products' => function($q) {
                $q->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->paginate(12);
    }

    /**
     * Get brand search results
     */
    protected function getBrandResults()
    {
        return Brand::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->withCount(['products' => function($q) {
                $q->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->paginate(12);
    }

    /**
     * Get blog search results
     */
    protected function getBlogResults()
    {
        return BlogPost::where('status', 'published')
            ->where(function($q) {
                $q->where('title', 'like', "%{$this->query}%")
                  ->orWhere('content', 'like', "%{$this->query}%")
                  ->orWhere('excerpt', 'like', "%{$this->query}%");
            })
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->paginate(12);
    }

    /**
     * Get blog category search results
     */
    protected function getBlogCategoryResults()
    {
        return BlogCategory::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->withCount(['posts' => function($q) {
                $q->where('status', 'published');
            }])
            ->having('posts_count', '>', 0)
            ->orderBy('name')
            ->paginate(12);
    }

    /**
     * Get blog tag search results
     */
    protected function getBlogTagResults()
    {
        return BlogTag::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->withCount(['posts' => function($q) {
                $q->where('status', 'published');
            }])
            ->having('posts_count', '>', 0)
            ->orderBy('name')
            ->paginate(12);
    }

    /**
     * Get all results (mixed) - returns products for main display
     */
    protected function getAllResults()
    {
        // For 'all' search type, return products as main results
        // Other content types will be accessed via suggestion properties
        return $this->getProductResults();
    }

    /**
     * Get search statistics
     */
    public function getSearchStatsProperty()
    {
        if (!$this->query || strlen($this->query) < 2) {
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

        $productCount = Product::where('is_active', true)
            ->where(function($q) {
                $searchTerms = explode(' ', $this->query);
                foreach ($searchTerms as $term) {
                    $term = trim($term);
                    if (strlen($term) >= 2) {
                        $q->where(function($subQuery) use ($term) {
                            $subQuery->where('name', 'like', "%{$term}%")
                                    ->orWhere('description', 'like', "%{$term}%")
                                    ->orWhereHas('variants', function($variantQuery) use ($term) {
                                        $variantQuery->where('sku', 'like', "%{$term}%");
                                    });
                        });
                    }
                }
            })
            ->count();

        $categoryCount = Category::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->count();

        $brandCount = Brand::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->count();

        $blogCount = BlogPost::where('status', 'published')
            ->where(function($q) {
                $q->where('title', 'like', "%{$this->query}%")
                  ->orWhere('content', 'like', "%{$this->query}%")
                  ->orWhere('excerpt', 'like', "%{$this->query}%");
            })
            ->count();

        $blogCategoryCount = BlogCategory::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->count();

        $blogTagCount = BlogTag::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->count();

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
     * Clear search
     */
    public function clearSearch()
    {
        $this->query = '';
        $this->resetPage();
    }

    /**
     * Set search type
     */
    public function setSearchType($type)
    {
        $this->searchType = $type;
        $this->resetPage();
    }

    /**
     * Get blog suggestions for mixed results
     */
    public function getBlogSuggestionsProperty()
    {
        if (!$this->query || strlen($this->query) < 2) {
            return collect();
        }

        return BlogPost::where('status', 'published')
            ->where(function($q) {
                $q->where('title', 'like', "%{$this->query}%")
                  ->orWhere('content', 'like', "%{$this->query}%")
                  ->orWhere('excerpt', 'like', "%{$this->query}%");
            })
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit(6)
            ->get();
    }

    /**
     * Get category suggestions for mixed results
     */
    public function getCategorySuggestionsProperty()
    {
        if (!$this->query || strlen($this->query) < 2) {
            return collect();
        }

        return Category::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->withCount(['products' => function($q) {
                $q->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->limit(4)
            ->get();
    }

    /**
     * Get brand suggestions for mixed results
     */
    public function getBrandSuggestionsProperty()
    {
        if (!$this->query || strlen($this->query) < 2) {
            return collect();
        }

        return Brand::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->withCount(['products' => function($q) {
                $q->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->limit(4)
            ->get();
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.search.search-results', [
            'results' => $this->results,
            'searchStats' => $this->searchStats,
            'blogSuggestions' => $this->blogSuggestions,
            'categorySuggestions' => $this->categorySuggestions,
            'brandSuggestions' => $this->brandSuggestions,
        ])->extends('layouts.app')
          ->section('content');
    }
}
