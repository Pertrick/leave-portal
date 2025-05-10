<?php

namespace Database\Seeders;

use App\Models\LeaveEntitlement;
use App\Models\LeaveType;
use App\Models\UserLevel;
use Illuminate\Database\Seeder;

class LeaveEntitlementSeeder extends Seeder
{
    public function run(): void
    {
        $userLevels = UserLevel::all();
        $leaveTypes = LeaveType::all();

        foreach ($userLevels as $userLevel) {
            foreach ($leaveTypes as $leaveType) {
                $entitlement = [
                    'user_level_id' => $userLevel->id,
                    'leave_type_id' => $leaveType->id,
                    'days_per_year' => $this->getDefaultDays($userLevel->level, $leaveType->name),
                    'can_carry_over' => $this->canCarryOver($leaveType->name),
                    'max_carry_over_days' => $this->getMaxCarryOverDays($userLevel->level, $leaveType->name),
                    'is_active' => true
                ];

                LeaveEntitlement::create($entitlement);
            }
        }
    }

    private function getDefaultDays(int $userLevel, string $leaveType): int
    {
        return match ($leaveType) {
            'Annual Leave' => match ($userLevel) {
                1, 2 => 30, // Admin, Manager
                3 => 25,    // Supervisor
                4 => 20,    // Junior
                default => 15
            },
            'Sick Leave' => 14,
            'Maternity Leave' => 90,
            'Paternity Leave' => 14,
            'Compassionate Leave' => 5,
            'Casual/Personal Leave' => 7,
            'Field Work' => 0, // Not counted as leave
            default => 0
        };
    }

    private function canCarryOver(string $leaveType): bool
    {
        return match ($leaveType) {
            'Annual Leave' => true,
            'Sick Leave' => false,
            'Maternity Leave' => false,
            'Paternity Leave' => false,
            'Compassionate Leave' => false,
            'Casual/Personal Leave' => false,
            'Field Work' => false,
            default => false
        };
    }

    private function getMaxCarryOverDays(int $userLevel, string $leaveType): int
    {
        if (!$this->canCarryOver($leaveType)) {
            return 0;
        }

        return match ($leaveType) {
            'Annual Leave' => match ($userLevel) {
                1, 2 => 15, // Admin, Manager
                3 => 10,    // Supervisor
                4 => 5,     // Junior
                default => 0
            },
            default => 0
        };
    }
} 