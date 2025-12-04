<?php

namespace App\Http\Controllers;

use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Product\Models\Product;
use Illuminate\Http\Request;

/**
 * Frontend Category Controller
 * Purpose: Handle public category browsing and product listing
 * 
 * @author AI Assistant
 * @date 2025-11-06
 */
class CategoryController extends Controller
{
    /**
     * Display all categories
     */
    public function index()
    {
        $categories = Category::with(['activeChildren', 'media'])
            ->parents()
            ->active()
            ->ordered()
            ->get();

        return view('frontend.categories.index', compact('categories'));
    }

    /**
     * Display a specific category and its products
     */
    public function show(Request $request, string $slug)
    {
        $category = Category::with(['activeChildren', 'parent', 'products', 'media'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Get products in this category and its subcategories
        $categoryIds = $this->getCategoryIdsWithChildren($category);

        $query = Product::whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->active()
            ->with(['images', 'categories', 'brand', 'defaultVariant', 'variants']);

        // Apply filters
        if ($request->filled('min_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where(function ($sq) use ($request) {
                    $sq->where('sale_price', '>=', $request->min_price)
                      ->orWhere(function ($ssq) use ($request) {
                          $ssq->whereNull('sale_price')
                             ->where('price', '>=', $request->min_price);
                      });
                });
            });
        }

        if ($request->filled('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where(function ($sq) use ($request) {
                    $sq->where('sale_price', '<=', $request->max_price)
                      ->orWhere(function ($ssq) use ($request) {
                          $ssq->whereNull('sale_price')
                             ->where('price', '<=', $request->max_price);
                      });
                });
            });
        }

        if ($request->filled('in_stock') && $request->in_stock == '1') {
            $query->whereHas('variants', function ($q) {
                $q->where('stock_quantity', '>', 0);
            });
        }

        if ($request->filled('on_sale') && $request->on_sale == '1') {
            $query->whereHas('variants', function ($q) {
                $q->whereNotNull('sale_price')
                  ->whereColumn('sale_price', '<', 'price');
            });
        }

        // Apply sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->join('product_variants', function($join) {
                    $join->on('products.id', '=', 'product_variants.product_id')
                         ->where('product_variants.is_default', true);
                })
                ->orderByRaw('COALESCE(product_variants.sale_price, product_variants.price) ASC')
                ->select('products.*');
                break;
            case 'price_high':
                $query->join('product_variants', function($join) {
                    $join->on('products.id', '=', 'product_variants.product_id')
                         ->where('product_variants.is_default', true);
                })
                ->orderByRaw('COALESCE(product_variants.sale_price, product_variants.price) DESC')
                ->select('products.*');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(24)->withQueryString();

        // Get breadcrumb
        $breadcrumb = $category->getBreadcrumb();

        return view('frontend.categories.show', compact('category', 'products', 'breadcrumb'));
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
}
