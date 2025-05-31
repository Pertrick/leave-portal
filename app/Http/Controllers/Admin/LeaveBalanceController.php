<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use App\Exports\LeaveBalancesExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response as HttpResponse;
use Maatwebsite\Excel\Facades\Excel;

class LeaveBalanceController extends Controller
{
    public function index(Request $request): Response
    {
        $query = LeaveBalance::with(['user.department', 'leaveType'])
            ->when(!Auth::user()->hasRole('admin'), function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('staff_id', 'like', "%{$search}%");
                });
            })
            ->when($request->input('department'), function ($query, $department) {
                $query->whereHas('user', function ($q) use ($department) {
                    $q->where('department_id', $department);
                });
            })
            ->when($request->input('leaveType'), function ($query, $leaveType) {
                $query->where('leave_type_id', $leaveType);
            })
            ->when($request->input('year'), function ($query, $year) {
                $query->where('year', $year);
            });

        $balances = $query->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Balances/Index', [
            'balances' => $balances,
            'departments' => Department::orderBy('name')->get(),
            'leaveTypes' => LeaveType::where('is_active', true)->get(),
            'filters' => $request->only(['search', 'department', 'leaveType', 'year']),
        ]);
    }

    public function export(Request $request)
    {
        $year = $request->input('year');
        $search = $request->input('search');
        
        // Create filename based on search and year
        $fileName = "leave-balances";
        if ($search) {
            $fileName .= "-" . str_replace(' ', '-', strtolower($search));
        }
        if ($year) {
            $fileName .= "-" . $year;
        }
        $fileName .= ".xlsx";

        return Excel::download(
            new LeaveBalancesExport($request->only(['search', 'department', 'leaveType', 'year'])),
            $fileName
        );
    }
} 