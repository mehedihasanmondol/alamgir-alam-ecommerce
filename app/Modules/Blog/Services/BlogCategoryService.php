<?php

namespace App\Modules\Blog\Services;

use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Repositories\BlogCategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * ModuleName: Blog
 * Purpose: Business logic for blog category management
 * 
 * @category Blog
 * @package  App\Modules\Blog\Services
 * @author   AI Assistant
 * @created  2025-11-07
 */
class BlogCategoryService
{
    protected BlogCategoryRepository $categoryRepository;

    public function __construct(BlogCategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function getActiveCategories(): Collection
    {
        return $this->categoryRepository->getActive();
    }

    public function getRootCategories(): Collection
    {
        return $this->categoryRepository->getRoots();
    }

    public function getCategory(int $id): ?BlogCategory
    {
        return $this->categoryRepository->find($id);
    }

    public function getCategoryBySlug(string $slug): ?BlogCategory
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    public function createCategory(array $data): BlogCategory
    {
        DB::beginTransaction();
        try {
            // Handle media_id (NEW: Media library system)
            if (isset($data['media_id']) && $data['media_id']) {
                // Using media library - media_id is already set, remove image upload field
                unset($data['image']);
            }
            // Handle legacy image upload
            elseif (isset($data['image']) && $data['image']) {
                $data['image_path'] = $this->uploadCategoryImage($data['image']);
                unset($data['image']);
            }

            $category = $this->categoryRepository->create($data);

            // Log activity (TODO: Install spatie/laravel-activitylog package)
            // activity()
            //     ->performedOn($category)
            //     ->causedBy(auth()->user())
            //     ->log('Created blog category');

            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateCategory(int $id, array $data): BlogCategory
    {
        DB::beginTransaction();
        try {
            $category = $this->getCategory($id);

            // Handle media_id (NEW: Media library system)
            if (isset($data['media_id']) && $data['media_id']) {
                // Using media library - delete old legacy image if switching from old system
                if ($category->image_path && !$category->media_id) {
                    Storage::disk('public')->delete($category->image_path);
                    $data['image_path'] = null; // Clear legacy path
                }
                unset($data['image']);
            }
            // Handle legacy image upload
            elseif (isset($data['image']) && $data['image']) {
                // Delete old image
                if ($category->image_path) {
                    Storage::disk('public')->delete($category->image_path);
                }
                $data['image_path'] = $this->uploadCategoryImage($data['image']);
                $data['media_id'] = null; // Clear media_id if uploading file
                unset($data['image']);
            }

            $category = $this->categoryRepository->update($id, $data);

            // Log activity (TODO: Install spatie/laravel-activitylog package)
            // activity()
            //     ->performedOn($category)
            //     ->causedBy(auth()->user())
            //     ->log('Updated blog category');

            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->getCategory($id);

        // Delete image
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        // Log activity (TODO: Install spatie/laravel-activitylog package)
        // activity()
        //     ->performedOn($category)
        //     ->causedBy(auth()->user())
        //     ->log('Deleted blog category');

        return $this->categoryRepository->delete($id);
    }

    protected function uploadCategoryImage($image): string
    {
        $filename = 'blog_category_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        return $image->storeAs('blog/categories', $filename, 'public');
    }

    /**
     * Get categories formatted for dropdown with hierarchy labels
     * Returns categories ordered hierarchically with indentation
     * 
     * @param int|null $excludeId Category ID to exclude (useful when editing to prevent circular references)
     * @return \Illuminate\Support\Collection
     */
    public function getCategoriesForDropdown(?int $excludeId = null): \Illuminate\Support\Collection
    {
        $categories = $this->getAllCategories();
        
        // Exclude specific category and its descendants if provided
        if ($excludeId) {
            $excludeCategory = $categories->firstWhere('id', $excludeId);
            if ($excludeCategory) {
                $excludeIds = [$excludeId];
                // Get all descendant IDs
                $this->getDescendantIds($excludeCategory, $categories, $excludeIds);
                $categories = $categories->whereNotIn('id', $excludeIds);
            }
        }
        
        // Build hierarchical structure
        return $this->buildHierarchicalList($categories);
    }

    /**
     * Recursively get all descendant IDs
     */
    private function getDescendantIds($category, $allCategories, &$excludeIds): void
    {
        $children = $allCategories->where('parent_id', $category->id);
        foreach ($children as $child) {
            $excludeIds[] = $child->id;
            $this->getDescendantIds($child, $allCategories, $excludeIds);
        }
    }

    /**
     * Build hierarchical list with proper indentation and labels
     */
    private function buildHierarchicalList($categories, $parentId = null, $prefix = ''): \Illuminate\Support\Collection
    {
        $result = collect();
        
        $items = $categories->where('parent_id', $parentId)->sortBy('sort_order');
        
        foreach ($items as $category) {
            // Create formatted label with hierarchy
            $category->dropdown_label = $prefix . $category->name;
            $category->dropdown_path = $category->getHierarchyPath();
            
            $result->push($category);
            
            // Recursively add children
            $children = $this->buildHierarchicalList(
                $categories,
                $category->id,
                $prefix . '— '
            );
            
            $result = $result->merge($children);
        }
        
        return $result;
    }
}
