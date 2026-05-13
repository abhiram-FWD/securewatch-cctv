@extends('layouts.guard')

@section('page-title', 'All Alerts')

@section('styles')
<style>
    @keyframes flash {
        0% { background-color: #dc3545; }
        50% { background-color: #bb2d3b; }
        100% { background-color: #dc3545; }
    }
    .emergency-banner {
        animation: flash 2s infinite;
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
    }
</style>
@endsection

@section('content')
    @php
        $hasEmergency = $alerts->where('is_emergency', true)->where('status', 'open')->count() > 0;
        $emergencyAlerts = $alerts->where('is_emergency', true)->where('status', 'open');
    @endphp

    @if($hasEmergency)
        <div class="emergency-banner text-center">
            <h3 class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i> 🚨 EMERGENCY ALERT ACTIVE 🚨</h3>
            @foreach($emergencyAlerts as $emAlert)
                <p class="mb-1"><strong>Camera:</strong> {{ $emAlert->camera->name ?? 'Unknown' }} - {{ $emAlert->description }}</p>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <form action="{{ route('guard.alerts') }}" method="GET" class="d-flex gap-2">
                    <select name="type" class="form-select form-select-sm" style="width: 150px;">
                        <option value="">All Types</option>
                        <option value="crowd" {{ request('type') == 'crowd' ? 'selected' : '' }}>Crowd</option>
                        <option value="crime" {{ request('type') == 'crime' ? 'selected' : '' }}>Crime</option>
                        <option value="worksite" {{ request('type') == 'worksite' ? 'selected' : '' }}>Worksite</option>
                        <option value="emergency" {{ request('type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                    <select name="status" class="form-select form-select-sm" style="width: 150px;">
                        <option value="">All Statuses</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                    @if(request()->hasAny(['type', 'status']))
                        <a href="{{ route('guard.alerts') }}" class="btn btn-sm btn-link text-muted">Clear</a>
                    @endif
                </form>
                
                <a href="{{ route('guard.alerts.create') }}" class="btn btn-teal">
                    <i class="bi bi-plus-circle me-1"></i> Raise Alert
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Camera</th>
                            <th>Description</th>
                            <th>Raised By</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alerts as $alert)
                            <tr class="{{ $alert->is_emergency && $alert->status == 'open' ? 'table-danger' : '' }}">
                                <td>
                                    @if($alert->is_emergency)
                                        <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-shield-exclamation me-1"></i> EMERGENCY</span>
                                    @else
                                        @php
                                            $color = 'secondary';
                                            $icon = 'info-circle';
                                            if($alert->type == 'crowd') { $color = 'warning text-dark'; $icon = 'people'; }
                                            if($alert->type == 'crime') { $color = 'danger'; $icon = 'exclamation-octagon'; }
                                            if($alert->type == 'worksite') { $color = 'info'; $icon = 'cone-striped'; }
                                        @endphp
                                        <span class="badge bg-{{ $color }}"><i class="bi bi-{{ $icon }} me-1"></i> {{ ucfirst($alert->type) }}</span>
                                    @endif
                                </td>
                                <td class="fw-medium">{{ $alert->camera->name ?? 'N/A' }}</td>
                                <td>
                                    <div style="max-width: 300px;" class="text-truncate" title="{{ $alert->description }}">
                                        {{ Str::limit($alert->description, 50) }}
                                    </div>
                                    @if($alert->instruction)
                                        <div class="mt-2 p-2 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded small">
                                            <i class="bi bi-info-circle-fill text-primary me-1"></i> 
                                            <strong>Manager Instruction:</strong> {{ $alert->instruction }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    {{ $alert->raisedBy->name ?? 'System' }}
                                </td>
                                <td>
                                    <span title="{{ $alert->created_at->format('M d, Y H:i') }}">
                                        {{ $alert->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td>
                                    @if($alert->status == 'open')
                                        <span class="badge bg-warning text-dark">Open</span>
                                    @else
                                        <span class="badge bg-success">Resolved</span>
                                    @endif
                                </td>
                                <td>
                                    @if($alert->status == 'open')
                                        <a href="{{ route('guard.alerts.resolve.show', $alert->id) }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-check2-circle me-1"></i> Resolve
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-secondary" disabled>
                                            <i class="bi bi-eye me-1"></i> View
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon">🔔</div>
                                        <div class="empty-title">No alerts found</div>
                                        <div class="empty-subtitle">No alerts match your current filters</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $alerts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
