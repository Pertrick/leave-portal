<?php

namespace App\Services;

use App\Models\Leave;
use App\Models\User;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LeaveApplicationService
{
    public function create(array $data, User $user): Leave
    {
        $data['user_id'] = $user->id;
        $data['uuid'] = (string) Str::uuid();
        $data['calendar_days'] = $this->calculateCalendarDays($data['start_date'], $data['end_date']);
        $data['working_days'] = $this->calculateWorkingDays($data['start_date'], $data['end_date']);
        $data['status'] = 'pending';
        
        return Leave::create($data);
    }

    public function update(Leave $leave, array $data): Leave
    {
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $data['calendar_days'] = $this->calculateCalendarDays($data['start_date'], $data['end_date']);
            $data['working_days'] = $this->calculateWorkingDays($data['start_date'], $data['end_date']);
        }

        $leave->update($data);
        return $leave;
    }

    public function approve(Leave $leave, User $approver): Leave
    {
        $leave->update([
            'status' => 'approved',
            'current_approval_level' => 'completed',
            'current_approval_id' => null,
        ]);

        // Create approval record
        $leave->approvals()->create([
            'user_id' => $approver->id,
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return $leave;
    }

    public function reject(Leave $leave, User $rejector, string $reason): Leave
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
        ]);

        return $leave;
    }

    public function getUserLeaves(User $user): Collection
    {
        return $user->leaves()
            ->with(['leaveType', 'approvals.user'])
            ->latest()
            ->get();
    }

    public function getPendingLeaves(): Collection
    {
        return Leave::where('status', 'pending')
            ->with(['user', 'leaveType', 'approvals.user'])
            ->latest()
            ->get();
    }

    public function getLeaveTypes(): Collection
    {
        return LeaveType::where('is_active', true)->get();
    }

    protected function calculateCalendarDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        return $start->diffInDays($end) + 1; // Include both start and end dates
    }

    protected function calculateWorkingDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $days = 0;
        $current = $start->copy();
        
        while ($current->lte($end)) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                $days++;
            }
            $current->addDay();
        }
        
        return $days;
    }
} 