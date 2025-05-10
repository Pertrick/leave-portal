<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AccountRequestRequest;
use App\Models\AccountRequest;
use App\Models\User;
use App\Notifications\NewAccountRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class ContactHRController extends Controller
{
    public function __construct() {}

    public function show()
    {
        return Inertia::render('auth/ContactHR');
    }

    public function submit(AccountRequestRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Create account request
            $accountRequest = AccountRequest::create([
                'staff_id' => $request->validated('staff_id'),
                'notes' => $request->validated('notes'),
                'status' => 'pending'
            ]);

            // Send notification to HR users
            $admin = User::role('Admin')->get();
            Notification::send($admin, new NewAccountRequest($accountRequest));

            DB::commit();

            return back()->with('status', 'Your request has been submitted. HR will contact you soon.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Failed to submit request. Please try again later.'
            ]);
        }
    }
} 