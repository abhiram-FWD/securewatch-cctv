@extends('layouts.manager')

@section('page-title', 'Area Reports')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('manager.reports') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="crowd" {{ request('type') == 'crowd' ? 'selected' : '' }}>Crowd</option>
                    <option value="crime" {{ request('type') == 'crime' ? 'selected' : '' }}>Crime</option>
                    <option value="worksite" {{ request('type') == 'worksite' ? 'selected' : '' }}>Worksite</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-teal w-100">Filter</button>
                <button type="button" class="btn btn-outline-secondary w-100" disabled title="PDF export is available in admin reports">
                    <i class="bi bi-file-pdf me-2"></i>PDF
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 bg-teal text-white">
            <div class="card-body text-center py-4">
                <h6 class="text-white-50 fw-bold mb-2 text-uppercase">Total Alerts</h6>
                <h2 class="display-5 fw-bold mb-0">{{ $total_alerts }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 bg-warning text-dark">
            <div class="card-body text-center py-4">
                <h6 class="text-dark-50 fw-bold mb-2 text-uppercase opacity-75">Crowd Alerts</h6>
                <h2 class="display-5 fw-bold mb-0">{{ $crowd_alerts }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 bg-danger text-white">
            <div class="card-body text-center py-4">
                <h6 class="text-white-50 fw-bold mb-2 text-uppercase">Crime Alerts</h6>
                <h2 class="display-5 fw-bold mb-0">{{ $crime_alerts }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 bg-primary text-white">
            <div class="card-body text-center py-4">
                <h6 class="text-white-50 fw-bold mb-2 text-uppercase">Worksite Alerts</h6>
                <h2 class="display-5 fw-bold mb-0">{{ $worksite_alerts }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">Alerts by Type</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center p-4">
                <canvas id="typeChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">Alert Trend (Last 7 Days)</h5>
            </div>
            <div class="card-body p-4">
                <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-4 pb-3">
        <h5 class="fw-bold mb-0">Filtered Alert Records</h5>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Camera</th>
                    <th>Type</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Resolution Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts_list as $alert)
                    <tr>
                        <td class="fw-medium">{{ $alert->camera->name ?? 'Unknown' }}</td>
                        <td>
                            @if($alert->type == 'crowd') <span class="badge bg-warning text-dark">Crowd</span>
                            @elseif($alert->type == 'crime') <span class="badge bg-danger">Crime</span>
                            @elseif($alert->type == 'worksite') <span class="badge bg-primary">Worksite</span>
                            @elseif($alert->type == 'emergency') <span class="badge bg-dark text-danger border border-danger">Emergency</span>
                            @else <span class="badge bg-secondary">{{ ucfirst($alert->type) }}</span> @endif
                        </td>
                        <td class="small">{{ $alert->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            @if($alert->status == 'open') <span class="badge bg-danger">Open</span>
                            @else <span class="badge bg-success">Resolved</span> @endif
                        </td>
                        <td class="small text-muted"><span class="d-inline-block text-truncate" style="max-width: 300px;" title="{{ $alert->resolution_note }}">{{ $alert->resolution_note ?? '-' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No records found for the selected filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Doughnut Chart
        const ctxType = document.getElementById('typeChart').getContext('2d');
        new Chart(ctxType, {
            type: 'doughnut',
            data: {
                labels: ['Crowd', 'Crime', 'Worksite'],
                datasets: [{
                    data: [{{ $crowd_alerts }}, {{ $crime_alerts }}, {{ $worksite_alerts }}],
                    backgroundColor: ['#ffc107', '#dc3545', '#0d6efd'],
                    borderWidth: 0
                }]
            },
            options: { cutout: '75%', plugins: { legend: { position: 'bottom' } } }
        });

        // Line Chart
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Alerts',
                    data: {!! json_encode($daily_counts) !!},
                    borderColor: '#1D9E75',
                    backgroundColor: 'rgba(29, 158, 117, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection
