<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\UserLevel;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\LeaveBalance;
use App\Models\LeaveBalanceAuditLog;
use App\Models\AccountRequest;

class StaffController extends Controller
{
    use AuthorizesRequests;

    public function list()
    {
        $query = User::with(['department', 'userLevel', 'roles']);

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Department filter
        if (request('department_id')) {
            $query->where('department_id', request('department_id'));
        }

        // User level filter
        if (request('user_level_id')) {
            $query->where('user_level_id', request('user_level_id'));
        }

        // Status filter
        if (request()->has('is_active') && request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }

        $staff = $query->orderBy('firstname')->paginate(10);

        return Inertia::render('Staff/List', [
            'staff' => $staff,
            'departments' => Department::orderBy('name')->get(),
            'userLevels' => UserLevel::orderBy('name')->get(),
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Staff/Show', [
            'staff' => $user->load(['department', 'userLevel', 'roles', 'leaveBalances.leaveType']),
        ]);
    }

    public function edit(User $user)
    {
        $this->authorize('manage-users');

        $roles = \Spatie\Permission\Models\Role::whereNotIn('name', ['supervisor', 'hod','employee'])->get();

        return Inertia::render('Staff/Edit', [
            'staff' => $user->load('roles'),
            'departments' => Department::orderBy('name')->get(),
            'userLevels' => UserLevel::orderBy('name')->get(),
            'roles' => $roles,
        ]);
    }

    public function update(User $user)
    {
        $this->authorize('manage-users');

        $validated = request()->validate([
            'staff_id' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'department_id' => ['required', 'exists:departments,id'],
            'user_level_id' => ['required', 'exists:user_levels,id'],
            'designation' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'staff_id' => $validated['staff_id'],
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
            'user_level_id' => $validated['user_level_id'],
            'designation' => $validated['designation'],
        ]);

        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('staff.list')
            ->with('success', 'Staff details updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        $this->authorize('manage-users');

        $user->update([
            'is_active' => !$user->is_active
        ]);

        return back()->with('success', 'Staff status updated successfully.');
    }

    public function leaveBalances(User $staff)
    {
       // $this->authorize('manage-leave-balances');

        $staff->load(['leaveBalances.leaveType', 'leaveBalances.auditLogs.adjustedBy', 'leaveBalances.auditLogs.leaveBalance.leaveType']);

        return Inertia::render('Staff/LeaveBalance', [
            'staff' => $staff,
            'auditLogs' => $staff->leaveBalances->flatMap->auditLogs->sortByDesc('created_at'),
        ]);
    }

    public function updateLeaveBalances(Request $request, User $staff)
    {
        // $this->authorize('manage-leave-balances');

        $validated = $request->validate([
            'balances' => 'required|array',
            'balances.*.id' => 'required|exists:leave_balances,id',
            'balances.*.total_entitled_days' => 'required|numeric|min:0',
            'balances.*.days_taken' => 'required|numeric|min:0',
            'balances.*.days_carried_forward' => 'required|numeric|min:0',
            'reason' => 'required|string|min:10',
        ]);

        DB::transaction(function () use ($validated, $staff) {
            foreach ($validated['balances'] as $balanceData) {
                $balance = LeaveBalance::findOrFail($balanceData['id']);
                
                // Verify the balance belongs to the staff member
                if ($balance->user_id !== $staff->id) {
                    throw new AuthorizationException('This leave balance does not belong to the specified staff member.');
                }

                $previousBalance = $balance->days_remaining;
                $newBalance = $balanceData['total_entitled_days'] - $balanceData['days_taken'];
                
                // Only update and create audit log if there are actual changes
                if ($balance->total_entitled_days != $balanceData['total_entitled_days'] ||
                    $balance->days_taken != $balanceData['days_taken'] ||
                    $balance->days_carried_forward != $balanceData['days_carried_forward']) {
                    
                    // Update the balance
                    $balance->update([
                        'total_entitled_days' => $balanceData['total_entitled_days'],
                        'days_taken' => $balanceData['days_taken'],
                        'days_carried_forward' => $balanceData['days_carried_forward'],
                        'days_remaining' => $newBalance
                    ]);

                    // Create audit log only if there are changes
                    LeaveBalanceAuditLog::create([
                        'leave_balance_id' => $balance->id,
                        'adjusted_by_id' => auth()->id(),
                        'previous_balance' => $previousBalance,
                        'new_balance' => $newBalance,
                        'reason' => $validated['reason'],
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Leave balances updated successfully.');
    }

    public function pendingLeaveAccounts()
    {
        // $this->authorize('manage-users');

        $query = AccountRequest::with(['processor'])
            ->where('status', config('account.request_status.pending'))
            ->orderBy('created_at', 'desc');

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where('staff_id', 'like', "%{$search}%");
        }

        $requests = $query->paginate(10);

        return Inertia::render('Staff/PendingLeaveAccounts', [
            'requests' => $requests,
        ]);
    }
} 