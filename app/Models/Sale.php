<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id', 'customer_id', 'user_id', 'invoice_no', 'status', 'sale_date',
        'subtotal', 'discount_type', 'discount_amount', 'tax_amount', 'shipping_amount',
        'total_amount', 'paid_amount', 'due_amount', 'change_amount', 'note',
        'hold_reference', 'cancelled_at', 'cancelled_by', 'cancel_reason',
    ];

    protected $casts = [
        'subtotal' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'shipping_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'paid_amount' => 'decimal:4',
        'due_amount' => 'decimal:4',
        'change_amount' => 'decimal:4',
        'sale_date' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
