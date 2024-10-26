<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $req, User $user)
    {        
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'read' => $notification->read,
                'type' => $notification->type,
                'created_at' => $notification->created_at,
                'author' => $notification->author,
                'notifiable' => $notification->notifiable
            ];
        })->toArray();
    
        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function destroy(Request $req, User $user, Notification $notification)
    {
        $notification->delete();

        return response()->json([
            'message' => 'Notif deleted successfully.'
        ]);
    }

    public function markAsRead(Request $res, User $user)
    {
        foreach ($user->notifications as $notification) {
            $notification->update([
                'read' => true
            ]);
        }; 

        return response()->json([
            'message' => 'Notif mark as read successfully.'
        ]);
    }
}
