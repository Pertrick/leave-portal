<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get user's notifications
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
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
        } catch (\Exception $e) {
            Log::error('Notification API error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'exception' => $e
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, DatabaseNotification $notification)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            // Ensure the notification belongs to the authenticated user
            if ($notification->notifiable_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $notification->markAsRead();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Mark as read error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $user->unreadNotifications()->update(['read_at' => now()]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Mark all as read error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Delete a specific notification
     */
    public function destroy(Request $request, DatabaseNotification $notification)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            // Ensure the notification belongs to the authenticated user
            if ($notification->notifiable_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $notification->delete();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Delete notification error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Delete all notifications for the user
     */
    public function deleteAll(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $user->notifications()->delete();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Delete all notifications error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
} 