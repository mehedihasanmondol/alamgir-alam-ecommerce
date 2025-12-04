<?php

namespace App\Modules\Ecommerce\Brand\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Modules\Ecommerce\Brand\Repositories\BrandRepository;
use App\Modules\Ecommerce\Brand\Services\BrandService;
use App\Modules\Ecommerce\Brand\Requests\StoreBrandRequest;
use App\Modules\Ecommerce\Brand\Requests\UpdateBrandRequest;
use Illuminate\Http\Request;

/**
 * ModuleName: E-commerce Brand
 * Purpose: Handle brand CRUD operations
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class BrandController extends Controller
{
    public function __construct(
        protected BrandRepository $repository,
        protected BrandService $service
    ) {}

    /**
     * Display a listing of brands (Livewire)
     */
    public function index(Request $request)
    {
        return view('admin.brands.index-livewire');
    }

    /**
     * Show the form for creating a new brand
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created brand
     */
    public function store(StoreBrandRequest $request)
    {
        try {
            $brand = $this->service->create($request->validated());

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Brand created successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create brand: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified brand
     */
    public function show(Brand $brand)
    {
        return view('admin.brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified brand
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        try {
            $this->service->update($brand, $request->validated());

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Brand updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update brand: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified brand
     */
    public function destroy(Brand $brand)
    {
        try {
            $this->service->delete($brand);

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Brand deleted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete brand: ' . $e->getMessage());
        }
    }

    /**
     * Toggle brand status
     */
    public function toggleStatus(Brand $brand)
    {
        try {
            $this->service->toggleStatus($brand);

            return response()->json([
                'success' => true,
                'message' => 'Brand status updated successfully!',
                'is_active' => $brand->fresh()->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Brand $brand)
    {
        try {
            $this->service->toggleFeatured($brand);

            return response()->json([
                'success' => true,
                'message' => 'Featured status updated successfully!',
                'is_featured' => $brand->fresh()->is_featured,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update featured status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Duplicate brand
     */
    public function duplicate(Brand $brand)
    {
        try {
            $newBrand = $this->service->duplicate($brand);

            return redirect()
                ->route('admin.brands.edit', $newBrand)
                ->with('success', 'Brand duplicated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to duplicate brand: ' . $e->getMessage());
        }
    }
}
