<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id', 'expense_category_id', 'expense_no', 'amount', 'expense_date',
        'payment_method', 'reference_no', 'vendor', 'description', 'receipt_path',
        'note', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'expense_date' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }
}
