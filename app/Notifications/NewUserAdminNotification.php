<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $user
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New User Registration')
            ->greeting('New User Registration Alert')
            ->line('A new user has registered in the system.')
            ->line('User Details:')
            ->line('Name: ' . $this->user->full_name)
            ->line('Staff ID: ' . $this->user->staff_id)
            ->line('Email: ' . $this->user->email)
            ->line('Department: ' . $this->user->department->name)
            ->line('User Level: ' . $this->user->userLevel->name)
            ->action('View User Details', route('users.show', $this->user->id))
            ->line('Please review the user details and take necessary actions if required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'staff_id' => $this->user->staff_id,
            'message' => 'New user registration: ' . $this->user->full_name,
            'type' => 'new_user',
        ];
    }
} 