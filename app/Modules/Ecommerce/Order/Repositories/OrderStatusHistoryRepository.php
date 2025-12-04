<?php

namespace App\Modules\Ecommerce\Order\Repositories;

use App\Modules\Ecommerce\Order\Models\OrderStatusHistory;
use Illuminate\Database\Eloquent\Collection;

class OrderStatusHistoryRepository
{
    /**
     * Create status history.
     */
    public function create(array $data): OrderStatusHistory
    {
        return OrderStatusHistory::create($data);
    }

    /**
     * Get order status history.
     */
    public function getByOrderId(int $orderId): Collection
    {
        return OrderStatusHistory::with('user')
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get latest status for order.
     */
    public function getLatest(int $orderId): ?OrderStatusHistory
    {
        return OrderStatusHistory::where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
