<?php

namespace App\Livewire\User;

use App\Modules\User\Repositories\UserRepository;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * ModuleName: User Management
 * Purpose: Livewire component for searching and filtering users
 * 
 * Key Methods:
 * - render(): Render the component
 * - updatingSearch(): Reset pagination when search changes
 * 
 * Dependencies:
 * - UserRepository
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class UserSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $role = '';
    public $isActive = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'role' => ['except' => ''],
        'isActive' => ['except' => ''],
    ];

    /**
     * Reset pagination when search changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when filters change
     */
    public function updatingRole()
    {
        $this->resetPage();
    }

    public function updatingIsActive()
    {
        $this->resetPage();
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->role = '';
        $this->isActive = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    /**
     * Render the component
     */
    public function render()
    {
        $userRepository = app(UserRepository::class);

        $filters = [
            'search' => $this->search,
            'role' => $this->role,
            'is_active' => $this->isActive,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ];

        $users = $userRepository->getAll($this->perPage, $filters);

        return view('livewire.user.user-search', [
            'users' => $users,
        ]);
    }
}
