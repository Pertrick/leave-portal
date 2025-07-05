<?php

namespace App\Notifications;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class LeaveApplicationApproved extends BaseNotification
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

        return (new MailMessage)
            ->subject("Leave Application Approved - {$leaveType}")
            ->view('emails.notification', [
                'title' => 'Leave Application Approved',
                'message' => "Your leave application has been approved by {$this->approver->full_name}.",
                'icon' => '✅',
                'priority' => 'normal',
                'details' => [
                    'Leave Type' => $leaveType,
                    'Start Date' => $startDate,
                    'End Date' => $endDate,
                    'Days' => $this->leave->working_days,
                    'Approved By' => $this->approver->full_name,
                    'Approval Date' => now()->format('M d, Y'),
                ],
                'actionUrl' => route('leave.applications.show', $this->leave->id),
                'actionText' => 'View Application'
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Leave Application Approved',
            'message' => "Your leave application has been approved by {$this->approver->full_name}.",
            'type' => 'leave_approved',
            'icon' => '✅',
            'priority' => 'normal',
            'leave_id' => $this->leave->id,
            'approver_id' => $this->approver->id,
            'approver_name' => $this->approver->full_name,
            'leave_type' => $this->leave->leaveType->name,
            'start_date' => Carbon::parse($this->leave->start_date)->format('Y-m-d'),
            'end_date' => Carbon::parse($this->leave->end_date)->format('Y-m-d'),
            'days' => $this->leave->working_days,
        ];
    }

    protected function getType(): string
    {
        return 'success';
    }
} 