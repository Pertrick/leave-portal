<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::create([
        //     'uuid' => \Illuminate\Support\Str::uuid(),
        //     'staff_id' => 'ADMIN001',
        //     'username' => 'admin',
        //     'firstname' => 'Admin',
        //     'lastname' => 'User',
        //     'email' => 'admin@example.com',
        //     'password' => Hash::make('password'),
        //     'join_date' => now(),
        // ]);

        $this->call([
            RolePermissionSeeder::class,
            LeaveSettingsSeeder::class,
            LocationSeeder::class,
            DepartmentSeeder::class,
            LeaveTypeSeeder::class,
            UserLevelSeeder::class,
            HolidaySeeder::class,
            LeaveEntitlementSeeder::class,
            ApprovalLevelSeeder::class,
            // RoleSeeder::class,
            AdminUserSeeder::class,
            NavigationSeeder::class,
            LeaveBalanceSeeder::class,
            RelationshipSeeder::class,
        ]);
    }
}
