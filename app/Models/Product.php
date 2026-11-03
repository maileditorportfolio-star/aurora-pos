<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id', 'category_id', 'brand_id', 'unit_id', 'name', 'slug', 'sku',
        'description', 'purchase_price', 'selling_price', 'wholesale_price',
        'min_stock_alert', 'track_inventory', 'is_active', 'has_variants', 'tax_rate',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:4',
        'selling_price' => 'decimal:4',
        'wholesale_price' => 'decimal:4',
        'min_stock_alert' => 'decimal:3',
        'track_inventory' => 'boolean',
        'is_active' => 'boolean',
        'has_variants' => 'boolean',
        'tax_rate' => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class);
    }
}
