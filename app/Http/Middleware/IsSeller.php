<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsSeller
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'seller') {
            return $next($request);
        }

        abort(403, 'Unauthorized: Only sellers can access this area.');
    }
}
