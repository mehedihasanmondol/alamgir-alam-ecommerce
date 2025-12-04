<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleOffer;
use App\Models\SiteSetting;
use App\Modules\Ecommerce\Product\Models\Product;
use Illuminate\Http\Request;

/**
 * ModuleName: Admin Sale Offers
 * Purpose: Manage sale offer products for homepage
 * 
 * Key Methods:
 * - index(): List all sale offers
 * - store(): Add product to sale offers
 * - destroy(): Remove product from sale offers
 * - reorder(): Update display order
 * - toggleStatus(): Enable/disable offer
 * 
 * Dependencies:
 * - SaleOffer Model
 * - Product Model
 * 
 * @category Controllers
 * @package  App\Http\Controllers\Admin
 * @author   Admin
 * @created  2025-11-06
 * @updated  2025-11-06
 */
class SaleOfferController extends Controller
{
    /**
     * Display sale offers management page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $saleOffers = SaleOffer::with('product')
            ->ordered()
            ->get();

        // Get section settings
        $sectionEnabled = SiteSetting::get('sale_offers_section_enabled', '1');
        $sectionTitle = SiteSetting::get('sale_offers_section_title', 'Sale Offers');

        return view('admin.sale-offers.index', compact('saleOffers', 'sectionEnabled', 'sectionTitle'));
    }

    /**
     * Add product to sale offers
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id|unique:sale_offers,product_id',
        ]);

        // Get the highest display order and add 1
        $maxOrder = SaleOffer::max('display_order') ?? 0;

        SaleOffer::create([
            'product_id' => $request->product_id,
            'display_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.sale-offers.index')
            ->with('success', 'Product added to sale offers successfully!');
    }

    /**
     * Remove product from sale offers
     *
     * @param SaleOffer $saleOffer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SaleOffer $saleOffer)
    {
        $saleOffer->delete();

        return redirect()
            ->route('admin.sale-offers.index')
            ->with('success', 'Product removed from sale offers successfully!');
    }

    /**
     * Update display order of sale offers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:sale_offers,id',
            'orders.*.display_order' => 'required|integer|min:0',
        ]);

        foreach ($request->orders as $order) {
            SaleOffer::where('id', $order['id'])
                ->update(['display_order' => $order['display_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Display order updated successfully!',
        ]);
    }

    /**
     * Toggle active status of sale offer
     *
     * @param SaleOffer $saleOffer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(SaleOffer $saleOffer)
    {
        $saleOffer->update([
            'is_active' => !$saleOffer->is_active,
        ]);

        $status = $saleOffer->is_active ? 'enabled' : 'disabled';

        return redirect()
            ->route('admin.sale-offers.index')
            ->with('success', "Sale offer {$status} successfully!");
    }

    /**
     * Toggle section visibility on homepage
     */
    public function toggleSection(Request $request)
    {
        SiteSetting::updateOrCreate(
            ['key' => 'sale_offers_section_enabled'],
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
            ['key' => 'sale_offers_section_title'],
            ['value' => $validated['title']]
        );

        return response()->json(['success' => true]);
    }
}
