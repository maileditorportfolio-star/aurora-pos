<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'store_id', 'type', 'amount', 'reason', 'status',
        'approved_by', 'approved_at', 'paid_at', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
