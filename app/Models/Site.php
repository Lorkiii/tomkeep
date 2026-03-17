<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'company_name',
        'address',
        'allowed_radius_m',
        'enforce_geofence',
        'wfh_anchor_enforced',
        'wfh_anchor_limit_m',
        'location',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'allowed_radius_m' => 'integer',
            'enforce_geofence' => 'boolean',
            'wfh_anchor_enforced' => 'boolean',
            'wfh_anchor_limit_m' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
