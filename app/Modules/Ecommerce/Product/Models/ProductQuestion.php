<?php

namespace App\Modules\Ecommerce\Product\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ModuleName: Product Question Model
 * Purpose: Manage product questions and answers
 * 
 * Key Methods:
 * - product(): Get associated product
 * - user(): Get question author
 * - answers(): Get all answers
 * - scopeApproved(): Filter approved questions
 * - scopePending(): Filter pending questions
 * - incrementHelpful(): Increment helpful count
 * - incrementNotHelpful(): Increment not helpful count
 * 
 * Dependencies:
 * - Product Model
 * - User Model
 * - ProductAnswer Model
 * 
 * @category Ecommerce
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'user_id',
        'question',
        'status',
        'helpful_count',
        'not_helpful_count',
        'answer_count',
        'user_name',
        'user_email',
    ];

    protected $casts = [
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'answer_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the question
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who asked the question
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all answers for this question
     */
    public function answers(): HasMany
    {
        return $this->hasMany(ProductAnswer::class, 'question_id');
    }

    /**
     * Get approved answers only
     */
    public function approvedAnswers(): HasMany
    {
        return $this->answers()->where('status', 'approved');
    }

    /**
     * Scope: Get approved questions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Get pending questions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get rejected questions
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
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
     * Update answer count
     */
    public function updateAnswerCount(): void
    {
        $this->update([
            'answer_count' => $this->answers()->where('status', 'approved')->count()
        ]);
    }

    /**
     * Get author name (user or guest)
     */
    public function getAuthorNameAttribute(): string
    {
        return $this->user ? $this->user->name : ($this->user_name ?? 'Guest');
    }

    /**
     * Check if question is from authenticated user
     */
    public function isAuthenticated(): bool
    {
        return !is_null($this->user_id);
    }
}
