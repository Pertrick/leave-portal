<?php

namespace Database\Seeders;

use App\Models\UserLevel;
use Illuminate\Database\Seeder;

class UserLevelSeeder extends Seeder
{
    public function run(): void
    {
        $userLevels = [
            [
                'name' => 'Manager',
                'level' => 2,
                'max_leave_days' => 25
            ],
            [
                'name' => 'Supervisor',
                'level' => 3,
                'max_leave_days' => 20
            ],
            [
                'name' => 'Junior',
                'level' => 4,
                'max_leave_days' => 18
            ]
        ];

        foreach ($userLevels as $userLevel) {
            UserLevel::create($userLevel);
        }
    }
} 