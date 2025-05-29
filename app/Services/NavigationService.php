<?php

namespace App\Services;

use App\Models\Navigation;
use Illuminate\Support\Collection;

class NavigationService
{
    public function getNavigationForUser(array $userRoles): Collection
    {
        return Navigation::whereNull('parent_id') // Get top-level navigation items
            ->where(function ($query) use ($userRoles) {
                // Filter based on roles or allow navigation if roles is null
                $query->whereNull('roles')
                    ->orWhere(function ($q) use ($userRoles) {
                        foreach ($userRoles as $role) {
                            $q->orWhereJsonContains('roles', $role);
                        }
                    });
            })
            ->where('is_active', true) // Ensure the item is active
            ->orderBy('order') // Order the navigation items
            ->with(['children' => function ($query) use ($userRoles) {
                // Apply the same role filtering to the children
                $query->where(function ($subQuery) use ($userRoles) {
                    $subQuery->whereNull('roles')
                        ->orWhere(function ($q) use ($userRoles) {
                            foreach ($userRoles as $role) {
                                $q->orWhereJsonContains('roles', $role);
                            }
                        });
                })
                ->where('is_active', true)
                ->orderBy('order'); // Order child items
            }])
            ->get(); // Retrieve the results
    }
    

    public function getCurrentPath(string $path): array
    {
        $segments = explode('/', trim($path, '/'));
        $navigation = collect();
        
        $currentPath = '';
        foreach ($segments as $segment) {
            $currentPath .= '/' . $segment;
            $item = Navigation::where('path', trim($currentPath, '/'))
                ->where('is_active', true)
                ->first();
            if ($item) {
                $navigation->push($item);
            }
        }

        return $navigation->toArray();
    }
} 