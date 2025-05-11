<?php

namespace App\Http\Middleware;

use App\Services\NavigationService;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ShareNavigation
{
    public function __construct(
        protected NavigationService $navigationService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $user = $request->user();
            $currentPath = $request->path();

            // Get navigation items for the user's role
            $navigation = $this->navigationService->getNavigationForUser($user->getRoleNames()->toArray());
            
            Log::info('Navigation items', [
                'navigation' => $navigation->toArray()
            ]);

            // Get current path items for breadcrumbs
            $currentPathItems = $this->navigationService->getCurrentPath($currentPath);

            Log::info('Breadcrumb items', [
                'breadcrumbs' => $currentPathItems
            ]);

            // Share with Inertia
            Inertia::share([
                'navigation' => $navigation,
                'breadcrumbs' => $currentPathItems,
            ]);
        } else {
            Log::info('No user found in ShareNavigation middleware');
        }

        return $next($request);
    }
} 