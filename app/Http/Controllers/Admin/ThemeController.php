<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use App\Services\ThemeService;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * Display theme settings page
     */
    public function index()
    {
        $themes = $this->themeService->getAllThemes();
        $activeTheme = $this->themeService->getActiveTheme();
        $categories = $this->themeService->getThemeCategories();

        return view('admin.theme-settings.index', compact('themes', 'activeTheme', 'categories'));
    }

    /**
     * Activate a theme
     */
    public function activate(Request $request, $id)
    {
        $success = $this->themeService->activateTheme($id);

        if ($success) {
            return redirect()
                ->route('admin.theme-settings.index')
                ->with('success', 'Theme activated successfully!');
        }

        return redirect()
            ->route('admin.theme-settings.index')
            ->with('error', 'Failed to activate theme.');
    }

    /**
     * Show edit form for a theme
     */
    public function edit($id)
    {
        $theme = ThemeSetting::findOrFail($id);
        $categories = $this->themeService->getThemeCategories();

        return view('admin.theme-settings.edit', compact('theme', 'categories'));
    }

    /**
     * Update theme colors
     */
    public function update(Request $request, $id)
    {
        $theme = ThemeSetting::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'primary_bg' => 'required|string|max:255',
            'primary_bg_hover' => 'required|string|max:255',
            'primary_text' => 'required|string|max:255',
            'primary_border' => 'required|string|max:255',
            'secondary_bg' => 'required|string|max:255',
            'secondary_bg_hover' => 'required|string|max:255',
            'secondary_text' => 'required|string|max:255',
            'secondary_border' => 'required|string|max:255',
            'success_bg' => 'required|string|max:255',
            'success_text' => 'required|string|max:255',
            'danger_bg' => 'required|string|max:255',
            'danger_text' => 'required|string|max:255',
            'warning_bg' => 'required|string|max:255',
            'warning_text' => 'required|string|max:255',
            'info_bg' => 'required|string|max:255',
            'info_text' => 'required|string|max:255',
            'button_primary_bg' => 'required|string|max:255',
            'button_primary_bg_hover' => 'required|string|max:255',
            'button_primary_text' => 'required|string|max:255',
            'button_secondary_bg' => 'required|string|max:255',
            'button_secondary_text' => 'required|string|max:255',
            'card_bg' => 'required|string|max:255',
            'card_text' => 'required|string|max:255',
            'card_border' => 'required|string|max:255',
            'sidebar_bg' => 'required|string|max:255',
            'sidebar_text' => 'required|string|max:255',
            'sidebar_active_bg' => 'required|string|max:255',
            'sidebar_active_text' => 'required|string|max:255',
            'sidebar_hover_bg' => 'required|string|max:255',
            'header_bg' => 'required|string|max:255',
            'header_text' => 'required|string|max:255',
            'header_border' => 'required|string|max:255',
            'link_text' => 'required|string|max:255',
            'link_hover_text' => 'required|string|max:255',
            'badge_primary_bg' => 'required|string|max:255',
            'badge_primary_text' => 'required|string|max:255',
            'badge_success_bg' => 'required|string|max:255',
            'badge_success_text' => 'required|string|max:255',
            'badge_danger_bg' => 'required|string|max:255',
            'badge_danger_text' => 'required|string|max:255',
            'input_bg' => 'required|string|max:255',
            'input_text' => 'required|string|max:255',
            'input_border' => 'required|string|max:255',
            'input_focus_border' => 'required|string|max:255',
            'input_focus_ring' => 'required|string|max:255',
            'table_header_bg' => 'required|string|max:255',
            'table_header_text' => 'required|string|max:255',
            'table_row_hover' => 'required|string|max:255',
            'table_border' => 'required|string|max:255',
        ]);

        $theme->update($validated);

        // Clear cache if this is the active theme
        if ($theme->is_active) {
            \Cache::forget('active_theme');
        }

        return redirect()
            ->route('admin.theme-settings.index')
            ->with('success', 'Theme updated successfully!');
    }

    /**
     * Duplicate a theme
     */
    public function duplicate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:theme_settings,name',
            'label' => 'required|string|max:255',
        ]);

        $this->themeService->duplicateTheme($id, $request->name, $request->label);

        return redirect()
            ->route('admin.theme-settings.index')
            ->with('success', 'Theme duplicated successfully!');
    }

    /**
     * Delete a custom theme
     */
    public function destroy($id)
    {
        $success = $this->themeService->deleteTheme($id);

        if ($success) {
            return redirect()
                ->route('admin.theme-settings.index')
                ->with('success', 'Theme deleted successfully!');
        }

        return redirect()
            ->route('admin.theme-settings.index')
            ->with('error', 'Cannot delete predefined or active themes.');
    }

    /**
     * Reset predefined theme to defaults
     */
    public function reset($id)
    {
        $success = $this->themeService->resetToDefault($id);

        if ($success) {
            return redirect()
                ->route('admin.theme-settings.index')
                ->with('success', 'Theme reset to default values!');
        }

        return redirect()
            ->route('admin.theme-settings.index')
            ->with('error', 'Can only reset predefined themes.');
    }
}
