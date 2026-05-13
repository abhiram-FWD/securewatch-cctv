<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Camera;
use App\Models\Alert;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GuardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        $active_cameras = Camera::where('status', 'active')->get();
        
        $open_alerts = Alert::where('status', 'open')
                            ->with(['camera'])
                            ->latest()
                            ->take(5)
                            ->get();
                            
        $resolved_today = Alert::where('resolved_by', $user->id)
                              ->where('status', 'resolved')
                              ->whereDate('updated_at', Carbon::today())
                              ->count();
                              
        $unread_messages = Message::where(function($q) use ($user) {
            $q->where('to_user_id', $user->id)
              ->orWhere('to_role', 'guard')
              ->orWhere('to_role', 'all');
        })->where('is_read', false)->count();
        
        $unread_notifications = Notification::where('user_id', $user->id)
                                            ->where('is_read', false)
                                            ->count();

        return view('guard.dashboard', compact(
            'active_cameras', 'open_alerts', 'resolved_today', 
            'unread_messages', 'unread_notifications'
        ));
    }
}
