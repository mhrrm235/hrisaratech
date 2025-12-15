@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>Letter Templates</h3>
</div>
<div class="page-content">
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('letter-templates.create') }}" class="btn btn-primary">+ Create Template</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        @forelse($templates as $template)
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $template->name }}</h5>
                    <p class="card-text">{{ $template->description }}</p>
                    <p><small class="badge bg-info">{{ ucfirst($template->type) }}</small></p>
                    <div class="btn-group" role="group">
                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#contentModal{{ $template->id }}">View Content</a>
                        <a href="{{ route('letter-templates.edit', $template) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" action="{{ route('letter-templates.destroy', $template) }}" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                    
                    <!-- Content Modal -->
                    <div class="modal fade" id="contentModal{{ $template->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ $template->name }} - Content Preview</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div style="border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                                        {!! $template->content !!}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-md-12">
            <div class="alert alert-info">No templates found. <a href="{{ route('letter-templates.create') }}">Create one</a></div>
        </div>
        @endforelse
    </div>
</div>
</div>
@endsection
