<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use App\Modules\User\Services\UserService;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 15;
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';

    public $showFilters = false;
    public $showDeleteModal = false;
    public $userToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortOrder = 'asc';
        }
    }

    public function toggleActive($userId, UserService $service)
    {
        $result = $service->toggleUserStatus($userId);
        if ($result['success']) {
            $this->dispatch('user-updated');
        }
    }

    public function confirmDelete($userId)
    {
        $this->userToDelete = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteUser(UserService $service)
    {
        if ($this->userToDelete) {
            $result = $service->deleteUser($this->userToDelete);
            if ($result['success']) {
                session()->flash('success', 'User deleted successfully!');
            }
        }

        $this->showDeleteModal = false;
        $this->userToDelete = null;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'roleFilter', 'statusFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render(UserService $service)
    {
        try {
            $filters = [
                'search' => $this->search,
                'role' => $this->roleFilter,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
                'sort_by' => $this->sortBy,
                'sort_order' => $this->sortOrder,
            ];

            if ($this->statusFilter !== '') {
                $filters['is_active'] = $this->statusFilter === 'active' ? '1' : '0';
            }

            $users = $service->getAllUsers($this->perPage, $filters);
            $stats = $service->getUserStats();

            return view('livewire.admin.user.user-list', [
                'users' => $users,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            \Log::error('UserList render error: ' . $e->getMessage());
            return view('livewire.admin.user.user-list', [
                'users' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'stats' => [
                    'total_users' => 0,
                    'active_users' => 0,
                    'admin_users' => 0,
                    'customer_users' => 0,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
