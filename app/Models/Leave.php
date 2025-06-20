<?php

declare(strict_types=1);

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'user_id',
        'leave_type_id',
        'start_date',
        'calendar_days',
        'working_days',
        'end_date',
        'total_days',
        'reason',
        'status',
        'applicant_comment',
        'replacement_staff_name',
        'replacement_staff_phone',
        'attachment',
        'current_approval_level',
        'current_approval_id',
        'is_cancelled',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'decimal:1',
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
        return $this->status === self::STATUS_PENDING;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function getStartDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    }

    public function getEndDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
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