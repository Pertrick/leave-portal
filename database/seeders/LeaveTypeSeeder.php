<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'requires_medical_proof' => false,
                'weekend_days' => [
                    'saturday' => false,
                    'sunday' => false
                ]
            ],
            [
                'name' => 'Sick Leave',
                'requires_medical_proof' => true,
                'weekend_days' => [
                    'saturday' => false,
                    'sunday' => false
                ]
            ],
            [
                'name' => 'Maternity Leave',
                'requires_medical_proof' => true,
                'weekend_days' => [
                    'saturday' => true,
                    'sunday' => true
                ]
            ],
            [
                'name' => 'Paternity Leave',
                'requires_medical_proof' => false,
                'weekend_days' => [
                    'saturday' => true,
                    'sunday' => true
                ]
            ],
            [
                'name' => 'Compassionate Leave',
                'requires_medical_proof' => false,
                'weekend_days' => [
                    'saturday' => false,
                    'sunday' => false
                ]
            ],
            [
                'name' => 'Casual/Personal Leave',
                'requires_medical_proof' => false,
                'weekend_days' => [
                    'saturday' => false,
                    'sunday' => false
                ]
            ],
            [
                'name' => 'Field Work',
                'requires_medical_proof' => false,
                'weekend_days' => [
                    'saturday' => true,
                    'sunday' => true
                ]
            ]
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }
    }
} 