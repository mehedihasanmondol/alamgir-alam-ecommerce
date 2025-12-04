<?php

namespace App\Modules\Stock\Repositories;

use App\Modules\Stock\Models\Supplier;

class SupplierRepository
{
    protected $model;

    public function __construct(Supplier $model)
    {
        $this->model = $model;
    }

    /**
     * Get all suppliers
     */
    public function getAll($perPage = 20)
    {
        return $this->model->latest()->paginate($perPage);
    }

    /**
     * Get active suppliers
     */
    public function getActive()
    {
        return $this->model->active()->orderBy('name')->get();
    }

    /**
     * Create supplier
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
     * Update supplier
     */
    public function update($id, array $data)
    {
        $supplier = $this->find($id);
        $supplier->update($data);
        return $supplier;
    }

    /**
     * Delete supplier
     */
    public function delete($id)
    {
        $supplier = $this->find($id);
        return $supplier->delete();
    }

    /**
     * Search suppliers
     */
    public function search($term)
    {
        return $this->model->where('name', 'like', "%{$term}%")
            ->orWhere('code', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->active()
            ->get();
    }
}
