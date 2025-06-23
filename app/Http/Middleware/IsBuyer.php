<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsBuyer
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'buyer') {
            return $next($request);
        }

        abort(403, 'Unauthorized: Only buyers can access this area.');
    }
}
