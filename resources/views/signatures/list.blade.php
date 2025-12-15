@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>Digital Signatures - {{ $model->subject ?? 'Document' }}</h3>
</div>
<div class="page-content">
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('letters.show', $id) }}" class="btn btn-secondary">Back to Document</a>
            <a href="{{ route('signatures.pad', ['signable' => $signable, 'id' => $id]) }}" class="btn btn-primary">+ Add Signature</a>
        </div>
    </div>
    
    <div class="alert alert-info" role="alert">
        <strong>Download Options:</strong>
        <ul class="mb-0 mt-2">
            <li><strong>Unsigned PDF:</strong> Available in the Document view before any signatures are added</li>
            <li><strong>Signed PDF:</strong> Available below for each signature - includes the digital signature image and metadata</li>
        </ul>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse($signatures as $signature)
    <div class="card mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h5>Signature by {{ $signature->signer->name }}</h5>
                    <small class="text-muted">Signed on {{ $signature->signed_date->format('d M Y H:i:s') }}</small>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-{{ $signature->is_verified ? 'success' : 'warning' }}">
                        {{ $signature->is_verified ? 'Verified' : 'Pending' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Signature Preview:</strong></p>
                    <div style="border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                        <img src="{{ $signature->signature_image }}" alt="Signature" style="max-width: 100%; height: auto;">
                    </div>
                </div>
                <div class="col-md-6">
                    <p><strong>Signature Details:</strong></p>
                    <div class="small">
                        <div class="mb-2">
                            <strong>IP Address:</strong><br>
                            <code style="background: #f5f5f5; padding: 4px 8px; border-radius: 3px; display: inline-block;">{{ $signature->ip_address ?? 'N/A' }}</code>
                        </div>
                        <div class="mb-2">
                            <strong>Device:</strong><br>
                            <code style="background: #f5f5f5; padding: 4px 8px; border-radius: 3px; display: block; word-break: break-all; font-size: 10px; max-height: 40px; overflow-y: auto;">{{ $signature->user_agent ?? 'N/A' }}</code>
                        </div>
                        <div class="mb-2">
                            <strong>Hash (SHA-256):</strong><br>
                            <code style="background: #f5f5f5; padding: 4px 8px; border-radius: 3px; display: block; word-break: break-all; font-size: 9px; font-family: 'Courier New', monospace; max-height: 50px; overflow-y: auto;">{{ $signature->signature_hash }}</code>
                        </div>
                        <div class="mb-2">
                            <strong>Verification Token:</strong><br>
                            <code style="background: #f5f5f5; padding: 4px 8px; border-radius: 3px; display: block; word-break: break-all; font-size: 9px; font-family: 'Courier New', monospace; max-height: 50px; overflow-y: auto;">{{ $signature->verification_token }}</code>
                        </div>
                    </div>

                    @php
                        $userRole = Auth::user()->employee->role->title ?? null;
                        $canVerify = in_array($userRole, ['HR', 'Power User']);
                    @endphp

                    @if($canVerify && !$signature->is_verified)
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $signature->id }}">Verify</button>
                    @endif

                    @if($signature->is_verified || Auth::user()->id === $signature->user_id)
                    <a href="{{ route('signatures.download', $signature) }}" class="btn btn-sm btn-info">Download PDF</a>
                    @endif
                </div>
            </div>

            @if($signature->verifications->count() > 0)
            <hr>
            <p><strong>Verification History:</strong></p>
            @foreach($signature->verifications as $verification)
            <div class="alert alert-sm alert-{{ $verification->status === 'verified' ? 'success' : 'danger' }} py-2">
                <strong>{{ ucfirst($verification->status) }}</strong> by {{ $verification->verifier->name }} on {{ $verification->verification_date->format('d M Y H:i:s') }}
                @if($verification->remarks)
                    <br><small>{{ $verification->remarks }}</small>
                @endif
            </div>
            @endforeach
            @endif
        </div>
    </div>

    <!-- Verify Modal -->
    @php
        $userRole = Auth::user()->employee->role->title ?? null;
        $canVerify = in_array($userRole, ['HR', 'Power User']);
    @endphp
    @if($canVerify)
    <div class="modal fade" id="verifyModal{{ $signature->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verify Signature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('signatures.verify', $signature) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status_{{ $signature->id }}" class="form-label">Decision</label>
                            <select class="form-control" id="status_{{ $signature->id }}" name="status" required>
                                <option value="">-- Select --</option>
                                <option value="verified">Approve Signature</option>
                                <option value="rejected">Reject Signature</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="remarks_{{ $signature->id }}" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks_{{ $signature->id }}" name="remarks" rows="3" placeholder="Add any notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @empty
    <div class="alert alert-info">
        No signatures yet. <a href="{{ route('signatures.pad', ['signable' => $signable, 'id' => $id]) }}">Add a signature</a>
    </div>
    @endforelse
</div>
</div>
@endsection
