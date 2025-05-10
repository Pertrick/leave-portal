<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Holiday;
use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'type'
    ];

    protected $casts = [
        'type' => 'string'
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(Holiday::class);
    }
} 