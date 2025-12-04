<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SecondaryMenuItem Model
 * Purpose: Manage secondary navigation menu items (Sale Offers, Best Sellers, etc.)
 * 
 * @author AI Assistant
 * @date 2025-11-06
 */
class SecondaryMenuItem extends Model
{
    protected $fillable = [
        'label',
        'url',
        'color',
        'type',
        'sort_order',
        'is_active',
        'open_new_tab',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_new_tab' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope: Get only active menu items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
