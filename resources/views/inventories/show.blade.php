@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>{{ $inventory->name }}</h3>
</div>
<div class="page-content">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">{{ $inventory->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('inventories.edit', $inventory) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
            <a href="{{ route('inventories.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-600 text-sm">Category</div>
            <div class="text-2xl font-bold text-gray-800">{{ $inventory->category->name }}</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-600 text-sm">Quantity</div>
            <div class="text-2xl font-bold text-gray-800">{{ $inventory->quantity }}</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-gray-600 text-sm">Status</div>
            <div class="text-xl font-bold">
                <span class="px-3 py-1 rounded text-white 
                    @if($inventory->status === 'active') bg-green-500 
                    @elseif($inventory->status === 'inactive') bg-yellow-500 
                    @else bg-red-500 
                    @endif">
                    {{ ucfirst($inventory->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Details</h2>
        <table class="w-full">
            <tr class="border-b">
                <td class="py-2 font-bold text-gray-700">Location:</td>
                <td class="py-2 text-gray-800">{{ $inventory->location ?? 'N/A' }}</td>
            </tr>
            <tr class="border-b">
                <td class="py-2 font-bold text-gray-700">Purchase Date:</td>
                <td class="py-2 text-gray-800">{{ $inventory->purchase_date ? $inventory->purchase_date->format('M d, Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="py-2 font-bold text-gray-700">Description:</td>
                <td class="py-2 text-gray-800">{{ $inventory->description ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Usage History</h2>
        
        @if($inventory->usageLogs->isEmpty())
            <div class="text-gray-600 text-center py-4">No usage logs yet</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left">Employee</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Borrowed Date</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Returned Date</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventory->usageLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2">{{ $log->employee->fullname }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $log->borrowed_date->format('M d, Y H:i') }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if($log->returned_date)
                                    {{ $log->returned_date->format('M d, Y H:i') }}
                                @else
                                    <span class="text-orange-600 font-bold">Currently Borrowed</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $log->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
</div>
@endsection
