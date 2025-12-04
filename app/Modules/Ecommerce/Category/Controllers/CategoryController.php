<?php

namespace App\Modules\Ecommerce\Category\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Category\Repositories\CategoryRepository;
use App\Modules\Ecommerce\Category\Services\CategoryService;
use App\Modules\Ecommerce\Category\Requests\StoreCategoryRequest;
use App\Modules\Ecommerce\Category\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;

/**
 * ModuleName: E-commerce Category
 * Purpose: Handle category CRUD operations
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class CategoryController extends Controller
{
    public function __construct(
        protected CategoryRepository $repository,
        protected CategoryService $service
    ) {}

    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'is_active' => $request->get('is_active'),
            'parent_id' => $request->get('parent_id'),
        ];

        $categories = $this->repository->paginate(15, $filters);
        $statistics = $this->service->getStatistics();
        $parents = $this->repository->getParents();

        return view('admin.categories.index', compact('categories', 'statistics', 'parents', 'filters'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $parentCategories = $this->repository->getHierarchicalDropdown();
        
        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = $this->service->create($request->validated());

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category created successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load('parent', 'children');
        
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        $parentCategories = $this->repository->getHierarchicalDropdown($category->id);
        
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $this->service->update($category, $request->validated());

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        try {
            $this->service->delete($category);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category deleted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Category $category)
    {
        try {
            $this->service->toggleStatus($category);

            return response()->json([
                'success' => true,
                'message' => 'Category status updated successfully!',
                'is_active' => $category->fresh()->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Duplicate category
     */
    public function duplicate(Category $category)
    {
        try {
            $newCategory = $this->service->duplicate($category);

            return redirect()
                ->route('admin.categories.edit', $newCategory)
                ->with('success', 'Category duplicated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to duplicate category: ' . $e->getMessage());
        }
    }
}
