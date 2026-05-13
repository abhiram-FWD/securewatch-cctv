@extends('layouts.manager')

@section('page-title', 'All Alerts')

@section('content')

@php
    $emergencies = $alerts->where('is_emergency', true)->where('status', 'open');
@endphp

@if($emergencies->count() > 0)
<div class="alert alert-danger shadow-sm border-0 mb-4 p-4 d-flex align-items-start gap-3">
    <i class="bi bi-exclamation-triangle-fill fs-1 text-danger"></i>
    <div>
        <h4 class="alert-heading fw-bold mb-1">EMERGENCY ALERTS ACTIVE</h4>
        <p class="mb-0 fw-medium">There are currently {{ $emergencies->count() }} active emergency alerts requiring immediate attention.</p>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 fw-bold">Filter Alerts</h6>
            <a href="{{ route('manager.alerts.create') }}" class="btn btn-sm btn-teal"><i class="bi bi-plus-circle me-1"></i> Raise Alert</a>
        </div>
        <form action="{{ route('manager.alerts') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="crowd" {{ request('type') == 'crowd' ? 'selected' : '' }}>Crowd</option>
                    <option value="crime" {{ request('type') == 'crime' ? 'selected' : '' }}>Crime</option>
                    <option value="worksite" {{ request('type') == 'worksite' ? 'selected' : '' }}>Worksite</option>
                    <option value="emergency" {{ request('type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                <a href="{{ route('manager.alerts') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th></th>
                    <th>Camera</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Raised By</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                    @php
                        $rowStyle = 'background-color: white;';
                        if($alert->is_emergency && $alert->status == 'open') $rowStyle = 'background-color: #FFEBEB;';
                        elseif($alert->status == 'resolved') $rowStyle = 'background-color: #EBFFEF;';
                    @endphp
                    <tr style="{{ $rowStyle }}">
                        <td class="text-center">
                            @if($alert->is_emergency && $alert->status == 'open')
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-5" title="Emergency"></i>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $alert->camera->name ?? 'Unknown' }}</td>
                        <td>
                            @if($alert->type == 'crowd') <span class="badge bg-warning text-dark">Crowd</span>
                            @elseif($alert->type == 'crime') <span class="badge bg-danger">Crime</span>
                            @elseif($alert->type == 'worksite') <span class="badge bg-primary">Worksite</span>
                            @elseif($alert->type == 'emergency') <span class="badge bg-dark text-danger border border-danger">Emergency</span>
                            @else <span class="badge bg-secondary">{{ ucfirst($alert->type) }}</span> @endif
                        </td>
                        <td class="small"><span class="d-inline-block text-truncate" style="max-width: 250px;" title="{{ $alert->description }}">{{ $alert->description }}</span></td>
                        <td class="small">{{ $alert->raisedBy->name ?? 'System' }}</td>
                        <td class="small text-muted">{{ $alert->created_at->format('M d, H:i') }}</td>
                        <td>
                            @if($alert->status == 'open') <span class="badge bg-danger">Open</span>
                            @else <span class="badge bg-success">Resolved</span> @endif
                        </td>
                        <td>
                            <a href="{{ route('manager.alerts.show', $alert->id) }}" class="btn btn-sm btn-outline-dark px-3 py-1">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
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
    @if($alerts->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $alerts->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
