<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'total_entitled_days',
        'days_taken',
        'days_remaining',
        'days_carried_forward'
    ];

    protected $casts = [
        'year' => 'integer',
        'total_entitled_days' => 'integer',
        'days_taken' => 'integer',
        'days_remaining' => 'integer',
        'days_carried_forward' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
} 