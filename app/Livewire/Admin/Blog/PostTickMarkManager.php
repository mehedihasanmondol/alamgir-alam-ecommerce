<?php

namespace App\Livewire\Admin\Blog;

use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Models\TickMark;
use App\Modules\Blog\Repositories\TickMarkRepository;
use Livewire\Component;

/**
 * ModuleName: Blog
 * Purpose: Livewire component for managing post tick marks dynamically
 * 
 * @category Blog
 * @package  App\Livewire\Admin\Blog
 * @author   AI Assistant
 * @created  2025-11-10
 */
class PostTickMarkManager extends Component
{
    public $postId;
    public $post;
    public $availableTickMarks = [];
    public $selectedTickMarks = [];
    public $showModal = false;
    
    // For creating/editing tick mark
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingTickMarkId = null;
    public $newTickMarkName = '';
    public $newTickMarkLabel = '';
    public $newTickMarkDescription = '';
    public $newTickMarkIcon = 'check-circle';
    public $newTickMarkColor = '#3B82F6'; // Hex color
    public $newTickMarkBgColor = 'bg-blue-500';
    public $newTickMarkTextColor = 'text-white';
    
    protected $listeners = ['refreshTickMarks' => 'loadData'];

    public function mount($postId)
    {
        $this->postId = $postId;
        $this->loadData();
    }

    public function loadData()
    {
        try {
            $this->post = Post::with('tickMarks')->find($this->postId);
            
            if ($this->post) {
                // Load all active tick marks
                $this->availableTickMarks = TickMark::active()->ordered()->get()->toArray();
                
                // Get currently selected tick marks
                $this->selectedTickMarks = $this->post->tickMarks->pluck('id')->toArray();
            }
        } catch (\Exception $e) {
            \Log::error('PostTickMarkManager loadData error: ' . $e->getMessage());
            session()->flash('error', 'Failed to load tick marks: ' . $e->getMessage());
        }
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->loadData();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
    }

    public function resetCreateForm()
    {
        $this->newTickMarkName = '';
        $this->newTickMarkLabel = '';
        $this->newTickMarkDescription = '';
        $this->newTickMarkIcon = 'check-circle';
        $this->newTickMarkColor = '#3B82F6';
        $this->newTickMarkBgColor = 'bg-blue-500';
        $this->newTickMarkTextColor = 'text-white';
    }

    public function toggleTickMark($tickMarkId)
    {
        try {
            if (in_array($tickMarkId, $this->selectedTickMarks)) {
                // Remove from selection
                $this->selectedTickMarks = array_diff($this->selectedTickMarks, [$tickMarkId]);
            } else {
                // Add to selection
                $this->selectedTickMarks[] = $tickMarkId;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to toggle tick mark: ' . $e->getMessage());
        }
    }

    public function saveTickMarks()
    {
        try {
            $this->post->syncTickMarks($this->selectedTickMarks);
            $this->loadData();
            $this->closeModal();
            
            session()->flash('success', 'Tick marks updated successfully!');
            $this->dispatch('tickMarkUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save tick marks: ' . $e->getMessage());
        }
    }

    public function createNewTickMark()
    {
        $this->validate([
            'newTickMarkName' => 'required|string|max:100',
            'newTickMarkLabel' => 'required|string|max:100',
            'newTickMarkDescription' => 'nullable|string|max:500',
            'newTickMarkIcon' => 'required|string|max:50',
            'newTickMarkColor' => 'required|string|max:50',
        ]);

        try {
            $tickMark = TickMark::create([
                'name' => $this->newTickMarkName,
                'label' => $this->newTickMarkLabel,
                'description' => $this->newTickMarkDescription,
                'icon' => $this->newTickMarkIcon,
                'color' => $this->newTickMarkColor,
                'bg_color' => $this->newTickMarkColor, // Store hex color
                'text_color' => $this->getContrastColor($this->newTickMarkColor),
                'is_active' => true,
                'is_system' => false,
                'sort_order' => TickMark::max('sort_order') + 1,
            ]);

            // Automatically select the new tick mark
            $this->selectedTickMarks[] = $tickMark->id;
            
            $this->loadData();
            $this->closeCreateModal();
            
            session()->flash('success', 'New tick mark created and added!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create tick mark: ' . $e->getMessage());
        }
    }

    public function openEditModal($tickMarkId)
    {
        $tickMark = TickMark::find($tickMarkId);
        
        if (!$tickMark) {
            session()->flash('error', 'Tick mark not found');
            return;
        }

        $this->editingTickMarkId = $tickMarkId;
        $this->newTickMarkName = $tickMark->name;
        $this->newTickMarkLabel = $tickMark->label;
        $this->newTickMarkDescription = $tickMark->description;
        $this->newTickMarkIcon = $tickMark->icon;
        $this->newTickMarkColor = $tickMark->color;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingTickMarkId = null;
        $this->resetCreateForm();
    }

    public function updateTickMark()
    {
        $this->validate([
            'newTickMarkName' => 'required|string|max:100',
            'newTickMarkLabel' => 'required|string|max:100',
            'newTickMarkDescription' => 'nullable|string|max:500',
            'newTickMarkIcon' => 'required|string|max:50',
            'newTickMarkColor' => 'required|string|max:50',
        ]);

        try {
            $tickMark = TickMark::find($this->editingTickMarkId);
            
            if (!$tickMark) {
                session()->flash('error', 'Tick mark not found');
                return;
            }

            $tickMark->update([
                'name' => $this->newTickMarkName,
                'label' => $this->newTickMarkLabel,
                'description' => $this->newTickMarkDescription,
                'icon' => $this->newTickMarkIcon,
                'color' => $this->newTickMarkColor,
                'bg_color' => $this->newTickMarkColor, // Store hex color
                'text_color' => $this->getContrastColor($this->newTickMarkColor),
            ]);

            $this->loadData();
            $this->closeEditModal();
            
            session()->flash('success', 'Tick mark updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update tick mark: ' . $e->getMessage());
        }
    }

    public function deleteTickMark($tickMarkId)
    {
        try {
            $tickMark = TickMark::find($tickMarkId);
            
            if (!$tickMark) {
                session()->flash('error', 'Tick mark not found');
                return;
            }

            // Remove from selected if it's selected
            $this->selectedTickMarks = array_diff($this->selectedTickMarks, [$tickMarkId]);
            
            $tickMark->delete();
            $this->loadData();
            
            session()->flash('success', 'Tick mark deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete tick mark: ' . $e->getMessage());
        }
    }

    /**
     * Calculate contrast color (white or black) based on background color
     */
    private function getContrastColor($hexColor)
    {
        // Remove # if present
        $hexColor = ltrim($hexColor, '#');
        
        // Convert to RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Calculate luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        // Return white for dark colors, black for light colors
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }

    public function render()
    {
        return view('livewire.admin.blog.post-tick-mark-manager');
    }
}
