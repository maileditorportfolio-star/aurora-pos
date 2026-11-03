<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_store_id', 'to_store_id', 'transfer_no', 'status', 'note',
        'transferred_by', 'received_by', 'transferred_at', 'received_at',
        'created_by', 'updated_by', 'approved_by',
    ];

    protected $casts = [
        'transferred_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function fromStore()
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function items()
    {
        return $this->hasMany(StockTransferItem::class, 'transfer_id');
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
