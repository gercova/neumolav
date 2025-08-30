<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next) {
        $response = $next($request);
        
        if(Auth::check()) {
            $module = $request->route()->getAction('as') ?? $request->path();
            AuditLog::create([
                'action'    => 'access',
                'module'    => $module,
                'user_id'   => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }
}
