<?php

namespace App\Modules\Ecommerce\Brand\Repositories;

use App\Modules\Ecommerce\Brand\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * ModuleName: E-commerce Brand
 * Purpose: Data access layer for brands
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class BrandRepository
{
    public function __construct(
        protected Brand $model
    ) {}

    /**
     * Get all brands with pagination
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Apply filters
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        return $query->ordered()->paginate($perPage);
    }

    /**
     * Get all brands
     */
    public function all(): Collection
    {
        return $this->model->ordered()->get();
    }

    /**
     * Get active brands
     */
    public function getActive(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    /**
     * Get featured brands
     */
    public function getFeatured(): Collection
    {
        return $this->model->active()->featured()->ordered()->get();
    }

    /**
     * Find brand by ID
     */
    public function find(int $id): ?Brand
    {
        return $this->model->find($id);
    }

    /**
     * Find brand by slug
     */
    public function findBySlug(string $slug): ?Brand
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Create new brand
     */
    public function create(array $data): Brand
    {
        return $this->model->create($data);
    }

    /**
     * Update brand
     */
    public function update(Brand $brand, array $data): bool
    {
        return $brand->update($data);
    }

    /**
     * Delete brand
     */
    public function delete(Brand $brand): bool
    {
        return $brand->delete();
    }

    /**
     * Restore soft-deleted brand
     */
    public function restore(int $id): bool
    {
        $brand = $this->model->withTrashed()->find($id);
        return $brand ? $brand->restore() : false;
    }

    /**
     * Get brands for dropdown
     */
    public function getForDropdown(): array
    {
        return $this->model->active()->ordered()->pluck('name', 'id')->toArray();
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(array $order): void
    {
        foreach ($order as $id => $sortOrder) {
            $this->model->where('id', $id)->update(['sort_order' => $sortOrder]);
        }
    }

    /**
     * Get brand statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
            'featured' => $this->model->featured()->count(),
        ];
    }
}
