<?php
declare(strict_types=1);

namespace App\Http\Controllers\Leave;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaveBalanceController extends Controller
{
    public function index(): Response
    {
        $balances = LeaveBalance::with(['user', 'leaveType'])
            ->when(!auth()->user()->isAdmin(), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->get();

        return Inertia::render('Leave/Balances/Index', [
            'balances' => $balances,
        ]);
    }

    public function create(): Response
    {
        $users = User::where('is_active', true)->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();

        return Inertia::render('Leave/Balances/Create', [
            'users' => $users,
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'total_days' => 'required|integer|min:0',
            'used_days' => 'required|integer|min:0',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        LeaveBalance::create($validated);

        return redirect()->route('leave.balances.index')
            ->with('success', 'Leave balance created successfully.');
    }

    public function edit(LeaveBalance $leaveBalance): Response
    {
        $users = User::where('is_active', true)->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();

        return Inertia::render('Leave/Balances/Edit', [
            'balance' => $leaveBalance,
            'users' => $users,
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function update(Request $request, LeaveBalance $leaveBalance)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'total_days' => 'required|integer|min:0',
            'used_days' => 'required|integer|min:0',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $leaveBalance->update($validated);

        return redirect()->route('leave.balances.index')
            ->with('success', 'Leave balance updated successfully.');
    }

    public function destroy(LeaveBalance $leaveBalance)
    {
        $leaveBalance->delete();

        return redirect()->route('leave.balances.index')
            ->with('success', 'Leave balance deleted successfully.');
    }
} 