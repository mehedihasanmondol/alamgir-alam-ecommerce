<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BestSellerProduct;
use App\Models\SiteSetting;
use App\Modules\Ecommerce\Product\Models\Product;
use Illuminate\Http\Request;

class BestSellerProductController extends Controller
{
    /**
     * Display best seller products management
     */
    public function index()
    {
        $bestSellerProducts = BestSellerProduct::with('product.variants')
            ->orderBy('sort_order')
            ->get();

        // Get section settings
        $sectionEnabled = SiteSetting::get('best_sellers_section_enabled', '1');
        $sectionTitle = SiteSetting::get('best_sellers_section_title', 'Best Sellers');

        return view('admin.best-seller-products.index', compact('bestSellerProducts', 'sectionEnabled', 'sectionTitle'));
    }

    /**
     * Add product to best sellers
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id|unique:best_seller_products,product_id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? BestSellerProduct::max('sort_order') + 1;
        $validated['is_active'] = true;

        BestSellerProduct::create($validated);

        return redirect()->route('admin.best-seller-products.index')
            ->with('success', 'Product added to best sellers successfully!');
    }

    /**
     * Update sort order
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:best_seller_products,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            BestSellerProduct::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(BestSellerProduct $bestSellerProduct)
    {
        $bestSellerProduct->is_active = !$bestSellerProduct->is_active;
        $bestSellerProduct->save();

        return response()->json([
            'success' => true,
            'is_active' => $bestSellerProduct->is_active,
        ]);
    }

    /**
     * Remove from best sellers
     */
    public function destroy(BestSellerProduct $bestSellerProduct)
    {
        $bestSellerProduct->delete();

        return redirect()->route('admin.best-seller-products.index')
            ->with('success', 'Product removed from best sellers successfully!');
    }

    /**
     * Toggle section visibility on homepage
     */
    public function toggleSection(Request $request)
    {
        SiteSetting::updateOrCreate(
            ['key' => 'best_sellers_section_enabled'],
            ['value' => $request->enabled ? '1' : '0']
        );

        return response()->json(['success' => true]);
    }

    /**
     * Update section title
     */
    public function updateSectionTitle(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        SiteSetting::updateOrCreate(
            ['key' => 'best_sellers_section_title'],
            ['value' => $validated['title']]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Search products for adding
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');
        
        if (empty($search)) {
            return response()->json([]);
        }
        
        $existingProductIds = BestSellerProduct::pluck('product_id')->toArray();
        
        $products = Product::where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->whereNotIn('id', $existingProductIds)
            ->where('is_active', true)
            ->with(['variants', 'images'])
            ->limit(10)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku ?? 'N/A',
                    'price' => $product->price,
                    'image_url' => $product->image_url ?? ($product->images->first()->image_path ?? null),
                ];
            });

        return response()->json($products);
    }
}
