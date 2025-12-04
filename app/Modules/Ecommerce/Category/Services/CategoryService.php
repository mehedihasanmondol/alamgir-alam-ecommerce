<?php

namespace App\Modules\Ecommerce\Category\Services;

use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Category\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\UploadedFile;

/**
 * ModuleName: E-commerce Category
 * Purpose: Business logic for category management
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class CategoryService
{
    public function __construct(
        protected CategoryRepository $repository
    ) {}

    /**
     * Create new category
     */
    public function create(array $data): Category
    {
        DB::beginTransaction();
        try {
            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = $this->uploadImage($data['image']);
            }

            // Auto-generate slug if not provided
            if (empty($data['slug']) && !empty($data['name'])) {
                $category = new Category();
                $data['slug'] = $category->generateUniqueSlug($data['name']);
            }

            // Auto-generate SEO fields if not provided
            $data = $this->autoGenerateSeoFields($data);

            $category = $this->repository->create($data);

            // Clear mega menu cache
            $this->clearMegaMenuCache();

            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update category
     */
    public function update(Category $category, array $data): Category
    {
        DB::beginTransaction();
        try {
            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                // Delete old image
                if ($category->image) {
                    $this->deleteImage($category->image);
                }
                $data['image'] = $this->uploadImage($data['image']);
            } elseif (isset($data['remove_image']) && $data['remove_image']) {
                // Remove image if requested
                if ($category->image) {
                    $this->deleteImage($category->image);
                }
                $data['image'] = null;
            } else {
                // Keep existing image
                unset($data['image']);
            }

            // Update slug if name changed
            if (isset($data['name']) && $data['name'] !== $category->name && empty($data['slug'])) {
                $data['slug'] = $category->generateUniqueSlug($data['name']);
            }

            // Auto-generate SEO fields if not provided
            $data = $this->autoGenerateSeoFields($data);

            $this->repository->update($category, $data);

            // Clear mega menu cache
            $this->clearMegaMenuCache();

            DB::commit();
            return $category->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete category
     */
    public function delete(Category $category): bool
    {
        DB::beginTransaction();
        try {
            // Check if category has children
            if ($category->hasChildren()) {
                throw new \Exception('Cannot delete category with subcategories. Please delete or reassign subcategories first.');
            }

            // Delete image
            if ($category->image) {
                $this->deleteImage($category->image);
            }

            $result = $this->repository->delete($category);

            // Clear mega menu cache
            $this->clearMegaMenuCache();

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Category $category): bool
    {
        return $this->repository->update($category, [
            'is_active' => !$category->is_active
        ]);
    }

    /**
     * Upload category image
     */
    protected function uploadImage(UploadedFile $file): string
    {
        return $file->store('categories', 'public');
    }

    /**
     * Delete category image
     */
    protected function deleteImage(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Auto-generate SEO fields
     */
    protected function autoGenerateSeoFields(array $data): array
    {
        // Auto-generate meta_title if not provided
        if (empty($data['meta_title']) && !empty($data['name'])) {
            $data['meta_title'] = $data['name'];
        }

        // Auto-generate meta_description if not provided
        if (empty($data['meta_description']) && !empty($data['description'])) {
            $data['meta_description'] = \Illuminate\Support\Str::limit(strip_tags($data['description']), 160);
        }

        // Auto-generate og_title if not provided
        if (empty($data['og_title']) && !empty($data['meta_title'])) {
            $data['og_title'] = $data['meta_title'];
        }

        // Auto-generate og_description if not provided
        if (empty($data['og_description']) && !empty($data['meta_description'])) {
            $data['og_description'] = $data['meta_description'];
        }

        return $data;
    }

    /**
     * Reorder categories
     */
    public function reorder(array $order): void
    {
        $this->repository->updateSortOrder($order);
    }

    /**
     * Duplicate category
     */
    public function duplicate(Category $category): Category
    {
        $data = $category->toArray();
        
        // Remove unique fields
        unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
        
        // Modify name and slug
        $data['name'] = $data['name'] . ' (Copy)';
        $data['slug'] = '';
        
        // Copy image if exists
        if ($category->image) {
            $data['image'] = $this->duplicateImage($category->image);
        }

        return $this->create($data);
    }

    /**
     * Duplicate image file
     */
    protected function duplicateImage(string $originalPath): ?string
    {
        if (!Storage::disk('public')->exists($originalPath)) {
            return null;
        }

        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        $newPath = 'categories/' . uniqid() . '.' . $extension;

        Storage::disk('public')->copy($originalPath, $newPath);

        return $newPath;
    }

    /**
     * Clear mega menu cache
     */
    protected function clearMegaMenuCache(): void
    {
        Cache::forget('mega_menu_categories');
    }

    /**
     * Get category statistics
     */
    public function getStatistics(): array
    {
        return $this->repository->getStatistics();
    }
}
