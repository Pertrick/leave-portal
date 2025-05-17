<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Leave;
use App\Models\Holiday;
use App\Models\Location;
use App\Models\LeaveType;
use Illuminate\Support\Str;
use App\Models\LeaveBalance;
use App\Models\ApprovalLevel;
use App\Models\LeaveApproval;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class LeaveApplicationService
{
    public function create(array $data, User $user): Leave
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $leaveType = LeaveType::findOrFail($data['leave_type_id']);

        // Validate dates
        $this->validateLeaveDates($startDate, $endDate, $user->location_id);

        // Calculate calendar days (including holidays)
        $calendarDays = $startDate->diffInDays($endDate) + 1;

        // Calculate working days (excluding weekends and holidays)
        $workingDays = $this->calculateWorkingDays($startDate, $endDate, $leaveType);

        // Get holidays in the date range
        $holidays = $this->getHolidaysBetween($startDate, $endDate);

        try {
            DB::beginTransaction();
            
            // Create the leave record
            $leave = $user->leaves()->create([
                'user_id' => $user->id,
                'leave_type_id' => $data['leave_type_id'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $data['reason'],
                'calendar_days' => $calendarDays,
                'working_days' => $workingDays,
                'status' => 'pending',
                'location_id' => $user->location_id,
                'holidays' => $holidays->pluck('name')->toArray()
            ]);

            // Get all active approval levels ordered by level
            $approvalLevels = ApprovalLevel::where('is_active', true)
                ->orderBy('level')
                ->get();

            if ($approvalLevels->isEmpty()) {
                throw new \InvalidArgumentException('No approval levels found.');
            }

            $firstApproval = null;

            // Create leave approval records for each level
            foreach ($approvalLevels as $level) {
                // Get approvers for this level
                $approvers = $this->getApprovers($level);
                
                if ($approvers->isEmpty()) {
                    throw new \InvalidArgumentException("No approvers found for level {$level->name}");
                }

                // Create approval record for each approver at this level
                foreach ($approvers as $approver) {
                    $leaveApproval = $leave->approvals()->create([
                        'user_id' => $user->id,
                        'approver_id' => $approver->id,
                        'status' => 'pending',
                        'sequence' => $level->level,
                        'level_id' => $level->id
                    ]);

                    // Store the first approval record
                    if ($level->level === $approvalLevels->first()->level && !$firstApproval) {
                        $firstApproval = $leaveApproval;
                    }
                }
            }

            if (!$firstApproval) {
                throw new \Exception('Failed to create initial approval record');
            }

            // Set the current approval level to the first level
            $firstLevel = $approvalLevels->first();
            $leave->update([
                'current_approval_level' => $firstLevel->name,
                'current_approval_id' => $firstApproval->id
            ]);

            DB::commit();
            return $leave;
            
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate leave dates
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $locationId
     * @throws \InvalidArgumentException
     */
    private function validateLeaveDates(Carbon $startDate, Carbon $endDate, ?int $locationId = null): void
    {
        if ($startDate->isWeekend()) {
            throw new \InvalidArgumentException('Leave cannot start on a weekend.');
        }

        if ($endDate->isWeekend()) {
            throw new \InvalidArgumentException('Leave cannot end on a weekend.');
        }

        if ($this->isHoliday($startDate, $locationId)) {
            throw new \InvalidArgumentException('Leave cannot start on a holiday.');
        }

        if ($this->isHoliday($endDate, $locationId)) {
            throw new \InvalidArgumentException('Leave cannot end on a holiday.');
        }

        if ($startDate->gt($endDate)) {
            throw new \InvalidArgumentException('Start date cannot be after end date.');
        }

        if ($startDate->lt(now()->startOfDay())) {
            throw new \InvalidArgumentException('Start date cannot be in the past.');
        }
    }

    public function createOrUpdateDraft(array $data, User $user, ?Leave $leave = null): Leave
    {
        $data = $this->calculateDays($data);
        if ($leave && $leave->isDraft()) {
            $leave->update($data);
        } else {
            $leave = $user->leaves()->create($data);
        }
        // Optionally handle file upload if needed:
        if (isset($data['attachment'])) {
            $path = $data['attachment']->store('attachments', 'public');
            $leave->update(['attachment_path' => $path]);
        }

        return $leave;
    }

    public function update(Leave $leave, array $data): Leave
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $leaveType = LeaveType::findOrFail($data['leave_type_id']);
        $user = Auth::user();

        // Validate dates
        $this->validateLeaveDates($startDate, $endDate, $user->location_id);

        // Calculate calendar days (including holidays)
        $calendarDays = $startDate->diffInDays($endDate) + 1;

        // Calculate working days (excluding weekends and holidays)
        $workingDays = $this->calculateWorkingDays($startDate, $endDate, $leaveType);

        // Get holidays in the date range
        $holidays = $this->getHolidaysBetween($startDate, $endDate);

        try {
            DB::beginTransaction();
            // Update the leave record
           $leave->update([
                'user_id' => $user->id,
                'leave_type_id' => $data['leave_type_id'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $data['reason'],
                'calendar_days' => $calendarDays,
                'working_days' => $workingDays,
                'status' => 'pending',
                'location_id' => $user->location_id,
                'holidays' => $holidays->pluck('name')->toArray()
            ]);

            // Get all active approval levels ordered by level
            $approvalLevels = ApprovalLevel::where('is_active', true)
                ->orderBy('level')
                ->get();

            if ($approvalLevels->isEmpty()) {
                throw new \InvalidArgumentException('No approval levels found.');
            }

            // Create leave approval records for each level
            foreach ($approvalLevels as $level) {
                // Get approvers for this level
                $approvers = $this->getApprovers($level);
                
                if ($approvers->isEmpty()) {
                    throw new \InvalidArgumentException("No approvers found for level {$level->name}");
                }
               
                    foreach ($approvers as $approver) {
                        if(!$leave->approvals()->where('level_id', $level->id)->where('user_id', $user->id)->where('approver_id', $approver->id)->exists()) {
                          $leaveApproval = $leave->approvals()->create([
                                'user_id' => $user->id,
                                'approver_id' => $approver->id,
                                'status' => 'pending',
                                'sequence' => $level->level,
                                'level_id' => $level->id
                            ]);

                            $leaveApproval->update([
                                'current_approval_id' => $leaveApproval->id
                            ]);
                        }
                }
            }

            // Set the current approval level to the first level
            $firstLevel = $approvalLevels->first();
            $leave->update([
                'current_approval_level' => $firstLevel->name,
            ]);

            DB::commit();
            return $leave;
            
        } catch(\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            throw $e;
        }
    }

    public function approve(Leave $leave, User $approver, ?string $comment = null): Leave
    {
        try {
            DB::beginTransaction();

            $leaveApproval = LeaveApproval::where('id', $leave->current_approval_id)
                                           ->where('approver_id', $approver->id)
                                           ->first();
            
            if (!$leaveApproval) {
                throw new \Exception('Invalid approval record');
            }

            $currentLevel = ApprovalLevel::where('id', $leaveApproval->level_id)->first();
            
            if (!$currentLevel) {
                throw new \Exception('Invalid approval level');
            }

            $nextLevel = $currentLevel->getNextLevel();

            // Update current approval record
            $leaveApproval->update([
                'status' => 'approved',
                'action_date' => now(),
                'remark' => $comment
            ]);

            if (!$nextLevel) {
                // This was the final approval level
                $leave->update([
                    'status' => 'approved',
                    'current_approval_level' => 'approved',
                    'current_approval_id' => null
                ]);
                
                $leaveBalance = LeaveBalance::where('user_id', $leave->user_id)
                    ->where('leave_type_id', $leave->leave_type_id)
                    ->where('year', Carbon::parse($leave->start_date)->year)
                    ->first();
                    
                if($leaveBalance) {
                    $newDaysTaken = $leaveBalance->days_taken + $leave->working_days;
                    $newDaysRemaining = $leaveBalance->days_remaining - $leave->working_days;
                    
                    $leaveBalance->update([
                        'days_taken' => $newDaysTaken,
                        'days_remaining' => $newDaysRemaining
                    ]);
                }
            } else {
                $nextApproval = LeaveApproval::where('leave_id', $leave->id)
                    ->where('level_id', $nextLevel->id)
                    ->first();
                    
                if (!$nextApproval) {
                    throw new \Exception('Next approval level record not found');
                }
                
                // Move to next approval level
                $leave->update([
                    'current_approval_level' => $nextLevel->level,
                    'current_approval_id' => $nextApproval->id
                ]);
            }

            DB::commit();
            return $leave;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function reject(Leave $leave, User $rejector, string $reason, ?string $comment = null): Leave
    {
        $leave->update([
            'status' => 'rejected',
            'current_approval_level' => 'rejected',
            'current_approval_id' => null,
        ]);

        // Create approval record
        $leave->approvals()->create([
            'user_id' => $rejector->id,
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'remark' => $comment
        ]);

        return $leave;
    }

    public function getUserLeaves(User $user): Collection
    {
        return $user->leaves()
            ->with(['leaveType', 'approvals.user'])
            ->where('status', '!=', 'draft')
            ->latest()
            ->get();
    }

    public function getUserDrafts(User $user): Collection
    {
        return $user->leaves()
            ->with(['leaveType'])
            ->where('status', 'draft')
            ->latest()
            ->get();
    }

    public function getPendingLeaves(): LengthAwarePaginator
    {
        $user = Auth::user();
        
        return Leave::where('status', 'pending')
            ->whereHas('approvals', function ($query) use ($user) {
                $query->where('approver_id', $user->id)
                    ->where('status', 'pending')
                    ->where('sequence', function ($subQuery) {
                        $subQuery->select('sequence')
                            ->from('leave_approvals')
                            ->whereColumn('leave_id', 'leaves.id')
                            ->where('status', 'pending')
                            ->orderBy('sequence')
                            ->limit(1);
                    });
            })
            ->with([
                'user',
                'leaveType',
                'approvals' => function ($query) {
                    $query->orderBy('sequence');
                },
                'approvals.approver',
                'approvals.approvalLevel'
            ])
            ->withCount([
                'approvals as approved_count' => function ($query) {
                    $query->where('status', 'approved');
                },
                'approvals as rejected_count' => function ($query) {
                    $query->where('status', 'rejected');
                }
            ])
            ->latest()
            ->paginate(10);
    }

    public function getLeaveTypes(): Collection
    {
        return LeaveType::where('is_active', true)->get();
    }

    public function getLeaveBalances(User $user): Collection
    {
        return $user->leaveBalances()
            ->with('leaveType')
            ->where('year', now()->year)
            ->get();
    }

    public function calculateDays(array $data): array
    {
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $data['calendar_days'] = $this->calculateCalendarDays($data['start_date'], $data['end_date']);
            
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $leaveType = LeaveType::findOrFail($data['leave_type_id']);
            
            $data['working_days'] = $this->calculateWorkingDays($startDate, $endDate,  $leaveType);
        } else {
            $data['calendar_days'] = 0;
            $data['working_days'] = 0;
        }
        
        return $data;
    }

    protected function calculateCalendarDays(?string $startDate, ?string $endDate): int
    {
        if (!$startDate || !$endDate) {
            return 0;
        }
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        return $start->diffInDays($end) + 1; // Include both start and end dates
    }

    private function calculateWorkingDays(Carbon $startDate, Carbon $endDate, ?LeaveType $leaveType = null): int
    {
        $days = 0;
        $currentDate = $startDate->copy();

        // Get holidays between dates
        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->toArray();

        while ($currentDate->lte($endDate)) {
            // Skip configured weekend days if leave type is provided
            if ($leaveType && $leaveType->isWeekendDay($currentDate->dayOfWeek)) {
                $currentDate->addDay();
                continue;
            }

            // Skip holidays
            if (in_array($currentDate->format('Y-m-d'), $holidays)) {
                $currentDate->addDay();
                continue;
            }

            $days++;
            $currentDate->addDay();
        }

        return $days;
    }

    /**
     * Check if a given date is a holiday
     *
     * @param Carbon $date The date to check
     * @param int|null $locationId The location ID to check holidays for
     * @return bool
     */
    private function isHoliday(Carbon $date, ?Location $location = null): bool
    {
        $query = Holiday::where('is_active', true)
            ->where(function ($query) use ($date, $location) {
                // Check fixed date holidays
                $query->where(function ($q) use ($date) {
                    $q->where('date', $date->format('Y-m-d'))
                        ->orWhere(function ($q) use ($date) {
                            $q->where('is_recurring', true)
                                ->where('recurrence_type', 'fixed')
                                ->where('recurrence_day', $date->day)
                                ->where('recurrence_month', $date->month);
                        });
                });

                // Check Easter-based holidays
                $query->orWhere(function ($q) use ($date) {
                    $q->where('is_recurring', true)
                        ->where('recurrence_type', 'easter')
                        ->whereRaw('DATE_ADD(?, INTERVAL easter_offset DAY) = ?', [
                            $this->calculateEasterSunday($date->year)->format('Y-m-d'),
                            $date->format('Y-m-d')
                        ]);
                        
                });

                // Check custom rule holidays
                $query->orWhere(function ($q) use ($date) {
                    $q->where('is_recurring', true)
                        ->where('recurrence_type', 'custom')
                        ->whereJsonContains('custom_rule->month', $date->month)
                        ->whereJsonContains('custom_rule->type', 'fixed')
                        ->whereJsonContains('custom_rule->day', $date->day);
                });

                // Add location filter if provided
                if ($location) {
                    $query->where(function ($q) use ($location) {
                        $q->whereNull('location_id')
                            ->orWhere('location_id', $location->id);
                    });
                }
            });

        return $query->exists();
    }

    private function calculateEasterSunday(int $year): Carbon
    {
        $a = $year % 19;
        $b = floor($year / 100);
        $c = $year % 100;
        $d = floor($b / 4);
        $e = $b % 4;
        $f = floor(($b + 8) / 25);
        $g = floor(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = floor($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = floor(($a + 11 * $h + 22 * $l) / 451);
        $month = floor(($h + $l - 7 * $m + 114) / 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($year, $month, $day);
    }

    /**
     * Get all holidays between two dates
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $locationId
     * @return Collection
     */
    private function getHolidaysBetween(Carbon $startDate, Carbon $endDate, ?int $locationId = null): Collection
    {
        $query = Holiday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('is_active', true);

        if ($locationId) {
            $query->where(function ($q) use ($locationId) {
                $q->where('location_id', $locationId)
                    ->orWhereNull('location_id');
            });
        }

        return $query->get();
    }

    /**
     * Get approvers for a specific approval level
     *
     * @param ApprovalLevel $level
     * @return Collection
     */
    private function getApprovers(ApprovalLevel $level): Collection
    {
        return User::whereHas('roles', function ($query) use ($level) {
            $query->where('name', $level->role_name);
        })
        ->where('department_id', Auth::user()->department_id)
        ->where('is_active', true)
        ->get();
    }
} 