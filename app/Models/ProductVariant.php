<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'name', 'sku', 'barcode', 'purchase_price', 'selling_price',
        'wholesale_price', 'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:4',
        'selling_price' => 'decimal:4',
        'wholesale_price' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
