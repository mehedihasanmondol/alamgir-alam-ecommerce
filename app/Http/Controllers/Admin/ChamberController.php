<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chamber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Admin Chamber Controller
 * 
 * Manages chambers/branches for appointment system
 * 
 * @category Controllers
 * @package  Admin
 * @created  2025-11-26
 */
class ChamberController extends Controller
{
    /**
     * Display chambers list
     */
    public function index()
    {
        $chambers = Chamber::withCount('appointments')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('admin.chambers.index', compact('chambers'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.chambers.create');
    }

    /**
     * Store new chamber
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'slot_duration' => 'required|integer|min:15|max:120',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Default operating hours (Saturday-Thursday: 9AM-5PM, Friday: Closed)
        $validated['operating_hours'] = [
            'saturday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'sunday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'monday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'tuesday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'wednesday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'thursday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'friday' => ['is_open' => false],
        ];
        
        $validated['closed_days'] = ['friday'];

        Chamber::create($validated);

        return redirect()->route('admin.chambers.index')
            ->with('success', 'Chamber created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Chamber $chamber)
    {
        return view('admin.chambers.edit', compact('chamber'));
    }

    /**
     * Update chamber
     */
    public function update(Request $request, Chamber $chamber)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'slot_duration' => 'required|integer|min:15|max:120',
            'break_start' => 'nullable|integer',
            'break_duration' => 'nullable|integer',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
            'operating_hours' => 'nullable|array',
            'closed_days' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $chamber->update($validated);

        return redirect()->route('admin.chambers.index')
            ->with('success', 'Chamber updated successfully');
    }

    /**
     * Toggle chamber status
     */
    public function toggleStatus(Chamber $chamber)
    {
        $chamber->update(['is_active' => !$chamber->is_active]);

        return redirect()->back()
            ->with('success', 'Chamber status updated');
    }

    /**
     * Delete chamber
     */
    public function destroy(Chamber $chamber)
    {
        // Check if chamber has appointments
        if ($chamber->appointments()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete chamber with existing appointments');
        }

        $chamber->delete();

        return redirect()->route('admin.chambers.index')
            ->with('success', 'Chamber deleted successfully');
    }
}
