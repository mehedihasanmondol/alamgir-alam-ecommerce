<?php

namespace App\Livewire\User;

use App\Modules\User\Services\UserService;
use Livewire\Component;

/**
 * ModuleName: User Management
 * Purpose: Livewire component for toggling user active status
 * 
 * Key Methods:
 * - toggleStatus(): Toggle user active/inactive status
 * 
 * Dependencies:
 * - UserService
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class UserStatusToggle extends Component
{
    public $userId;
    public $isActive;

    public function mount($userId, $isActive)
    {
        $this->userId = $userId;
        $this->isActive = $isActive;
    }

    /**
     * Toggle user status
     */
    public function toggleStatus()
    {
        $userService = app(UserService::class);
        $result = $userService->toggleUserStatus($this->userId);

        if ($result['success']) {
            $this->isActive = $result['is_active'];
            $this->dispatch('user-status-updated', [
                'message' => $result['message'],
                'userId' => $this->userId,
                'isActive' => $this->isActive,
            ]);
        } else {
            $this->dispatch('user-status-error', [
                'message' => $result['message'],
            ]);
        }
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.user.user-status-toggle');
    }
}
