<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use App\Models\SiteSetting;

/**
 * Social Authentication Service Provider
 * 
 * Dynamically configures Socialite providers using database settings
 * Falls back to .env values if database settings are empty
 */
class SocialAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure Socialite providers after app is booted
        $this->app->booted(function () {
            $this->configureSocialite();
        });
    }

    /**
     * Configure Socialite providers with database settings
     */
    protected function configureSocialite(): void
    {
        try {
            $socialite = $this->app->make(SocialiteFactory::class);

            // Configure Google
            $googleClientId = SiteSetting::get('google_client_id') ?: config('services.google.client_id');
            $googleClientSecret = SiteSetting::get('google_client_secret') ?: config('services.google.client_secret');
            $googleRedirect = SiteSetting::get('google_redirect_url') ?: config('services.google.redirect');

            if ($googleClientId && $googleClientSecret) {
                config([
                    'services.google.client_id' => $googleClientId,
                    'services.google.client_secret' => $googleClientSecret,
                    'services.google.redirect' => $googleRedirect ?: (config('app.url') . '/login/google/callback'),
                ]);
            }

            // Configure Facebook
            $facebookClientId = SiteSetting::get('facebook_client_id') ?: config('services.facebook.client_id');
            $facebookClientSecret = SiteSetting::get('facebook_client_secret') ?: config('services.facebook.client_secret');
            $facebookRedirect = SiteSetting::get('facebook_redirect_url') ?: config('services.facebook.redirect');

            if ($facebookClientId && $facebookClientSecret) {
                config([
                    'services.facebook.client_id' => $facebookClientId,
                    'services.facebook.client_secret' => $facebookClientSecret,
                    'services.facebook.redirect' => $facebookRedirect ?: (config('app.url') . '/login/facebook/callback'),
                ]);
            }

            // Configure Apple
            $appleClientId = SiteSetting::get('apple_client_id') ?: config('services.apple.client_id');
            $appleClientSecret = SiteSetting::get('apple_client_secret') ?: config('services.apple.client_secret');
            $appleRedirect = SiteSetting::get('apple_redirect_url') ?: config('services.apple.redirect');

            if ($appleClientId && $appleClientSecret) {
                config([
                    'services.apple.client_id' => $appleClientId,
                    'services.apple.client_secret' => $appleClientSecret,
                    'services.apple.redirect' => $appleRedirect ?: (config('app.url') . '/login/apple/callback'),
                ]);
            }
        } catch (\Exception $e) {
            // Silently fail during migrations or when database is not ready
            \Log::debug('SocialAuthServiceProvider: Could not configure socialite - ' . $e->getMessage());
        }
    }
}
