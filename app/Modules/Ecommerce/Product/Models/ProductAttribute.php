<?php

namespace App\Modules\Ecommerce\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_visible',
        'is_variation',
        'position',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_variation' => 'boolean',
        'position' => 'integer',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_attribute_id')->orderBy('position');
    }

    public function scopeActive($query)
    {
        return $query->where('is_visible', true);
    }
}
