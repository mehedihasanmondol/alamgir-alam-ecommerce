<?php

namespace App\Modules\Ecommerce\Product\Models;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'media_id',
        'image_path',
        'thumbnail_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Get image URL (large size)
     */
    public function getImageUrl(): ?string
    {
        if ($this->media) {
            return $this->media->large_url;
        }
        
        // Fallback to old image_path field
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    /**
     * Get thumbnail URL (small size)
     */
    public function getThumbnailUrl(): ?string
    {
        if ($this->media) {
            return $this->media->small_url;
        }
        
        // Fallback to old thumbnail_path field
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null;
    }

    /**
     * Get medium size URL
     */
    public function getMediumUrl(): ?string
    {
        if ($this->media) {
            return $this->media->medium_url;
        }
        
        // Fallback to old image_path field
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
