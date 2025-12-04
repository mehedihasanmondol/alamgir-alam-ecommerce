<?php

namespace App\Modules\Ecommerce\Category\Repositories;

use App\Modules\Ecommerce\Category\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * ModuleName: E-commerce Category
 * Purpose: Data access layer for categories
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class CategoryRepository
{
    public function __construct(
        protected Category $model
    ) {}

    /**
     * Get all categories with pagination
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with('parent')->withCount('children');

        // Apply filters
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        return $query->ordered()->paginate($perPage);
    }

    /**
     * Get all categories
     */
    public function all(): Collection
    {
        return $this->model->with('parent')->ordered()->get();
    }

    /**
     * Get active categories
     */
    public function getActive(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    /**
     * Get parent categories only
     */
    public function getParents(): Collection
    {
        return $this->model->parents()->active()->ordered()->get();
    }

    /**
     * Get category tree (hierarchical structure)
     */
    public function getTree(): Collection
    {
        return $this->model->with('children.children')
            ->parents()
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Find category by ID
     */
    public function find(int $id): ?Category
    {
        return $this->model->with('parent', 'children')->find($id);
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->model->with('parent', 'children')->where('slug', $slug)->first();
    }

    /**
     * Create new category
     */
    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    /**
     * Update category
     */
    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Delete category
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    /**
     * Restore soft-deleted category
     */
    public function restore(int $id): bool
    {
        $category = $this->model->withTrashed()->find($id);
        return $category ? $category->restore() : false;
    }

    /**
     * Get categories for dropdown
     */
    public function getForDropdown(int $excludeId = null): array
    {
        $query = $this->model->ordered();

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->pluck('name', 'id')->toArray();
    }

    /**
     * Get hierarchical categories for dropdown
     */
    public function getHierarchicalDropdown(int $excludeId = null): array
    {
        $categories = $this->model->with('children')->parents()->ordered()->get();
        $result = [];

        foreach ($categories as $category) {
            if ($excludeId && $category->id === $excludeId) {
                continue;
            }

            $result[$category->id] = $category->name;

            if ($category->children->isNotEmpty()) {
                foreach ($category->children as $child) {
                    if ($excludeId && $child->id === $excludeId) {
                        continue;
                    }
                    $result[$child->id] = '— ' . $child->name;
                }
            }
        }

        return $result;
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
     * Get category statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
            'parents' => $this->model->parents()->count(),
            'with_children' => $this->model->has('children')->count(),
        ];
    }
}
