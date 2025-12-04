<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Brand\Models\Brand;

/**
 * ModuleName: Product List Component
 * Purpose: Display and filter products in shop page
 * 
 * @category Livewire
 * @package  Shop
 * @created  2025-11-09
 */
class ProductList extends Component
{
    use WithPagination;

    // Route parameters
    public $slug = null; // Category or brand slug from route
    public $category = null; // Category model instance
    public $brand = null; // Brand model instance
    public $pageType = 'shop'; // 'shop', 'category', or 'brand'

    // URL query string parameters
    #[Url(as: 'q')]
    public $search = '';
    
    #[Url(as: 'cat', keep: true)]
    public $selectedCategories = [];
    
    #[Url(as: 'brand', keep: true)]
    public $selectedBrands = [];
    
    #[Url(as: 'min')]
    public $minPrice = '';
    
    #[Url(as: 'max')]
    public $maxPrice = '';
    
    #[Url(as: 'rating')]
    public $minRating = '';
    
    #[Url(as: 'stock')]
    public $inStock = false;
    
    #[Url(as: 'sale')]
    public $onSale = false;
    
    #[Url(as: 'sort')]
    public $sortBy = 'latest';
    
    #[Url(as: 'show')]
    public $perPage = 24;

    public $viewMode = 'grid';
    public $showFilters = false;

    /**
     * Mount component with optional slug
     */
    public function mount($slug = null)
    {
        $this->slug = $slug;
        
        // Determine page type and load category or brand if needed
        if ($slug) {
            // Check if it's a category route
            if (request()->route()->getName() === 'categories.show') {
                $this->category = Category::with(['activeChildren', 'parent', 'products', 'media'])
                    ->where('slug', $slug)
                    ->where('is_active', true)
                    ->firstOrFail();
                
                $this->pageType = 'category';
            }
            // Check if it's a brand route
            elseif (request()->route()->getName() === 'brands.show') {
                $this->brand = Brand::with(['products', 'media'])
                    ->where('slug', $slug)
                    ->where('is_active', true)
                    ->firstOrFail();
                
                $this->pageType = 'brand';
            }
        }
    }

    /**
     * Reset pagination when filters change
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategories()
    {
        $this->resetPage();
    }

    public function updatingSelectedBrands()
    {
        $this->resetPage();
    }

    public function updatingMinPrice()
    {
        $this->resetPage();
    }

    public function updatingMaxPrice()
    {
        $this->resetPage();
    }

    public function updatingMinRating()
    {
        $this->resetPage();
    }

    public function updatingInStock()
    {
        $this->resetPage();
    }

    public function updatingOnSale()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Clear all filters
     */
    public function clearFilters()
    {
        $this->reset([
            'search',
            'selectedCategories',
            'selectedBrands',
            'minPrice',
            'maxPrice',
            'minRating',
            'inStock',
            'onSale'
        ]);
        $this->resetPage();
    }

    /**
     * Clear specific filter
     */
    public function clearFilter($filter)
    {
        $this->reset($filter);
        $this->resetPage();
    }

    /**
     * Toggle view mode
     */
    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    /**
     * Get filtered products
     */
    public function getProductsProperty()
    {
        $query = Product::with(['variants', 'categories', 'brand', 'images', 'defaultVariant'])
            ->where('is_active', true);

        // If viewing a specific category, filter by that category and its children
        if ($this->category) {
            $categoryIds = $this->getCategoryIdsWithChildren($this->category);
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        // If viewing a specific brand, filter by that brand
        if ($this->brand) {
            $query->where('brand_id', $this->brand->id);
        }

        // Search
        if ($this->search) {
            $searchTerms = explode(' ', $this->search);
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
        }

        // Category Filter (for additional filtering on shop and brand pages)
        if (!empty($this->selectedCategories) && !$this->category) {
            $query->whereHas('categories', function($q) {
                $q->whereIn('categories.id', $this->selectedCategories);
            });
        }

        // Brand Filter
        if (!empty($this->selectedBrands)) {
            $query->whereIn('brand_id', $this->selectedBrands);
        }

        // Price Range Filter
        if ($this->minPrice !== '' && $this->minPrice !== null) {
            $query->whereHas('variants', function($q) {
                $q->where('price', '>=', $this->minPrice);
            });
        }
        if ($this->maxPrice !== '' && $this->maxPrice !== null) {
            $query->whereHas('variants', function($q) {
                $q->where('price', '<=', $this->maxPrice);
            });
        }

        // Rating Filter
        if ($this->minRating) {
            $query->where('average_rating', '>=', $this->minRating);
        }

        // In Stock Filter
        if ($this->inStock) {
            $query->whereHas('variants', function($q) {
                $q->where('stock_quantity', '>', 0);
            });
        }

        // On Sale Filter
        if ($this->onSale) {
            $query->whereHas('variants', function($q) {
                $q->whereNotNull('sale_price')
                  ->whereColumn('sale_price', '<', 'price');
            });
        }

        // Sorting
        switch ($this->sortBy) {
            case 'price_low':
                $query->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_id = products.id) DESC');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($this->perPage);
    }

    /**
     * Get categories with product counts
     */
    public function getCategoriesProperty()
    {
        return Category::where('is_active', true)
            ->withCount(['products' => function($q) {
                $q->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get brands with product counts
     */
    public function getBrandsProperty()
    {
        return Brand::where('is_active', true)
            ->withCount(['products' => function($q) {
                $q->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get price range
     */
    public function getPriceRangeProperty()
    {
        return Product::where('products.is_active', true)
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->selectRaw('MIN(product_variants.price) as min_price, MAX(product_variants.price) as max_price')
            ->first();
    }

    /**
     * Check if any filters are active
     */
    public function hasActiveFilters()
    {
        return $this->search || 
               !empty($this->selectedCategories) || 
               !empty($this->selectedBrands) ||
               $this->minPrice ||
               $this->maxPrice ||
               $this->minRating ||
               $this->inStock ||
               $this->onSale;
    }

    /**
     * Add product to cart
     */
    #[On('add-to-cart')]
    public function addToCart($productId, $variantId, $quantity = 1)
    {
        $product = Product::with(['variants', 'images', 'brand'])->find($productId);
        
        if (!$product) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Product not found'
            ]);
            return;
        }
        
        $variant = $product->variants->where('id', $variantId)->first();
        
        if (!$variant) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Product variant not found'
            ]);
            return;
        }
        
        // Check if variant can be added to cart (considers global stock restriction setting)
        if (!$variant->canAddToCart()) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'This product is currently out of stock'
            ]);
            return;
        }
        
        // Only check stock quantity if restriction is enabled
        $restrictionEnabled = \App\Modules\Ecommerce\Product\Models\ProductVariant::isStockRestrictionEnabled();
        if ($restrictionEnabled && $variant->stock_quantity < $quantity) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Insufficient stock available'
            ]);
            return;
        }
        
        $cart = session()->get('cart', []);
        $cartKey = 'variant_' . $variant->id;
        
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'product_name' => $product->name,
                'slug' => $product->slug,
                'brand' => $product->brand ? $product->brand->name : null,
                'price' => $variant->sale_price ?? $variant->price,
                'original_price' => $variant->price,
                'quantity' => $quantity,
                'image' => $product->getPrimaryThumbnailUrl(), // Use media library
                'sku' => $variant->sku,
                'stock_quantity' => $variant->stock_quantity,
            ];
        }
        
        session()->put('cart', $cart);
        
        $this->dispatch('cart-updated');
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Product added to cart!'
        ]);
    }

    /**
     * Toggle wishlist
     */
    #[On('toggle-wishlist')]
    public function toggleWishlist($productId, $variantId = null)
    {
        $wishlist = session()->get('wishlist', []);
        
        $key = 'product_' . $productId;
        if ($variantId) {
            $key = 'variant_' . $variantId;
        }
        
        if (isset($wishlist[$key])) {
            // Remove from wishlist
            unset($wishlist[$key]);
            session()->put('wishlist', $wishlist);
            
            $this->dispatch('wishlist-updated');
            $this->dispatch('show-toast', [
                'type' => 'info',
                'message' => 'Removed from wishlist'
            ]);
            return;
        }
        
        // Add to wishlist
        $product = Product::with(['variants', 'images', 'brand'])->find($productId);
        
        if (!$product) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Product not found'
            ]);
            return;
        }
        
        $variant = null;
        if ($variantId) {
            $variant = $product->variants->where('id', $variantId)->first();
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
        
        $this->dispatch('wishlist-updated');
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Added to wishlist!'
        ]);
    }

    /**
     * Get category IDs including all children recursively
     */
    protected function getCategoryIdsWithChildren(Category $category): array
    {
        $ids = [$category->id];

        foreach ($category->activeChildren as $child) {
            $ids[] = $child->id;
            
            // Get third level
            foreach ($child->activeChildren as $grandChild) {
                $ids[] = $grandChild->id;
            }
        }

        return $ids;
    }

    /**
     * Get breadcrumb for category or brand page
     */
    public function getBreadcrumbProperty()
    {
        // Brand page breadcrumb
        if ($this->brand) {
            return [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => 'Brands', 'url' => route('brands.index')],
                ['label' => $this->brand->name, 'url' => null],
            ];
        }

        // Category page breadcrumb
        if ($this->category) {
            $breadcrumb = [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => 'Categories', 'url' => route('categories.index')],
            ];

            // Add parent categories
            foreach ($this->category->ancestors() as $ancestor) {
                $breadcrumb[] = [
                    'label' => $ancestor->name,
                    'url' => route('categories.show', $ancestor->slug),
                ];
            }

            // Add current category
            $breadcrumb[] = [
                'label' => $this->category->name,
                'url' => null,
            ];

            return $breadcrumb;
        }

        // Shop page breadcrumb
        return [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Shop', 'url' => null]
        ];
    }

    /**
     * Get SEO data for category or brand page
     */
    public function getSeoDataProperty()
    {
        $siteName = \App\Models\SiteSetting::get('site_name', config('app.name'));
        
        // SEO for Category page
        if ($this->category) {
            return [
                'title' => !empty($this->category->meta_title) 
                    ? $this->category->meta_title 
                    : $this->category->name . ' | ' . $siteName,
                
                'description' => !empty($this->category->meta_description) 
                    ? $this->category->meta_description 
                    : (!empty($this->category->description) 
                        ? \Illuminate\Support\Str::limit(strip_tags($this->category->description), 160)
                        : 'Shop ' . $this->category->name . ' products. Browse our collection of quality ' . $this->category->name),
                
                'keywords' => !empty($this->category->meta_keywords) 
                    ? $this->category->meta_keywords 
                    : $this->category->name . ', ' . $this->category->name . ' products, shop ' . $this->category->name . ', ' . \App\Models\SiteSetting::get('meta_keywords', 'ecommerce, products'),
                
                'og_image' => ($this->category->media && $this->category->media->large_url)
                    ? $this->category->media->large_url
                    : (!empty($this->category->og_image)
                        ? asset('storage/' . $this->category->og_image)
                        : ($this->category->image 
                            ? asset('storage/' . $this->category->image) 
                            : (\App\Models\SiteSetting::get('site_logo')
                                ? asset('storage/' . \App\Models\SiteSetting::get('site_logo'))
                                : asset('images/og-default.jpg')))),
                
                'og_type' => 'website',
                'canonical' => route('categories.show', $this->category->slug),
            ];
        }
        
        // SEO for Brand page (when accessed via Livewire)
        if ($this->brand) {
            return [
                'title' => !empty($this->brand->meta_title) 
                    ? $this->brand->meta_title 
                    : $this->brand->name . ' Products | ' . $siteName,
                
                'description' => !empty($this->brand->meta_description) 
                    ? $this->brand->meta_description 
                    : (!empty($this->brand->description) 
                        ? \Illuminate\Support\Str::limit(strip_tags($this->brand->description), 160)
                        : 'Shop ' . $this->brand->name . ' products. Discover quality products from ' . $this->brand->name),
                
                'keywords' => !empty($this->brand->meta_keywords) 
                    ? $this->brand->meta_keywords 
                    : $this->brand->name . ', ' . $this->brand->name . ' products, shop ' . $this->brand->name . ', ' . \App\Models\SiteSetting::get('meta_keywords', 'ecommerce, products'),
                
                'og_image' => ($this->brand->media && $this->brand->media->large_url)
                    ? $this->brand->media->large_url
                    : (!empty($this->brand->og_image)
                        ? asset('storage/' . $this->brand->og_image)
                        : ($this->brand->logo 
                            ? asset('storage/' . $this->brand->logo) 
                            : (\App\Models\SiteSetting::get('site_logo')
                                ? asset('storage/' . \App\Models\SiteSetting::get('site_logo'))
                                : asset('images/og-default.jpg')))),
                
                'og_type' => 'website',
                'canonical' => route('brands.show', $this->brand->slug),
            ];
        }
        
        // SEO for general Shop page
        return [
            'title' => 'Shop All Products | ' . $siteName,
            'description' => \App\Models\SiteSetting::get('meta_description', 'Shop our wide selection of quality products'),
            'keywords' => \App\Models\SiteSetting::get('meta_keywords', 'shop, products, ecommerce'),
            'og_image' => \App\Models\SiteSetting::get('site_logo') 
                ? asset('storage/' . \App\Models\SiteSetting::get('site_logo'))
                : asset('images/og-default.jpg'),
            'og_type' => 'website',
            'canonical' => route('shop'),
        ];
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.shop.product-list', [
            'products' => $this->products,
            'categories' => $this->categories,
            'brands' => $this->brands,
            'priceRange' => $this->priceRange,
            'breadcrumb' => $this->breadcrumb,
            'category' => $this->category,
            'brand' => $this->brand,
            'pageType' => $this->pageType,
            'seoData' => $this->seoData,
        ])->extends('layouts.app')
          ->section('content');
    }
}
