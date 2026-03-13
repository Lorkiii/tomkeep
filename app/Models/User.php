<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if ($user->role !== 'student' || $user->status !== 'approved' || filled($user->student_code)) {
                return;
            }

            $startedAt = $user->approved_at instanceof DateTimeInterface
                ? $user->approved_at
                : now();

            $user->student_code = self::generateUniqueStudentCode($startedAt, 5);
        });
    }

    protected static function generateUniqueStudentCode(DateTimeInterface $startedAt, int $suffixLength): string
    {
        $prefix = 'ST' . $startedAt->format('ym');
        $maxLength = 12;

        for ($length = $suffixLength; $length <= $maxLength; $length++) {
            $maxValue = (10 ** $length) - 1;

            for ($attempt = 0; $attempt < 25; $attempt++) {
                $candidate = $prefix . str_pad((string) random_int(0, $maxValue), $length, '0', STR_PAD_LEFT);

                if (! static::query()->where('student_code', $candidate)->exists()) {
                    return $candidate;
                }
            }
        }

        throw new \RuntimeException('Unable to generate unique student code for approved user.');
    }

    protected $fillable = [
        'username',
        'student_code',
        'position',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'contact_number',
        'address',
        'course',
        'date_of_birth',
        'school_attended',
        'number_of_hours',
        'profile_completed',
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
            'address' => 'array',
            'approved_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'is_active' => 'boolean',
            'profile_completed' => 'boolean',
            'number_of_hours' => 'integer',
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
