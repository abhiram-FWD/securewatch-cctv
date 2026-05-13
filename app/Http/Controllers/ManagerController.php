<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Camera;
use App\Models\Alert;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ManagerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        $my_cameras = $user->cameras()->where('status', 'active')->get();
        $camera_ids = $my_cameras->pluck('id')->toArray();
        
        $open_alerts_query = Alert::whereIn('camera_id', $camera_ids)->where('status', 'open');
        $open_alerts_count = $open_alerts_query->count();
        
        $recent_alerts = Alert::whereIn('camera_id', $camera_ids)
                            ->with(['camera', 'raisedBy'])
                            ->latest()
                            ->take(5)
                            ->get();
                            
        $resolved_today = Alert::whereIn('camera_id', $camera_ids)
                            ->where('status', 'resolved')
                            ->whereDate('updated_at', Carbon::today())
                            ->count();
                            
        $manager = auth()->user();
        $cameras = $manager->cameras()
            ->where('status', 'active')
            ->get();
        
        $on_duty_guards = User::where('role', 'guard')
                              ->where('status', 'active')
                              ->get()
                              ->filter(fn($guard) => $guard->isOnDuty())
                              ->count();

        $unread_messages = Message::where(function($q) use ($user) {
            $q->where('to_user_id', $user->id)
              ->orWhere('to_role', 'manager')
              ->orWhere('to_role', 'all');
        })->where('is_read', false)->count();

        return view('manager.dashboard', compact(
            'my_cameras', 'open_alerts_count', 'recent_alerts', 
            'resolved_today', 'cameras', 'on_duty_guards', 'unread_messages'
        ));
    }
}
