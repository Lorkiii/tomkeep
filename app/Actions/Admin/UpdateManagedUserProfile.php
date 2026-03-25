<?php

namespace App\Actions\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateManagedUserProfile
{
    /**
     * Persist a full admin-side profile edit and record the before/after state.
     *
     * @param array<string, mixed> $attributes
     */
    public function execute(User $managedUser, array $attributes, User $actor): User
    {
        // Capture the original values so the audit log can show exactly what changed.
        $oldValues = $this->snapshot($managedUser);

        DB::transaction(function () use ($managedUser, $attributes, $actor, $oldValues): void {
            // Apply the validated payload in one place so the action remains reusable.
            $managedUser->fill($attributes);
            $managedUser->save();

            // Record the admin edit for future traceability inside the audit log viewer.
            AuditLog::query()->create([
                'user_id' => $actor->id,
                'action' => 'user_profile_updated',
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
     * Keep the audit payload limited to the fields this admin workflow can affect.
     *
     * @return array<string, mixed>
     */
    private function snapshot(User $managedUser): array
    {
        return $managedUser->only([
            'username',
            'email',
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
            'role',
            'is_active',
            'admin_notes',
        ]);
    }
}