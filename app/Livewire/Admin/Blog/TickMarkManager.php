<?php

namespace App\Livewire\Admin\Blog;

use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Services\TickMarkService;
use Livewire\Component;

/**
 * ModuleName: Blog
 * Purpose: Livewire component for managing blog post tick marks
 * 
 * @category Blog
 * @package  App\Livewire\Admin\Blog
 * @author   AI Assistant
 * @created  2025-11-10
 */
class TickMarkManager extends Component
{
    public $postId;
    public $post;
    
    // Tick mark states
    public $isVerified = false;
    public $isEditorChoice = false;
    public $isTrending = false;
    public $isPremium = false;
    public $verificationNotes = '';
    
    // Modal states
    public $showModal = false;
    public $showVerificationModal = false;
    
    protected $listeners = ['refreshTickMarks' => '$refresh'];

    public function mount($postId)
    {
        $this->postId = $postId;
        $this->loadPost();
    }

    public function loadPost()
    {
        try {
            $this->post = Post::with('verifier')->find($this->postId);
            
            if ($this->post) {
                $this->isVerified = (bool) $this->post->is_verified;
                $this->isEditorChoice = (bool) $this->post->is_editor_choice;
                $this->isTrending = (bool) $this->post->is_trending;
                $this->isPremium = (bool) $this->post->is_premium;
                $this->verificationNotes = $this->post->verification_notes ?? '';
            }
        } catch (\Exception $e) {
            \Log::error('TickMarkManager loadPost error: ' . $e->getMessage());
            session()->flash('error', 'Failed to load post: ' . $e->getMessage());
        }
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function openVerificationModal()
    {
        $this->showVerificationModal = true;
    }

    public function closeVerificationModal()
    {
        $this->showVerificationModal = false;
        $this->verificationNotes = '';
    }

    public function toggleVerification(TickMarkService $service)
    {
        try {
            // Simple toggle without modal for now
            $post = $service->toggleVerification($this->postId);
            $this->loadPost();
            
            $message = $post->is_verified ? 'Post verified successfully!' : 'Verification removed!';
            session()->flash('success', $message);
            $this->dispatch('tickMarkUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update verification: ' . $e->getMessage());
        }
    }

    public function saveVerification(TickMarkService $service)
    {
        try {
            $service->markAsVerified($this->postId, $this->verificationNotes);
            $this->loadPost();
            $this->closeVerificationModal();
            
            session()->flash('success', 'Post verified successfully!');
            $this->dispatch('tickMarkUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to verify post: ' . $e->getMessage());
        }
    }

    public function toggleEditorChoice(TickMarkService $service)
    {
        try {
            $service->toggleEditorChoice($this->postId);
            $this->loadPost();
            
            $message = $this->isEditorChoice ? 'Added to Editor\'s Choice!' : 'Removed from Editor\'s Choice!';
            session()->flash('success', $message);
            $this->dispatch('tickMarkUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update Editor\'s Choice: ' . $e->getMessage());
        }
    }

    public function toggleTrending(TickMarkService $service)
    {
        try {
            $service->toggleTrending($this->postId);
            $this->loadPost();
            
            $message = $this->isTrending ? 'Marked as Trending!' : 'Removed from Trending!';
            session()->flash('success', $message);
            $this->dispatch('tickMarkUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update Trending status: ' . $e->getMessage());
        }
    }

    public function togglePremium(TickMarkService $service)
    {
        try {
            $service->togglePremium($this->postId);
            $this->loadPost();
            
            $message = $this->isPremium ? 'Marked as Premium!' : 'Removed from Premium!';
            session()->flash('success', $message);
            $this->dispatch('tickMarkUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update Premium status: ' . $e->getMessage());
        }
    }

    public function updateAllTickMarks(TickMarkService $service)
    {
        try {
            $data = [
                'is_verified' => $this->isVerified,
                'is_editor_choice' => $this->isEditorChoice,
                'is_trending' => $this->isTrending,
                'is_premium' => $this->isPremium,
            ];
            
            if ($this->isVerified && $this->verificationNotes) {
                $data['verification_notes'] = $this->verificationNotes;
            }
            
            $service->updateTickMarks($this->postId, $data);
            $this->loadPost();
            $this->closeModal();
            
            session()->flash('success', 'Tick marks updated successfully!');
            $this->dispatch('tickMarkUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update tick marks: ' . $e->getMessage());
        }
    }

    public function clearAllTickMarks(TickMarkService $service)
    {
        try {
            $service->clearAllTickMarks($this->postId);
            $this->loadPost();
            $this->closeModal();
            
            session()->flash('success', 'All tick marks cleared!');
            $this->dispatch('tickMarkUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to clear tick marks: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.blog.tick-mark-manager');
    }
}
