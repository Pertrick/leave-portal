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
        'level',
        'max_leave_days'
    ];

    protected $casts = [
        'level' => 'integer',
        'max_leave_days' => 'integer'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
} 