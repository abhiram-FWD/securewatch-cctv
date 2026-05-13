<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alert;

class AlertController extends Controller
{
    public function adminIndex(Request $request)
    {
        $query = Alert::with(['camera', 'raisedBy', 'resolvedBy']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59']);
        }

        $alerts = $query->latest()->paginate(10);

        return view('admin.alerts.index', compact('alerts'));
    }

    public function managerIndex(Request $request)
    {
        $cameraIds = \Illuminate\Support\Facades\Auth::user()->cameras()->pluck('cameras.id')->toArray();
        $query = Alert::with(['camera', 'raisedBy', 'resolvedBy'])->whereIn('camera_id', $cameraIds);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59']);
        }

        $alerts = $query->orderBy('is_emergency', 'desc')
                       ->latest()
                       ->paginate(10);

        return view('manager.alerts.index', compact('alerts'));
    }

    public function guardIndex(Request $request)
    {
        $query = Alert::with(['camera', 'raisedBy', 'resolvedBy']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $alerts = $query->orderBy('is_emergency', 'desc')
                       ->orderBy('status', 'asc')
                       ->latest()
                       ->paginate(10);

        return view('guard.alerts.index', compact('alerts'));
    }
    
    public function guardCreate()
    {
        return view('guard.alerts.create');
    }

    public function managerCreate()
    {
        return view('manager.alerts.create');
    }
    
    public function guardResolveShow($id)
    {
        $alert = Alert::with(['camera', 'raisedBy'])->findOrFail($id);
        return view('guard.alerts.resolve', compact('alert'));
    }

    public function show($id)
    {
        $alert = Alert::with(['camera', 'raisedBy', 'resolvedBy'])->findOrFail($id);
        
        if (\Illuminate\Support\Facades\Auth::user()->role === 'manager') {
            return view('manager.alerts.show', compact('alert'));
        }
        
        // Wait, guard doesn't have a specific show view? Let me check the user prompt.
        // Prompt says guard resolve uses guard.alerts.resolve. I'll make a separate method for that later if needed.
        return redirect()->route('guard.alerts');
    }

    public function instruct(Request $request, $id)
    {
        $validated = $request->validate([
            'instruction' => 'required|string|min:10',
        ]);

        $alert = Alert::findOrFail($id);
        $alert->update(['instruction' => $validated['instruction']]);

        $guards = \App\Models\User::where('role', 'guard')->where('status', 'active')->get();
        foreach ($guards as $guard) {
            /** @var \App\Models\User $guard */
            if (!$guard->isOnDuty()) {
                continue;
            }
            \App\Models\Notification::create([
                'user_id' => $guard->id,
                'title' => "Instruction for Alert #{$alert->id}",
                'message' => "Manager sent instruction: {$validated['instruction']}",
                'type' => 'alert',
            ]);
        }

        \App\Models\Log::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'Instruction Sent',
            'description' => "Sent instruction for Alert #{$alert->id}"
        ]);

        return redirect()->back()->with('success', 'Instruction sent successfully.');
    }

    public function showResolve($id)
    {
        $alert = \App\Models\Alert::with([
            'camera',
            'raisedBy',
            'resolvedBy'
        ])->findOrFail($id);

        if($alert->status === 'resolved') {
            return redirect()
                ->route('guard.alerts')
                ->with('error',
                    'This alert is already resolved.');
        }

        return view('guard.alerts.resolve',
            compact('alert'));
    }

    public function resolve(Request $request, $id)
    {
        $validated = $request->validate([
            'resolution_note' => 'required|string|min:20|max:500',
        ]);

        $alert = Alert::findOrFail($id);
        
        if ($alert->status !== 'open') {
            return redirect()->back()->with('error', 'Alert is already resolved.');
        }
        
        $alert->update([
            'status' => 'resolved',
            'resolution_note' => $validated['resolution_note'],
            'resolved_by' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        
        if ($user->role === 'guard') {
            $managers = \App\Models\User::where('role', 'manager')->get();
            foreach ($managers as $manager) {
                \App\Models\Notification::create([
                    'user_id' => $manager->id,
                    'title' => "Alert Resolved",
                    'message' => "Alert #{$alert->id} has been resolved by {$user->name}",
                    'type' => 'alert',
                ]);
            }

            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'title' => "Alert Resolved",
                    'message' => "Alert #{$alert->id} has been resolved by {$user->name}",
                    'type' => 'alert',
                ]);
            }
            
            \App\Models\Log::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'Alert Resolved by Guard',
                'description' => "Resolved Alert #{$alert->id}"
            ]);
        } else {
            \App\Models\Notification::create([
                'user_id' => $alert->raised_by,
                'title' => "Alert Resolved",
                'message' => "Alert #{$alert->id} resolved by Manager",
                'type' => 'alert',
            ]);
            
            \App\Models\Log::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'Alert Resolved',
                'description' => "Resolved Alert #{$alert->id}"
            ]);
        }

        broadcast(new \App\Events\AlertResolved($alert))->toOthers();

        return redirect()->back()->with('success', 'Alert marked as resolved.');
    }

    public function raise(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role === 'guard' && !session('is_on_duty')) {
            return redirect()->back()->with('error', 'You cannot raise alerts while off duty.');
        }

        $validated = $request->validate([
            'camera_id' => 'required|exists:cameras,id',
            'type' => 'required|in:crowd,crime,worksite',
            'description' => 'required|string|min:10',
        ]);

        $alert = Alert::create([
            'camera_id' => $validated['camera_id'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'raised_by' => \Illuminate\Support\Facades\Auth::id(),
            'status' => 'open',
            'is_emergency' => false,
        ]);

        $guards = \App\Models\User::where('role', 'guard')->where('status', 'active')->get();
        foreach ($guards as $guard) {
            if ($guard->id !== \Illuminate\Support\Facades\Auth::id()) {
                \App\Models\Notification::create([
                    'user_id' => $guard->id,
                    'title' => "New Alert Raised",
                    'message' => "A new {$validated['type']} alert was raised.",
                    'type' => 'alert',
                ]);
            }
        }
        
        $managers = \App\Models\User::where('role', 'manager')->where('status', 'active')->get();
        foreach ($managers as $manager) {
            if ($manager->id !== \Illuminate\Support\Facades\Auth::id()) {
                \App\Models\Notification::create([
                    'user_id' => $manager->id,
                    'title' => "New Alert Raised",
                    'message' => "A new {$validated['type']} alert was raised.",
                    'type' => 'alert',
                ]);
            }
        }
        
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'title' => "New Alert Raised",
                'message' => "A new {$validated['type']} alert was raised.",
                'type' => 'alert',
            ]);
        }

        \App\Models\Log::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => \Illuminate\Support\Facades\Auth::user()->role === 'guard' ? 'Alert Raised by Guard' : 'Alert Raised',
            'description' => "Raised new {$validated['type']} alert for camera #{$validated['camera_id']}"
        ]);

        broadcast(new \App\Events\AlertRaised($alert))->toOthers();

        $route = \Illuminate\Support\Facades\Auth::user()->role === 'manager' ? 'manager.alerts' : 'guard.alerts';
        return redirect()->route($route)->with('success', 'Alert raised successfully.');
    }

    public function emergency(Request $request)
    {

        $validated = $request->validate([
            'camera_id' => 'required|exists:cameras,id',
            'description' => 'required|string|min:10',
        ]);

        $alert = Alert::create([
            'camera_id' => $validated['camera_id'],
            'type' => 'emergency',
            'description' => $validated['description'],
            'raised_by' => \Illuminate\Support\Facades\Auth::id(),
            'status' => 'open',
            'is_emergency' => true,
        ]);

        $staff = \App\Models\User::whereIn('role', ['admin', 'manager', 'guard'])->get();
        foreach ($staff as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => "🚨 EMERGENCY ALERT RAISED",
                'message' => "Emergency triggered at Camera #{$validated['camera_id']}: {$validated['description']}",
                'type' => 'alert',
            ]);
        }

        \App\Models\Log::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'EMERGENCY Alert Raised',
            'description' => "Raised EMERGENCY alert for camera #{$validated['camera_id']}"
        ]);

        broadcast(new \App\Events\AlertRaised($alert))->toOthers();

        return redirect()->route('guard.alerts')->with('success', 'Emergency alert raised! All staff notified.');
    }

    public function getNewAlerts(Request $request)
    {
        if(!\Illuminate\Support\Facades\Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $tenSecondsAgo = \Carbon\Carbon::now()->subSeconds(10);
        $alerts = Alert::where('created_at', '>=', $tenSecondsAgo)
                      ->where('status', 'open')
                      ->get();
                      
        $formatted = $alerts->map(function($alert) {
            return [
                'id' => $alert->id,
                'type' => $alert->type,
                'camera' => $alert->camera->name ?? 'Unknown',
                'description' => $alert->description,
                'is_emergency' => $alert->is_emergency
            ];
        });

        return response()->json([
            'count' => $alerts->count(),
            'alerts' => $formatted,
            'has_emergency' => $alerts->where('is_emergency', true)->count() > 0
        ]);
    }
}
