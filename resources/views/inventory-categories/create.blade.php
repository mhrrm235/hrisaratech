@extends('layouts.dashboard')

@section('content')

<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading mb-4">
    <h3>Add Inventory Category</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-body">

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('inventory-categories.store') }}" method="POST">
                            @csrf

                            {{-- Category Name --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    Category Name <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="form-control"
                                    value="{{ old('name') }}"
                                    required
                                >
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    Description
                                </label>
                                <textarea
                                    name="description"
                                    id="description"
                                    class="form-control"
                                    rows="4"
                                >{{ old('description') }}</textarea>
                            </div>

                            {{-- Items Count (Manual Input) --}}
                            <div class="mb-4">
                                <label for="items_count" class="form-label">
                                    Items Count
                                </label>
                                <input
                                    type="number"
                                    name="items_count"
                                    id="items_count"
                                    class="form-control"
                                    min="0"
                                    value="{{ old('items_count') }}"
                                >
                                <small class="text-muted">
                                    Enter the maximum number of items for this category.
                                </small>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    Create
                                </button>

                                <a href="{{ route('inventory-categories.index') }}"
                                   class="btn btn-secondary px-4">
                                    Cancel
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
