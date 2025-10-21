<?php

namespace App\Http\Middleware;

use Closure;

class AllowWebSocketsDashboard
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
