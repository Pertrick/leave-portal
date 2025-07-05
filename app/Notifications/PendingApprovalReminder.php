<?php

namespace App\Notifications;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class PendingApprovalReminder extends BaseNotification
{
    public function __construct(
        protected Leave $leave,
        protected User $approver
    ) {}

    public function toMail(object $notifiable): MailMessage
    {
        $leaveType = $this->leave->leaveType->name;
        $startDate = Carbon::parse($this->leave->start_date)->format('M d, Y');
        $endDate = Carbon::parse($this->leave->end_date)->format('M d, Y');
        $applicantName = $this->leave->user->full_name;

        return (new MailMessage)
            ->subject("Pending Leave Approval - {$leaveType}")
            ->view('emails.notification', [
                'title' => 'Pending Leave Approval',
                'message' => "You have a pending leave application that requires your approval.",
                'icon' => '⏰',
                'priority' => 'high',
                'details' => [
                    'Applicant' => $applicantName,
                    'Leave Type' => $leaveType,
                    'Start Date' => $startDate,
                    'End Date' => $endDate,
                    'Days' => $this->leave->working_days,
                    'Department' => $this->leave->user->department->name,
                    'Submitted' => Carbon::parse($this->leave->created_at)->format('M d, Y'),
                ],
                'actionUrl' => route('leave.approvals.show', $this->leave->id),
                'actionText' => 'Review Application'
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pending Leave Approval',
            'message' => "You have a pending leave application that requires your approval.",
            'type' => 'pending_approval',
            'icon' => '⏰',
            'priority' => 'high',
            'leave_id' => $this->leave->id,
            'applicant_id' => $this->leave->user->id,
            'applicant_name' => $this->leave->user->full_name,
            'leave_type' => $this->leave->leaveType->name,
            'start_date' => Carbon::parse($this->leave->start_date)->format('Y-m-d'),
            'end_date' => Carbon::parse($this->leave->end_date)->format('Y-m-d'),
            'days' => $this->leave->working_days,
            'submitted_date' => Carbon::parse($this->leave->created_at)->format('Y-m-d'),
        ];
    }

    protected function getType(): string
    {
        return 'warning';
    }

    protected function getPriority(): string
    {
        return 'high';
    }
} 