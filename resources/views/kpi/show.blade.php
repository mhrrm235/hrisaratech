@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3>KPI Report - {{ $employee->fullname }}</h3>
            <p class="text-muted">{{ $employee->department->name }} • {{ $employee->role?->title }}</p>
        </div>
        <div>
            <a href="{{ route('reports.export-pdf', $employee->id) }}?period={{ $period }}" class="btn btn-sm btn-primary" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <button class="btn btn-sm btn-secondary" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="container-fluid">
        <!-- Period and Summary -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-muted mb-2">Period</h6>
                                <div class="input-group input-group-sm">
                                    <input type="month" id="periodSelect" class="form-control" value="{{ $period }}" onchange="changePeriod()">
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <h6 class="text-muted mb-2">Composite Score</h6>
                                <h2 class="mb-0">
                                    <span class="text-{{ 
                                        ($kpiRecords->first()?->composite_score ?? 0) >= 90 ? 'success' : 
                                        (($kpiRecords->first()?->composite_score ?? 0) >= 75 ? 'info' : 
                                        (($kpiRecords->first()?->composite_score ?? 0) >= 60 ? 'warning' : 'danger'))
                                    }}">
                                        {{ round($kpiRecords->first()?->composite_score ?? 0, 2) }}/100
                                    </span>
                                </h2>
                            </div>
                            <div class="col-md-2 text-center">
                                <h6 class="text-muted mb-2">Performance Level</h6>
                                <span class="badge badge-{{ 
                                    $kpiRecords->first()?->performance_level === 'excellent' ? 'success' : 
                                    ($kpiRecords->first()?->performance_level === 'good' ? 'info' : 
                                    ($kpiRecords->first()?->performance_level === 'satisfactory' ? 'warning' : 
                                    ($kpiRecords->first()?->performance_level === 'needs_improvement' ? 'warning' : 'danger')))
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $kpiRecords->first()?->performance_level ?? 'N/A')) }}
                                </span>
                            </div>
                            <div class="col-md-2 text-center">
                                <h6 class="text-muted mb-2">KPIs Achieved</h6>
                                <h4 class="mb-0">{{ $kpiRecords->where('status', 'achieved')->count() }}/{{ $kpiRecords->count() }}</h4>
                            </div>
                            <div class="col-md-2 text-center">
                                <h6 class="text-muted mb-2">Warnings</h6>
                                <h4 class="mb-0 text-warning">{{ $kpiRecords->where('status', 'warning')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Categories -->
        @foreach($kpisByCategory as $category => $records)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            {{ $category }} 
                            <span class="badge badge-secondary float-end">
                                @php
                                    $avgScore = $records->avg(function($r) { return $r->getAchievementPercentage(); });
                                @endphp
                                Avg: {{ round($avgScore, 1) }}%
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Metric</th>
                                        <th style="width: 100px;">Target</th>
                                        <th style="width: 100px;">Actual</th>
                                        <th style="width: 150px;">Achievement</th>
                                        <th style="width: 100px;">Status</th>
                                        <th style="width: 80px;">Variance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records as $record)
                                    <tr>
                                        <td>
                                            <strong>{{ $record->kpi->name }}</strong>
                                            <br><small class="text-muted">{{ $record->kpi->unit }}</small>
                                        </td>
                                        <td>{{ $record->target_value }}</td>
                                        <td>{{ $record->actual_value }}</td>
                                        <td>
                                            @php
                                                $achievement = $record->getAchievementPercentage();
                                            @endphp
                                            <div class="progress" style="height: 20px; min-width: 100px;">
                                                <div class="progress-bar {{ $achievement >= 100 ? 'bg-success' : ($achievement >= 80 ? 'bg-warning' : 'bg-danger') }}" 
                                                     style="width: {{ min($achievement, 100) }}%;">
                                                    {{ round($achievement, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @switch($record->status)
                                                @case('achieved')
                                                    <span class="badge bg-success">Achieved</span>
                                                    @break
                                                @case('warning')
                                                    <span class="badge bg-warning">Warning</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-danger">Critical</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @php
                                                $variance = $record->getVariance();
                                            @endphp
                                            <span class="{{ $variance > 0 ? 'text-success' : 'text-danger' }}">
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

        <!-- Performance Review -->
        @if($performanceReview)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">Performance Review - {{ $performanceReview->reviewed_by }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3">Strengths</h6>
                                <p>{{ $performanceReview->strengths ?? 'No strengths recorded' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">Areas for Improvement</h6>
                                <p>{{ $performanceReview->areas_for_improvement ?? 'No improvement areas recorded' }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="mb-3">Comments</h6>
                                <p>{{ $performanceReview->comments ?? 'No comments' }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Reviewed Date: {{ $performanceReview->reviewed_date->format('d M Y') }}</small>
                            </div>
                            <div class="col-md-6 text-end">
                                <span class="badge bg-{{ $performanceReview->status === 'approved' ? 'success' : 'warning' }}">
                                    {{ ucfirst($performanceReview->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No performance review available for this period.
                </div>
            </div>
        </div>
        @endif

        <!-- Navigation -->
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('kpi.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function changePeriod() {
        const period = document.getElementById('periodSelect').value;
        window.location.href = `{{ route('kpi.show', $employee->id) }}?period=${period}`;
    }
</script>
@endsection
