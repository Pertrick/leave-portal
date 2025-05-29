<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Services\SupervisorService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentHeadController extends Controller
{
    protected $supervisorService;

    public function __construct(SupervisorService $supervisorService)
    {
        $this->middleware('permission:manage-hods');
        $this->supervisorService = $supervisorService;
    }

    public function index()
    {
        $departments = Department::with(['activeHead.user', 'actingHead.user'])
            ->get();

        return Inertia::render('DepartmentHeads/Index', [
            'departments' => $departments
        ]);
    }

    public function assign(Request $request, Department $department)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_acting' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string'
        ]);

        $user = User::findOrFail($request->user_id);
        
        $this->supervisorService->assignDepartmentHead($department, $user, $request->all());

        return redirect()->back()->with('success', 'Department head assigned successfully');
    }

    public function deactivate(Request $request, Department $department)
    {
        $request->validate([
            'end_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $head = $department->activeHead();
        if ($head) {
            $this->supervisorService->deactivateDepartmentHead($head, $request->all());
        }

        return redirect()->back()->with('success', 'Department head deactivated successfully');
    }
} 