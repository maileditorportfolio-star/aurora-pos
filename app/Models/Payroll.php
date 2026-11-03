<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id', 'payroll_no', 'month', 'year', 'status', 'total_employees',
        'total_basic', 'total_overtime', 'total_bonus', 'total_allowance',
        'total_deduction', 'total_advance', 'total_net', 'note',
        'created_by', 'updated_by', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'total_employees' => 'integer',
        'total_basic' => 'decimal:4',
        'total_overtime' => 'decimal:4',
        'total_bonus' => 'decimal:4',
        'total_allowance' => 'decimal:4',
        'total_deduction' => 'decimal:4',
        'total_advance' => 'decimal:4',
        'total_net' => 'decimal:4',
        'approved_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
