<?php

namespace App\Modules\User\Services;

use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Models\UserActivity;
use App\Modules\User\Models\Role;
use App\Services\AuthorProfileService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * ModuleName: User Management
 * Purpose: Business logic for user management operations
 * 
 * Key Methods:
 * - getAllUsers(): Get paginated users
 * - createUser(): Create new user
 * - updateUser(): Update user details
 * - deleteUser(): Delete user
 * - toggleUserStatus(): Activate/deactivate user
 * 
 * Dependencies:
 * - UserRepository
 * - UserActivity Model
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class UserService
{
    protected UserRepository $userRepository;
    protected AuthorProfileService $authorProfileService;

    public function __construct(
        UserRepository $userRepository,
        AuthorProfileService $authorProfileService
    ) {
        $this->userRepository = $userRepository;
        $this->authorProfileService = $authorProfileService;
    }

    /**
     * Get all users with pagination and filters
     */
    public function getAllUsers(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->userRepository->getAll($perPage, $filters);
    }

    /**
     * Get user by ID
     */
    public function getUserById(int $id)
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Create new user
     */
    public function createUser(array $data): array
    {
        try {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Handle avatar upload
            if (isset($data['avatar']) && $data['avatar']) {
                $data['avatar'] = $this->uploadAvatar($data['avatar']);
            }

            // Set default values
            $data['is_active'] = $data['is_active'] ?? true;

            $user = $this->userRepository->create($data);

            // Auto-assign role-based permissions
            if (isset($data['role'])) {
                $this->autoAssignRolePermissions($user->id, $data['role']);
            }

            // Assign roles if provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                $this->userRepository->syncRoles($user->id, $data['roles']);
            }

            // Create author profile if user has author role
            $user->load('roles'); // Reload user with roles
            if ($this->authorProfileService->userHasAuthorRole($user)) {
                $this->authorProfileService->createOrUpdateAuthorProfile($user->id, $data);
            }

            // Log activity
            $this->logActivity($user->id, 'user_created', 'User account created');

            return [
                'success' => true,
                'user' => $user,
                'message' => 'User created successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update user
     */
    public function updateUser(int $id, array $data): array
    {
        try {
            $user = $this->userRepository->findById($id);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                ];
            }

            // Store old role for comparison
            $oldRole = $user->role;

            // Hash password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Handle avatar upload (legacy file upload)
            if (isset($data['avatar']) && $data['avatar']) {
                // Delete old avatar
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $data['avatar'] = $this->uploadAvatar($data['avatar']);
            }

            // Handle media_id (media library avatar)
            // If media_id is provided, it takes precedence
            if (isset($data['media_id'])) {
                // Keep the media_id value (can be null to remove)
                $data['media_id'] = $data['media_id'];
            }

            $this->userRepository->update($id, $data);

            // Auto-assign role-based permissions if role changed
            if (isset($data['role']) && $data['role'] !== $oldRole) {
                $this->autoAssignRolePermissions($id, $data['role']);
            }

            // Sync roles if provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                $this->userRepository->syncRoles($id, $data['roles']);
            }

            // Reload user with updated roles
            $user->refresh();
            $user->load('roles');

            // Handle author profile based on role change
            $newRole = $data['role'] ?? $oldRole;
            $this->authorProfileService->handleRoleChange($user, $oldRole, $newRole);

            // If user is author, update or create author profile
            if ($this->authorProfileService->userHasAuthorRole($user)) {
                $this->authorProfileService->createOrUpdateAuthorProfile($user->id, $data);
            } elseif ($oldRole === 'author' && $newRole !== 'author') {
                // User is no longer an author - optionally delete profile
                // Commented out to preserve author data
                // $this->authorProfileService->deleteAuthorProfile($user->id);
            }

            // Log activity
            $this->logActivity($id, 'user_updated', 'User account updated');

            return [
                'success' => true,
                'message' => 'User updated successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(int $id): array
    {
        try {
            $user = $this->userRepository->findById($id);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                ];
            }

            // Delete avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $this->userRepository->delete($id);

            return [
                'success' => true,
                'message' => 'User deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(int $id): array
    {
        try {
            $user = $this->userRepository->findById($id);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                ];
            }

            $newStatus = !$user->is_active;
            $this->userRepository->update($id, ['is_active' => $newStatus]);

            // Log activity
            $action = $newStatus ? 'activated' : 'deactivated';
            $this->logActivity($id, 'status_changed', "User account {$action}");

            return [
                'success' => true,
                'message' => 'User status updated successfully',
                'is_active' => $newStatus,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update user status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Assign role to user
     */
    public function assignRole(int $userId, int $roleId): array
    {
        try {
            $result = $this->userRepository->assignRole($userId, $roleId);
            
            if ($result) {
                $this->logActivity($userId, 'role_assigned', 'New role assigned to user');
                return [
                    'success' => true,
                    'message' => 'Role assigned successfully',
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to assign role',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to assign role: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Remove role from user
     */
    public function removeRole(int $userId, int $roleId): array
    {
        try {
            $result = $this->userRepository->removeRole($userId, $roleId);
            
            if ($result) {
                $this->logActivity($userId, 'role_removed', 'Role removed from user');
                return [
                    'success' => true,
                    'message' => 'Role removed successfully',
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to remove role',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to remove role: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get user statistics
     */
    public function getUserStats(): array
    {
        return [
            'total_users' => $this->userRepository->getAll(999999)->total(),
            'active_users' => $this->userRepository->getActiveCount(),
            'admin_users' => $this->userRepository->getByRole('admin')->count(),
            'customer_users' => $this->userRepository->getByRole('customer')->count(),
        ];
    }

    /**
     * Upload avatar
     */
    protected function uploadAvatar($file): string
    {
        return $file->store('avatars', 'public');
    }

    /**
     * Auto-assign permissions based on user role type
     * 
     * @param int $userId
     * @param string $roleType (admin, author, customer)
     * @return void
     */
    protected function autoAssignRolePermissions(int $userId, string $roleType): void
    {
        // Map role types to role slugs
        $roleSlugMap = [
            'admin' => 'admin',
            'author' => 'author',
            'customer' => 'customer',
        ];

        // Get the role slug from the role type
        $roleSlug = $roleSlugMap[$roleType] ?? null;

        if (!$roleSlug) {
            return; // No matching role found
        }

        // Find the role by slug
        $role = Role::where('slug', $roleSlug)->where('is_active', true)->first();

        if (!$role) {
            return; // Role not found
        }

        // Clear existing roles first
        $this->userRepository->syncRoles($userId, []);

        // Assign the new role
        $this->userRepository->assignRole($userId, $role->id);

        // Log the auto-assignment
        $this->logActivity($userId, 'role_auto_assigned', "Auto-assigned {$role->name} role with permissions");
    }

    /**
     * Log user activity
     */
    protected function logActivity(int $userId, string $type, string $description): void
    {
        UserActivity::create([
            'user_id' => $userId,
            'activity_type' => $type,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
