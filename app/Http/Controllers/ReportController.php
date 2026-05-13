<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alert;
use App\Models\Camera;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function getReportData($date)
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        $baseQuery = Alert::whereBetween('created_at', [$startOfDay, $endOfDay]);

        $all_today_alerts = $baseQuery->with(['camera', 'raisedBy', 'resolvedBy'])->latest()->get();

        $total_alerts_today = $all_today_alerts->count();
        $resolved_alerts_today = $all_today_alerts->where('status', 'resolved')->count();
        $pending_alerts_today = $all_today_alerts->where('status', 'open')->count();
        
        $crowd_alerts_today = $all_today_alerts->where('type', 'crowd')->count();
        $crime_alerts_today = $all_today_alerts->where('type', 'crime')->count();
        $worksite_alerts_today = $all_today_alerts->where('type', 'worksite')->count();
        $emergency_alerts_today = $all_today_alerts->where('is_emergency', true)->count();

        // Most active camera today
        $most_active_camera = null;
        $most_active_camera_id = Alert::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->select('camera_id', DB::raw('count(*) as total'))
            ->groupBy('camera_id')
            ->orderBy('total', 'desc')
            ->first();
            
        if ($most_active_camera_id) {
            $camera = Camera::find($most_active_camera_id->camera_id);
            $most_active_camera = $camera ? $camera->name . " ({$most_active_camera_id->total} alerts)" : "Unknown";
        } else {
            $most_active_camera = "N/A";
        }

        // Most active guard today
        $most_active_guard = null;
        $most_active_guard_id = Alert::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'resolved')
            ->whereNotNull('resolved_by')
            ->select('resolved_by', DB::raw('count(*) as total'))
            ->groupBy('resolved_by')
            ->orderBy('total', 'desc')
            ->first();
            
        if ($most_active_guard_id) {
            $guard = User::find($most_active_guard_id->resolved_by);
            $most_active_guard = $guard ? $guard->name . " ({$most_active_guard_id->total} resolved)" : "Unknown";
        } else {
            $most_active_guard = "N/A";
        }

        // Alerts by hour
        $alerts_by_hour = array_fill(0, 24, 0);
        foreach ($all_today_alerts as $alert) {
            $hour = Carbon::parse($alert->created_at)->hour;
            $alerts_by_hour[$hour]++;
        }

        // Alerts by type
        $alerts_by_type = [
            $crowd_alerts_today,
            $crime_alerts_today,
            $worksite_alerts_today,
            $emergency_alerts_today
        ];

        // Top 5 cameras
        $top_cameras_data = Alert::whereBetween('alerts.created_at', [$startOfDay, $endOfDay])
            ->join('cameras', 'alerts.camera_id', '=', 'cameras.id')
            ->select('cameras.name', DB::raw('count(*) as total'))
            ->groupBy('cameras.id', 'cameras.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
            
        $top_cameras_labels = $top_cameras_data->pluck('name')->toArray();
        $top_cameras_counts = $top_cameras_data->pluck('total')->toArray();

        return compact(
            'date',
            'total_alerts_today', 'resolved_alerts_today', 'pending_alerts_today',
            'crowd_alerts_today', 'crime_alerts_today', 'worksite_alerts_today', 'emergency_alerts_today',
            'most_active_camera', 'most_active_guard',
            'alerts_by_hour', 'alerts_by_type', 'all_today_alerts',
            'top_cameras_labels', 'top_cameras_counts'
        );
    }

    public function adminReport(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $data = $this->getReportData($date);
        
        return view('admin.reports.index', $data);
    }

    public function managerReport(Request $request)
    {
        $user = Auth::user();
        $camera_ids = $user->cameras()->pluck('cameras.id')->toArray();

        $query = Alert::whereIn('camera_id', $camera_ids);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59']);
        }

        $alerts_list = $query->latest()->get();

        $base_query = Alert::whereIn('camera_id', $camera_ids);
        
        $total_alerts = (clone $base_query)->count();
        $crowd_alerts = (clone $base_query)->where('type', 'crowd')->count();
        $crime_alerts = (clone $base_query)->where('type', 'crime')->count();
        $worksite_alerts = (clone $base_query)->where('type', 'worksite')->count();
        
        $open_alerts = (clone $base_query)->where('status', 'open')->count();
        $resolved_alerts = (clone $base_query)->where('status', 'resolved')->count();

        $daily_counts = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            $daily_counts[] = Alert::whereIn('camera_id', $camera_ids)
                                ->whereDate('created_at', $date)
                                ->count();
        }

        return view('manager.reports.index', compact(
            'alerts_list', 'total_alerts', 'crowd_alerts', 'crime_alerts',
            'worksite_alerts', 'open_alerts', 'resolved_alerts',
            'daily_counts', 'labels'
        ));
    }

    public function download(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $data = $this->getReportData($date);
        
        $pdf = Pdf::loadView('admin.reports.pdf', $data);
        return $pdf->download('SecureWatch-Report-'.$date.'.pdf');
    }
}
