<?php

namespace App\Modules\Stock\Repositories;

use App\Modules\Stock\Models\Warehouse;

class WarehouseRepository
{
    protected $model;

    public function __construct(Warehouse $model)
    {
        $this->model = $model;
    }

    /**
     * Get all warehouses
     */
    public function getAll($perPage = 20)
    {
        return $this->model->latest()->paginate($perPage);
    }

    /**
     * Get active warehouses
     */
    public function getActive()
    {
        return $this->model->active()->orderBy('name')->get();
    }

    /**
     * Create warehouse
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
        return $this->model->findOrFail($id);
    }

    /**
     * Update warehouse
     */
    public function update($id, array $data)
    {
        $warehouse = $this->find($id);
        $warehouse->update($data);
        return $warehouse;
    }

    /**
     * Delete warehouse
     */
    public function delete($id)
    {
        $warehouse = $this->find($id);
        return $warehouse->delete();
    }

    /**
     * Get default warehouse
     */
    public function getDefault()
    {
        return Warehouse::getDefault();
    }

    /**
     * Set as default
     */
    public function setAsDefault($id)
    {
        $warehouse = $this->find($id);
        return $warehouse->setAsDefault();
    }

    /**
     * Get stock levels for all products in warehouse
     */
    public function getStockLevels($warehouseId)
    {
        $warehouse = $this->find($warehouseId);
        
        return $warehouse->stockMovements()
            ->selectRaw('product_id, variant_id, SUM(CASE WHEN type IN ("in", "adjustment") THEN quantity ELSE -quantity END) as stock_level')
            ->groupBy('product_id', 'variant_id')
            ->with('product', 'variant')
            ->get();
    }
}
