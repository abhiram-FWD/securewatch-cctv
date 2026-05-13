<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Camera;
use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class CameraController extends Controller
{
    public function index()
    {
        $cameras = Camera::with('managers')->latest()->paginate(8);
        return view('admin.cameras.index', compact('cameras'));
    }

    public function create()
    {
        $managers = User::where('role', 'manager')->where('status', 'active')->get();
        return view('admin.cameras.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'stream_url' => 'required|url',
            'status' => 'required|in:active,inactive',
            'managers' => 'nullable|array',
            'managers.*' => 'exists:users,id'
        ]);

        $camera = Camera::create([
            'name' => $validated['name'],
            'location' => $validated['location'],
            'stream_url' => $validated['stream_url'],
            'status' => $validated['status'],
        ]);

        if (!empty($validated['managers'])) {
            $camera->managers()->attach($validated['managers']);
        }

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'Camera Added',
            'description' => "Added new camera: {$camera->name}"
        ]);

        return redirect()->route('admin.cameras.index')->with('success', 'Camera added successfully.');
    }

    public function edit($id)
    {
        $camera = Camera::findOrFail($id);
        $managers = User::where('role', 'manager')->where('status', 'active')->get();
        $assigned_managers = $camera->managers->pluck('id')->toArray();
        
        return view('admin.cameras.edit', compact('camera', 'managers', 'assigned_managers'));
    }

    public function update(Request $request, $id)
    {
        $camera = Camera::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'stream_url' => 'required|url',
            'status' => 'required|in:active,inactive',
            'managers' => 'nullable|array',
            'managers.*' => 'exists:users,id'
        ]);

        $camera->update([
            'name' => $validated['name'],
            'location' => $validated['location'],
            'stream_url' => $validated['stream_url'],
            'status' => $validated['status'],
        ]);

        $camera->managers()->sync($validated['managers'] ?? []);

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'Camera Updated',
            'description' => "Updated camera: {$camera->name}"
        ]);

        return redirect()->route('admin.cameras.index')->with('success', 'Camera updated successfully.');
    }

    public function destroy($id)
    {
        $camera = Camera::findOrFail($id);
        $name = $camera->name;
        $camera->delete();

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'Camera Deleted',
            'description' => "Deleted camera: {$name}"
        ]);

        return redirect()->route('admin.cameras.index')->with('success', 'Camera deleted successfully.');
    }
}
