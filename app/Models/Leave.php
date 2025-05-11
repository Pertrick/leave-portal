<?php

declare(strict_types=1);

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'user_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'calendar_days',
        'working_days',
        'reason',
        'applicant_comment',
        'replacement_staff_name',
        'replacement_staff_phone',
        'attachment',
        'current_approval_level',
        'current_approval_id',
        'status',
        'is_cancelled'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'calendar_days' => 'integer',
        'working_days' => 'integer',
        'is_cancelled' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(LeaveApproval::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }



    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($leave) {
            $leave->uuid = (string) Uuid::uuid4();
        });
    }

    public function getRouteKeyName()
{
    return 'uuid';
}
} 