@extends('layouts.admin')

@section('page-title', 'Activity Logs')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.logs.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="action_type" class="form-control form-control-sm" placeholder="Search action..." value="{{ request('action_type') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
                <a href="{{ route('admin.logs.download', request()->query()) }}" class="btn btn-sm btn-danger w-100" title="Export PDF"><i class="bi bi-file-pdf"></i> PDF</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>
                            <div class="fw-medium text-dark">{{ $log->user->name ?? 'System' }}</div>
                            <span class="badge bg-light text-secondary border small">{{ $log->user->role ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @php
                                $action = strtolower($log->action);
                                $badgeClass = 'badge-action-default';
                                if(str_contains($action, 'login')) $badgeClass = 'badge-action-login';
                                elseif(str_contains($action, 'raised')) $badgeClass = 'badge-action-raised';
                                elseif(str_contains($action, 'resolved')) $badgeClass = 'badge-action-resolved';
                                elseif(str_contains($action, 'camera')) $badgeClass = 'badge-action-camera';
                                elseif(str_contains($action, 'message')) $badgeClass = 'badge-action-message';
                                elseif(str_contains($action, 'deleted')) $badgeClass = 'badge-action-deleted';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $log->action }}</span>
                        </td>
                        <td class="text-muted small">{{ $log->description }}</td>
                        <td class="small text-muted">
                            <div>{{ $log->created_at->format('M d, Y') }}</div>
                            <div>{{ $log->created_at->format('h:i A') }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <div class="empty-icon">🗂️</div>
                                <div class="empty-title">No activity logs found</div>
                                <div class="empty-subtitle">Activity logs will appear here once actions are performed</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
