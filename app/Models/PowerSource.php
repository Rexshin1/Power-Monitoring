<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'area',
        'type', // 'grid' or 'battery'
        'nominal_voltage',
        'capacity',
        'cost_per_kwh',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'nominal_voltage' => 'float',
        'capacity' => 'float',
        'cost_per_kwh' => 'float',
    ];
}
