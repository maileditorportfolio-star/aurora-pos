<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    use HasFactory;

    protected $fillable = ['transfer_id', 'product_id', 'variant_id', 'quantity', 'received_quantity'];

    protected $casts = [
        'quantity' => 'decimal:3',
        'received_quantity' => 'decimal:3',
    ];

    public function transfer()
    {
        return $this->belongsTo(StockTransfer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
