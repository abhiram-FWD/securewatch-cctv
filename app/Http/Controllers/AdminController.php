<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Camera;
use App\Models\Alert;
use App\Models\Log;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AdminController extends Controller
{
    public function dashboard()
    {
        $total_cameras = Camera::count();
        $active_cameras = Camera::active()->count();
        $total_users = User::count();
        $total_guards = User::where('role', 'guard')->count();
        $total_managers = User::where('role', 'manager')->count();
        $open_alerts = Alert::where('status', 'open')->count();
        $total_alerts = Alert::count();
        $emergency_alerts = Alert::where('is_emergency', true)->where('status', 'open')->count();
        $resolved_today = Alert::where('status', 'resolved')->whereDate('updated_at', Carbon::today())->count();
        
        $recent_alerts = Alert::with(['camera', 'raisedBy'])->latest()->take(5)->get();
        $recent_logs = Log::with('user')->latest()->take(5)->get();
        $cameras = Camera::active()->get();

        return view('admin.dashboard', compact(
            'total_cameras', 'active_cameras', 'total_users', 
            'total_guards', 'total_managers', 'open_alerts', 'total_alerts',
            'emergency_alerts', 'resolved_today', 'recent_alerts', 
            'recent_logs', 'cameras'
        ));
    }

    public function users(Request $request)
    {
        $query = User::query();
        if ($request->filled('role')) $query->where('role', $request->role);
        if ($request->filled('status')) $query->where('status', $request->status);
        $users = $query->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $shifts = Shift::all();
        $cameras = Camera::where('status','active')->get();
        return view('admin.users.create', compact('shifts', 'cameras'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,manager,guard',
            'shift' => 'required_if:role,guard,manager',
            'area' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'shift' => $validated['role'] === 'admin' ? null : ($validated['shift'] ?? null),
            'area' => $validated['area'] ?? null,
            'status' => $validated['status'],
        ]);

        $user->assignRole($validated['role']);

        if($request->role === 'manager' && $request->cameras) {
            $user->cameras()->sync($request->cameras);
        }

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'Created User',
            'description' => "Created new user: {$user->email} with role {$user->role}"
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $shifts = Shift::all();
        $cameras = Camera::where('status','active')->get();
        return view('admin.users.edit', compact('user', 'shifts', 'cameras'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,manager,guard',
            'shift' => 'required_if:role,guard,manager',
            'area' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->shift = $validated['role'] === 'admin' ? null : ($validated['shift'] ?? null);
        $user->area = $validated['area'] ?? null;
        $user->status = $validated['status'];
        $user->save();

        $user->syncRoles([$validated['role']]);

        if($request->role === 'manager' && $request->cameras) {
            $user->cameras()->sync($request->cameras);
        } elseif ($request->role === 'manager') {
            $user->cameras()->sync([]);
        }

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'Updated User',
            'description' => "Updated user: {$user->email}"
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself.');
        }

        $email = $user->email;
        $user->delete();

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted User',
            'description' => "Deleted user: {$email}"
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function pendingUsers()
    {
        $pendingUsers = \App\Models\User
            ::where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.users.pending', 
            compact('pendingUsers'));
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        Notification::create([
            'user_id' => $user->id,
            'message' => "Your SecureWatch account has been approved! You can now login.",
            'type' => 'system',
            'is_read' => false
        ]);

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'User Approved',
            'description' => "Approved registration for user: {$user->email}"
        ]);

        return redirect()->back()->with('success', "User {$user->name} has been approved successfully!");
    }

    public function rejectUser(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ]);

        $user = User::findOrFail($id);
        $user->status = 'rejected';
        $user->rejection_reason = $request->rejection_reason;
        $user->save();

        Notification::create([
            'user_id' => $user->id,
            'message' => "Your registration has been rejected.\n   Reason: {$request->rejection_reason}",
            'type' => 'system',
            'is_read' => false
        ]);

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'User Rejected',
            'description' => "Rejected registration for user: {$user->email}. Reason: {$user->rejection_reason}"
        ]);

        return redirect()->back()->with('success', "User has been rejected.");
    }
}
