<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Feedback Service
 * 
 * Handles business logic for feedback management
 * including auto-user registration and image processing
 * 
 * @category Services
 * @package  App\Services
 * @author   Windsurf AI
 * @created  2025-11-25
 */
class FeedbackService
{
    protected $imageService;

    public function __construct(ImageCompressionService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Create new feedback with auto-user registration
     */
    public function createFeedback(array $data): array
    {
        // Check if user exists by email or mobile
        $user = $this->findOrCreateUser($data);

        // Process images if provided (check settings)
        $images = null;
        $showImages = \App\Models\SiteSetting::get('feedback_show_images', '1') === '1';
        if ($showImages && isset($data['images']) && is_array($data['images'])) {
            $images = $this->processImages($data['images']);
        }

        // Check if auto-approve is enabled
        $autoApprove = \App\Models\SiteSetting::get('feedback_auto_approve', '0') === '1';
        $status = $autoApprove ? 'approved' : 'pending';

        // Create feedback
        $feedback = Feedback::create([
            'user_id' => $user->id,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_mobile' => $data['customer_mobile'],
            'customer_address' => $data['customer_address'] ?? null,
            'rating' => $data['rating'] ?? null,
            'title' => $data['title'] ?? null,
            'feedback' => $data['feedback'],
            'images' => $images,
            'status' => $status,
        ]);

        return [
            'feedback' => $feedback,
            'user' => $user,
            'was_created' => $user->wasRecentlyCreated,
        ];
    }

    /**
     * Find or create user based on feedback data
     */
    protected function findOrCreateUser(array $data): User
    {
        // First check if user is already logged in
        if (Auth::check()) {
            return Auth::user();
        }

        // Try to find user by email or mobile (if email is provided)
        $query = User::query();
        
        if (!empty($data['customer_email'])) {
            $query->where('email', $data['customer_email']);
        } elseif (!empty($data['customer_mobile'])) {
            $query->where('mobile', $data['customer_mobile']);
        }
        
        $user = $query->first();

        // If user exists, return it (NO auto-login)
        if ($user) {
            return $user;
        }

        // Create new user (NO auto-login)
        $user = User::create([
            'name' => $data['customer_name'],
            'email' => !empty($data['customer_email']) ? $data['customer_email'] : null,
            'mobile' => $data['customer_mobile'],
            'address' => !empty($data['customer_address']) ? $data['customer_address'] : null,
            'password' => Hash::make(Str::random(16)), // Random password
            'email_verified_at' => !empty($data['customer_email']) ? now() : null,
        ]);

        // Assign customer role
        $customerRole = \App\Modules\User\Models\Role::where('slug', 'customer')->first();
        if ($customerRole) {
            $user->roles()->attach($customerRole->id);
        }

        return $user;
    }

    /**
     * Process and compress images to webp
     */
    protected function processImages(array $images): array
    {
        $processedImages = [];

        foreach ($images as $image) {
            if ($image->isValid()) {
                // Generate unique filename
                $filename = Str::uuid() . '.webp';
                
                // Store in feedback directory
                $path = 'feedback/' . date('Y/m');
                
                // Convert to webp and save
                $savedPath = $this->imageService->compressAndSave(
                    $image,
                    $path,
                    $filename,
                    80 // Quality
                );

                if ($savedPath) {
                    $processedImages[] = [
                        'original' => $savedPath,
                        'thumbnail' => $this->imageService->createThumbnail($savedPath, 300, 300),
                        'medium' => $this->imageService->createThumbnail($savedPath, 800, 800),
                    ];
                }
            }
        }

        return $processedImages;
    }

    /**
     * Approve feedback
     */
    public function approveFeedback(Feedback $feedback): bool
    {
        return $feedback->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);
    }

    /**
     * Reject feedback
     */
    public function rejectFeedback(Feedback $feedback): bool
    {
        return $feedback->update([
            'status' => 'rejected',
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Feedback $feedback): bool
    {
        return $feedback->update([
            'is_featured' => !$feedback->is_featured,
        ]);
    }

    /**
     * Get featured feedback for display
     */
    public function getFeaturedFeedback(int $limit = 6)
    {
        return Feedback::approved()
            ->featured()
            ->with('user')
            ->mostHelpful()
            ->limit($limit)
            ->get();
    }

    /**
     * Get all approved feedback with pagination
     */
    public function getApprovedFeedback(int $perPage = 10, array $filters = [])
    {
        $query = Feedback::approved()->with('user');

        // Apply filters
        if (isset($filters['rating']) && $filters['rating']) {
            $query->byRating($filters['rating']);
        }

        // Apply sorting
        $sort = $filters['sort'] ?? 'recent';
        switch ($sort) {
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

        return $query->paginate($perPage);
    }

    /**
     * Mark feedback as helpful (only once per user)
     */
    public function markHelpful(Feedback $feedback, ?int $userId = null): array
    {
        if (!$userId) {
            return ['success' => false, 'message' => 'You must be logged in to vote'];
        }

        // Check if user already voted
        $existingVote = \App\Models\FeedbackVote::where('feedback_id', $feedback->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type === 'helpful') {
                return ['success' => false, 'message' => 'You already marked this as helpful'];
            }
            
            // User previously voted "not helpful", so switch their vote
            $existingVote->update(['vote_type' => 'helpful']);
            $feedback->decrement('not_helpful_count');
            $feedback->increment('helpful_count');
            
            return ['success' => true, 'message' => 'Vote changed to helpful'];
        }

        // Create new vote
        \App\Models\FeedbackVote::create([
            'feedback_id' => $feedback->id,
            'user_id' => $userId,
            'vote_type' => 'helpful',
            'ip_address' => request()->ip(),
        ]);

        $feedback->increment('helpful_count');
        
        return ['success' => true, 'message' => 'Marked as helpful'];
    }

    /**
     * Mark feedback as not helpful (only once per user)
     */
    public function markNotHelpful(Feedback $feedback, ?int $userId = null): array
    {
        if (!$userId) {
            return ['success' => false, 'message' => 'You must be logged in to vote'];
        }

        // Check if user already voted
        $existingVote = \App\Models\FeedbackVote::where('feedback_id', $feedback->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type === 'not_helpful') {
                return ['success' => false, 'message' => 'You already marked this as not helpful'];
            }
            
            // User previously voted "helpful", so switch their vote
            $existingVote->update(['vote_type' => 'not_helpful']);
            $feedback->decrement('helpful_count');
            $feedback->increment('not_helpful_count');
            
            return ['success' => true, 'message' => 'Vote changed to not helpful'];
        }

        // Create new vote
        \App\Models\FeedbackVote::create([
            'feedback_id' => $feedback->id,
            'user_id' => $userId,
            'vote_type' => 'not_helpful',
            'ip_address' => request()->ip(),
        ]);

        $feedback->increment('not_helpful_count');
        
        return ['success' => true, 'message' => 'Marked as not helpful'];
    }
}
