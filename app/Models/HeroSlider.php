<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroSlider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'media_id',
        'link',
        'button_text',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the media associated with the hero slider
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Get active sliders ordered
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->with('media')
            ->orderBy('order')
            ->get();
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): string
    {
        // First, check if using media library
        if ($this->media_id && $this->media) {
            return $this->media->large_url ?? $this->media->url;
        }

        // Fallback to legacy image field
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            return Storage::url($this->image);
        }

        // Return placeholder if no image
        return asset('images/placeholder-slider.jpg');
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Delete image when slider is deleted
        static::deleting(function ($slider) {
            if ($slider->image && !filter_var($slider->image, FILTER_VALIDATE_URL)) {
                Storage::delete($slider->image);
            }
        });
    }
}
