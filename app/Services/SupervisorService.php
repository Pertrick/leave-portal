<?php

namespace App\Services;

use App\Models\User;
use App\Models\Supervisor;
use App\Models\DepartmentHead;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class SupervisorService
{
    public function assignSupervisor(User $user, User $supervisor, array $data = [])
    {
        return DB::transaction(function () use ($user, $supervisor, $data) {
            // Deactivate current primary supervisor if exists
            if ($data['is_primary'] ?? false) {
                $user->activeSupervisors()
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            return Supervisor::create([
                'user_id' => $user->id,
                'supervisor_id' => $supervisor->id,
                'department_id' => $user->department_id,
                'is_primary' => $data['is_primary'] ?? false,
                'start_date' => $data['start_date'] ?? now(),
                'end_date' => $data['end_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    public function deactivateSupervisor(Supervisor $supervisor, array $data = [])
    {
        return $supervisor->update([
            'is_active' => false,
            'end_date' => $data['end_date'] ?? now(),
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function assignDepartmentHead(Department $department, User $user, array $data = [])
    {
        return DB::transaction(function () use ($department, $user, $data) {
            // Deactivate current head if exists
            if (!($data['is_acting'] ?? false)) {
                $department->heads()
                    ->where('is_acting', false)
                    ->update(['end_date' => now()]);
            }

            return DepartmentHead::create([
                'department_id' => $department->id,
                'user_id' => $user->id,
                'is_acting' => $data['is_acting'] ?? false,
                'start_date' => $data['start_date'] ?? now(),
                'end_date' => $data['end_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    public function deactivateDepartmentHead(DepartmentHead $head, array $data = [])
    {
        return $head->update([
            'end_date' => $data['end_date'] ?? now(),
            'notes' => $data['notes'] ?? null,
        ]);
    }
} 