<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\AccountRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAccountRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly AccountRequest $accountRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Account Request - Leave Portal')
            ->greeting('Hello HR Team,')
            ->line('A new account request has been submitted.')
            ->line("Staff ID: {$this->accountRequest->staff_id}")
            ->when($this->accountRequest->notes, function (MailMessage $message) {
                return $message->line("Additional Notes: {$this->accountRequest->notes}");
            })
            ->action('View Request', route('admin.account-requests.show', $this->accountRequest))
            ->line('Please review and process this request at your earliest convenience.');
    }
} 