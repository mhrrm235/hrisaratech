<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryUsageLog;
use App\Models\Inventory;
use App\Models\Employee;
use Carbon\Carbon;

class InventoryUsageLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logs = InventoryUsageLog::with('inventory', 'employee')->get();
        return view('inventory-usage-logs.index', compact('logs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $inventories = Inventory::all();
        $employees = Employee::all();
        return view('inventory-usage-logs.create', compact('inventories', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'employee_id' => 'required|exists:employees,id',
            'borrowed_date' => 'required|date',
            'returned_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Parse datetime-local format (YYYY-MM-DDTHH:mm)
        $borrowedDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->borrowed_date);
        $returnedDate = $request->returned_date ? Carbon::createFromFormat('Y-m-d\TH:i', $request->returned_date) : null;

        InventoryUsageLog::create([
            'inventory_id' => $request->inventory_id,
            'employee_id' => $request->employee_id,
            'borrowed_date' => $borrowedDate,
            'returned_date' => $returnedDate,
            'notes' => $request->notes,
        ]);

        return redirect()->route('inventory-usage-logs.index')->with('success', 'Usage log created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $log = InventoryUsageLog::with('inventory', 'employee')->findOrFail($id);
        return view('inventory-usage-logs.show', compact('log'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $log = InventoryUsageLog::findOrFail($id);
        $inventories = Inventory::all();
        $employees = Employee::all();
        return view('inventory-usage-logs.edit', compact('log', 'inventories', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $log = InventoryUsageLog::findOrFail($id);
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'employee_id' => 'required|exists:employees,id',
            'borrowed_date' => 'required|date',
            'returned_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Parse datetime-local format (YYYY-MM-DDTHH:mm)
        $borrowedDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->borrowed_date);
        $returnedDate = $request->returned_date ? Carbon::createFromFormat('Y-m-d\TH:i', $request->returned_date) : null;

        $log->update([
            'inventory_id' => $request->inventory_id,
            'employee_id' => $request->employee_id,
            'borrowed_date' => $borrowedDate,
            'returned_date' => $returnedDate,
            'notes' => $request->notes,
        ]);

        return redirect()->route('inventory-usage-logs.index')->with('success', 'Usage log updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $log = InventoryUsageLog::findOrFail($id);
        $log->delete();
        return redirect()->route('inventory-usage-logs.index')->with('success', 'Usage log deleted successfully.');
    }
}
