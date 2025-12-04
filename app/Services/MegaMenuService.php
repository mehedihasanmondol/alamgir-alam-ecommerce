<?php

namespace App\Services;

use App\Models\HomepageSetting;
use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Order\Models\OrderItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * MegaMenuService
 * Purpose: Calculate trending brands dynamically per category based on sales data
 * 
 * @author AI Assistant
 * @date 2025-11-19
 */
class MegaMenuService
{
    /**
     * Get trending brands for a specific category based on sales
     * 
     * @param int $categoryId
     * @param int|null $limit
     * @param int|null $days
     * @return \Illuminate\Support\Collection
     */
    public function getTrendingBrandsByCategory(int $categoryId, ?int $limit = null, ?int $days = null)
    {
        // Check if dynamic trending brands feature is enabled
        $isEnabled = HomepageSetting::get('mega_menu_trending_brands_enabled', true);
        $isDynamic = HomepageSetting::get('mega_menu_trending_brands_dynamic', true);
        
        if (!$isEnabled) {
            return collect();
        }
        
        // If dynamic is disabled, fall back to featured brands
        if (!$isDynamic) {
            return $this->getFallbackTrendingBrands($limit);
        }
        
        // Get settings
        $limit = $limit ?? (int) HomepageSetting::get('mega_menu_trending_brands_limit', 6);
        $days = $days ?? (int) HomepageSetting::get('mega_menu_trending_brands_days', 30);
        
        // Cache key for this category
        $cacheKey = "trending_brands_category_{$categoryId}_{$limit}_{$days}";
        
        return Cache::remember($cacheKey, 3600, function () use ($categoryId, $limit, $days) {
            // Get all descendant category IDs (including current category)
            $categoryIds = $this->getCategoryWithDescendants($categoryId);
            
            // Get brand IDs with sales totals from many-to-many categories relationship
            $brandSales = DB::table('order_items')
                ->select('products.brand_id', DB::raw('SUM(order_items.quantity) as total_sales'))
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->join('category_product', 'category_product.product_id', '=', 'products.id')
                ->whereIn('category_product.category_id', $categoryIds)
                ->whereNotNull('products.brand_id')
                ->where('orders.status', '!=', 'cancelled')
                ->where('orders.status', '!=', 'failed')
                ->where('orders.created_at', '>=', now()->subDays($days))
                ->whereNull('products.deleted_at')
                ->groupBy('products.brand_id')
                ->orderByDesc('total_sales')
                ->limit($limit)
                ->pluck('brand_id');
            
            // If no sales data, check if fallback is enabled
            if ($brandSales->isEmpty()) {
                $useFallback = HomepageSetting::get('mega_menu_trending_brands_fallback', false);
                return $useFallback ? $this->getFallbackTrendingBrands($limit) : collect();
            }
            
            // Fetch full brand records maintaining order
            $trendingBrands = Brand::whereIn('id', $brandSales)
                ->where('is_active', true)
                ->get()
                ->sortBy(function ($brand) use ($brandSales) {
                    return array_search($brand->id, $brandSales->toArray());
                })
                ->values();
            
            return $trendingBrands;
        });
    }
    
    /**
     * Get all trending brands (not category-specific)
     * Used for global sections like "Brands A-Z"
     * 
     * @param int|null $limit
     * @param int|null $days
     * @return \Illuminate\Support\Collection
     */
    public function getGlobalTrendingBrands(?int $limit = null, ?int $days = null)
    {
        // Check if feature is enabled
        $isEnabled = HomepageSetting::get('mega_menu_trending_brands_enabled', true);
        $isDynamic = HomepageSetting::get('mega_menu_trending_brands_dynamic', true);
        
        if (!$isEnabled) {
            return collect();
        }
        
        // If dynamic is disabled, fall back to featured brands
        if (!$isDynamic) {
            return $this->getFallbackTrendingBrands($limit);
        }
        
        // Get settings
        $limit = $limit ?? (int) HomepageSetting::get('mega_menu_trending_brands_limit', 6);
        $days = $days ?? (int) HomepageSetting::get('mega_menu_trending_brands_days', 30);
        
        // Cache key
        $cacheKey = "trending_brands_global_{$limit}_{$days}";
        
        return Cache::remember($cacheKey, 3600, function () use ($limit, $days) {
            // First, get brand IDs with sales totals using subquery
            $brandSales = DB::table('order_items')
                ->select('products.brand_id', DB::raw('SUM(order_items.quantity) as total_sales'))
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->where('orders.status', '!=', 'cancelled')
                ->where('orders.status', '!=', 'failed')
                ->where('orders.created_at', '>=', now()->subDays($days))
                ->whereNull('products.deleted_at')
                ->groupBy('products.brand_id')
                ->orderByDesc('total_sales')
                ->limit($limit)
                ->pluck('brand_id');
            
            // If no sales data, check if fallback is enabled
            if ($brandSales->isEmpty()) {
                $useFallback = HomepageSetting::get('mega_menu_trending_brands_fallback', false);
                return $useFallback ? $this->getFallbackTrendingBrands($limit) : collect();
            }
            
            // Fetch full brand records maintaining order
            $trendingBrands = Brand::whereIn('id', $brandSales)
                ->where('is_active', true)
                ->get()
                ->sortBy(function ($brand) use ($brandSales) {
                    return array_search($brand->id, $brandSales->toArray());
                })
                ->values();
            
            return $trendingBrands;
        });
    }
    
    /**
     * Get category and all its descendants (recursive)
     * Includes parent category, children, grandchildren, and all deeper levels
     * 
     * @param int $categoryId
     * @return array
     */
    protected function getCategoryWithDescendants(int $categoryId): array
    {
        $categoryIds = [$categoryId];
        
        // Recursive function to get all descendants
        $getChildren = function($parentIds) use (&$getChildren, &$categoryIds) {
            $children = Category::whereIn('parent_id', $parentIds)
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();
            
            if (!empty($children)) {
                $categoryIds = array_merge($categoryIds, $children);
                // Recursively get children of children
                $getChildren($children);
            }
        };
        
        // Start recursive search from the main category
        $getChildren([$categoryId]);
        
        return array_unique($categoryIds);
    }
    
    /**
     * Fallback to featured brands when no sales data available
     * 
     * @param int|null $limit
     * @return \Illuminate\Support\Collection
     */
    protected function getFallbackTrendingBrands(?int $limit = null)
    {
        $limit = $limit ?? (int) HomepageSetting::get('mega_menu_trending_brands_limit', 6);
        
        return Brand::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Clear trending brands cache
     * Call this when orders are created/updated
     * 
     * @return void
     */
    public function clearTrendingBrandsCache(): void
    {
        // Get settings for cache key generation
        $limit = (int) HomepageSetting::get('mega_menu_trending_brands_limit', 6);
        $days = (int) HomepageSetting::get('mega_menu_trending_brands_days', 30);
        
        // Clear global trending brands cache
        Cache::forget("trending_brands_global_{$limit}_{$days}");
        
        // Clear category-specific caches
        $categories = Category::pluck('id');
        foreach ($categories as $categoryId) {
            Cache::forget("trending_brands_category_{$categoryId}_{$limit}_{$days}");
        }
        
        // Also clear mega menu categories cache
        Cache::forget('mega_menu_categories');
    }
}
