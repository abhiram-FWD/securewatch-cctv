@extends('layouts.guard')

@section('page-title', 'Guard Dashboard')

@section('content')
    <!-- Duty Status Banner -->
    @if(session('is_on_duty'))
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-circle-fill me-2 fs-5"></i>
            <div>
                <strong>You are ON DUTY</strong> | 
                @if(session('shift_start') && session('shift_end'))
                    Shift ends at {{ \Carbon\Carbon::parse(session('shift_end'))->format('h:i A') }}
                @else
                    Shift details unavailable
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-secondary d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-circle me-2 fs-5"></i>
            <div>
                <strong>You are currently OFF DUTY 😴</strong><br>
                @if(session('shift_start'))
                    Your next shift starts at {{ \Carbon\Carbon::parse(session('shift_start'))->format('h:i A') }}. 
                @endif
                <span class="text-danger fw-bold">You cannot raise alerts while off duty.</span>
            </div>
        </div>
    @endif

    <!-- EMERGENCY ROW -->
    <div class="row mb-4">
        <div class="col-12">
            <button type="button" class="btn btn-danger w-100 py-3 d-flex flex-column align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#emergencyModal" style="border-radius: 10px; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);">
                <span class="fs-4 fw-bold"><i class="bi bi-shield-exclamation me-2"></i> 🚨 RAISE EMERGENCY ALERT</span>
                <span class="small opacity-75 mt-1">Use only for critical situations</span>
            </button>
        </div>
    </div>

    <!-- STATS ROW -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Open Alerts</h6>
                            <h2 class="mb-0 fw-bold">{{ $open_alerts->count() }}</h2>
                        </div>
                        <div class="fs-1 opacity-50"><i class="bi bi-exclamation-triangle"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">My Resolved Today</h6>
                            <h2 class="mb-0 fw-bold">{{ $resolved_today }}</h2>
                        </div>
                        <div class="fs-1 opacity-50"><i class="bi bi-check-circle"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-teal text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Active Cameras</h6>
                            <h2 class="mb-0 fw-bold">{{ $active_cameras->count() }}</h2>
                        </div>
                        <div class="fs-1 opacity-50"><i class="bi bi-camera-video"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Unread Messages</h6>
                            <h2 class="mb-0 fw-bold">{{ $unread_messages }}</h2>
                        </div>
                        <div class="fs-1 opacity-50"><i class="bi bi-envelope"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- ALL CAMERAS GRID -->
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">Live Camera Feeds</h5>
                    <div class="row g-3">
                        @forelse($active_cameras as $camera)
                            <div class="col-md-6 col-lg-4">
                                <div class="camera-card">
                                    @if($camera->stream_url)
                                        <video autoplay loop muted playsinline>
                                            <source src="{{ $camera->stream_url }}" type="video/mp4">
                                        </video>
                                    @else
                                        <div class="camera-placeholder d-flex h-100 align-items-center justify-content-center text-secondary">
                                            <i class="bi bi-camera-video-off fs-3"></i>
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
                                    <div class="camera-hover-btn">
                                        <a href="{{ route('guard.alerts.create', ['camera_id' => $camera->id]) }}" class="btn btn-sm btn-outline-light w-100">
                                            <i class="bi bi-exclamation-circle me-1"></i> Report Issue
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 text-muted">
                                <i class="bi bi-camera-video-off fs-1"></i>
                                <p class="mt-2">No active cameras found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT OPEN ALERTS -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Active Alerts</h5>
                        <a href="{{ route('guard.alerts') }}" class="btn btn-sm btn-outline-teal">View All</a>
                    </div>
                    
                    <div class="d-flex flex-column gap-3">
                        @forelse($open_alerts as $alert)
                            <div class="p-3 border rounded {{ $alert->is_emergency ? 'border-danger bg-danger bg-opacity-10' : '' }}">
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        @if($alert->is_emergency)
                                            <span class="badge bg-danger me-1">🚨 EMERGENCY</span>
                                        @else
                                            <span class="badge bg-warning text-dark me-1">{{ ucfirst($alert->type) }}</span>
                                        @endif
                                        <span class="small fw-bold">{{ $alert->camera->name ?? 'Unknown Camera' }}</span>
                                    </div>
                                    <span class="small text-muted">{{ $alert->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="small mb-2 text-truncate" style="max-width: 100%;">
                                    {{ Str::limit($alert->description, 100) }}
                                </p>
                                <a href="{{ route('guard.alerts.resolve.show', $alert->id) }}" class="btn btn-sm btn-teal w-100">Take Action</a>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-check2-circle fs-2"></i>
                                <p class="mt-2 mb-0">No active alerts. All clear!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
