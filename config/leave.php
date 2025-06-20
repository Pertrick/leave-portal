<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Leave Application Settings
    |--------------------------------------------------------------------------
    |
    | This file contains configurable settings for leave applications.
    | These settings can be overridden using environment variables.
    |
    */

    'validation' => [
        /*
        |--------------------------------------------------------------------------
        | Date Validation Rules
        |--------------------------------------------------------------------------
        |
        | Configure which date validation rules are enforced when creating
        | or updating leave applications.
        |
        */
        
        'prevent_weekend_start' => env('LEAVE_PREVENT_WEEKEND_START', true),
        'prevent_weekend_end' => env('LEAVE_PREVENT_WEEKEND_END', true),
        'prevent_holiday_start' => env('LEAVE_PREVENT_HOLIDAY_START', true),
        'prevent_holiday_end' => env('LEAVE_PREVENT_HOLIDAY_END', true),
        'prevent_past_dates' => env('LEAVE_PREVENT_PAST_DATES', true),
        'allow_backdated_medical' => env('LEAVE_ALLOW_BACKDATED_MEDICAL', true),
        'backdated_medical_days_limit' => env('LEAVE_BACKDATED_MEDICAL_DAYS_LIMIT', 7),
        
        /*
        |--------------------------------------------------------------------------
        | Medical Leave Settings
        |--------------------------------------------------------------------------
        |
        | Special settings for medical leave types.
        |
        */
        'medical_leave' => [
            'allow_backdating' => env('LEAVE_MEDICAL_ALLOW_BACKDATING', true),
            'backdate_limit_days' => env('LEAVE_MEDICAL_BACKDATE_LIMIT_DAYS', 7),
            'require_attachment' => env('LEAVE_MEDICAL_REQUIRE_ATTACHMENT', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Settings for file uploads in leave applications.
    |
    */
    'file_upload' => [
        'max_size_mb' => env('LEAVE_FILE_MAX_SIZE_MB', 2),
        'allowed_extensions' => env('LEAVE_FILE_ALLOWED_EXTENSIONS', 'pdf,jpg,jpeg,png'),
        'storage_path' => env('LEAVE_FILE_STORAGE_PATH', 'leave-attachments'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Approval Settings
    |--------------------------------------------------------------------------
    |
    | Settings for leave approval workflow.
    |
    */
    'approval' => [
        'auto_approve_single_level' => env('LEAVE_AUTO_APPROVE_SINGLE_LEVEL', false),
        'notify_on_submission' => env('LEAVE_NOTIFY_ON_SUBMISSION', true),
        'notify_on_approval' => env('LEAVE_NOTIFY_ON_APPROVAL', true),
        'notify_on_rejection' => env('LEAVE_NOTIFY_ON_REJECTION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Display Settings
    |--------------------------------------------------------------------------
    |
    | Settings for how leave information is displayed.
    |
    */
    'display' => [
        'show_holidays_in_calendar' => env('LEAVE_SHOW_HOLIDAYS_IN_CALENDAR', true),
        'show_weekends_in_calendar' => env('LEAVE_SHOW_WEEKENDS_IN_CALENDAR', true),
        'calendar_panes' => env('LEAVE_CALENDAR_PANES', 2),
        'date_format' => env('LEAVE_DATE_FORMAT', 'Y-m-d'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Leave Balance Settings
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for leave balances.
    |
    */

    'proration' => [
        'enabled' => env('LEAVE_PRORATION_ENABLED', true),
        'half_year_month' => env('LEAVE_PRORATION_HALF_YEAR_MONTH', 6), // June
    ],
    'defaults' => [
        'annual_leave' => [
            'days_per_year' => [
                1 => 30, // Admin
                2 => 30, // Manager
                3 => 20, // Supervisor
                4 => 25, // Junior
                'default' => 15,
            ],
            'can_carry_over' => true,
            'max_carry_over_days' => [
                1 => 15, // Admin
                2 => 15, // Manager
                3 => 5, // Supervisor
                4 => 10,  // Junior
                'default' => 0,
            ],
        ],
        'sick_leave' => [
            'days_per_year' => 14,
            'can_carry_over' => false,
            'max_carry_over_days' => 0,
        ],
        'maternity_leave' => [
            'days_per_year' => 90,
            'can_carry_over' => false,
            'max_carry_over_days' => 0,
        ],
        'paternity_leave' => [
            'days_per_year' => 14,
            'can_carry_over' => false,
            'max_carry_over_days' => 0,
        ],
        'compassionate_leave' => [
            'days_per_year' => 5,
            'can_carry_over' => false,
            'max_carry_over_days' => 0,
        ],
        'casual_leave' => [
            'days_per_year' => 7,
            'can_carry_over' => false,
            'max_carry_over_days' => 0,
        ],
        'field_work' => [
            'days_per_year' => 0,
            'can_carry_over' => false,
            'max_carry_over_days' => 0,
        ],
    ],
    'max_draft_leave_per_user' => env('MAX_DRAFT_LEAVE_PER_USER', 3),
]; 