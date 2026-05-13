@extends('layouts.admin')

@section('page-title', 'All Alerts')

@section('content')
<div class="content-card mb-4">
    <div class="card-body-custom py-3">
        <form action="{{ route('admin.alerts.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="crowd"     {{ request('type')=='crowd'     ? 'selected':'' }}>Crowd</option>
                    <option value="crime"     {{ request('type')=='crime'     ? 'selected':'' }}>Crime</option>
                    <option value="worksite"  {{ request('type')=='worksite'  ? 'selected':'' }}>Worksite</option>
                    <option value="emergency" {{ request('type')=='emergency' ? 'selected':'' }}>Emergency</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="open"     {{ request('status')=='open'     ? 'selected':'' }}>Open</option>
                    <option value="resolved" {{ request('status')=='resolved' ? 'selected':'' }}>Resolved</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-teal w-100">Filter</button>
                <a href="{{ route('admin.alerts.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
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
                    <tr class="{{ $alert->is_emergency && $alert->status=='open' ? 'emergency-flash' : '' }} {{ $alert->status=='resolved' ? 'table-success' : '' }}">
                        <td class="small fw-bold text-muted">#{{ $alert->id }}</td>
                        <td class="fw-medium">{{ $alert->camera->name ?? 'Unknown' }}</td>
                        <td>
                            @if($alert->is_emergency)
                                <span class="badge alert-type-emergency px-2 py-1">🚨 Emergency</span>
                            @elseif($alert->type=='crowd')
                                <span class="badge alert-type-crowd px-2 py-1">Crowd</span>
                            @elseif($alert->type=='crime')
                                <span class="badge alert-type-crime px-2 py-1">Crime</span>
                            @elseif($alert->type=='worksite')
                                <span class="badge alert-type-worksite px-2 py-1">Worksite</span>
                            @else
                                <span class="badge bg-secondary px-2 py-1">{{ ucfirst($alert->type) }}</span>
                            @endif
                        </td>
                        <td class="small">
                            <span class="d-inline-block text-truncate truncate-200" title="{{ $alert->description }}">
                                {{ $alert->description }}
                            </span>
                        </td>
                        <td class="small">{{ $alert->raisedBy->name ?? 'System' }}</td>
                        <td class="small text-muted">{{ $alert->created_at->format('M d, H:i') }}</td>
                        <td>
                            @if($alert->status=='open')
                                <span class="badge badge-red px-2 py-1">Open</span>
                            @else
                                <span class="badge badge-green px-2 py-1">Resolved</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary px-3" title="View alert details">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
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
        <div class="px-3 py-2 border-top">
            {{ $alerts->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
