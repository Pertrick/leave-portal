<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function getSupervisedUsers(Supervisor $supervisor)
    {
        $users = User::whereIn('id', 
            Supervisor::where('supervisor_id', $supervisor->supervisor_id)
                ->where('is_active', true)
                ->pluck('user_id')
        )
        ->select('id', 'firstname', 'lastname', 'email', 'designation')
        ->get();

        return response()->json($users);
    }
} 