<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    /**
     * Display system settings page
     */
    public function index()
    {
        // System settings sections
        $sections = [
            'cache' => [
                'title' => 'Cache Management',
                'description' => 'Clear application, route, config, and view caches',
                'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                'component' => 'cache-management',
            ],
            'artisan' => [
                'title' => 'Artisan Commands',
                'description' => 'Execute Laravel artisan commands',
                'icon' => 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'component' => 'artisan-command-runner',
            ],
            'maintenance' => [
                'title' => 'Construction Mode',
                'description' => 'Enable/disable site maintenance mode',
                'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                'component' => 'construction-mode',
            ],
        ];

        return view('admin.system-settings.index', compact('sections'));
    }
}
