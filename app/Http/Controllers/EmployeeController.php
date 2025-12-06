<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use App\Models\Presence;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    // Display a list of employees
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    // Show the form for creating a new employee
    public function create()
    {
        $departments = Department::all();
        $roles = Role::all();
        return view('employees.create', compact('departments', 'roles'));
    }

    // Store a newly created employee in storage
    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'birth_date' => 'required|date',
            'hire_date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|string|max:50',
            'salary' => 'required|numeric',
        ]);

        $employee = Employee::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'birth_date' =>  Carbon::parse($request->birth_date)->format('Y-m-d H:i:s'),
            'hire_date' =>  Carbon::parse($request->hire_date)->format('Y-m-d H:i:s'),
            'department_id' => $request->department_id,
            'role_id' => $request->role_id,
            'status' => $request->status,
            'salary' => $request->salary
        ]);

        // Auto-create a User account for this employee if not exists
        $user = User::where('email', $employee->email)->first();
        $generatedPassword = null;

        if (! $user) {
            $generatedPassword = Str::random(12); // random password for new user

            User::create([
                'name' => $employee->fullname,
                'email' => $employee->email,
                'password' => Hash::make($generatedPassword),
                'employee_id' => (string) $employee->id,
            ]);
        } else {
            // Ensure employee id is linked
            if (empty($user->employee_id)) {
                $user->employee_id = (string) $employee->id;
                $user->save();
            }
        }

        $message = 'Employee created successfully.';
        if ($generatedPassword) {
            $message .= " User account created (email: {$employee->email}). Password: {$generatedPassword} — please change after first login.";
        }

        return redirect()->route('employees.index')->with('success', $message);
    }

    // Display the specified employee
    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        $present = Presence::where(['employee_id' => $id, 'status' => 'present'])->count();
        $absent = Presence::where(['employee_id' => $id, 'status' => 'absent'])->count();
        $leave = Presence::where(['employee_id' => $id, 'status' => 'leave'])->count();

        return view('employees.show', compact('employee', 'present', 'absent', 'leave'));
    }

    // Show the form for editing the specified employee
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $departments = Department::all();
        $roles = Role::all();
        
        return view('employees.edit', compact('employee', 'departments', 'roles'));
    }

    // Update the specified employee in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'birth_date' => 'required|date',
            'hire_date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|string|max:50',
            'salary' => 'required|numeric',
        ]);

        $employee = Employee::findOrFail($id);
        $oldEmail = $employee->email;
        $employee->update($request->all());

        // If email changed, update user record if exists
        if ($oldEmail !== $employee->email) {
            $user = User::where('employee_id', $id)->orWhere('email', $oldEmail)->first();
            if ($user) {
                $user->email = $employee->email;
                $user->name = $employee->fullname;
                $user->save();
            }
        }

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    // Remove the specified employee from storage
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        // When an employee is deleted, also delete their user account
        $user = User::where('employee_id', $employee->id)->first();
        if ($user) {
            $user->delete();
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}