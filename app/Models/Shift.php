<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['store_id', 'name', 'start_time', 'end_time', 'break_minutes', 'is_active'];

    protected $casts = [
        'break_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }
}
