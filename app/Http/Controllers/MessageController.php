<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function adminIndex()
    {
        $sent_messages = Message::with('receiver')
                            ->where('from_user_id', Auth::id())
                            ->latest()
                            ->paginate(10);
        $all_users = User::where('id', '!=', Auth::id())->where('status', 'active')->get();

        return view('admin.messages.index', compact('sent_messages', 'all_users'));
    }

    public function managerIndex()
    {
        $user = Auth::user();
        
        $messagesQuery = Message::where(function($q) use ($user) {
            $q->where('to_user_id', $user->id)
              ->orWhere('to_role', 'manager')
              ->orWhere('to_role', 'all');
        })->latest();

        $messages = $messagesQuery->get();

        Message::whereIn('id', $messages->pluck('id'))
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('manager.messages.index', compact('messages'));
    }

    public function guardIndex()
    {
        $user = Auth::user();
        
        $messagesQuery = Message::where(function($q) use ($user) {
            $q->where('to_user_id', $user->id)
              ->orWhere('to_role', 'guard')
              ->orWhere('to_role', 'all');
        })->latest();

        $messages = $messagesQuery->get();

        Message::whereIn('id', $messages->pluck('id'))
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('guard.messages.index', compact('messages'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'to_type' => 'required|in:specific,role,all',
            'to_user_id' => 'required_if:to_type,specific',
            'to_role' => 'required_if:to_type,role',
            'type' => 'required|in:warning,instruction,laziness,appreciation,general',
            'message' => 'required|string|min:10',
        ]);

        if ($validated['to_type'] === 'specific') {
            $receiver = User::where('id', $validated['to_user_id'])
                ->where('status', 'active')
                ->first();

            if (!$receiver) {
                return redirect()->back()->with('error', 'Selected user is not active.');
            }

            Message::create([
                'from_user_id' => Auth::id(),
                'to_user_id' => $receiver->id,
                'to_role' => null,
                'type' => $validated['type'],
                'message' => $validated['message'],
                'is_read' => false,
            ]);

            $receivers = collect([$receiver]);
        } elseif ($validated['to_type'] === 'role') {
            Message::create([
                'from_user_id' => Auth::id(),
                'to_user_id' => null,
                'to_role' => $validated['to_role'],
                'type' => $validated['type'],
                'message' => $validated['message'],
                'is_read' => false,
            ]);

            $receivers = User::where('role', $validated['to_role'])
                ->where('status', 'active')
                ->where('id', '!=', Auth::id())
                ->get();
        } elseif ($validated['to_type'] === 'all') {
            Message::create([
                'from_user_id' => Auth::id(),
                'to_user_id' => null,
                'to_role' => 'all',
                'type' => $validated['type'],
                'message' => $validated['message'],
                'is_read' => false,
            ]);

            $receivers = User::where('id', '!=', Auth::id())
                ->where('status', 'active')
                ->get();
        } else {
            $receivers = collect();
        }

        foreach ($receivers as $receiver) {
            Notification::create([
                'user_id' => $receiver->id,
                'title' => 'New Message: ' . ucfirst($validated['type']),
                'message' => 'You have received a new message from ' . Auth::user()->name,
                'is_read' => false,
                'type' => 'message'
            ]);
        }

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'Message Sent',
            'description' => "Sent {$validated['type']} message to " . $receivers->count() . " users."
        ]);

        return redirect()->back()->with('success', 'Message sent successfully.');
    }

    public function markRead(Request $request, $id)
    {
        $user = Auth::user();
        $message = Message::where(function($q) use ($user) {
                $q->where('to_user_id', $user->id)
                  ->orWhere('to_role', $user->role)
                  ->orWhere('to_role', 'all');
            })
            ->findOrFail($id);

        $message->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
