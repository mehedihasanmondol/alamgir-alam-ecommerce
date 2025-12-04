<?php

namespace App\Modules\Blog\Repositories;

use App\Modules\Blog\Models\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * ModuleName: Blog
 * Purpose: Handle all database queries for blog comments
 * 
 * @category Blog
 * @package  App\Modules\Blog\Repositories
 * @author   AI Assistant
 * @created  2025-11-07
 */
class CommentRepository
{
    protected Comment $model;

    public function __construct(Comment $model)
    {
        $this->model = $model;
    }

    public function all(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['post', 'user'])->latest();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['post_id'])) {
            $query->where('blog_post_id', $filters['post_id']);
        }

        return $query->paginate($perPage);
    }

    public function getPending(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->pending()
            ->with(['post', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): ?Comment
    {
        return $this->model->with(['post', 'user', 'replies'])->findOrFail($id);
    }

    public function create(array $data): Comment
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Comment
    {
        $comment = $this->find($id);
        $comment->update($data);
        return $comment->fresh();
    }

    public function delete(int $id): bool
    {
        $comment = $this->find($id);
        return $comment->delete();
    }

    public function getCountByStatus(): array
    {
        return [
            'all' => $this->model->count(),
            'approved' => $this->model->approved()->count(),
            'pending' => $this->model->pending()->count(),
            'spam' => $this->model->spam()->count(),
            'trash' => $this->model->where('status', 'trash')->count(),
        ];
    }
}
