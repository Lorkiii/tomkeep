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
        'date',
        'time_in',
        'lunch_out',
        'lunch_in',
        'time_out',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',        // DATE -> Carbon date
            'time_in' => 'string',   // TIME -> keep as HH:MM:SS string
            'lunch_out' => 'string', // TIME -> keep as HH:MM:SS string
            'lunch_in' => 'string',  // TIME -> keep as HH:MM:SS string
            'time_out' => 'string',  // TIME -> keep as HH:MM:SS string
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
