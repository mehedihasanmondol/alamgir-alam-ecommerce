<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ModuleName: Promotional Banner Model
 * Purpose: Manage promotional banners with countdown timers
 * 
 * Features:
 * - Countdown timer support
 * - Active/inactive status
 * - Customizable colors
 * - Optional links
 * - Sort ordering
 * - Dismissible option
 * 
 * @category Models
 * @package  App\Models
 * @created  2025-11-13
 */
class PromotionalBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'countdown_end',
        'background_color',
        'text_color',
        'link_url',
        'link_text',
        'is_active',
        'show_countdown',
        'is_dismissible',
        'sort_order',
    ];

    protected $casts = [
        'countdown_end' => 'datetime',
        'is_active' => 'boolean',
        'show_countdown' => 'boolean',
        'is_dismissible' => 'boolean',
    ];

    /**
     * Scope: Get only active banners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get ordered banners
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Check if countdown is still valid
     */
    public function isCountdownActive(): bool
    {
        if (!$this->show_countdown || !$this->countdown_end) {
            return false;
        }
        
        return $this->countdown_end->isFuture();
    }

    /**
     * Get time remaining for countdown
     */
    public function getTimeRemaining(): ?array
    {
        if (!$this->isCountdownActive()) {
            return null;
        }

        $now = now();
        $diff = $now->diff($this->countdown_end);

        return [
            'days' => $diff->d,
            'hours' => $diff->h,
            'minutes' => $diff->i,
            'seconds' => $diff->s,
            'total_seconds' => $this->countdown_end->diffInSeconds($now),
        ];
    }
}
