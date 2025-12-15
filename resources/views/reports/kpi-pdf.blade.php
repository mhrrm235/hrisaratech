<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KPI Report - {{ $record->employee->fullname }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 12px 15px;
            border-left: 4px solid #667eea;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .employee-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-item {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 150px;
            padding: 8px;
            background: #f8f9fa;
        }
        .info-value {
            display: table-cell;
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .score-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 12px;
        }
        .score-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .score-name {
            font-weight: bold;
            font-size: 14px;
        }
        .score-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .badge-excellent {
            background-color: #28a745;
        }
        .badge-good {
            background-color: #17a2b8;
        }
        .badge-satisfactory {
            background-color: #ffc107;
            color: #333;
        }
        .badge-needs-improvement {
            background-color: #fd7e14;
        }
        .badge-unsatisfactory {
            background-color: #dc3545;
        }
        .progress-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 8px;
        }
        .progress-fill {
            height: 100%;
            background: #667eea;
        }
        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .metrics-table th,
        .metrics-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            font-size: 13px;
        }
        .metrics-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .metrics-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .composite-score {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .composite-score h2 {
            font-size: 36px;
            margin-bottom: 5px;
        }
        .composite-score p {
            font-size: 14px;
            opacity: 0.9;
        }
        .footer {
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .incidents-table {
            width: 100%;
            border-collapse: collapse;
        }
        .incidents-table th,
        .incidents-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            font-size: 12px;
        }
        .incidents-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Key Performance Indicator Report</h1>
        <p>Period: {{ \Carbon\Carbon::createFromFormat('Y-m', $record->period)->format('F Y') }}</p>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>
    </div>

    <!-- Employee Information -->
    <div class="section">
        <div class="section-title">Employee Information</div>
        <div class="employee-info">
            <div class="info-item">
                <div class="info-label">Employee Name</div>
                <div class="info-value">{{ $record->employee->fullname }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Employee ID</div>
                <div class="info-value">{{ $record->employee->employee_id ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Department</div>
                <div class="info-value">{{ $record->employee->department->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Position</div>
                <div class="info-value">{{ $record->employee->role?->title ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Supervisor</div>
                <div class="info-value">{{ $record->employee->supervisor?->fullname ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Composite Score -->
    <div class="composite-score">
        <h2>{{ round($record->composite_score, 2) }}/100</h2>
        <p>
            @if($record->composite_score >= 90)
                Excellent Performance
            @elseif($record->composite_score >= 75)
                Good Performance
            @elseif($record->composite_score >= 60)
                Satisfactory Performance
            @elseif($record->composite_score >= 45)
                Needs Improvement
            @else
                Unsatisfactory Performance
            @endif
        </p>
    </div>

    <!-- KPI Breakdown -->
    <div class="section">
        <div class="section-title">Performance Metrics by Category</div>
        
        <!-- Attendance -->
        <div class="score-card">
            <div class="score-header">
                <div class="score-name">Attendance</div>
                <span class="score-badge badge-{{ 
                    $record->attendance_score >= 90 ? 'excellent' : 
                    ($record->attendance_score >= 75 ? 'good' : 
                    ($record->attendance_score >= 60 ? 'satisfactory' : 
                    ($record->attendance_score >= 45 ? 'needs-improvement' : 'unsatisfactory')))
                }}">{{ round($record->attendance_score, 1) }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $record->attendance_score }}%;"></div>
            </div>
            <table class="metrics-table">
                <tr><th>Metric</th><th>Value</th></tr>
                <tr><td>Present Days</td><td>{{ $record->present_days ?? 0 }}</td></tr>
                <tr><td>Absent Days</td><td>{{ $record->absent_days ?? 0 }}</td></tr>
                <tr><td>Late Count</td><td>{{ $record->late_count ?? 0 }}</td></tr>
            </table>
        </div>

        <!-- Tasks/Productivity -->
        <div class="score-card">
            <div class="score-header">
                <div class="score-name">Productivity</div>
                <span class="score-badge badge-{{ 
                    $record->tasks_score >= 90 ? 'excellent' : 
                    ($record->tasks_score >= 75 ? 'good' : 
                    ($record->tasks_score >= 60 ? 'satisfactory' : 
                    ($record->tasks_score >= 45 ? 'needs-improvement' : 'unsatisfactory')))
                }}">{{ round($record->tasks_score, 1) }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $record->tasks_score }}%;"></div>
            </div>
            <table class="metrics-table">
                <tr><th>Metric</th><th>Value</th></tr>
                <tr><td>Tasks Completed</td><td>{{ $record->tasks_completed ?? 0 }}</td></tr>
                <tr><td>On-Time Delivery</td><td>{{ ($record->on_time_percentage ?? 0) }}%</td></tr>
            </table>
        </div>

        <!-- Compliance -->
        <div class="score-card">
            <div class="score-header">
                <div class="score-name">Compliance</div>
                <span class="score-badge badge-{{ 
                    $record->compliance_score >= 90 ? 'excellent' : 
                    ($record->compliance_score >= 75 ? 'good' : 
                    ($record->compliance_score >= 60 ? 'satisfactory' : 
                    ($record->compliance_score >= 45 ? 'needs-improvement' : 'unsatisfactory')))
                }}">{{ round($record->compliance_score, 1) }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $record->compliance_score }}%;"></div>
            </div>
        </div>

        <!-- Quality -->
        <div class="score-card">
            <div class="score-header">
                <div class="score-name">Quality</div>
                <span class="score-badge badge-{{ 
                    $record->quality_score >= 90 ? 'excellent' : 
                    ($record->quality_score >= 75 ? 'good' : 
                    ($record->quality_score >= 60 ? 'satisfactory' : 
                    ($record->quality_score >= 45 ? 'needs-improvement' : 'unsatisfactory')))
                }}">{{ round($record->quality_score, 1) }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $record->quality_score }}%;"></div>
            </div>
        </div>

        <!-- Conduct -->
        <div class="score-card">
            <div class="score-header">
                <div class="score-name">Conduct & Behavior</div>
                <span class="score-badge badge-{{ 
                    $record->conduct_score >= 90 ? 'excellent' : 
                    ($record->conduct_score >= 75 ? 'good' : 
                    ($record->conduct_score >= 60 ? 'satisfactory' : 
                    ($record->conduct_score >= 45 ? 'needs-improvement' : 'unsatisfactory')))
                }}">{{ round($record->conduct_score, 1) }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $record->conduct_score }}%;"></div>
            </div>
        </div>
    </div>

    <!-- Incidents (if any) -->
    @if(isset($incidents) && $incidents->count() > 0)
    <div class="section">
        <div class="section-title">Recorded Incidents</div>
        <table class="incidents-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incidents as $incident)
                <tr>
                    <td>{{ $incident->incident_date->format('d M Y') }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $incident->type)) }}</td>
                    <td>{{ ucfirst($incident->severity) }}</td>
                    <td>{{ ucfirst($incident->status) }}</td>
                    <td>{{ Str::limit($incident->description, 60) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This is an automated report generated by HR System. For inquiries, please contact HR Department.</p>
        <p>Report Reference: KPI-{{ $record->employee->id }}-{{ $record->period }}</p>
    </div>
</body>
</html>
