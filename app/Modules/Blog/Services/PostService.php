<?php

namespace App\Modules\Blog\Services;

use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Repositories\PostRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * ModuleName: Blog
 * Purpose: Business logic for blog post management
 * 
 * Key Methods:
 * - createPost(): Create new post with tags
 * - updatePost(): Update post with tags
 * - deletePost(): Soft delete post
 * - publishPost(): Publish a draft post
 * - schedulePost(): Schedule a post for future publishing
 * - uploadFeaturedImage(): Handle featured image upload
 * 
 * Dependencies:
 * - PostRepository
 * 
 * @category Blog
 * @package  App\Modules\Blog\Services
 * @author   AI Assistant
 * @created  2025-11-07
 */
class PostService
{
    protected PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Get all posts with filters
     */
    public function getAllPosts(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->postRepository->all($perPage, $filters);
    }

    /**
     * Get single post
     */
    public function getPost(int $id): ?Post
    {
        return $this->postRepository->find($id);
    }

    /**
     * Get post by slug
     */
    public function getPostBySlug(string $slug): ?Post
    {
        $post = $this->postRepository->findBySlug($slug);
        
        // Increment views for published posts (not for unlisted posts)
        if ($post && $post->isPublished()) {
            $post->incrementViews();
        }
        
        return $post;
    }

    /**
     * Create new post
     */
    public function createPost(array $data): Post
    {
        DB::beginTransaction();
        try {
            // Handle featured image upload (legacy support - prefer media_id)
            if (isset($data['featured_image']) && $data['featured_image'] && !isset($data['media_id'])) {
                $data['featured_image'] = $this->uploadFeaturedImage($data['featured_image']);
            }

            // Set author if not provided
            if (!isset($data['author_id'])) {
                $data['author_id'] = auth()->id();
            }

            // Set published_at if status is published
            if ($data['status'] === 'published' && !isset($data['published_at'])) {
                $data['published_at'] = now();
            }

            // Handle tick mark fields
            if (isset($data['is_verified']) && $data['is_verified']) {
                $data['verified_at'] = now();
                $data['verified_by'] = auth()->id();
            }

            // Extract relationships before creating post
            $categories = $data['categories'] ?? [];
            $tags = $data['tags'] ?? [];
            unset($data['categories'], $data['tags']);

            $post = $this->postRepository->create($data);

            // Sync categories and tags
            if (!empty($categories)) {
                $post->categories()->sync($categories);
            }
            if (!empty($tags)) {
                $post->tags()->sync($tags);
            }

            // Log activity (TODO: Install spatie/laravel-activitylog package)
            // activity()
            //     ->performedOn($post)
            //     ->causedBy(auth()->user())
            //     ->log('Created blog post');

            DB::commit();
            return $post->load(['categories', 'tags', 'media']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update existing post
     */
    public function updatePost(int $id, array $data): Post
    {
        DB::beginTransaction();
        try {
            $post = $this->getPost($id);

            // Handle featured image upload (legacy support - prefer media_id)
            if (isset($data['featured_image']) && $data['featured_image'] && !isset($data['media_id'])) {
                // Delete old image
                if ($post->featured_image) {
                    Storage::disk('public')->delete($post->featured_image);
                }
                $data['featured_image'] = $this->uploadFeaturedImage($data['featured_image']);
            }

            // Update published_at if status changed to published
            if ($data['status'] === 'published' && $post->status !== 'published') {
                $data['published_at'] = now();
            }

            // Extract relationships before updating post
            $categories = $data['categories'] ?? [];
            $tags = $data['tags'] ?? [];
            unset($data['categories'], $data['tags']);

            $post = $this->postRepository->update($id, $data);

            // Sync categories and tags
            if (isset($categories)) {
                $post->categories()->sync($categories);
            }
            if (isset($tags)) {
                $post->tags()->sync($tags);
            }

            // Log activity (TODO: Install spatie/laravel-activitylog package)
            // activity()
            //     ->performedOn($post)
            //     ->causedBy(auth()->user())
            //     ->log('Updated blog post');

            DB::commit();
            return $post->load(['categories', 'tags', 'media']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete post
     */
    public function deletePost(int $id): bool
    {
        $post = $this->getPost($id);

        // Delete featured image
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        // Log activity (TODO: Install spatie/laravel-activitylog package)
        // activity()
        //     ->performedOn($post)
        //     ->causedBy(auth()->user())
        //     ->log('Deleted blog post');

        return $this->postRepository->delete($id);
    }

    /**
     * Publish a draft post
     */
    public function publishPost(int $id): Post
    {
        return $this->updatePost($id, [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Schedule a post
     */
    public function schedulePost(int $id, string $scheduledAt): Post
    {
        return $this->updatePost($id, [
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    /**
     * Upload featured image
     */
    protected function uploadFeaturedImage($image): string
    {
        $filename = 'blog_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        return $image->storeAs('blog/featured', $filename, 'public');
    }

    /**
     * Get published posts
     */
    public function getPublishedPosts(int $perPage = 10): LengthAwarePaginator
    {
        return $this->postRepository->getPublished($perPage);
    }

    /**
     * Get featured posts
     */
    public function getFeaturedPosts(int $limit = 5): Collection
    {
        return $this->postRepository->getFeatured($limit);
    }

    /**
     * Get posts by category
     */
    public function getPostsByCategory(int $categoryId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->postRepository->getByCategory($categoryId, $perPage);
    }

    /**
     * Get posts by tag
     */
    public function getPostsByTag(int $tagId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->postRepository->getByTag($tagId, $perPage);
    }

    /**
     * Get posts by author
     */
    public function getPostsByAuthor(int $authorId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->postRepository->getByAuthor($authorId, $perPage);
    }

    /**
     * Search posts
     */
    public function searchPosts(string $query, int $perPage = 10): LengthAwarePaginator
    {
        return $this->postRepository->search($query, $perPage);
    }

    /**
     * Get popular posts
     */
    public function getPopularPosts(int $limit = 5): Collection
    {
        return $this->postRepository->getPopular($limit);
    }

    /**
     * Get recent posts
     */
    public function getRecentPosts(int $limit = 5): Collection
    {
        return $this->postRepository->getRecent($limit);
    }

    /**
     * Get posts count by status
     */
    public function getPostsCountByStatus(): array
    {
        return $this->postRepository->getCountByStatus();
    }

    /**
     * Toggle featured status for a post
     */
    public function toggleFeatured(Post $post): Post
    {
        $post->update(['is_featured' => !$post->is_featured]);
        return $post->fresh();
    }
}
