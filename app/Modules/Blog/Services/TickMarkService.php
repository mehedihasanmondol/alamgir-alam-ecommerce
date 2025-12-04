<?php

namespace App\Modules\Blog\Services;

use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Models\TickMark;
use App\Modules\Blog\Repositories\PostRepository;
use App\Modules\Blog\Repositories\TickMarkRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ModuleName: Blog
 * Purpose: Service for managing blog post tick marks (verification, editor's choice, trending, premium)
 * 
 * @category Blog
 * @package  App\Modules\Blog\Services
 * @author   AI Assistant
 * @created  2025-11-10
 */
class TickMarkService
{
    protected PostRepository $postRepository;
    protected TickMarkRepository $tickMarkRepository;

    public function __construct(
        PostRepository $postRepository,
        TickMarkRepository $tickMarkRepository
    ) {
        $this->postRepository = $postRepository;
        $this->tickMarkRepository = $tickMarkRepository;
    }

    /**
     * Toggle verification status for a post
     */
    public function toggleVerification(int $postId, ?string $notes = null): Post
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->find($postId);
            
            $post->is_verified = !$post->is_verified;
            
            if ($post->is_verified) {
                $post->verified_at = now();
                $post->verified_by = Auth::id();
                $post->verification_notes = $notes;
            } else {
                $post->verified_at = null;
                $post->verified_by = null;
                $post->verification_notes = null;
            }
            
            $post->save();
            
            DB::commit();
            return $post->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mark post as verified
     */
    public function markAsVerified(int $postId, ?string $notes = null): Post
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->find($postId);
            
            $post->update([
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => Auth::id(),
                'verification_notes' => $notes,
            ]);
            
            DB::commit();
            return $post->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove verification from post
     */
    public function removeVerification(int $postId): Post
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->find($postId);
            
            $post->update([
                'is_verified' => false,
                'verified_at' => null,
                'verified_by' => null,
                'verification_notes' => null,
            ]);
            
            DB::commit();
            return $post->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle editor's choice status
     */
    public function toggleEditorChoice(int $postId): Post
    {
        $post = $this->postRepository->find($postId);
        $post->is_editor_choice = !$post->is_editor_choice;
        $post->save();
        
        return $post->fresh();
    }

    /**
     * Toggle trending status
     */
    public function toggleTrending(int $postId): Post
    {
        $post = $this->postRepository->find($postId);
        $post->is_trending = !$post->is_trending;
        $post->save();
        
        return $post->fresh();
    }

    /**
     * Toggle premium status
     */
    public function togglePremium(int $postId): Post
    {
        $post = $this->postRepository->find($postId);
        $post->is_premium = !$post->is_premium;
        $post->save();
        
        return $post->fresh();
    }

    /**
     * Update multiple tick marks at once
     */
    public function updateTickMarks(int $postId, array $data): Post
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->find($postId);
            
            $updateData = [];
            
            // Handle verification
            if (isset($data['is_verified'])) {
                $updateData['is_verified'] = $data['is_verified'];
                
                if ($data['is_verified']) {
                    $updateData['verified_at'] = now();
                    $updateData['verified_by'] = Auth::id();
                    $updateData['verification_notes'] = $data['verification_notes'] ?? null;
                } else {
                    $updateData['verified_at'] = null;
                    $updateData['verified_by'] = null;
                    $updateData['verification_notes'] = null;
                }
            }
            
            // Handle other tick marks
            if (isset($data['is_editor_choice'])) {
                $updateData['is_editor_choice'] = $data['is_editor_choice'];
            }
            
            if (isset($data['is_trending'])) {
                $updateData['is_trending'] = $data['is_trending'];
            }
            
            if (isset($data['is_premium'])) {
                $updateData['is_premium'] = $data['is_premium'];
            }
            
            $post->update($updateData);
            
            DB::commit();
            return $post->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get tick mark statistics
     */
    public function getStatistics(): array
    {
        return [
            'verified' => Post::verified()->count(),
            'editor_choice' => Post::editorChoice()->count(),
            'trending' => Post::trending()->count(),
            'premium' => Post::premium()->count(),
            'total_with_marks' => Post::where(function($query) {
                $query->where('is_verified', true)
                      ->orWhere('is_editor_choice', true)
                      ->orWhere('is_trending', true)
                      ->orWhere('is_premium', true);
            })->count(),
        ];
    }

    /**
     * Get posts by tick mark type
     */
    public function getPostsByTickMark(string $type, int $perPage = 10)
    {
        $query = Post::with(['author', 'category', 'tags']);
        
        switch ($type) {
            case 'verified':
                $query->verified();
                break;
            case 'editor_choice':
                $query->editorChoice();
                break;
            case 'trending':
                $query->trending();
                break;
            case 'premium':
                $query->premium();
                break;
            default:
                throw new \InvalidArgumentException("Invalid tick mark type: {$type}");
        }
        
        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Bulk update tick marks for multiple posts
     */
    public function bulkUpdateTickMarks(array $postIds, string $tickMarkType, bool $value): int
    {
        DB::beginTransaction();
        try {
            $updateData = [];
            
            switch ($tickMarkType) {
                case 'verified':
                    $updateData['is_verified'] = $value;
                    if ($value) {
                        $updateData['verified_at'] = now();
                        $updateData['verified_by'] = Auth::id();
                    } else {
                        $updateData['verified_at'] = null;
                        $updateData['verified_by'] = null;
                        $updateData['verification_notes'] = null;
                    }
                    break;
                case 'editor_choice':
                    $updateData['is_editor_choice'] = $value;
                    break;
                case 'trending':
                    $updateData['is_trending'] = $value;
                    break;
                case 'premium':
                    $updateData['is_premium'] = $value;
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid tick mark type: {$tickMarkType}");
            }
            
            $affected = Post::whereIn('id', $postIds)->update($updateData);
            
            DB::commit();
            return $affected;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Clear all tick marks from a post
     */
    public function clearAllTickMarks(int $postId): Post
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->find($postId);
            
            $post->update([
                'is_verified' => false,
                'is_editor_choice' => false,
                'is_trending' => false,
                'is_premium' => false,
                'verified_at' => null,
                'verified_by' => null,
                'verification_notes' => null,
            ]);
            
            DB::commit();
            return $post->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Attach a custom tick mark to a post
     */
    public function attachTickMark(int $postId, int $tickMarkId, ?string $notes = null): Post
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->find($postId);
            $post->attachTickMark($tickMarkId, $notes);
            
            DB::commit();
            return $post->fresh(['tickMarks']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Detach a custom tick mark from a post
     */
    public function detachTickMark(int $postId, int $tickMarkId): Post
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->find($postId);
            $post->detachTickMark($tickMarkId);
            
            DB::commit();
            return $post->fresh(['tickMarks']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Sync custom tick marks for a post
     */
    public function syncTickMarks(int $postId, array $tickMarkIds): Post
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->find($postId);
            $post->syncTickMarks($tickMarkIds);
            
            DB::commit();
            return $post->fresh(['tickMarks']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get all available tick marks
     */
    public function getAllTickMarks()
    {
        return $this->tickMarkRepository->allActive();
    }

    /**
     * Get tick marks for a specific post
     */
    public function getPostTickMarks(int $postId)
    {
        $post = $this->postRepository->find($postId);
        return $post->tickMarks;
    }

    /**
     * Create a new custom tick mark
     */
    public function createTickMark(array $data): TickMark
    {
        return $this->tickMarkRepository->create($data);
    }

    /**
     * Update a tick mark
     */
    public function updateTickMarkDefinition(int $tickMarkId, array $data): bool
    {
        return $this->tickMarkRepository->update($tickMarkId, $data);
    }

    /**
     * Delete a custom tick mark
     */
    public function deleteTickMark(int $tickMarkId): bool
    {
        return $this->tickMarkRepository->delete($tickMarkId);
    }

    /**
     * Bulk attach tick mark to multiple posts
     */
    public function bulkAttachTickMark(array $postIds, int $tickMarkId): int
    {
        DB::beginTransaction();
        try {
            $affected = 0;
            foreach ($postIds as $postId) {
                $post = $this->postRepository->find($postId);
                if ($post) {
                    $post->attachTickMark($tickMarkId);
                    $affected++;
                }
            }
            
            DB::commit();
            return $affected;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Bulk detach tick mark from multiple posts
     */
    public function bulkDetachTickMark(array $postIds, int $tickMarkId): int
    {
        DB::beginTransaction();
        try {
            $affected = 0;
            foreach ($postIds as $postId) {
                $post = $this->postRepository->find($postId);
                if ($post) {
                    $post->detachTickMark($tickMarkId);
                    $affected++;
                }
            }
            
            DB::commit();
            return $affected;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
