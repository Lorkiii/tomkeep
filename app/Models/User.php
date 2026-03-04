<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'address',
        'course',
        'date_of_birth',
        'school_attended',
        'email',
        'password',
        'role',
        'status',
        'approved_by',
        'approved_at',
        'admin_notes',
        'is_active',
        'last_seen_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'address'=> 'array',
            'approved_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    public function dailyTimeRecords(): HasMany
    {
        return $this->hasMany(DailyTimeRecord::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
