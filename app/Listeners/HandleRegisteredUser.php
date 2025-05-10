<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\UserSetting;
use App\Notifications\NewUserAdminNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleRegisteredUser implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;
        $password = $event->password;

        // Create default settings for the user
        $user->settings()->create(UserSetting::getDefaults());

        // Send welcome notification with login credentials
        $user->notify(new WelcomeNotification($user, $password));

        // Notify administrators
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewUserAdminNotification($user));
        }

        // Log the registration
        Log::info('New user registration', [
            'user_id' => $user->id,
            'staff_id' => $user->staff_id,
            'email' => $user->email,
            'department' => $user->department->name ?? 'N/A',
            'user_level' => $user->userLevel->name ?? 'N/A',
            'registration_date' => now()->toDateTimeString(),
        ]);
    }
} 