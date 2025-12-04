<?php

if (!function_exists('theme')) {
    /**
     * Get theme class by key
     * 
     * @param string $key Theme class key (e.g., 'primary_bg', 'button_primary_bg')
     * @param string $default Default class if key not found
     * @return string Tailwind CSS class
     */
    function theme(string $key, string $default = ''): string
    {
        return \App\Models\ThemeSetting::getClass($key, $default);
    }
}

if (!function_exists('theme_active')) {
    /**
     * Get active theme model
     * 
     * @return \App\Models\ThemeSetting|null
     */
    function theme_active()
    {
        return \App\Models\ThemeSetting::getActive();
    }
}

if (!function_exists('theme_classes')) {
    /**
     * Get multiple theme classes at once
     * 
     * @param array $keys Array of keys to retrieve
     * @return string Space-separated classes
     */
    function theme_classes(array $keys): string
    {
        $classes = [];
        foreach ($keys as $key) {
            $class = theme($key);
            if ($class) {
                $classes[] = $class;
            }
        }
        return implode(' ', $classes);
    }
}
