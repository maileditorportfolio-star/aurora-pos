<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id', 'product_id', 'variant_id', 'quantity', 'quantity_received',
        'unit_cost', 'discount_amount', 'tax_amount', 'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'quantity_received' => 'decimal:3',
        'unit_cost' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'line_total' => 'decimal:4',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
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
