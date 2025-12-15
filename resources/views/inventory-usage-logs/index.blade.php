@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
<div class="page-content">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Inventory Usage Logs</h1>
        <a href="{{ route('inventory-usage-logs.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Log Usage
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

      <div class="card">
            
            <div class="card-body">

                <div class="d-flex">
                    @if (session('role') == 'HR')
                        <a href="{{ route('payrolls.create') }}" class="btn btn-primary mb-3 ms-auto">New Payroll</a>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                                <table class="table table-striped" id="table1">
                <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left">Item</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Employee</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Borrowed Date</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Returned Date</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-2">{{ $log->inventory->name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $log->employee->fullname }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $log->borrowed_date->format('M d, Y H:i') }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        @if($log->returned_date)
                            {{ $log->returned_date->format('M d, Y H:i') }}
                        @else
                            <span class="text-orange-600 font-bold">Not Returned</span>
                        @endif
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        @if($log->returned_date)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Returned</span>
                        @else
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded">Borrowed</span>
                        @endif
                    </td>
                    <td class="border border-gray-300 px-4 py-2 text-center">
                        <a href="{{ route('inventory-usage-logs.edit', $log) }}" class="text-blue-500 hover:text-blue-700 mr-3">Edit</a>
                        <form method="POST" action="{{ route('inventory-usage-logs.destroy', $log) }}" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($logs->isEmpty())
    <div class="mt-4 p-4 bg-gray-100 border border-gray-300 text-gray-700 rounded text-center">
        No logs found. <a href="{{ route('inventory-usage-logs.create') }}" class="text-blue-500 hover:text-blue-700">Create one</a>
    </div>
    @endif
</div>
</div>
@endsection
