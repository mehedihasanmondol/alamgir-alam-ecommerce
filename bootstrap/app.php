<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
            
            Route::middleware('web')
                ->group(base_path('routes/blog.php'));
        },
    )
    ->withSchedule(function ($schedule) {
        // Newsletter: Every Monday at 9 AM
        $schedule->command('email:send-newsletter')
            ->weeklyOn(1, '9:00')
            ->timezone('Asia/Dhaka')
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Newsletter emails sent successfully');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Newsletter emails failed');
            });

        // Promotions: Every Friday at 10 AM
        $schedule->command('email:send-promotions')
            ->weeklyOn(5, '10:00')
            ->timezone('Asia/Dhaka')
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Promotional emails sent successfully');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Promotional emails failed');
            });

        // Recommendations: Every Wednesday at 2 PM
        $schedule->command('email:send-recommendations')
            ->weekly()
            ->wednesdays()
            ->at('14:00')
            ->timezone('Asia/Dhaka')
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Recommendation emails sent successfully');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Recommendation emails failed');
            });
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Register global web middleware
        $middleware->web(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);
        
        // Register middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'user.active' => \App\Http\Middleware\CheckUserActive::class,
            'admin.access' => \App\Http\Middleware\CheckAdminAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
