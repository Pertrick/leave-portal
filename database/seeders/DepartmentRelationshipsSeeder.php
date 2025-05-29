<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use App\Models\Supervisor;
use App\Models\DepartmentHead;
use Illuminate\Database\Seeder;

class DepartmentRelationshipsSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();
        
        foreach ($departments as $department) {
            // Get users in this department
            $departmentUsers = $department->users()->get();
            
            if ($departmentUsers->isEmpty()) {
                continue;
            }

            $potentialHead = $departmentUsers->where('user_level_id', 3)->first() 
                ?? $departmentUsers->where('user_level_id', 2)->first()
                ?? $departmentUsers->first();

            if ($potentialHead) {
                DepartmentHead::create([
                    'department_id' => $department->id,
                    'user_id' => $potentialHead->id,
                    'is_acting' => false,
                    'start_date' => now(),
                ]);

                $potentialHead->assignRole('hod');
            }

            // Assign supervisors (prefer user_level_id 2)
            $potentialSupervisors = $departmentUsers
                ->where('user_level_id', 2)
                ->take(2);

            foreach ($potentialSupervisors as $supervisor) {
                // Get users to supervise (excluding the supervisor themselves)
                $usersToSupervise = $departmentUsers
                    ->where('id', '!=', $supervisor->id)
                    ->where('user_level_id', 1);

                foreach ($usersToSupervise as $user) {
                    Supervisor::create([
                        'user_id' => $user->id,
                        'supervisor_id' => $supervisor->id,
                        'department_id' => $department->id,
                        'is_primary' => true,
                        'is_active' => true,
                        'start_date' => now(),
                    ]);
                }

                // Assign supervisor role
                $supervisor->assignRole('supervisor');
            }
        }
    }
} 