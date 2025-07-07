<?php

namespace App\Notifications;

use App\Models\ContactSupport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSupportRequest extends Notification implements ShouldQueue
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
            ->subject('New Support Request: ' . $this->supportRequest->subject)
            ->greeting('New Support Request')
            ->line('A new support request has been submitted.')
            ->line('From: ' . $this->supportRequest->user->full_name)
            ->line('Subject: ' . $this->supportRequest->subject)
            ->line('Category: ' . $this->supportRequest->category_label)
            ->line('Priority: ' . $this->supportRequest->priority_label)
            ->line('Message: ' . substr($this->supportRequest->message, 0, 100) . '...')
            ->action('View Request', route('contact-support.show', $this->supportRequest->id))
            ->line('Please respond to this request as soon as possible.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'support_request_id' => $this->supportRequest->id,
            'user_id' => $this->supportRequest->user_id,
            'subject' => $this->supportRequest->subject,
            'category' => $this->supportRequest->category,
            'priority' => $this->supportRequest->priority,
            'message' => 'New support request from ' . $this->supportRequest->user->full_name,
            'type' => 'new_support_request',
        ];
    }
} 