<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Exports\LeaveApplicationsExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Maatwebsite\Excel\Facades\Excel;

class LeaveApplicationController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Leave::select([
                'leaves.*',
                'users.firstname',
                'users.lastname',
                'users.email',
                'departments.name as department_name',
                'leave_types.name as leave_type_name'
            ])
            ->join('users', 'leaves.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->join('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
            ->with(['approvals' => function ($query) {
                $query->with(['approver:id,firstname,lastname,email', 'approvalLevel:id,name,level'])
                    ->orderBy('sequence');
            }])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where(function ($query) use ($search) {
                    $query->where('users.firstname', 'like', "%{$search}%")
                        ->orWhere('users.lastname', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhere('leaves.reason', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('leaves.status', $request->input('status'));
            })
            ->when($request->filled('department_id'), function ($q) use ($request) {
                $q->where('users.department_id', $request->input('department_id'));
            })
            ->when($request->filled('leave_type_id'), function ($q) use ($request) {
                $q->where('leaves.leave_type_id', $request->input('leave_type_id'));
            })
            ->when($request->filled('date_range'), function ($q) use ($request) {
                $dates = explode(' to ', $request->input('date_range'));
                if (count($dates) === 2) {
                    $q->where(function ($query) use ($dates) {
                        $query->whereBetween('leaves.start_date', $dates)
                            ->orWhereBetween('leaves.end_date', $dates);
                    });
                }
            })
            ->when($request->filled('approver_status'), function ($q) use ($request) {
                $status = $request->input('approver_status');
                $q->whereHas('approvals', function ($query) use ($status) {
                    $query->where('status', $status);
                });
            });

        $leaves = $query->latest('leaves.created_at')->paginate(10)->withQueryString();

        return Inertia::render('Admin/LeaveApplications/Index', [
            'leaves' => $leaves,
            'departments' => Department::all(),
            'leaveTypes' => LeaveType::all(),
            'filters' => $request->only(['search', 'status', 'department_id', 'leave_type_id', 'date_range', 'approver_status'])
        ]);
    }

    public function show(Leave $leave): Response
    {
        $leave = Leave::select([
                'leaves.*',
                'users.firstname',
                'users.lastname',
                'users.email',
                'departments.name as department_name',
                'leave_types.name as leave_type_name'
            ])
            ->join('users', 'leaves.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->join('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
            ->where('leaves.id', $leave->id)
            ->with(['approvals' => function ($query) {
                $query->with(['approver:id,firstname,lastname,email', 'approvalLevel:id,name,level'])
                    ->orderBy('sequence');
            }])
            ->first();


        return Inertia::render('Admin/LeaveApplications/Show', [
            'leave' => $leave
        ]);
    }

    public function approve(Leave $leave)
    {
        try {
            $leave->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            return redirect()->back()->with('success', 'Leave application approved successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error approving leave application: ' . $e->getMessage());
        }
    }

    public function reject(Leave $leave)
    {
        try {
            $leave->update([
                'status' => 'rejected',
                'rejected_by' => Auth::id(),
                'rejected_at' => now()
            ]);

            return redirect()->back()->with('success', 'Leave application rejected successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error rejecting leave application: ' . $e->getMessage());
        }
    }

    public function export(Request $request): BinaryFileResponse
    {
        $query = Leave::select([
                'leaves.*',
                'users.firstname',
                'users.lastname',
                'users.email',
                'departments.name as department_name',
                'leave_types.name as leave_type_name'
            ])
            ->join('users', 'leaves.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->join('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
            ->with(['approvals' => function ($query) {
                $query->with(['approver:id,firstname,lastname,email', 'approvalLevel:id,name,level'])
                    ->orderBy('sequence');
            }])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where(function ($query) use ($search) {
                    $query->where('users.firstname', 'like', "%{$search}%")
                        ->orWhere('users.lastname', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhere('leaves.reason', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('leaves.status', $request->input('status'));
            })
            ->when($request->filled('department_id'), function ($q) use ($request) {
                $q->where('users.department_id', $request->input('department_id'));
            })
            ->when($request->filled('leave_type_id'), function ($q) use ($request) {
                $q->where('leaves.leave_type_id', $request->input('leave_type_id'));
            })
            ->when($request->filled('date_range'), function ($q) use ($request) {
                $dates = explode(' to ', $request->input('date_range'));
                if (count($dates) === 2) {
                    $q->where(function ($query) use ($dates) {
                        $query->whereBetween('leaves.start_date', $dates)
                            ->orWhereBetween('leaves.end_date', $dates);
                    });
                }
            })
            ->when($request->filled('approver_status'), function ($q) use ($request) {
                $status = $request->input('approver_status');
                $q->whereHas('approvals', function ($query) use ($status) {
                    $query->where('status', $status);
                });
            });

        $leaves = $query->get();

        return Excel::download(new LeaveApplicationsExport($leaves), 'leave_applications_' . now()->format('Y-m-d_His') . '.xlsx');
    }
} 