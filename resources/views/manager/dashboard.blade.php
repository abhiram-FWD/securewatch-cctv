@extends('layouts.manager')

@section('page-title', 'Manager Dashboard')

@section('content')

@if(session('is_on_duty'))
<div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4">
    <i class="bi bi-circle-fill text-success me-3 fs-5"></i>
    <div class="fw-medium">You are ON DUTY | Shift ends at {{ Auth::user()->shift == 'morning' ? '06:00 PM' : (Auth::user()->shift == 'night' ? '06:00 AM' : '08:00 PM') }}</div>
</div>
@else
<div class="alert alert-secondary border-0 shadow-sm d-flex align-items-center mb-4 text-secondary">
    <i class="bi bi-moon-stars-fill me-3 fs-5"></i>
    <div class="fw-medium">You are currently OFF DUTY. Your shift starts at {{ Auth::user()->shift == 'morning' ? '06:00 AM' : (Auth::user()->shift == 'night' ? '06:00 PM' : '09:00 AM') }}</div>
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-bottom border-teal border-4">
            <div class="card-body">
                <h6 class="text-muted fw-bold mb-3 text-uppercase small">My Cameras</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0 fw-bold">{{ $my_cameras->count() }}</h2>
                    <div class="p-3 bg-teal bg-opacity-10 text-teal rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                        <i class="bi bi-camera-video fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-bottom border-danger border-4">
            <div class="card-body">
                <h6 class="text-muted fw-bold mb-3 text-uppercase small">Open Alerts (Area)</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0 fw-bold">{{ $open_alerts_count }}</h2>
                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-bottom border-success border-4">
            <div class="card-body">
                <h6 class="text-muted fw-bold mb-3 text-uppercase small">Resolved Today</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0 fw-bold">{{ $resolved_today }}</h2>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-bottom border-primary border-4">
            <div class="card-body">
                <h6 class="text-muted fw-bold mb-3 text-uppercase small">On Duty Guards</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0 fw-bold">{{ $on_duty_guards }}</h2>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                        <i class="bi bi-shield-lock fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">Live Camera Feeds — My Assigned Cameras</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse($cameras as $camera)
                    <div class="col-md-4">
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
                    </div>
                    @empty
                    <div class="col-12 text-center py-4 text-muted">No cameras found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">Alerts in My Area</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush mt-2">
                    @forelse($recent_alerts as $alert)
                    <div class="list-group-item px-0 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                @if($alert->type == 'crowd') <span class="badge bg-warning text-dark">Crowd</span>
                                @elseif($alert->type == 'crime') <span class="badge bg-danger">Crime</span>
                                @else <span class="badge bg-primary">{{ ucfirst($alert->type) }}</span> @endif
                            </div>
                            <small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="fw-medium mb-1">{{ $alert->camera->name ?? 'Unknown' }}</div>
                        <p class="text-muted small mb-2 text-truncate">{{ $alert->description }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            @if($alert->status == 'open') <span class="badge bg-danger bg-opacity-10 text-danger">Open</span>
                            @else <span class="badge bg-success bg-opacity-10 text-success">Resolved</span> @endif
                            
                            <a href="{{ route('manager.alerts.show', $alert->id) }}" class="btn btn-sm btn-outline-dark" style="font-size:0.75rem;">View & Respond</a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">No recent alerts in your area.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm bg-teal bg-opacity-10">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-megaphone me-2"></i>Instruction Quick Send</h6>
                <form action="#" method="POST" id="quick-instruct-form">
                    @csrf
                    <select class="form-select form-select-sm mb-2">
                        <option value="">Select an open alert...</option>
                        @foreach($recent_alerts->where('status', 'open') as $alert)
                            <option value="{{ $alert->id }}">#{{ $alert->id }} - {{ $alert->camera->name ?? '' }}</option>
                        @endforeach
                    </select>
                    <textarea class="form-control form-control-sm mb-2" rows="2" placeholder="Instruction text..."></textarea>
                    <button type="button" class="btn btn-sm btn-teal w-100" onclick="alert('Use Alert Show page to send instructions for now.')">Send to Guards</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
