@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>Letter Archives</h3>
</div>
<div class="page-content">
<div class="container-fluid">
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Total Letters</th>
                        <th>Approved</th>
                        <th>Printed</th>
                        <th>Generated Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archives as $archive)
                    <tr>
                        <td>{{ $archive->month }}</td>
                        <td>{{ $archive->year }}</td>
                        <td><span class="badge bg-primary">{{ $archive->total_letters }}</span></td>
                        <td><span class="badge bg-success">{{ $archive->approved_letters }}</span></td>
                        <td><span class="badge bg-info">{{ $archive->printed_letters }}</span></td>
                        <td>{{ $archive->generated_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('letter-archives.show', $archive) }}" class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No archives found yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection
