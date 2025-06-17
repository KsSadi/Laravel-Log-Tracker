<?php

namespace Kssadi\LogTracker\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeLogExport
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('log-tracker.export.enabled', true)) {
            abort(403, 'Export functionality is disabled.');
        }

        if (!auth()->check()) {
            abort(403, 'Authentication required for log export.');
        }

        return $next($request);
    }
}
