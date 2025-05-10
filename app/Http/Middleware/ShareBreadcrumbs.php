<?php

namespace App\Http\Middleware;

use App\Services\BreadcrumbService;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ShareBreadcrumbs
{
    public function __construct(
        protected BreadcrumbService $breadcrumbService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $userRoles = $request->user()->roles->pluck('name')->toArray();
            
            Inertia::share([
                'breadcrumbs' => $this->breadcrumbService->getBreadcrumbsForPath(
                    $request->path(),
                    $userRoles
                ),
                'navigation' => $this->breadcrumbService->getNavigationForUser($userRoles),
            ]);
        }

        return $next($request);
    }
} 