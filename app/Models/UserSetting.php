<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'theme',
        'color_scheme',
        'compact_mode',
        'email_notifications',
        'push_notifications',
        'leave_approval_notifications',
        'leave_request_notifications',
        'system_notifications',
        'date_format',
        'time_format',
        'timezone',
        'language',
        'dashboard_widgets',
        'quick_links',
        'show_profile',
        'show_leave_balance',
        'show_contact_info',
        'custom_settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'compact_mode' => 'boolean',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'leave_approval_notifications' => 'boolean',
        'leave_request_notifications' => 'boolean',
        'system_notifications' => 'boolean',
        'show_profile' => 'boolean',
        'show_leave_balance' => 'boolean',
        'show_contact_info' => 'boolean',
        'dashboard_widgets' => 'array',
        'quick_links' => 'array',
        'custom_settings' => 'array',
    ];

    /**
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the default settings for a new user.
     */
    public static function getDefaults(): array
    {
        return [
            'theme' => 'light',
            'color_scheme' => 'blue',
            'compact_mode' => false,
            'email_notifications' => true,
            'push_notifications' => true,
            'leave_approval_notifications' => true,
            'leave_request_notifications' => true,
            'system_notifications' => true,
            'date_format' => 'Y-m-d',
            'time_format' => '24h',
            'timezone' => 'UTC',
            'language' => 'en',
            'dashboard_widgets' => [],
            'quick_links' => [],
            'show_profile' => true,
            'show_leave_balance' => true,
            'show_contact_info' => true,
            'custom_settings' => [],
        ];
    }
} 