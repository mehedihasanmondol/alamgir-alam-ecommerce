<?php

namespace App\Modules\Blog\Repositories;

use App\Modules\Blog\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * ModuleName: Blog
 * Purpose: Handle all database queries for blog posts
 * 
 * Key Methods:
 * - all(): Get all posts with pagination
 * - find(): Find post by ID
 * - findBySlug(): Find post by slug
 * - create(): Create new post
 * - update(): Update existing post
 * - delete(): Soft delete post
 * - getPublished(): Get published posts
 * - getFeatured(): Get featured posts
 * - getByCategory(): Get posts by category
 * - getByTag(): Get posts by tag
 * - getByAuthor(): Get posts by author
 * - search(): Search posts
 * 
 * Dependencies:
 * - Post model
 * 
 * @category Blog
 * @package  App\Modules\Blog\Repositories
 * @author   AI Assistant
 * @created  2025-11-07
 * @updated  2025-11-07
 */
class PostRepository
{
    protected Post $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    /**
     * Get all posts with pagination
     */
    public function all(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['author', 'categories', 'tags'])
            ->latest('created_at');

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category_id'])) {
            $query->where('blog_category_id', $filters['category_id']);
        }

        if (isset($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (isset($filters['featured']) && $filters['featured'] !== '') {
            $query->where('is_featured', (bool) $filters['featured']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Find post by ID
     */
    public function find(int $id): ?Post
    {
        return $this->model->with(['author', 'categories', 'tags', 'approvedComments'])
            ->findOrFail($id);
    }

    /**
     * Find post by slug
     */
    public function findBySlug(string $slug): ?Post
    {
        return $this->model->with(['author', 'categories', 'tags', 'approvedComments'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Create new post
     */
    public function create(array $data): Post
    {
        $post = $this->model->create($data);

        // Attach tags if provided
        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post->load(['author', 'categories', 'tags']);
    }

    /**
     * Update existing post
     */
    public function update(int $id, array $data): Post
    {
        $post = $this->find($id);
        $post->update($data);

        // Sync tags if provided
        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post->fresh(['author', 'categories', 'tags']);
    }

    /**
     * Delete post (soft delete)
     */
    public function delete(int $id): bool
    {
        $post = $this->find($id);
        return $post->delete();
    }

    /**
     * Get published posts
     */
    public function getPublished(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->published()
            ->with(['author', 'categories', 'tags'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get featured posts
     */
    public function getFeatured(int $limit = 5): Collection
    {
        return $this->model->published()
            ->featured()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get posts by category
     */
    public function getByCategory(int $categoryId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->published()
            ->byCategory($categoryId)
            ->with(['author', 'categories', 'tags'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get posts by tag
     */
    public function getByTag(int $tagId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->published()
            ->byTag($tagId)
            ->with(['author', 'categories', 'tags', 'tickMarks'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get posts by author
     */
    public function getByAuthor(int $authorId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->published()
            ->byAuthor($authorId)
            ->with(['author', 'categories', 'tags'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Search posts
     */
    public function search(string $query, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->published()
            ->search($query)
            ->with(['author', 'categories', 'tags'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get popular posts
     */
    public function getPopular(int $limit = 5): Collection
    {
        return $this->model->published()
            ->popular($limit)
            ->with(['author', 'categories'])
            ->get();
    }

    /**
     * Get recent posts
     */
    public function getRecent(int $limit = 5): Collection
    {
        return $this->model->published()
            ->recent($limit)
            ->with(['author', 'categories'])
            ->get();
    }

    /**
     * Get posts count by status
     */
    public function getCountByStatus(): array
    {
        return [
            'all' => $this->model->count(),
            'published' => $this->model->where('status', 'published')->count(),
            'draft' => $this->model->where('status', 'draft')->count(),
            'scheduled' => $this->model->where('status', 'scheduled')->count(),
            'unlisted' => $this->model->where('status', 'unlisted')->count(),
        ];
    }
}
