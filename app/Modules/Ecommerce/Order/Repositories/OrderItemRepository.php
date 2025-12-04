<?php

namespace App\Modules\Ecommerce\Order\Repositories;

use App\Modules\Ecommerce\Order\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;

class OrderItemRepository
{
    /**
     * Create order item.
     */
    public function create(array $data): OrderItem
    {
        return OrderItem::create($data);
    }

    /**
     * Create multiple order items.
     */
    public function createMany(array $items): bool
    {
        return OrderItem::insert($items);
    }

    /**
     * Get order items.
     */
    public function getByOrderId(int $orderId): Collection
    {
        return OrderItem::with(['product', 'variant'])
            ->where('order_id', $orderId)
            ->get();
    }

    /**
     * Update order item.
     */
    public function update(OrderItem $item, array $data): bool
    {
        return $item->update($data);
    }

    /**
     * Delete order item.
     */
    public function delete(OrderItem $item): bool
    {
        return $item->delete();
    }

    /**
     * Get best selling products.
     */
    public function getBestSellingProducts(int $limit = 10): Collection
    {
        return OrderItem::selectRaw('product_id, product_name, SUM(quantity) as total_sold')
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();
    }
}
