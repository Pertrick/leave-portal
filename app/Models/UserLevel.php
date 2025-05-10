<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserLevel extends Model
{
    protected $fillable = [
        'name',
        'level'
    ];

    protected $casts = [
        'level' => 'integer'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
} 