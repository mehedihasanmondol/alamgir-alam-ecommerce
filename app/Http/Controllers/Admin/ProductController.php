<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Product\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.product.index-livewire');
    }

    public function create()
    {
        return view('admin.product.create-livewire');
    }

    public function edit(Product $product)
    {
        // Eager load relationships needed for editing
        $product->load(['categories', 'brand', 'variants', 'images']);
        
        return view('admin.product.edit-livewire', compact('product'));
    }
}
