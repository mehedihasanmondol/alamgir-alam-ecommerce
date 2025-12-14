<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Feedback Model
 * 
 * Manages customer feedback for authors/business
 * Similar structure to ProductReview but for general feedback
 * 
 * @category Feedback
 * @package  App\Models
 * @author   Windsurf AI
 * @created  2025-11-25
 */
class Feedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'feedback';

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_mobile',
        'customer_address',
        'rating',
        'title',
        'feedback',
        'source',
        'source_reference_id',
        'source_metadata',
        'images',
        'status',
        'is_featured',
        'helpful_count',
        'not_helpful_count',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'rating' => 'integer',
        'images' => 'array',
        'source_metadata' => 'array',
        'is_featured' => 'boolean',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who submitted the feedback
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the feedback
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Get approved feedback
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Get pending feedback
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get rejected feedback
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope: Get featured feedback
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Filter by rating
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope: Order by most helpful
     */
    public function scopeMostHelpful($query)
    {
        return $query->orderBy('helpful_count', 'desc');
    }

    /**
     * Scope: Order by most recent
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Order by highest rating
     */
    public function scopeHighestRated($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    /**
     * Scope: Order by lowest rating
     */
    public function scopeLowestRated($query)
    {
        return $query->orderBy('rating', 'asc');
    }

    /**
     * Increment helpful count
     */
    public function incrementHelpful(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * Increment not helpful count
     */
    public function incrementNotHelpful(): void
    {
        $this->increment('not_helpful_count');
    }

    /**
     * Check if feedback is from authenticated user
     */
    public function isAuthenticated(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Get star rating as array for display
     */
    public function getStarsAttribute(): array
    {
        return [
            'filled' => $this->rating,
            'empty' => 5 - $this->rating,
        ];
    }

    /**
     * Get customer display name
     */
    public function getCustomerDisplayNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }

        return $this->customer_name ?? 'Guest';
    }

    /**
     * Get formatted mobile number
     */
    public function getFormattedMobileAttribute(): string
    {
        $mobile = $this->customer_mobile;

        // Mask middle digits for privacy
        if (strlen($mobile) >= 8) {
            return substr($mobile, 0, 3) . '****' . substr($mobile, -3);
        }

        return $mobile;
    }

    /**
     * Scope: Filter by source
     */
    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope: Get YouTube feedback
     */
    public function scopeFromYoutube($query)
    {
        return $query->where('source', 'youtube');
    }

    /**
     * Scope: Get manual feedback
     */
    public function scopeManual($query)
    {
        return $query->where('source', 'manual');
    }

    /**
     * Check if feedback is from YouTube
     */
    public function isFromYoutube(): bool
    {
        return $this->source === 'youtube';
    }

    /**
     * Get formatted source name
     */
    public function getSourceLabelAttribute(): string
    {
        return ucfirst($this->source);
    }

    /**
     * Get YouTube video title from metadata
     */
    public function getYoutubeVideoTitleAttribute(): ?string
    {
        return $this->source_metadata['video_title'] ?? null;
    }

    /**
     * Get YouTube video ID from metadata
     */
    public function getYoutubeVideoIdAttribute(): ?string
    {
        return $this->source_metadata['video_id'] ?? null;
    }

    /**
     * Get YouTube comment URL
     */
    public function getYoutubeCommentUrlAttribute(): ?string
    {
        if ($this->isFromYoutube() && $this->source_reference_id) {
            $videoId = $this->youtube_video_id;
            $commentId = $this->source_reference_id;
            return "https://www.youtube.com/watch?v={$videoId}&lc={$commentId}";
        }

        return null;
    }
}
