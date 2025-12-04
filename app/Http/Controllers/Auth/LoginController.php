<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * ModuleName: Authentication
 * Purpose: Handle user login and authentication
 * 
 * Key Methods:
 * - showLoginForm(): Display login page
 * - login(): Process login request
 * - logout(): Handle user logout
 * 
 * Dependencies:
 * - Laravel Auth
 * 
 * @category Authentication
 * @package  App\Http\Controllers\Auth
 * @author   Admin
 * @created  2025-01-03
 * @updated  2025-01-03
 */
class LoginController extends Controller
{
    /**
     * Show the login form
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_or_mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email_or_mobile'));
        }

        $credentials = $this->getCredentials($request->input('email_or_mobile'));
        $credentials['password'] = $request->input('password');

        $remember = $request->boolean('keep_signed_in');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Update keep_signed_in preference and last login time
            Auth::user()->update([
                'keep_signed_in' => $remember,
                'last_login_at' => now(),
            ]);

            // Redirect based on role
            $user = Auth::user();
            
            // Customer role redirects to frontend profile
            if ($user->role === 'customer') {
                return redirect()->intended(route('customer.profile'));
            }
            
            // Admin, Author, and other roles redirect to admin panel
            if (in_array($user->role, ['admin', 'author'])) {
                return redirect()->intended('/admin/dashboard');
            }

            // Default fallback to homepage
            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'email_or_mobile' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Determine if input is email or mobile
     *
     * @param string $input
     * @return array
     */
    protected function getCredentials(string $input): array
    {
        $field = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        
        return [$field => $input];
    }

    /**
     * Handle logout request
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
