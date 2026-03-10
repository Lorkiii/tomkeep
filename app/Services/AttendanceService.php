<?php

namespace App\Services;

use App\Exceptions\AttendanceException;
use App\Models\AuditLog;
use App\Models\DailyTimeRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public const ACTION_TIME_IN = 'time_in';
    public const ACTION_LUNCH_OUT = 'lunch_out';
    public const ACTION_LUNCH_IN = 'lunch_in';
    public const ACTION_TIME_OUT = 'time_out';

    /**
     * @return array<int, string>
     */
    public static function allowedActions(): array
    {
        return [
            self::ACTION_TIME_IN,
            self::ACTION_LUNCH_OUT,
            self::ACTION_LUNCH_IN,
            self::ACTION_TIME_OUT,
        ];
    }

    public function mark(
        int $userId,
        string $action,
        ?string $occurredAt = null,
        ?int $actorUserId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): DailyTimeRecord {
        return DB::transaction(function () use ($userId, $action, $occurredAt, $actorUserId, $ipAddress, $userAgent) {
            $user = User::query()->find($userId);

            if (! $user) {
                throw new AttendanceException('User not found.', 'RECORD_NOT_FOUND', 404);
            }

            if ($user->status !== 'approved') {
                throw new AttendanceException('User is not approved for attendance actions.', 'USER_NOT_APPROVED', 403);
            }

            $date = now()->toDateString();
            $time = $occurredAt ?? now()->format('h:i:s');

            $record = DailyTimeRecord::query()
                ->where('user_id', $userId)
                ->whereDate('date', $date)
                ->lockForUpdate()
                ->first();

            if ($action === self::ACTION_TIME_IN && ! $record) {
                $record = DailyTimeRecord::query()->create([
                    'user_id' => $userId,
                    'date' => $date,
                    'time_in' => $time,
                ]);

                $this->logAttendanceAction(
                    actorUserId: $actorUserId ?? $userId,
                    record: $record,
                    oldValues: null,
                    newValues: ['time_in' => $time],
                    ipAddress: $ipAddress,
                    userAgent: $userAgent
                );

                return $record;
            }

            if (! $record) {
                throw new AttendanceException('No daily time record exists for this date.', 'RECORD_NOT_FOUND', 404);
            }

            $this->assertActionAllowed($record, $action);

            $oldValues = [
                'time_in' => $record->time_in,
                'lunch_out' => $record->lunch_out,
                'lunch_in' => $record->lunch_in,
                'time_out' => $record->time_out,
            ];

            $record->{$action} = $time;
            $record->save();

            $newValues = [
                'time_in' => $record->time_in,
                'lunch_out' => $record->lunch_out,
                'lunch_in' => $record->lunch_in,
                'time_out' => $record->time_out,
            ];

            $this->logAttendanceAction(
                actorUserId: $actorUserId ?? $userId,
                record: $record,
                oldValues: $oldValues,
                newValues: $newValues,
                ipAddress: $ipAddress,
                userAgent: $userAgent
            );

            return $record;
        });
    }

    private function assertActionAllowed(DailyTimeRecord $record, string $action): void
    {
        if ($record->time_out !== null) {
            throw new AttendanceException('Attendance is already complete for this date.', 'ATTENDANCE_ALREADY_SET');
        }

        if ($action === self::ACTION_TIME_IN) {
            if ($record->time_in !== null) {
                throw new AttendanceException('Time in has already been set.', 'ATTENDANCE_ALREADY_SET');
            }

            return;
        }

        if ($action === self::ACTION_LUNCH_OUT) {
            if ($record->time_in === null) {
                throw new AttendanceException('Lunch out cannot be recorded before time in.', 'ATTENDANCE_OUT_OF_ORDER');
            }

            if ($record->lunch_out !== null) {
                throw new AttendanceException('Lunch out has already been set.', 'ATTENDANCE_ALREADY_SET');
            }

            return;
        }

        if ($action === self::ACTION_LUNCH_IN) {
            if ($record->lunch_out === null) {
                throw new AttendanceException('Lunch in cannot be recorded before lunch out.', 'ATTENDANCE_OUT_OF_ORDER');
            }

            if ($record->lunch_in !== null) {
                throw new AttendanceException('Lunch in has already been set.', 'ATTENDANCE_ALREADY_SET');
            }

            return;
        }

        if ($action === self::ACTION_TIME_OUT) {
            if ($record->lunch_in === null) {
                throw new AttendanceException('Time out cannot be recorded before lunch in.', 'ATTENDANCE_OUT_OF_ORDER');
            }

            if ($record->time_out !== null) {
                throw new AttendanceException('Time out has already been set.', 'ATTENDANCE_ALREADY_SET');
            }

            return;
        }

        throw new AttendanceException('Invalid attendance action.', 'INVALID_ACTION');
    }

    private function logAttendanceAction(
        int $actorUserId,
        DailyTimeRecord $record,
        ?array $oldValues,
        ?array $newValues,
        ?string $ipAddress,
        ?string $userAgent
    ): void {
        AuditLog::query()->create([
            'user_id' => $actorUserId,
            'action' => 'attendance_action',
            'model_type' => 'DailyTimeRecord',
            'model_id' => $record->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
