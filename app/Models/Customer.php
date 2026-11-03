<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id', 'name', 'phone', 'email', 'address', 'tax_number',
        'opening_balance', 'current_balance', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:4',
        'current_balance' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function ledgers()
    {
        return $this->hasMany(CustomerLedger::class);
    }
}
