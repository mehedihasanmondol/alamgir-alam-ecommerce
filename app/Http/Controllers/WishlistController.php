<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Ecommerce\Product\Models\Product;

/**
 * ModuleName: Wishlist Controller
 * Purpose: Handle wishlist operations (session-based)
 * 
 * @category Controllers
 * @package  App\Http\Controllers
 * @created  2025-11-09
 */
class WishlistController extends Controller
{
    /**
     * Add item to wishlist
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
        ]);
        
        $wishlist = session()->get('wishlist', []);
        
        // Create unique key for wishlist item
        $key = 'product_' . $request->product_id;
        if ($request->variant_id) {
            $key = 'variant_' . $request->variant_id;
        }
        
        // Check if already in wishlist
        if (isset($wishlist[$key])) {
            return response()->json([
                'success' => false,
                'message' => 'Item already in wishlist'
            ]);
        }
        
        // Get product details
        $product = Product::with(['variants', 'images', 'brand'])->find($request->product_id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        
        $variant = null;
        if ($request->variant_id) {
            $variant = $product->variants->where('id', $request->variant_id)->first();
        } else {
            $variant = $product->variants->first();
        }
        
        // Add to wishlist
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
        
        // Dispatch Livewire event for counter update
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item added to wishlist',
                'wishlist_count' => count($wishlist)
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Item added to wishlist',
            'wishlist_count' => count($wishlist)
        ]);
    }
    
    /**
     * Remove item from wishlist
     */
    public function remove(Request $request)
    {
        $request->validate([
            'wishlist_key' => 'required|string',
        ]);
        
        $wishlist = session()->get('wishlist', []);
        
        if (isset($wishlist[$request->wishlist_key])) {
            unset($wishlist[$request->wishlist_key]);
            session()->put('wishlist', $wishlist);
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed from wishlist',
                'wishlist_count' => count($wishlist)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Item not found in wishlist'
        ], 404);
    }
    
    /**
     * Get wishlist count
     */
    public function count()
    {
        $wishlist = session()->get('wishlist', []);
        
        return response()->json([
            'count' => count($wishlist)
        ]);
    }
    
    /**
     * Display wishlist page
     */
    public function index()
    {
        $wishlist = session()->get('wishlist', []);
        
        return view('frontend.wishlist.index', compact('wishlist'));
    }
    
    /**
     * Move item from wishlist to cart
     */
    public function moveToCart(Request $request)
    {
        $request->validate([
            'wishlist_key' => 'required|string',
        ]);
        
        $wishlist = session()->get('wishlist', []);
        
        if (!isset($wishlist[$request->wishlist_key])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in wishlist'
            ], 404);
        }
        
        $item = $wishlist[$request->wishlist_key];
        
        // Add to cart
        $cart = session()->get('cart', []);
        $cartKey = 'variant_' . $item['variant_id'];
        
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += 1;
        } else {
            $cart[$cartKey] = [
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'],
                'product_name' => $item['product_name'],
                'slug' => $item['slug'],
                'brand' => $item['brand'],
                'price' => $item['price'],
                'original_price' => $item['original_price'],
                'quantity' => 1,
                'image' => $item['image'],
                'sku' => $item['sku'],
            ];
        }
        
        session()->put('cart', $cart);
        
        // Remove from wishlist
        unset($wishlist[$request->wishlist_key]);
        session()->put('wishlist', $wishlist);
        
        return response()->json([
            'success' => true,
            'message' => 'Item moved to cart',
            'wishlist_count' => count($wishlist),
            'cart_count' => count($cart)
        ]);
    }
}
