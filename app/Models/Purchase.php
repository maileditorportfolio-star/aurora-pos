<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id', 'supplier_id', 'purchase_no', 'reference_no', 'status', 'purchase_date',
        'expected_date', 'subtotal', 'discount_type', 'discount_amount', 'tax_amount',
        'shipping_amount', 'total_amount', 'paid_amount', 'due_amount', 'note',
        'created_by', 'updated_by', 'approved_by', 'approved_at', 'received_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'shipping_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'paid_amount' => 'decimal:4',
        'due_amount' => 'decimal:4',
        'purchase_date' => 'date',
        'expected_date' => 'date',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
