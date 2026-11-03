<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_item_id', 'store_id', 'payment_no', 'amount', 'payment_method',
        'reference_no', 'paid_at', 'note', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'paid_at' => 'datetime',
    ];

    public function payrollItem()
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }
}
