<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalLevel extends Model
{
    protected $fillable = [
        'name',
        'level',
        'role_name',
        'is_active',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer'
    ];

    public function leaveApprovals(): HasMany
    {
        return $this->hasMany(LeaveApprovalLevel::class);
    }

    public function getNextLevel(): ?self
    {
        return static::where('level', '>', $this->level)
            ->where('is_active', true)
            ->orderBy('level')
            ->first();
    }

    public function getPreviousLevel(): ?self
    {
        return static::where('level', '<', $this->level)
            ->where('is_active', true)
            ->orderByDesc('level')
            ->first();
    }
} 