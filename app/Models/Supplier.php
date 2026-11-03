<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id', 'name', 'company_name', 'email', 'phone', 'address', 'city',
        'opening_balance', 'current_balance', 'is_active', 'notes', 'created_by', 'updated_by',
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

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function ledgers()
    {
        return $this->hasMany(SupplierLedger::class);
    }
}
