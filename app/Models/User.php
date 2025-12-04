<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Modules\User\Models\Permission;
use App\Modules\User\Models\Role;
use App\Modules\User\Models\UserActivity;
use App\Modules\Ecommerce\Delivery\Models\DeliveryZone;
use App\Modules\Ecommerce\Delivery\Models\DeliveryMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * ModuleName: User Management
 * Purpose: Enhanced User model with roles and permissions
 * 
 * Key Methods:
 * - roles(): Get user roles
 * - hasRole(): Check if user has specific role
 * - hasPermission(): Check if user has specific permission
 * - activities(): Get user activity log
 * 
 * Dependencies:
 * - Role Model
 * - Permission Model
 * - UserActivity Model
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'role',
        'google_id',
        'facebook_id',
        'apple_id',
        'keep_signed_in',
        'email_verified_at',
        'is_active',
        'last_login_at',
        'avatar',
        'media_id',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'default_delivery_zone_id',
        'default_delivery_method_id',
        'email_order_updates',
        'email_promotions',
        'email_newsletter',
        'email_recommendations',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'keep_signed_in' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the roles for this user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withTimestamps();
    }

    /**
     * Get user activities
     */
    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()
            ->where('slug', $roleSlug)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->roles()
            ->whereIn('slug', $roleSlugs)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionSlug) {
                $query->where('slug', $permissionSlug)
                    ->where('is_active', true);
            })
            ->exists();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by role
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Get full name with fallback
     */
    public function getFullNameAttribute(): string
    {
        return $this->name ?: 'Unknown User';
    }

    /**
     * Get default delivery zone
     */
    public function defaultDeliveryZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class, 'default_delivery_zone_id');
    }

    /**
     * Get default delivery method
     */
    public function defaultDeliveryMethod(): BelongsTo
    {
        return $this->belongsTo(DeliveryMethod::class, 'default_delivery_method_id');
    }

    /**
     * Get coupons used by this user
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_user')
            ->withPivot(['order_id', 'discount_amount', 'used_at'])
            ->withTimestamps();
    }

    /**
     * Get user addresses
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(\App\Modules\User\Models\UserAddress::class);
    }

    /**
     * Get wishlist count (session-based)
     * 
     * @return int
     */
    public function wishlistCount(): int
    {
        $wishlist = session()->get('wishlist', []);
        return count($wishlist);
    }

    /**
     * Get blog posts authored by this user
     */
    public function posts(): HasMany
    {
        return $this->hasMany(\App\Modules\Blog\Models\Post::class, 'author_id');
    }

    /**
     * Get published blog posts authored by this user
     */
    public function publishedPosts(): HasMany
    {
        return $this->posts()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Get the author profile for this user
     */
    public function authorProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\AuthorProfile::class);
    }

    /**
     * Get the media (avatar) for this user
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Media::class, 'media_id');
    }
}
