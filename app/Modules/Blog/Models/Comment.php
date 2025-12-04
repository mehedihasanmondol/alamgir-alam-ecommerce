<?php

namespace App\Modules\Blog\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ModuleName: Blog
 * Purpose: Represents a blog comment with nested replies and moderation
 * 
 * Key Methods:
 * - post(): Get the post this comment belongs to
 * - user(): Get the user who made the comment
 * - parent(): Get parent comment
 * - replies(): Get child comments (replies)
 * - approvedBy(): Get user who approved the comment
 * - approve(): Approve the comment
 * - markAsSpam(): Mark comment as spam
 * - scopeApproved(): Query only approved comments
 * - scopePending(): Query only pending comments
 * 
 * Dependencies:
 * - Post model
 * - User model
 * 
 * @category Blog
 * @package  App\Modules\Blog\Models
 * @author   AI Assistant
 * @created  2025-11-07
 * @updated  2025-11-07
 */
class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blog_comments';

    protected $fillable = [
        'blog_post_id',
        'user_id',
        'parent_id',
        'guest_name',
        'guest_email',
        'guest_website',
        'content',
        'ip_address',
        'user_agent',
        'status',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Get the post this comment belongs to
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'blog_post_id');
    }

    /**
     * Get the user who made the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get parent comment
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get child comments (replies)
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }

    /**
     * Get approved replies
     */
    public function approvedReplies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->where('status', 'approved')
            ->latest();
    }

    /**
     * Get user who approved the comment
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get commenter name (user or guest)
     */
    public function getCommenterNameAttribute(): string
    {
        return $this->user ? $this->user->name : $this->guest_name;
    }

    /**
     * Get commenter email (user or guest)
     */
    public function getCommenterEmailAttribute(): string
    {
        return $this->user ? $this->user->email : $this->guest_email;
    }

    /**
     * Check if comment is from guest
     */
    public function isGuest(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Check if comment is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if comment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if comment is spam
     */
    public function isSpam(): bool
    {
        return $this->status === 'spam';
    }

    /**
     * Approve the comment
     */
    public function approve(int $approvedBy): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approvedBy,
        ]);
    }

    /**
     * Mark comment as spam
     */
    public function markAsSpam(): void
    {
        $this->update(['status' => 'spam']);
    }

    /**
     * Move comment to trash
     */
    public function moveToTrash(): void
    {
        $this->update(['status' => 'trash']);
    }

    /**
     * Scope to get only approved comments
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get only pending comments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get only spam comments
     */
    public function scopeSpam($query)
    {
        return $query->where('status', 'spam');
    }

    /**
     * Scope to get only trashed comments
     */
    public function scopeTrashed($query)
    {
        return $query->where('status', 'trash');
    }

    /**
     * Scope to get root comments (no parent)
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
