<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 'store_id', 'return_no', 'status', 'return_date', 'subtotal',
        'tax_amount', 'total_amount', 'refund_amount', 'refund_method', 'note',
        'created_by', 'updated_by', 'approved_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'refund_amount' => 'decimal:4',
        'return_date' => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class, 'return_id');
    }
}
