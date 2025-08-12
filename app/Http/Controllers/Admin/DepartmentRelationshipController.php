<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\Supervisor;
use App\Models\DepartmentHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class DepartmentRelationshipController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/DepartmentRelationships', [
            'departments' => Department::all(),
        ]);
    }

    public function show(Department $department)
    {
        try {
            // Get all departments for the dropdown
            $departments = Department::orderBy('name')->get();

            $departmentHead = $department->activeHead()->with('user')->first();

            // Get unique active supervisors for this department
            $supervisors = \App\Models\Supervisor::select([
                    'supervisors.id',
                    'supervisors.supervisor_id',
                    'supervisors.start_date',
                    'supervisors.is_primary',
                    'users.firstname',
                    'users.lastname',
                    'users.email',
                    'users.designation'
                ])
                ->join('users', 'supervisors.supervisor_id', '=', 'users.id')
                ->where('supervisors.department_id', $department->id)
                ->where('supervisors.is_active', true)
                ->where('users.is_active', true)
                ->whereIn('supervisors.id', function ($query) use ($department) {
                    $query->select(DB::raw('MIN(id)'))
                        ->from('supervisors')
                        ->where('department_id', $department->id)
                        ->where('is_active', true)
                        ->groupBy('supervisor_id');
                })
                ->get()
                ->map(function ($supervisor) {
                    // Count supervised users for this supervisor
                    $supervisedCount = \App\Models\Supervisor::where('supervisor_id', $supervisor->supervisor_id)
                        ->where('is_active', true)
                        ->count();
                    
                    return [
                        'id' => $supervisor->id,
                        'supervisor_id' => $supervisor->supervisor_id,
                        'supervisor' => [
                            'id' => $supervisor->supervisor_id,
                            'firstname' => $supervisor->firstname,
                            'lastname' => $supervisor->lastname,
                            'email' => $supervisor->email,
                            'designation' => $supervisor->designation,
                        ],
                        'start_date' => $supervisor->start_date,
                        'is_primary' => $supervisor->is_primary,
                        'supervised_users_count' => $supervisedCount,
                    ];
                });

            // Get all active users in the department
            $availableUsers = User::where('department_id', $department->id)
                ->where('is_active', true)
                ->select('id', 'firstname', 'lastname', 'email', 'designation', 'department_id')
                ->orderBy('firstname')
                ->get();

            // Use the same users list for both head and supervisor selection
            $availableSupervisors = $availableUsers;

            return Inertia::render('Admin/DepartmentRelationships', [
                'departments' => $departments,
                'departmentHead' => $departmentHead,
                'supervisors' => $supervisors,
                'availableUsers' => $availableUsers,
                'availableSupervisors' => $availableSupervisors,
                'currentDepartment' => $department,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in DepartmentRelationshipController@show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading department relationships: ' . $e->getMessage());
        }
    }

    public function assignHead(Request $request, Department $department)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'start_date' => 'required|date',
                'is_acting' => 'boolean',
                'notes' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // Check if user is already a head of another department
            $existingHead = DepartmentHead::where('user_id', $validated['user_id'])
                ->whereNull('end_date')
                ->where('department_id', '!=', $department->id)
                ->first();

            if ($existingHead) {
                throw ValidationException::withMessages([
                    'user_id' => 'This user is already a head of another department.',
                ]);
            }

            // Deactivate current head if exists
            if ($department->activeHead) {
                $department->activeHead->update([
                    'end_date' => now(),
                ]);

                // Remove role from previous head
                $previousHead = $department->activeHead->user;
                $previousHead->removeRole('hod');
            }

            // Create new head
            DepartmentHead::create([
                'department_id' => $department->id,
                'user_id' => $validated['user_id'],
                'is_acting' => $validated['is_acting'] ?? false,
                'start_date' => $validated['start_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Assign role to new head
            $user = User::find($validated['user_id']);
            $user->assignRole('hod');

            // Update pending leave approvals for the department
            if ($department->activeHead) {
                $previousHead = $department->activeHead;
                // Find all pending leave approvals where the previous head was the approver
                $pendingApprovals = \App\Models\LeaveApproval::where('approver_id', $previousHead->user_id)
                    ->where('status', 'pending')
                    ->whereHas('leave', function ($query) use ($department) {
                        $query->whereHas('user', function ($q) use ($department) {
                            $q->where('department_id', $department->id);
                        });
                    })
                    ->get();

                // Update each pending approval to the new head
                foreach ($pendingApprovals as $approval) {
                    $approval->update([
                        'approver_id' => $user->id
                    ]);

                    // // If this was the current approval for the leave, update the leave record
                    // if ($approval->leave->current_approval_id === $approval->id) {
                    //     $approval->leave->update([
                    //         'current_approval_id' => $approval->id
                    //     ]);
                    // }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Department head assigned successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Error assigning department head: ' . $e->getMessage());
        }
    }

    public function deactivateHead(Department $department)
    {
        try {
            DB::beginTransaction();

            if ($department->activeHead) {
                $department->activeHead->update([
                    'is_acting' => true,
                    'end_date' => now(),
                ]);

                // Remove role
                $user = $department->activeHead->user;
                $user->removeRole('hod-manager');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Department head deactivated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deactivating department head: ' . $e->getMessage());
        }
    }

    public function assignSupervisor(Request $request, Department $department)
    {
        try {
            $validated = $request->validate([
                'supervisor_id' => 'required|exists:users,id',
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'start_date' => 'required|date',
                'is_primary' => 'boolean',
            ]);

            DB::beginTransaction();

            // Check if supervisor is already supervising these users
            $existingSupervisors = Supervisor::where('supervisor_id', $validated['supervisor_id'])
                ->whereIn('user_id', $validated['user_ids'])
                ->where('is_active', true)
                ->exists();

            if ($existingSupervisors) {
                throw ValidationException::withMessages([
                    'supervisor_id' => 'This supervisor is already supervising one or more of the selected users.',
                ]);
            }

            foreach ($validated['user_ids'] as $userId) {
                // Check if user already has a primary supervisor
                $hasPrimarySupervisor = Supervisor::where('user_id', $userId)
                    ->where('is_primary', true)
                    ->where('is_active', true)
                    ->exists();

                // Determine if this new supervisor should be primary
                $isPrimary = $validated['is_primary'] ?? false;
                
                // If user already has a primary supervisor and we're trying to make this one primary,
                // deactivate the existing primary supervisor
                if ($isPrimary && $hasPrimarySupervisor) {
                    Supervisor::where('user_id', $userId)
                        ->where('is_primary', true)
                        ->where('is_active', true)
                        ->update([
                            'is_primary' => false,
                            'end_date' => now(),
                        ]);
                }
                
                // If user doesn't have a primary supervisor and this isn't explicitly marked as secondary,
                // make this one primary
                if (!$hasPrimarySupervisor && !isset($validated['is_primary'])) {
                    $isPrimary = true;
                }

                Supervisor::create([
                    'user_id' => $userId,
                    'supervisor_id' => $validated['supervisor_id'],
                    'department_id' => $department->id,
                    'is_primary' => $isPrimary,
                    'is_active' => true,
                    'start_date' => $validated['start_date'],
                ]);
            }

            // Assign role if not already assigned
            $user = User::find($validated['supervisor_id']);
            if (!$user->hasRole('supervisor')) {
                $user->assignRole('supervisor');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Supervisor assigned successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error assigning supervisor: ' . $e->getMessage());
        }
    }

    public function deactivateSupervisor(Supervisor $supervisor)
    {
        try {
            DB::beginTransaction();

            $supervisor->update([
                'is_active' => false,
                'end_date' => now(),
            ]);

            // Remove role if no active supervised users
            if (!$supervisor->supervisor->activeSupervisedUsers()->exists()) {
                $supervisor->supervisor->removeRole('supervisor-manager');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Supervisor deactivated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deactivating supervisor: ' . $e->getMessage());
        }
    }

    public function updateHead(Request $request, Department $department)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'start_date' => 'required|date',
                'is_acting' => 'boolean',
                'notes' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // Check if user is already a head of another department
            $existingHead = DepartmentHead::where('user_id', $validated['user_id'])
                ->where('is_acting', false)
                ->whereNull('end_date')
                ->where('department_id', '!=', $department->id)
                ->first();

            if ($existingHead) {
                throw ValidationException::withMessages([
                    'user_id' => 'This user is already a head of another department.',
                ]);
            }

            // If new head is regular, deactivate both regular and acting heads
            $previousHeads = $department->activeHead()->with('user')->get();

            // Update end_date for all current heads
            $department->activeHead()->update(['end_date' => now()]);
    
            // Remove roles from previous heads
            $previousHeads->each(function ($head) {
                $head->user->removeRole('hod');
            });
            // Create new head
            DepartmentHead::create([
                'department_id' => $department->id,
                'user_id' => $validated['user_id'],
                'is_acting' => $validated['is_acting'] ?? false,
                'start_date' => $validated['start_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Assign role to new head
            $user = User::find($validated['user_id']);
            $user->assignRole('hod');

            // Update pending leave approvals for the department
            $previousHeads->each(function ($head) use ($user, $department) {
                // Find all pending leave approvals where the previous head was the approver
                $pendingApprovals = \App\Models\LeaveApproval::where('approver_id', $head->user_id)
                    ->where('status', 'pending')
                    ->whereHas('leave', function ($query) use ($department) {
                        $query->whereHas('user', function ($q) use ($department) {
                            $q->where('department_id', $department->id);
                        });
                    })
                    ->get();

                // Update each pending approval to the new head
                foreach ($pendingApprovals as $approval) {
                    $approval->update([
                        'approver_id' => $user->id
                    ]);
                }
            });

            DB::commit();

            return redirect()->back()->with('success', 'Department head updated successfully');
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating department head: ' . $e->getMessage());
        }
    }

    public function updateSupervisorUsers(Request $request, Supervisor $supervisor)
    {
        try {
            $validated = $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'action' => 'required|in:add,remove'
            ]);

            DB::beginTransaction();

            if ($validated['action'] === 'add') {
                // Add new users to supervisor
                foreach ($validated['user_ids'] as $userId) {
                    // Check if relationship already exists
                    $existingSupervisor = Supervisor::where('user_id', $userId)
                        ->where('supervisor_id', $supervisor->supervisor_id)
                        ->where('is_active', true)
                        ->first();

                    if (!$existingSupervisor) {
                        // Check if user already has a primary supervisor
                        $hasPrimarySupervisor = Supervisor::where('user_id', $userId)
                            ->where('is_primary', true)
                            ->where('is_active', true)
                            ->exists();

                        // If user doesn't have a primary supervisor, make this one primary
                        // Otherwise, make it secondary
                        $isPrimary = !$hasPrimarySupervisor;

                        Supervisor::create([
                            'user_id' => $userId,
                            'supervisor_id' => $supervisor->supervisor_id,
                            'department_id' => $supervisor->department_id,
                            'is_primary' => $isPrimary,
                            'is_active' => true,
                            'start_date' => now(),
                        ]);
                    }
                }
            } elseif ($validated['action'] === 'remove') {
                // Remove users from supervisor
                Supervisor::where('supervisor_id', $supervisor->supervisor_id)
                    ->whereIn('user_id', $validated['user_ids'])
                    ->where('is_active', true)
                    ->update([
                        'is_active' => false,
                        'end_date' => now(),
                    ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Supervisor users updated successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating supervisor users: ' . $e->getMessage());
        }
    }

    public function getSupervisedUsers($supervisorUserId)
    {
        try {
            // Get all users who are supervised by this supervisor
            $supervisedUsers = User::whereIn('id', function ($query) use ($supervisorUserId) {
                $query->select('user_id')
                    ->from('supervisors')
                    ->where('supervisor_id', $supervisorUserId)
                    ->where('is_active', true);
            })
            ->select('id', 'firstname', 'lastname', 'email', 'designation')
            ->get();

            // Also get the supervisor relationship details for each user
            $supervisedUsersWithDetails = $supervisedUsers->map(function ($user) use ($supervisorUserId) {
                $supervisorRelationship = \App\Models\Supervisor::where('user_id', $user->id)
                    ->where('supervisor_id', $supervisorUserId)
                    ->where('is_active', true)
                    ->first();
                
                return [
                    'id' => $user->id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'designation' => $user->designation,
                    'is_primary' => $supervisorRelationship ? $supervisorRelationship->is_primary : false,
                    'start_date' => $supervisorRelationship ? $supervisorRelationship->start_date : null,
                ];
            });

            return response()->json($supervisedUsersWithDetails);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching supervised users: ' . $e->getMessage()], 500);
        }
    }
}