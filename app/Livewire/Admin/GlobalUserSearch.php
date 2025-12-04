<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

/**
 * ModuleName: User Management
 * Purpose: Livewire component for global user search in admin panel
 * 
 * Key Methods:
 * - render(): Render search results
 * 
 * Dependencies:
 * - User Model
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class GlobalUserSearch extends Component
{
    public $query = '';
    public $results = [];
    public $showResults = false;

    /**
     * Update search results when query changes
     */
    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->showResults = false;
            return;
        }

        $this->results = User::where(function ($q) {
            $q->where('name', 'like', '%' . $this->query . '%')
                ->orWhere('email', 'like', '%' . $this->query . '%')
                ->orWhere('mobile', 'like', '%' . $this->query . '%');
        })
        ->limit(10)
        ->get();

        $this->showResults = true;
    }

    /**
     * Clear search
     */
    public function clearSearch()
    {
        $this->query = '';
        $this->results = [];
        $this->showResults = false;
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.admin.global-user-search');
    }
}
