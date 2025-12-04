<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Feedback Vote Model
 * 
 * Tracks user votes on feedback (helpful/not helpful)
 * Ensures one vote per user per feedback
 * 
 * @category Models
 * @package  App\Models
 * @author   Windsurf AI
 * @created  2025-11-26
 */
class FeedbackVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_id',
        'user_id',
        'vote_type',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the feedback that owns the vote
     */
    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }

    /**
     * Get the user that owns the vote
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
