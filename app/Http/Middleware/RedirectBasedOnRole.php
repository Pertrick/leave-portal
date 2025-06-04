<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }

        if ($request->is('admin/*') || $request->is('admin')) {
            if (!$request->user()->hasAnyRole(['admin', 'hr'])) {
                return redirect()->route('dashboard');
            }
        }

        if ($request->is('dashboard') && $request->user()->hasAnyRole(['admin', 'hr'])) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
} 