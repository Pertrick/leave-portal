<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactSupport;
use App\Models\User;
use App\Notifications\NewSupportRequest;
use App\Notifications\SupportRequestUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class ContactSupportController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactSupport::with(['user', 'responder', 'user.department'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search by subject or message
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/ContactSupport/Index', [
            'requests' => $requests,
            'categories' => ContactSupport::CATEGORIES,
            'priorities' => ContactSupport::PRIORITIES,
            'statuses' => ContactSupport::STATUSES,
            'users' => User::orderBy('firstname')->get(['id', 'firstname', 'lastname', 'email']),
            'filters' => $request->only(['status', 'priority', 'category', 'user_id', 'search']),
        ]);
    }

    public function show(ContactSupport $contactSupport)
    {
        $contactSupport->load(['user', 'responder', 'user.department']);

        return Inertia::render('Admin/ContactSupport/Show', [
            'request' => $contactSupport,
            'categories' => ContactSupport::CATEGORIES,
            'priorities' => ContactSupport::PRIORITIES,
            'statuses' => ContactSupport::STATUSES,
        ]);
    }

    public function respond(Request $request, ContactSupport $contactSupport)
    {
        $validated = $request->validate([
            'admin_response' => 'required|string|max:2000',
            'status' => 'required|in:' . implode(',', array_keys(ContactSupport::STATUSES)),
            'priority' => 'required|in:' . implode(',', array_keys(ContactSupport::PRIORITIES)),
        ]);

        $contactSupport->update([
            'admin_response' => $validated['admin_response'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
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

    public function updatePriority(Request $request, ContactSupport $contactSupport)
    {
        $validated = $request->validate([
            'priority' => 'required|in:' . implode(',', array_keys(ContactSupport::PRIORITIES)),
        ]);

        $contactSupport->update([
            'priority' => $validated['priority'],
        ]);

        return redirect()->back()
            ->with('success', 'Priority updated successfully.');
    }

    public function assign(Request $request, ContactSupport $contactSupport)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $contactSupport->update([
            'assigned_to' => $validated['assigned_to'],
        ]);

        return redirect()->back()
            ->with('success', 'Request assigned successfully.');
    }

    public function close(ContactSupport $contactSupport)
    {
        $contactSupport->update([
            'status' => 'closed',
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Request closed successfully.');
    }

    public function reopen(ContactSupport $contactSupport)
    {
        $contactSupport->update([
            'status' => 'open',
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Request reopened successfully.');
    }

    public function destroy(ContactSupport $contactSupport)
    {
        $contactSupport->delete();

        return redirect()->route('admin.contact-support.index')
            ->with('success', 'Support request deleted successfully.');
    }
} 