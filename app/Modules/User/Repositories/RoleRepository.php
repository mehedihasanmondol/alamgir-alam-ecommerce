<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\Role;
use Illuminate\Database\Eloquent\Collection;

/**
 * ModuleName: User Management
 * Purpose: Handle all database operations for roles
 * 
 * Key Methods:
 * - getAll(): Get all roles
 * - findById(): Find role by ID
 * - create(): Create new role
 * - update(): Update role
 * - delete(): Delete role
 * 
 * Dependencies:
 * - Role Model
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class RoleRepository
{
    /**
     * Get all roles
     */
    public function getAll(): Collection
    {
        return Role::with('permissions')->get();
    }

    /**
     * Get active roles
     */
    public function getActive(): Collection
    {
        return Role::active()->with('permissions')->get();
    }

    /**
     * Find role by ID
     */
    public function findById(int $id): ?Role
    {
        return Role::with('permissions')->find($id);
    }

    /**
     * Find role by slug
     */
    public function findBySlug(string $slug): ?Role
    {
        return Role::where('slug', $slug)->with('permissions')->first();
    }

    /**
     * Create new role
     */
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Update role
     */
    public function update(int $id, array $data): bool
    {
        $role = Role::find($id);
        if (!$role) {
            return false;
        }

        return $role->update($data);
    }

    /**
     * Delete role
     */
    public function delete(int $id): bool
    {
        $role = Role::find($id);
        if (!$role) {
            return false;
        }

        return $role->delete();
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(int $roleId, array $permissionIds): bool
    {
        $role = Role::find($roleId);
        if (!$role) {
            return false;
        }

        $role->permissions()->sync($permissionIds);
        return true;
    }
}
