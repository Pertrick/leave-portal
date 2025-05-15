<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ApprovalLevel;
use Illuminate\Database\Seeder;

class ApprovalLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'name' => 'Supervisor Approval',
                'level' => 1,
                'role_name' => 'Supervisor',
                'is_active' => true,
                'description' => 'First level approval by immediate supervisor'
            ],
            [
                'name' => 'Department Head Approval',
                'level' => 2,
                'role_name' => 'HOD',
                'is_active' => true,
                'description' => 'Second level approval by department head'
            ],
            // [
            //     'name' => 'HR Approval',
            //     'level' => 3,
            //     'role_name' => 'HR',
            //     'description' => 'Final approval by HR'
            // ]
        ];

        foreach ($levels as $level) {
            ApprovalLevel::create($level);
        }
    }
} 