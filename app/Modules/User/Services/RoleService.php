<?php

namespace App\Modules\User\Services;

use App\Modules\User\Repositories\RoleRepository;
use Illuminate\Support\Str;

/**
 * ModuleName: User Management
 * Purpose: Business logic for role management operations
 * 
 * Key Methods:
 * - getAllRoles(): Get all roles
 * - createRole(): Create new role
 * - updateRole(): Update role
 * - deleteRole(): Delete role
 * - assignPermissions(): Assign permissions to role
 * 
 * Dependencies:
 * - RoleRepository
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class RoleService
{
    protected RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Get all roles
     */
    public function getAllRoles()
    {
        return $this->roleRepository->getAll();
    }

    /**
     * Get active roles
     */
    public function getActiveRoles()
    {
        return $this->roleRepository->getActive();
    }

    /**
     * Get role by ID
     */
    public function getRoleById(int $id)
    {
        return $this->roleRepository->findById($id);
    }

    /**
     * Create new role
     */
    public function createRole(array $data): array
    {
        try {
            // Generate slug if not provided
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $role = $this->roleRepository->create($data);

            // Assign permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $this->roleRepository->assignPermissions($role->id, $data['permissions']);
            }

            return [
                'success' => true,
                'role' => $role,
                'message' => 'Role created successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update role
     */
    public function updateRole(int $id, array $data): array
    {
        try {
            $role = $this->roleRepository->findById($id);
            if (!$role) {
                return [
                    'success' => false,
                    'message' => 'Role not found',
                ];
            }

            // Update slug if name changed
            if (isset($data['name']) && $data['name'] !== $role->name) {
                $data['slug'] = Str::slug($data['name']);
            }

            $this->roleRepository->update($id, $data);

            // Update permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $this->roleRepository->assignPermissions($id, $data['permissions']);
            }

            return [
                'success' => true,
                'message' => 'Role updated successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete role
     */
    public function deleteRole(int $id): array
    {
        try {
            $role = $this->roleRepository->findById($id);
            if (!$role) {
                return [
                    'success' => false,
                    'message' => 'Role not found',
                ];
            }

            // Check if role is assigned to users
            if ($role->users()->count() > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete role that is assigned to users',
                ];
            }

            $this->roleRepository->delete($id);

            return [
                'success' => true,
                'message' => 'Role deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(int $roleId, array $permissionIds): array
    {
        try {
            $result = $this->roleRepository->assignPermissions($roleId, $permissionIds);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Permissions assigned successfully',
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to assign permissions',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to assign permissions: ' . $e->getMessage(),
            ];
        }
    }
}
