<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Create Admin user
        $adminUser = User::create([
            'staff_id' => 'ADM001',
            'username' => 'admin',
            'firstname' => 'System',
            'lastname' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'join_date' => now(),
            'is_active' => true,
        ]);

        // Assign Admin role
        $adminUser->assignRole($adminRole);
    }
} 