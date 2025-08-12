<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalanceAuditLog extends Model
{
    protected $fillable = [
        'leave_balance_id',
        'adjusted_by_id',
        'previous_balance',
        'new_balance',
        'reason',
    ];

    protected $casts = [
        'previous_balance' => 'decimal:2',
        'new_balance' => 'decimal:2',
    ];

    public function leaveBalance(): BelongsTo
    {
        return $this->belongsTo(LeaveBalance::class);
    }

    public function adjustedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by_id');
    }

    /**
     * Convert audit log to leave history format
     */
    public function toLeaveHistoryFormat(): array
    {
        $difference = $this->new_balance - $this->previous_balance;
        $isDeduction = $difference < 0;
        
        return [
            'id' => 'audit_' . $this->id,
            'type' => 'manual_adjustment',
            'title' => $isDeduction ? 'Manual Leave Deduction' : 'Manual Leave Addition',
            'leave_type' => $this->leaveBalance->leaveType->name,
            'days' => abs($difference),
            'status' => 'completed',
            'reason' => $this->reason,
            'adjusted_by' => $this->adjustedBy->firstname . ' ' . $this->adjustedBy->lastname,
            'created_at' => $this->created_at,
            'is_deduction' => $isDeduction,
            'previous_balance' => $this->previous_balance,
            'new_balance' => $this->new_balance,
        ];
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
} 