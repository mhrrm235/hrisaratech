@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>Edit Letter</h3>
</div>
<div class="page-content">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('letters.update', $letter) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="letter_type" class="form-label">Letter Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="letter_type" name="letter_type" required>
                                <option value="official" {{ old('letter_type', $letter->letter_type) == 'official' ? 'selected' : '' }}>Official Letter</option>
                                <option value="memo" {{ old('letter_type', $letter->letter_type) == 'memo' ? 'selected' : '' }}>Memorandum</option>
                                <option value="notice" {{ old('letter_type', $letter->letter_type) == 'notice' ? 'selected' : '' }}>Notice</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="letter_template_id" class="form-label">Use Template (Optional)</label>
                            <select class="form-control" id="letter_template_id" name="letter_template_id">
                                <option value="">-- No Template --</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" {{ old('letter_template_id', $letter->letter_template_id) == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject', $letter->subject) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="10" required>{{ old('content', $letter->content) }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Letter</button>
                            <a href="{{ route('letters.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
