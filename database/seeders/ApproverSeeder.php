<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\ApprovalLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ApproverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all departments
        $departments = Department::all();
        
        // Get all approval levels
        $approvalLevels = ApprovalLevel::where('is_active', true)
            ->orderBy('level')
            ->get();

        foreach ($departments as $department) {
            foreach ($approvalLevels as $level) {
                // Create or get the role for this approval level
                $role = Role::firstOrCreate(
                    ['name' => $level->role_name],
                    ['name' => $level->role_name]
                );

                // Create an approver for this department and level
                $approver = User::firstOrCreate(
                    [
                        'email' => fake()->email(),
                    ],
                    [
                        'staff_id' => fake()->numberBetween(100000, 999999),
                        'firstname' => fake()->firstName(),
                        'lastname' => fake()->lastName(),
                        'username' => fake()->userName(),
                        'user_level_id' => $level->level == 1 ? 3 : 2,
                        'password' => Hash::make('password'),
                        'department_id' => $department->id,
                        'is_active' => true,
                        'join_date' => fake()->dateTimeBetween('-5 years', 'now'),
                        'email_verified_at' => now(),
                        
                    ]
                );

                // Assign the role to the approver if not already assigned
                if (!$approver->roles()->where('name', $role->name)->exists()) {
                    $approver->roles()->attach($role->id);
                }

                $this->command->info("Created approver for {$department->name} at level {$level->name}");
            }
        }
    }
} 