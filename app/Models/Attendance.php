<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'store_id', 'date', 'shift_id', 'check_in', 'check_out',
        'check_in_ip', 'check_out_ip', 'status', 'late_minutes', 'early_leave_minutes',
        'overtime_minutes', 'working_minutes', 'note', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'working_minutes' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
