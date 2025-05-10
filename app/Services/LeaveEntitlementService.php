<?php

namespace App\Services;

use App\Models\LeaveEntitlement;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\LeaveType;

class LeaveEntitlementService
{
    public function getEntitlement(User $user, LeaveType $leaveType): ?LeaveEntitlement
    {
        return LeaveEntitlement::where('user_level_id', $user->user_level_id)
            ->where('leave_type_id', $leaveType->id)
            ->where('is_active', true)
            ->first();
    }

    public function getDaysPerYear(User $user, LeaveType $leaveType): int
    {
        $entitlement = $this->getEntitlement($user, $leaveType);
        return $entitlement ? $entitlement->days_per_year : 0;
    }

    public function canCarryOver(User $user, LeaveType $leaveType): bool
    {
        $entitlement = $this->getEntitlement($user, $leaveType);
        return $entitlement ? $entitlement->can_carry_over : false;
    }

    public function getMaxCarryOverDays(User $user, LeaveType $leaveType): int
    {
        $entitlement = $this->getEntitlement($user, $leaveType);
        return $entitlement ? $entitlement->max_carry_over_days : 0;
    }

} 