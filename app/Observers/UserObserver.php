<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AuthorProfileService;

/**
 * ModuleName: User Management
 * Purpose: Observer to handle user model events, especially role changes
 * 
 * Key Methods:
 * - saved(): Handle user save event (create/update)
 * - deleting(): Handle user deletion
 * 
 * Dependencies:
 * - User Model
 * - AuthorProfileService
 * 
 * @author AI Assistant
 * @date 2025-11-16
 */
class UserObserver
{
    protected AuthorProfileService $authorProfileService;

    public function __construct(AuthorProfileService $authorProfileService)
    {
        $this->authorProfileService = $authorProfileService;
    }

    /**
     * Handle the User "saved" event
     * This fires after both create and update
     */
    public function saved(User $user): void
    {
        // Ensure author profile exists if user has author role
        if ($this->authorProfileService->userHasAuthorRole($user)) {
            $this->authorProfileService->ensureAuthorProfileExists($user);
        }
    }

    /**
     * Handle the User "deleting" event
     */
    public function deleting(User $user): void
    {
        // Delete author profile if exists
        if ($user->authorProfile) {
            $this->authorProfileService->deleteAuthorProfile($user->id);
        }
    }
}
