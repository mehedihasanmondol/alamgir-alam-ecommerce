<?php

namespace App\Livewire;

use Livewire\Component;
use App\Modules\Ecommerce\Category\Models\Category;

/**
 * ModuleName: Mobile Menu Component
 * Purpose: Multi-level mobile navigation with subcategories
 * 
 * Features:
 * - Main categories list with arrows
 * - Subcategory navigation
 * - Back button and breadcrumb tracking
 * - Shop all link for each category
 * - Smooth slide animations
 * 
 * @category Livewire
 * @package  Components
 * @created  2025-11-13
 */
class MobileMenu extends Component
{
    public $isOpen = false;
    public $currentLevel = 'main'; // 'main' or 'subcategory'
    public $selectedCategory = null;
    public $categories;
    public $subcategories = [];

    protected $listeners = ['openMenu'];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function openMenu()
    {
        $this->isOpen = true;
        $this->currentLevel = 'main';
        $this->selectedCategory = null;
        $this->subcategories = [];
    }

    public function closeMenu()
    {
        $this->isOpen = false;
        $this->currentLevel = 'main';
        $this->selectedCategory = null;
        $this->subcategories = [];
    }

    public function selectCategory($categoryId)
    {
        $category = Category::find($categoryId);
        
        if ($category) {
            $this->selectedCategory = $category;
            $this->subcategories = $category->children()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
            
            if ($this->subcategories->count() > 0) {
                $this->currentLevel = 'subcategory';
            } else {
                // If no subcategories, redirect to category page
                return redirect()->route('categories.show', $category->slug);
            }
        }
    }

    public function goBack()
    {
        $this->currentLevel = 'main';
        $this->selectedCategory = null;
        $this->subcategories = [];
    }

    public function render()
    {
        return view('livewire.mobile-menu');
    }
}
