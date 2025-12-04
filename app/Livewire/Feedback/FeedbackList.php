<?php

namespace App\Livewire\Feedback;

use App\Models\Feedback;
use App\Services\FeedbackService;
use Livewire\Component;

/**
 * Feedback List Livewire Component
 * 
 * Display and manage feedback with infinite scroll (load more)
 * Similar to product review list functionality
 * 
 * @category Livewire
 * @package  Feedback
 * @author   Windsurf AI
 * @created  2025-11-25
 */
class FeedbackList extends Component
{
    public $sortBy = 'recent'; // recent, helpful, highest, lowest
    public $filterRating = null; // null or 1-5
    public $perLoad = 10;
    public $offset = 0;
    public $totalCount = 0;
    public $averageRating = 0;
    public $ratingDistribution = [];
    public $ratingEnabled = true;
    public $showImages = true;
    public $timeEnabled = true;
    public $helpfulEnabled = true;
    
    // Gallery modal
    public $showGalleryModal = false;
    public $galleryImages = [];
    public $currentImageIndex = 0;
    
    // Messages
    public $voteMessage = '';
    public $voteMessageType = ''; // success or error

    protected $queryString = ['sortBy', 'filterRating'];
    protected $listeners = ['feedback-submitted' => '$refresh'];

    public function mount()
    {
        $this->ratingEnabled = \App\Models\SiteSetting::get('feedback_rating_enabled', '1') === '1';
        $this->showImages = \App\Models\SiteSetting::get('feedback_show_images', '1') === '1';
        $this->timeEnabled = \App\Models\SiteSetting::get('feedback_time_enabled', '1') === '1';
        $this->helpfulEnabled = \App\Models\SiteSetting::get('feedback_helpful_enabled', '1') === '1';
        $this->perLoad = (int) \App\Models\SiteSetting::get('feedback_per_page_frontend', '10');
        $this->calculateStats();
    }

    public function updatingSortBy()
    {
        $this->offset = 0;
    }

    public function updatingFilterRating()
    {
        $this->offset = 0;
    }

    public function loadMore()
    {
        $this->offset += $this->perLoad;
    }

    public function filterByRating(?int $rating)
    {
        $this->filterRating = $rating;
        $this->offset = 0;
    }

    public function voteHelpful(int $id)
    {
        if (!auth()->check()) {
            $this->voteMessage = 'You must be logged in to vote';
            $this->voteMessageType = 'error';
            $this->dispatch('scroll-to-message');
            return;
        }

        $feedback = Feedback::find($id);
        if ($feedback) {
            $result = app(FeedbackService::class)->markHelpful($feedback, auth()->id());
            $this->voteMessage = $result['message'];
            $this->voteMessageType = $result['success'] ? 'success' : 'error';
            $this->dispatch('vote-updated');
            $this->dispatch('scroll-to-message');
        }
    }

    public function voteNotHelpful(int $id)
    {
        if (!auth()->check()) {
            $this->voteMessage = 'You must be logged in to vote';
            $this->voteMessageType = 'error';
            $this->dispatch('scroll-to-message');
            return;
        }

        $feedback = Feedback::find($id);
        if ($feedback) {
            $result = app(FeedbackService::class)->markNotHelpful($feedback, auth()->id());
            $this->voteMessage = $result['message'];
            $this->voteMessageType = $result['success'] ? 'success' : 'error';
            $this->dispatch('vote-updated');
            $this->dispatch('scroll-to-message');
        }
    }

    public function openGallery(int $feedbackId, int $imageIndex = 0)
    {
        $feedback = Feedback::find($feedbackId);
        if ($feedback && $feedback->images) {
            $this->galleryImages = array_map(function($img) {
                $path = is_array($img) ? $img['original'] : $img;
                return asset('storage/' . $path);
            }, $feedback->images);
            $this->currentImageIndex = $imageIndex;
            $this->showGalleryModal = true;
        }
    }

    public function closeGallery()
    {
        $this->showGalleryModal = false;
        $this->galleryImages = [];
        $this->currentImageIndex = 0;
    }

    public function nextImage()
    {
        if ($this->currentImageIndex < count($this->galleryImages) - 1) {
            $this->currentImageIndex++;
        } else {
            $this->currentImageIndex = 0;
        }
    }

    public function previousImage()
    {
        if ($this->currentImageIndex > 0) {
            $this->currentImageIndex--;
        } else {
            $this->currentImageIndex = count($this->galleryImages) - 1;
        }
    }

    protected function calculateStats()
    {
        $feedbackService = app(FeedbackService::class);
        
        // Get total count
        $query = Feedback::approved();
        if ($this->filterRating) {
            $query->byRating($this->filterRating);
        }
        $this->totalCount = $query->count();
        
        // Calculate average rating
        $this->averageRating = Feedback::approved()->avg('rating') ?? 0;
        
        // Calculate rating distribution
        $this->ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = Feedback::approved()->byRating($i)->count();
            $percentage = $this->totalCount > 0 ? ($count / $this->totalCount) * 100 : 0;
            $this->ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => round($percentage, 1),
            ];
        }
    }

    public function render()
    {
        $this->calculateStats();
        
        // Build query
        $query = Feedback::approved()->with('user');
        
        // Apply rating filter
        if ($this->filterRating) {
            $query->byRating($this->filterRating);
        }
        
        // Apply sorting
        switch ($this->sortBy) {
            case 'helpful':
                $query->mostHelpful();
                break;
            case 'highest':
                $query->highestRated();
                break;
            case 'lowest':
                $query->lowestRated();
                break;
            default:
                $query->recent();
        }
        
        // Get feedback with limit (cumulative for load more)
        $limit = $this->offset + $this->perLoad;
        $feedback = $query->limit($limit)->get();

        return view('livewire.feedback.feedback-list', [
            'feedbackItems' => $feedback,
            'hasMore' => $limit < $this->totalCount,
            'loadedCount' => min($limit, $this->totalCount),
        ]);
    }
}
