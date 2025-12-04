<?php

namespace App\Livewire\Admin\SystemSettings;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CacheManagement extends Component
{
    public $loading = false;
    public $successMessage = '';

    /**
     * Clear application cache
     */
    public function clearAppCache()
    {
        try {
            Artisan::call('cache:clear');
            $this->successMessage = 'Application cache cleared successfully!';
            $this->dispatch('cache-cleared', type: 'application');
        } catch (\Exception $e) {
            $this->dispatch('cache-error', message: $e->getMessage());
        }
    }

    /**
     * Clear route cache
     */
    public function clearRouteCache()
    {
        try {
            Artisan::call('route:clear');
            $this->successMessage = 'Route cache cleared successfully!';
            $this->dispatch('cache-cleared', type: 'route');
        } catch (\Exception $e) {
            $this->dispatch('cache-error', message: $e->getMessage());
        }
    }

    /**
     * Clear config cache
     */
    public function clearConfigCache()
    {
        try {
            Artisan::call('config:clear');
            $this->successMessage = 'Configuration cache cleared successfully!';
            $this->dispatch('cache-cleared', type: 'config');
        } catch (\Exception $e) {
            $this->dispatch('cache-error', message: $e->getMessage());
        }
    }

    /**
     * Clear view cache
     */
    public function clearViewCache()
    {
        try {
            Artisan::call('view:clear');
            $this->successMessage = 'View cache cleared successfully!';
            $this->dispatch('cache-cleared', type: 'view');
        } catch (\Exception $e) {
            $this->dispatch('cache-error', message: $e->getMessage());
        }
    }

    /**
     * Clear all caches
     */
    public function clearAllCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('optimize:clear');
            
            $this->successMessage = 'All caches cleared successfully!';
            $this->dispatch('cache-cleared', type: 'all');
        } catch (\Exception $e) {
            $this->dispatch('cache-error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.system-settings.cache-management');
    }
}
