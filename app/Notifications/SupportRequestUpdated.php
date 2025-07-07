<?php

namespace App\Notifications;

use App\Models\ContactSupport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportRequestUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ContactSupport $supportRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Support Request Updated: ' . $this->supportRequest->subject)
            ->greeting('Support Request Update')
            ->line('Your support request has been updated.')
            ->line('Subject: ' . $this->supportRequest->subject)
            ->line('Status: ' . $this->supportRequest->status_label)
            ->line('Response: ' . $this->supportRequest->admin_response)
            ->action('View Details', route('contact-support.show', $this->supportRequest->id))
            ->line('Thank you for using our support system.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'support_request_id' => $this->supportRequest->id,
            'status' => $this->supportRequest->status,
            'responded_by' => $this->supportRequest->responded_by,
            'message' => 'Your support request has been updated',
            'type' => 'support_request_updated',
        ];
    }
} 