<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function index()
    {
        $morning_guards = User::where('role', 'guard')->where('shift', 'morning')->get();
        $night_guards = User::where('role', 'guard')->where('shift', 'night')->get();
        $managers = User::where('role', 'manager')->where('shift', 'day')->get();

        return view('admin.shifts.index', compact('morning_guards', 'night_guards', 'managers'));
    }

    public function update(Request $request, $userId)
    {
        $request->validate([
            'shift' => 'required|in:morning,night,day'
        ]);

        $user = User::findOrFail($userId);
        $user->shift = $request->shift;
        $user->save();

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'Shift Updated',
            'description' => "Updated shift for user {$user->name} to {$request->shift}"
        ]);

        return redirect()->route('admin.shifts')->with('success', "Shift updated successfully for {$user->name}.");
    }
}
