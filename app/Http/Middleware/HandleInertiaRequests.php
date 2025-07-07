<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(\Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');

        // Get flash messages and clear them
        $alert = null;
        $session = $request->session();
        
        if ($session->has('success')) {
            $alert = ['type' => 'success', 'message' => $session->get('success')];
            $session->forget('success');
        } elseif ($session->has('error')) {
            $alert = ['type' => 'error', 'message' => $session->get('error')];
            $session->forget('error');
        } elseif ($session->has('warning')) {
            $alert = ['type' => 'warning', 'message' => $session->get('warning')];
            $session->forget('warning');
        } elseif ($session->has('info')) {
            $alert = ['type' => 'info', 'message' => $session->get('info')];
            $session->forget('info');
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user() ? [
                    ...$request->user()->toArray(),
                    'roles' => $request->user()->getRoleNames(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                ] : null,
            ],
            'ziggy' => [
                ...(new \Tighten\Ziggy\Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'alert' => $alert,
        ];
    }

}
