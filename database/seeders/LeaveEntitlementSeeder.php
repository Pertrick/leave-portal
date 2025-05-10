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
                $configKey = $this->getConfigKey($leaveType->name);
                $config = config("leave.defaults.{$configKey}");

                if (!$config) {
                    continue;
                }

                $daysPerYear = $this->getDaysPerYear($config, $userLevel->level);
                $canCarryOver = $config['can_carry_over'] ?? false;
                $maxCarryOverDays = $this->getMaxCarryOverDays($config, $userLevel->level, $canCarryOver);

                LeaveEntitlement::create([
                    'user_level_id' => $userLevel->id,
                    'leave_type_id' => $leaveType->id,
                    'days_per_year' => $daysPerYear,
                    'can_carry_over' => $canCarryOver,
                    'max_carry_over_days' => $maxCarryOverDays,
                    'is_active' => true,
                ]);
            }
        }
    }

    private function getDaysPerYear(array $config, int $userLevel): int
    {
        if (!isset($config['days_per_year'])) {
            return 0;
        }

        if (is_array($config['days_per_year'])) {
            return $config['days_per_year'][$userLevel] ?? $config['days_per_year']['default'] ?? 0;
        }

        return $config['days_per_year'];
    }

    private function getMaxCarryOverDays(array $config, int $userLevel, bool $canCarryOver): int
    {
        if (!$canCarryOver || !isset($config['max_carry_over_days'])) {
            return 0;
        }

        if (is_array($config['max_carry_over_days'])) {
            return $config['max_carry_over_days'][$userLevel] ?? $config['max_carry_over_days']['default'] ?? 0;
        }

        return $config['max_carry_over_days'];
    }

    private function getConfigKey(string $leaveType): string
    {
        return strtolower(str_replace([' ', '/'], '_', $leaveType));
    }
} 