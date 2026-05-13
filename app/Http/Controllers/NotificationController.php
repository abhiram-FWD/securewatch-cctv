<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNew()
    {
        if (!Auth::check()) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->take(20)
            ->get()
            ->map(fn($n) => [
                'id'       => $n->id,
                'message'  => $n->message,
                'type'     => $n->type,
                'is_read'  => $n->is_read,
                'time_ago' => $n->created_at->diffForHumans(),
            ]);

        $unread_count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unread_count,
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
