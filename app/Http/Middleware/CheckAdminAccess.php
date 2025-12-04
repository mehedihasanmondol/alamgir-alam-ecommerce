<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ModuleName: Authorization
 * Purpose: Middleware to check if user has admin panel access
 * 
 * Admin panel is accessible by: admin, author roles
 * Not accessible by: customer role
 * 
 * @author AI Assistant
 * @date 2025-11-17
 */
class CheckAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access admin panel.');
        }

        $user = auth()->user();

        // Check if user has admin panel access (admin or author role)
        if (in_array($user->role, ['admin', 'author'])) {
            return $next($request);
        }

        // Customer and other roles are not allowed
        abort(403, 'You do not have permission to access the admin panel.');
    }
}
