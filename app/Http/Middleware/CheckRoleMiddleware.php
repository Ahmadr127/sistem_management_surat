<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!in_array($request->user()->role, $roles)) {
            return redirect('/dashboard')->with('error', 'Unauthorized access');
        }
        
        return $next($request);
    }
}
