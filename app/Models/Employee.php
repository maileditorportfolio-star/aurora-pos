<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'store_id', 'employee_code', 'first_name', 'last_name', 'email',
        'phone', 'date_of_birth', 'gender', 'address', 'joining_date', 'designation',
        'employment_status', 'salary_type', 'basic_salary', 'bank_name', 'bank_account_no',
        'emergency_contact', 'documents', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:4',
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'documents' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function payrollItems()
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function adjustments()
    {
        return $this->hasMany(SalaryAdjustment::class);
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
