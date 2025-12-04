<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Payment Gateway Model
 * 
 * Manages online payment gateway configurations
 * Supports bKash, Nagad, SSL Commerz, and other gateways
 */
class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'is_active',
        'is_test_mode',
        'credentials',
        'settings',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
        'credentials' => 'array',
        'settings' => 'array',
    ];

    /**
     * Get orders using this gateway
     */
    public function orders()
    {
        return $this->hasMany(\App\Modules\Ecommerce\Order\Models\Order::class);
    }

    /**
     * Scope for active gateways
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get credential value
     */
    public function getCredential($key, $default = null)
    {
        return data_get($this->credentials, $key, $default);
    }

    /**
     * Get setting value
     */
    public function getSetting($key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }
}
