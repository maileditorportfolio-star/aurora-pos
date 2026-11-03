<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id', 'employee_id', 'basic_salary', 'working_days', 'present_days',
        'leave_days', 'absent_days', 'overtime_hours', 'overtime_amount', 'bonus',
        'allowance', 'deduction', 'advance', 'other_adjustments', 'net_salary', 'note',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:4',
        'working_days' => 'integer',
        'present_days' => 'integer',
        'leave_days' => 'decimal:1',
        'absent_days' => 'integer',
        'overtime_hours' => 'decimal:2',
        'overtime_amount' => 'decimal:4',
        'bonus' => 'decimal:4',
        'allowance' => 'decimal:4',
        'deduction' => 'decimal:4',
        'advance' => 'decimal:4',
        'other_adjustments' => 'decimal:4',
        'net_salary' => 'decimal:4',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payments()
    {
        return $this->hasMany(SalaryPayment::class);
    }
}
