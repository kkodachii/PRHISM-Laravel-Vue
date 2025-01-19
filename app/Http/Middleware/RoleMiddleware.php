<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            // Redirect with a toast message
            return redirect()->back()->with('toast', [
                'message' => 'You must be logged in to access this page.',
                'type' => 'error'
            ]);
        }

        $userRole = Auth::user()->role->name;
        Log::info('User Role:', ['role' => $userRole]);
        /** 
        *if (!in_array($userRole, $roles)) {
         *   Log::warning('Unauthorized Access Attempt:', ['role' => $userRole, 'required_roles' => $roles]);

          *  // Redirect with a toast message
           * return redirect()->back()->with('toast', [
            *    'message' => 'You are not authorized to access this page.',
             *   'type' => 'error'
            *]);
        *}
        */
        return $next($request);
    }
}
