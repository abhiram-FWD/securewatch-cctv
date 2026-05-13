@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
@php
  $pendingCount = \App\Models\User::where('status','pending')->count();
@endphp

@if($pendingCount > 0)
<div style="background:#FAEEDA; 
     border:1px solid #EF9F27;
     border-radius:8px; 
     padding:14px 20px;
     margin-bottom:20px;
     display:flex;
     justify-content:space-between;
     align-items:center">
  <div>
    <strong style="color:#854F0B">
      🔔 {{ $pendingCount }} pending approval(s)
    </strong>
    <div style="font-size:12px;color:#854F0B;margin-top:2px">
      New registration requests waiting for your review
    </div>
  </div>
  <a href="{{ route('admin.pending') }}" 
     class="btn-teal" 
     style="font-size:12px;padding:6px 14px;
            border-radius:6px;
            text-decoration:none;color:#fff;
            background:#1D9E75">
    Review Now →
  </a>
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-light-teal me-3"><i class="bi bi-camera"></i></div>
                <div>
                    <h3 class="fw-bold mb-0">{{ $total_cameras }}</h3>
                    <div class="text-muted small text-uppercase">Total Cameras</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-light-red me-3"><i class="bi bi-bell"></i></div>
                <div>
                    <h3 class="fw-bold mb-0 text-danger">{{ $open_alerts }}</h3>
                    <div class="text-muted small text-uppercase">Open Alerts</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100 p-3">
            <div class="d-flex align-items-center mb-2">
                <div class="stat-icon bg-light-blue me-3"><i class="bi bi-people"></i></div>
                <div>
                    <h3 class="fw-bold mb-0">{{ $total_users }}</h3>
                    <div class="text-muted small text-uppercase">Total Users</div>
                </div>
            </div>
            @php
                $activeUsers = \App\Models\User::where('status', 'active')->count();
                $pendingUsers = \App\Models\User::where('status', 'pending')->count();
                $inactiveUsers = \App\Models\User::where('status', 'inactive')->count();
            @endphp
            <div class="d-flex justify-content-between small text-muted border-top pt-2 mt-auto">
                <div><span class="text-success"><i class="bi bi-circle-fill" style="font-size:8px;"></i> {{ $activeUsers }}</span> Active</div>
                <div><span class="text-warning"><i class="bi bi-circle-fill" style="font-size:8px;"></i> {{ $pendingUsers }}</span> Pending</div>
                <div><span class="text-secondary"><i class="bi bi-circle-fill" style="font-size:8px;"></i> {{ $inactiveUsers }}</span> Inactive</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-light-green me-3"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <h3 class="fw-bold mb-0">{{ $resolved_today }}</h3>
                    <div class="text-muted small text-uppercase">Resolved Today</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card p-3 bg-danger text-white border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-uppercase small fw-medium opacity-75">Emergency Alerts</div>
                    <h3 class="fw-bold mb-0">{{ $emergency_alerts }}</h3>
                </div>
                <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-uppercase small text-muted">Active Cameras</div><h4 class="fw-bold mb-0">{{ $active_cameras }}</h4></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-uppercase small text-muted">Total Guards</div><h4 class="fw-bold mb-0">{{ $total_guards }}</h4></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-uppercase small text-muted">Total Managers</div><h4 class="fw-bold mb-0">{{ $total_managers }}</h4></div>
            </div>
        </div>
    </div>
</div>

<h5 class="fw-bold mb-3">Live Camera Feeds</h5>
<div class="camera-grid mb-5">
    @foreach($cameras as $camera)
    <div class="camera-card">
        @if($camera->status === 'active' && $camera->stream_url)
            <video src="{{ $camera->stream_url }}" autoplay muted loop playsinline></video>
        @else
            <div class="camera-placeholder d-flex h-100 align-items-center justify-content-center text-secondary">
                <i class="bi bi-camera-video-off fs-1"></i>
            </div>
        @endif
        <span class="{{ $camera->status === 'active' ? 'camera-badge-live' : 'camera-badge-offline' }}">
            {{ $camera->status === 'active' ? 'LIVE' : 'OFFLINE' }}
        </span>
        <span class="camera-badge-num">CAM-{{ str_pad($camera->id, 3, '0', STR_PAD_LEFT) }}</span>
        <div class="camera-overlay">
            <div class="small fw-bold">{{ $camera->name }}</div>
            <div class="small text-white-50">{{ $camera->location }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="table-container h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Recent Alerts</h5>
                <a href="{{ route('admin.alerts.index') }}" class="btn btn-sm btn-outline-teal">View All Alerts</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Camera</th>
                            <th>Type</th>
                            <th>Raised By</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_alerts as $alert)
                        <tr>
                            <td>{{ $alert->camera->name ?? 'Unknown' }}</td>
                            <td>
                                @if($alert->type == 'crowd') <span class="badge bg-warning text-dark">Crowd</span>
                                @elseif($alert->type == 'crime') <span class="badge bg-danger">Crime</span>
                                @elseif($alert->type == 'worksite') <span class="badge bg-primary">Worksite</span>
                                @elseif($alert->type == 'emergency') <span class="badge bg-dark text-danger border border-danger">Emergency</span>
                                @else <span class="badge bg-secondary">{{ ucfirst($alert->type) }}</span> @endif
                            </td>
                            <td>{{ $alert->raisedBy->name ?? 'System' }}</td>
                            <td class="small text-muted">{{ $alert->created_at->diffForHumans() }}</td>
                            <td>
                                @if($alert->status == 'open') <span class="badge bg-danger">Open</span>
                                @else <span class="badge bg-success">Resolved</span> @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state py-3">
                                    <div class="empty-icon">🔔</div>
                                    <div class="empty-title">No alerts found</div>
                                    <div class="empty-subtitle">No recent alerts available</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="table-container h-100">
            <h5 class="fw-bold mb-3">Recent Activity</h5>
            <div class="list-group list-group-flush">
                @forelse($recent_logs as $log)
                <div class="list-group-item px-0 py-2">
                    <div class="d-flex w-100 justify-content-between mb-1">
                        <strong class="small">{{ $log->user->name ?? 'System' }}</strong>
                        <small class="text-muted text-11">{{ $log->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="small mb-1">{{ $log->action }}</div>
                    <small class="text-muted d-block text-truncate">{{ $log->description }}</small>
                </div>
                @empty
                <div class="empty-state py-3">
                    <div class="empty-icon">🗂️</div>
                    <div class="empty-title">No recent activity</div>
                    <div class="empty-subtitle">New activity logs will appear here</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
