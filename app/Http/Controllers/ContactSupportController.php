<?php

namespace App\Http\Controllers;

use App\Models\ContactSupport;
use App\Models\User;
use App\Notifications\NewSupportRequest;
use App\Notifications\SupportRequestUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class ContactSupportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = ContactSupport::with(['user', 'responder'])
            ->when($user->hasAnyRole(['admin', 'hr']), function ($query) {
                // Admin/HR can see all requests
                return $query;
            }, function ($query) use ($user) {
                // Regular users can only see their own requests
                return $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc');

        $requests = $query->paginate(15);

        return Inertia::render('ContactSupport/Index', [
            'requests' => $requests,
            'categories' => ContactSupport::CATEGORIES,
            'priorities' => ContactSupport::PRIORITIES,
            'statuses' => ContactSupport::STATUSES,
        ]);
    }

    public function create()
    {
        return Inertia::render('ContactSupport/Create', [
            'categories' => ContactSupport::CATEGORIES,
            'priorities' => ContactSupport::PRIORITIES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'category' => 'required|in:' . implode(',', array_keys(ContactSupport::CATEGORIES)),
            'priority' => 'required|in:' . implode(',', array_keys(ContactSupport::PRIORITIES)),
        ]);

        $supportRequest = ContactSupport::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        // Notify admins about new support request
        $admins = User::role(['admin', 'hr'])->get();
        Notification::send($admins, new NewSupportRequest($supportRequest));

        return redirect()->route('contact-support.index')
            ->with('success', 'Your support request has been submitted successfully.');
    }

    public function show(ContactSupport $contactSupport)
    {
        $user = auth()->user();
        
        // Check if user can view this request
        if (!$user->hasAnyRole(['admin', 'hr']) && $contactSupport->user_id !== $user->id) {
            abort(403);
        }

        $contactSupport->load(['user', 'responder']);

        return Inertia::render('ContactSupport/Show', [
            'request' => $contactSupport,
            'categories' => ContactSupport::CATEGORIES,
            'priorities' => ContactSupport::PRIORITIES,
            'statuses' => ContactSupport::STATUSES,
        ]);
    }

    public function respond(Request $request, ContactSupport $contactSupport)
    {
        $this->authorize('manage_support_requests');

        $validated = $request->validate([
            'admin_response' => 'required|string|max:2000',
            'status' => 'required|in:' . implode(',', array_keys(ContactSupport::STATUSES)),
        ]);

        $contactSupport->update([
            'admin_response' => $validated['admin_response'],
            'status' => $validated['status'],
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        // Notify user about the response
        $contactSupport->user->notify(new SupportRequestUpdated($contactSupport));

        return redirect()->back()
            ->with('success', 'Response sent successfully.');
    }

    public function updateStatus(Request $request, ContactSupport $contactSupport)
    {
        $this->authorize('manage_support_requests');

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(ContactSupport::STATUSES)),
        ]);

        $contactSupport->update([
            'status' => $validated['status'],
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Status updated successfully.');
    }
} 