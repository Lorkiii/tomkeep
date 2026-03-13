<?php

namespace App\Services;

use App\Exceptions\AttendanceException;
use App\Models\AuditLog;
use App\Models\DailyTimeRecord;
use App\Models\Site;
use App\Models\User;
use App\Support\AttendancePolicy;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function __construct(private readonly AttendancePolicy $attendancePolicy)
    {
    }

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
        ?float $latitude = null,
        ?float $longitude = null,
        ?int $actorUserId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): DailyTimeRecord {
        return DB::transaction(function () use ($userId, $action, $occurredAt, $latitude, $longitude, $actorUserId, $ipAddress, $userAgent) {
            $user = User::query()->find($userId);

            if (! $user) {
                throw new AttendanceException('User not found.', 'RECORD_NOT_FOUND', 404);
            }

            if ($user->status !== 'approved') {
                throw new AttendanceException('User is not approved for attendance actions.', 'USER_NOT_APPROVED', 403);
            }

            $this->assertValidLocation($latitude, $longitude);

            $date = now()->toDateString();
            $time = $occurredAt ?? now()->format('H:i:s');

            $record = DailyTimeRecord::query()
                ->where('user_id', $userId)
                ->whereDate('date', $date)
                ->lockForUpdate()
                ->first();

            if ($action === self::ACTION_TIME_IN && ! $record) {
                $attendanceMode = $this->determineAttendanceMode($latitude, $longitude);

                $record = DailyTimeRecord::query()->create([
                    'user_id' => $userId,
                    'attendance_mode' => $attendanceMode,
                    'date' => $date,
                    'time_in' => $time,
                    'time_in_latitude' => $latitude,
                    'time_in_longitude' => $longitude,
                    // Persist the current global rule on the record so future
                    // policy changes do not retroactively rewrite prior DTRs.
                    'wfh_movement_limit_m' => $this->defaultWfhMovementLimit(),
                ]);

                $this->logAttendanceAction(
                    actorUserId: $actorUserId ?? $userId,
                    record: $record,
                    oldValues: null,
                    newValues: [
                        'attendance_mode' => $record->attendance_mode,
                        'time_in' => $time,
                        'time_in_latitude' => $latitude,
                        'time_in_longitude' => $longitude,
                        'wfh_movement_limit_m' => $record->wfh_movement_limit_m,
                    ],
                    ipAddress: $ipAddress,
                    userAgent: $userAgent
                );

                return $record;
            }

            if (! $record) {
                throw new AttendanceException('No daily time record exists for this date.', 'RECORD_NOT_FOUND', 404);
            }

            $this->assertActionAllowed($record, $action);
            $this->assertTimeOutWithinMovementLimit($record, $action, $latitude, $longitude);

            $oldValues = [
                'attendance_mode' => $record->attendance_mode,
                'time_in' => $record->time_in,
                'time_in_latitude' => $record->time_in_latitude,
                'time_in_longitude' => $record->time_in_longitude,
                'lunch_out' => $record->lunch_out,
                'lunch_in' => $record->lunch_in,
                'time_out' => $record->time_out,
                'time_out_latitude' => $record->time_out_latitude,
                'time_out_longitude' => $record->time_out_longitude,
                'wfh_movement_limit_m' => $record->wfh_movement_limit_m,
            ];

            $record->{$action} = $time;

            if ($action === self::ACTION_TIME_OUT) {
                $record->time_out_latitude = $latitude;
                $record->time_out_longitude = $longitude;
            }

            $record->save();

            $newValues = [
                'attendance_mode' => $record->attendance_mode,
                'time_in' => $record->time_in,
                'time_in_latitude' => $record->time_in_latitude,
                'time_in_longitude' => $record->time_in_longitude,
                'lunch_out' => $record->lunch_out,
                'lunch_in' => $record->lunch_in,
                'time_out' => $record->time_out,
                'time_out_latitude' => $record->time_out_latitude,
                'time_out_longitude' => $record->time_out_longitude,
                'wfh_movement_limit_m' => $record->wfh_movement_limit_m,
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

    private function assertValidLocation(?float $latitude, ?float $longitude): void
    {
        if ($latitude === null || $longitude === null) {
            throw new AttendanceException('Current GPS location is required before recording attendance.', 'ATTENDANCE_LOCATION_REQUIRED');
        }

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            throw new AttendanceException('The captured GPS coordinates are invalid. Please try again.', 'ATTENDANCE_LOCATION_INVALID');
        }
    }

    private function determineAttendanceMode(float $latitude, float $longitude): string
    {
        return $this->findMatchingGeofenceSite($latitude, $longitude) !== null ? 'on_site' : 'wfh';
    }

    private function assertTimeOutWithinMovementLimit(DailyTimeRecord $record, string $action, float $latitude, float $longitude): void
    {
        // The anchor-distance rule applies only to WFH time-out. On-site logs
        // are governed by site radius checks when the day is first classified.
        if ($action !== self::ACTION_TIME_OUT || $record->attendance_mode !== 'wfh') {
            return;
        }

        if ($record->time_in_latitude === null || $record->time_in_longitude === null) {
            return;
        }

        $movementLimit = (int) ($record->wfh_movement_limit_m ?? $this->defaultWfhMovementLimit());
        $distance = $this->distanceMeters(
            $record->time_in_latitude,
            $record->time_in_longitude,
            $latitude,
            $longitude,
        );
        // If the user tries to finish the day from a materially different WFH
        // location, reject the action and keep the anchor point authoritative.
        if ($distance > $movementLimit) {
            throw new AttendanceException(
                'Time out must be recorded near your original time-in location.',
                'ATTENDANCE_TIMEOUT_TOO_FAR'
            );
        }
    }

    /**
     * @return array<string, float|int>|null
     */
    private function findMatchingGeofenceSite(float $latitude, float $longitude): ?array
    {
        foreach ($this->activeGeofenceSites() as $site) {
            $distance = $this->distanceMeters(
                (float) $site['latitude'],
                (float) $site['longitude'],
                $latitude,
                $longitude,
            );

            if ($distance <= (int) $site['allowed_radius_m']) {
                return $site;
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, float|int>>
     */
    private function activeGeofenceSites(): array
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            return DB::table('sites')
                ->selectRaw('id, allowed_radius_m, ST_Y(location) as latitude, ST_X(location) as longitude')
                ->where('is_active', true)
                ->where('enforce_geofence', true)
                ->get()
                ->map(fn ($site): array => [
                    'id' => (int) $site->id,
                    'allowed_radius_m' => (int) $site->allowed_radius_m,
                    'latitude' => (float) $site->latitude,
                    'longitude' => (float) $site->longitude,
                ])
                ->all();
        }

        return Site::query()
            ->where('is_active', true)
            ->where('enforce_geofence', true)
            ->get(['id', 'allowed_radius_m', 'location'])
            ->map(function (Site $site): array {
                preg_match('/POINT\(([-0-9.]+)\s+([-0-9.]+)\)/', (string) $site->location, $matches);

                return [
                    'id' => (int) $site->id,
                    'allowed_radius_m' => (int) $site->allowed_radius_m,
                    'latitude' => isset($matches[2]) ? (float) $matches[2] : 0.0,
                    'longitude' => isset($matches[1]) ? (float) $matches[1] : 0.0,
                ];
            })
            ->all();
    }

    private function distanceMeters(float $fromLatitude, float $fromLongitude, float $toLatitude, float $toLongitude): float
    {
        $earthRadius = 6371000;
        $latDelta = deg2rad($toLatitude - $fromLatitude);
        $lonDelta = deg2rad($toLongitude - $fromLongitude);

        $haversine = sin($latDelta / 2) ** 2
            + cos(deg2rad($fromLatitude)) * cos(deg2rad($toLatitude)) * sin($lonDelta / 2) ** 2;

        $arc = 2 * atan2(sqrt($haversine), sqrt(1 - $haversine));

        return $earthRadius * $arc;
    }

    private function defaultWfhMovementLimit(): int
    {
        // The policy layer owns how the current global rule is resolved so the
        // attendance service does not need to know whether the value comes
        // from fallback config or the admin-managed system setting.
        return $this->attendancePolicy->wfhAnchorLimitMeters();
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
