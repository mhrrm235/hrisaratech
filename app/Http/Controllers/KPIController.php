<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeKPIRecord;
use App\Models\KPI;
use App\Models\PerformanceReview;
use App\Models\Incident;
use App\Models\KPIRecordProxy;
use App\Services\KPICalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KPIController extends Controller
{
    /**
     * Show KPI dashboard for current user
     */
    public function dashboard()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'User not linked to employee record.');
        }

        $period = now()->format('Y-m');
        try {
            // Use raw SQL due to ORM cache issues
            $records = \DB::select(
                'SELECT * FROM employee_kpi_records WHERE employee_id = ? AND period = ? ORDER BY id',
                [$employee->id, $period]
            );
            
            $kpiRecords = collect($records)->map(function($record) {
                $kpi = KPI::find($record->kpi_id);
                return new KPIRecordProxy($record, $kpi);
            });
        } catch (\Exception $e) {
            // Table may not exist yet
            $kpiRecords = collect([]);
        }

        $compositeScore = $kpiRecords->first()?->composite_score ?? 0;
        $performanceLevel = $kpiRecords->first()?->performance_level ?? 'na';
        $kpisByCategory = $kpiRecords->groupBy(function($r) { return $r->kpi->category; });
        
        try {
            $incidents = Incident::where('employee_id', $employee->id)
                ->where('status', '!=', 'resolved')
                ->orderByDesc('incident_date')
                ->get();
        } catch (\Exception $e) {
            $incidents = collect([]);
        }

        return view('kpi.dashboard', compact('employee', 'period', 'kpiRecords', 'compositeScore', 'performanceLevel', 'kpisByCategory', 'incidents'));
    }

    /**
     * Show employee KPI report
     */
    public function show($id)
    {
        $user = Auth::user();
        $employee = Employee::findOrFail($id);

        if ($user->id !== $employee->user_id && !in_array($user->employee?->role->title ?? null, ['Manager', 'HR', 'Power User'])) {
            abort(403, 'Unauthorized');
        }

        $period = request('period', now()->format('Y-m'));
        try {
            // Use raw SQL due to ORM cache issues
            $records = \DB::select(
                'SELECT * FROM employee_kpi_records WHERE employee_id = ? AND period = ? ORDER BY id',
                [$employee->id, $period]
            );
            
            $kpiRecords = collect($records)->map(function($record) {
                $kpi = KPI::find($record->kpi_id);
                return new KPIRecordProxy($record, $kpi);
            });
        } catch (\Exception $e) {
            $kpiRecords = collect([]);
        }

        $kpisByCategory = $kpiRecords->groupBy(function($r) { return $r->kpi->category; });
        try {
            $performanceReview = PerformanceReview::where('employee_id', $id)
                ->where('period', $period)
                ->first();
        } catch (\Exception $e) {
            $performanceReview = null;
        }

        return view('kpi.show', compact('employee', 'period', 'kpiRecords', 'kpisByCategory', 'performanceReview'));
    }

    /**
     * Show team KPI
     */
    public function team()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'User not linked to employee.');
        }

        $period = request('period', now()->format('Y-m'));
        $teamMembers = Employee::where('supervisor_id', $employee->id)->get();

        $teamKPIs = $teamMembers->map(function($member) use ($period) {
            $records = \DB::select(
                'SELECT * FROM employee_kpi_records WHERE employee_id = ? AND period = ? LIMIT 1',
                [$member->id, $period]
            );
            $first = reset($records) ?: null;
            return [
                'employee' => $member,
                'composite_score' => $first?->composite_score ?? 0,
                'performance_level' => $first?->performance_level ?? 'na',
            ];
        })->sortByDesc('composite_score');

        return view('kpi.team', compact('teamMembers', 'teamKPIs', 'period'));
    }

    /**
     * Show department KPI summary
     */
    public function department()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'User not linked.');
        }

        $period = request('period', now()->format('Y-m'));
        $deptEmployees = Employee::where('department_id', $employee->department_id)->get();

        $deptKPIs = $deptEmployees->map(function($emp) use ($period) {
            $records = \DB::select(
                'SELECT * FROM employee_kpi_records WHERE employee_id = ? AND period = ? LIMIT 1',
                [$emp->id, $period]
            );
            $first = reset($records) ?: null;
            return [
                'employee' => $emp,
                'composite_score' => $first?->composite_score ?? 0,
                'performance_level' => $first?->performance_level ?? 'na',
            ];
        })->sortByDesc('composite_score');

        $avgScore = $deptKPIs->avg('composite_score');

        return view('kpi.department', compact('deptEmployees', 'deptKPIs', 'avgScore', 'period'));
    }
}
