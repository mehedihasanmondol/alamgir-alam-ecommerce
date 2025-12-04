<?php

namespace App\Modules\Blog\Repositories;

use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Database\Eloquent\Collection;

/**
 * ModuleName: Blog
 * Purpose: Handle all database queries for blog categories
 * 
 * @category Blog
 * @package  App\Modules\Blog\Repositories
 * @author   AI Assistant
 * @created  2025-11-07
 */
class BlogCategoryRepository
{
    protected BlogCategory $model;

    public function __construct(BlogCategory $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with('parent')->ordered()->get();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    public function getRoots(): Collection
    {
        return $this->model->roots()->active()->ordered()->get();
    }

    public function find(int $id): ?BlogCategory
    {
        return $this->model->with(['parent', 'children', 'posts'])->findOrFail($id);
    }

    public function findBySlug(string $slug): ?BlogCategory
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    public function create(array $data): BlogCategory
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): BlogCategory
    {
        $category = $this->find($id);
        $category->update($data);
        return $category->fresh();
    }

    public function delete(int $id): bool
    {
        $category = $this->find($id);
        return $category->delete();
    }
}
