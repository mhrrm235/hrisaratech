@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>Edit Category</h3>
</div>
<div class="page-content">
<div class="container mx-auto px-4 py-8 max-w-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Category</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventory-categories.update', $category) }}" method="POST" class="bg-white p-6 rounded-lg shadow">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Category Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('name', $category->name) }}" required>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" id="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label for="items_count" class="fomr-label">items count</label>
            <textarea name="items_count" id="items_" class="form-control"></textarea>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded flex-1">
                Update
            </button>
            <a href="{{ route('inventory-categories.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded flex-1 text-center">
                Cancel
            </a>
        </div>
    </form>
</div>
</div>
@endsection
