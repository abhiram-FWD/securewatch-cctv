<!DOCTYPE html>
<html>
<head>
    <title>SecureWatch Daily Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1D9E75;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #1D9E75;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .summary-box {
            width: 23%;
            display: inline-block;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            margin-right: 1%;
            box-sizing: border-box;
            border-radius: 4px;
        }
        .summary-box.last {
            margin-right: 0;
        }
        .summary-box h3 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .summary-box p {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .section-title {
            margin-top: 20px;
            margin-bottom: 10px;
            color: #1D9E75;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            color: #fff;
            font-weight: bold;
        }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-success { background-color: #28a745; }
        .bg-primary { background-color: #0d6efd; }
        .bg-info { background-color: #17a2b8; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .row {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>SecureWatch</h1>
        <p>Daily Operations Report</p>
        <p>Date: {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
    </div>

    <div class="row">
        <div class="summary-box">
            <h3>Total Alerts</h3>
            <p>{{ $total_alerts_today }}</p>
        </div>
        <div class="summary-box">
            <h3>Resolved</h3>
            <p>{{ $resolved_alerts_today }}</p>
        </div>
        <div class="summary-box">
            <h3>Pending</h3>
            <p>{{ $pending_alerts_today }}</p>
        </div>
        <div class="summary-box last">
            <h3>Emergencies</h3>
            <p style="color: #dc3545;">{{ $emergency_alerts_today }}</p>
        </div>
    </div>

    <h2 class="section-title">Alerts Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th>Crowd</th>
                <th>Crime</th>
                <th>Worksite</th>
                <th>Most Active Camera</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $crowd_alerts_today }}</td>
                <td class="text-center">{{ $crime_alerts_today }}</td>
                <td class="text-center">{{ $worksite_alerts_today }}</td>
                <td class="text-center">{{ $most_active_camera }}</td>
            </tr>
        </tbody>
    </table>

    <h2 class="section-title">All Alerts Log</h2>
    <table>
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="15%">Camera</th>
                <th width="10%">Type</th>
                <th width="25%">Description</th>
                <th width="15%">Raised By</th>
                <th width="10%">Time</th>
                <th width="10%">Status</th>
                <th width="10%">Resolved By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($all_today_alerts as $alert)
                <tr>
                    <td>{{ $alert->id }}</td>
                    <td>{{ $alert->camera->name ?? 'N/A' }}</td>
                    <td>
                        @if($alert->is_emergency)
                            <span class="badge bg-danger">Emergency</span>
                        @else
                            <span class="badge {{ $alert->type == 'crowd' ? 'bg-warning' : ($alert->type == 'crime' ? 'bg-danger' : 'bg-primary') }}">
                                {{ ucfirst($alert->type) }}
                            </span>
                        @endif
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($alert->description, 50) }}</td>
                    <td>{{ $alert->raisedBy->name ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($alert->created_at)->format('H:i') }}</td>
                    <td>
                        @if($alert->status == 'open')
                            <span class="badge bg-warning">Open</span>
                        @else
                            <span class="badge bg-success">Resolved</span>
                        @endif
                    </td>
                    <td>{{ $alert->resolvedBy->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No alerts recorded on this date.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated by SecureWatch System on {{ \Carbon\Carbon::now()->format('F d, Y H:i:s') }}
    </div>

</body>
</html>
