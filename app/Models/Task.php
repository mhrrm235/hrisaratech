<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'assigned_to', 'due_date', 'status',
    ];

    /**
     * Get the employee that is assigned to the task.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    /**
     * Accessor helper for employee name so views can safely show a name.
     */
    public function getEmployeeNameAttribute()
    {
        return $this->employee?->fullname ?? 'Unknown Employee';
    }
}