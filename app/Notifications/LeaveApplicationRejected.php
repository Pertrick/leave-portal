<?php

namespace App\Notifications;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class LeaveApplicationRejected extends BaseNotification
{
    public function __construct(
        protected Leave $leave,
        protected User $rejecter,
        protected string $reason = null
    ) {}

    public function toMail(object $notifiable): MailMessage
    {
        $leaveType = $this->leave->leaveType->name;
        $startDate = Carbon::parse($this->leave->start_date)->format('M d, Y');
        $endDate = Carbon::parse($this->leave->end_date)->format('M d, Y');

        $details = [
            'Leave Type' => $leaveType,
            'Start Date' => $startDate,
            'End Date' => $endDate,
            'Days' => $this->leave->working_days,
            'Rejected By' => $this->rejecter->full_name,
            'Rejection Date' => now()->format('M d, Y'),
        ];

        if ($this->reason) {
            $details['Reason for Rejection'] = $this->reason;
        }

        return (new MailMessage)
            ->subject("Leave Application Rejected - {$leaveType}")
            ->view('emails.notification', [
                'title' => 'Leave Application Rejected',
                'message' => "Your leave application has been rejected by {$this->rejecter->full_name}.",
                'icon' => '❌',
                'priority' => 'high',
                'details' => $details,
                'actionUrl' => route('leave.applications.show', $this->leave->id),
                'actionText' => 'View Application'
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Leave Application Rejected',
            'message' => "Your leave application has been rejected by {$this->rejecter->full_name}.",
            'type' => 'leave_rejected',
            'icon' => '❌',
            'priority' => 'high',
            'leave_id' => $this->leave->id,
            'rejecter_id' => $this->rejecter->id,
            'rejecter_name' => $this->rejecter->full_name,
            'leave_type' => $this->leave->leaveType->name,
            'start_date' => Carbon::parse($this->leave->start_date)->format('Y-m-d'),
            'end_date' => Carbon::parse($this->leave->end_date)->format('Y-m-d'),
            'days' => $this->leave->working_days,
            'rejection_reason' => $this->reason,
        ];
    }

    protected function getType(): string
    {
        return 'error';
    }

    protected function getPriority(): string
    {
        return 'high';
    }
} 