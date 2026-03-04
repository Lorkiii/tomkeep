<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'company_name',
        'address',
        'allowed_radius_m',
        'location',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'allowed_radius_m' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
