<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update the Employee
        $employee = Employee::updateOrCreate(
            ['email' => 'admin@example.com'], // key unique
            [
                'fullname' => 'System Administrator',
                'phone_number' => '000-000-0000',
                'address' => 'Head Office',
                'birth_date' => Carbon::parse('1990-01-01'),
                'hire_date' => Carbon::now(),
                'department_id' => 1,
                'role_id' => 1,
                'supervisor_id' => null,
                'status' => 'active',
                'salary' => 0,
            ]
        );

        // Create or update the User
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // key unique
            [
                'name' => 'Administrator',
                'password' => Hash::make('Password123!'),
                'employee_id' => $employee->id,
            ]
        );
    }
}
