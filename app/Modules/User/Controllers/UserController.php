<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\UserService;
use App\Modules\User\Services\RoleService;
use App\Modules\User\Requests\StoreUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

/**
 * ModuleName: User Management
 * Purpose: Handle user management operations in admin panel
 * 
 * Key Methods:
 * - index(): List all users
 * - create(): Show create user form
 * - store(): Store new user
 * - edit(): Show edit user form
 * - update(): Update user
 * - destroy(): Delete user
 * 
 * Dependencies:
 * - UserService
 * - RoleService
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class UserController extends Controller
{
    protected UserService $userService;
    protected RoleService $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'role' => $request->get('role'),
            'is_active' => $request->get('is_active'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $users = $this->userService->getAllUsers(15, $filters);
        $stats = $this->userService->getUserStats();

        return view('admin.users.index', compact('users', 'stats', 'filters'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = $this->roleService->getActiveRoles();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request)
    {
        $result = $this->userService->createUser($request->validated());

        if ($result['success']) {
            return redirect()
                ->route('admin.users.index')
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * Display the specified user
     */
    public function show(int $id)
    {
        $user = $this->userService->getUserById($id);
        
        if (!$user) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'User not found');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(int $id)
    {
        $user = $this->userService->getUserById($id);
        
        if (!$user) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'User not found');
        }

        $roles = $this->roleService->getActiveRoles();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $result = $this->userService->updateUser($id, $request->validated());

        if ($result['success']) {
            return redirect()
                ->route('admin.users.index')
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    /**
     * Remove the specified user
     */
    public function destroy(int $id)
    {
        $result = $this->userService->deleteUser($id);

        if ($result['success']) {
            return redirect()
                ->route('admin.users.index')
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->with('error', $result['message']);
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(int $id)
    {
        $result = $this->userService->toggleUserStatus($id);

        return response()->json($result);
    }
}
