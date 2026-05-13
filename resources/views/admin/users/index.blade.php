@extends('layouts.admin')

@section('page-title', 'User Management')

@section('content')
<div class="content-card">
    <div class="card-header-custom">
        <div class="d-flex align-items-center gap-3">
            <form class="d-flex gap-2" method="GET" action="{{ route('admin.users.index') }}">
                <select name="role" class="form-select form-select-sm" style="width:140px;">
                    <option value="">All Roles</option>
                    <option value="admin"   {{ request('role')=='admin'   ? 'selected':'' }}>Admin</option>
                    <option value="manager" {{ request('role')=='manager' ? 'selected':'' }}>Manager</option>
                    <option value="guard"   {{ request('role')=='guard'   ? 'selected':'' }}>Guard</option>
                </select>
                <select name="status" class="form-select form-select-sm" style="width:140px;">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status')=='active'   ? 'selected':'' }}>Active</option>
                    <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>Inactive</option>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </form>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-teal btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add New User
        </a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="padding:10px 14px; font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid #e2e8f0;">Name</th>
                    <th style="padding:10px 14px; font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid #e2e8f0;">Email</th>
                    <th style="padding:10px 14px; font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid #e2e8f0;">Role</th>
                    <th style="padding:10px 14px; font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid #e2e8f0;">Shift</th>
                    <th style="padding:10px 14px; font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid #e2e8f0;">Area</th>
                    <th style="padding:10px 14px; font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid #e2e8f0;">Status</th>
                    <th style="padding:10px 14px; font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid #e2e8f0; text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td style="padding:12px 14px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width:32px; height:32px; font-size:12px; flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="fw-medium" style="font-size:13px;">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="text-muted" style="font-size:12px; padding:12px 14px;">{{ $user->email }}</td>
                        <td style="padding:12px 14px;">
                            @if($user->role=='admin')
                                <span class="badge" style="background:#1a2332; color:#fff; font-size:11px; padding:4px 8px;">Admin</span>
                            @elseif($user->role=='manager')
                                <span class="badge badge-blue" style="font-size:11px; padding:4px 8px;">Manager</span>
                            @else
                                <span class="badge badge-teal" style="font-size:11px; padding:4px 8px;">Guard</span>
                            @endif
                        </td>
                        <td style="font-size:12px; padding:12px 14px;">{{ $user->shift ? ucfirst($user->shift) : '—' }}</td>
                        <td style="font-size:12px; padding:12px 14px;">{{ $user->area ?: '—' }}</td>
                        <td style="padding:12px 14px;">
                            @if($user->status=='active')
                                <span class="badge badge-green" style="font-size:11px; padding:4px 8px;">
                                    <span class="live-dot" style="width:5px; height:5px; background:#22c55e;"></span> Active
                                </span>
                            @else
                                <span class="badge" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:4px 8px;">Inactive</span>
                            @endif
                        </td>
                        <td style="padding:12px 14px; text-align:right;">
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                               class="btn btn-sm btn-outline-primary me-1" title="Edit user">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        title="Delete user" {{ auth()->id()==$user->id ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon">👥</div>
                                <div class="empty-title">No users found</div>
                                <div class="empty-subtitle">Add your first user to get started</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div style="padding:12px 16px; border-top:1px solid #f1f5f9;">
            {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
