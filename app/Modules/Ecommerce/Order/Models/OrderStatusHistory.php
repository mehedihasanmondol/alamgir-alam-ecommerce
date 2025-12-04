<?php

namespace App\Modules\Ecommerce\Order\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'old_status',
        'new_status',
        'notes',
        'customer_notified',
        'notified_at',
    ];

    protected $casts = [
        'customer_notified' => 'boolean',
        'notified_at' => 'datetime',
    ];

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who changed the status.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->new_status));
    }

    /**
     * Get status change description.
     */
    public function getDescriptionAttribute(): string
    {
        if ($this->old_status) {
            return "Status changed from {$this->old_status} to {$this->new_status}";
        }

        return "Order status set to {$this->new_status}";
    }
}
