<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action_type')) {
            $query->where('action', 'like', '%' . $request->action_type . '%');
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59']);
        }

        $logs = $query->latest()->paginate(15);
        $users = User::all();

        return view('admin.logs.index', compact('logs', 'users'));
    }

    public function download(Request $request)
    {
        $query = Log::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action_type')) {
            $query->where('action', 'like', '%' . $request->action_type . '%');
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59']);
        }

        $logs = $query->latest()->get();

        $pdf = Pdf::loadView('admin.logs.pdf', compact('logs'));
        
        $filename = 'SecureWatch-Activity-Logs-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
}
