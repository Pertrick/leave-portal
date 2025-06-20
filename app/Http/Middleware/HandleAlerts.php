<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class HandleAlerts
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Debug logging
        Log::info('HandleAlerts middleware called', [
            'session_has_alert' => $request->session()->has('alert'),
            'session_has_success' => $request->session()->has('success'),
            'session_has_error' => $request->session()->has('error'),
            'session_has_warning' => $request->session()->has('warning'),
            'session_has_info' => $request->session()->has('info'),
        ]);

        // Check for custom alert format
        if ($request->session()->has('alert')) {
            $alert = $request->session()->get('alert');
            Log::info('Sharing custom alert', ['alert' => $alert]);
            Inertia::share('alert', $alert);
        }
        
        // Check for Laravel's standard flash messages
        $flashTypes = ['success', 'error', 'warning', 'info'];
        foreach ($flashTypes as $type) {
            if ($request->session()->has($type)) {
                $message = $request->session()->get($type);
                $alert = [
                    'type' => $type,
                    'message' => $message
                ];
                Log::info('Sharing flash message as alert', ['alert' => $alert]);
                Inertia::share('alert', $alert);
                break; // Only use the first flash message found
            }
        }

        return $response;
    }
} 