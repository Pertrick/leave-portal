<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\AccountRequest;
use App\Models\Department;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

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

            //update AccountRequest Service
            AccountRequest::processed();

            event(new Registered($user));


            return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to the portal.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Registration failed. Please try again.')
                ->withInput();
        }
    }
}
