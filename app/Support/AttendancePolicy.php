<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Models\User;

class AttendancePolicy
{
    private const WFH_ANCHOR_LIMIT_KEY = 'wfh_anchor_limit_m';

    public function wfhAnchorLimitMeters(): int
    {
        $storedValue = SystemSetting::query()
            ->where('key', self::WFH_ANCHOR_LIMIT_KEY)
            ->value('value');

        return $this->normalizeLimit($storedValue ?? config('attendance.wfh_anchor_limit_m', 20));
    }

    public function updateWfhAnchorLimitMeters(int $limit, User $actor): SystemSetting
    {
        $normalizedLimit = $this->normalizeLimit($limit);
        $existingSetting = SystemSetting::query()->firstWhere('key', self::WFH_ANCHOR_LIMIT_KEY);

        $setting = SystemSetting::query()->updateOrCreate(
            ['key' => self::WFH_ANCHOR_LIMIT_KEY],
            [
                'value' => (string) $normalizedLimit,
                'updated_by' => $actor->id,
            ]
        );

        AuditLog::query()->create([
            'user_id' => $actor->id,
            'action' => 'attendance_policy_updated',
            'model_type' => 'SystemSetting',
            'model_id' => $setting->id,
            'old_values' => [
                'wfh_anchor_limit_m' => $existingSetting ? $this->normalizeLimit($existingSetting->value) : null,
            ],
            'new_values' => [
                'wfh_anchor_limit_m' => $normalizedLimit,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $setting;
    }

    private function normalizeLimit(mixed $value): int
    {
        return max(1, (int) $value);
    }
}