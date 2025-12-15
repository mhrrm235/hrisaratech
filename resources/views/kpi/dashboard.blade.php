@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>My KPI Dashboard</h3>
</div>

<div class="page-content">
    <div class="container-fluid">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary">
                    <div class="card-body">
                        <h6 class="text-primary font-weight-bold mb-1">Composite Score</h6>
                        <h2 class="mb-0">{{ round($compositeScore, 2) }}/100</h2>
                        <small class="text-muted">Overall Performance</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-success">
                    <div class="card-body">
                        <h6 class="text-success font-weight-bold mb-1">Performance Level</h6>
                        <h4 class="mb-0">
                            @switch($performanceLevel)
                                @case('excellent')
                                    <span class="badge badge-success">Excellent</span>
                                    @break
                                @case('good')
                                    <span class="badge badge-info">Good</span>
                                    @break
                                @case('satisfactory')
                                    <span class="badge badge-warning">Satisfactory</span>
                                    @break
                                @case('needs_improvement')
                                    <span class="badge badge-warning">Needs Improvement</span>
                                    @break
                                @default
                                    <span class="badge badge-danger">Unsatisfactory</span>
                            @endswitch
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-info">
                    <div class="card-body">
                        <h6 class="text-info font-weight-bold mb-1">KPIs Achieved</h6>
                        <h2 class="mb-0">{{ $kpiRecords->where('status', 'achieved')->count() }}/{{ $kpiRecords->count() }}</h2>
                        <small class="text-muted">Targets Met</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-warning">
                    <div class="card-body">
                        <h6 class="text-warning font-weight-bold mb-1">Period</h6>
                        <h4 class="mb-0">{{ \Carbon\Carbon::createFromFormat('Y-m', $period)->format('M Y') }}</h4>
                        <small class="text-muted">{{ $period }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Categories -->
        @foreach($kpisByCategory as $category => $records)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ $category }} Metrics</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>KPI</th>
                                        <th>Actual Value</th>
                                        <th>Target Value</th>
                                        <th>Achievement %</th>
                                        <th>Status</th>
                                        <th>Variance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records as $record)
                                    <tr>
                                        <td>
                                            <strong>{{ $record->kpi->name }}</strong><br>
                                            <small class="text-muted">{{ $record->kpi->unit }}</small>
                                        </td>
                                        <td>{{ $record->actual_value }}</td>
                                        <td>{{ $record->target_value }}</td>
                                        <td>
                                            @php
                                                $achievement = $record->getAchievementPercentage();
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar {{ $achievement >= 100 ? 'bg-success' : ($achievement >= 80 ? 'bg-warning' : 'bg-danger') }}" 
                                                     role="progressbar" 
                                                     style="width: {{ min($achievement, 100) }}%" 
                                                     aria-valuenow="{{ $achievement }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ round($achievement, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @switch($record->status)
                                                @case('achieved')
                                                    <span class="badge badge-success">Achieved</span>
                                                    @break
                                                @case('warning')
                                                    <span class="badge badge-warning">Warning</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-danger">Critical</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @php
                                                $variance = $record->getVariance();
                                                $varClass = $variance > 0 ? 'text-success' : 'text-danger';
                                            @endphp
                                            <span class="{{ $varClass }}">
                                                {{ $variance > 0 ? '+' : '' }}{{ round($variance, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Incidents -->
        @if($incidents->count() > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Active Incidents</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Severity</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incidents as $incident)
                                    <tr>
                                        <td>{{ ucfirst(str_replace('_', ' ', $incident->type)) }}</td>
                                        <td>{{ $incident->incident_date->format('d M Y') }}</td>
                                        <td>
                                            @switch($incident->severity)
                                                @case('low')
                                                    <span class="badge badge-info">Low</span>
                                                    @break
                                                @case('medium')
                                                    <span class="badge badge-warning">Medium</span>
                                                    @break
                                                @case('high')
                                                    <span class="badge badge-danger">High</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-dark">Critical</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $incident->status === 'resolved' ? 'success' : 'warning' }}">
                                                {{ ucfirst($incident->status) }}
                                            </span>
                                        </td>
                                        <td><small>{{ $incident->description }}</small></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-md-12">
                <a href="{{ route('reports.export-pdf', $employee->id) }}?period={{ $period }}" class="btn btn-primary" target="_blank">
                    <i class="fas fa-file-pdf"></i> Export PDF Report
                </a>
                <a href="{{ route('kpi.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Refresh
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
