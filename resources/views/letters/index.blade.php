@extends('layouts.dashboard')

@section('content')
@php
    $userRole = Auth::user()->employee->role->title ?? null;
    $isHROrPowerUser = in_array($userRole, ['HR', 'Power User']);
    $pendingCount = $isHROrPowerUser ? \App\Models\Letter::where('status', 'pending')->count() : 0;
@endphp
<div class="page-heading">
    <h3>Letters
        @if($isHROrPowerUser && $pendingCount > 0)
            <span class="badge bg-warning">{{ $pendingCount }} Pending</span>
        @endif
    </h3>
</div>
<div class="page-content">
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('letters.create') }}" class="btn btn-primary">+ Create Letter</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Letter Number</th>
                        <th>Subject</th>
                        <th>From</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($letters as $letter)
                    <tr>
                        <td>{{ $letter->letter_number ?? 'Draft' }}</td>
                        <td>{{ $letter->subject }}</td>
                        <td>{{ $letter->user->name }}</td>
                        <td>
                            <span class="badge bg-{{ $letter->status === 'draft' ? 'secondary' : ($letter->status === 'pending' ? 'warning' : ($letter->status === 'approved' ? 'success' : 'info')) }}">
                                {{ ucfirst($letter->status) }}
                            </span>
                        </td>
                        <td>{{ $letter->created_date->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('letters.show', $letter) }}" class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No letters found. <a href="{{ route('letters.create') }}">Create one</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection
