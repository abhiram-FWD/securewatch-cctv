@extends('layouts.admin')

@section('page-title', 'Shift Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Shift Management</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $now = \Carbon\Carbon::now();
        $hour = $now->hour;
        // Morning is 6:00 to 17:59 (6AM to 6PM)
        // Night is 18:00 to 5:59 (6PM to 6AM)
        $isMorningNow = $hour >= 6 && $hour < 18;
    @endphp

    <!-- Morning Shift -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 d-flex align-items-center">
                🌅 Morning Shift &mdash; 6:00 AM to 6:00 PM
                <span class="badge bg-success ms-3">{{ $morning_guards->count() }} Guards</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Status</th>
                            <th>Current Shift</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($morning_guards as $guard)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            {{ substr($guard->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $guard->name }}</h6>
                                            <small class="text-muted">{{ $guard->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($isMorningNow)
                                        <span class="text-success"><i class="bi bi-circle-fill me-1"></i> On Duty</span>
                                    @else
                                        <span class="text-secondary"><i class="bi bi-circle me-1"></i> Off Duty</span>
                                    @endif
                                </td>
                                <td>Morning</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-teal" data-bs-toggle="modal" data-bs-target="#changeShiftModal-{{ $guard->id }}">
                                        Change Shift
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No guards assigned to morning shift</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Night Shift -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 d-flex align-items-center">
                🌙 Night Shift &mdash; 6:00 PM to 6:00 AM
                <span class="badge bg-primary ms-3">{{ $night_guards->count() }} Guards</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Status</th>
                            <th>Current Shift</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($night_guards as $guard)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            {{ substr($guard->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $guard->name }}</h6>
                                            <small class="text-muted">{{ $guard->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(!$isMorningNow)
                                        <span class="text-success"><i class="bi bi-circle-fill me-1"></i> On Duty</span>
                                    @else
                                        <span class="text-secondary"><i class="bi bi-circle me-1"></i> Off Duty</span>
                                    @endif
                                </td>
                                <td>Night</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-teal" data-bs-toggle="modal" data-bs-target="#changeShiftModal-{{ $guard->id }}">
                                        Change Shift
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No guards assigned to night shift</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Managers Shift -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 d-flex align-items-center">
                👷 Day Shift (Managers) &mdash; 9:00 AM to 6:00 PM
                <span class="badge bg-teal ms-3">{{ $managers->count() }} Managers</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Status</th>
                            <th>Current Shift</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($managers as $manager)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-teal text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            {{ substr($manager->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $manager->name }}</h6>
                                            <small class="text-muted">{{ $manager->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $isManagerOnDuty = $hour >= 9 && $hour < 18;
                                    @endphp
                                    @if($isManagerOnDuty)
                                        <span class="text-success"><i class="bi bi-circle-fill me-1"></i> On Duty</span>
                                    @else
                                        <span class="text-secondary"><i class="bi bi-circle me-1"></i> Off Duty</span>
                                    @endif
                                </td>
                                <td>Day</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No managers assigned</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Guards -->
@foreach($morning_guards->concat($night_guards) as $guard)
<div class="modal fade" id="changeShiftModal-{{ $guard->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.shifts.update', $guard->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title">Change Shift for {{ $guard->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-4">
                        <div class="form-check custom-radio border rounded p-3 mb-2 {{ $guard->shift == 'morning' ? 'bg-light' : '' }}">
                            <input class="form-check-input ms-0 me-2 mt-1" type="radio" name="shift" id="shiftMorning-{{ $guard->id }}" value="morning" {{ $guard->shift == 'morning' ? 'checked' : '' }}>
                            <label class="form-check-label w-100" for="shiftMorning-{{ $guard->id }}">
                                <strong>🌅 Morning Shift</strong><br>
                                <small class="text-muted">6:00 AM to 6:00 PM</small>
                            </label>
                        </div>
                        <div class="form-check custom-radio border rounded p-3 {{ $guard->shift == 'night' ? 'bg-light' : '' }}">
                            <input class="form-check-input ms-0 me-2 mt-1" type="radio" name="shift" id="shiftNight-{{ $guard->id }}" value="night" {{ $guard->shift == 'night' ? 'checked' : '' }}>
                            <label class="form-check-label w-100" for="shiftNight-{{ $guard->id }}">
                                <strong>🌙 Night Shift</strong><br>
                                <small class="text-muted">6:00 PM to 6:00 AM</small>
                            </label>
                        </div>
                    </div>
                    <div class="alert alert-warning mb-0 border-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Changing shift will affect alert notifications for this guard.
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-teal">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
    .bg-teal {
        background-color: #1D9E75 !important;
    }
    .text-teal {
        color: #1D9E75 !important;
    }
    .btn-teal {
        background-color: #1D9E75;
        color: white;
    }
    .btn-teal:hover {
        background-color: #15825f;
        color: white;
    }
    .btn-outline-teal {
        border-color: #1D9E75;
        color: #1D9E75;
    }
    .btn-outline-teal:hover {
        background-color: #1D9E75;
        color: white;
    }
    .custom-radio {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .custom-radio:hover {
        border-color: #1D9E75 !important;
    }
    .custom-radio input:checked + label strong {
        color: #1D9E75;
    }
</style>
@endsection
