<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * ModuleName: Blog
 * Purpose: Author profile information for blog authors
 * 
 * Key Methods:
 * - user(): Get the user associated with this profile
 * - getFullAvatarUrlAttribute(): Get the full avatar URL
 * - hasSocialLinks(): Check if author has any social links
 * 
 * Dependencies:
 * - User Model
 * 
 * @category Blog
 * @package  App\Models
 * @author   AI Assistant
 * @created  2025-11-16
 */
class AuthorProfile extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = [
        'user_id',
        'slug',
        'bio',
        'job_title',
        'website',
        'twitter',
        'facebook',
        'linkedin',
        'instagram',
        'github',
        'youtube',
        'whatsapp',
        'avatar',
        'media_id',
        'is_featured',
        'display_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the user that owns the profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full avatar URL
     */
    public function getFullAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        return asset('storage/' . $this->avatar);
    }

    /**
     * Get avatar or user's avatar as fallback
     */
    public function getAvatarOrFallbackAttribute(): ?string
    {
        return $this->avatar ?? $this->user->avatar ?? null;
    }

    /**
     * Check if author has any social links
     */
    public function hasSocialLinks(): bool
    {
        return !empty($this->website) 
            || !empty($this->twitter) 
            || !empty($this->facebook) 
            || !empty($this->linkedin) 
            || !empty($this->instagram) 
            || !empty($this->github) 
            || !empty($this->youtube)
            || !empty($this->whatsapp);
    }

    /**
     * Get all social links as an array
     */
    public function getSocialLinksAttribute(): array
    {
        $links = [];

        if ($this->website) {
            $links['website'] = [
                'url' => $this->website,
                'label' => 'Website',
                'icon' => 'globe',
            ];
        }

        if ($this->twitter) {
            $links['twitter'] = [
                'url' => 'https://twitter.com/' . $this->twitter,
                'label' => 'Twitter',
                'icon' => 'twitter',
            ];
        }

        if ($this->facebook) {
            $links['facebook'] = [
                'url' => 'https://facebook.com/' . $this->facebook,
                'label' => 'Facebook',
                'icon' => 'facebook',
            ];
        }

        if ($this->linkedin) {
            $links['linkedin'] = [
                'url' => 'https://linkedin.com/in/' . $this->linkedin,
                'label' => 'LinkedIn',
                'icon' => 'linkedin',
            ];
        }

        if ($this->instagram) {
            $links['instagram'] = [
                'url' => 'https://instagram.com/' . $this->instagram,
                'label' => 'Instagram',
                'icon' => 'instagram',
            ];
        }

        if ($this->github) {
            $links['github'] = [
                'url' => 'https://github.com/' . $this->github,
                'label' => 'GitHub',
                'icon' => 'github',
            ];
        }

        if ($this->youtube) {
            $links['youtube'] = [
                'url' => 'https://youtube.com/@' . $this->youtube,
                'label' => 'YouTube',
                'icon' => 'youtube',
            ];
        }

        return $links;
    }

    /**
     * Scope to get only featured authors
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->orderBy('display_order');
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Override boot method to generate slug from user's name
     */
    protected static function bootHasUniqueSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                // Generate slug from user's name if user exists
                $slugSource = $model->user ? $model->user->name : 'author';
                $model->slug = $model->generateUniqueSlug($slugSource);
            }
        });

        static::updating(function ($model) {
            // Only auto-update slug if explicitly requested
            if ($model->isDirty('slug') && !empty($model->slug)) {
                $model->slug = $model->generateUniqueSlug($model->slug);
            }
        });
    }

    /**
     * Get route key name for slug-based routing
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope to find by slug
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Get the media (avatar) for this author profile
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Media::class, 'media_id');
    }
}
