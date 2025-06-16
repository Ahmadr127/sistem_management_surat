<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is a staff (role = 0)
        if (Auth::check() && Auth::user()->role === 0) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}
