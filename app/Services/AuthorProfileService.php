<?php

namespace App\Services;

use App\Models\AuthorProfile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * ModuleName: Blog/Author Management
 * Purpose: Business logic for author profile management
 * 
 * Key Methods:
 * - createOrUpdateAuthorProfile(): Create or update author profile for a user
 * - deleteAuthorProfile(): Delete author profile
 * - syncAuthorProfileFromUser(): Sync author info from user
 * - uploadAuthorAvatar(): Handle avatar upload
 * 
 * Dependencies:
 * - AuthorProfile Model
 * - User Model
 * 
 * @author AI Assistant
 * @date 2025-11-16
 */
class AuthorProfileService
{
    /**
     * Create or update author profile for a user
     */
    public function createOrUpdateAuthorProfile(int $userId, array $data = []): array
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                ];
            }

            // Check if author profile already exists
            $authorProfile = AuthorProfile::where('user_id', $userId)->first();

            // Handle avatar upload
            if (isset($data['author_avatar']) && $data['author_avatar']) {
                // Delete old avatar if exists
                if ($authorProfile && $authorProfile->avatar) {
                    Storage::disk('public')->delete($authorProfile->avatar);
                }
                $data['avatar'] = $this->uploadAuthorAvatar($data['author_avatar']);
                unset($data['author_avatar']);
            }

            // Prepare author profile data
            $profileData = [
                'user_id' => $userId,
                'bio' => $data['author_bio'] ?? $authorProfile->bio ?? null,
                'job_title' => $data['author_job_title'] ?? $authorProfile->job_title ?? null,
                'website' => $data['author_website'] ?? $authorProfile->website ?? null,
                'twitter' => $data['author_twitter'] ?? $authorProfile->twitter ?? null,
                'facebook' => $data['author_facebook'] ?? $authorProfile->facebook ?? null,
                'linkedin' => $data['author_linkedin'] ?? $authorProfile->linkedin ?? null,
                'instagram' => $data['author_instagram'] ?? $authorProfile->instagram ?? null,
                'github' => $data['author_github'] ?? $authorProfile->github ?? null,
                'youtube' => $data['author_youtube'] ?? $authorProfile->youtube ?? null,
                'whatsapp' => $data['author_whatsapp'] ?? $authorProfile->whatsapp ?? null,
                'avatar' => $data['avatar'] ?? $authorProfile->avatar ?? null,
                'media_id' => $data['author_media_id'] ?? $authorProfile->media_id ?? null,
                'is_featured' => $data['author_is_featured'] ?? $authorProfile->is_featured ?? false,
                'display_order' => $data['author_display_order'] ?? $authorProfile->display_order ?? 0,
            ];

            if ($authorProfile) {
                // Update existing profile
                $authorProfile->update($profileData);
                $message = 'Author profile updated successfully';
            } else {
                // Create new profile
                $authorProfile = AuthorProfile::create($profileData);
                $message = 'Author profile created successfully';
            }

            return [
                'success' => true,
                'profile' => $authorProfile,
                'message' => $message,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to save author profile: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete author profile for a user
     */
    public function deleteAuthorProfile(int $userId): array
    {
        try {
            $authorProfile = AuthorProfile::where('user_id', $userId)->first();

            if (!$authorProfile) {
                return [
                    'success' => true,
                    'message' => 'No author profile to delete',
                ];
            }

            // Delete avatar if exists
            if ($authorProfile->avatar) {
                Storage::disk('public')->delete($authorProfile->avatar);
            }

            $authorProfile->delete();

            return [
                'success' => true,
                'message' => 'Author profile deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete author profile: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if user has author role
     */
    public function userHasAuthorRole(User $user): bool
    {
        // Check both the 'role' field and roles relationship
        return $user->role === 'author' || $user->hasRole('author');
    }

    /**
     * Ensure author profile exists for user with author role
     */
    public function ensureAuthorProfileExists(User $user): void
    {
        if ($this->userHasAuthorRole($user)) {
            // Check if profile already exists
            $exists = AuthorProfile::where('user_id', $user->id)->exists();
            
            if (!$exists) {
                // Create basic author profile
                AuthorProfile::create([
                    'user_id' => $user->id,
                    'bio' => null,
                    'job_title' => null,
                    'is_featured' => false,
                    'display_order' => 0,
                ]);
            }
        }
    }

    /**
     * Handle role change - create or delete author profile
     */
    public function handleRoleChange(User $user, ?string $oldRole, string $newRole): void
    {
        $wasAuthor = $oldRole === 'author' || ($oldRole && $user->roles()->where('slug', 'author')->exists());
        $isAuthor = $newRole === 'author';

        // Also check roles relationship
        $hasAuthorRole = $user->roles()->where('slug', 'author')->exists();

        if (($isAuthor || $hasAuthorRole) && !$wasAuthor) {
            // User became an author - create profile
            $this->ensureAuthorProfileExists($user);
        }
    }

    /**
     * Upload author avatar
     */
    protected function uploadAuthorAvatar($file): string
    {
        return $file->store('authors/avatars', 'public');
    }

    /**
     * Get author profile by user ID
     */
    public function getAuthorProfileByUserId(int $userId): ?AuthorProfile
    {
        return AuthorProfile::where('user_id', $userId)->first();
    }
}
