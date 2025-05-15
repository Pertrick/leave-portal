<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\LeaveEntitlement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'requires_medical_proof',
        'weekend_days'
    ];

    protected $casts = [
        'requires_medical_proof' => 'boolean',
        'weekend_days' => 'array'
    ];

    /**
     * Check if a specific day of the week is considered a weekend day
     */
    public function isWeekendDay(int $dayOfWeek): bool
    {
        $weekendDays = $this->weekend_days;
        
        return match($dayOfWeek) {
            6 => $weekendDays['saturday'] ?? false, // Saturday
            7 => $weekendDays['sunday'] ?? false,   // Sunday
            default => false
        };
    }

    /**
     * Get all weekend days as an array of day numbers
     */
    public function getWeekendDaysArray(): array
    {
        $weekendDays = [];
        if ($this->weekend_days['saturday'] ?? false) {
            $weekendDays[] = 6;
        }
        if ($this->weekend_days['sunday'] ?? false) {
            $weekendDays[] = 7;
        }
        return $weekendDays;
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function leaveEntitlements(): HasMany
    {
        return $this->hasMany(LeaveEntitlement::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }
} 