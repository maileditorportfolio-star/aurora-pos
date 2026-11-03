<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSequence extends Model
{
    protected $fillable = ['store_id', 'type', 'prefix', 'current_number', 'padding'];

    protected $casts = [
        'current_number' => 'integer',
        'padding' => 'integer',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
