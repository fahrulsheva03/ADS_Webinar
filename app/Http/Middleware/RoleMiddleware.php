<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        $allowed = array_values(array_filter(array_map('trim', preg_split('/[|,]/', $roles) ?: [])));

        if ($allowed === [] || ! in_array((string) ($user->role ?? ''), $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}
