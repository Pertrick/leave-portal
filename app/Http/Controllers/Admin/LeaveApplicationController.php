<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Leave;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with(['user.department', 'leaveType'])
            ->when($request->department, function ($query, $department) {
                return $query->whereHas('user', function ($q) use ($department) {
                    $q->where('department_id', $department);
                });
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->start_date, function ($query, $date) {
                return $query->where('start_date', '>=', $date);
            })
            ->when($request->end_date, function ($query, $date) {
                return $query->where('end_date', '<=', $date);
            });

        $applications = $query->latest()->paginate(10);
        $departments = Department::all();

        return Inertia::render('Admin/LeaveApplications', [
            'applications' => $applications,
            'departments' => $departments,
            'filters' => $request->only(['department', 'status', 'start_date', 'end_date'])
        ]);
    }

    public function approve(Leave $leave)
    {
        try {
            $leave->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
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
                'rejected_by' => auth()->id(),
                'rejected_at' => now()
            ]);

            return redirect()->back()->with('success', 'Leave application rejected successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error rejecting leave application: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = Leave::with(['user.department', 'leaveType'])
            ->when($request->department, function ($query, $department) {
                return $query->whereHas('user', function ($q) use ($department) {
                    $q->where('department_id', $department);
                });
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->start_date, function ($query, $date) {
                return $query->where('start_date', '>=', $date);
            })
            ->when($request->end_date, function ($query, $date) {
                return $query->where('end_date', '<=', $date);
            });

        $applications = $query->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leave-applications.csv"',
        ];

        $callback = function() use ($applications) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Employee Name',
                'Department',
                'Leave Type',
                'Start Date',
                'End Date',
                'Status',
                'Applied At',
                'Approved/Rejected At'
            ]);

            // Add data
            foreach ($applications as $application) {
                fputcsv($file, [
                    $application->user->firstname . ' ' . $application->user->lastname,
                    $application->user->department->name,
                    $application->leaveType->name,
                    $application->start_date,
                    $application->end_date,
                    $application->status,
                    $application->created_at,
                    $application->approved_at ?? $application->rejected_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 