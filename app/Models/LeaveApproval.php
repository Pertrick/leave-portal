<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveApproval extends Model
{
    protected $fillable = [
        'leave_id',
        'user_id',
        'status',
        'comments',
        'approved_at',
        'rejected_at',
        'rejection_reason'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    public function leave(): BelongsTo
    {
        return $this->belongsTo(Leave::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 