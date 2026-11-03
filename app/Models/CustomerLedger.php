<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLedger extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'customer_id', 'store_id', 'type', 'reference_type', 'reference_id',
        'debit', 'credit', 'balance', 'note', 'created_by',
    ];

    protected $casts = [
        'debit' => 'decimal:4',
        'credit' => 'decimal:4',
        'balance' => 'decimal:4',
        'created_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
