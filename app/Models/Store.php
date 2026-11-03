<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'phone', 'email', 'address', 'city', 'state', 'country',
        'zip_code', 'is_active', 'currency_code', 'vat_percent', 'receipt_paper_size',
        'receipt_header', 'receipt_footer', 'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'vat_percent' => 'decimal:2',
        'settings' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_store')->withPivot('is_default')->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function invoiceSequences()
    {
        return $this->hasMany(InvoiceSequence::class);
    }
}
