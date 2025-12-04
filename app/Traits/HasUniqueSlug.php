<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait: HasUniqueSlug
 * Purpose: Auto-generate unique slugs for models
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
trait HasUniqueSlug
{
    /**
     * Boot the trait
     */
    protected static function bootHasUniqueSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug) && isset($model->name)) {
                $model->slug = $model->generateUniqueSlug($model->name);
            }
        });

        static::updating(function ($model) {
            // Check if model wants to disable auto-slug updates
            if (method_exists($model, 'shouldAutoUpdateSlug') && !$model->shouldAutoUpdateSlug()) {
                return;
            }
            
            if ($model->isDirty('name') && !$model->isDirty('slug')) {
                $model->slug = $model->generateUniqueSlug($model->name);
            }
        });
    }

    /**
     * Generate a unique slug with Bangla/Unicode support
     */
    public function generateUniqueSlug(string $text): string
    {
        // Use custom slug helper that supports Bangla characters
        $slug = generate_slug($text);
        
        // Fallback to Laravel's Str::slug if generate_slug returns empty
        if (empty($slug)) {
            $slug = Str::slug($text);
        }
        
        $originalSlug = $slug;
        $count = 1;

        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    protected function slugExists(string $slug): bool
    {
        $query = static::where('slug', $slug);

        if (isset($this->id)) {
            $query->where('id', '!=', $this->id);
        }

        return $query->exists();
    }
}
