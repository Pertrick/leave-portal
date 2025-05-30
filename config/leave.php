<?php

return [
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