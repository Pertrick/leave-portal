<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'max_leave_days_per_request',
                'value' => '14',
                'description' => 'Maximum number of days allowed in a single leave request'
            ],
            [
                'key' => 'min_leave_days_notice',
                'value' => '3',
                'description' => 'Minimum number of days notice required before taking leave'
            ],
            [
                'key' => 'max_carry_forward_days',
                'value' => '10',
                'description' => 'Maximum number of days that can be carried forward to next year'
            ],
            [
                'key' => 'leave_year_start_month',
                'value' => '1',
                'description' => 'Month when the leave year starts (1-12)'
            ],
            [
                'key' => 'leave_year_start_day',
                'value' => '1',
                'description' => 'Day when the leave year starts (1-31)'
            ],
            [
                'key' => 'max_consecutive_leave_days',
                'value' => '21',
                'description' => 'Maximum number of consecutive days allowed for leave'
            ],
            [
                'key' => 'require_attachment_threshold',
                'value' => '5',
                'description' => 'Number of days after which attachment is required'
            ],
            [
                'key' => 'auto_approve_leave_days',
                'value' => '3',
                'description' => 'Number of days below which leave is auto-approved'
            ],
            [
                'key' => 'weekend_days',
                'value' => '6,7',
                'description' => 'Days of week considered as weekends (1-7, comma separated)'
            ],
            [
                'key' => 'working_hours_start',
                'value' => '09:00',
                'description' => 'Start time of working hours'
            ],
            [
                'key' => 'working_hours_end',
                'value' => '17:00',
                'description' => 'End time of working hours'
            ]
        ];

        foreach ($settings as $setting) {
            DB::table('leave_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
} 