<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMustChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user must change password
            if ($user->must_change_password) {
                // Allow access to profile routes for changing password
                if (!$request->routeIs('profile.*') && !$request->routeIs('logout')) {
                    return redirect()->route('profile.edit')
                        ->with('warning', 'You must change your password before continuing. Please update your password below.');
                }
            }
        }
        
        return $next($request);
    }
}
