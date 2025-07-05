<?php

namespace App\Services;

use App\Models\User;
use App\Models\Leave;
use App\Notifications\LeaveApplicationSubmitted;
use App\Notifications\LeaveApplicationApproved;
use App\Notifications\LeaveApplicationRejected;
use App\Notifications\PendingApprovalReminder;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification when leave application is submitted
     */
    public function notifyLeaveSubmitted(Leave $leave): void
    {
        try {
            $applicant = $leave->user;
            
            // Notify supervisors
            $supervisors = $applicant->activeSupervisors;
            foreach ($supervisors as $supervisor) {
                $supervisor->notify(new LeaveApplicationSubmitted($leave, $applicant));
            }
            
            // Notify department head if no supervisors
            if ($supervisors->isEmpty()) {
                $departmentHead = $applicant->currentDepartmentHead;
                if ($departmentHead) {
                    $departmentHead->notify(new LeaveApplicationSubmitted($leave, $applicant));
                }
            }
            
            Log::info("Leave application submitted notification sent for leave ID: {$leave->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send leave submitted notification: " . $e->getMessage());
        }
    }

    /**
     * Send notification when leave application is approved
     */
    public function notifyLeaveApproved(Leave $leave, User $approver): void
    {
        try {
            $applicant = $leave->user;
            $applicant->notify(new LeaveApplicationApproved($leave, $approver));
            
            Log::info("Leave application approved notification sent for leave ID: {$leave->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send leave approved notification: " . $e->getMessage());
        }
    }

    /**
     * Send notification when leave application is rejected
     */
    public function notifyLeaveRejected(Leave $leave, User $rejecter, string $reason = null): void
    {
        try {
            $applicant = $leave->user;
            $applicant->notify(new LeaveApplicationRejected($leave, $rejecter, $reason));
            
            Log::info("Leave application rejected notification sent for leave ID: {$leave->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send leave rejected notification: " . $e->getMessage());
        }
    }

    /**
     * Send reminder for pending approvals
     */
    public function sendPendingApprovalReminders(): void
    {
        try {
            // Get pending leave applications
            $pendingLeaves = Leave::where('status', 'pending')
                ->with(['user', 'leaveType', 'user.department'])
                ->get();

            foreach ($pendingLeaves as $leave) {
                $this->sendPendingApprovalReminder($leave);
            }
            
            Log::info("Pending approval reminders sent for " . $pendingLeaves->count() . " applications");
        } catch (\Exception $e) {
            Log::error("Failed to send pending approval reminders: " . $e->getMessage());
        }
    }

    /**
     * Send reminder for a specific pending approval
     */
    public function sendPendingApprovalReminder(Leave $leave): void
    {
        try {
            $applicant = $leave->user;
            
            // Get the next approver in the chain
            $nextApprover = $this->getNextApprover($leave);
            
            if ($nextApprover) {
                $nextApprover->notify(new PendingApprovalReminder($leave, $nextApprover));
                Log::info("Pending approval reminder sent to {$nextApprover->full_name} for leave ID: {$leave->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send pending approval reminder: " . $e->getMessage());
        }
    }

    /**
     * Get the next approver for a leave application
     */
    private function getNextApprover(Leave $leave): ?User
    {
        // Get the current approval level
        $currentApproval = $leave->leaveApprovals()
            ->where('status', 'pending')
            ->orderBy('approval_level')
            ->first();

        if (!$currentApproval) {
            return null;
        }

        // Return the approver for the current level
        return User::find($currentApproval->approver_id);
    }

    /**
     * Send notification to multiple users
     */
    public function notifyMultipleUsers(array $userIds, $notification): void
    {
        try {
            $users = User::whereIn('id', $userIds)->get();
            
            foreach ($users as $user) {
                $user->notify($notification);
            }
            
            Log::info("Notification sent to " . count($users) . " users");
        } catch (\Exception $e) {
            Log::error("Failed to send notification to multiple users: " . $e->getMessage());
        }
    }

    /**
     * Send notification to all users with a specific role
     */
    public function notifyUsersWithRole(string $role, $notification): void
    {
        try {
            $users = User::role($role)->get();
            
            foreach ($users as $user) {
                $user->notify($notification);
            }
            
            Log::info("Notification sent to " . count($users) . " users with role: {$role}");
        } catch (\Exception $e) {
            Log::error("Failed to send notification to users with role {$role}: " . $e->getMessage());
        }
    }
} 