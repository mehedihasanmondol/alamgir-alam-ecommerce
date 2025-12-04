<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media_library';

    protected $fillable = [
        'user_id',
        'original_filename',
        'filename',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'aspect_ratio',
        'disk',
        'path',
        'large_path',
        'medium_path',
        'small_path',
        'metadata',
        'alt_text',
        'description',
        'tags',
        'scope',
    ];

    protected $casts = [
        'metadata' => 'array',
        'tags' => 'array',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'aspect_ratio' => 'decimal:4',
    ];

    /**
     * Get the user that owns the media.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full URL for the original image.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get the full URL for the large size.
     */
    public function getLargeUrlAttribute(): ?string
    {
        return $this->large_path 
            ? Storage::disk($this->disk)->url($this->large_path) 
            : $this->url;
    }

    /**
     * Get the full URL for the medium size.
     */
    public function getMediumUrlAttribute(): ?string
    {
        return $this->medium_path 
            ? Storage::disk($this->disk)->url($this->medium_path) 
            : $this->url;
    }

    /**
     * Get the full URL for the small size.
     */
    public function getSmallUrlAttribute(): ?string
    {
        return $this->small_path 
            ? Storage::disk($this->disk)->url($this->small_path) 
            : $this->url;
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Scope for user-specific media.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId)->where('scope', 'user');
    }

    /**
     * Scope for global media.
     */
    public function scopeGlobal($query)
    {
        return $query->where('scope', 'global');
    }

    /**
     * Scope for searching media.
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('original_filename', 'like', "%{$search}%")
              ->orWhere('filename', 'like', "%{$search}%")
              ->orWhere('alt_text', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for filtering by mime type.
     */
    public function scopeByMimeType($query, $mimeType)
    {
        if (!$mimeType) {
            return $query;
        }

        return $query->where('mime_type', 'like', $mimeType . '%');
    }

    /**
     * Scope for filtering by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Delete media and associated files.
     */
    public function deleteWithFiles(): bool
    {
        $disk = Storage::disk($this->disk);

        // Delete all size variants
        $paths = array_filter([
            $this->path,
            $this->large_path,
            $this->medium_path,
            $this->small_path,
        ]);

        foreach ($paths as $path) {
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        }

        return $this->delete();
    }
}
