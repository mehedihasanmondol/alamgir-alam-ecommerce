<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

/**
 * ModuleName: Social Authentication
 * Purpose: Handle social login (Google, Facebook, Apple)
 * 
 * Key Methods:
 * - redirectToProvider(): Redirect to social provider
 * - handleProviderCallback(): Handle callback from social provider
 * - findOrCreateUser(): Find existing user or create new one
 * 
 * Dependencies:
 * - Laravel Socialite
 * - User Model
 * 
 * @category Authentication
 * @package  App\Http\Controllers\Auth
 * @author   Admin
 * @created  2025-11-19
 */
class SocialLoginController extends Controller
{
    /**
     * Redirect to social provider
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider(string $provider)
    {
        // Validate provider
        if (!in_array($provider, ['google', 'facebook', 'apple'])) {
            return redirect()->route('login')->with('error', 'Invalid social login provider.');
        }

        // Check if provider is enabled
        $enabledSetting = \App\Models\SiteSetting::get("enable_{$provider}_login", '0');
        if ($enabledSetting !== '1') {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' login is currently disabled.');
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Social login is not configured properly.');
        }
    }

    /**
     * Handle callback from social provider
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback(string $provider)
    {
        // Validate provider
        if (!in_array($provider, ['google', 'facebook', 'apple'])) {
            return redirect()->route('login')->with('error', 'Invalid social login provider.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to authenticate with ' . ucfirst($provider) . '. Please try again.');
        }

        // Find or create user
        $user = $this->findOrCreateUser($socialUser, $provider);

        // Login the user
        Auth::login($user, true);

        // Update last login time
        $user->update([
            'last_login_at' => now(),
        ]);

        // Redirect based on role
        if ($user->role === 'customer') {
            return redirect()->intended(route('customer.profile'));
        }

        if (in_array($user->role, ['admin', 'author'])) {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/');
    }

    /**
     * Find existing user or create new one
     *
     * @param \Laravel\Socialite\Contracts\User $socialUser
     * @param string $provider
     * @return \App\Models\User
     */
    protected function findOrCreateUser($socialUser, string $provider): User
    {
        $providerIdField = $provider . '_id';

        // Check if user exists with this provider ID
        $user = User::where($providerIdField, $socialUser->getId())->first();

        if ($user) {
            return $user;
        }

        // Check if user exists with this email
        if ($socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Link social account to existing user
                $user->update([
                    $providerIdField => $socialUser->getId(),
                ]);

                return $user;
            }
        }

        // Download and save avatar locally
        $avatarPath = null;
        if ($socialUser->getAvatar()) {
            $avatarPath = $this->downloadAndSaveAvatar($socialUser->getAvatar(), $provider, $socialUser->getId());
        }

        // Create new user with customer role by default
        return User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
            'email' => $socialUser->getEmail(),
            'mobile' => null,
            $providerIdField => $socialUser->getId(),
            'password' => Hash::make(Str::random(32)), // Random password for social users
            'role' => 'customer', // Default role is customer
            'email_verified_at' => now(), // Auto-verify email for social login
            'is_active' => true,
            'avatar' => $avatarPath,
        ]);
    }

    /**
     * Download avatar from URL and save locally
     *
     * @param string $avatarUrl
     * @param string $provider
     * @param string $socialId
     * @return string|null
     */
    protected function downloadAndSaveAvatar(string $avatarUrl, string $provider, string $socialId): ?string
    {
        try {
            // Get image content
            $imageContent = file_get_contents($avatarUrl);
            
            if ($imageContent === false) {
                return null;
            }

            // Generate unique filename
            $extension = pathinfo(parse_url($avatarUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $provider . '_' . $socialId . '_' . time() . '.' . $extension;
            $path = 'avatars/' . $filename;

            // Save to storage
            Storage::disk('public')->put($path, $imageContent);

            return $path;
        } catch (\Exception $e) {
            // Log error but don't fail the registration
            \Log::warning('Failed to download social avatar: ' . $e->getMessage());
            return null;
        }
    }
}
