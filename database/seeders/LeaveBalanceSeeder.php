<?php

namespace Database\Seeders;

use App\Models\LeaveBalance;
use App\Models\LeaveEntitlement;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active leave types
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        // Get all users
        $users = User::all();
        
        // Current year
        $currentYear = now()->year;
        
        // Create leave balances for each user and leave type
        foreach ($users as $user) {
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
                
                // Check if balance already exists
                $exists = LeaveBalance::where([
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $currentYear,
                ])->exists();
                
                if (!$exists) {
                    // Get carried over days from previous year if applicable
                    $carriedOverDays = 0;
                    if ($entitlement->can_carry_over) {
                        $previousYearBalance = LeaveBalance::where([
                            'user_id' => $user->id,
                            'leave_type_id' => $leaveType->id,
                            'year' => $currentYear - 1,
                        ])->first();
                        
                        if ($previousYearBalance && $previousYearBalance->remaining_days > 0) {
                            $carriedOverDays = min(
                                $previousYearBalance->remaining_days,
                                $entitlement->max_carry_over_days
                            );
                        }
                    }
                    
                    // Calculate total days including carried over days
                    $totalDays = $entitlement->days_per_year + $carriedOverDays;
                    
                    LeaveBalance::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $currentYear,
                        'total_entitled_days' => $totalDays,
                        'days_taken' => 0,
                        'days_remaining' => $totalDays,
                        'days_carried_forward' => $carriedOverDays,
                    ]);
                }
            }
        }
    }
} 