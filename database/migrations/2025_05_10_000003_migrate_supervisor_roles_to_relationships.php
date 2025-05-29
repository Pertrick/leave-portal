<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Supervisor;
use App\Models\DepartmentHead;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up()
    {
        DB::transaction(function () {
            // Create roles if they don't exist
            $supervisorRole = Role::firstOrCreate(['name' => 'supervisor']);
            $hodRole = Role::firstOrCreate(['name' => 'hod']);
            
            // Migrate supervisors
            $supervisors = User::role('supervisor')->get();
            
            foreach ($supervisors as $supervisor) {
                $department = $supervisor->department;
                if (!$department) continue;

                // Get users in their department
                $departmentUsers = $department->users()
                    ->where('id', '!=', $supervisor->id)
                    ->get();
                    
                // Create supervisor relationships
                foreach ($departmentUsers as $user) {
                    Supervisor::create([
                        'user_id' => $user->id,
                        'supervisor_id' => $supervisor->id,
                        'department_id' => $department->id,
                        'is_primary' => true,
                        'is_active' => true,
                        'start_date' => now(),
                    ]);
                }
            }

            // Migrate HODs
            $hods = User::role('HOD')->get();
            
            foreach ($hods as $hod) {
                $department = $hod->department;
                if (!$department) continue;

                DepartmentHead::create([
                    'department_id' => $department->id,
                    'user_id' => $hod->id,
                    'is_acting' => false,
                    'start_date' => now(),
                ]);
            }
        });
    }

    public function down()
    {
        // No down migration as this is a one-way migration
    }
}; 