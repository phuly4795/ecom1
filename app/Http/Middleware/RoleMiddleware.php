<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = $request->user();

        if (!$user || !$user->roles()->where('name', $role)->exists()) {
            // abort(403, 'Bạn không có quyền truy cập chức năng này.');
            return redirect(route('home'));
        }

        return $next($request);
    }
}
