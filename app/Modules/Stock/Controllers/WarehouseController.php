<?php

namespace App\Modules\Stock\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Repositories\WarehouseRepository;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    protected $warehouseRepo;

    public function __construct(WarehouseRepository $warehouseRepo)
    {
        $this->warehouseRepo = $warehouseRepo;
    }

    public function index()
    {
        $warehouses = $this->warehouseRepo->getAll(20);
        return view('admin.stock.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('admin.stock.warehouses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:warehouses,code',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'manager_name' => 'nullable|string',
            'is_active' => 'boolean',
            'capacity' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $this->warehouseRepo->create($validated);

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse created successfully');
    }

    public function edit($id)
    {
        $warehouse = $this->warehouseRepo->find($id);
        return view('admin.stock.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:warehouses,code,' . $id,
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'manager_name' => 'nullable|string',
            'is_active' => 'boolean',
            'capacity' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $this->warehouseRepo->update($id, $validated);

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse updated successfully');
    }

    public function destroy($id)
    {
        try {
            $this->warehouseRepo->delete($id);
            return redirect()->route('admin.warehouses.index')
                ->with('success', 'Warehouse deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete warehouse with existing stock movements');
        }
    }

    public function setDefault($id)
    {
        $this->warehouseRepo->setAsDefault($id);
        return back()->with('success', 'Default warehouse set successfully');
    }
}
