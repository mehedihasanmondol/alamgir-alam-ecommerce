<?php

namespace App\Modules\Blog\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Repositories\TickMarkRepository;
use App\Modules\Blog\Requests\TickMarkRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ModuleName: Blog
 * Purpose: Controller for managing tick marks
 * 
 * @category Blog
 * @package  App\Modules\Blog\Controllers\Admin
 * @author   AI Assistant
 * @created  2025-11-10
 */
class TickMarkController extends Controller
{
    protected TickMarkRepository $tickMarkRepository;

    public function __construct(TickMarkRepository $tickMarkRepository)
    {
        $this->tickMarkRepository = $tickMarkRepository;
    }

    /**
     * Display a listing of tick marks
     */
    public function index(): View
    {
        $tickMarks = $this->tickMarkRepository->getWithPostCount();
        
        return view('admin.blog.tick-marks.index', compact('tickMarks'));
    }

    /**
     * Show the form for creating a new tick mark
     */
    public function create(): View
    {
        return view('admin.blog.tick-marks.create');
    }

    /**
     * Store a newly created tick mark
     */
    public function store(TickMarkRequest $request): RedirectResponse
    {
        try {
            $this->tickMarkRepository->create($request->validated());
            
            return redirect()
                ->route('admin.blog.tick-marks.index')
                ->with('success', 'Tick mark created successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create tick mark: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified tick mark
     */
    public function edit(int $id): View
    {
        $tickMark = $this->tickMarkRepository->find($id);
        
        if (!$tickMark) {
            abort(404, 'Tick mark not found');
        }
        
        return view('admin.blog.tick-marks.edit', compact('tickMark'));
    }

    /**
     * Update the specified tick mark
     */
    public function update(TickMarkRequest $request, int $id): RedirectResponse
    {
        try {
            $tickMark = $this->tickMarkRepository->find($id);
            
            if (!$tickMark) {
                return back()->with('error', 'Tick mark not found');
            }
            
            if ($tickMark->is_system) {
                return back()->with('error', 'System tick marks cannot be modified');
            }
            
            $this->tickMarkRepository->update($id, $request->validated());
            
            return redirect()
                ->route('admin.blog.tick-marks.index')
                ->with('success', 'Tick mark updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update tick mark: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified tick mark
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $tickMark = $this->tickMarkRepository->find($id);
            
            if (!$tickMark) {
                return back()->with('error', 'Tick mark not found');
            }
            
            if ($tickMark->is_system) {
                return back()->with('error', 'System tick marks cannot be deleted');
            }
            
            $this->tickMarkRepository->delete($id);
            
            return redirect()
                ->route('admin.blog.tick-marks.index')
                ->with('success', 'Tick mark deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete tick mark: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive(int $id): RedirectResponse
    {
        try {
            $this->tickMarkRepository->toggleActive($id);
            
            return back()->with('success', 'Tick mark status updated!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(Request $request): RedirectResponse
    {
        try {
            $order = $request->input('order', []);
            $this->tickMarkRepository->updateSortOrder($order);
            
            return back()->with('success', 'Sort order updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update sort order: ' . $e->getMessage());
        }
    }
}
