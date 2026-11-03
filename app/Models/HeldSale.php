<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeldSale extends Model
{
    use HasFactory;

    protected $fillable = ['store_id', 'user_id', 'hold_no', 'cart_data', 'customer_id', 'note', 'held_at'];

    protected $casts = [
        'cart_data' => 'array',
        'held_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
