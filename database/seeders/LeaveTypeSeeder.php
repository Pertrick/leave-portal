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
                'requires_medical_proof' => false
            ],
            [
                'name' => 'Sick Leave',
                'requires_medical_proof' => true
            ],
            [
                'name' => 'Maternity Leave',
                'requires_medical_proof' => true
            ],
            [
                'name' => 'Paternity Leave',
                'requires_medical_proof' => false
            ],
            [
                'name' => 'Compassionate Leave',
                'requires_medical_proof' => false
            ],
            [
                'name' => 'Casual/Personal Leave',
                'requires_medical_proof' => false
            ],
            [
                'name' => 'Field Work',
                'requires_medical_proof' => false
            ]
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }
    }
} 