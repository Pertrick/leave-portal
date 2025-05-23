<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected User $user,
        protected string $password
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to the Leave Portal')
            ->greeting("Hello {$this->user->first_name}!")
            ->line('Welcome to the Leave Portal. Your account has been successfully created.')
            ->line('Here are your login credentials:')
            ->line("Email: {$this->user->email}")
            ->line("Staff ID: {$this->user->staff_id}")
            ->line("Password: {$this->password}")
            ->line('For security reasons, please change your password after your first login.')
            ->line('You can now log in to the portal using either your email or staff ID along with your password.')
            ->line('If you have any questions or need assistance, please contact your department administrator or HR.')
            ->action('Log in to Leave Portal', route('login'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Welcome to Leave Portal',
            'message' => 'Your account has been successfully created.',
            'user_id' => $this->user->id,
            'staff_id' => $this->user->staff_id,
            'email' => $this->user->email,
        ];
    }
} 