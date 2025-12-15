<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Department;
use App\Models\Task;
use App\Models\LeaveRequest;
use App\Models\Letter;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\KPI;
use App\Models\LetterTemplate;
use App\Models\LetterConfiguration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncAllDummyData extends Command
{
    protected $signature = 'sync:dummy-data';
    protected $description = 'Synchronize and create all dummy data (roles, departments, employees, tasks, leave, letters, inventory, KPIs)';

    public function handle()
    {
        $this->info('Starting comprehensive dummy data sync...');

        try {
            // 1. Create Roles
            $this->info('Creating roles...');
            $hrRole = Role::firstOrCreate(
                ['title' => 'HR'],
                ['description' => 'Human Resources']
            );
            $managerRole = Role::firstOrCreate(
                ['title' => 'Manager'],
                ['description' => 'Department Manager']
            );
            $developerRole = Role::firstOrCreate(
                ['title' => 'Developer'],
                ['description' => 'Software Developer']
            );
            $powerUserRole = Role::firstOrCreate(
                ['title' => 'Power User'],
                ['description' => 'Power User with elevated privileges']
            );
            $this->info('✓ Roles synced');

            // 2. Create Departments
            $this->info('Creating departments...');
            $deptIT = Department::firstOrCreate(
                ['name' => 'IT'],
                ['description' => 'Information Technology', 'status' => 'active']
            );
            $deptHR = Department::firstOrCreate(
                ['name' => 'Human Resources'],
                ['description' => 'Human Resources Department', 'status' => 'active']
            );
            $deptMarketing = Department::firstOrCreate(
                ['name' => 'Marketing'],
                ['description' => 'Marketing Department', 'status' => 'active']
            );
            $deptSales = Department::firstOrCreate(
                ['name' => 'Sales'],
                ['description' => 'Sales Department', 'status' => 'active']
            );
            $deptOps = Department::firstOrCreate(
                ['name' => 'Operations'],
                ['description' => 'Operations Department', 'status' => 'active']
            );
            $this->info('✓ Departments synced');

            // 3. Create Users and Employees
            $this->info('Creating employees and users...');
            
            // Admin user
            $adminUser = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make('password'),
                ]
            );
            $adminEmp = Employee::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'user_id' => $adminUser->id,
                    'fullname' => 'Administrator',
                    'email' => 'admin@example.com',
                    'phone_number' => '081234567890',
                    'address' => '123 Admin Street',
                    'department_id' => $deptHR->id,
                    'role_id' => $hrRole->id,
                    'supervisor_id' => null,
                    'hire_date' => '2020-01-01',
                    'status' => 'active',
                    'salary' => 10000000,
                ]
            );
            $adminUser->update(['employee_id' => $adminEmp->id]);

            // Manager
            $mgrUser = User::firstOrCreate(
                ['email' => 'manager@example.com'],
                [
                    'name' => 'John Manager',
                    'password' => Hash::make('password'),
                ]
            );
            $mgrEmp = Employee::firstOrCreate(
                ['email' => 'manager@example.com'],
                [
                    'user_id' => $mgrUser->id,
                    'fullname' => 'John Manager',
                    'email' => 'manager@example.com',
                    'phone_number' => '081234567891',
                    'address' => '456 Manager Ave',
                    'department_id' => $deptIT->id,
                    'role_id' => $managerRole->id,
                    'supervisor_id' => $adminEmp->id,
                    'hire_date' => '2021-01-01',
                    'status' => 'active',
                    'salary' => 8000000,
                ]
            );
            $mgrUser->update(['employee_id' => $mgrEmp->id]);

            // Employees
            $employees = [];
            for ($i = 3; $i <= 5; $i++) {
                $dept = $i === 3 ? $deptIT : ($i === 4 ? $deptMarketing : $deptSales);
                $roleId = $i === 3 ? $developerRole->id : $powerUserRole->id;
                
                $emp = Employee::firstOrCreate(
                    ['email' => "employee{$i}@example.com"],
                    [
                        'user_id' => User::firstOrCreate(
                            ['email' => "employee{$i}@example.com"],
                            [
                                'name' => "Employee {$i}",
                                'password' => Hash::make('password'),
                            ]
                        )->id,
                        'fullname' => "Employee {$i}",
                        'email' => "employee{$i}@example.com",
                        'phone_number' => "08123456789{$i}",
                        'address' => "Street {$i}",
                        'department_id' => $dept->id,
                        'role_id' => $roleId,
                        'supervisor_id' => $mgrEmp->id,
                        'hire_date' => '2022-01-01',
                        'status' => 'active',
                        'salary' => 6000000,
                    ]
                );
                
                User::where('email', "employee{$i}@example.com")->update(['employee_id' => $emp->id]);
                $employees[] = $emp;
            }
            $this->info('✓ Employees synced (5 total)');

            // 4. Create KPIs
            $this->info('Creating KPIs...');
            $kpis = [
                ['code' => 'KPI001', 'name' => 'Attendance Rate', 'category' => 'Attendance', 'unit' => '%', 'target' => 95],
                ['code' => 'KPI002', 'name' => 'Projects Completed', 'category' => 'Productivity', 'unit' => 'count', 'target' => 12],
                ['code' => 'KPI003', 'name' => 'Tasks On-Time', 'category' => 'Productivity', 'unit' => '%', 'target' => 90],
                ['code' => 'KPI004', 'name' => 'Code Quality Score', 'category' => 'Quality', 'unit' => '%', 'target' => 85],
                ['code' => 'KPI005', 'name' => 'Customer Satisfaction', 'category' => 'Quality', 'unit' => '%', 'target' => 88],
                ['code' => 'KPI006', 'name' => 'Policy Compliance', 'category' => 'Department', 'unit' => '%', 'target' => 100],
                ['code' => 'KPI007', 'name' => 'Training Completion', 'category' => 'Behavior', 'unit' => '%', 'target' => 100],
                ['code' => 'KPI008', 'name' => 'Team Collaboration', 'category' => 'Behavior', 'unit' => 'score', 'target' => 8],
                ['code' => 'KPI009', 'name' => 'Leave Balance', 'category' => 'Leave', 'unit' => 'days', 'target' => 12],
                ['code' => 'KPI010', 'name' => 'Salary Processing', 'category' => 'Salary', 'unit' => '%', 'target' => 100],
            ];

            foreach ($kpis as $kpi) {
                KPI::firstOrCreate(
                    ['name' => $kpi['name']],
                    $kpi
                );
            }
            $this->info('✓ KPIs synced (10 total)');

            // 5. Create Tasks
            $this->info('Creating tasks...');
            Task::firstOrCreate(
                ['id' => 1],
                [
                    'employee_id' => $employees[0]->id,
                    'title' => 'Complete API Documentation',
                    'description' => 'Write comprehensive API documentation',
                    'status' => 'pending',
                    'priority' => 'high',
                    'due_date' => Carbon::now()->addDays(7),
                ]
            );
            Task::firstOrCreate(
                ['id' => 2],
                [
                    'employee_id' => $employees[1]->id,
                    'title' => 'Marketing Campaign Design',
                    'description' => 'Design new marketing campaign',
                    'status' => 'completed',
                    'priority' => 'medium',
                    'due_date' => Carbon::now()->subDays(5),
                ]
            );
            $this->info('✓ Tasks synced (2 total)');

            // 6. Create Leave Requests
            $this->info('Creating leave requests...');
            LeaveRequest::firstOrCreate(
                ['id' => 1],
                [
                    'employee_id' => $employees[0]->id,
                    'leave_type' => 'annual',
                    'start_date' => Carbon::now()->addDays(14),
                    'end_date' => Carbon::now()->addDays(16),
                    'status' => 'pending',
                ]
            );
            LeaveRequest::firstOrCreate(
                ['id' => 2],
                [
                    'employee_id' => $employees[1]->id,
                    'leave_type' => 'sick',
                    'start_date' => Carbon::now()->subDays(2),
                    'end_date' => Carbon::now()->subDays(1),
                    'status' => 'approved',
                ]
            );
            $this->info('✓ Leave Requests synced (2 total)');

            // 7. Create Inventory Categories
            $this->info('Creating inventory categories...');
            $categories = [
                ['name' => 'Office Supplies', 'description' => 'Pens, papers, etc'],
                ['name' => 'Electronics', 'description' => 'Monitors, keyboards, etc'],
                ['name' => 'Furniture', 'description' => 'Desks, chairs, etc'],
                ['name' => 'Software Licenses', 'description' => 'License keys and software'],
                ['name' => 'Network Equipment', 'description' => 'Routers, switches, etc'],
            ];
            foreach ($categories as $i => $cat) {
                InventoryCategory::firstOrCreate(
                    ['id' => $i + 1],
                    $cat
                );
            }
            $this->info('✓ Inventory Categories synced (5 total)');

            // 8. Create Inventory Items
            $this->info('Creating inventory items...');
            $inventories = [
                ['id' => 1, 'inventory_category_id' => 1, 'name' => 'Ballpoint Pens', 'quantity' => 100],
                ['id' => 2, 'inventory_category_id' => 2, 'name' => 'LCD Monitor 24\"', 'quantity' => 15],
                ['id' => 3, 'inventory_category_id' => 3, 'name' => 'Office Chair', 'quantity' => 20],
                ['id' => 4, 'inventory_category_id' => 4, 'name' => 'MS Office License', 'quantity' => 50],
                ['id' => 5, 'inventory_category_id' => 5, 'name' => 'Cisco Switch', 'quantity' => 3],
            ];
            foreach ($inventories as $inv) {
                Inventory::firstOrCreate(
                    ['id' => $inv['id']],
                    $inv
                );
            }
            $this->info('✓ Inventory Items synced (5 total)');

            // 8a. Create Letter Templates
            $this->info('Creating letter templates...');
            $templates = [
                [
                    'name' => 'Surat Penawaran Kerja',
                    'slug' => 'job-offer',
                    'description' => 'Template surat penawaran pekerjaan untuk karyawan baru',
                    'content' => "[COMPANY_NAME]\n\nYang Terhormat [EMPLOYEE_NAME],\n\nDengan senang hati kami menawarkan posisi [POSITION] kepada Anda. Silakan hubungi HR untuk detail lengkap.\n\nHormat kami,\nHR Department"
                ],
                [
                    'name' => 'Surat Kontrak Kerja',
                    'slug' => 'employment-contract',
                    'description' => 'Template kontrak kerja permanent',
                    'content' => "KONTRAK KERJA\n\nDengan ini disepakati antara [COMPANY_NAME] dan [EMPLOYEE_NAME] untuk mengadakan perjanjian kerja sebagai berikut:\n\nPos: [POSITION]\nGaji: [SALARY]\nJakarta, [DATE]\n\nTanda Tangan:"
                ],
                [
                    'name' => 'Surat Rekomendasi',
                    'slug' => 'recommendation-letter',
                    'description' => 'Template surat rekomendasi kerja',
                    'content' => "SURAT REKOMENDASI\n\nDengan ini saya merekomendasikan [EMPLOYEE_NAME] sebagai karyawan yang kompeten dan profesional. [EMPLOYEE_NAME] telah bekerja dengan baik selama [PERIOD].\n\nHormat kami,\n[RECOMMENDER_NAME]"
                ],
                [
                    'name' => 'Surat Pernyataan Kerja',
                    'slug' => 'work-certificate',
                    'description' => 'Template sertifikat kerja',
                    'content' => "SURAT KETERANGAN KERJA\n\nYang bertanda tangan di bawah ini menerangkan bahwa [EMPLOYEE_NAME] bekerja di [COMPANY_NAME] sejak [START_DATE] sampai [END_DATE] sebagai [POSITION].\n\nJakarta, [DATE]\nDiminta untuk keperluan: [PURPOSE]"
                ],
                [
                    'name' => 'Surat Izin Cuti',
                    'slug' => 'leave-permission',
                    'description' => 'Template surat izin cuti',
                    'content' => "SURAT IZIN CUTI\n\nDengan ini saya mengajukan izin cuti sebanyak [DAYS] hari mulai dari [START_DATE] sampai [END_DATE].\n\nAlasan: [REASON]\n\nHormat kami,\n[EMPLOYEE_NAME]"
                ]
            ];
            foreach ($templates as $i => $template) {
                LetterTemplate::firstOrCreate(
                    ['name' => $template['name']],
                    $template
                );
            }
            $this->info('✓ Letter Templates synced (5 total)');

            // 8b. Create Letter Configuration
            $this->info('Creating letter configuration...');
            LetterConfiguration::firstOrCreate(
                ['id' => 1],
                [
                    'letter_number_format' => '{NUMBER}/{DEPT}/{MONTH}/{YEAR}',
                    'current_number' => 0,
                    'company_name' => 'PT Aratech Indonesia',
                    'company_address' => 'Jl. Gatot Subroto No. 1, Jakarta',
                    'company_phone' => '(021) 1234-5678',
                    'company_email' => 'info@aratech.co.id',
                ]
            );
            $this->info('✓ Letter Configuration synced');

            // 9. Seed KPI Records
            $this->info('Creating KPI records...');
            $kpiModels = KPI::all();
            $period = '2025-12';
            $inserted = 0;

            foreach ($employees as $emp) {
                foreach ($kpiModels as $kpi) {
                    // Check if already exists
                    $exists = DB::selectOne(
                        'SELECT id FROM employee_kpi_records WHERE employee_id = ? AND kpi_id = ? AND period = ?',
                        [$emp->id, $kpi->id, $period]
                    );

                    if (!$exists) {
                        $actualValue = rand(50, 100);
                        $targetValue = $kpi->target ?? 100;
                        $achievement = $targetValue > 0 ? ($actualValue / $targetValue) * 100 : 0;

                        if ($achievement >= 90) {
                            $status = 'achieved';
                            $performanceLevel = 'excellent';
                        } elseif ($achievement >= 75) {
                            $status = 'achieved';
                            $performanceLevel = 'good';
                        } elseif ($achievement >= 60) {
                            $status = 'warning';
                            $performanceLevel = 'satisfactory';
                        } elseif ($achievement >= 45) {
                            $status = 'warning';
                            $performanceLevel = 'needs_improvement';
                        } else {
                            $status = 'critical';
                            $performanceLevel = 'unsatisfactory';
                        }

                        DB::insert(
                            'INSERT INTO employee_kpi_records (employee_id, kpi_id, period, actual_value, target_value, status, performance_level, composite_score, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                            [
                                $emp->id,
                                $kpi->id,
                                $period,
                                $actualValue,
                                $targetValue,
                                $status,
                                $performanceLevel,
                                $achievement,
                                'Auto-generated dummy record',
                                Carbon::now(),
                                Carbon::now(),
                            ]
                        );
                        $inserted++;
                    }
                }
            }
            $this->info("✓ KPI Records synced ($inserted inserted)");

            $this->info('');
            $this->info('✅ All dummy data successfully synchronized!');
            $this->info('Summary:');
            $this->info('  • Roles: 4');
            $this->info('  • Departments: 5');
            $this->info('  • Employees: 5 (with associated users)');
            $this->info('  • KPIs: 10');
            $this->info('  • Tasks: 2');
            $this->info('  • Leave Requests: 2');
            $this->info('  • Inventory Categories: 5');
            $this->info('  • Inventory Items: 5');
            $this->info('  • Letter Templates: 5');
            $this->info('  • Letter Configuration: 1');
            $this->info('  • KPI Records: ' . $inserted . ' (for 5 employees × 10 KPIs)');

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
