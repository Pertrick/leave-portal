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
                'level' => 1
            ],
            [
                'name' => 'Supervisor',
                'level' => 2
            ],
            [
                'name' => 'Junior',
                'level' => 3,
            ],
            [
                'name' => 'Intern/corpers',
                'level' => 5,
            ],
        ];

        foreach ($userLevels as $userLevel) {
            UserLevel::create($userLevel);
        }
    }
} 