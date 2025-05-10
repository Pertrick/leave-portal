<?php

namespace App\Services;

use App\Models\Breadcrumb;
use Illuminate\Support\Collection;

class BreadcrumbService
{
    public function getBreadcrumbsForPath(string $path, array $userRoles): Collection
    {
        $pathParts = collect(explode('/', trim($path, '/')));
        $breadcrumbs = collect();
        $currentPath = '';

        foreach ($pathParts as $part) {
            $currentPath .= '/' . $part;
            
            $breadcrumb = Breadcrumb::where('path', $currentPath)
                ->active()
                ->forRoles($userRoles)
                ->first();

            if ($breadcrumb) {
                $breadcrumbs->push([
                    'title' => $breadcrumb->title,
                    'href' => $breadcrumb->getFullPath(),
                    'icon' => $breadcrumb->icon,
                ]);
            }
        }

        return $breadcrumbs;
    }

    public function getNavigationForUser(array $userRoles): Collection
    {
        return Breadcrumb::whereNull('parent_id')
            ->active()
            ->forRoles($userRoles)
            ->with(['children' => function ($query) use ($userRoles) {
                $query->active()->forRoles($userRoles);
            }])
            ->orderBy('order')
            ->get()
            ->map(function ($item) {
                return [
                    'title' => $item->title,
                    'path' => $item->getFullPath(),
                    'icon' => $item->icon,
                    'children' => $item->children->map(function ($child) {
                        return [
                            'title' => $child->title,
                            'path' => $child->getFullPath(),
                            'icon' => $child->icon,
                        ];
                    }),
                ];
            });
    }
} 