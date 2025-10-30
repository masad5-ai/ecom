<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_FORBIDDEN, 'Authentication required.');
        }

        $roles = collect($roles)->filter()->map(fn ($role) => strtolower($role));

        if ($roles->isEmpty()) {
            return $next($request);
        }

        if ($user->isAdmin() || $roles->contains($user->role)) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN, 'You do not have permission to access this resource.');
    }
}
