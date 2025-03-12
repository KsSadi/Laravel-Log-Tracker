<?php

namespace Kssadi\LogTracker\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeLogTracker
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized access to Log Tracker');
        }

        return $next($request);
    }
}
