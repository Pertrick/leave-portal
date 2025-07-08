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
        
        $requests = ContactSupport::with(['responder'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

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
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif,txt,zip,rar', // 10MB max
        ]);

        $supportRequest = ContactSupport::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-attachments', 'public');
                $attachments[] = [
                    'filename' => $file->hashName(),
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $supportRequest->update(['attachments' => $attachments]);
        }

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
        if ($contactSupport->user_id !== $user->id) {
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

    public function edit(ContactSupport $contactSupport)
    {
        $user = auth()->user();
        
        // Check if user can edit this request
        if ($contactSupport->user_id !== $user->id) {
            abort(403);
        }

        // Only allow editing if request is still open
        if ($contactSupport->status !== 'open') {
            return redirect()->route('contact-support.show', $contactSupport)
                ->with('error', 'Cannot edit a closed or in-progress request.');
        }

        return Inertia::render('ContactSupport/Edit', [
            'request' => $contactSupport,
            'categories' => ContactSupport::CATEGORIES,
            'priorities' => ContactSupport::PRIORITIES,
        ]);
    }

    public function update(Request $request, ContactSupport $contactSupport)
    {
        $user = auth()->user();
        
        // Check if user can update this request
        if ($contactSupport->user_id !== $user->id) {
            abort(403);
        }

        // Only allow updates if request is still open
        if ($contactSupport->status !== 'open') {
            return redirect()->back()
                ->with('error', 'Cannot update a closed or in-progress request.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'category' => 'required|in:' . implode(',', array_keys(ContactSupport::CATEGORIES)),
            'priority' => 'required|in:' . implode(',', array_keys(ContactSupport::PRIORITIES)),
        ]);

        $contactSupport->update([
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
        ]);

        return redirect()->back()
            ->with('success', 'Support request updated successfully.');
    }

    public function destroy(ContactSupport $contactSupport)
    {
        $user = auth()->user();
        
        // Check if user can delete this request
        if ($contactSupport->user_id !== $user->id) {
            abort(403);
        }

        // Only allow deletion if request is still open
        if ($contactSupport->status !== 'open') {
            return redirect()->back()
                ->with('error', 'Cannot delete a closed or in-progress request.');
        }

        $contactSupport->delete();

        return redirect()->route('contact-support.index')
            ->with('success', 'Support request deleted successfully.');
    }

    public function downloadAttachment(ContactSupport $contactSupport, $filename)
    {
        $user = auth()->user();
        
        // Check if user can download this attachment
        if ($contactSupport->user_id !== $user->id && !$user->hasAnyRole(['admin', 'hr'])) {
            abort(403);
        }

        // Find the attachment
        $attachment = collect($contactSupport->attachments)->firstWhere('filename', $filename);
        
        if (!$attachment) {
            abort(404);
        }

        $filePath = storage_path('app/public/' . $attachment['file_path']);
        
        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, $attachment['original_name']);
    }
} 