<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /*public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }*/
    public function handle($request, Closure $next, ...$permissions) {
        if (!Auth::check()) {
            abort(403, 'No autenticado.');
        }

        // Support both comma-separated and pipe-separated permissions (OR logic)
        $perms = [];
        foreach ($permissions as $p) {
            foreach (explode('|', $p) as $subP) {
                $trimmed = trim($subP);
                if ($trimmed !== '') {
                    $perms[] = $trimmed;
                }
            }
        }

        $user = Auth::user();
        foreach ($perms as $permission) {
            if ($user->can($permission)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permiso para realizar esta acción.');
    }
}
