<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Ecommerce\Product\Models\Product;

/**
 * ModuleName: Frontend Product Controller
 * Purpose: Handle public product display
 * 
 * Key Methods:
 * - show($slug): Display single product detail page
 * 
 * Dependencies:
 * - Product Model
 * 
 * @category Frontend
 * @package  Controllers
 * @author   Windsurf AI
 * @created  2025-11-06
 */
class ProductController extends Controller
{
    /**
     * Display the product detail page
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show(string $slug)
    {
        // Find product by slug with all relationships
        $product = Product::with([
            'variants.attributeValues.attribute', 
            'images.media', 
            'categories.parent',
            'brand'
        ])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

        // Get default variant for simple products
        $defaultVariant = $product->variants->where('is_default', true)->first() 
                       ?? $product->variants->first();

        // Get related products (same categories, limit 8)
        $categoryIds = $product->categories->pluck('id')->toArray();
        $relatedProducts = Product::with(['variants', 'images', 'brand'])
            ->when(!empty($categoryIds), function ($query) use ($categoryIds) {
                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                });
            })
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(8)
            ->get();

        // Get recently viewed products from session
        $recentlyViewed = $this->getRecentlyViewedProducts($product->id);

        // Get inspired by browsing products (based on browsing history)
        $inspiredByBrowsing = $this->getInspiredByBrowsing($product);

        // Get popular/featured products for bottom slider
        $popularProducts = $this->getPopularOrFeaturedProducts($product->id);

        // Track this product as recently viewed
        $this->trackRecentlyViewed($product->id);

        // Get review statistics - count actual approved reviews
        $approvedReviews = $product->approvedReviews;
        $totalReviews = $approvedReviews->count();
        
        // Calculate average rating from approved reviews
        if ($totalReviews > 0) {
            $averageRating = round($approvedReviews->avg('rating'), 1);
        } else {
            $averageRating = 0;
        }
        
        // Get Q&A statistics
        $totalQuestions = $product->approvedQuestions()->count();
        $totalAnswers = $product->approvedQuestions()->whereHas('answers', function($query) {
            $query->where('status', 'approved');
        })->count();
        
        // Prepare SEO data
        $siteName = \App\Models\SiteSetting::get('site_name', config('app.name'));
        $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
        
        $seoData = [
            'title' => !empty($product->meta_title) 
                ? $product->meta_title 
                : $product->name . ' | ' . $siteName,
            
            'description' => !empty($product->meta_description) 
                ? $product->meta_description 
                : (!empty($product->short_description) 
                    ? \Illuminate\Support\Str::limit(strip_tags($product->short_description), 160)
                    : \Illuminate\Support\Str::limit(strip_tags($product->description), 160)),
            
            'keywords' => !empty($product->meta_keywords) 
                ? $product->meta_keywords 
                : $product->name . ', ' . ($product->brand ? $product->brand->name . ', ' : '') . ($product->categories->first() ? $product->categories->first()->name . ', ' : '') . \App\Models\SiteSetting::get('meta_keywords', 'products, shop'),
            
            'og_image' => ($primaryImage && $primaryImage->media && $primaryImage->media->large_url)
                ? $primaryImage->media->large_url
                : ($primaryImage && $primaryImage->image_path
                    ? asset('storage/' . $primaryImage->image_path)
                    : (\App\Models\SiteSetting::get('site_logo')
                        ? asset('storage/' . \App\Models\SiteSetting::get('site_logo'))
                        : asset('images/og-default.jpg'))),
            
            'og_type' => 'product',
            'canonical' => route('products.show', $product->slug),
            'price' => $defaultVariant ? $defaultVariant->price : null,
            'currency' => 'BDT',
            'availability' => ($defaultVariant && $defaultVariant->stock_quantity > 0) ? 'in stock' : 'out of stock',
        ];

        return view('frontend.products.show', compact(
            'product', 
            'defaultVariant',
            'relatedProducts', 
            'recentlyViewed',
            'inspiredByBrowsing',
            'popularProducts',
            'averageRating',
            'totalReviews',
            'totalQuestions',
            'totalAnswers',
            'seoData'
        ));
    }

    /**
     * Track recently viewed products in session
     *
     * @param int $productId
     * @return void
     */
    protected function trackRecentlyViewed(int $productId): void
    {
        $recentlyViewed = session()->get('recently_viewed', []);
        
        // Remove if already exists
        $recentlyViewed = array_diff($recentlyViewed, [$productId]);
        
        // Add to beginning
        array_unshift($recentlyViewed, $productId);
        
        // Keep only last 10
        $recentlyViewed = array_slice($recentlyViewed, 0, 10);
        
        session()->put('recently_viewed', $recentlyViewed);
    }

    /**
     * Get recently viewed products
     *
     * @param int $currentProductId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getRecentlyViewedProducts(int $currentProductId)
    {
        $recentlyViewedIds = session()->get('recently_viewed', []);
        
        // Remove current product from list
        $recentlyViewedIds = array_diff($recentlyViewedIds, [$currentProductId]);
        
        if (empty($recentlyViewedIds)) {
            return collect([]);
        }

        // Get products maintaining the order
        return Product::with(['variants', 'images', 'brand'])
            ->whereIn('id', $recentlyViewedIds)
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($product) use ($recentlyViewedIds) {
                return array_search($product->id, $recentlyViewedIds);
            })
            ->take(6);
    }

    /**
     * Get inspired by browsing products based on user's browsing history
     *
     * @param Product $currentProduct
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getInspiredByBrowsing(Product $currentProduct)
    {
        $recentlyViewedIds = session()->get('recently_viewed', []);
        
        // If no browsing history, return products from same categories
        if (empty($recentlyViewedIds)) {
            $categoryIds = $currentProduct->categories->pluck('id')->toArray();
            return Product::with(['variants', 'images', 'brand'])
                ->when(!empty($categoryIds), function ($query) use ($categoryIds) {
                    $query->whereHas('categories', function ($q) use ($categoryIds) {
                        $q->whereIn('categories.id', $categoryIds);
                    });
                })
                ->where('id', '!=', $currentProduct->id)
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit(10)
                ->get();
        }

        // Get recently viewed products to analyze browsing patterns
        $recentlyViewedProducts = Product::with('categories')
            ->whereIn('id', $recentlyViewedIds)
            ->where('is_active', true)
            ->get();

        // Collect category IDs and brand IDs from browsing history
        $categoryIds = $recentlyViewedProducts->pluck('categories')->flatten()->pluck('id')->unique()->toArray();
        $brandIds = $recentlyViewedProducts->pluck('brand_id')->filter()->unique()->toArray();

        // Get products from browsed categories and brands
        $query = Product::with(['variants', 'images', 'brand'])
            ->where('id', '!=', $currentProduct->id)
            ->where('is_active', true);

        // Filter by categories or brands from browsing history
        $query->where(function ($q) use ($categoryIds, $brandIds) {
            if (!empty($categoryIds)) {
                $q->whereHas('categories', function ($subQuery) use ($categoryIds) {
                    $subQuery->whereIn('categories.id', $categoryIds);
                });
            }
            if (!empty($brandIds)) {
                $q->orWhereIn('brand_id', $brandIds);
            }
        });

        // Exclude already viewed products
        $query->whereNotIn('id', $recentlyViewedIds);

        return $query->inRandomOrder()
            ->limit(10)
            ->get();
    }

    /**
     * Get popular products (by sales count) or featured products as fallback
     *
     * @param int $currentProductId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getPopularOrFeaturedProducts(int $currentProductId)
    {
        // First, try to get popular products (by sales_count)
        $popularProducts = Product::with(['variants', 'images', 'brand'])
            ->where('id', '!=', $currentProductId)
            ->where('is_active', true)
            ->where('sales_count', '>', 0)
            ->orderBy('sales_count', 'desc')
            ->limit(10)
            ->get();

        // If no popular products found, fallback to featured products
        if ($popularProducts->isEmpty()) {
            $popularProducts = Product::with(['variants', 'images', 'brand'])
                ->where('id', '!=', $currentProductId)
                ->where('is_active', true)
                ->where('is_featured', true)
                ->inRandomOrder()
                ->limit(10)
                ->get();
        }

        return $popularProducts;
    }
}
