<?php

namespace App\Livewire\Admin\SecondaryMenu;

use App\Models\SecondaryMenuItem;
use Livewire\Component;
use Livewire\Attributes\On;

/**
 * ModuleName: Admin - Secondary Menu Management
 * Purpose: Manage secondary navigation menu items with modal-based CRUD
 * 
 * Key Methods:
 * - openCreateModal(): Open add menu item modal
 * - openEditModal($id): Open edit menu item modal
 * - confirmDelete($id): Open delete confirmation modal
 * - store(): Create new menu item
 * - update(): Update existing menu item
 * - delete(): Delete menu item (soft delete)
 * - reorder($order): Update menu items order
 * 
 * Dependencies:
 * - SecondaryMenuItem Model
 * 
 * @category Livewire
 * @package  Admin\SecondaryMenu
 * @author   AI Assistant
 * @created  2025-11-06
 * @updated  2025-11-06
 */
class SecondaryMenuList extends Component
{
    public $menuItems = [];
    
    // Modal states
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    // Form fields
    public $menuItemId = null;
    public $label = '';
    public $url = '';
    public $color = 'text-gray-700';
    public $customColorClass = '';
    public $showCustomColorInput = false;
    public $type = 'link';
    public $sort_order = 1;
    public $is_active = true;
    public $open_new_tab = false;
    
    // Delete confirmation
    public $itemToDelete = null;

    protected $rules = [
        'label' => 'required|string|max:255',
        'url' => 'required|string|max:255',
        'color' => 'required|string|max:255',
        'customColorClass' => 'nullable|string|max:500',
        'type' => 'required|in:link,dropdown',
        'sort_order' => 'required|integer|min:0',
        'is_active' => 'boolean',
        'open_new_tab' => 'boolean',
    ];

    public function mount()
    {
        $this->loadMenuItems();
    }

    public function loadMenuItems()
    {
        $this->menuItems = SecondaryMenuItem::ordered()->get();
    }

    /**
     * Watch for color changes to toggle custom input
     */
    public function updatedColor($value)
    {
        $this->showCustomColorInput = ($value === 'custom');
        
        // Clear custom class if switching away from custom
        if ($value !== 'custom') {
            $this->customColorClass = '';
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->sort_order = $this->menuItems->count() + 1;
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function openEditModal($id)
    {
        $item = SecondaryMenuItem::findOrFail($id);
        
        $this->menuItemId = $item->id;
        $this->label = $item->label;
        $this->url = $item->url;
        $this->type = $item->type;
        $this->sort_order = $item->sort_order;
        $this->is_active = $item->is_active;
        $this->open_new_tab = $item->open_new_tab;
        
        // Check if color is a predefined option or custom
        $predefinedColors = [
            'text-gray-700', 'text-red-600', 'text-blue-600', 'text-green-600',
            'text-purple-600', 'text-orange-600', 'text-yellow-600', 'text-pink-600',
            'text-indigo-600', 'text-teal-600', 'text-cyan-600', 'text-rose-600',
            'text-emerald-600', 'text-sky-600', 'text-amber-600', 'text-lime-600'
        ];
        
        if (in_array($item->color, $predefinedColors)) {
            $this->color = $item->color;
            $this->customColorClass = '';
            $this->showCustomColorInput = false;
        } else {
            $this->color = 'custom';
            $this->customColorClass = $item->color;
            $this->showCustomColorInput = true;
        }
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();
        
        // Use custom class if 'custom' is selected, otherwise use predefined color
        $finalColor = $this->color === 'custom' ? $this->customColorClass : $this->color;

        SecondaryMenuItem::create([
            'label' => $this->label,
            'url' => $this->url,
            'color' => $finalColor,
            'type' => $this->type,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'open_new_tab' => $this->open_new_tab,
        ]);

        $this->loadMenuItems();
        $this->closeCreateModal();
        
        session()->flash('success', 'Menu item created successfully!');
        $this->dispatch('menu-updated');
    }

    public function update()
    {
        $this->validate();
        
        // Use custom class if 'custom' is selected, otherwise use predefined color
        $finalColor = $this->color === 'custom' ? $this->customColorClass : $this->color;

        $item = SecondaryMenuItem::findOrFail($this->menuItemId);
        
        $item->update([
            'label' => $this->label,
            'url' => $this->url,
            'color' => $finalColor,
            'type' => $this->type,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'open_new_tab' => $this->open_new_tab,
        ]);

        $this->loadMenuItems();
        $this->closeEditModal();
        
        session()->flash('success', 'Menu item updated successfully!');
        $this->dispatch('menu-updated');
    }

    public function confirmDelete($id)
    {
        $this->itemToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteMenuItem()
    {
        if ($this->itemToDelete) {
            $item = SecondaryMenuItem::find($this->itemToDelete);
            if ($item) {
                $item->delete();
                
                session()->flash('success', 'Menu item deleted successfully!');
            }
        }

        $this->loadMenuItems();
        $this->showDeleteModal = false;
        $this->itemToDelete = null;
        $this->dispatch('menu-updated');
    }

    #[On('reorder-menu')]
    public function reorder($order)
    {
        foreach ($order as $index => $id) {
            SecondaryMenuItem::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        $this->loadMenuItems();
        session()->flash('success', 'Menu items reordered successfully!');
    }

    private function resetForm()
    {
        $this->menuItemId = null;
        $this->label = '';
        $this->url = '';
        $this->color = 'text-gray-700';
        $this->customColorClass = '';
        $this->showCustomColorInput = false;
        $this->type = 'link';
        $this->sort_order = 1;
        $this->is_active = true;
        $this->open_new_tab = false;
    }

    public function render()
    {
        return view('livewire.admin.secondary-menu.secondary-menu-list');
    }
}
