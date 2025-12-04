<?php

namespace App\Modules\Blog\Services;

use App\Modules\Blog\Models\Comment;
use App\Modules\Blog\Repositories\CommentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * ModuleName: Blog
 * Purpose: Business logic for comment management and moderation
 * 
 * @category Blog
 * @package  App\Modules\Blog\Services
 * @author   AI Assistant
 * @created  2025-11-07
 */
class CommentService
{
    protected CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getAllComments(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        return $this->commentRepository->all($perPage, $filters);
    }

    public function getPendingComments(int $perPage = 20): LengthAwarePaginator
    {
        return $this->commentRepository->getPending($perPage);
    }

    public function getComment(int $id): ?Comment
    {
        return $this->commentRepository->find($id);
    }

    public function createComment(array $data): Comment
    {
        DB::beginTransaction();
        try {
            // Add IP and user agent
            $data['ip_address'] = request()->ip();
            $data['user_agent'] = request()->userAgent();

            // Set user_id if authenticated
            if (auth()->check()) {
                $data['user_id'] = auth()->id();
            }

            $comment = $this->commentRepository->create($data);

            DB::commit();
            return $comment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function approveComment(int $id): Comment
    {
        $comment = $this->getComment($id);
        $comment->approve(auth()->id());

        // Log activity (TODO: Install spatie/laravel-activitylog package)
        // activity()
        //     ->performedOn($comment)
        //     ->causedBy(auth()->user())
        //     ->log('Approved comment');

        return $comment;
    }

    public function markAsSpam(int $id): Comment
    {
        $comment = $this->getComment($id);
        $comment->markAsSpam();

        // Log activity (TODO: Install spatie/laravel-activitylog package)
        // activity()
        //     ->performedOn($comment)
        //     ->causedBy(auth()->user())
        //     ->log('Marked comment as spam');

        return $comment;
    }

    public function moveToTrash(int $id): Comment
    {
        $comment = $this->getComment($id);
        $comment->moveToTrash();

        // Log activity (TODO: Install spatie/laravel-activitylog package)
        // activity()
        //     ->performedOn($comment)
        //     ->causedBy(auth()->user())
        //     ->log('Moved comment to trash');

        return $comment;
    }

    public function deleteComment(int $id): bool
    {
        $comment = $this->getComment($id);

        // Log activity (TODO: Install spatie/laravel-activitylog package)
        // activity()
        //     ->performedOn($comment)
        //     ->causedBy(auth()->user())
        //     ->log('Deleted comment');

        return $this->commentRepository->delete($id);
    }

    public function getCommentsCountByStatus(): array
    {
        return $this->commentRepository->getCountByStatus();
    }
}
