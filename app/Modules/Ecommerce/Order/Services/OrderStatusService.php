<?php

namespace App\Modules\Ecommerce\Order\Services;

use App\Modules\Ecommerce\Order\Models\Order;
use App\Modules\Ecommerce\Order\Models\OrderStatusHistory;
use App\Modules\Ecommerce\Order\Repositories\OrderRepository;
use App\Modules\Ecommerce\Order\Repositories\OrderStatusHistoryRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderStatusService
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderStatusHistoryRepository $statusHistoryRepository
    ) {}

    /**
     * Update order status.
     */
    public function updateStatus(
        Order $order,
        string $newStatus,
        string $notes = null,
        bool $notifyCustomer = true
    ): bool {
        try {
            DB::beginTransaction();

            $oldStatus = $order->status;

            // Update order status
            $this->orderRepository->update($order, [
                'status' => $newStatus,
                $this->getStatusTimestampField($newStatus) => now(),
            ]);

            // Create status history
            $this->createStatusHistory($order, $oldStatus, $newStatus, $notes, $notifyCustomer);

            // Update payment status if needed
            if ($newStatus === 'delivered') {
                $this->orderRepository->update($order, [
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);
            }

            DB::commit();

            // Send notification to customer if enabled
            if ($notifyCustomer) {
                $this->sendStatusNotification($order, $newStatus);
            }

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create status history record.
     */
    public function createStatusHistory(
        Order $order,
        ?string $oldStatus,
        string $newStatus,
        ?string $notes = null,
        bool $notifyCustomer = false
    ): OrderStatusHistory {
        return $this->statusHistoryRepository->create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'customer_notified' => $notifyCustomer,
            'notified_at' => $notifyCustomer ? now() : null,
        ]);
    }

    /**
     * Get timestamp field for status.
     */
    protected function getStatusTimestampField(string $status): ?string
    {
        return match($status) {
            'shipped' => 'shipped_at',
            'delivered' => 'delivered_at',
            'cancelled' => 'cancelled_at',
            default => null,
        };
    }

    /**
     * Send status notification to customer.
     */
    protected function sendStatusNotification(Order $order, string $status): void
    {
        // TODO: Implement SMS/Email notification
        // This will be integrated with SmsService later
        
        // Example:
        // $message = $this->getStatusMessage($order, $status);
        // app(SmsService::class)->send($order->customer_phone, $message);
    }

    /**
     * Get status notification message.
     */
    protected function getStatusMessage(Order $order, string $status): string
    {
        return match($status) {
            'confirmed' => "Your order #{$order->order_number} has been confirmed.",
            'processing' => "Your order #{$order->order_number} is being processed.",
            'shipped' => "Your order #{$order->order_number} has been shipped. Tracking: {$order->tracking_number}",
            'delivered' => "Your order #{$order->order_number} has been delivered. Thank you!",
            'cancelled' => "Your order #{$order->order_number} has been cancelled.",
            default => "Your order #{$order->order_number} status has been updated to {$status}.",
        };
    }

    /**
     * Get available status transitions.
     */
    public function getAvailableStatuses(Order $order): array
    {
        $currentStatus = $order->status;

        return match($currentStatus) {
            'pending' => ['processing', 'confirmed', 'cancelled'],
            'processing' => ['confirmed', 'cancelled'],
            'confirmed' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => ['refunded'],
            'cancelled' => [],
            'refunded' => [],
            'failed' => ['pending'],
            default => [],
        };
    }

    /**
     * Check if status transition is valid.
     */
    public function isValidTransition(Order $order, string $newStatus): bool
    {
        $availableStatuses = $this->getAvailableStatuses($order);
        return in_array($newStatus, $availableStatuses);
    }
}
