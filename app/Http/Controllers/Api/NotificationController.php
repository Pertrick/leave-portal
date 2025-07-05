<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Get user's notifications
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $page = $request->get('page', 1);
        $perPage = 20;
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
            
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'total_count' => $totalCount
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, DatabaseNotification $notification)
    {
        $user = $request->user();
        
        // Ensure the notification belongs to the authenticated user
        if ($notification->notifiable_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        
        $user->unreadNotifications()->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Delete a specific notification
     */
    public function destroy(Request $request, DatabaseNotification $notification)
    {
        $user = $request->user();
        
        // Ensure the notification belongs to the authenticated user
        if ($notification->notifiable_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Delete all notifications for the user
     */
    public function deleteAll(Request $request)
    {
        $user = $request->user();
        
        $user->notifications()->delete();
        
        return response()->json(['success' => true]);
    }
} 