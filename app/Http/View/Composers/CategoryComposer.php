<?php

namespace App\Http\View\Composers;

use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Services\MegaMenuService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * CategoryComposer
 * Purpose: Provide category and brand data to views (especially for mega menu)
 * 
 * Features:
 * - Dynamic mega menu categories with nested children
 * - Dynamic trending brands per category based on sales
 * - Fallback to featured brands when no sales data
 * 
 * @author AI Assistant
 * @date 2025-11-06
 * @updated 2025-11-19
 */
class CategoryComposer
{
    protected $megaMenuService;
    
    public function __construct(MegaMenuService $megaMenuService)
    {
        $this->megaMenuService = $megaMenuService;
    }
    
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $megaMenuCategories = $this->getMegaMenuCategories();
        
        // Calculate trending brands per category
        $categoryTrendingBrands = $this->getCategoryTrendingBrands($megaMenuCategories);
        
        // Get global trending brands for "Brands A-Z" section
        $globalTrendingBrands = $this->megaMenuService->getGlobalTrendingBrands();
        
        $view->with([
            'megaMenuCategories' => $megaMenuCategories,
            'categoryTrendingBrands' => $categoryTrendingBrands,
            'globalTrendingBrands' => $globalTrendingBrands,
        ]);
    }

    /**
     * Get categories for mega menu
     * Cached for performance
     */
    protected function getMegaMenuCategories()
    {
        return Cache::remember('mega_menu_categories', 3600, function () {
            return Category::with(['activeChildren' => function ($query) {
                    $query->with(['activeChildren' => function ($subQuery) {
                        $subQuery->orderBy('sort_order')->limit(8); // Limit third-level categories
                    }])
                    ->orderBy('sort_order')
                    ->limit(10); // Limit subcategories per parent
                }])
                ->parents()
                ->active()
                ->ordered()
                ->limit(8) // Limit to 8 main categories for mega menu
                ->get();
        });
    }

    /**
     * Calculate trending brands for each category
     * 
     * @param \Illuminate\Support\Collection $categories
     * @return array
     */
    protected function getCategoryTrendingBrands($categories)
    {
        $categoryTrendingBrands = [];
        
        foreach ($categories as $category) {
            $categoryTrendingBrands[$category->id] = $this->megaMenuService->getTrendingBrandsByCategory($category->id);
        }
        
        return $categoryTrendingBrands;
    }
}
