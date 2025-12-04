<?php

namespace App\Modules\Ecommerce\Order\Repositories;

use App\Modules\Ecommerce\Order\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    /**
     * Get all orders with pagination.
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Order::with(['user', 'items', 'billingAddress', 'shippingAddress'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Find order by ID.
     */
    public function find(int $id): ?Order
    {
        return Order::with([
            'user',
            'items.product',
            'items.variant',
            'billingAddress',
            'shippingAddress',
            'deliveryZone',
            'deliveryMethod',
            'statusHistories.user',
            'payments'
        ])->find($id);
    }

    /**
     * Find order by order number.
     */
    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::with([
            'user',
            'items.product',
            'items.variant',
            'billingAddress',
            'shippingAddress',
            'deliveryZone',
            'deliveryMethod',
            'statusHistories',
            'payments'
        ])->where('order_number', $orderNumber)->first();
    }

    /**
     * Get user orders.
     */
    public function getUserOrders(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Order::with(['items.product', 'billingAddress', 'shippingAddress'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create new order.
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    /**
     * Update order.
     */
    public function update(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    /**
     * Delete order.
     */
    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    /**
     * Get recent orders.
     */
    public function getRecent(int $limit = 10): Collection
    {
        return Order::with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get orders by status.
     */
    public function getByStatus(string $status, int $limit = null): Collection
    {
        $query = Order::with(['user', 'items'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get order statistics.
     */
    public function getStatistics(array $filters = []): array
    {
        $query = Order::query();

        // Apply date filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_orders' => (clone $query)->count(),
            'pending_orders' => (clone $query)->where('status', 'pending')->count(),
            'processing_orders' => (clone $query)->where('status', 'processing')->count(),
            'completed_orders' => (clone $query)->where('status', 'delivered')->count(),
            'cancelled_orders' => (clone $query)->where('status', 'cancelled')->count(),
            'total_revenue' => (clone $query)->where('payment_status', 'paid')->sum('total_amount'),
            'pending_revenue' => (clone $query)->where('payment_status', 'pending')->sum('total_amount'),
        ];
    }

    /**
     * Get daily orders count for chart.
     */
    public function getDailyOrdersCount(int $days = 7): array
    {
        $data = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = $data->firstWhere('date', $date)?->count ?? 0;
            $result[$date] = $count;
        }

        return $result;
    }

    /**
     * Get revenue by day for chart.
     */
    public function getDailyRevenue(int $days = 7): array
    {
        $data = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->where('created_at', '>=', now()->subDays($days))
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $revenue = $data->firstWhere('date', $date)?->revenue ?? 0;
            $result[$date] = (float) $revenue;
        }

        return $result;
    }

    /**
     * Get orders by status count.
     */
    public function getStatusDistribution(): array
    {
        return Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
}
