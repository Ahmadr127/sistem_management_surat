<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceSanctumAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for Mobile Bearer Token
        if (auth('api')->check()) {
            auth()->shouldUse('api');
            return $next($request);
        }

        // Check for Web Session (Ajax from Alpine/Vue)
        if (auth('web')->check()) {
            auth()->shouldUse('web');
            return $next($request);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated.'
        ], 401);
    }
}
