<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\UserLevel;
use App\Events\NewAccount;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\AccountRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\RegisterRequest;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register', [
            'departments' => Department::select('id', 'name')->get(),
            'userLevels' => UserLevel::select('id', 'name')->get(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $user = User::create([
                'staff_id' => $validated['staff_id'],
                'username' => $validated['username'],
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'department_id' => $validated['department_id'],
                'user_level_id' => $validated['user_level_id'],
                'designation' => $validated['designation'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'dob' => $validated['dob'] ?? null,
                'join_date' => $validated['join_date'],
                'password' => Hash::make($validated['password']),
            ]);

            $employeeRole = Role::firstOrCreate(['name' => 'Employee']);
            $user->assignRole($employeeRole);

            event(new NewAccount($user, $validated['password']));

            AccountRequest::processed($validated['staff_id'], auth()->id());
            
            DB::commit();

            return redirect()->back()->with('success', 'Account Created Successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Registration failed. Please try again.')
                ->withInput();
        }
    }
}
