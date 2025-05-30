<?php

namespace App\Services;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveEntitlement;
use Illuminate\Support\Facades\DB;

class LeaveBalanceService
{
    /**
     * Initialize leave balances for a new user
     */
    public function initializeLeaveBalances(User $user): void
    {
        // Get all active leave types
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        // Calculate entitled days based on join date
        $joinDate = $user->join_date;
        $currentYear = now()->year;
        $joinYear = $joinDate->year;
        $joinMonth = $joinDate->month;

        // Check if proration is enabled and if joined after the half-year month
        $isProrated = config('leave.proration.enabled') && 
            $joinYear === $currentYear && 
            $joinMonth > config('leave.proration.half_year_month');

        foreach ($leaveTypes as $leaveType) {
            // Get the entitlement for this user's level and leave type
            $entitlement = LeaveEntitlement::where([
                'user_level_id' => $user->user_level_id,
                'leave_type_id' => $leaveType->id,
                'is_active' => true,
            ])->first();

            // Skip if no entitlement exists
            if (!$entitlement) {
                continue;
            }

            // Calculate base entitled days
            $baseEntitledDays = $this->calculateEntitledDays(
                $entitlement->days_per_year,
                $isProrated
            );

            // Get carried over days from previous year if applicable
            $carriedOverDays = 0;
            if ($entitlement->can_carry_over) {
                $previousYearBalance = LeaveBalance::where([
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $currentYear - 1,
                ])->first();

                if ($previousYearBalance && $previousYearBalance->days_remaining > 0) {
                    $carriedOverDays = min(
                        $previousYearBalance->days_remaining,
                        $entitlement->max_carry_over_days
                    );
                }
            }

            // Calculate total days including carried over days
            $totalDays = $baseEntitledDays + $carriedOverDays;

            // Create leave balance record
            LeaveBalance::create([
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'total_entitled_days' => $totalDays,
                'days_taken' => 0,
                'days_carried_forward' => $carriedOverDays,
                'days_remaining' => $totalDays,
                'year' => $currentYear,
            ]);
        }
    }

    /**
     * Calculate entitled days based on join date
     */
    private function calculateEntitledDays(
        float $baseEntitlement,
        bool $isProrated
    ): float {
        if (!$isProrated) {
            return $baseEntitlement;
        }

        // If joined after the half-year month, give half of the entitlement
        return round($baseEntitlement / 2, 1);
    }
} 