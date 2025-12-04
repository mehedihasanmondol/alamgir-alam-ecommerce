<?php

namespace App\Modules\Stock\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Repositories\SupplierRepository;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $supplierRepo;

    public function __construct(SupplierRepository $supplierRepo)
    {
        $this->supplierRepo = $supplierRepo;
    }

    public function index()
    {
        $suppliers = $this->supplierRepo->getAll(20);
        return view('admin.stock.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.stock.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:suppliers,code',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'credit_limit' => 'nullable|numeric',
            'payment_terms' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $this->supplierRepo->create($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier created successfully');
    }

    public function edit($id)
    {
        $supplier = $this->supplierRepo->find($id);
        return view('admin.stock.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:suppliers,code,' . $id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'credit_limit' => 'nullable|numeric',
            'payment_terms' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $this->supplierRepo->update($id, $validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully');
    }

    public function destroy($id)
    {
        try {
            $this->supplierRepo->delete($id);
            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Supplier deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete supplier with existing records');
        }
    }
}
