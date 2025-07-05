<?php

namespace App\Notifications;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class LeaveApplicationSubmitted extends BaseNotification
{
    public function __construct(
        protected Leave $leave,
        protected User $applicant
    ) {}

    public function toMail(object $notifiable): MailMessage
    {
        $leaveType = $this->leave->leaveType->name;
        $startDate = Carbon::parse($this->leave->start_date)->format('M d, Y');
        $endDate = Carbon::parse($this->leave->end_date)->format('M d, Y');
        $days = $this->leave->working_days;

        return (new MailMessage)
            ->subject("Leave Application Submitted - {$leaveType}")
            ->view('emails.notification', [
                'title' => 'Leave Application Submitted',
                'message' => "A new leave application has been submitted by {$this->applicant->full_name}.",
                'icon' => '📝',
                'priority' => 'normal',
                'details' => [
                    'Applicant' => $this->applicant->full_name,
                    'Leave Type' => $leaveType,
                    'Start Date' => $startDate,
                    'End Date' => $endDate,
                    'Days' => $days,
                    'Reason' => $this->leave->reason,
                    'Department' => $this->applicant->department->name,
                ],
                'actionUrl' => route('admin.leave-applications.show', $this->leave->id),
                'actionText' => 'Review Application'
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Leave Application Submitted',
            'message' => "A new leave application has been submitted by {$this->applicant->full_name}.",
            'type' => 'leave_submitted',
            'icon' => '📝',
            'priority' => 'normal',
            'leave_id' => $this->leave->id,
            'applicant_id' => $this->applicant->id,
            'applicant_name' => $this->applicant->full_name,
            'leave_type' => $this->leave->leaveType->name,
            'start_date' => Carbon::parse($this->leave->start_date)->format('Y-m-d'),
            'end_date' => Carbon::parse($this->leave->end_date)->format('Y-m-d'),
            'days' => $this->leave->working_days,
        ];
    }

    protected function getType(): string
    {
        return 'info';
    }
} 