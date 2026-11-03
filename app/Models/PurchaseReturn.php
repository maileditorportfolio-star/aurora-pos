<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id', 'store_id', 'return_no', 'status', 'return_date', 'subtotal',
        'tax_amount', 'total_amount', 'note', 'created_by', 'updated_by', 'approved_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'return_date' => 'date',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'return_id');
    }
}
