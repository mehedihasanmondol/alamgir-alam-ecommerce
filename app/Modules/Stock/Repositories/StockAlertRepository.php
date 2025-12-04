<?php

namespace App\Modules\Stock\Repositories;

use App\Modules\Stock\Models\StockAlert;

class StockAlertRepository
{
    protected $model;

    public function __construct(StockAlert $model)
    {
        $this->model = $model;
    }

    /**
     * Get all alerts
     */
    public function getAll($perPage = 20)
    {
        return $this->model->with(['product', 'variant', 'warehouse'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get pending alerts
     */
    public function getPending()
    {
        return $this->model->pending()
            ->with(['product', 'variant', 'warehouse'])
            ->latest()
            ->get();
    }

    /**
     * Create alert
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Find by ID
     */
    public function find($id)
    {
        return $this->model->with(['product', 'variant', 'warehouse'])
            ->findOrFail($id);
    }

    /**
     * Update alert
     */
    public function update($id, array $data)
    {
        $alert = $this->find($id);
        $alert->update($data);
        return $alert;
    }

    /**
     * Delete alert
     */
    public function delete($id)
    {
        $alert = $this->find($id);
        return $alert->delete();
    }

    /**
     * Check if alert exists
     */
    public function exists($productId, $variantId, $warehouseId)
    {
        return $this->model->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->where('warehouse_id', $warehouseId)
            ->whereIn('status', ['pending', 'notified'])
            ->exists();
    }

    /**
     * Mark as notified
     */
    public function markAsNotified($id)
    {
        $alert = $this->find($id);
        return $alert->markAsNotified();
    }

    /**
     * Mark as resolved
     */
    public function markAsResolved($id, $notes = null)
    {
        $alert = $this->find($id);
        return $alert->markAsResolved($notes);
    }
}
