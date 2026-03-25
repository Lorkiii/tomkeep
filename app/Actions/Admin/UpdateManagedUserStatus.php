<?php

namespace App\Actions\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateManagedUserStatus
{
    /**
     * Apply an admin-driven status transition and write a matching audit record.
     */
    public function execute(User $managedUser, string $status, ?string $adminNotes, User $actor): User
    {
        // Normalize notes once so the same value is used in the model and the log.
        $note = trim((string) $adminNotes);
        $oldValues = $this->snapshot($managedUser);

        DB::transaction(function () use ($managedUser, $status, $note, $actor, $oldValues): void {
            // Status and admin notes are the primary lifecycle controls in the admin area.
            $managedUser->status = $status;
            $managedUser->admin_notes = $note !== '' ? $note : $managedUser->admin_notes;

            if ($status === 'approved') {
                // Approval stores both the actor and the timestamp because other flows depend on them.
                $managedUser->approved_by = $actor->id;
                $managedUser->approved_at = Carbon::now();
            }

            if ($status !== 'approved') {
                // Non-approved states should not retain approval metadata.
                $managedUser->approved_by = null;
                $managedUser->approved_at = null;
            }

            $managedUser->save();

            // The action name stays semantic so downstream audit screens can group events clearly.
            AuditLog::query()->create([
                'user_id' => $actor->id,
                'action' => $this->auditAction($managedUser, $status),
                'model_type' => 'User',
                'model_id' => $managedUser->id,
                'old_values' => $oldValues,
                'new_values' => $this->snapshot($managedUser->fresh()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return $managedUser->fresh();
    }

    /**
     * Limit the audit snapshot to lifecycle fields relevant to status changes.
     *
     * @return array<string, mixed>
     */
    private function snapshot(User $managedUser): array
    {
        return $managedUser->only([
            'role',
            'status',
            'student_code',
            'profile_completed',
            'approved_by',
            'approved_at',
            'admin_notes',
            'is_active',
        ]);
    }

    /**
     * Pick a more specific audit action when the managed account is a student.
     */
    private function auditAction(User $managedUser, string $status): string
    {
        if ($managedUser->role === 'student' && $status === 'approved') {
            return 'student_approved';
        }

        if ($managedUser->role === 'student' && $status === 'rejected') {
            return 'student_rejected';
        }

        return 'user_status_updated';
    }
}