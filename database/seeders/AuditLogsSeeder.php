<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\DailyTimeRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@timekeep.local')->first();
        $juan = User::query()->where('email', 'juan@student.local')->first();
        $maria = User::query()->where('email', 'maria@student.local')->first();

        if (! $admin) {
            return;
        }

        if ($juan) {
            $this->firstOrCreateAudit([
                'user_id' => $admin->id,
                'action' => 'approve',
                'model_type' => 'User',
                'model_id' => $juan->id,
            ], [
                'old_values' => ['status' => 'pending'],
                'new_values' => ['status' => 'approved', 'student_code' => $juan->student_code],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'AuditLogsSeeder',
            ]);

            $today = now()->toDateString();
            $juanTodayDtrId = DailyTimeRecord::query()
                ->where('user_id', $juan->id)
                ->whereDate('date', $today)
                ->value('id');

            $this->firstOrCreateAudit([
                'user_id' => $juan->id,
                'action' => 'time_in',
                'model_type' => 'DailyTimeRecord',
                'model_id' => $juanTodayDtrId,
            ], [
                'old_values' => null,
                'new_values' => ['time_in' => $today . ' 07:58:00'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'AuditLogsSeeder',
            ]);
        }

        if ($maria) {
            $this->firstOrCreateAudit([
                'user_id' => null,
                'action' => 'registration',
                'model_type' => 'User',
                'model_id' => $maria->id,
            ], [
                'old_values' => null,
                'new_values' => ['status' => $maria->status, 'student_code' => $maria->student_code],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'AuditLogsSeeder',
            ]);
        }
    }

    /**
     * @param array{user_id: int|null, action: string, model_type: string, model_id: int|null} $identity
     * @param array{old_values: array<string,mixed>|null, new_values: array<string,mixed>|null, ip_address?: string|null, user_agent?: string|null} $payload
     */
    private function firstOrCreateAudit(array $identity, array $payload): void
    {
        AuditLog::query()->firstOrCreate($identity, [
            'old_values' => $payload['old_values'],
            'new_values' => $payload['new_values'],
            'ip_address' => $payload['ip_address'] ?? null,
            'user_agent' => $payload['user_agent'] ?? null,
        ]);
    }
}

