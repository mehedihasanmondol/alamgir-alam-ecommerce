<?php

namespace App\Services;

use App\Models\ThemeSetting;
use Illuminate\Support\Facades\Cache;

class ThemeService
{
    /**
     * Get theme class by key
     */
    public function getClass(string $key, string $default = ''): string
    {
        return ThemeSetting::getClass($key, $default);
    }

    /**
     * Get active theme
     */
    public function getActiveTheme(): ?ThemeSetting
    {
        return ThemeSetting::getActive();
    }

    /**
     * Get all themes
     */
    public function getAllThemes()
    {
        return ThemeSetting::orderBy('is_predefined', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get predefined themes only
     */
    public function getPredefinedThemes()
    {
        return ThemeSetting::where('is_predefined', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Activate a theme
     */
    public function activateTheme(int $themeId): bool
    {
        $theme = ThemeSetting::find($themeId);
        
        if (!$theme) {
            return false;
        }
        
        return $theme->activate();
    }

    /**
     * Create or update theme
     */
    public function saveTheme(array $data): ThemeSetting
    {
        if (isset($data['id'])) {
            $theme = ThemeSetting::findOrFail($data['id']);
            $theme->update($data);
        } else {
            $theme = ThemeSetting::create($data);
        }
        
        // If this theme is being set as active
        if (isset($data['is_active']) && $data['is_active']) {
            $theme->activate();
        }
        
        return $theme;
    }

    /**
     * Delete a custom theme
     */
    public function deleteTheme(int $themeId): bool
    {
        $theme = ThemeSetting::find($themeId);
        
        if (!$theme || $theme->is_predefined || $theme->is_active) {
            return false;
        }
        
        return $theme->delete();
    }

    /**
     * Duplicate a theme
     */
    public function duplicateTheme(int $themeId, string $newName, string $newLabel): ThemeSetting
    {
        $original = ThemeSetting::findOrFail($themeId);
        
        $duplicate = $original->replicate();
        $duplicate->name = $newName;
        $duplicate->label = $newLabel;
        $duplicate->is_active = false;
        $duplicate->is_predefined = false;
        $duplicate->save();
        
        return $duplicate;
    }

    /**
     * Reset theme to default values
     */
    public function resetToDefault(int $themeId): bool
    {
        $theme = ThemeSetting::find($themeId);
        
        if (!$theme || !$theme->is_predefined) {
            return false;
        }
        
        // Get fresh defaults from seeder logic
        $defaults = $this->getThemeDefaults($theme->name);
        
        if ($defaults) {
            $theme->update($defaults);
            Cache::forget('active_theme');
            return true;
        }
        
        return false;
    }

    /**
     * Get theme class categories for admin UI
     */
    public function getThemeCategories(): array
    {
        return [
            'Primary Colors' => [
                'primary_bg' => 'Primary Background',
                'primary_bg_hover' => 'Primary Background Hover',
                'primary_text' => 'Primary Text',
                'primary_border' => 'Primary Border',
            ],
            'Secondary Colors' => [
                'secondary_bg' => 'Secondary Background',
                'secondary_bg_hover' => 'Secondary Background Hover',
                'secondary_text' => 'Secondary Text',
                'secondary_border' => 'Secondary Border',
            ],
            'Status Colors' => [
                'success_bg' => 'Success Background',
                'success_text' => 'Success Text',
                'danger_bg' => 'Danger Background',
                'danger_text' => 'Danger Text',
                'warning_bg' => 'Warning Background',
                'warning_text' => 'Warning Text',
                'info_bg' => 'Info Background',
                'info_text' => 'Info Text',
            ],
            'Button Colors' => [
                'button_primary_bg' => 'Primary Button Background',
                'button_primary_bg_hover' => 'Primary Button Hover',
                'button_primary_text' => 'Primary Button Text',
                'button_secondary_bg' => 'Secondary Button Background',
                'button_secondary_text' => 'Secondary Button Text',
            ],
            'Card & Container' => [
                'card_bg' => 'Card Background',
                'card_text' => 'Card Text',
                'card_border' => 'Card Border',
                'card_shadow' => 'Card Shadow',
            ],
            'Sidebar (Admin)' => [
                'sidebar_bg' => 'Sidebar Background',
                'sidebar_text' => 'Sidebar Text',
                'sidebar_active_bg' => 'Sidebar Active Background',
                'sidebar_active_text' => 'Sidebar Active Text',
                'sidebar_hover_bg' => 'Sidebar Hover',
            ],
            'Header' => [
                'header_bg' => 'Header Background',
                'header_text' => 'Header Text',
                'header_border' => 'Header Border',
            ],
            'Links' => [
                'link_text' => 'Link Text',
                'link_hover_text' => 'Link Hover Text',
            ],
            'Badges' => [
                'badge_primary_bg' => 'Primary Badge Background',
                'badge_primary_text' => 'Primary Badge Text',
                'badge_success_bg' => 'Success Badge Background',
                'badge_success_text' => 'Success Badge Text',
                'badge_danger_bg' => 'Danger Badge Background',
                'badge_danger_text' => 'Danger Badge Text',
            ],
            'Forms & Inputs' => [
                'input_bg' => 'Input Background',
                'input_text' => 'Input Text',
                'input_border' => 'Input Border',
                'input_focus_border' => 'Input Focus Border',
                'input_focus_ring' => 'Input Focus Ring',
            ],
            'Tables' => [
                'table_header_bg' => 'Table Header Background',
                'table_header_text' => 'Table Header Text',
                'table_row_hover' => 'Table Row Hover',
                'table_border' => 'Table Border',
            ],
        ];
    }

    /**
     * Get default theme values
     */
    private function getThemeDefaults(string $themeName): ?array
    {
        $defaults = [
            'default' => ['primary_bg' => 'bg-blue-600'],
            'green' => ['primary_bg' => 'bg-green-600'],
            'red' => ['primary_bg' => 'bg-red-600'],
            'purple' => ['primary_bg' => 'bg-purple-600'],
            'dark' => ['primary_bg' => 'bg-gray-800'],
            'indigo' => ['primary_bg' => 'bg-indigo-600'],
        ];
        
        return $defaults[$themeName] ?? null;
    }
}
