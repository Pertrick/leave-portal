<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    abstract public function toMail(object $notifiable): MailMessage;

    /**
     * Get the array representation of the notification.
     */
    abstract public function toArray(object $notifiable): array;

    /**
     * Get notification icon based on type
     */
    protected function getIcon(): string
    {
        return match ($this->getType()) {
            'success' => '✅',
            'warning' => '⚠️',
            'error' => '❌',
            'info' => 'ℹ️',
            default => '📢'
        };
    }

    /**
     * Get notification type
     */
    protected function getType(): string
    {
        return 'info';
    }

    /**
     * Get notification priority
     */
    protected function getPriority(): string
    {
        return 'normal';
    }
} 