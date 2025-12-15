@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>Signature Verification Logs</h3>
</div>
<div class="page-content">
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Signer</th>
                        <th>Document</th>
                        <th>Document Type</th>
                        <th>Signed Date</th>
                        <th>Status</th>
                        <th>Verified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($signatures as $signature)
                    <tr>
                        <td>{{ $signature->signer->name }}</td>
                        <td>
                            @if($signature->signable instanceof App\Models\Letter)
                                {{ $signature->signable->subject }}
                            @else
                                {{ class_basename($signature->signable) }}
                            @endif
                        </td>
                        <td>
                            @if($signature->signable instanceof App\Models\Letter)
                                <span class="badge bg-info">Letter</span>
                            @else
                                <span class="badge bg-secondary">{{ class_basename($signature->signable) }}</span>
                            @endif
                        </td>
                        <td>{{ $signature->signed_date->format('d M Y H:i') }}</td>
                        <td>
                            <span class="badge bg-{{ $signature->is_verified ? 'success' : 'warning' }}">
                                {{ $signature->is_verified ? 'Verified' : 'Pending' }}
                            </span>
                        </td>
                        <td>
                            @if($signature->verified_at)
                                {{ $signature->verified_at->format('d M Y H:i') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($signature->signable instanceof App\Models\Letter)
                                <a href="{{ route('letters.show', $signature->signable) }}" class="btn btn-sm btn-info">View Document</a>
                            @endif
                            <a href="{{ route('signatures.validate', $signature) }}" class="btn btn-sm btn-outline-secondary" onclick="validateSignature(event, this)">Validate</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No signatures found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="row mt-3">
        <div class="col-md-12">
            {{ $signatures->links() }}
        </div>
    </div>
</div>
</div>

<script>
function validateSignature(event, element) {
    event.preventDefault();
    const url = element.href;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                alert('✓ ' + data.message);
                element.classList.add('btn-success');
                element.classList.remove('btn-outline-secondary');
            } else {
                alert('✗ ' + data.message);
                element.classList.add('btn-danger');
                element.classList.remove('btn-outline-secondary');
            }
        })
        .catch(error => {
            alert('Error validating signature: ' + error.message);
        });
}
</script>
@endsection
