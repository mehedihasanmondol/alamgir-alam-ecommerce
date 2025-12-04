<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

/**
 * ModuleName: User Management
 * Purpose: Handle all database operations for permissions
 * 
 * Key Methods:
 * - getAll(): Get all permissions
 * - getByModule(): Get permissions by module
 * - create(): Create new permission
 * 
 * Dependencies:
 * - Permission Model
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class PermissionRepository
{
    /**
     * Get all permissions
     */
    public function getAll(): Collection
    {
        return Permission::all();
    }

    /**
     * Get active permissions
     */
    public function getActive(): Collection
    {
        return Permission::active()->get();
    }

    /**
     * Get permissions by module
     */
    public function getByModule(string $module): Collection
    {
        return Permission::byModule($module)->get();
    }

    /**
     * Find permission by ID
     */
    public function findById(int $id): ?Permission
    {
        return Permission::find($id);
    }

    /**
     * Create new permission
     */
    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    /**
     * Update permission
     */
    public function update(int $id, array $data): bool
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return false;
        }

        return $permission->update($data);
    }

    /**
     * Delete permission
     */
    public function delete(int $id): bool
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return false;
        }

        return $permission->delete();
    }
}
