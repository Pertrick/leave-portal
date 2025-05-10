<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveEntitlement extends Model
{
    protected $fillable = [
        'user_level_id',
        'leave_type_id',
        'days_per_year',
        'can_carry_over',
        'max_carry_over_days',
        'is_active'
    ];

    protected $casts = [
        'days_per_year' => 'integer',
        'can_carry_over' => 'boolean',
        'max_carry_over_days' => 'integer',
        'is_active' => 'boolean'
    ];

    public function userLevel(): BelongsTo
    {
        return $this->belongsTo(UserLevel::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
} 