<?php

namespace App\Modules\Blog\Repositories;

use App\Modules\Blog\Models\TickMark;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * ModuleName: Blog
 * Purpose: Repository for TickMark data access
 * 
 * @category Blog
 * @package  App\Modules\Blog\Repositories
 * @author   AI Assistant
 * @created  2025-11-10
 */
class TickMarkRepository
{
    protected TickMark $model;

    public function __construct(TickMark $model)
    {
        $this->model = $model;
    }

    /**
     * Get all tick marks
     */
    public function all(): Collection
    {
        return $this->model->ordered()->get();
    }

    /**
     * Get all active tick marks
     */
    public function allActive(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    /**
     * Get paginated tick marks
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->ordered()->paginate($perPage);
    }

    /**
     * Find tick mark by ID
     */
    public function find(int $id): ?TickMark
    {
        return $this->model->find($id);
    }

    /**
     * Find tick mark by slug
     */
    public function findBySlug(string $slug): ?TickMark
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Create new tick mark
     */
    public function create(array $data): TickMark
    {
        return $this->model->create($data);
    }

    /**
     * Update tick mark
     */
    public function update(int $id, array $data): bool
    {
        $tickMark = $this->find($id);
        
        if (!$tickMark) {
            return false;
        }
        
        return $tickMark->update($data);
    }

    /**
     * Delete tick mark
     */
    public function delete(int $id): bool
    {
        $tickMark = $this->find($id);
        
        if (!$tickMark || $tickMark->is_system) {
            return false;
        }
        
        return $tickMark->delete();
    }

    /**
     * Toggle active status
     */
    public function toggleActive(int $id): bool
    {
        $tickMark = $this->find($id);
        
        if (!$tickMark) {
            return false;
        }
        
        $tickMark->is_active = !$tickMark->is_active;
        return $tickMark->save();
    }

    /**
     * Get system tick marks
     */
    public function getSystemTickMarks(): Collection
    {
        return $this->model->system()->ordered()->get();
    }

    /**
     * Get custom tick marks
     */
    public function getCustomTickMarks(): Collection
    {
        return $this->model->custom()->ordered()->get();
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(array $order): bool
    {
        try {
            foreach ($order as $id => $sortOrder) {
                $this->model->where('id', $id)->update(['sort_order' => $sortOrder]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Search tick marks
     */
    public function search(string $query): Collection
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('label', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->ordered()
            ->get();
    }

    /**
     * Get tick marks with post count
     */
    public function getWithPostCount(): Collection
    {
        return $this->model
            ->withCount('posts')
            ->ordered()
            ->get();
    }
}
