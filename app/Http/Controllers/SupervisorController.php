<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supervisor;
use App\Services\SupervisorService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupervisorController extends Controller
{
    protected $supervisorService;

    public function __construct(SupervisorService $supervisorService)
    {
        // $this->middleware('permission:manage-supervisors');
        $this->supervisorService = $supervisorService;
    }

    public function index()
    {
        $users = User::with(['department', 'activeSupervisors.supervisor'])
            ->whereHas('activeSupervisors')
            ->orWhereHas('supervisedUsers')
            ->get();

        return Inertia::render('Supervisors/Index', [
            'users' => $users
        ]);
    }

    public function assign(Request $request, User $user)
    {
        $request->validate([
            'supervisor_id' => 'required|exists:users,id',
            'is_primary' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string'
        ]);

        $supervisor = User::findOrFail($request->supervisor_id);
        
        $this->supervisorService->assignSupervisor($user, $supervisor, $request->all());

        return redirect()->back()->with('success', 'Supervisor assigned successfully');
    }

    public function deactivate(Request $request, Supervisor $supervisor)
    {
        $request->validate([
            'end_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $this->supervisorService->deactivateSupervisor($supervisor, $request->all());

        return redirect()->back()->with('success', 'Supervisor deactivated successfully');
    }

    public function getSupervisedUsers(Supervisor $supervisor)
    {
        $users = User::whereIn('id', 
            Supervisor::where('supervisor_id', $supervisor->supervisor_id)
                ->where('is_active', true)
                ->pluck('user_id')
        )
        ->select('id', 'firstname', 'lastname', 'email', 'designation')
        ->get();

        dd($users);

        return response()->json($users);
    }
} 