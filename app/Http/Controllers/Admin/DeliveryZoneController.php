<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Delivery\Services\DeliveryService;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function __construct(
        protected DeliveryService $deliveryService
    ) {}

    /**
     * Display a listing of delivery zones.
     */
    public function index()
    {
        $zones = $this->deliveryService->getAllZones(15);
        
        return view('admin.delivery.zones.index', compact('zones'));
    }

    /**
     * Show the form for creating a new delivery zone.
     */
    public function create()
    {
        return view('admin.delivery.zones.create');
    }

    /**
     * Store a newly created delivery zone.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:delivery_zones,code',
            'description' => 'nullable|string',
            'countries' => 'nullable|array',
            'countries.*' => 'string|max:10',
            'states' => 'nullable|array',
            'states.*' => 'string|max:255',
            'cities' => 'nullable|array',
            'cities.*' => 'string|max:255',
            'postal_codes' => 'nullable|array',
            'postal_codes.*' => 'string|max:20',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $this->deliveryService->createZone($validated);

        return redirect()
            ->route('admin.delivery.zones.index')
            ->with('success', 'Delivery zone created successfully.');
    }

    /**
     * Show the form for editing the specified delivery zone.
     */
    public function edit(int $id)
    {
        $zone = $this->deliveryService->getAllZones(999999)
            ->firstWhere('id', $id);

        if (!$zone) {
            return redirect()
                ->route('admin.delivery.zones.index')
                ->with('error', 'Delivery zone not found.');
        }

        return view('admin.delivery.zones.edit', compact('zone'));
    }

    /**
     * Update the specified delivery zone.
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:delivery_zones,code,' . $id,
            'description' => 'nullable|string',
            'countries' => 'nullable|array',
            'countries.*' => 'string|max:10',
            'states' => 'nullable|array',
            'states.*' => 'string|max:255',
            'cities' => 'nullable|array',
            'cities.*' => 'string|max:255',
            'postal_codes' => 'nullable|array',
            'postal_codes.*' => 'string|max:20',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $this->deliveryService->updateZone($id, $validated);

        return redirect()
            ->route('admin.delivery.zones.index')
            ->with('success', 'Delivery zone updated successfully.');
    }

    /**
     * Remove the specified delivery zone.
     */
    public function destroy(int $id)
    {
        $this->deliveryService->deleteZone($id);

        return redirect()
            ->route('admin.delivery.zones.index')
            ->with('success', 'Delivery zone deleted successfully.');
    }

    /**
     * Toggle zone active status.
     */
    public function toggleStatus(int $id)
    {
        $zone = $this->deliveryService->getAllZones(999999)
            ->firstWhere('id', $id);

        if ($zone) {
            $this->deliveryService->updateZone($id, [
                'is_active' => !$zone->is_active
            ]);

            return response()->json([
                'success' => true,
                'is_active' => !$zone->is_active
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Zone not found'
        ], 404);
    }
}
