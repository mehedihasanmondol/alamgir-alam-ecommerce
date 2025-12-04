<?php

namespace App\Modules\Ecommerce\Product\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ModuleName: Product Answer Model
 * Purpose: Manage answers to product questions
 * 
 * Key Methods:
 * - question(): Get associated question
 * - user(): Get answer author
 * - scopeApproved(): Filter approved answers
 * - scopeBestAnswer(): Filter best answers
 * - markAsBestAnswer(): Mark as best answer
 * - incrementHelpful(): Increment helpful count
 * 
 * Dependencies:
 * - ProductQuestion Model
 * - User Model
 * 
 * @category Ecommerce
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_id',
        'user_id',
        'answer',
        'is_best_answer',
        'is_verified_purchase',
        'is_rewarded',
        'status',
        'helpful_count',
        'not_helpful_count',
        'user_name',
        'user_email',
    ];

    protected $casts = [
        'is_best_answer' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'is_rewarded' => 'boolean',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the question that owns the answer
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(ProductQuestion::class, 'question_id');
    }

    /**
     * Get the user who answered
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get approved answers
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Get pending answers
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get best answers
     */
    public function scopeBestAnswer($query)
    {
        return $query->where('is_best_answer', true);
    }

    /**
     * Scope: Get verified purchase answers
     */
    public function scopeVerifiedPurchase($query)
    {
        return $query->where('is_verified_purchase', true);
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
     * Mark this answer as the best answer
     */
    public function markAsBestAnswer(): void
    {
        // Remove best answer flag from other answers
        $this->question->answers()->update(['is_best_answer' => false]);
        
        // Mark this as best answer
        $this->update(['is_best_answer' => true]);
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
     * Get author name (user or guest)
     */
    public function getAuthorNameAttribute(): string
    {
        return $this->user ? $this->user->name : ($this->user_name ?? 'Guest');
    }

    /**
     * Check if answer is from authenticated user
     */
    public function isAuthenticated(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Boot method to update question answer count
     */
    protected static function booted(): void
    {
        static::created(function ($answer) {
            if ($answer->status === 'approved') {
                $answer->question->updateAnswerCount();
            }
        });

        static::updated(function ($answer) {
            if ($answer->wasChanged('status')) {
                $answer->question->updateAnswerCount();
            }
        });

        static::deleted(function ($answer) {
            $answer->question->updateAnswerCount();
        });
    }
}
