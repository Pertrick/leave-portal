<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Navigation extends Model
{
    protected $fillable = [
        'path',
        'title',
        'parent_id',
        'order',
        'icon',
        'roles',
        'is_active'
    ];

    protected $casts = [
        'roles' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForRoles($query, array $roles)
    {
        return $query->where(function ($q) use ($roles) {
            $q->whereNull('roles')
              ->orWhereJsonContains('roles', $roles);
        });
    }

    public function getFullPath(): string
    {
        if ($this->parent) {
            return $this->parent->getFullPath() . '/' . $this->path;
        }
        return '/' . $this->path;
    }
} 