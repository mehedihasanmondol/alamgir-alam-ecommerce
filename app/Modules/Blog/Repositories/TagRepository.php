<?php

namespace App\Modules\Blog\Repositories;

use App\Modules\Blog\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

/**
 * ModuleName: Blog
 * Purpose: Handle all database queries for blog tags
 * 
 * @category Blog
 * @package  App\Modules\Blog\Repositories
 * @author   AI Assistant
 * @created  2025-11-07
 */
class TagRepository
{
    protected Tag $model;

    public function __construct(Tag $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    public function getPopular(int $limit = 10): Collection
    {
        return $this->model->popular($limit)->get();
    }

    public function find(int $id): ?Tag
    {
        return $this->model->with('posts')->findOrFail($id);
    }

    public function findBySlug(string $slug): ?Tag
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    public function create(array $data): Tag
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Tag
    {
        $tag = $this->find($id);
        $tag->update($data);
        return $tag->fresh();
    }

    public function delete(int $id): bool
    {
        $tag = $this->find($id);
        return $tag->delete();
    }

    public function search(string $query): Collection
    {
        return $this->model->search($query)->get();
    }
}
