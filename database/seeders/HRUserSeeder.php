<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class HRUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create HR role if it doesn't exist
        $hrRole = Role::firstOrCreate(['name' => 'HR']);

        // Create HR user
        $hrUser = User::create([
            'staff_id' => 'HR001',
            'username' => 'hr.admin',
            'firstname' => 'HR',
            'lastname' => 'Administrator',
            'email' => 'hr@example.com',
            'password' => Hash::make('password'),
            'join_date' => now(),
            'is_active' => true,
        ]);

        // Assign HR role
        $hrUser->assignRole($hrRole);
    }
} 