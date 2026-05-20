<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            if ($role === 'admin') return redirect()->route('admin.dashboard');
            if ($role === 'manager') return redirect()->route('manager.dashboard');
            if ($role === 'guard') return redirect()->route('guard.dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        $roles = ['manager' => 'Manager', 'guard' => 'Guard'];
        return view('auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[0-9]/'
            ],
            'role' => 'required|in:manager,guard',
            'shift' => 'nullable|in:morning,night,day',
            'area' => 'nullable|string'
        ]);

        // Auto assign shift based on role
        if($request->role === 'manager') {
            $shift = 'day';
        } else {
            $shift = $request->shift;
        }

        // Create user with correct shift
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'shift' => $shift,
            'area' => $request->area,
            'status' => 'pending'
        ]);

        $user->assignRole($validated['role']);

        $admins = \App\Models\User
          ::where('role','admin')->get();

        foreach($admins as $admin) {
          \App\Models\Notification::create([
            'user_id' => $admin->id,
            'message' => "New registration request \n                  from {$user->name} \n                  ({$user->role}). \n                  Please review and approve.",
            'type' => 'registration',
            'is_read' => false
          ]);
        }

        \App\Models\Log::create([
          'user_id' => $user->id,
          'action' => 'Registration Request',
          'description' => "New {$user->role} \n                    registration: {$user->name}"
        ]);

        return redirect()->route('login')->with('success', "Registration submitted! \n Please wait for admin approval.");
    }

    public function login(Request $request)
    {
      $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:4',
        'login_type' => 'required|in:admin,manager,guard',
      ]);

      // HARDCODED ADMIN CHECK
      // Admin credentials are fixed for security
      // Cannot be changed from normal login
      if(
        $request->email === 'admin@gmail.com' && 
        $request->password === 'admin@123'
      ) {
        if ($request->login_type !== 'admin') {
          return back()->withErrors([
            'email' => 'Admin details cannot be used for manager or guard login.'
          ]);
        }

        // Find admin user in database
        $adminUser = \App\Models\User::where('email', 'admin@gmail.com')
          ->where('role', 'admin')
          ->first();

        if($adminUser) {
          // Log in the admin directly
          Auth::login($adminUser);

          // Log the action
          \App\Models\Log::create([
            'user_id' => $adminUser->id,
            'action' => 'Admin Login',
            'description' => 'Admin logged in at ' . now()->format('d M Y H:i:s')
          ]);

          return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Welcome back, Administrator!');
        }
      }

      // BLOCK admin credentials for non-admin
      // If someone tries admin email 
      // but wrong password → block immediately
      if($request->email === 'admin@gmail.com') {
        return back()->withErrors([
          'email' => 'Invalid credentials.'
        ]);
      }

      // NORMAL LOGIN for Manager and Guard
      // Try normal authentication
      if(Auth::attempt([
        'email' => $request->email,
        'password' => $request->password
      ], $request->remember)) {

        $user = Auth::user();

        // Enforce login_type matching the role
        if ($user->role !== $request->login_type) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'These credentials are not valid for the selected login type.'
            ]);
        }

        // Block if admin tries to login 
        // through normal form (extra security)
        if($user->role === 'admin') {
          Auth::logout();
          return back()->withErrors([
            'email' => 'Please use admin credentials to login.'
          ]);
        }

        // Check user status
        if($user->status === 'pending') {
          Auth::logout();
          return back()->withErrors([
            'email' => 'Your account is pending admin approval.'
          ]);
        }

        if($user->status === 'rejected') {
          Auth::logout();
          return back()->withErrors([
            'email' => 'Your registration was rejected. Reason: ' . $user->rejection_reason
          ]);
        }

        if($user->status === 'inactive') {
          Auth::logout();
          return back()->withErrors([
            'email' => 'Your account has been deactivated.'
          ]);
        }

        // Log successful login
        \App\Models\Log::create([
          'user_id' => $user->id,
          'action' => 'User Login',
          'description' => $user->name . ' logged in at ' . now()->format('d M Y H:i:s')
        ]);

        // Redirect based on role
        if($user->role === 'manager') {
          return redirect()->route('manager.dashboard')
            ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        if($user->role === 'guard') {
          return redirect()->route('guard.dashboard')
            ->with('success', 'Welcome back, ' . $user->name . '!');
        }
      }

      // Login failed
      return back()->withErrors([
        'email' => 'Invalid email or password. Please try again.'
      ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            Log::create([
                'user_id' => $user->id,
                'action' => 'User Logout',
                'description' => "User {$user->name} logged out at " . Carbon::now()->toDateTimeString()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
