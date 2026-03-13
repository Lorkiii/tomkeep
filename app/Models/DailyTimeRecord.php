<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyTimeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_mode',
        'date',
        'time_in',
        'time_in_latitude',
        'time_in_longitude',
        'lunch_out',
        'lunch_in',
        'time_out',
        'time_out_latitude',
        'time_out_longitude',
        'wfh_movement_limit_m',
    ];

    protected function casts(): array
    {
        return [
            'attendance_mode' => 'string',
            'date' => 'date',        // DATE -> Carbon date
            'time_in' => 'string',   // TIME -> keep as HH:MM:SS string
            'time_in_latitude' => 'float',
            'time_in_longitude' => 'float',
            'lunch_out' => 'string', // TIME -> keep as HH:MM:SS string
            'lunch_in' => 'string',  // TIME -> keep as HH:MM:SS string
            'time_out' => 'string',  // TIME -> keep as HH:MM:SS string
            'time_out_latitude' => 'float',
            'time_out_longitude' => 'float',
            'wfh_movement_limit_m' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
