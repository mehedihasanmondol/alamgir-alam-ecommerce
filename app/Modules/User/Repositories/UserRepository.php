<?php

namespace App\Modules\User\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * ModuleName: User Management
 * Purpose: Handle all database operations for users
 * 
 * Key Methods:
 * - getAll(): Get all users with pagination
 * - findById(): Find user by ID
 * - create(): Create new user
 * - update(): Update user
 * - delete(): Delete user
 * - search(): Search users
 * 
 * Dependencies:
 * - User Model
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class UserRepository
{
    /**
     * Get all users with pagination
     */
    public function getAll(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = User::with('roles');

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%")
                    ->orWhere('mobile', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?User
    {
        return User::with('roles', 'activities')->find($id);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create new user
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update user
     */
    public function update(int $id, array $data): bool
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }

        return $user->update($data);
    }

    /**
     * Delete user
     */
    public function delete(int $id): bool
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    /**
     * Get active users count
     */
    public function getActiveCount(): int
    {
        return User::where('is_active', true)->count();
    }

    /**
     * Get users by role
     */
    public function getByRole(string $role): Collection
    {
        return User::where('role', $role)->get();
    }

    /**
     * Assign role to user
     */
    public function assignRole(int $userId, int $roleId): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $user->roles()->syncWithoutDetaching([$roleId]);
        return true;
    }

    /**
     * Remove role from user
     */
    public function removeRole(int $userId, int $roleId): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $user->roles()->detach($roleId);
        return true;
    }

    /**
     * Sync user roles
     */
    public function syncRoles(int $userId, array $roleIds): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $user->roles()->sync($roleIds);
        return true;
    }

    /**
     * Update last login
     */
    public function updateLastLogin(int $userId): bool
    {
        return User::where('id', $userId)->update([
            'last_login_at' => now(),
        ]);
    }
}
