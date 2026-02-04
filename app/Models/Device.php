<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'device_id',
        'type',
        'location',
        'power_source',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
