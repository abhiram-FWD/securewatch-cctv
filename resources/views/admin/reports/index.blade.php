@extends('layouts.admin')

@section('page-title', 'Daily Report')

@section('content')
<div class="container-fluid py-4">
    <!-- Header with Date Picker and Download PDF -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Daily Report</h2>
        <div class="d-flex gap-3">
            <form action="{{ route('admin.reports.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                <button type="submit" class="btn btn-teal">Generate Report</button>
            </form>
            <a href="{{ route('admin.reports.download', ['date' => $date]) }}" class="btn btn-danger d-flex align-items-center">
                <i class="bi bi-file-pdf me-2"></i> Download PDF Report
            </a>
        </div>
    </div>

    <!-- Summary Stats Row 1 -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-white-50">Total Alerts Today</h6>
                    <h2 class="mb-0 fw-bold">{{ $total_alerts_today }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-white-50">Resolved</h6>
                    <h2 class="mb-0 fw-bold">{{ $resolved_alerts_today }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-dark-50">Pending</h6>
                    <h2 class="mb-0 fw-bold">{{ $pending_alerts_today }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-white-50">Emergencies</h6>
                    <h2 class="mb-0 fw-bold">{{ $emergency_alerts_today }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats Row 2 -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-warning h-100">
                <div class="card-body">
                    <h6 class="text-muted">Crowd Alerts</h6>
                    <h3 class="mb-0 text-warning fw-bold">{{ $crowd_alerts_today }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-danger h-100">
                <div class="card-body">
                    <h6 class="text-muted">Crime Alerts</h6>
                    <h3 class="mb-0 text-danger fw-bold">{{ $crime_alerts_today }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-primary h-100">
                <div class="card-body">
                    <h6 class="text-muted">Worksite Alerts</h6>
                    <h3 class="mb-0 text-primary fw-bold">{{ $worksite_alerts_today }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-4 border-teal h-100">
                <div class="card-body">
                    <h6 class="text-muted">Most Active Camera</h6>
                    <h5 class="mb-0 text-teal fw-bold">{{ $most_active_camera }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Alerts by Type</h6>
                </div>
                <div class="card-body">
                    <div class="chart-wrap">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Alerts by Hour (24h)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-wrap">
                        <canvas id="hourChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Charts and Table -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Top 5 Cameras</h6>
                </div>
                <div class="card-body">
                    <div class="chart-wrap-tall">
                        <canvas id="cameraChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">All Alerts for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive table-scroll-y">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Camera</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                    <th class="pe-4">Resolution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($all_today_alerts as $alert)
                                    <tr class="{{ $alert->is_emergency ? 'table-danger' : '' }}">
                                        <td class="ps-4 fw-medium">#{{ $alert->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light text-secondary rounded p-2 me-2">
                                                    <i class="bi bi-camera-video"></i>
                                                </div>
                                                {{ $alert->camera->name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($alert->is_emergency)
                                                <span class="badge bg-danger">Emergency</span>
                                            @else
                                                <span class="badge {{ $alert->type == 'crowd' ? 'bg-warning text-dark' : ($alert->type == 'crime' ? 'bg-danger' : 'bg-primary') }}">
                                                    {{ ucfirst($alert->type) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($alert->status == 'open')
                                                <span class="badge bg-warning text-dark">Open</span>
                                            @else
                                                <span class="badge bg-success">Resolved</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($alert->created_at)->format('H:i') }}</td>
                                        <td class="pe-4">
                                            @if($alert->status == 'resolved')
                                                <small class="d-block text-truncate truncate-150" title="{{ $alert->resolution_note }}">
                                                    {{ $alert->resolution_note }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">No alerts found for this date.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Doughnut chart: Alerts by type
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: ['Crowd', 'Crime', 'Worksite', 'Emergency'],
            datasets: [{
                data: {{ json_encode($alerts_by_type) }},
                backgroundColor: [
                    '#EF9F27', // amber
                    '#E24B4A', // red
                    '#185FA5', // blue
                    '#8B0000'  // dark red
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '70%'
        }
    });

    // Bar chart: Alerts by hour
    const hourCtx = document.getElementById('hourChart').getContext('2d');
    new Chart(hourCtx, {
        type: 'bar',
        data: {
            labels: ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'],
            datasets: [{
                label: 'Number of Alerts',
                data: {{ json_encode($alerts_by_hour) }},
                backgroundColor: '#1D9E75',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Horizontal bar chart: Top 5 cameras
    const cameraCtx = document.getElementById('cameraChart').getContext('2d');
    new Chart(cameraCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($top_cameras_labels) !!},
            datasets: [{
                label: 'Alerts',
                data: {{ json_encode($top_cameras_counts) }},
                backgroundColor: '#1D9E75',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 } },
                y: { grid: { display: false } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
