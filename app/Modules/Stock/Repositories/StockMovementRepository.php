<?php

namespace App\Modules\Stock\Repositories;

use App\Modules\Stock\Models\StockMovement;

class StockMovementRepository
{
    protected $model;

    public function __construct(StockMovement $model)
    {
        $this->model = $model;
    }

    /**
     * Get all movements with filters
     */
    public function getAll(array $filters = [], $perPage = 20)
    {
        $query = $this->model->with(['product', 'variant', 'warehouse', 'user', 'supplier'])
            ->latest();

        // Filter by type
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Filter by warehouse
        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        // Filter by product
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        // Filter by date range
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        // Search by reference number
        if (!empty($filters['search'])) {
            $query->where('reference_number', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage);
    }

    /**
     * Create new stock movement
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
        return $this->model->with(['product', 'variant', 'warehouse', 'user', 'supplier'])
            ->findOrFail($id);
    }

    /**
     * Update movement
     */
    public function update($id, array $data)
    {
        $movement = $this->find($id);
        $movement->update($data);
        return $movement;
    }

    /**
     * Delete movement
     */
    public function delete($id)
    {
        $movement = $this->find($id);
        return $movement->delete();
    }

    /**
     * Get movements for a product
     */
    public function getForProduct($productId, $variantId = null, $warehouseId = null)
    {
        $query = $this->model->where('product_id', $productId);

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->with(['warehouse', 'user'])
            ->latest()
            ->get();
    }

    /**
     * Get current stock level
     */
    public function getCurrentStock($productId, $variantId = null, $warehouseId = null)
    {
        $query = $this->model->where('product_id', $productId);

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        // Sum all quantities (positive and negative)
        return $query->sum('quantity');
    }

    /**
     * Get movements by type
     */
    public function getByType($type, $limit = null)
    {
        $query = $this->model->where('type', $type)
            ->with(['product', 'warehouse', 'user'])
            ->latest();

        return $limit ? $query->take($limit)->get() : $query->get();
    }

    /**
     * Get recent movements
     */
    public function getRecent($limit = 10)
    {
        return $this->model->with(['product', 'variant', 'warehouse', 'user'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get movements for date range
     */
    public function getForDateRange($startDate, $endDate)
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])
            ->with(['product', 'warehouse'])
            ->get();
    }
}
