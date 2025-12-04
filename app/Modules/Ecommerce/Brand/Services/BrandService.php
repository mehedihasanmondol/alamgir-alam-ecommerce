<?php

namespace App\Modules\Ecommerce\Brand\Services;

use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Modules\Ecommerce\Brand\Repositories\BrandRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * ModuleName: E-commerce Brand
 * Purpose: Business logic for brand management
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class BrandService
{
    public function __construct(
        protected BrandRepository $repository
    ) {}

    /**
     * Create new brand
     */
    public function create(array $data): Brand
    {
        DB::beginTransaction();
        try {
            // Auto-generate slug if not provided
            if (empty($data['slug']) && !empty($data['name'])) {
                $brand = new Brand();
                $data['slug'] = $brand->generateUniqueSlug($data['name']);
            }

            // Auto-generate SEO fields if not provided
            $data = $this->autoGenerateSeoFields($data);

            $brand = $this->repository->create($data);

            DB::commit();
            return $brand;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update brand
     */
    public function update(Brand $brand, array $data): Brand
    {
        DB::beginTransaction();
        try {
            // Update slug if name changed
            if (isset($data['name']) && $data['name'] !== $brand->name && empty($data['slug'])) {
                $data['slug'] = $brand->generateUniqueSlug($data['name']);
            }

            // Auto-generate SEO fields if not provided
            $data = $this->autoGenerateSeoFields($data);

            $this->repository->update($brand, $data);

            DB::commit();
            return $brand->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete brand
     */
    public function delete(Brand $brand): bool
    {
        DB::beginTransaction();
        try {
            // TODO: Check if brand has products when Product model is created
            // if ($brand->products()->exists()) {
            //     throw new \Exception('Cannot delete brand with products. Please reassign or delete products first.');
            // }

            // Delete logo
            if ($brand->logo) {
                $this->deleteLogo($brand->logo);
            }

            $result = $this->repository->delete($brand);

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle brand status
     */
    public function toggleStatus(Brand $brand): bool
    {
        return $this->repository->update($brand, [
            'is_active' => !$brand->is_active
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Brand $brand): bool
    {
        return $this->repository->update($brand, [
            'is_featured' => !$brand->is_featured
        ]);
    }

    /**
     * Upload brand logo
     */
    protected function uploadLogo(UploadedFile $file): string
    {
        return $file->store('brands', 'public');
    }

    /**
     * Delete brand logo
     */
    protected function deleteLogo(string $path): void
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
     * Reorder brands
     */
    public function reorder(array $order): void
    {
        $this->repository->updateSortOrder($order);
    }

    /**
     * Duplicate brand
     */
    public function duplicate(Brand $brand): Brand
    {
        $data = $brand->toArray();
        
        // Remove unique fields
        unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
        
        // Modify name and slug
        $data['name'] = $data['name'] . ' (Copy)';
        $data['slug'] = '';
        
        // Copy logo if exists
        if ($brand->logo) {
            $data['logo'] = $this->duplicateLogo($brand->logo);
        }

        return $this->create($data);
    }

    /**
     * Duplicate logo file
     */
    protected function duplicateLogo(string $originalPath): ?string
    {
        if (!Storage::disk('public')->exists($originalPath)) {
            return null;
        }

        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        $newPath = 'brands/' . uniqid() . '.' . $extension;

        Storage::disk('public')->copy($originalPath, $newPath);

        return $newPath;
    }

    /**
     * Get brand statistics
     */
    public function getStatistics(): array
    {
        return $this->repository->getStatistics();
    }
}
