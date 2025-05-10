<?php

declare(strict_types=1);

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'description'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id')
            ->where('model_type', User::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->name));
    }

    public function getPermissionsListAttribute(): array
    {
        return $this->permissions->pluck('name')->toArray();
    }
} 